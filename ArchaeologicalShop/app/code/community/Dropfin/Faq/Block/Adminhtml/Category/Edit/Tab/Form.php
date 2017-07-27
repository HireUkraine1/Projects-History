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

class Dropfin_Faq_Block_Adminhtml_Category_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('category_form', array('legend'=>Mage::helper('faq')->__('Faq category information')));

        $fieldset->addField('category_name', 'text', array(
            'label'     => Mage::helper('faq')->__('Category Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'category_name',
        ));        

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        $fieldset->addField('category_description', 'editor', array(
            'name'      => 'category_description',
            'label'     => Mage::helper('faq')->__('Category Description'),
            'title'     => Mage::helper('faq')->__('Category Description'),
            'style'     => 'width:700px; height:300px;',
            'config'    => $wysiwygConfig,
            'wysiwyg'   => true,
            'required'  => false
        ));

        $fieldset->addField('display_order', 'text', array(
            'label'     => Mage::helper('faq')->__('Display Order'),
            'name'      => 'display_order',
        ));

        $store_id = $fieldset->addField('store_id', 'multiselect', array (
            'name' => 'store_id[]',
            'label' => Mage::helper('faq')->__('Store view'),
            'title' => Mage::helper('faq')->__('Store view'),
            'required' => true,
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('faq')->__('Status'),
            'name'      => 'status',
            'values'    => array(
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('faq')->__('Enabled'),
                ),
                array(
                    'value'     => 2,
                    'label'     => Mage::helper('faq')->__('Disabled'),
                ),
            ),
        ));

        if ( Mage::getSingleton('adminhtml/session')->getCategoryData() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getCategoryData());
            Mage::getSingleton('adminhtml/session')->setCategoryData(null);
        } elseif ( Mage::registry('category_data') ) {
            $form->setValues(Mage::registry('category_data')->getData());
        }
        
        return parent::_prepareForm();
    }
}