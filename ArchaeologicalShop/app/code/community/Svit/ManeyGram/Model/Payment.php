<?php
class Svit_ManeyGram_Model_Payment extends Mage_Payment_Model_Method_Abstract{
	// Code to match up with the groups node in default.xml
	protected $_code = "svit_mg";
	// This is the block that's displayed on the checkout
	protected $_formBlockType = 'svit_maneygram/form_pay';
	// This is the block that's used to add information to the payment info in the admin and previous
	// order screens
	protected $_infoBlockType = 'svit_maneygram/info_pay';


	// Use this to set whether the payment method should be available in only certain circumstances
	// This should only allow our payment method for over two items.
	public function isAvailable($quote = null){
		if(!$quote){
			return false;
		}
		
		if($quote->getAllVisibleItems() <= 2){
			return false;
		}
		
		return true;
	}

	public function getInstructions()
	{
		return trim($this->getConfigData('instructions'));
	}
	   
}