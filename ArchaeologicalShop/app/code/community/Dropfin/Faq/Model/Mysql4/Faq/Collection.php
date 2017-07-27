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

class Dropfin_Faq_Model_Mysql4_Faq_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('faq/faq');
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
            array('store_table' => $this->getTable('faq/faq_store')),
            'main_table.question_id = store_table.question_id',
            array ()
        )->where('store_table.store_id in (?)', array (0, $store));
        
        return $this;
    }

    /**
    * Category filter for the grid items
    */
    public function addCategoryFilter($category){
        if ($category instanceof Dropfin_Faq_Model_Category) {
            $category = array($category->getId());
        }        
        $this->getSelect()->join(
            array('category_table' => $this->getTable('faq/category_item')),
            'main_table.question_id = category_table.question_id',
            array ()
        )->where('category_table.category_id in (?)', array (0, $category));
                
        return $this;
    }
    
}