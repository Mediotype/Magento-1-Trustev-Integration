<?php
class Mediotype_Trustev_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    // trustev/adminhtml_index/index
    public function setStatusAction(){
        $orderId = $this->getRequest()->getParam('order_id', false);
        $status = $this->getRequest()->getParam('status', false);

        if(!$orderId || !$status){
            $this->_getSession()->addWarning('No Order Id or Status provided');
            $this->_redirectReferer();
            return;
        }
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);
        if(!$order->getId()){
            $this->_getSession()->addWarning('Failed to load order');
            $this->_redirectReferer();
            return;
        }

        try{
            $order->setStatus($status);
            $state = 'pending';
            if($status == 'trustev_review') {
                $state = 'holded';
            }
            if($status == 'trustev_fraud') {
                $state = 'payment_review';
            }
            $order->setState($state);
            $order->save();
            $this->_getSession()->addSuccess('Order status updated');

        } catch (Exception $e){
            $this->_getSession()->addWarning('Failed to save order status');
        }

        $this->_redirectReferer();

    }
}
