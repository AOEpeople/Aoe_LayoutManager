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
    const XML_NODE_RELATED_CACHE = 'global/widget/related_cache_types';

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
     * Processing object before save data
     *
     * @return Aoe_Layout_Model_Layout
     */
    protected function _beforeSave()
    {
        if (is_array($this->getData('store_ids'))) {
            $this->setData('store_ids', implode(',', $this->getData('store_ids')));
        }

        return parent::_beforeSave();
    }

    /**
     * Getter
     * Explode to array if string setted
     *
     * @return array
     */
    public function getStoreIds()
    {
        if (is_string($this->getData('store_ids'))) {
            return explode(',', $this->getData('store_ids'));
        }
        return $this->getData('store_ids');
    }

    /**
     * Invalidate related cache types
     *
     * @return Aoe_Layout_Model_Layout
     */
    protected function _invalidateCache()
    {
        $types = Mage::getConfig()->getNode(self::XML_NODE_RELATED_CACHE);
        if ($types) {
            $types = $types->asArray();
            Mage::app()->getCacheInstance()->invalidateType(array_keys($types));
        }

        return $this;
    }

    /**
     * Clean related cache types
     *
     * @return Aoe_Layout_Model_Layout
     */
    protected function _cleanCache()
    {
        Mage::app()->getCacheInstance()->cleanType('layout');
        Mage::app()->getCacheInstance()->cleanType('block_html');
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