<?php
/**
 * @author Joel Hart
 */

/** @var $installer Mage_Core_Model_Resource_Setup*/
$installer = $this;

$installer->startSetup();

 /** Note: Trustev values start at 0, not 1.  Currently, this is accounted for in the model */
$installer->run("
	INSERT INTO `{$this->getTable('mediotype_trustev/status_type')}` (`id`,`name`)
	VALUES  (1,'Completed'),
            (2,'RejectedFraud'),
            (3,'RejectedAuthFailure'),
            (4,'RejectedSuspicious'),
            (5,'Cancelled'),
            (6,'ChargebackFraud'),
            (7,'ChargebackOther'),
            (8,'Refunded'),
            (9,'Placed'),
            (10,'OnHoldReview')
    ON DUPLICATE KEY UPDATE `name`=VALUES(`name`);
	");

$installer->run("
    UPDATE `{$this->getTable('sales/order_status')}`
    SET `trustev_status_id` = (CASE WHEN `status` = 'canceled' THEN 5
                                  WHEN `status` = 'cancel_ogone' THEN 5
                                  WHEN `status` = 'closed' THEN 8
                                  WHEN `status` = 'complete' THEN 1
                                  WHEN `status` = 'decline_ogone' THEN 3
                                  WHEN `status` = 'fraud' THEN 2
                                  WHEN `status` = 'holded' THEN 10
                                  WHEN `status` = 'payment_review' THEN 5
                                  WHEN `status` = 'paypal_canceled_reversal' THEN 5
                                  WHEN `status` = 'paypal_reversed' THEN 8
                                  WHEN `status` = 'pending' THEN 9
                                  WHEN `status` = 'pending_ogone' THEN 9
                                  WHEN `status` = 'pending_payment' THEN 9
                                  WHEN `status` = 'pending_paypal' THEN 9
                                  WHEN `status` = 'processed_ogone' THEN 9
                                  WHEN `status` = 'processing' THEN 9
                                  WHEN `status` = 'processing_ogone' THEN 9
                                  WHEN `status` = 'waiting_authorozation' THEN 9
                                END)
    WHERE `status` IN ('canceled',
                      'cancel_ogone',
                      'closed',
                      'complete',
                      'decline_ogone',
                      'fraud',
                      'holded',
                      'payment_review',
                      'paypal_canceled_reversal',
                      'paypal_reversed',
                      'pending',
                      'pending_ogone',
                      'pending_payment',
                      'pending_paypal',
                      'processed_ogone',
                      'processing',
                      'processing_ogone',
                      'waiting_authorozation');
  ");
$installer->endSetup();
