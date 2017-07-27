<?php
class Magedev_Ordermanager_Model_Observer
{
    const SALES_ORDER_GRID_NAME = 'sales_order_grid';
    
    public function addOptionsToAction($observer)
    {
         if (self::SALES_ORDER_GRID_NAME == $observer->getEvent()->getBlock()->getId()) {

            $massActionBlock = $observer->getEvent()->getBlock()->getMassactionBlock();
            if ($massActionBlock) {
			
				$isEnableOrdeMoreFields = Mage::getStoreConfig('orderview/general/enabled');
       			if($isEnableOrdeMoreFields == 1) {
		    
 				if(Mage::getStoreConfig('orderview/mass_act_mng/add_mass_invoce') == 1){
					 $massActionBlock->addItem('invoiceonly_order', array(
						 'label'=> Mage::helper('sales')->__('----Invoice Only'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/doMassInvoiceonly'),
					));
				}	
			
				if(Mage::getStoreConfig('orderview/mass_act_mng/add_mass_ship') == 1){
					$massActionBlock->addItem('ship_order', array(
						 'label'=> Mage::helper('sales')->__('----Ship Only'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/doMassShip'),
					));
				}
			
				if(Mage::getStoreConfig('orderview/mass_act_mng/add_mass_ico') == 1){
					$massActionBlock->addItem('invoice_order', array(
						 'label'=> Mage::helper('sales')->__('----Invoice>Capture(Offline)'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/doMassInvoice'),
					));
				}
				
				if(Mage::getStoreConfig('orderview/mass_act_mng/add_mass_icon') == 1){
					$massActionBlock->addItem('invoice_order_online', array(
						 'label'=> Mage::helper('sales')->__('----Invoice>Capture(Online)'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/doMassInvoiceCapture'),
					));
				}
             
			 
				 if(Mage::getStoreConfig('orderview/mass_act_mng/add_mass_icoffship') == 1){
				 
					$massActionBlock->addItem('invoice_ship_order_off', array(
						 'label'=> Mage::helper('sales')->__('----Invoice>Capture(Offline)>Ship'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/doMassInvoiceShipOff'),
					));
				}
				
				if(Mage::getStoreConfig('orderview/mass_act_mngz/add_mass_iconship') == 1){
					$massActionBlock->addItem('invoice_ship_order_online', array(
						 'label'=> Mage::helper('sales')->__('----Invoice>Capture(Online)>Ship'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/doMassInvoiceShipOnline'),
					));
				}
			
				if(Mage::getStoreConfig('orderview/mass_act_mng/add_mass_archieve') == 1){
					$massActionBlock->addItem('archive_order', array(
						 'label'=> Mage::helper('sales')->__('----Move To Archieve'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/doMassArchive'),
					));
				}
				
				if(Mage::getStoreConfig('orderview/mass_act_mng/add_mass_active') == 1){
 					$massActionBlock->addItem('restore_order', array(
						 'label'=> Mage::helper('sales')->__('----Move To Active'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/doMassActive'),
					));
				}
				
				if(Mage::getStoreConfig('orderview/delete_inv_mng/delete_inv') == 1){
					$massActionBlock->addItem('delete_inv', array(
						'label'=> Mage::helper('core')->__('----Delete Invoice'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/massDeleteInvoice'),
						 'confirm' => Mage::helper('core')->__('Are you sure to delete Invoice for the selected orders?'),
					));
				}
				
				if(Mage::getStoreConfig('orderview/delete_ship_mng/delete_ship') == 1){
					$massActionBlock->addItem('delete_ship', array(
						'label'=> Mage::helper('core')->__('----Delete Shipping'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/massDeleteShip'),
						 'confirm' => Mage::helper('core')->__('Are you sure to delete Shipping for the selected orders?'),
					));
				}
				
				if(Mage::getStoreConfig('orderview/delete_credit_mng/delete_credit') == 1){
					$massActionBlock->addItem('delete_credit', array(
						'label'=> Mage::helper('core')->__('----Delete Credit Memo'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/massDeleteCreditMemo'),
						 'confirm' => Mage::helper('core')->__('Are you sure to delete CreditMemo for the selected orders?'),
					));
				}
				
				if(Mage::getStoreConfig('orderview/delete_in_ship_cr_mng/delete_in_ship_cr') == 1){
					$massActionBlock->addItem('delete_isc', array(
						'label'=> Mage::helper('core')->__('----Delete Invoice+Ship+Credit Memo'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/massDeleteINC'),
						 'confirm' => Mage::helper('core')->__('Are you sure to delete Invoice+Ship+Credit Memo for the selected orders?'),
					));
				}
				
				if(Mage::getStoreConfig('orderview/deletemng/allow_delete') == 1){
					$massActionBlock->addItem('delete_orders', array(
						'label'=> Mage::helper('core')->__('----Delete Order'),
						 'url'  =>  Mage::getUrl('ordermanager/adminhtml_ordereditor/massDeleteOrder'),
						 'confirm' => Mage::helper('core')->__('Are you sure to delete the selected orders?'),
					));
				}
				
       		 }
	 
            }
        }
    }
	
	public function autosetArchieveOrders() // run cron
	{
		$isOrderManagerEnabled = Mage::getStoreConfig('orderview/general/enabled');
		$isEnableArchieveOrders = Mage::getStoreConfig('orderview/order_arch_mng/enable_archieve');
		if(!$isOrderManagerEnabled || !$isEnableArchieveOrders) return ;
		
		$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
		$statusArray = Mage::getStoreConfig('orderview/order_arch_mng/archive_orders_on_status');
		$statusArray = explode(",",$statusArray);
			
		$order_collection = Mage::getModel('sales/order')->getCollection()
							->addFieldToFilter('status', array('in' => $statusArray));
		
		 $prefix = Mage::getConfig()->getTablePrefix();
         foreach ($order_collection as $order) {

            $orderId = $order->getEntityId();
			$order = Mage::getModel('sales/order')->load($orderId); 
 			$order->setIsArchieved(1); 
			$order->save();
 			$vals = array();
			$vals['is_archieved'] = 1;
 		//	$where = $write->quoteInto('entity_id =?', $orderId);
		//	$write->update("sales_flat_order_grid", $vals ,$where);
			
			$gridTable = $prefix.'sales_flat_order_grid';
			$condition = "update ".$gridTable." SET `is_archieved` = 1 where entity_id = '".$orderId."'";
			$write->query($condition);
         }
 	}
}
