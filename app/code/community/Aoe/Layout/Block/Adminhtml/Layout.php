<?php
/**
 * Layout grid container
 *
 * @category    Aoe
 * @package     Aoe_Layout
 * @author      Manish Jain
 */
class Aoe_Layout_Block_Adminhtml_Layout extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'aoe_layout';
        $this->_controller = 'adminhtml_layout';
        $this->_headerText = Mage::helper('aoe_layout')->__('Manage Layout Update');
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('aoe_layout')->__('Add New Layout Update'));
    }
}