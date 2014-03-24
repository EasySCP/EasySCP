--
-- EasySCP a Virtual Hosting Control Panel
-- Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
--
-- This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
-- To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
--
-- @link 		http://www.easyscp.net
-- @author 		EasySCP Team
-- --------------------------------------------------------

--
-- Datenbank: `powerdns`
--
CREATE DATABASE IF NOT EXISTS `powerdns`
  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
use `powerdns`;
-- --------------------------------------------------------

--
-- Table structure for table `domains`
--
CREATE TABLE IF NOT EXISTS `domains` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `easyscp_domain_id` INT DEFAULT NULL,
  `easyscp_domain_alias_id` INT DEFAULT NULL,
  `name` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `master` VARCHAR(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_check` INT DEFAULT NULL,
  `type` VARCHAR(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notified_serial` INT DEFAULT NULL,
  `account` VARCHAR(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_index` (`name`)
);
-- --------------------------------------------------------

--
-- Table structure for table `records`
--
CREATE TABLE IF NOT EXISTS `records` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `domain_id` INT DEFAULT NULL,
  `name` VARCHAR(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` VARCHAR(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` VARCHAR(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ttl` INT DEFAULT NULL,
  `prio` INT DEFAULT NULL,
  `change_date` INT DEFAULT NULL,
  `protected` TINYINT(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `DomainRecords` (`domain_id`,`name`,`type`,`content`),
  KEY `rec_name_index` (`name`),
  KEY `nametype_index` (`name`, `type`),
  KEY `domain_id` (`domain_id`)
);
-- --------------------------------------------------------

--
-- Table structure for table `supermasters`
--
CREATE TABLE IF NOT EXISTS `supermasters` (
  `ip` VARCHAR(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nameserver` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `account` VARCHAR(40) COLLATE utf8_unicode_ci DEFAULT NULL
);
-- --------------------------------------------------------