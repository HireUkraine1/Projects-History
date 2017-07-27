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

class Dropfin_Faq_Model_Mysql4_Faq extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the question_id refers to the key field in your database table.
        $this->_init('faq/faq', 'question_id');
    }

	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		if($object->getId()){
			$select = $this->_getReadAdapter()->select()->from($this->getTable('faq/faq_store'))
							->where('question_id = ?', $object->getId());

			if ($data = $this->_getReadAdapter()->fetchAll($select)) {
				$stores = array ();
				foreach ($data as $row) {
					$stores[] = $row['store_id'];
				}
				$object->setData('store_id', $stores);
			}

			$faqSelectedCategory = $this->_getReadAdapter()->select()->from($this->getTable('faq/category_item'))
										->where('question_id = '.$object->getId());

			if ($data = $this->_getReadAdapter()->fetchAll($faqSelectedCategory)) {
				$categoryId = array ();
				foreach ($data as $row) {
					$categorySelect = $this->_getReadAdapter()->select()->from($this->getTable('faq/category'))
								->where($row['category_id'].' = category_id');
					if ($categoryData = $this->_getReadAdapter()->fetchAll($categorySelect)) {
						foreach ($categoryData as $categoryRow) {
							$categoryId[] = $categoryRow['category_id'];
						}
					}
				}
				$object->setData('category_id', $categoryId);
			}
		}
		return parent::_afterLoad($object);
	}

	protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedTime(Mage::getSingleton('core/date')->gmtDate());
        return parent::_beforeSave($object);
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		$condition = $this->_getWriteAdapter()->quoteInto('question_id = ?', $object->getId());
		
		// assign store to faq item
		$this->_getWriteAdapter()->delete($this->getTable('faq/faq_store'), $condition);
		foreach ((array) $object->getData('store_id') as $store) {
			$stores = array ();
			$stores['question_id'] = $object->getId();
			$stores['store_id'] = $store;
			$this->_getWriteAdapter()->insert(
				$this->getTable('faq/faq_store'), $stores
			);
		}
		
		// assign category to faq item
        $this->_getWriteAdapter()->delete($this->getTable('faq/category_item'), $condition);
        foreach ((array) $object->getData('category_id') as $categoryId) {
            $categorys = array ();
            $categorys['question_id'] = $object->getId();
            $categorys['category_id'] = $categoryId;
            $this->_getWriteAdapter()->insert(
                $this->getTable('faq/category_item'), $categorys
            );
        }
		
		return parent::_afterSave($object);
	}
}