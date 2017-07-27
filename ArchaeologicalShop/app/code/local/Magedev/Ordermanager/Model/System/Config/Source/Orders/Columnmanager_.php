<?php
class Magedev_Ordermanager_Model_System_Config_Source_Orders_Columnmanager
{

    public function toOptionArray() {
	
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
}