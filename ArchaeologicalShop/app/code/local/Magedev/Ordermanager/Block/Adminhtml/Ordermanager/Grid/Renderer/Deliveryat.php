<?php
 
class Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Deliveryat
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
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
		$deliveryAt = $row->getDeliveryAt();
		
		if($deliveryAt != "" && $deliveryAt != "1970-01-01" && $deliveryAt != "0000-00-00")
		{
			$date   = new Datetime($deliveryAt);
			return '<i>'.$date->format('M-d-Y').'</i>';
		}else{return 'N/A';}
		
		//if($deliveryAt == '0000-00-00'){return Mage::helper('sales')->__('N/A');}else{return $deliveryAt;}
    }
}
?>