<?php

/* @var $installer Aoe_Layout_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'aoe_layout/layout'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('aoe_layout/layout'))
    ->addColumn('layout_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Layout Id')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Widget Title')
    ->addColumn('store_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ids')
    ->addColumn('area', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    ), 'Area')
    ->addColumn('package', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    ), 'Package')
    ->addColumn('theme', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
    ), 'Theme')
    ->addColumn('handle', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
    ), 'Handle')
    ->addColumn('xml', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
    ), 'Xml')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort order')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
    ), 'Is Layout Active')
    ->addColumn('layout_active_from', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => true,
    ), 'Layout Active From Date')
    ->addColumn('layout_active_to', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        'nullable'  => true,
    ), 'Layout Active To Date')
    ->setComment('AOE Layout');

$installer->getConnection()->createTable($table);

/**
 * Create table 'aoe_layout/layout_update'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('aoe_layout/layout_update'))
    ->addColumn('layout_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Layout Id')
    ->addColumn('layout_update_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Layout Update Id')
    ->addIndex($installer->getIdxName('aoe_layout/layout', 'layout_id'), 'layout_id')
    ->addIndex($installer->getIdxName('aoe_layout/layout_update', 'layout_update_id'), 'layout_update_id')
    ->addIndex($installer->getIdxName('aoe_layout/layout_update', array('layout_update_id', 'layout_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('layout_update_id', 'layout_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey($installer->getFkName('aoe_layout/layout_update', 'layout_id', 'aoe_layout/layout', 'layout_id'),
        'layout_id', $installer->getTable('aoe_layout/layout'), 'layout_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('aoe_layout/layout_update', 'layout_update_id', 'core/layout_update', 'layout_update_id'),
        'layout_update_id', $installer->getTable('core/layout_update'), 'layout_update_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Layout Updates');
$installer->getConnection()->createTable($table);

$installer->endSetup();