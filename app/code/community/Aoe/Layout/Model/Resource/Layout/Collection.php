<?php
/**
 * Layout Collection
 *
 * @category    Aoe
 * @package     Aoe_Layout
 * @author      Manish Jain
 */
class Aoe_Layout_Model_Resource_Layout_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Constructor
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('aoe_layout/layout');
    }

    /**
     * Filter by store ids
     *
     * @param array|integer $storeIds
     * @param boolean $withDefaultStore if TRUE also filter by store id '0'
     * @return Aoe_Layout_Model_Resource_Layout_Collection
     */
    public function addStoreFilter($storeIds = array(), $withDefaultStore = true)
    {
        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }
        if ($withDefaultStore && !in_array('0', $storeIds)) {
            array_unshift($storeIds, 0);
        }
        $where = array();
        foreach ($storeIds as $storeId) {
            $where[] = $this->_getConditionSql('store_ids', array('finset' => $storeId));
        }

        $this->_select->where(implode(' OR ', $where));

        return $this;
    }
}