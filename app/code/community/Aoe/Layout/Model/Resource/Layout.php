<?php
/**
 * Layout Resource Model
 *
 * @category    Aoe
 * @package     Aoe_Layout
 * @author      Manish Jain
 */
class  Aoe_Layout_Model_Resource_Layout extends Mage_Core_Model_Resource_Layout
{
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
        if (!$object->getCopiedFromOriginal()) {
            foreach (array('layout_active_from', 'layout_active_to') as $dataKey) {
                $date = $object->getData($dataKey);
                if (!$date) {
                    $object->setData($dataKey, new Zend_Db_Expr('NULL'));
                }
            }
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
        $layoutId = (int)$object->getId();

        if ($layoutId) {
            $this->_deleteLayoutUpdates($layoutId);
            $this->_saveLayoutUpdates($object);
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
        $layoutUpdateLinkTable = $this->getTable('core/layout_link');

        $insert = array(
            'store_id'           => (int)$layoutInstance->getStoreId(),
            'area'               => $layoutInstance->getArea(),
            'package'            => $layoutInstance->getPackage(),
            'theme'              => $layoutInstance->getTheme(),
            'layout_update_id'   => (int)$layoutInstance->getId(),
            'is_active'          => (int)$layoutInstance->getIsActive(),
            'layout_active_from' => $layoutInstance->getLayoutActiveFrom(),
            'layout_active_to'   => $layoutInstance->getLayoutActiveTo()
        );

        $writeAdapter->insert($layoutUpdateLinkTable, $insert);
        return $this;
    }

    /**
     * Perform actions before object delete.
     * Collect layout update id and set to object for further delete
     *
     * @param Varien_Object $object
     * @return Aoe_Layout_Model_Resource_Layout
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        $object->setLayoutUpdateIdToDelete($object->getId());
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
        $this->_deleteLayoutUpdates($object->getLayoutUpdateIdToDelete());
        return parent::_afterDelete($object);
    }

    /**
     * Delete layout updates by given id
     *
     * @param array $layoutUpdateId
     * @return Aoe_Layout_Model_Resource_Layout
     */
    protected function _deleteLayoutUpdates($layoutUpdateId)
    {
        $writeAdapter = $this->_getWriteAdapter();
        if ($layoutUpdateId) {
            $inCond = $writeAdapter->prepareSqlCondition('layout_update_id', array(
                'in' => $layoutUpdateId
            ));
            $writeAdapter->delete(
                $this->getTable('core/layout_link'),
                $inCond
            );
        }
        return $this;
    }

    /**
     * Retrieve select object and join it to product entity table to get type ids
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_CatalogInventory_Model_Stock_Item $object
     * @return Varien_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object)
            ->join(array('link' => $this->getTable('core/layout_link')),
                'link.layout_update_id=core_layout_update.layout_update_id',
                array('link.*')
            );
        return $select;
    }

    /**
     * Retrieve layout updates by handle
     *
     * @param string $handle
     * @param array $params
     * @return string
     */
    public function fetchUpdatesByHandle($handle, $params = array())
    {
        $todayDate  = Mage::app()->getLocale()->date()
            ->toString(Varien_Date::DATE_INTERNAL_FORMAT);

        $bind = array(
            'store_id'  => Mage::app()->getStore()->getId(),
            'area'      => Mage::getSingleton('core/design_package')->getArea(),
            'package'   => Mage::getSingleton('core/design_package')->getPackageName(),
            'theme'     => Mage::getSingleton('core/design_package')->getTheme('layout'),
            'is_active' => 1,
            'today_date' => $todayDate,
        );

        foreach ($params as $key => $value) {
            if (isset($bind[$key])) {
                $bind[$key] = $value;
            }
        }
        $bind['layout_update_handle'] = $handle;
        $result = '';

        $readAdapter = $this->_getReadAdapter();
        if ($readAdapter) {
            $select = $readAdapter->select()
                ->from(array('layout_update' => $this->getMainTable()), array('xml'))
                ->join(array('link'=>$this->getTable('core/layout_link')),
                    'link.layout_update_id=layout_update.layout_update_id',
                    '')
                ->where('link.store_id IN (0, :store_id)')
                ->where('link.area = :area')
                ->where('link.package = :package')
                ->where('link.theme = :theme')
                ->where('layout_update.handle = :layout_update_handle')
                ->where('link.is_active = :is_active')
                ->where('link.layout_active_from <= :today_date OR link.layout_active_from IS NULL')
                ->where('link.layout_active_to >= :today_date OR link.layout_active_to IS NULL')
                ->order('layout_update.sort_order ' . Varien_Db_Select::SQL_ASC);

            $result = join('', $readAdapter->fetchCol($select, $bind));
        }
        return $result;
    }

    /**
     * Retrieve layout active to date by handle
     *
     * @param string $handle
     * @return string
     */
    public function fetchLayoutActiveToByHandle($handle)
    {

        $bind = array(
            'store_id'  => Mage::app()->getStore()->getId(),
            'area'      => Mage::getSingleton('core/design_package')->getArea(),
            'package'   => Mage::getSingleton('core/design_package')->getPackageName(),
            'theme'     => Mage::getSingleton('core/design_package')->getTheme('layout'),
            'is_active' => 1
        );

        $bind['layout_update_handle'] = $handle;
        $result = '';

        $readAdapter = $this->_getReadAdapter();
        if ($readAdapter) {
            $select = $readAdapter->select()
                ->from(array('layout_update' => $this->getMainTable()), array('link.layout_active_to'))
                ->join(array('link'=>$this->getTable('core/layout_link')),
                    'link.layout_update_id=layout_update.layout_update_id',
                    '')
                ->where('link.store_id IN (0, :store_id)')
                ->where('link.area = :area')
                ->where('link.package = :package')
                ->where('link.theme = :theme')
                ->where('layout_update.handle = :layout_update_handle')
                ->where('link.is_active = :is_active')
                ->where('link.layout_active_to IS NOT NULL')
                ->order('link.layout_active_to ' . Varien_Db_Select::SQL_ASC)
                ->limit(1);

            $result = join('', $readAdapter->fetchCol($select, $bind));
        }
        return $result;
    }
}