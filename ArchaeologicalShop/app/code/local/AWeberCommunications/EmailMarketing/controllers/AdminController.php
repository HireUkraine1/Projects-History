<?php

/* AWeber Magento API Integration
 * (C) AWeber Communications, Inc
 *
 * AdminController.php
 *
 * This module implements the admin controller in Magento
 */

class AWeberCommunications_EmailMarketing_AdminController extends Mage_Adminhtml_Controller_Action {

     /* Index Action:  Renders the connected or not connected UI */
     public function indexAction() {

         $helper = Mage::helper('emailmarketing');
         $config = Mage::getModel('emailmarketing/config');
         $this->loadLayout();

         if (!$config->waspGet('is_connected')) {
             $html = $helper->not_connected($this, $config);
             $block = $this->getLayout()->createBlock('core/text', 'aweber-block')->setText($html);
         } else {
             $html = $helper->is_connected($this, $config);
             $block = $this->getLayout()->createBlock('core/text', 'aweber-block')->setText($html);
         }

         $this->_addContent($block);
         $this->_setActiveMenu('aweber_menu')->renderLayout();
     }

     /* ErrorReport Action: Renders the error report for troubleshooting */
     public function errorReportAction() {
         $helper = Mage::helper('emailmarketing');
         $config = Mage::getModel('emailmarketing/config');
         $this->loadLayout();

         if (!$config->waspGet('is_connected')) {
             $html = $helper->not_connected($this, $config);
             $block = $this->getLayout()->createBlock('core/text', 'aweber-block')->setText($html);
         } else {
             $html = $helper->error_report($this, $config);
             $block = $this->getLayout()->createBlock('core/text', 'aweber-block')->setText($html);
         }

         $this->_addContent($block);
         $this->_setActiveMenu('aweber_menu')->renderLayout();
     }

     /* Update Action: Updates the EmailMarketing settings */
     protected function errorUpdateAction() {
         $config         = Mage::getModel('emailmarketing/config');
         $url            = $this->getURL('emailmarketing/admin/errorReport');
         $action         = array_key_exists('action'      , $_POST) ? $_POST['action']       : null;

         if ((strpos($action, 'queue_remove_') !== false) && ($value = 'delete')) {
             $id          = substr($action, 13, strlen($action) - 13);
             $subscriber  = $config->waspQueue->load($id);
             $email       = $subscriber->getEmail();

             $subscriber->delete();
             $subscriber->save();
             $this->_redirectUrl($url . "?function=removed&email=" . rawurlencode($email));
             return;
         }

         if ((strpos($action, 'queue_retry_') !== false) && ($value = 'retry')) {
             $id          = substr($action, 12, strlen($action) - 12);
             $subscriber  = $config->waspQueue->load($id);
             $email       = $subscriber->getEmail();
             $ad_tracking = $subscriber->getAdTracking();
             $name        = $subscriber->getName();
             $list_id     = $subscriber->getListId();

             if ($config->WASP->addSubscriber($ad_tracking, $name, $email, $list_id)) {
                 $subscriber->delete();
                 $subscriber->save();
                 $this->_redirectUrl($url . "?function=subscribed&email=" . rawurlencode($email));
                 return;

             } else {
                 $subscriber->setStatus('error');
                 $subscriber->setResult($config->waspGet('error'));
                 $subscriber->save();
                 $this->_redirectUrl($url . "?function=failed&email=" . rawurlencode($email));
                 return;
             }
         }
         $this->_redirectUrl($url);
     }

     /* Connect Action: Accepts the Authorization Code from AWeber */
     protected function connectAction() {
         $config = Mage::getModel('emailmarketing/config');

         if ($config->waspGet('is_connected')) {
             /* do nothing if we're already connected */

         } else if (isset($_GET) and array_key_exists('authorization_code', $_GET)) {
             /* attempt to connect */
             $authorization_code = $_GET['authorization_code'];
             $config->WASP->connect($authorization_code);

         } else {
             /* no auth code returned */
             $config->waspSet('error', 'Authorization code not returned!');
         }

         /* redirect back to index action */
         $url =  $this->getURL('emailmarketing/admin/index');
         $this->_redirectUrl($url);
         return;
     }

     /* Disconnect Action: Removes Credentials from our Database */
     protected function disconnectAction() {
         $config = Mage::getModel('emailmarketing/config');
         $config->WASP->disconnect();
         $url =  $this->getURL('emailmarketing/admin/index');
         $this->_redirectUrl($url);
     }

     /* Update Action: Updates the EmailMarketing settings */
     protected function updateAction() {
         $config         = Mage::getModel('emailmarketing/config');
         $url            = $this->getURL('emailmarketing/admin/index');
         $products       = $config->WASP->products();
         $action         = array_key_exists('action'      , $_POST) ? $_POST['action']       : null;
         $default_list   = array_key_exists('default_list', $_POST) ? $_POST['default_list'] : null;
         $new_product_id = array_key_exists('new_product' , $_POST) ? $_POST['new_product']  : null;
         $new_list_id    = array_key_exists('new_list'    , $_POST) ? $_POST['new_list']     : null;
         $uses_rules     = array_key_exists('use_rules'   , $_POST) ? true                   : false;

         foreach($products as $product_id => $product_name) {
             if (($action == "rule_$product_id") or (!$uses_rules)) {
                 $config->WASP->deleteRule($product_id);
             }
         }

         if (!empty($new_product_id) and !empty($new_list_id)) {
             $config->WASP->addRule($new_product_id, $new_list_id);
         }

         if (is_numeric($default_list)) {
             $config->WASP->setDefaultList($default_list);
         }

         $this->_redirectUrl($url);
     }
}
