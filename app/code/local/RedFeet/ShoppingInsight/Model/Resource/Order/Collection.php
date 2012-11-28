<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Collection
 *
 * @author Usuario
 */
class RedFeet_ShoppingInsight_Model_Resource_Order_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    //put your code here
    public function _construct() {
        parent::_construct();
        $this->_init('shoppinginsight/order', 'order_id');
    }
}
