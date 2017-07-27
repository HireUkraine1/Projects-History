<?php
class Magedev_Ordermanager_Model_System_Config_Source_Orders_Columnmanager
{

  public function toOptionArray()
  {
	  	$selectedColumns = Mage::getStoreConfig('orderview/general/order_grid_col');
		$selectedColArr = explode(",",$selectedColumns);
		$columnsContainer = $this->collectOptions();
	 	
	 	//echo '<pre>';print_r($selectedColArr);die;
	
	/*It is required to set the sort saved values in system config grid*/
        $options = array();
        foreach ($selectedColArr as $selCol)
        {
            if(isset($columnsContainer[$selCol]))
            {
                $options[] = array( 'value' => $selCol, 'label' => $columnsContainer[$selCol] );
                unset($columnsContainer[$selCol]);
            }
        }

        foreach ($columnsContainer as $code => $label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }

        return $options;
		
  }
    public function collectOptions1() {
	
	/*return array( array('value' => 1, 'label'=>Mage::helper('newmodule')->('Yes')), array('value' => 0, 'label'=>Mage::helper('newmodule')->('No')), );*/

return array(array('value' => 'real_order_id','label'=>Mage::helper('ordermanager')->__('Order #')),
				array('value' => 'store_id','label'=>Mage::helper('ordermanager')->__('Purchased From (Store)')),
				array('value' => 'created_at','label'=>Mage::helper('ordermanager')->__('Purchased On')),
				array('value' => 'product_detail','label'=>Mage::helper('ordermanager')->__('Product Name(s)')),
				array('value' => 'product_options','label'=>Mage::helper('ordermanager')->__('Product Option(s)')),
				array('value' => 'product_sku','label'=>Mage::helper('ordermanager')->__('Product Sku(s)')),
				array('value' => 'qty','label'=>Mage::helper('ordermanager')->__('Product Quantity')),
				array('value' => 'weight','label'=>Mage::helper('ordermanager')->__('Product Weight')),

				array('value' => 'payment_method','label'=>Mage::helper('ordermanager')->__('Payment Method')),
				array('value' => 'shipping_method','label'=>Mage::helper('ordermanager')->__('Shipping Method')),
				array('value' => 'customer_email','label'=>Mage::helper('ordermanager')->__('Customer Email')),
				array('value' => 'customer_group','label'=>Mage::helper('ordermanager')->__('Customer Group')),
				array('value' => 'coupon_code','label'=>Mage::helper('ordermanager')->__('Coupon Code')),

								
				array('value' => 'billing_name','label'=>Mage::helper('ordermanager')->__('Bill to Name')),
				array('value' => 'billing_company','label'=>Mage::helper('ordermanager')->__('Bill to Company')),
				array('value' => 'billing_street','label'=>Mage::helper('ordermanager')->__('Bill to Street')),
				array('value' => 'billing_postcode','label'=>Mage::helper('ordermanager')->__('Bill to Postcode')),
				array('value' => 'billing_state','label'=>Mage::helper('ordermanager')->__('Bill to State')),
				array('value' => 'billing_country','label'=>Mage::helper('ordermanager')->__('Bill to Country')),
				
				array('value' => 'shipping_name','label'=>Mage::helper('ordermanager')->__('Ship to Name')),
				array('value' => 'shipping_company','label'=>Mage::helper('ordermanager')->__('Ship to Company')),
				array('value' => 'shipping_street','label'=>Mage::helper('ordermanager')->__('Ship to Street')),
				array('value' => 'shipping_postcode','label'=>Mage::helper('ordermanager')->__('Ship to Postcode')),
				array('value' => 'shipping_state','label'=>Mage::helper('ordermanager')->__('Ship to State')),
				array('value' => 'shipping_country','label'=>Mage::helper('ordermanager')->__('Ship to Country')),
				
				array('value' => 'base_tax_amount','label'=>Mage::helper('ordermanager')->__('Tax Amount(Base)')),
				array('value' => 'tax_amount','label'=>Mage::helper('ordermanager')->__('Tax Amount(Purchased)')),
				
				array('value' => 'base_discount_amount','label'=>Mage::helper('ordermanager')->__('Discount (Base)')),
				array('value' => 'discount_amount','label'=>Mage::helper('ordermanager')->__('Discount (Purchased)')),
				
				array('value' => 'base_total_refunded','label'=>Mage::helper('ordermanager')->__('Refunded (Base)')),
				array('value' => 'total_refunded','label'=>Mage::helper('ordermanager')->__('Refunded (Purchased)')),
												
				array('value' => 'base_grand_total','label'=>Mage::helper('ordermanager')->__('G.T. (Base)')),
				array('value' => 'grand_total','label'=>Mage::helper('ordermanager')->__('G.T. (Purchased)')),
				array('value' => 'status','label'=>Mage::helper('ordermanager')->__('Status')),
				array('value' => 'delivery_date','label'=>Mage::helper('ordermanager')->__('Delivery At')),
				array('value' => 'tracking_number','label'=>Mage::helper('ordermanager')->__('Tracking Number')),
				array('value' => 'order_type','label'=>Mage::helper('ordermanager')->__('Order Type')),
 				//array('value' => 'order_invoiced','label'=>Mage::helper('ordermanager')->__('Invoiced')),
				//array('value' => 'order_refunded','label'=>Mage::helper('ordermanager')->__('Refunded')),
				
				array('value' => 'is_edited','label'=>Mage::helper('ordermanager')->__('Is Edited')),
				array('value' => 'comments','label'=>Mage::helper('ordermanager')->__('History Comment(s)')),
				array('value' => 'edit_reason','label'=>Mage::helper('ordermanager')->__('Edit Reason(Order Comment)')),
				array('value' => 'action','label'=>Mage::helper('ordermanager')->__('Action'))
);
   
    }
	 public function collectOptions() {
	
	/*return array( array('value' => 1, 'label'=>Mage::helper('newmodule')->('Yes')), array('value' => 0, 'label'=>Mage::helper('newmodule')->('No')), );*/

return array( 'real_order_id'    =>Mage::helper('ordermanager')->__('Order #'),
				 'store_id'    =>Mage::helper('ordermanager')->__('Purchased From (Store)'),
				 'created_at'    =>Mage::helper('ordermanager')->__('Purchased On'),
				 'product_detail'    =>Mage::helper('ordermanager')->__('Product Name(s)'),
				 'product_options'    =>Mage::helper('ordermanager')->__('Product Option(s)'),
				 'product_sku'    =>Mage::helper('ordermanager')->__('Product Sku(s)'),
				 'qty'    =>Mage::helper('ordermanager')->__('Product Quantity'),
				 'weight'    =>Mage::helper('ordermanager')->__('Product Weight'),

				 'payment_method'    =>Mage::helper('ordermanager')->__('Payment Method'),
				 'shipping_method'    =>Mage::helper('ordermanager')->__('Shipping Method'),
				 'customer_email'    =>Mage::helper('ordermanager')->__('Customer Email'),
				 'customer_group'    =>Mage::helper('ordermanager')->__('Customer Group'),
				 'coupon_code'    =>Mage::helper('ordermanager')->__('Coupon Code'),

								
				 'billing_name'    =>Mage::helper('ordermanager')->__('Bill to Name'),
				 'billing_company'    =>Mage::helper('ordermanager')->__('Bill to Company'),
				 'billing_street'    =>Mage::helper('ordermanager')->__('Bill to Street'),
				 'billing_postcode'    =>Mage::helper('ordermanager')->__('Bill to Postcode'),
				 'billing_state'    =>Mage::helper('ordermanager')->__('Bill to State'),
				 'billing_country'    =>Mage::helper('ordermanager')->__('Bill to Country'),
				
				 'shipping_name'    =>Mage::helper('ordermanager')->__('Ship to Name'),
				 'shipping_company'    =>Mage::helper('ordermanager')->__('Ship to Company'),
				 'shipping_street'    =>Mage::helper('ordermanager')->__('Ship to Street'),
				 'shipping_postcode'    =>Mage::helper('ordermanager')->__('Ship to Postcode'),
				 'shipping_state'    =>Mage::helper('ordermanager')->__('Ship to State'),
				 'shipping_country'    =>Mage::helper('ordermanager')->__('Ship to Country'),
				
				 'base_tax_amount'    =>Mage::helper('ordermanager')->__('Tax Amount(Base)'),
				 'tax_amount'    =>Mage::helper('ordermanager')->__('Tax Amount(Purchased)'),
				
				 'base_discount_amount'    =>Mage::helper('ordermanager')->__('Discount (Base)'),
				 'discount_amount'    =>Mage::helper('ordermanager')->__('Discount (Purchased)'),
				
				 'base_total_refunded'    =>Mage::helper('ordermanager')->__('Refunded (Base)'),
				 'total_refunded'    =>Mage::helper('ordermanager')->__('Refunded (Purchased)'),
												
				 'base_grand_total'    =>Mage::helper('ordermanager')->__('G.T. (Base)'),
				 'grand_total'    =>Mage::helper('ordermanager')->__('G.T. (Purchased)'),
				 'status'    =>Mage::helper('ordermanager')->__('Status'),
				 'delivery_date'    =>Mage::helper('ordermanager')->__('Delivery At'),
				 'tracking_number'    =>Mage::helper('ordermanager')->__('Tracking Number'),
				 'order_type'    =>Mage::helper('ordermanager')->__('Order Type'),
 				// 'order_invoiced'    =>Mage::helper('ordermanager')->__('Invoiced'),
				// 'order_refunded'    =>Mage::helper('ordermanager')->__('Refunded'),
				
				 'is_edited'    =>Mage::helper('ordermanager')->__('Is Edited'),
				 'comments'    =>Mage::helper('ordermanager')->__('History Comment(s)'),
				 'edit_reason'    =>Mage::helper('ordermanager')->__('Edit Reason(Order Comment)'),
				 'action'    =>Mage::helper('ordermanager')->__('Action')
);
   
    }
}