<?php
class Aheadgroups_Baseall_Block_Message_System_Config_Extensionlist extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('aheadgroups/baseall/extensionlist.phtml');
        }
        return $this;
    }

	public function render(Varien_Data_Form_Element_Abstract $element)
    {
		//return parent::render($element);
        
		$useContainerId = $element->getData('use_container_id');
		$block = $this->getLayout()->createBlock('core/template')
			   ->setTemplate('aheadgroups/baseall/extensionlist.phtml'); 
		 $extensions = $block->renderView();
		 
        return sprintf('<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5"><h4 id="%s">%s</h4></td></tr>',
            $element->getHtmlId(), $element->getHtmlId(), $extensions
        );
    }

    /**
     * Get the button and scripts contents
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $originalData = $element->getOriginalData();
        return $this->_toHtml();
    }

}
