<?php

class RedFeet_ShoppingInsight_Block_List extends Mage_Catalog_Block_Product_Abstract{
    public $totals = array();
    public $total = 0;
    protected $enable = true;


    public function __construct(){
        $this->enable = Mage::getStoreConfig("shoppinginsight/config/active");
       
    }
    
    public function isEnabled(){
        return $this->enable;
    }
    
    protected function _prepareData()
    {
        if($this->enable){
            $product = Mage::registry('product');
            /* @var $product Mage_Catalog_Model_Product */
            $track = Mage::getModel("shoppinginsight/track");
            $ids = $track->getCountProductOrder($product->getId());
            $this->total = $track->getCountTotal($product->getId());

            foreach($ids as $id){
                $array[] = $id["product_id"];
                $this->totals[$id["product_id"]] = $id["ordered"];
            }
            $this->_itemCollection = $product->getCollection()
    //            ->addAttributeToSelect('required_options')
    //            ->addAttributeToSort('position', Varien_Db_Select::SQL_ASC)
                    ->addAttributeToFilter('entity_id', array('in'=>$array))
                ->addStoreFilter()
            ;

            if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
    //            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
    //                Mage::getSingleton('checkout/session')->getQuoteId()
    //            );
                $this->_addProductAttributesAndPrices($this->_itemCollection);
            }
    //        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
    //        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

            $this->_itemCollection->load();

    //        foreach ($this->_itemCollection as $product) {
    //            $product->setDoNotUseCategoryId(true);
    //        }
        }
        return $this;
       
    }
    
    public function getTotals(){
        return $this->totals;
    }
    
    public function getTotal(){
        return $this->total;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItems()
    {
        return $this->_itemCollection;
    }
}
