<?php
/**
 * @author Joel Hart
 */

/** @var $installer Mage_Core_Model_Resource_Setup*/
$installer = $this;

$installer->startSetup();

$installer->run("
	INSERT INTO `{$this->getTable('sales/order_status')}` (`status`,`label`,`trustev_status_id`)
	VALUES  ('trustev_review','Order Review',10),
            ('trustev_fraud','Payment Review',2)
    ON DUPLICATE KEY UPDATE `trustev_status_id`=VALUES(`trustev_status_id`);
	");

$installer->run("
    UPDATE `{$this->getTable('sales/order_status')}`
    SET `trustev_status_id` = 10
    WHERE `status` = 'payment_review';
  ");

$installer->run("
	INSERT INTO `{$this->getTable('sales/order_status_state')}` (`status`,`state`,`is_default`)
	VALUES  ('trustev_review','holded',0),
            ('trustev_fraud','payment_review',0)
    ON DUPLICATE KEY UPDATE `is_default`=VALUES(`is_default`);
	");

$installer->endSetup();
