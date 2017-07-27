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

class Dropfin_Faq_Block_Adminhtml_Category_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('faqGrid');
        $this->setDefaultSort('category_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('faq/category')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('category_id', array(
            'header'    => Mage::helper('faq')->__('Id'),
            'align'     =>'right',
            'width'     => '80px',
            'index'     => 'category_id',
            'filter_index' => 'main_table.category_id',
        ));

        $this->addColumn('category_name', array(
            'header'    => Mage::helper('faq')->__('Category'),
            'align'     =>'left',
            'index'     => 'category_name',
        ));

        $this->addColumn('display_order', array(
            'header'    => Mage::helper('faq')->__('Display Order'),
            'align'     =>'right',
            'width'     => '80px',
            'index'     => 'display_order',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('faq')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback' => array($this, '_filterStoreCondition'),
            ));
        }

        $this->addColumn('status', array(
            'header'    => Mage::helper('faq')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                1 => 'Enabled',
                2 => 'Disabled',
            ),
        ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('faq')->__('Action'),
                'width'     => '80px',
                'type'      => 'action',
                'align'     => 'center',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('faq')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('faq')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('faq')->__('XML'));
        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _prepareMassaction() {

        $this->setMassactionIdField('category_id');
        $this->getMassactionBlock()->setFormFieldName('category_id');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('faq')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('faq')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('faq/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('faq')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('faq')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

    protected function _filterStoreCondition($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addStoreFilter($value);
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}