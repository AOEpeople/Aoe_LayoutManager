<?php
/**
 * Layout Model  for different purposes
 *
 * @category    Aoe
 * @package     Aoe_Layout
 * @author      Manish Jain
 */
class Aoe_Layout_Model_Layout extends Mage_Core_Model_Abstract
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
     * Clean related cache types
     *
     * @return Aoe_Layout_Model_Layout
     */
    protected function _cleanCache()
    {
        Mage::app()->getCacheInstance()->cleanType('layout');
        return $this;
    }

    /**
     * Clean related cache if instance contain layout updates
     */
    protected function _afterSave()
    {
        if ($this->hasDataChanges()) {
            $this->_cleanCache();
        }
        return parent::_afterSave();
    }

    /**
     * Clean related cache as instance contain layout updates
     */
    protected function _beforeDelete()
    {
        $this->_cleanCache();
        return parent::_beforeDelete();
    }
}