<?php

/* AWeber Magento API Integration
 * (C) AWeber Communications, Inc
 *
 * Config Model
 *
 * This module contains the WASP compatible model for this api integration
 */


require_once(dirname(__FILE__) . "/WASP.php");


class AWeberCommunications_EmailMarketing_Model_Config extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('emailmarketing/config');
        $this->waspQueue = Mage::getModel('emailmarketing/queue');
        $this->WASP = new AWeberWaspModule($this);
    }

    /* waspCacheGet()
     *
     * retrieves the VALUE of the $NAME key from the session
     */ 
    public function waspCacheGet($name) {
        $session = Mage::getSingleton('customer/session');
        return $session['AWeberCommunications_' . $name];
    }

     /* waspCacheSet()
     *
     * stores the $VALUE of the $NAME key in the session
     */ 
    public function waspCacheSet($name, $value) {
        $session = Mage::getSingleton('customer/session');
        $session['AWeberCommunications_' . $name] = $value;
        return $value;
    }

    /* waspDelete()
     *
     * deletes the value of the $NAME key in the database
     */ 
    public function waspDelete($name) {
        return $this->waspSet($name, null);
    }


     /* waspSet()
     *
     * stores the $VALUE of the $NAME key in persistent storage
     */ 
    public function waspSet($name, $value) {

        $config = Mage::getModel('emailmarketing/config');
        $config->load($name, 'name');
        $config->setName($name);
        $config->setValue(json_encode($value));
        $config->save();
        return $value;
    }

    /* waspGet()
     *
     * retrieves the VALUE of the $NAME key from persistent storage
     */ 
    public function waspGet($name) {

        $config = Mage::getModel('emailmarketing/config');
        $config->load($name, 'name');
        return json_decode($config->getValue(), true);
    }

    /* waspProducts()
     *
     * return an associative array of products
     * @return: array('product_id' => 'product_name', ...)
     */ 
    public function waspProducts() {
        $product_list = array();

        $products = Mage::getModel('catalog/product')->getCollection();
        $products->addAttributeToSelect('name');

        foreach ($products as $product) {
            $product_list[$product->getId()] = $product->getName();
        }

        return $product_list;
    }
}

