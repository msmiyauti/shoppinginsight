<?php
class RedFeet_ShoppingInsight_Model_Observer extends Mage_Core_Model_Abstract{
    
//    public function __construct() {    	
//    }
    
    
    /**
     * 
     * @param Varien_Event_Observer $observer
     */
    public function addProductView(Varien_Event_Observer $observer){
        $enable = Mage::getStoreConfig("shoppinginsight/config/active");
        if($enable){
            $product = $observer->getProduct();
            $customer = Mage::getSingleton("customer/session");
            if($customer->getId()){
                //$cart = Mage::getModel('checkout/cart')->getQuote();

                $track = Mage::getModel("shoppinginsight/track");
                $session = $this->getSessionData();
                $collection = Mage::getModel("shoppinginsight/track")->getCollection();

                $collection->getSelect()
                        ->where("visitor_id = {$session["visitor_id"]}")
                        ->where("product_id = {$product->getId()}")
                        ;
                $count = $collection->count();

                if(!$count){
                    $track->setVisitorId($session["visitor_id"]);
                    $track->setProductId($product->getId());
                    $track->setCustomerId($session["customer_id"]);
                    //$track->setQuoteId($cart->getId());
                    $track->setUrlReferer($session["referer"]);
                    try{
                        $track->save();
                    }  catch (Exception $e){
        //                echo $e->getMessage(); die();
                    }
                }
            }
        
        }
    }
    
    
    /**
     * 
     * @param Varien_Event_Obeserver $observer
     */
    public function addOrderAfter(Varien_Event_Obeserver $observer){
        $enable = Mage::getStoreConfig("shoppinginsight/config/active");
        if($enable){
            try{
                $this->trackUpdate($observer);
                
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
    }
    
    /**
     * 
     * @param Varien_Event_Obeserver $observer
     */
    public function loginProcess(Varien_Event_Obeserver $observer){
        //$customer = $observer->getCustomer();
    }
    
    
    public function trackUpdate(Varien_Event_Obeserver $observer){
        $enable = Mage::getStoreConfig("shoppinginsight/config/active");
        if($enable){
            $session = $this->getSessionData();
            $quote = Mage::getModel('checkout/session')->getQuote();

            try{
                $resource = Mage::getSingleton('core/resource');
                $table = $resource->getTableName('shoppinginsight/track');
                $writeConnection = $resource->getConnection('core_write');

                $query = "UPDATE {$table} SET quote_id = '{$quote->getId()}' WHERE quote_id = 0 and visitor_id = "
                     . (int) $session["visitor_id"];


                $writeConnection->query($query);
            }  catch (Exception $e){

            }
        }
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