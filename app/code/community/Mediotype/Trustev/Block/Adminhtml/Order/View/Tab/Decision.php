<?php

/**
 * Class Mediotype_Trustev_Block_Adminhtml_Order_View_Tab_Decision
 */
class Mediotype_Trustev_Block_Adminhtml_Order_View_Tab_Decision
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * set template file for tab
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('mediotype/trustev/order/view/tab/decision.phtml');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Trustev Decision');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Trutev Decision');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * @return Mediotype_Trustev_Model_Request_Case
     */
    public function getTrustevCase()
    {
        return Mage::getModel('mediotype_trustev/request_case')->load($this->getOrder()->getId(),'order_id');
    }

    /**
     * @return string
     */
    public function getOrderStatusOptions()
    {
        $order = $this->getOrder();
        $html = '<span>No changes available</span>';
        switch($order->getStatus()){
            case 'pending':
                $targetUrl = $this->getUrl('trustev/adminhtml_index/setStatus', array("status"=>"trustev_review","order_id"=>$order->getId()));
                $html = "<button onclick=\"window.setLocation('{$targetUrl}')\"><span><span>Set Order to Trustev Review</span></span></button>";
                break;
            case 'trustev_review':
                $pendingUrl = $this->getUrl('trustev/adminhtml_index/setStatus', array("status"=>"pending","order_id"=>$order->getId()));;
                $fraudUrl = $this->getUrl('trustev/adminhtml_index/setStatus', array("status"=>"trustev_fraud","order_id"=>$order->getId()));;
                $html = "<button onclick=\"window.setLocation('{$pendingUrl}')\"><span><span>Set Order to Pending</span></span></button>
                        <br />
                        <br />
                        <button onclick=\"window.setLocation('{$fraudUrl}')\"><span><span>Set Order to Trustev Fraud</span></span></button>";
                break;
            case 'trustev_fraud':
                $pendingUrl = $this->getUrl('trustev/adminhtml_index/setStatus', array("status"=>"pending","order_id"=>$order->getId()));;
                $html = "<button onclick=\"window.setLocation('{$pendingUrl}')\"><span><span>Reinstate Order</span></span></button>";
                break;
            default:
                break;
        }
        return $html;
    }
}