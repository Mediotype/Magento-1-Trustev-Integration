<?php
/**
 * @author Joel Hart
 */

/** @var $installer Mage_Core_Model_Resource_Setup*/
$installer = $this;

$installer->startSetup();

/**
 * Creates table for storing Trustev statuses types
 */
$statusTable = $installer->getConnection()
    ->newTable($this->getTable('mediotype_trustev/status_type'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Status ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
        'nullable'  => false,
    ), 'Associated Label')
    ->setComment('List of Trustev status types which can be applied to a case');

$installer->getConnection()->createTable($statusTable);

/**
 * Creates table for storing Trustev webhook requests
 */
$webhookTable = $installer->getConnection()
    ->newTable($this->getTable('mediotype_trustev/webhook_log'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Increment Id')
    ->addColumn('case_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 256, array(
    ), 'Case ID provided by Trustev')
    ->addColumn('status_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
    ), 'Status ID')
    ->setComment('Log of data sent to webhook by Trustev');

$installer->getConnection()->createTable($webhookTable);

/**
 * Creates table for storing Trustev payment types
 */
$paymentTable = $installer->getConnection()
    ->newTable($this->getTable('mediotype_trustev/payment_type'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Payment ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 64, array(
        'nullable'  => false,
    ), 'Associated Label')
    ->setComment('List of Trustev payment types which can be applied to a case');

$installer->getConnection()->createTable($paymentTable);

/**
 * Creates table for storing Trustev case ids, decisions, and statuses
 */
$caseTable = $installer->getConnection()
    ->newTable($this->getTable('mediotype_trustev/case'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'ID')
    ->addColumn('case_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 256, array(
        'nullable'  => false,
    ), 'Case ID provided by Trustev')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Associated Order ID')
    ->addColumn('response', Varien_Db_Ddl_Table::TYPE_BLOB, null, array(
        'nullable'  => false,
    ), 'JSON Response data from Trustev')
    ->addColumn('decision', Varien_Db_Ddl_Table::TYPE_BLOB, null, array(
        'nullable'  => true,
    ), 'Decision data from Trustev')
    ->setComment('Data table for Trustev Case')
    ->addForeignKey(
        $installer->getFkName(
            'mediotype_trustev_case',
            'order_id',
            'sales/order',
            'entity_id'
        ),
        'order_id',
        $installer->getTable('sales/order'),
        'entity_id',
        Varien_Db_Ddl_Table::ACTION_RESTRICT,
        Varien_Db_Ddl_Table::ACTION_RESTRICT);

$installer->getConnection()->createTable($caseTable);

if(!$installer->getConnection()->tableColumnExists($installer->getTable('sales/order_status'),'trustev_status_id'))
{
    $installer->getConnection()->addColumn($installer->getTable('sales/order_status'), 'trustev_status_id', array(
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => true,
        'comment' => 'Mediotype Trustev Status Type Id'
    ));
}

$installer->endSetup();
