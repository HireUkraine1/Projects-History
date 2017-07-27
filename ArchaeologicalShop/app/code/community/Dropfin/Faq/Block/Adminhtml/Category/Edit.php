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

class Dropfin_Faq_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct() {

        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'faq';
        $this->_controller = 'adminhtml_category';

        $this->_updateButton('save', 'label', Mage::helper('faq')->__('Save Category'));
        $this->_updateButton('delete', 'label', Mage::helper('faq')->__('Delete Category'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('faq_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'faq_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'faq_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    protected function _prepareLayout() {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    public function getHeaderText()
    {
        if( Mage::registry('category_data') && Mage::registry('category_data')->getId() ) {
            return Mage::helper('faq')->__("Edit Category '%s'", $this->htmlEscape(Mage::registry('category_data')->getCategoryName()));
        } else {
            return Mage::helper('faq')->__('Add New Faq Category');
        }
    }
}