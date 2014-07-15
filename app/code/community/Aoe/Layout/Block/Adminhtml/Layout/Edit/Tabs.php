<?php
/**
 * Layout edit tabs container
 *
 * @category    Aoe
 * @package     Aoe_Layout
 * @author      Manish Jain
 */
class Aoe_Layout_Block_Adminhtml_Layout_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('layout_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('aoe_layout')->__('Layout Update'));
    }
}