<?php
/**
 * Layout Manager grid container
 *
 * @category    Aoe
 * @package     Aoe_LayoutManager
 * @author      Manish Jain
 */
class Aoe_LayoutManager_Block_Adminhtml_Layout extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_blockGroup = 'aoe_layoutmanager';
        $this->_controller = 'adminhtml_layout';
        $this->_headerText = Mage::helper('aoe_layoutmanager')->__('Manage Layout Update');
        parent::__construct();
        $this->_updateButton('add', 'label', Mage::helper('aoe_layoutmanager')->__('Add New Layout Update'));
    }
}
