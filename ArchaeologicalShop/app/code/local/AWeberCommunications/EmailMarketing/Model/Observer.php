<?php

/* AWeber Magento API Integration
 * (C) AWeber Communications, Inc
 *
 * EmailMarketing : Sales Order Observer
 *
 * This module is responsible for calling WASP->addSubscriber
 */


require_once(dirname(__FILE__) . "/WASP.php");


class AWeberCommunications_EmailMarketing_Model_Observer extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $config = Mage::getModel('emailmarketing/config');
        $this->WASP = new AWeberWaspModule($config);
    }

    public function processQueue() {

        $config = Mage::getModel('emailmarketing/config');
        $subscribers = $config->waspQueue->newSubscribers();

        // Handle New Subscribers
        foreach($subscribers as $subscriber) {
            $email       = $subscriber->getEmail();
            $ad_tracking = $subscriber->getAdTracking();
            $name        = $subscriber->getName();
            $list_id     = $subscriber->getListId();

            if ($config->WASP->addSubscriber($ad_tracking, $name, $email, $list_id)) {
                $subscriber->delete();
                $subscriber->save();
            } else {
                $subscriber->setStatus('retry');
                $subscriber->setResult($config->waspGet('error'));
                $subscriber->save();
            }
        }

        // Handle Retries
        $subscribers = $config->waspQueue->retrySubscribers();
        foreach($subscribers as $subscriber) {
            $email       = $subscriber->getEmail();
            $ad_tracking = $subscriber->getAdTracking();
            $name        = $subscriber->getName();
            $list_id     = $subscriber->getListId();

            if ($config->WASP->addSubscriber($ad_tracking, $name, $email, $list_id)) {
                $subscriber->delete();
                $subscriber->save();
            } else {
                $subscriber->setStatus('error');
                $subscriber->setResult($config->waspGet('error'));
                $subscriber->save();
            }
        }
        return true;
    }

    public function subscribeCustomer($observer) {

        /* translate an order into a standardized associative array */
        $order = $observer->getEvent()->getOrder();

        /* customer name, email, address, etc.. */
        $email = $order->getCustomerEmail();
        $subscriber_data = $order->getBillingAddress();
        $name = $subscriber_data->getName();

        /* products purchased */
        $purchased_products = array();
        foreach ($order->getAllItems() as $item) {
            $product_id = $item->getProductId();
            array_push($purchased_products, $product_id);
        }

        /* add subscriber via api */
        $this->WASP->purchaseNotification('magento', $name, $email, $purchased_products);

        /* return */
        return $this;
    }
}
