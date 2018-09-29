<?php
class Mediotype_Trustev_Model_Observer{
	/**
	 * @param Varien_Event_Observer $event
	 */
	public function postCaseAfterSaveOrder(Varien_Event_Observer $observer)
	{
        /** @var $newCase Mediotype_Trustev_Model_Request_Case */
        $newCase = Mage::getModel('mediotype_trustev/request_case');
        $dataHelper = $newCase->getDataHelper();
        if($dataHelper->getEnabled())
        {
            //set order data
            $newCase->buildCaseFromOrder($observer->getOrder()->getId());
            //make the api call
            $caseResponse = $newCase->apiCall();
            if ($caseResponse === false) {
                //we had an error
                Mage::logException(new Exception('Did not receive correct case data from Trustev'));
                return;
            }
            //we have a case, request a decision
            $decision = $newCase->getDecision();
            if(!$dataHelper->getIsTestMode())
            {
                // TODO: If desired, change order status at this time based on decision
            }
        }
	}

    /**
     * @param Varien_Event_Observer $event
     */
    public function checkStatusUpdate(Varien_Event_Observer $observer)
    {
        $order = $observer->getOrder();/** @var $order Mage_Sales_Model_Order */
        if ($order->hasDataChanges() && $order->getId()) {
            $case = Mage::getModel('mediotype_trustev/request_case')->load($order->getId(), 'order_id');
            $dataHelper = $case->getDataHelper();
            if ($case->getId() && $dataHelper->getEnabled()) {
                /** @var $case Mediotype_Trustev_Model_Request_Case */
                if ($order->getData('status') != $order->getOrigData('status')) {
                    $case->updateStatus($order->getData('status'));
                    return;
                }
                if (!$order->isCanceled()
                    && !$order->canUnhold()
                    && !$order->canInvoice()
                    && !$order->canShip()
                ) {
                    if (0 == $order->getBaseGrandTotal() || $order->canCreditmemo()) {
                        $case->updateStatus('complete', 'Order Complete');
                        return;
                    }
                }
                if (floatval($order->getTotalRefunded()) || (!$order->getTotalRefunded()
                        && $order->hasForcedCanCreditmemo())
                ) {
                    if ($order->getState() !== $order::STATE_CLOSED) {
                        $case->updateStatus('closed', 'Payment refunded');
                        return;
                    }
                }
                if ($order->getData('status') != 'processing' && $order->getIsInProcess()) {
                    $case->updateStatus('processing', 'Processing Payment and Shipping');
                }
            }
        }
    }

    /**
     * @param Varien_Event_Observer $event
     */
    public function parseBinSaveValue(Varien_Event_Observer $observer)
    {
        $payment        = $observer->getPayment(); /** @var  $payment Mage_Sales_Model_Quote_Payment */
        $data           = $observer->getInput();

        if($data->getData('cc_number') != null) {
            $payment->setData('cc_bin',Mage::helper('core')->encrypt(substr($data->getData('cc_number'),0,6)));
        }

    }
}