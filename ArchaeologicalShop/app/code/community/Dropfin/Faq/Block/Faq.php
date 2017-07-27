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

class Dropfin_Faq_Block_Faq extends Mage_Core_Block_Template {

    protected $_storeId = 0;
	protected function _construct() 
    {
        parent::_construct();

        $this->_storeId = Mage::app()->getStore()->getId();

        if(Mage::helper('faq')->isCategoryEnable()) {
        	$this->setTemplate('dropfin/faq/list_with_category.phtml');
        } else {
        	$this->setTemplate('dropfin/faq/list_without_category.phtml');
        }        
    }

    public function getFaqItems($category = null)
    {
        $itemCollection = Mage::getModel('faq/faq')->getCollection()
                            ->addStoreFilter($this->_storeId)
                            ->addFieldToFilter('status', array('eq' => 1))
                            ;
        if($category) {
            $itemCollection->addCategoryFilter($category);
        }
        $itemCollection->setOrder('case when main_table.display_order = 0 then 1 else 0 end, main_table.display_order', 'ASC');
        return $itemCollection;
    }

    public function getCategoryList() 
    {
        $categoryCollection = Mage::getModel('faq/category')->getCollection()
                                ->addStoreFilter($this->_storeId)
                                ->addFieldToFilter('status', array('eq' => 1))
                                ;
        $categoryCollection->setOrder('case when main_table.display_order = 0 then 1 else 0 end, main_table.display_order', 'ASC');
    	return $categoryCollection;
    }
    
}