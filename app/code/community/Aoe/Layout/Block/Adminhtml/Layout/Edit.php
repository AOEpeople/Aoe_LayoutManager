<?php
/**
 * Layout edit container
 *
 * @category    Aoe
 * @package     Aoe_Layout
 * @author      Manish Jain
 */

class Aoe_Layout_Block_Adminhtml_Layout_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_objectId = 'layout_id';
        $this->_blockGroup = 'aoe_layout';
        $this->_controller = 'adminhtml_layout';

        $this->_addButton('save_and_edit_button', array(
                'label'   => Mage::helper('aoe_layout')->__('Save and Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save'
            ), 100
        );

        $this->_formScripts[] = '
            function saveAndContinueEdit() {
            editForm.submit($(\'edit_form\').action + \'back/edit/\');}';
    }

    /**
     * Getter
     *
     * @return Aoe_Layout_Model_Layout
     */
    public function getLayoutInstance()
    {
        return Mage::registry('current_layout_instance');
    }


    /**
     * Return translated header text depending on creating/editing action
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getLayoutInstance()->getId()) {
            return Mage::helper('aoe_layout')->__('Layout Update "%s"', $this->escapeHtml($this->getLayoutInstance()->getTitle()));
        }
        else {
            return Mage::helper('aoe_layout')->__('New Layout Update');
        }
    }

    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }
}