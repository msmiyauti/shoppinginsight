<?php
class RedFeet_ShoppingInsight_Model_Observer extends Mage_Core_Model_Abstract{
    
//    public function __construct() {    	
//    }
    
    
    public function addProductView(Varien_Event_Observer $observer){
        $product = $observer->getProduct();
        $customer = Mage::getSingleton("customer/session");
        if($customer->getId()){
            $cart = Mage::getModel('checkout/cart')->getQuote();
            //print_r($cart->getId()); 
            //die();
            $track = Mage::getModel("shoppinginsight/track");
            $session = $this->getSessionData();
            //print_r($track->load(19)->getData()); die();
           
            $track->setVisitorId($session["visitor_id"]);
            $track->setProductId($product->getId());
            $track->setCustomerId($session["customer_id"]);
            $track->setUrlReferer($session["referer"]);
            try{
                $track->save();
                print_r($track->getData());
            }  catch (Exception $e){
//                echo $e->getMessage(); die();
            }

        }
    }
    
    public function loginProcess(Varien_Event_Obeserver $observer){
        $customer = $observer->getCustomer();
        
    }
    
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