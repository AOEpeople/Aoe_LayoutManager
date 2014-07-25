<?php
/**
 * Layout grid block
 *
 * @category    Aoe
 * @package     Aoe_Layout
 * @author      Manish Jain
 */

class Aoe_Layout_Block_Adminhtml_Layout_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Set defaults
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('layoutGrid');
        $this->setDefaultSort('layout_update_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection object
     *
     * @return Aoe_Layout_Block_Adminhtml_Layout_Grid
     */
    protected function _prepareCollection()
    {
        /* @var $collection Aoe_Layout_Model_Resource_Layout_Collection */
        $collection = Mage::getModel('aoe_layout/layout')->getCollection()
                    ->join(array('link'=>'core/layout_link'), 'link.layout_update_id = main_table.layout_update_id',
                        array('area','package','theme', 'is_active', 'layout_active_from', 'layout_active_to'));
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Aoe_Layout_Block_Adminhtml_Layout_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('layout_update_id', array(
            'header'    => Mage::helper('aoe_layout')->__('ID'),
            'align'     => 'left',
            'index'     => 'layout_update_id',
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('aoe_layout')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('area', array(
            'header'    => Mage::helper('aoe_layout')->__('Area'),
            'align'     => 'left',
            'index'     => 'area',
        ));

        $this->addColumn('package', array(
            'header'    => Mage::helper('aoe_layout')->__('Package'),
            'align'     => 'left',
            'index'     => 'package',
        ));

        $this->addColumn('theme', array(
            'header'    => Mage::helper('aoe_layout')->__('Theme'),
            'align'     => 'left',
            'index'     => 'theme',
        ));

        $this->addColumn('handle', array(
            'header'    => Mage::helper('aoe_layout')->__('Handle'),
            'align'     => 'left',
            'index'     => 'handle',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('aoe_layout')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('aoe_layout')->__('Disabled'),
                1 => Mage::helper('aoe_layout')->__('Enabled')
            ),
        ));

        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM
        );

        $this->addColumn('layout_active_from', array(
            'header'    => Mage::helper('aoe_layout')->__('From Date'),
            'index'     => 'layout_active_from',
            'type'      => 'date',
            'format' => $dateFormatIso
        ));

        $this->addColumn('layout_active_to', array(
            'header'    => Mage::helper('aoe_layout')->__('To Date'),
            'index'     => 'layout_active_to',
            'type'      => 'date',
            'format' => $dateFormatIso
        ));

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('aoe_layout')->__('Sort Order'),
            'width'     => '100',
            'align'     => 'center',
            'index'     => 'sort_order',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     * @param object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('layout_update_id' => $row->getId()));
    }

    /**
     * Define row click callback
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}