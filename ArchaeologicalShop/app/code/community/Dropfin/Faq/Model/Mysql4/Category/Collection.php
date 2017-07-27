<?php
/**
 * Dropfin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade 
 * this extension to newer versions in the future. 
 *
 * @category    Dropfin
 * @package     FAQ
 * @copyright   Copyright (c) Dropfin (http://www.dropfin.com)
 */

class Dropfin_Faq_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('faq/category');
    }

    /**
    * Store filter for the grid items
    */
    public function addStoreFilter($store)
    {
    	if ($store instanceof Mage_Core_Model_Store) {
            $store = array ($store->getId());
        }

        $this->getSelect()->join(
            array('category_store' => $this->getTable('faq/category_store')),
            'main_table.category_id = category_store.category_id',
            array ()
        )->where('category_store.store_id in (?)', array (0, $store));
        
        return $this;
    }

    protected function _toOptionHash($valueField = 'category_id', $labelField = 'category_name')
    {
        return parent::_toOptionHash($valueField, $labelField);
    }
}