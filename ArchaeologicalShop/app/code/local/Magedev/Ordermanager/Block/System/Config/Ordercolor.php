<?php
class Magedev_Ordermanager_Block_System_Config_Ordercolor extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('ordermanager/order_status_color.phtml');
        }
        return $this;
    }

    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {         $originalData = $element->getOriginalData();
        return $this->_toHtml();
    } 

}
