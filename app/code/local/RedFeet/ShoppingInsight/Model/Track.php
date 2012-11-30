<?php


class RedFeet_ShoppingInsight_Model_Track extends Mage_Core_Model_Abstract {
    
    protected $_n = 4;
    function _construct()
    {
        $this->_init('shoppinginsight/track');
    }
    
    
    public function getCountProductOrder($visited){
        $collection = Mage::getModel("shoppinginsight/order")->getCollection();

        $collection->getSelect()
                ->join(array("t"=>"shoppinginsight_track"), "main_table.quote_id = t.quote_id", array("main_table.product_id", "ordered"=>"count(main_table.product_id)"))
                ->where("t.product_id=$visited" )
                ->group("main_table.product_id")
                ->order("ordered desc")
                ->limit($this->_n);
        //echo $collection->load(1);
//        echo "<pre>";
//        print_r($collection->getData());
        $data = $collection->getData();
        return $data;
        
    }
    
    public function getCountTotal($visited){
        $collection = Mage::getModel("shoppinginsight/order")->getCollection();

        $collection->getSelect()
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns(array("total"=>"count(main_table.product_id)"))
                ->join(array("t"=>"shoppinginsight_track"), "main_table.quote_id = t.quote_id", array( ))
                ->where("t.product_id=$visited" )
                ;
        $data = $collection->getData();
        return $data[0]["total"];
        
    }
}