<?php
class RedFeet_ShoppingInsight_Model_Observer extends Mage_Core_Model_Abstract{
    
//    public function __construct() {    	
//    }
    
    
    /**
     * 
     * @param Varien_Event_Observer $observer
     */
    public function addProductView(Varien_Event_Observer $observer){
        $product = $observer->getProduct();
        $customer = Mage::getSingleton("customer/session");
        if($customer->getId()){
            $cart = Mage::getModel('checkout/cart')->getQuote();
            
            $track = Mage::getModel("shoppinginsight/track");
            $session = $this->getSessionData();
           
            $track->getCountProductOrder(1);
            
            $track->setVisitorId($session["visitor_id"]);
            $track->setProductId($product->getId());
            $track->setCustomerId($session["customer_id"]);
            $track->setQuoteId($cart->getId());
            $track->setUrlReferer($session["referer"]);
            try{
                $track->save();
            }  catch (Exception $e){
//                echo $e->getMessage(); die();
            }

        }
    }
    /**
     * 
     * @param Varien_Event_Obeserver $observer
     */
    public function addOrderAfter(Varien_Event_Obeserver $observer){
        try{
            $order = $observer->getOrder();
            $quote = $observer->getQuote();

            $session = $this->getSessionData();

            $items = $order->getAllItems();
            foreach($items as $item){
                $trackOrder = Mage::getModel("shoppinginsight/order");
                $trackOrder->setVisitorId($session["visitor_id"]);
                $trackOrder->setProductId($item->getProductId());
                $trackOrder->setQuoteId($quote->getId());
                $trackOrder->save();
            }
        }  catch (Exception $e){
            Mage::throwException($e->getMessage()); 
        }
    }
    
    /**
     * 
     * @param Varien_Event_Obeserver $observer
     */
    public function loginProcess(Varien_Event_Obeserver $observer){
        //$customer = $observer->getCustomer();
    }
    
    /**
     * 
     * @return array data
     */
    public function getSessionData(){
        $session = Mage::getSingleton('core/session');
        $data = $session->getVisitorData();
        $referer = $data["http_referer"];
//        $session_id = $data["session_id"];
        $visitor_id = $data["visitor_id"];
        $customer_id = isset($data["customer_id"])?$data["customer_id"]:"";
//        print_r($data);
        return array(
            "referer" => $referer,
            "visitor_id" => $visitor_id,
            "customer_id" => $customer_id
        );
        
    }
}