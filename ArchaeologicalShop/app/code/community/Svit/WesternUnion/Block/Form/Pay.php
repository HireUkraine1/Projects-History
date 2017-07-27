<?php 
class Svit_WesternUnion_Block_Form_Pay extends Mage_Core_Block_Template{
	

	protected $_instructions;

	protected function _construct()
	{
		parent::_construct();
		$this->setTemplate('svit/westernunion/form/pay.phtml');
	}

	public function getInstructions()
	{
		if (is_null($this->_instructions)) {
			$this->_instructions = $this->getMethod()->getInstructions();
		}
		return $this->_instructions;
	}
	public function getMethodCode()
	{
		return $this->getMethod()->getCode();
	}

}