<?php
 /* Upgrade Script
  * (C) AWeber Communications, Inc
  *
  * Refer to this tutorial for future use.
  * http://www.magentocommerce.com/knowledge-base/entry/magento-for-dev-part-6-magento-setup-resources/
  */

$installer = $this;
$installer->startSetup();
$installer->run("

    CREATE TABLE `{$installer->getTable('emailmarketing/queue')}` (
       `id` INT(11) NOT NULL AUTO_INCREMENT,
       `name` TEXT,
       `email` TEXT NOT NULL,
       `ad_tracking` TEXT,
       `list_id` INT(11),
       `status` ENUM('new', 'retry', 'error') NOT NULL DEFAULT 'new',
       `created_at` INT(11) NOT NULL DEFAULT 0,
       `result` TEXT NOT NULL DEFAULT '',
       PRIMARY KEY (`id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
