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

class Dropfin_Faq_Helper_Data extends Mage_Core_Helper_Abstract {
	
	const XML_PATH_ENABLED = 'dropfin_faq/general/enable';
	const XML_PATH_CATEGORY_ENABLED = 'dropfin_faq/general/category_option';

	public function _getStoreConfig($xml) {
		return Mage::getStoreConfig($xml);
	}

	public function isEnable() {
		return $this->_getStoreConfig(self::XML_PATH_ENABLED);
	}

	public function isCategoryEnable() {
		return $this->_getStoreConfig(self::XML_PATH_CATEGORY_ENABLED);
	}

}