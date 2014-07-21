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
}