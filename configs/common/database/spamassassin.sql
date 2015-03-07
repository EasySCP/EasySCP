--
-- EasySCP a Virtual Hosting Control Panel
-- Copyright (C) 2010-2015 by Easy Server Control Panel - http://www.easyscp.net
--
-- This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
-- To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
--
-- @link 		http://www.easyscp.net
-- @author 		EasySCP Team
-- --------------------------------------------------------

create database `{SPAMASSASSIN_DATABASE}` CHARACTER SET utf8 COLLATE utf8_unicode_ci;

use `{SPAMASSASSIN_DATABASE}`;

CREATE TABLE IF NOT EXISTS bayes_expire (
  id int(11) NOT NULL default '0',
  runtime int(11) NOT NULL default '0',
  KEY bayes_expire_idx1 (id)
) TYPE=InnoDB;

CREATE TABLE IF NOT EXISTS bayes_global_vars (
  variable varchar(30) NOT NULL default '',
  value varchar(200) NOT NULL default '',
  PRIMARY KEY  (variable)
) TYPE=InnoDB;

INSERT IGNORE INTO bayes_global_vars VALUES ('VERSION','3');

CREATE TABLE IF NOT EXISTS bayes_seen (
  id int(11) NOT NULL default '0',
  msgid varchar(200) binary NOT NULL default '',
  flag char(1) NOT NULL default '',
  PRIMARY KEY  (id,msgid)
) TYPE=InnoDB;

CREATE TABLE IF NOT EXISTS bayes_token (
  id int(11) NOT NULL default '0',
  token char(5) NOT NULL default '',
  spam_count int(11) NOT NULL default '0',
  ham_count int(11) NOT NULL default '0',
  atime int(11) NOT NULL default '0',
  PRIMARY KEY  (id, token),
  INDEX bayes_token_idx1 (id, atime)
) TYPE=InnoDB;

CREATE TABLE IF NOT EXISTS bayes_vars (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(200) NOT NULL default '',
  spam_count int(11) NOT NULL default '0',
  ham_count int(11) NOT NULL default '0',
  token_count int(11) NOT NULL default '0',
  last_expire int(11) NOT NULL default '0',
  last_atime_delta int(11) NOT NULL default '0',
  last_expire_reduce int(11) NOT NULL default '0',
  oldest_token_age int(11) NOT NULL default '2147483647',
  newest_token_age int(11) NOT NULL default '0',
  PRIMARY KEY  (id),
  UNIQUE bayes_vars_idx1 (username)
) TYPE=InnoDB;

CREATE TABLE IF NOT EXISTS awl (
  username varchar(100) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  ip varchar(40) NOT NULL default '',
  count int(11) NOT NULL default '0',
  totscore float NOT NULL default '0',
  lastupdate timestamp(14) NOT NULL,
  signedby varchar(255) NOT NULL default '',
  PRIMARY KEY (username,email,signedby,ip)
) TYPE=InnoDB;
