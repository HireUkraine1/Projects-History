<?php

class Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Comments
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
	 		$format_val = '';$options = '';
			$orderIncId = $row['increment_id'];
			$orderId = $row->getId();
			
			$commentsLimit = Mage::getStoreConfig('orderview/general/comments_limit');
			$order = Mage::getModel('sales/order')->load($orderId);
			
			$historyArr  = $order->getAllStatusHistory();
			$i = 1;$j = 0;
			foreach ($historyArr as $_historyItem) 
			{
 				if($_historyItem->getData('comment') != "")
				{
					$format_val = $_historyItem->getData('comment');
					$options[] = '<span style="float:left;font-weight:bold;">' . $i .'.' . '</span><span style="margin-left:2px;">' .$format_val .'</span>' ;
					$i++;
				}
				$j++;
				if($j > $commentsLimit){break;}
				
 			}
			if(is_array($options) && $options != "")
			{
				$format_val = implode('<br/>', $options);//echo $format_val;die;
			}
		//echo $format_val;die;
		 return $format_val;
     }
}
?>