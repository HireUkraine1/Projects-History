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

class Dropfin_Faq_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() {
		$this->_controller = 'adminhtml_category';
		$this->_blockGroup = 'faq';
		$this->_headerText = Mage::helper('faq')->__('Faq Category Manager');
		$this->_addButtonLabel = Mage::helper('faq')->__('Add New Faq Category');
		parent::__construct();
	}

}