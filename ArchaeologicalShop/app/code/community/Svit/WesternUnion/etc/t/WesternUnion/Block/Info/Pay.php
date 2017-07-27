<?php
// This block allows data along with the payment method to be presented on the admin screen and user order screen.
class Svit_WesternUnion_Block_Info_Pay extends Mage_Payment_Block_Info{

    protected $_instructions;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('svit/westernunion/info/pay.phtml');
    }

    public function getInstructions()
    {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getInfo()->getAdditionalInformation('instructions');
            if(empty($this->_instructions)) {
                $this->_instructions = $this->getMethod()->getInstructions();
            }
        }
        return $this->_instructions;
    }
   
   
   
   
   
    protected function _prepareSpecificInformation($transport = null)
       {
           if (null !== $this->_paymentSpecificInformation) {
               return $this->_paymentSpecificInformation;
           }
           $info = $this->getInfo();
           $transport = new Varien_Object();
           /*
           $transport->addData(array(
               Mage::helper('payment')->__('Check No#') => $info->getCheckNo(),
               Mage::helper('payment')->__('Check Date') => $info->getCheckDate()
           ));*/
		   
		   $transport = parent::_prepareSpecificInformation($transport);
           return $transport;
       }
}