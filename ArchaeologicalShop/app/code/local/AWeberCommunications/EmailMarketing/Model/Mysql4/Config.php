<?php

/* AWeber Magento API Integration
 * (C) AWeber Communications, Inc
 *
 * Config : Mysql Model
 *
 */

class AWeberCommunications_EmailMarketing_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct() {
        $this->_init('emailmarketing/config', 'id');
    }
}

