<?php
class Magedev_Ordermanager_Model_System_Config_Source_Orders_Status
{
    public function toOptionArray($multiselect) {
       /* Default config does not allow to load all status*/
	   	$statusArr = Mage::getSingleton('sales/order_config')->getStatuses();
        $options = array();
		$options[] = array('value'=>'', 'label'=>'--Please Select--');
        foreach ($statusArr as $code=>$label) 
		{
            $options[] = array('value'=>$code, 'label'=>$label);
        }
        return $options;        
    }
}