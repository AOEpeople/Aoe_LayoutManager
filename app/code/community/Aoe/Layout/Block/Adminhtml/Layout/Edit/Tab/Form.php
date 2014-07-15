<?php
/**
 * Layout form tab block
 *
 * @category    Aoe
 * @package     Aoe_Layout
 * @author      Manish Jain
 */

class Aoe_Layout_Block_Adminhtml_Layout_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setActive(true);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('aoe_layout')->__('Layout Update Properties');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('aoe_layout')->__('Layout Update Properties');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
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
     * Prepare form before rendering HTML
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        $layoutInstance = $this->getLayoutInstance();
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend' => Mage::helper('aoe_layout')->__('Layout Update Properties'))
        );

        if ($layoutInstance->getId()) {
            $fieldset->addField('layout_id', 'hidden', array(
                'name' => 'layout_id',
            ));
        }

        $this->_addElementTypes($fieldset);

        $fieldset->addField('title', 'text', array(
            'name'  => 'title',
            'label' => Mage::helper('aoe_layout')->__('Title'),
            'title' => Mage::helper('aoe_layout')->__('Title'),
            'class' => '',
            'required' => true,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_ids', 'multiselect', array(
                'name'      => 'store_ids[]',
                'label'     => Mage::helper('aoe_layout')->__('Assign to Store Views'),
                'title'     => Mage::helper('aoe_layout')->__('Assign to Store Views'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('area', 'select', array(
            'label'     => Mage::helper('aoe_layout')->__('Area'),
            'title'     => Mage::helper('aoe_layout')->__('Area'),
            'name'      => 'area',
            'required'  => false,
            'options'   => array(
                'frontend' => Mage::helper('aoe_layout')->__('frontend'),
                'adminhtml' => Mage::helper('aoe_layout')->__('adminhtml'),
                'install' => Mage::helper('aoe_layout')->__('install'),
                '' => Mage::helper('aoe_layout')->__('all areas')
            )
        ));

        $fieldset->addField('package', 'text', array(
            'name'  => 'package',
            'label' => Mage::helper('aoe_layout')->__('Package'),
            'title' => Mage::helper('aoe_layout')->__('Package'),
            'class' => '',
            'required' => false,
            'note' => Mage::helper('aoe_layout')->__('Leave blank for "all packages"')
        ));

        $fieldset->addField('theme', 'text', array(
            'name'  => 'theme',
            'label' => Mage::helper('aoe_layout')->__('Theme'),
            'title' => Mage::helper('aoe_layout')->__('Theme'),
            'class' => '',
            'required' => false,
            'note' => Mage::helper('aoe_layout')->__('Leave blank for "all themes"')
        ));

        $fieldset->addField('handle', 'text', array(
            'name'  => 'handle',
            'label' => Mage::helper('aoe_layout')->__('Handle'),
            'title' => Mage::helper('aoe_layout')->__('Handle'),
            'class' => '',
            'required' => true,
        ));

        $fieldset->addField('xml', 'textarea', array(
            'name'  => 'xml',
            'label' => Mage::helper('aoe_layout')->__('Layout Update XML'),
            'title' => Mage::helper('aoe_layout')->__('Layout Update XML'),
            'class' => 'textarea',
            'required' => true,
        ));

        $fieldset->addField('sort_order', 'text', array(
            'name'  => 'sort_order',
            'label' => Mage::helper('aoe_layout')->__('Sort Order'),
            'title' => Mage::helper('aoe_layout')->__('Sort Order'),
            'class' => '',
            'required' => false,
            'note' => Mage::helper('aoe_layout')->__('Sort Order of layout in the same block reference')
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => Mage::helper('aoe_layout')->__('Status'),
            'title'     => Mage::helper('aoe_layout')->__('Status'),
            'name'      => 'is_active',
            'required'  => false,
            'options'   => array(
                0 => Mage::helper('aoe_layout')->__('Disabled'),
                1 => Mage::helper('aoe_layout')->__('Enabled')
            )
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
        );

        $fieldset->addField('layout_active_from', 'date', array(
            'name'      => 'layout_active_from',
            'label'     => Mage::helper('aoe_layout')->__('Layout Update From'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso,
            'class'     => 'validate-date validate-date-range date-range-layout_active-from'
        ));

        $fieldset->addField('layout_active_to', 'date', array(
            'name'      => 'layout_active_to',
            'label'     => Mage::helper('aoe_layout')->__('Layout Update To'),
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => $dateFormatIso,
            'class'     => 'validate-date validate-date-range date-range-layout_active-to'
        ));

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Initialize form fields values
     *
     * @return Aoe_Layout_Block_Adminhtml_Layout_Edit_Tab_Form
     */
    protected function _initFormValues()
    {
        $this->getForm()->addValues($this->getLayoutInstance()->getData());
        return parent::_initFormValues();
    }
}