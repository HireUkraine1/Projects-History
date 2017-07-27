<?php

/* AWeber Magento API Integration
 * (C) AWeber Communications, Inc
 *
 * Queue Model
 *
 * This module contains the WASP compatible queue model for this api integration
 */


class AWeberCommunications_EmailMarketing_Model_Queue extends Mage_Core_Model_Abstract {

    protected function _construct() {
        $this->_init('emailmarketing/queue');
    }

    public function clear() {
        $queue =  Mage::getModel('emailmarketing/queue')->getCollection();
        foreach($queue as $row) {
            $row->delete();
            $row->save();
        }
    }

    public function add($ad_tracking, $name, $email, $list_id) {
        $queue = Mage::getModel('emailmarketing/queue');
        $queue->setAdTracking($ad_tracking);
        $queue->setName($name);
        $queue->setEmail($email);
        $queue->setListId($list_id);
        $queue->setCreatedAt(time());
        $queue->save();
        return true;
    }

    public function newSubscribers() {
        return Mage::getModel('emailmarketing/queue')->getCollection()->addFieldToFilter('status', array('eq' => 'new'));
    }

    public function retrySubscribers() {
        return Mage::getModel('emailmarketing/queue')->getCollection()->addFieldToFilter('status', array('eq' => 'retry'));
    }

    public function errorSubscribers() {
        $collection = Mage::getModel('emailmarketing/queue')->getCollection();
        $collection->addFieldToFilter('created_at', array('lt' => (time() - (10 * 60))));
        return $collection;
    }
}

