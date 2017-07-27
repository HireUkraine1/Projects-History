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

$installer = $this;
$installer->startSetup();
$installer->run("

  DROP TABLE IF EXISTS {$this->getTable('faq/faq')};
  CREATE TABLE IF NOT EXISTS {$this->getTable('faq/faq')} (
    `question_id` int(11) unsigned NOT NULL auto_increment,
    `question` varchar(255) NOT NULL,
    `answer` TEXT NOT NULL,
    `display_order` int(11) NOT NULL,
    `status` SMALLINT(5) NOT NULL DEFAULT 1,
    `created_time` datetime NULL,
    `updated_time` datetime NULL,
    PRIMARY KEY (`question_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  DROP TABLE IF EXISTS {$this->getTable('faq/faq_store')};
  CREATE TABLE IF NOT EXISTS {$this->getTable('faq/faq_store')} (
    `question_id` int(11) unsigned NOT NULL,
    `store_id` smallint(5) unsigned NOT NULL,
    PRIMARY KEY (`question_id`,`store_id`),
    CONSTRAINT `STORE_FAQ` FOREIGN KEY (`question_id`) REFERENCES `{$this->getTable('faq/faq')}` (`question_id`) ON UPDATE CASCADE ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  DROP TABLE IF EXISTS {$this->getTable('faq/category')};
  CREATE TABLE IF NOT EXISTS {$this->getTable('faq/category')} (
    `category_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `category_name` VARCHAR(255) NOT NULL,
    `category_description` TEXT NOT NULL,
    `display_order` int(11) NOT NULL,
    `status` SMALLINT(5) NOT NULL DEFAULT 1,
    `created_time` DATETIME NOT NULL,
    `updated_time` DATETIME NOT NULL,
    PRIMARY KEY (`category_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  DROP TABLE IF EXISTS {$this->getTable('faq/category_store')};
  CREATE TABLE IF NOT EXISTS {$this->getTable('faq/category_store')} (
    `category_id` INT(11) UNSIGNED NOT NULL,
    `store_id` SMALLINT(5) UNSIGNED NOT NULL,
    PRIMARY KEY (`category_id`,`store_id`),
    CONSTRAINT `STORE_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES `{$this->getTable('faq/category')}` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  DROP TABLE IF EXISTS {$this->getTable('faq/category_item')};
  CREATE TABLE IF NOT EXISTS {$this->getTable('faq/category_item')} (
    `category_id` INT(11) UNSIGNED NOT NULL,
    `question_id` INT(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`category_id`,`question_id`),
    CONSTRAINT `CATEGORY_ITEM` FOREIGN KEY (`category_id`) REFERENCES `{$this->getTable('faq/category')}` (`category_id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `CATEGORY_ITEM_ITEM` FOREIGN KEY (`question_id`) REFERENCES `{$this->getTable('faq/faq')}` (`question_id`) ON UPDATE CASCADE ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();