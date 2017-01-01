--
-- EasySCP a Virtual Hosting Control Panel
-- Copyright (C) 2010-2017 by Easy Server Control Panel - http://www.easyscp.net
--
-- This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
-- To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
--
-- @link 		http://www.easyscp.net
-- @author 		EasySCP Team
-- --------------------------------------------------------

--
-- Datenbank: `mail`
--
CREATE DATABASE IF NOT EXISTS `mail`
  DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
use `mail`;
-- --------------------------------------------------------

--
-- Table structure for table `domains`
--
CREATE TABLE IF NOT EXISTS `domains` (
  `domain` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`domain`)
);
-- --------------------------------------------------------

--
-- Table structure for table `forwardings`
--
CREATE TABLE IF NOT EXISTS `forwardings` (
  `source` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `destination` TEXT COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`source`)
);
-- --------------------------------------------------------

--
-- Table structure for table `transport`
--
CREATE TABLE IF NOT EXISTS `transport` (
  `domain` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `transport` VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  UNIQUE KEY `domain` (`domain`)
);
-- --------------------------------------------------------

--
-- Table structure for Tabelle `users`
--
CREATE TABLE IF NOT EXISTS `users` (
  `email` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`email`)
);
-- --------------------------------------------------------