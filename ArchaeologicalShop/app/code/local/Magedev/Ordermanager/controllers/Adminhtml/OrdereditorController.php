<?php
/**
 * Magento Order Grid Module
 *
 * NOTICE OF LICENSE 
 *
 * This source file is subject to the License Version.
 * DISCLAIMER
 *
 * @category   Order Grid
 * @package    Magedev_Grid
 * @copyright  Copyright (c) 2010-2014
 * @version    1.0.0
*/ 
require_once 'Zend/Json/Decoder.php'; 
include_once('Mage/Adminhtml/controllers/Sales/OrderController.php');
class Magedev_Ordermanager_Adminhtml_OrdereditorController extends Mage_Adminhtml_Controller_action
{
	private $_order;
 
    protected function _isAllowed()
    {
        return true;
    }
 
    public function doMassInvoiceAction()// invoice + Capture(offline)
	{

		$orderIds = $this->getRequest()->getPost('order_ids', array());
        $count = $this->createInvoiceOnly($orderIds);
        if ($count>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were invoiced successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	public function massDeleteInvoiceAction()
	{
		$prefix = Mage::getConfig()->getTablePrefix();
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		$writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$allDelete = 1;
		
		$statusArray = Mage::getStoreConfig('orderview/delete_inv_mng/del_inv_if');
		$statusArray = explode(",",$statusArray);
		$statusArray = array_filter($statusArray);
		if(count($statusArray)<=0){$allDelete = 2;}
		$countDeleteInvoice = 0;
		
		foreach ($orderIds as $orderId) {
		  $orderId = intval($orderId);
		  if (isset($orderId) && $orderId != "") {
		  
			   $orderId = $orderId;
			   $resource = Mage::getSingleton('core/resource');
			   $writeConnection = $resource->getConnection('core_write');
			   $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
			   
			   $order = Mage::getModel('sales/order')->load($orderId);
			   $orderStatus = $order->getStatus();
			if(in_array($orderStatus,$statusArray) || $allDelete == 2)
			{
				if ($order->hasInvoices()) {
					$invoices = Mage::getResourceModel('sales/order_invoice_collection')->setOrderFilter($orderId)->load();
					foreach($invoices as $invoice){
						$invoice = Mage::getModel('sales/order_invoice')->load($invoice->getId());
						$invoice->delete();
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_invoice')."` WHERE `order_id`=".$orderId);
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_invoice_grid')."` WHERE `order_id`=".$orderId);
						$countDeleteInvoice++;
					}
				
					foreach ($order->getAllItems() as $item) 
					{
						$item['qty_canceled'] = 0;
						$item['qty_invoiced'] = 0;
						$item['qty_refunded'] = 0;
						$item['qty_shipped'] = 0;
						
						$item['row_invoiced'] = 0;
						$item['base_row_invoiced'] = 0;
						$item['tax_invoiced'] = 0;
						$item['base_tax_invoiced'] = 0;
						$item['discount_invoiced'] = 0;
						$item['base_discount_invoiced'] = 0;
						
						$item['amount_refunded'] = 0;
						$item['base_amount_refunded'] = 0;
						$item['tax_refunded'] = 0;
						$item['base_tax_refunded'] = 0;
						$item['discount_refunded'] = 0;
						$item['base_discount_refunded'] = 0;
						
						
						$item->save();
					}
					$order->setStatus('pending');
					$order->setState('new');
					
					$order->setBaseShippingInvoiced(0);
					$order->setBaseSubtotalInvoiced(0);
					$order->setBaseTaxInvoiced(0);
					$order->setBaseTotalInvoiced(0);
					$order->setBaseTotalInvoicedCost(0);
					$order->setDiscountInvoiced(0);
					$order->setShippingInvoiced(0);
					$order->setSubtotalInvoiced(0);
					$order->setTaxInvoiced(0);
					$order->setTotalInvoiced(0);
					
					$order->setBaseTaxRefunded(0);
					$order->setBaseTaxCanceled(0);
					$order->setBaseTotalCanceled(0);
					$order->setBaseTotalRefunded(0);
					$order->setDiscountRefunded(0);
					$order->setTaxRefunded(0);
					$order->setTotalRefunded(0);
					
					$order->setBaseTotalPaid(0);
					$order->setTotalPaid(0);
					$order->save();
				}
				
			}
			
			if($countDeleteInvoice >0)
			{
				$this->_getSession()->addSuccess($this->__('Invoice(s) were successfully deleted.'));
				$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/");
			}else{
					$this->_getSession()->addNotice($this->__('Selected Order(s) Status are not allow to delete Invoice, to Delete please update Grid Manager config settings.'));
					$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/");

			}
			$this->_redirectUrl($path);

		}
	  }
	
	}
	
	public function massDeleteShipAction()
	{
		$prefix = Mage::getConfig()->getTablePrefix();
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		$writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$allDelete = 1;
		
		$statusArray = Mage::getStoreConfig('orderview/delete_ship_mng/del_ship_if');
		$statusArray = explode(",",$statusArray);
		$statusArray = array_filter($statusArray);
		if(count($statusArray)<=0){$allDelete = 2;}
		$countDeleteShip = 0;
		foreach ($orderIds as $orderId)
		{
			  $orderId = intval($orderId);
			  if (isset($orderId) && $orderId != "")
			  {
			   $orderId = $orderId;
			   $coreResource = Mage::getSingleton('core/resource');
			   $write = $coreResource->getConnection('core_write');
			   $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
			   
			   $order = Mage::getModel('sales/order')->load($orderId);
			   $orderStatus = $order->getStatus();
			   if(in_array($orderStatus,$statusArray) || $allDelete == 2)
			   {
			   		if ($order->hasShipments()) {
						$shipments = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($orderId)->load();
						foreach($shipments as $shipment)
						{
							$shipment = Mage::getModel('sales/order_shipment')->load($shipment->getId());
							$shipment->delete();
							$write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_shipment')."` WHERE `order_id`=".$orderId);            
							$write->query("DELETE FROM `".$coreResource->getTableName('sales_flat_shipment_grid')."` WHERE `order_id`=".$orderId);            
							$countDeleteShip++;
						}
						$order->setStatus('pending');
						$order->setState('new');
						$order->save();

					}
			   }
			}
		}

			if($countDeleteShip >0)
			{
				$this->_getSession()->addSuccess($this->__('Shipping(s) were successfully deleted.'));
				$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/");
			}else{
					$this->_getSession()->addNotice($this->__('Selected Order(s) Status are not allow to delete Shipping, to Delete please update Grid Manager config settings.'));
					$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/");

			}
			$this->_redirectUrl($path);
	}
	
	public function massDeleteCreditMemoAction()
	{
		$prefix = Mage::getConfig()->getTablePrefix();
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		$writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$allDelete = 1;
		
		$statusArray = Mage::getStoreConfig('orderview/delete_credit_mng/delete_credit_if');
		$statusArray = explode(",",$statusArray);
		$statusArray = array_filter($statusArray);
		if(count($statusArray)<=0){$allDelete = 2;}
		$countDeleteCreditMemo = 0;
		
		foreach ($orderIds as $orderId) {
		  $orderId = intval($orderId);
		  if (isset($orderId) && $orderId != "") {
		  
			   $orderId = $orderId;
			   $resource = Mage::getSingleton('core/resource');
			   $writeConnection = $resource->getConnection('core_write');
			   $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
			   
			   $order = Mage::getModel('sales/order')->load($orderId);
			   $orderStatus = $order->getStatus();
			if(in_array($orderStatus,$statusArray) || $allDelete == 2)
			{
				if ($order->hasInvoices() || $order->hasCreditmemos()) {
				
					$invoices = Mage::getResourceModel('sales/order_invoice_collection')->setOrderFilter($orderId)->load();
					foreach($invoices as $invoice){
						$invoice = Mage::getModel('sales/order_invoice')->load($invoice->getId());
						$invoice->delete();
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_invoice')."` WHERE `order_id`=".$orderId);
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_invoice_grid')."` WHERE `order_id`=".$orderId);
					}
										
					$creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')->setOrderFilter($orderId)->load();
					foreach($creditmemos as $creditmemo){
						$creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemo->getId());
						$creditmemo->delete();
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_creditmemo')."` WHERE `order_id`=".$orderId);
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_creditmemo_grid')."` WHERE `order_id`=".$orderId);

						$countDeleteCreditMemo++;
					}
					
					foreach ($order->getAllItems() as $item) 
					{
						$item['qty_canceled'] = 0;
						$item['qty_invoiced'] = 0;
						$item['qty_refunded'] = 0;
						$item['qty_shipped'] = 0;
						
						$item['row_invoiced'] = 0;
						$item['base_row_invoiced'] = 0;
						$item['tax_invoiced'] = 0;
						$item['base_tax_invoiced'] = 0;
						$item['discount_invoiced'] = 0;
						$item['base_discount_invoiced'] = 0;
						
						$item['amount_refunded'] = 0;
						$item['base_amount_refunded'] = 0;
						$item['tax_refunded'] = 0;
						$item['base_tax_refunded'] = 0;
						$item['discount_refunded'] = 0;
						$item['base_discount_refunded'] = 0;
						
						
						$item->save();
					}
					$order->setStatus('pending');
					$order->setState('new');
					
					$order->setBaseShippingInvoiced(0);
					$order->setBaseSubtotalInvoiced(0);
					$order->setBaseTaxInvoiced(0);
					$order->setBaseTotalInvoiced(0);
					$order->setBaseTotalInvoicedCost(0);
					$order->setDiscountInvoiced(0);
					$order->setShippingInvoiced(0);
					$order->setSubtotalInvoiced(0);
					$order->setTaxInvoiced(0);
					$order->setTotalInvoiced(0);
					
					$order->setBaseTaxRefunded(0);
					$order->setBaseTaxCanceled(0);
					$order->setBaseTotalCanceled(0);
					$order->setBaseTotalRefunded(0);
					$order->setDiscountRefunded(0);
					$order->setTaxRefunded(0);
					$order->setTotalRefunded(0);
					
					$order->setBaseTotalPaid(0);
					$order->setTotalPaid(0);
					$order->save();
				}
				
			}
			
			if($countDeleteCreditMemo >0)
			{
				$this->_getSession()->addSuccess($this->__('Credit Memo(s) were successfully deleted.'));
				$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/");
			}else{
					$this->_getSession()->addNotice($this->__('Selected Order(s) Status are not allow to delete Invoice, to Delete please update Grid Manager config settings.'));
					$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/");

			}
			$this->_redirectUrl($path);

		}
	  }
	
	
	}
	public function massDeleteINCAction()//delete invoice,shio,creditmemo
	{
		$prefix = Mage::getConfig()->getTablePrefix();
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		$writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$allDelete = 1;
		
		$statusArray = Mage::getStoreConfig('orderview/delete_credit_mng/delete_credit_if');
		$statusArray = explode(",",$statusArray);
		$statusArray = array_filter($statusArray);
		if(count($statusArray)<=0){$allDelete = 2;}
		$countDeleteINC = 0;
		$isDeleted = 1;
		
		foreach ($orderIds as $orderId) {
		  $orderId = intval($orderId);
		  if (isset($orderId) && $orderId != "") {
		  
			   $orderId = $orderId;
			   $resource = Mage::getSingleton('core/resource');
			   $writeConnection = $resource->getConnection('core_write');
			   $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
			   
			   $order = Mage::getModel('sales/order')->load($orderId);
			   $orderStatus = $order->getStatus();
			if(in_array($orderStatus,$statusArray) || $allDelete == 2)
			{
				if ($order->hasInvoices() || $order->hasCreditmemos() || $order->hasShipments()) {
				
					$invoices = Mage::getResourceModel('sales/order_invoice_collection')->setOrderFilter($orderId)->load();
					foreach($invoices as $invoice){
						$invoice = Mage::getModel('sales/order_invoice')->load($invoice->getId());
						$invoice->delete();
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_invoice')."` WHERE `order_id`=".$orderId);
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_invoice_grid')."` WHERE `order_id`=".$orderId);
						$isDeleted = 2;
					}
										
					$creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')->setOrderFilter($orderId)->load();
					foreach($creditmemos as $creditmemo){
						$creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemo->getId());
						$creditmemo->delete();
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_creditmemo')."` WHERE `order_id`=".$orderId);
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_creditmemo_grid')."` WHERE `order_id`=".$orderId);
						$isDeleted = 2;
					}
					
					if ($order->hasShipments()) {
						$shipments = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($orderId)->load();
						foreach($shipments as $shipment){
							$shipment = Mage::getModel('sales/order_shipment')->load($shipment->getId());
							$shipment->delete();
							$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_shipment')."` WHERE `order_id`=".$orderId);            
							$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_shipment_grid')."` WHERE `order_id`=".$orderId);            
							$isDeleted = 2;
						}
					}
			
					if($isDeleted == 2)$countDeleteINC++;
			
					foreach ($order->getAllItems() as $item) 
					{
						$item['qty_canceled'] = 0;
						$item['qty_invoiced'] = 0;
						$item['qty_refunded'] = 0;
						$item['qty_shipped'] = 0;
						
						$item['row_invoiced'] = 0;
						$item['base_row_invoiced'] = 0;
						$item['tax_invoiced'] = 0;
						$item['base_tax_invoiced'] = 0;
						$item['discount_invoiced'] = 0;
						$item['base_discount_invoiced'] = 0;
						
						$item['amount_refunded'] = 0;
						$item['base_amount_refunded'] = 0;
						$item['tax_refunded'] = 0;
						$item['base_tax_refunded'] = 0;
						$item['discount_refunded'] = 0;
						$item['base_discount_refunded'] = 0;
						
						
						$item->save();
					}
					$order->setStatus('pending');
					$order->setState('new');
					
					$order->setBaseShippingInvoiced(0);
					$order->setBaseSubtotalInvoiced(0);
					$order->setBaseTaxInvoiced(0);
					$order->setBaseTotalInvoiced(0);
					$order->setBaseTotalInvoicedCost(0);
					$order->setDiscountInvoiced(0);
					$order->setShippingInvoiced(0);
					$order->setSubtotalInvoiced(0);
					$order->setTaxInvoiced(0);
					$order->setTotalInvoiced(0);
					
					$order->setBaseTaxRefunded(0);
					$order->setBaseTaxCanceled(0);
					$order->setBaseTotalCanceled(0);
					$order->setBaseTotalRefunded(0);
					$order->setDiscountRefunded(0);
					$order->setTaxRefunded(0);
					$order->setTotalRefunded(0);
					
					$order->setBaseTotalPaid(0);
					$order->setTotalPaid(0);
					$order->save();
				}
				
			}
			
			if($countDeleteINC >0)
			{
				$this->_getSession()->addSuccess($this->__('Invoice, Shipping, Credit Memo were successfully deleted.'));
				$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/");
			}else{
					$this->_getSession()->addNotice($this->__('Selected Order(s) Status are not allow to delete Invoice, to Delete please update Grid Manager config settings.'));
					$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/");

			}
			$this->_redirectUrl($path);

		}
	  }
	}
	
	public function massDeleteOrderAction()
	{
		$prefix = Mage::getConfig()->getTablePrefix();
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		$writeConnection = Mage::getSingleton('core/resource')->getConnection('core_write');
		$allDelete = 1;
		
		$statusArray = Mage::getStoreConfig('orderview/deletemng/del_ord_if');
		$statusArray = explode(",",$statusArray);
		$statusArray = array_filter($statusArray);
		if(count($statusArray)<=0){$allDelete = 2;}


		/*Reset foreign checks to 1 Starts*/
			$writeConnection->query("SET FOREIGN_KEY_CHECKS=0");
		/*Reset foreign checks to 1 Ends*/
		$countDeleteOrder = 0;
		foreach ($orderIds as $orderId) {
		  $orderId = intval($orderId);
		  if (isset($orderId) && $orderId != "") {
		  
			   $orderId = $orderId;
			   $resource = Mage::getSingleton('core/resource');
			   $writeConnection = $resource->getConnection('core_write');
			   $connectionRead = Mage::getSingleton('core/resource')->getConnection('core_read');
			   
			   $order = Mage::getModel('sales/order')->load($orderId);
			   $orderStatus = $order->getStatus();
			if(in_array($orderStatus,$statusArray) || $allDelete == 2)
			{
	
			   $incrementId = $order['increment_id'];
			   $quoteId = $order['quote_id'];
		
		
		/*Delete Order Credit Memo related entries*/
			  if ($order->hasCreditmemos())
			  {
				$creditmemos = Mage::getResourceModel('sales/order_creditmemo_collection')->setOrderFilter($orderId)->load();
				foreach($creditmemos as $creditmemo){
					$creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemo->getId());
					$creditmemo->delete();
					$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_creditmemo')."` WHERE `order_id`=".$orderId);
					$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_creditmemo_grid')."` WHERE `order_id`=".$orderId);

				}
			 }
		/*Delete Order Credit Memo related entries Ends*/
		
		/*Delete Order Invoice related entries*/
				if ($order->hasInvoices())
				{
					$invoices = Mage::getResourceModel('sales/order_invoice_collection')->setOrderFilter($orderId)->load();
					foreach($invoices as $invoice){
						$invoice = Mage::getModel('sales/order_invoice')->load($invoice->getId());
						$invoice->delete();
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_invoice')."` WHERE `order_id`=".$orderId);
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_invoice_grid')."` WHERE `order_id`=".$orderId);

					}
				}
		/*Delete Order Invoice related entries Ends*/
		
		/*Delete Order Quote related entries*/
			   
			   if ($order->hasShipments())
			   {
					$shipments = Mage::getResourceModel('sales/order_shipment_collection')->setOrderFilter($orderId)->load();
					foreach($shipments as $shipment){
						$shipment = Mage::getModel('sales/order_shipment')->load($shipment->getId());
						$shipment->delete();
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_shipment')."` WHERE `order_id`=".$orderId);            
						$writeConnection->query("DELETE FROM `".$resource->getTableName('sales_flat_shipment_grid')."` WHERE `order_id`=".$orderId);
					}
				}
				
				
		/*Delete sales flat order related entries Starts*/
		
				$condition = "delete from ".$prefix."sales_flat_quote where entity_id = '".$quoteId."'";
				$writeConnection->query($condition);
				   
				$orderAllItems = $order->getAllItems();
				foreach($orderAllItems as $item)
				{
					$quoteItemId = $item->getQuoteItemId();
					
					$condition = "delete from ".$prefix."sales_flat_quote_item where item_id = '".$quoteItemId."'";
					$writeConnection->query($condition);
					
					$condition = "delete from ".$prefix."sales_flat_quote_item_option where item_id = '".$quoteItemId."'";
					$writeConnection->query($condition);
					
					$item->delete();
				}
		
				$addressIdReadQry = "select address_id from ".$prefix."sales_flat_quote_address where quote_id='".$quoteId."' and address_type = 'billing' ";
				$addressIdArr = $connectionRead->fetchRow($addressIdReadQry);
				
				$addressIdReadshipQry = "select address_id from ".$prefix."sales_flat_quote_address where quote_id='".$quoteId."' and address_type = 'shipping' ";
				$addressIdShippingArr = $connectionRead->fetchRow($addressIdReadshipQry);
				
				$addressIdReadQry1 = "select address_id from ".$prefix."sales_flat_quote_address where quote_id='".$quoteId."'";
				$quoteParentIdArr = $connectionRead->fetchRow($addressIdReadQry1);
				
				$condition = "delete from ".$prefix."sales_flat_quote_address where quote_id = '".$quoteId."'";
				$writeConnection->query($condition);
				
				$condition = "delete from ".$prefix."sales_flat_quote_payment where quote_id = '".$quoteId."'";
				$writeConnection->query( $condition);
				
				$condition = "delete from ".$prefix."sales_flat_order_status_history where parent_id = '".$orderId."'";
				$writeConnection->query( $condition);
				
				/*delete billing type*/
				$condition = "delete from ".$prefix."sales_flat_quote_shipping_rate where address_id = '".$addressIdArr['address_id']."'";
				$writeConnection->query($condition);
				
				/*delete shipping type*/
				$condition = "delete from ".$prefix."sales_flat_quote_shipping_rate where address_id = '".$addressIdShippingArr['address_id']."'";
				$writeConnection->query($condition);
				
				$condition = "delete from ".$prefix."sales_flat_quote_address_item where parent_item_id = '".$quoteParentIdArr['address_id']."'";
				$writeConnection->query($condition);
				
				$condition = "delete from ".$prefix."sales_flat_order_grid where entity_id = '".$orderId."'";
				$writeConnection->query( $condition);

		/*Delete Order Quote related entries Ends*/
				
				$condition = "delete from ".$prefix."sales_flat_order_address where parent_id = '".$orderId."'";
				$writeConnection->query( $condition);
				
				$condition = "delete from ".$prefix."sales_flat_order_payment where parent_id = '".$orderId."'";
				$writeConnection->query( $condition);
				
				$order->delete();
				$countDeleteOrder++;
		}
			}
		}
			/*Reset foreign checks to 1 Starts*/	
				$writeConnection->query("SET FOREIGN_KEY_CHECKS=1");
			/*Reset foreign checks to 1 Ends*/
			if($countDeleteOrder >0)
			{
				$this->_getSession()->addSuccess($this->__('Order(s) were successfully deleted.'));
				$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/");
			}else{
					$this->_getSession()->addNotice($this->__('Selected Order(s) Status are not allow to delete, to Delete please update Grid Manager config settings.'));
					$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order/");

			}
			$this->_redirectUrl($path);
	}
	
    public function doMassInvoiceonlyAction() // invoice only
	{

		$orderIds = $this->getRequest()->getPost('order_ids', array());
                         
        $countInvoiceOrder = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if (isset($orderId) && $orderId != "") {
                
				$order = Mage::getModel('sales/order')->load($orderId);
				if($order->canInvoice())//$invoice->getTotalQty()
				{
							$qtyToInvoice = array();
							$orderItems = $order->getAllItems();
							foreach ($orderItems as $orderItem) {
								$itemId = $orderItem->getId();
								$qtyToInvoice[$itemId] = $orderItem->getQtyToInvoice();
							}
							
							//$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
							$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($qtyToInvoice);
						if ($invoice->getTotalQty()) // check if return invoice obj has the few qty to invoice
						{
							//$invoice->setRequestedCaptureCase('offline');
							$invoice->register();
							
							$isToNotifyCustomer = Mage::getStoreConfig('orderview/general/notify_customer_oninvoice');
							
							if($isToNotifyCustomer)	$invoice->setEmailSent(true);
							if($isToNotifyCustomer) $invoice->getOrder()->setCustomerNoteNotify(true);
							
							$invoice->getOrder()->setIsInProcess(true);
							
							$transactionSave = Mage::getModel('core/resource_transaction')
												->addObject($invoice)
												->addObject($invoice->getOrder());
							$transactionSave->save();                
							
							if($isToNotifyCustomer) $invoice->sendEmail(true, '');
							
							$countInvoiceOrder++;
						}
				}
            }            
        }
    
	
        if ($countInvoiceOrder>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were invoiced successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	public function doMassShipAction()
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
        $count = $this->createShipOnly($orderIds);
        if ($count>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were shipped successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	public function doMassInvoiceCaptureAction()// invoice + Capture(offline)
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
        $count = $this->createInvoiceCaptureOnly($orderIds);
        if ($count>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were invoiced and captured successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	public function doMassInvoiceShipOffAction() // invoice + Capture(offline) + ship
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());

        $count = $this->createInvoiceOnly($orderIds);
        $count = $this->createShipOnly($orderIds);
		
        if ($count>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were invoiced and shipped successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	public function doMassInvoiceShipOnlineAction() // invoice + Capture(online) + ship
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());

        $count = $this->createInvoiceCaptureOnly($orderIds);
        $count = $this->createShipOnly($orderIds);
		
        if ($count>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were invoiced and shipped successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	
	public function doMassArchiveAction()
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
 		foreach ($orderIds as $orderId) {
			$order = Mage::getModel('sales/order')->load($orderId); 
			
			$order->setIsArchieved(1); 
            $order->save();
			
			$vals = array();
    		$vals['is_archieved'] = 1;
	
			$where = $write->quoteInto('entity_id =?', $orderId);
			
			$prefix = Mage::getConfig()->getTablePrefix();
			$archieveTable = $prefix."sales_flat_order_grid" ;
    		$write->update($archieveTable, $vals ,$where);
	
		}
        $countOrder = count($orderIds);

       if ($countOrder>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were archieved successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
	}
	
	public function doMassActiveAction() 
	{
		$orderIds = $this->getRequest()->getPost('order_ids', array());
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		foreach ($orderIds as $orderId) {
			$order = Mage::getModel('sales/order')->load($orderId); 
			
			$order->setIsArchieved(0);
            $order->save();
			
			$vals = array();
    		$vals['is_archieved'] = 0;
	
			$where = $write->quoteInto('entity_id =?', $orderId);
			
			$prefix = Mage::getConfig()->getTablePrefix();
			$activeTable = $prefix."sales_flat_order_grid" ;
    		$write->update($activeTable, $vals ,$where);
		}
        $countOrder = count($orderIds);

       if ($countOrder>0) Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sales')->__('Batch order(s) were Active successfully.'));
		 
		$path = Mage::helper('adminhtml')->getUrl("adminhtml/sales_order");
		$this->_redirectUrl($path);
		
	}
	
	
	private function _loadOrder($orderId) {
		$this->_order = Mage::getModel('sales/order')->load($orderId);
		if(!$this->_order->getId()) return false;
		return true;
	}
	
	public function saveinvoicestatusAction() {
	
		$field = $this->getRequest()->getParam('field');
		
		$invoiceId = $this->getRequest()->getParam('invoice_id');
		$value = $this->getRequest()->getPost('value');
 		
		if($field == "order_status") {
		$orderId = $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);
		//echo $order->getId();
				$order->setStatus($value)->save();
				$this->getResponse()->setBody(ucfirst($value));
				return true;
		}
		if($field == "order_name") {
			$orderId = $this->getRequest()->getParam('order_id');
			$order = Mage::getModel('sales/order')->load($orderId);
			$order->setIncrementId($value)->save();
			$this->getResponse()->setBody(ucfirst($value));
			return true;
		}
		
		if (!empty($field) && !empty($invoiceId)) {
			$invoice = Mage::getModel('sales/order_invoice')
                    ->load($invoiceId);
			$invoiceState = $invoice->setState($value);
			$invoice->save();

			$statuses = Mage::getModel('sales/order_invoice')->getStates();
			$invoiceState = $invoice->getState();
			if(isset($invoiceState))
			echo $invoiceStateLabel = $statuses[$invoiceState];
			else echo 'error in saving..';
		}
	}
	
	public function savetrackingAction() {
	 
		$field = $this->getRequest()->getParam('field');
		
		$orderId = $this->getRequest()->getParam('order_id');
		$trackingEntityId = $this->getRequest()->getParam('entity_id');
		$value = $this->getRequest()->getPost('value');
 		
		if($field == "tracking") {
			$order = Mage::getModel('sales/order')->load($orderId);
			
			$tracking = Mage::getResourceModel('sales/order_shipment_track_collection')
						->setOrderFilter($order)
						->addFieldToFilter('entity_id',$trackingEntityId)
						->getData();
			
			foreach($tracking as $sc){
				//$trackMethod = $sc->getTrackNumber($value);
				//$trackMethod ->save();
			}
			
			 $track = Mage::getModel('sales/order_shipment_track')
                 ->setData('number',  $value)
                 ->setData('entity_id', $trackingEntityId)
                 ->save();
				 
				 echo $value;die;
				 
		
			$tracking['track_number'] = $value;
			echo $tracking->save();die;
//		echo '<pre>';print_r($tracking);die;
				//$order->setStatus($value)->save();
				//$this->getResponse()->setBody(ucfirst($value));
				//return true;
		}
		
	}
	
	public function createShipOnly($orderIds)
    {                   
        $countShipOrder = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if (isset($orderId) && $orderId != "") {
                
				$order = Mage::getModel('sales/order')->load($orderId);
				if($order->canShip())
				{
							$itemToShip = array();
							$orderItems = $order->getAllItems();
							foreach ($orderItems as $orderItem) {
								$itemId = $orderItem->getId();
								$itemToShip[$itemId] = $orderItem->getQtyToShip();
							}
							

							$ship = Mage::getModel('sales/service_order', $order)->prepareShipment($itemToShip);
						if ($ship->getTotalQty()) // check if return invoice obj has the few qty to chip
						{
							$ship->register();
							
							$isToNotifyCustomer = Mage::getStoreConfig('orderview/general/notify_customer_onship');
							if($isToNotifyCustomer) $ship->setEmailSent(true);
							if($isToNotifyCustomer) $ship->getOrder()->setCustomerNoteNotify(true);
							
							$ship->getOrder()->setIsInProcess(true);
							
							$transactionSave = Mage::getModel('core/resource_transaction')
												->addObject($ship)
												->addObject($ship->getOrder());
							$transactionSave->save();                
							
							if($isToNotifyCustomer) $ship->sendEmail(true, '');
							
							$countShipOrder++;
						}
				}
            }            
        }
        return $countShipOrder;        
    }
	
	public function createInvoiceOnly($orderIds)//capture offline
    {                         
        $countInvoiceOrder = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if (isset($orderId) && $orderId != "") {
                
				$order = Mage::getModel('sales/order')->load($orderId);
				if($order->canInvoice())//$invoice->getTotalQty()
				{
							$qtyToInvoice = array();
							$orderItems = $order->getAllItems();
							foreach ($orderItems as $orderItem) {
								$itemId = $orderItem->getId();
								$qtyToInvoice[$itemId] = $orderItem->getQtyToInvoice();
							}
							
							//$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
							$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($qtyToInvoice);
						if ($invoice->getTotalQty()) // check if return invoice obj has the few qty to invoice
						{
							$invoice->setRequestedCaptureCase('offline');
							$invoice->register();
							
							$isToNotifyCustomer = Mage::getStoreConfig('orderview/general/notify_customer_oninvoice');
							if($isToNotifyCustomer) $invoice->setEmailSent(true);
							if($isToNotifyCustomer) $invoice->getOrder()->setCustomerNoteNotify(true);
							
							$invoice->getOrder()->setIsInProcess(true);
							
							$transactionSave = Mage::getModel('core/resource_transaction')
												->addObject($invoice)
												->addObject($invoice->getOrder());
							$transactionSave->save();                
							
							if($isToNotifyCustomer) $invoice->sendEmail(true, '');
							
							$countInvoiceOrder++;
						}
				}
            }            
        }
        return $countInvoiceOrder;        
    }
	
	public function createInvoiceCaptureOnly($orderIds) //capture online
    {                              
        $countInvoice = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if (isset($orderId) && $orderId != "") {
                
				$order = Mage::getModel('sales/order')->load($orderId);
				if($order->canInvoice())//$invoice->getTotalQty()
				{
							$savedQtys = array();
							$orderItems = $order->getAllItems();
							foreach ($orderItems as $orderItem) {
								$itemId = $orderItem->getId();
								$savedQtys[$itemId] = $orderItem->getQtyToInvoice();
							}
							
							//$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
							$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
						if ($invoice->getTotalQty()) // check if return invoice obj has the few qty to invoice
						{
							$invoice->setRequestedCaptureCase('online');
							$invoice->register();
							$invoice->getOrder()->setIsInProcess(true);
							
							$isToNotifyCustomer = Mage::getStoreConfig('orderview/general/notify_customer_oninvoice');
							if($isToNotifyCustomer) $invoice->setEmailSent(true);
							if($isToNotifyCustomer) $invoice->getOrder()->setCustomerNoteNotify(true);
							
							$transactionSave = Mage::getModel('core/resource_transaction')
												->addObject($invoice)
												->addObject($invoice->getOrder());
							$transactionSave->save();
							 
							if($isToNotifyCustomer) $invoice->sendEmail(true, '');
							
							$countInvoice++;
						}
				}
            }            
        }
        return $count;        
    }	
	
	 
	
	public function createInvoiceNCOnly($orderIds) // notify customer
    {                                
        $countInvoiceOrder = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if (isset($orderId) && $orderId != "") {
                
				$order = Mage::getModel('sales/order')->load($orderId);
				if($order->canInvoice())//$invoice->getTotalQty()
				{
							$savedQtys = array();
							$orderItems = $order->getAllItems();
							foreach ($orderItems as $orderItem) {
								$itemId = $orderItem->getId();
								$savedQtys[$itemId] = $orderItem->getQtyToInvoice();
							}
							
							//$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
							$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
						if ($invoice->getTotalQty()) // check if return invoice obj has the few qty to invoice
						{
							$invoice->register();
							
							$isToNotifyCustomer = Mage::getStoreConfig('orderview/general/notify_customer_oninvoice');
							if($isToNotifyCustomer) $invoice->setEmailSent(true);
							if($isToNotifyCustomer) $invoice->getOrder()->setCustomerNoteNotify(true);
							
							$invoice->getOrder()->setIsInProcess(true);
							
							$transactionSave = Mage::getModel('core/resource_transaction')
												->addObject($invoice)
												->addObject($invoice->getOrder());
							$transactionSave->save();                
							
							if($isToNotifyCustomer) $invoice->sendEmail(true, '');
							
							$countInvoiceOrder++;
						}
				}
            }            
        }
        return $count;        
    }
	

	
	public function createInvoiceCaptureNCOnly($orderIds)//notify customer
    {                                
        $count = 0;
        foreach ($orderIds as $orderId) {
            $orderId = intval($orderId);
            if (isset($orderId) && $orderId != "") {
                
				$order = Mage::getModel('sales/order')->load($orderId);
				if($order->canInvoice())//$invoice->getTotalQty()
				{
							$savedQtys = array();
							$orderItems = $order->getAllItems();
							foreach ($orderItems as $orderItem) {
								$itemId = $orderItem->getId();
								$savedQtys[$itemId] = $orderItem->getQtyToInvoice();
							}
							
							//$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice(array());
							$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
						if ($invoice->getTotalQty()) // check if return invoice obj has the few qty to invoice
						{
							$invoice->setRequestedCaptureCase('online');
							$invoice->register();
							$invoice->getOrder()->setIsInProcess(true);
							
							$isToNotifyCustomer = Mage::getStoreConfig('orderview/general/notify_customer_oninvoice');
							if($isToNotifyCustomer) $invoice->setEmailSent(true);
							if($isToNotifyCustomer) $invoice->getOrder()->setCustomerNoteNotify(true);
							
							$transactionSave = Mage::getModel('core/resource_transaction')
												->addObject($invoice)
												->addObject($invoice->getOrder());
							$transactionSave->save();                
							if($isToNotifyCustomer) $invoice->sendEmail(true, '');
							$count++;
						}
				}
            }            
        }
        return $count;        
    }	
	
	 /**
     * Initialize order model instance
     *
     * @return Mage_Sales_Model_Order || false
     */
    public function _initOrder()
    {
        $id = $this->getRequest()->getParam('order_id');

        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
	
	
	 public function editsinglehistoryAction()
	 {
	 		$field = $this->getRequest()->getParam('field');
			$orderId = $this->getRequest()->getParam('order_id');
			$historyEntityId = $this->getRequest()->getParam('item_id');
			$value = $this->getRequest()->getParam('value');
		
			$historyModel = Mage::getModel('sales/order_status_history');
			$historyModel->load($historyEntityId);
			$historyModel->setComment($value);
			$historyModel->save();
			echo $value;die;
	 }	
	 public function deleteallhistoryAction()
	 {
	 	
	 	if ($order = $this->_initOrder()) {
           try {
		   
				$response = false;
				 
				//$order = $this->_initOrder(); // it is required as it set order object while template load via ajax
	
				$field = $this->getRequest()->getParam('field');
				$orderId = $this->getRequest()->getParam('order_id');
				$historyEntityIds = $this->getRequest()->getParam('item_ids');
				$idsArr = explode(",",$historyEntityIds);
				$idsArr = array_filter($idsArr);
				if(count($idsArr) < 1)
				{
					 $response = array(
                    'error'     => true,
                    'message'   => $this->__('Please select atleast one item to delete.'),
               		 );
					$this->loadLayout('empty');
					$this->renderLayout();
				}
				else
				{
					foreach($idsArr as $historyEntityId)
					{
						$historyModel = Mage::getModel('sales/order_status_history');
						$historyModel->load($historyEntityId);
						$historyModel->delete();
						$historyModel->save();
					}
					$this->loadLayout('empty');
					$this->renderLayout();
				}
			 }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add order history.'),
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
		}	
	
	 }
	 
	 
	 public function deletesinglehistoryAction()
	 {
	 	if ($order = $this->_initOrder()) {
           try {
		   
				$response = false;
				 
				//$order = $this->_initOrder(); // it is required as it set order object while template load via ajax
	
				$field = $this->getRequest()->getParam('field');
				$orderId = $this->getRequest()->getParam('order_id');
				$historyEntityId = $this->getRequest()->getParam('item_id');
			
				$historyModel = Mage::getModel('sales/order_status_history');
				$historyModel->load($historyEntityId);
				$historyModel->delete();
				$historyModel->save();
				
				$this->loadLayout('empty');
				$this->renderLayout();
			 }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
		}	
	}
	
	public function savewithdelAction()
	{	

	 	if ($order = $this->_initOrder()) {
           try {
		   
				$response = false;
				
				$postData = $this->getRequest()->getParams();
				
			foreach($postData as $data)
			{ 
					if($data = json_decode($data, Zend_Json::TYPE_ARRAY)) {
						//echo '<pre>';print_r($data);die;
						//$dataArr[] = $data;
					 
							foreach($data['his_item_id'] as $key => $val)
							{
								if(isset($data['deleteHistory']) && count($data['deleteHistory'])>0 && in_array($val,$data['deleteHistory'])) //delete coments
								{
									$historyModel = Mage::getModel('sales/order_status_history');
									$historyModel->load($val);
									$historyModel->delete();
									$historyModel->save();

								}else{
									$historyModel = Mage::getModel('sales/order_status_history');
									$historyModel->load($val);
									$historyModel->setComment($data['comment'][$key]);
									$historyModel->save();
								}
							}
						
					}
			}	
				$this->loadLayout('empty');
				$this->renderLayout();
				 
			 }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__($e.'Cannot add order history.'),
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
		}	
	}
	
	public function addCommentAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $response = false;
                $data = $this->getRequest()->getPost('history');
                $notify = isset($data['is_customer_notified']) ? $data['is_customer_notified'] : false;
                $visible = isset($data['is_visible_on_front']) ? $data['is_visible_on_front'] : false;

                $order->addStatusHistoryComment($data['comment'], $data['status'])
						->setIsVisibleOnFront($visible)
						->setIsCustomerNotified($notify);

                $comment = trim(strip_tags($data['comment']));

                $order->save();
                $order->sendOrderUpdateEmail($notify, $comment);

                $this->loadLayout('empty');
                $this->renderLayout();
            }
            catch (Mage_Core_Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $e->getMessage(),
                );
            }
            catch (Exception $e) {
                $response = array(
                    'error'     => true,
                    'message'   => $this->__('Cannot add order history.')
                );
            }
            if (is_array($response)) {
                $response = Mage::helper('core')->jsonEncode($response);
                $this->getResponse()->setBody($response);
            }
        }
    }
	
	public function updatecustomerAction()
	{
		$isNotified = "";
		$isVisible = "";
		$id = $_POST['order_id'];
		  if ($order = $this->_initOrder()) {
			//$order = Mage::getModel('sales/order')->load($id);
			if(isset($_POST['history']['comment']) && $_POST['history']['comment'] != "")
			{
				$comment = trim(strip_tags($_POST['history']['comment']));
				$notify = isset($_POST['history']['is_customer_notified']) ? $_POST['history']['is_customer_notified'] : false;
				$visible = isset($_POST['history']['is_visible_on_front']) ? $_POST['history']['is_visible_on_front'] : false;
				
				 $order->addStatusHistoryComment($comment)
						->setIsVisibleOnFront($visible)
						->setIsCustomerNotified($notify);
				
				
				$order->save();
				if($notify)
				{
					$order->sendOrderUpdateEmail($notify, $comment);
				}
				
				echo 'Successfully updated.';die; // Please do not change this, checked for success message.
			}else{
				echo 'Customer was not successfully updated.';die;
			}
		}
	}
	
	public function bradcastMessageAction()
	{
		$totalResponse = array( 'is_success' => false,'is_error'=> false,'succ_unsucc_message' => false);
		
		$isNotified = "";
		$isVisible = "";
 		$statusArray = "";
		
		$selectedStatusArr = array_filter($_POST['status']);
		$selectedStatus = count($selectedStatusArr);	 
		if(is_array($selectedStatusArr) && $selectedStatus > 0)
		{
 			if(isset($_POST['status'][0]) && $_POST['status'][0] != "")
			{
				$statusArray = explode(",",$_POST['status'][0]);
			}
		}
		else
		{
			$statusArray = Mage::getStoreConfig('orderview/broadcast_mng/bradmsg');
			$statusArray = explode(",",$statusArray);
			$statusArray = array_filter($statusArray);
		}
 		 
		//echo count($statusArray);die;
		if(count($statusArray) <= 0  )
		{
			$totalResponse['is_success'] = false;
			$totalResponse['message'] = $this->__('Please select the order status you want to send the message.');
 		}
		else
		{
			$order_collection = Mage::getModel('sales/order')->getCollection()
								->addFieldToFilter('status', array('in' => $statusArray));
	
 
			if(count($order_collection) > 0)
			{
				foreach ($order_collection as $order)
				{
						//$order = Mage::getModel('sales/order')->load($id);
						if(isset($_POST['history']['comment']) && $_POST['history']['comment'] != "")
						{
							$comment = trim(strip_tags($_POST['history']['comment']));
							$notify = isset($_POST['history']['is_customer_notified']) ? $_POST['history']['is_customer_notified'] : false;
							$visible = isset($_POST['history']['is_visible_on_front']) ? $_POST['history']['is_visible_on_front'] : false;
							
							 $order->addStatusHistoryComment($comment)
									->setIsVisibleOnFront($visible)
									->setIsCustomerNotified($notify);
							$order->save();
							if($notify)
							{
								$order->sendOrderUpdateEmail($notify, $comment);
							}
					 }
				}
					$totalResponse['is_success'] = true;
					$totalResponse['message'] = $this->__('Your message was sent successfully.');
 			}
			else
			{
					$totalResponse['is_success'] = false;
					$totalResponse['message'] = $this->__('Could not found any order for the selected order status.');
 			}
		}
		
		$this->getResponse()->setBody(Zend_Json::encode($totalResponse));
			
	}
	
	public function saveadmincommentAction()
	{	
		$field = $this->getRequest()->getParam('field');
		$orderId = $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);		
	 
		$value = $this->getRequest()->getParam('value');
		$order->setEditComments($value);
		$order->save();
		
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		$where = $write->quoteInto('entity_id =?', $orderId);
		$vals = array();
		$vals['is_edit'] = 1;
		$vals['edit_comments'] = 1;
		//$write->update("sales_flat_order_grid", $vals ,$where);
		
		$prefix = Mage::getConfig()->getTablePrefix();
		$gridTable = $prefix.'sales_flat_order_grid';
		$condition = "update ".$gridTable." SET `edit_comments` = '".$value."' where entity_id = '".$orderId."'";
		$write->query($condition);
					
		echo $value;die;
	}
}