<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order's statuses grid
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magedev_Ordermanager_Block_Adminhtml_Order_Status_Grid extends Mage_Adminhtml_Block_Sales_Order_Status_Grid
{
     protected function _prepareColumns()
    {
        $this->addColumn('label', array(
            'header' => Mage::helper('sales')->__('Status'),
            'index' => 'label',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('sales')->__('Status Code'),
            'type'  => 'text',
            'index' => 'status',
            'filter_index' => 'main_table.status',
            'width'     => '200px',
        ));

		 $this->addColumn('colour', array(
            'header' => Mage::helper('sales')->__('Status Colour'),
            'type'  => 'text',
            'index' => 'colour',
            'filter_index' => 'main_table.colour',
            'width'     => '200px',
        ));
		
        $this->addColumn('is_default', array(
            'header'    => Mage::helper('sales')->__('Default Status'),
            'index'     => 'is_default',
            'width'     => '100px',
            'type'      => 'options',
            'options'   => array(0 => $this->__('No'), 1 => $this->__('Yes')),
            'sortable'  => false,
        ));

        $this->addColumn('state', array(
            'header'=> Mage::helper('sales')->__('State Code [State Title]'),
            'type'  => 'text',
            'index' => 'state',
            'width'     => '250px',
            'frame_callback' => array($this, 'decorateState')
        ));

        $this->addColumn('unassign', array(
            'header'    => Mage::helper('sales')->__('Action'),
            'index'     => 'unassign',
            'width'     => '100px',
            'type'      => 'text',
            'frame_callback' => array($this, 'decorateAction'),
            'sortable'  => false,
            'filter'    => false,
        ));

        return Mage_Adminhtml_Block_Widget_Grid::_prepareColumns();
    }

 }
