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

class Dropfin_Faq_Block_Adminhtml_Faq_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {

        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('faq_form', array('legend'=>Mage::helper('faq')->__('Faq item information')));

        $storeId = $fieldset->addField('store_id', 'multiselect', array (
            'name' => 'store_id[]', 
            'label' => Mage::helper('faq')->__('Store view'),
            'title' => Mage::helper('faq')->__('Store view'),
            'required' => true,
            'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
        ));

        $categories = Mage::getModel('faq/category')->getCollection()
                        ->addFieldToFilter('status', array('eq' => 1))
                        ->toOptionHash();
        if(count($categories) > 0) {
            $categoryId = $fieldset->addField('category_id', 'multiselect', array(
                'name' => 'category_id',
                'label' => Mage::helper('faq')->__('Category'),
                'title' => Mage::helper('faq')->__('Category'),
                'required' => false,
                'options' => $categories,
            ));
        }

        $fieldset->addField('question', 'text', array(
            'label'     => Mage::helper('faq')->__('Question'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'question',
            'style'     => 'width:500px;',
        ));

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
        $fieldset->addField('answer', 'editor', array(
            'name'      => 'answer',
            'label'     => Mage::helper('faq')->__('Answer'),
            'title'     => Mage::helper('faq')->__('Answer'),
            'style'     => 'width:700px; height:300px;',
            'config'    => $wysiwygConfig,
            'wysiwyg'   => true,
            'required'  => true
        ));

        $fieldset->addField('display_order', 'text', array(
            'label'     => Mage::helper('faq')->__('Display Order'),
            'name'      => 'display_order',
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

        if ( Mage::getSingleton('adminhtml/session')->getFaqData() ) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getFaqData());
            Mage::getSingleton('adminhtml/session')->setFaqData(null);
        } elseif ( Mage::registry('faq_data') ) {
            $form->setValues(Mage::registry('faq_data')->getData());
        }

        if(count($categories) > 0) {
            $model = Mage::registry('faq_data');
            $cat_id = $model->getData('category_id');
            $selected = $cat_id ? $cat_id : "";
            $this->setChild('form_after', $this->getLayout()->createBlock('faq/adminhtml_widget_form_element_selectdependence')
                ->addFieldMap($storeId->getHtmlId(), $categoryId->getHtmlId(), $selected)
            );
        }
        return parent::_prepareForm();
    }
}