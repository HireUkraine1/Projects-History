<?php
 
class Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Customertarget
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
		//echo '<pre>';print_r($row);die;
		$order_id = $row['increment_id'];$result = "";
		
		$orderId = $row->getId();
		
		//$order = Mage::getModel('sales/order')->load($order_id);echo '<pre>';print_r($order);die;
		$order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
		
		$items = $order->getAllVisibleItems();
		//echo '<pre>';print_r($items);die;
		
		$viewImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/btn_show-hide_icon.gif';
		$customerViewImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/fam_user.gif';
		$customerSpeak = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/speak.gif';
		$adminNoteforOrder= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/fam_monitor.gif';
		
		$closeImage = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'adminhtml/default/default/images/rule_component_remove.gif';
		
		$itemcount=count($items);
		$name=array();
		$unitPrice=array();
		$sku=array();
		$ids=array();
		$qty=array();
		 
	$itemOnGrid = Mage::getStoreConfig('orderview/general/hide_product_view');
		
		if($itemOnGrid == 0)
		{
		
			//$result .='<td style="font-weight:bold;">Image</td> <td style="font-weight:bold;">Qty</td>
			//	   <td style="font-weight:bold;">Product</td><td style="font-weight:bold;">Status</td> <td style="font-weight:bold;">Price</td></tr>';
		//	$result .='<td style="font-weight:bold;">Image</td>  <td style="font-weight:bold;">Product</td> </tr>';
		}
		
		$i = 0 ;
		$_coreHelper = Mage::helper('core');
		
		foreach ($items as $itemId => $item)
		{
			//echo '<pre>';print_r($item);die;
		 	$imageSize = Mage::getStoreConfig('orderview/general/product_thumb_size');
		 
		   $productName = $item->getName();
		   $_productId = $item->getProductId();
		   $_product = Mage::getModel('catalog/product')->load($_productId);
		   $productImage = Mage::helper('catalog/image')->init($_product, 'image')->resize($imageSize);
 
		    
					
					
		   if($itemOnGrid == 0)
		   {
				$result .= '<div style="clear:both;"><div style="float:left;width:29%"><img src="'.$productImage.'" /></div><div style="margin:3px;">'.$productName.'</div></div>';
 		  }
		  
			$i++;
		} 
 		return $result;
		die;		
	 
    }
}
?>