<?php

/* AWeber Magento API Integration
 * (C) AWeber Communications, Inc
 *
 * Queue : Mysql Collection Model
 *
 */

class AWeberCommunications_EmailMarketing_Model_Mysql4_Queue_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {

    protected function _construct() {
        $this->_init('emailmarketing/queue');
    }
}

