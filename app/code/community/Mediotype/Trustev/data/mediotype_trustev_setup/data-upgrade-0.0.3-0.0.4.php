<?php

$installer = $this;
$installer->startSetup();

if(!$installer->getConnection()->tableColumnExists($installer->getTable('sales/quote_payment'),'cc_bin'))
{
    $installer->getConnection()->addColumn($this->getTable('sales/quote_payment'), 'cc_bin', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'length' => 255,
        'comment' => 'Trustev CCBIN FIELD'
    ));
}

if(!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_payment'),'cc_bin'))
{
    $installer->getConnection()->addColumn($this->getTable('sales/order_payment'), 'cc_bin', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'length' => 255,
        'comment' => 'Trustev CCBIN FIELD'
    ));
}

$installer->endSetup();