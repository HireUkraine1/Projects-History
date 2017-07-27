<?php

class Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Invoiced
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
		return $this->_getValue($row);
    }
	
	 public function _getValue(Varien_Object $row)
     {
	 		$format_val = '';
			$orderIncId = $row['increment_id'];
			$orderId = $row->getId();
			
 			$order = Mage::getModel('sales/order')->load($orderId);
			$invoiced = $order->getTotalInvoiced();
			if(isset($invoiced) && $invoiced != "" )
			{
				return true;
			}else{ return false;}
			
      }
}
?>