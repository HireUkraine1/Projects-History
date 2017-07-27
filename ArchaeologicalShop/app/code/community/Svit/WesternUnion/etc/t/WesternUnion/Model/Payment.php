<?php
class Svit_WesternUnion_Model_Payment extends Mage_Payment_Model_Method_Abstract{
	// Code to match up with the groups node in default.xml
	protected $_code = "svit_wu";
	// This is the block that's displayed on the checkout
	protected $_formBlockType = 'svit_westernunion/form_pay';
	// This is the block that's used to add information to the payment info in the admin and previous
	// order screens
	protected $_infoBlockType = 'svit_westernunion/info_pay';


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
		$resource = Mage::getSingleton('core/resource');
		$countryCodeCustomer = Mage::getSingleton('checkout/type_onepage')->getQuote()->getShippingAddress()->getCountryId();
		$readConnection = $resource->getConnection('core_read');
		$query = "SELECT `continents`.`name`  FROM `continents`
                  WHERE `continents`.`code`= (
                  SELECT `countries`.`continent_code`
                  FROM `countries`
                  WHERE  `countries`.`code` = '$countryCodeCustomer' )";

		$continent = $readConnection->fetchOne($query);

		if($continent == 'Africa'){
			return trim($this->getConfigData('instructions_africa'));
		}else{
			return trim($this->getConfigData('instructions'));
		}

	}
	   
}