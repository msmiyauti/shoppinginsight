<?php


class RedFeet_ShoppingInsight_Model_Track extends Mage_Core_Model_Abstract {
    
    function _construct()
    {
        $this->_init('shoppinginsight/track');
    }
    
    
    public function getCountProductOrder($visited){
        $collection = Mage::getModel("shoppinginsight/order")->getCollection();

        $collection->getSelect()
                ->join(array("t"=>"shoppinginsight_track"), "main_table.visitor_id = t.visitor_id", array("main_table.product_id", "ordered"=>"count(main_table.product_id)"))
                ->where("t.product_id=$visited" )
                ->group("main_table.product_id");
        //echo $collection->load(1);
//        echo "<pre>";
//        print_r($collection->getData());
        $data = $collection->getData();
        return $data;
        
    }
}