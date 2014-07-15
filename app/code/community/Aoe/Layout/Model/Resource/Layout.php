<?php
/**
 * Layout Resource Model
 *
 * @category    Aoe
 * @package     Aoe_Layout
 * @author      Manish Jain
 */
class  Aoe_Layout_Model_Resource_Layout extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Constructor
     *
     */
    public function _construct()
    {
        // Note that the layout_id refers to the key field in your database table.
        $this->_init('aoe_layout/layout', 'layout_id');
    }


    /**
     * Process layout data before saving
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Aoe_Layout_Model_Resource_Layout
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        /*
         * For two attributes which represent timestamp data in DB
         * we should make converting such as:
         * If they are empty we need to convert them into DB
         * type NULL so in DB they will be empty and not some default value
         */
        foreach (array('layout_active_from', 'layout_active_to') as $field) {
            $value = !$object->getData($field) ? null : $object->getData($field);
            $object->setData($field, $this->formatDate($value));
        }

        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param Aoe_Layout_Model_Layout $object
     * @return Aoe_Layout_Model_Resource_Layout
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $layoutTable         = $this->getTable('aoe_layout/layout');
        $layoutUpdateTable   = $this->getTable('aoe_layout/layout_update');
        $readAdapter       = $this->_getReadAdapter();
        $writeAdapter      = $this->_getWriteAdapter();

        $layoutId = (int)$object->getId();

        if ($layoutId) {
            $inCond = $readAdapter->prepareSqlCondition('layout_id', $layoutId);

            $select = $readAdapter->select()
                ->from($layoutUpdateTable, 'layout_update_id')
                ->where($inCond);
            $removeLayoutUpdateIds = $readAdapter->fetchCol($select);

            $writeAdapter->delete($layoutUpdateTable, $inCond);
            $this->_deleteLayoutUpdates($removeLayoutUpdateIds);


            $layoutUpdateIds = $this->_saveLayoutUpdates($object);

            foreach ($layoutUpdateIds as $layoutUpdateId) {
                $writeAdapter->insert($layoutUpdateTable, array(
                    'layout_id' => $layoutId,
                    'layout_update_id' => $layoutUpdateId
                ));
            }

        }
        return parent::_afterSave($object);
    }


    /**
     * Prepare and save layout updates data
     *
     * @param Aoe_Layout_Model_Layout $layoutInstance
     * @return array of inserted layout updates ids
     */
    protected function _saveLayoutUpdates($layoutInstance)
    {
        $writeAdapter          = $this->_getWriteAdapter();
        $pageLayoutUpdateIds   = array();
        $storeIds              = $this->_prepareStoreIds($layoutInstance->getStoreIds());
        $layoutUpdateTable     = $this->getTable('core/layout_update');
        $layoutUpdateLinkTable = $this->getTable('core/layout_link');

        $handle = $layoutInstance->getHandle();
        $xml = $layoutInstance->getXml();
        $insert = array(
            'handle'     => $handle,
            'xml'        => $xml
        );
        if (strlen($layoutInstance->getSortOrder())) {
            $insert['sort_order'] = $layoutInstance->getSortOrder();
        };

        $designModel = Mage::getModel('core/design_package');
        $areas = $this->_prepareAreas($layoutInstance->getArea());

        /*
         * Code written to support all areas, all packages and all themes.
         * core_layout_link table have UNQ_CORE_LAYOUT_LINK_STORE_ID_PACKAGE_THEME_LAYOUT_UPDATE_ID
         */
        $design = $designModel->getThemeList();
        if ($package = $layoutInstance->getPackage()) {
            $packages = array($package => $design[$package]);
        } else {
            $packages = $design;
        }
        if ($theme = $layoutInstance->getTheme()) {
            foreach ($packages as $package=>$themes) {
                if (in_array($theme, $themes)) {
                    $packages[$package] = array($theme);
                } else {
                    unset($packages[$package]);
                }
            }
        }

        foreach ($areas as $area) {
            $writeAdapter->insert($layoutUpdateTable, $insert);
            $layoutUpdateId = $writeAdapter->lastInsertId($layoutUpdateTable);
            $layoutUpdateIds[] = $layoutUpdateId;

            $data = array();
            foreach ($storeIds as $storeId) {
                foreach ($packages as $package=>$themes) {
                    foreach ($themes as $theme) {
                        $data[] = array(
                            'store_id'         => $storeId,
                            'area'             => $area,
                            'package'          => $package,
                            'theme'            => $theme,
                            'layout_update_id' => $layoutUpdateId
                        );
                    }
                }
            }

            $writeAdapter->insertMultiple($layoutUpdateLinkTable, $data);
        }
        return $layoutUpdateIds;
    }

    /**
     * Prepare store ids.
     * If one of store id is default (0) return all store ids
     *
     * @param array $storeIds
     * @return array
     */
    protected function _prepareStoreIds($storeIds)
    {
        if (in_array('0', $storeIds)) {
            $storeIds = array(0);
        }
        return $storeIds;
    }

    /**
     * Prepare areas.
     * If area is blank return all areas
     *
     * @param array $area
     * @return array
     */
    protected function _prepareAreas($area)
    {
        if ($area) {
            $areas = array($area);
        } else {
            $areas = array('frontend', 'adminhtml', 'install');
        }
        return $areas;
    }


    /**
     * Perform actions before object delete.
     * Collect layout id and layout update ids and set to object for further delete
     *
     * @param Varien_Object $object
     * @return Aoe_Layout_Model_Resource_Layout
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $writeAdapter = $this->_getWriteAdapter();
        $select = $writeAdapter->select()
            ->from(array('main_table' => $this->getTable('aoe_layout/layout')), array())
            ->joinInner(
                array('layout_update_table' => $this->getTable('aoe_layout/layout_update')),
                'layout_update_table.layout_id = main_table.layout_id',
                array('layout_update_id')
            )
            ->where('main_table.layout_id=?', $object->getId());
        $result = $writeAdapter->fetchCol($select);
        $object->setLayoutUpdateIdsToDelete($result);
        return $this;
    }

    /**
     * Perform actions after object delete.
     * Delete layout updates by layout update ids collected in _beforeSave
     *
     * @param Aoe_Layout_Model_Layout $object
     * @return Aoe_Layout_Model_Resource_Layout
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        $this->_deleteLayoutUpdates($object->getLayoutUpdateIdsToDelete());
        return parent::_afterDelete($object);
    }

    /**
     * Delete layout updates by given ids
     *
     * @param array $layoutUpdateIds
     * @return Aoe_Layout_Model_Resource_Layout
     */
    protected function _deleteLayoutUpdates($layoutUpdateIds)
    {
        $writeAdapter = $this->_getWriteAdapter();
        if ($layoutUpdateIds) {
            $inCond = $writeAdapter->prepareSqlCondition('layout_update_id', array(
                'in' => $layoutUpdateIds
            ));
            $writeAdapter->delete(
                $this->getTable('core/layout_update'),
                $inCond
            );
        }
        return $this;
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'store_ids')
            ->where("{$this->getIdFieldName()} = ?", (int)$id);
        $storeIds = $adapter->fetchOne($select);
        return $storeIds ? explode(',', $storeIds) : array();
    }
}