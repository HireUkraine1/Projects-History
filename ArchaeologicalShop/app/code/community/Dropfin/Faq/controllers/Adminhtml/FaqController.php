<?php
/**
 * Dropfin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade 
 * this extension to newer versions in the future. 
 *
 * @category    Dropfin
 * @package     FAQ
 * @copyright   Copyright (c) Dropfin (http://www.dropfin.com)
 */

class Dropfin_Faq_Adminhtml_FaqController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('dropfin/faq/items')
			->_addBreadcrumb(Mage::helper('faq')->__('Faq Items'), Mage::helper('faq')->__('Faq Items'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('faq/faq')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('faq_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('dropfin/faq/items');

			$this->_addBreadcrumb(Mage::helper('faq')->__('Faq Item Manager'), Mage::helper('faq')->__('Faq Item Manager'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('faq/adminhtml_faq_edit'))
				->_addLeft($this->getLayout()->createBlock('faq/adminhtml_faq_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Faq Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('faq/faq');
			$model->setData($data)->setId($this->getRequest()->getParam('id'));
			try {
                if (!$this->getRequest()->getParam('id')) {
                    $model->setCreatedTime(Mage::getSingleton('core/date')->gmtDate());
                }
                $model->setUpdatedTime(Mage::getSingleton('core/date')->gmtDate());
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('faq')->__('Faq item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Unable to find faq item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('faq/faq');
                $model->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('faq')->__('Faq item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $faqIds = $this->getRequest()->getParam('faq');
        if(!is_array($faqIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Please select item(s)'));
        } else {
            try {
                foreach ($faqIds as $faqId) {
                    $faq = Mage::getModel('faq/faq')->load($faqId);
                    $faq->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('faq')->__(
                        'Total of %d record(s) were successfully deleted', count($faqIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $faqIds = $this->getRequest()->getParam('faq');
        if(!is_array($faqIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('faq')->__('Please select item(s)'));
        } else {
            try {
                foreach ($faqIds as $faqId) {
                    $faq = Mage::getSingleton('faq/faq')
                        ->load($faqId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('faq')->__('Total of %d record(s) were successfully updated', count($faqIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'faq_items.csv';
        $content    = $this->getLayout()->createBlock('faq/adminhtml_faq_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'faq_items.xml';
        $content    = $this->getLayout()->createBlock('faq/adminhtml_faq_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}