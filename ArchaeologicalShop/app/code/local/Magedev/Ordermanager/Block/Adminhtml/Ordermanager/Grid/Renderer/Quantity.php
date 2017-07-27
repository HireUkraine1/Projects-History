<?php
 
class Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Quantity extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
	public function detail()
	{
		$layout = Mage::getSingleton('core/layout');
	 
		  
		$update = $layout->getUpdate();
		$update->load('order_items');
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();

		$result = array( 'outputHtml' => $output, 'otherVar' => 'foo', );
		$this->getResponse()->setBody(Zend_Json::encode($result));        
	}
	
	/**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
 		$order_id = $row['increment_id'];$orderQty = "";
		
 		$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		
		$items = $order->getAllVisibleItems();
 		foreach ($items as $itemId => $item)
		{
 		 	
			$itemStatus = $item->getStatus();
			$itemStatusId = $item->getStatusId();
						
 		   	if($itemStatusId == 9)$orderQty .= $itemStatus. "=>" .number_format($item['qty_invoiced'],0).'<br/>';
			if($itemStatusId == 1)$orderQty .= $itemStatus. "=>" .number_format($item['qty_ordered'],0).'<br/>';
			if($itemStatusId == 3)$orderQty .= $itemStatus. "=>" .number_format($item['qty_backordered'],0).'<br/>';
			if($itemStatusId == 2)$orderQty .= $itemStatus. "=>" .number_format($item['qty_shipped'],0).'<br/>';
			if($itemStatusId == 5)$orderQty .= $itemStatus. "=>" .number_format($item['qty_canceled'],0).'<br/>';
			if($itemStatusId == 8)$orderQty .= $itemStatus. "=>" .number_format($item['qty_refunded'],0).'<br/>';

		}  
		return $orderQty;
		die;		
	 
    }
}
?>