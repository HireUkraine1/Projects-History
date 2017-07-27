<?php
 /* Install Script
  * (C) AWeber Communications, Inc
  *
  * Refer to this tutorial for future use.
  * http://www.magentocommerce.com/knowledge-base/entry/magento-for-dev-part-6-magento-setup-resources/
  */

$installer = $this;
$installer->startSetup();
$installer->run("

    CREATE TABLE `{$installer->getTable('emailmarketing/config')}` (
       `id` int(11) NOT NULL auto_increment,
       `name` char(200) NOT NULL UNIQUE,
       `value` text,
       PRIMARY KEY (`id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
