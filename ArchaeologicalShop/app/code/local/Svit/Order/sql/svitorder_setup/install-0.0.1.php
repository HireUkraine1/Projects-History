<?php
$installer = $this;
$installer->startSetup();
$installer->run(
"
    CREATE TABLE IF NOT EXISTS `continents` (
`code` CHAR(2) NOT NULL COMMENT 'Continent code',
  `name` VARCHAR(255),
  PRIMARY KEY (`code`)
) ENGINE=InnoDB;



CREATE TABLE IF NOT EXISTS `countries` (
`code` CHAR(2) NOT NULL COMMENT 'Two-letter country code (ISO 3166-1 alpha-2)',
  `name` VARCHAR(255) NOT NULL COMMENT 'English country name',
  `full_name` VARCHAR(255) NOT NULL COMMENT 'Full English country name',
  `iso3` CHAR(3) NOT NULL COMMENT 'Three-letter country code (ISO 3166-1 alpha-3)',
  `number` SMALLINT(3) ZEROFILL NOT NULL COMMENT 'Three-digit country number (ISO 3166-1 numeric)',
  `continent_code` CHAR(2) NOT NULL,
  PRIMARY KEY (`code`),
  KEY `continent_code` (`continent_code`),
  CONSTRAINT `fk_countries_continents` FOREIGN KEY (`continent_code`) REFERENCES `continents` (`code`)
) ENGINE=InnoDB;
"
);
$installer->endSetup();