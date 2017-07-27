<?php

/* AWeber Magento API Integration
 * (C) AWeber Communications, Inc
 *
 * Queue : Mysql Model
 *
 */

class AWeberCommunications_EmailMarketing_Model_Mysql4_Queue extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct() {
        $this->_init('emailmarketing/queue', 'id');
    }
}

