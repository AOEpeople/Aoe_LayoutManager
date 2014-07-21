<?php

/* @var $installer Aoe_Layout_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$connection = $installer->getConnection();

$layoutTable = $installer->getTable('core/layout_update');
$layoutLinkTable = $installer->getTable('core/layout_link');

$connection->addColumn($layoutTable, 'title', array(
    'type'    => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length'  => 255,
    'comment' => 'Layout Update Title'
));

$connection->addColumn($layoutLinkTable, 'is_active', array(
    'type'    => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable'  => false,
    'default' => 1,
    'comment' => 'Is Layout Active'
));

$connection->addColumn($layoutLinkTable, 'layout_active_from', array(
    'type'    => Varien_Db_Ddl_Table::TYPE_DATE,
    'nullable'  => true,
    'default' => null,
    'comment' => 'Layout Active From Date'
));

$connection->addColumn($layoutLinkTable, 'layout_active_to', array(
    'type'    => Varien_Db_Ddl_Table::TYPE_DATE,
    'nullable'  => true,
    'default' => null,
    'comment' => 'Layout Active To Date'
));

$installer->endSetup();