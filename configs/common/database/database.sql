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
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `admin_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_pass` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `admin_type` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain_created` int(10) unsigned NOT NULL DEFAULT '0',
  `customer_id` varchar(200) COLLATE utf8_unicode_ci DEFAULT '0',
  `created_by` int(10) unsigned DEFAULT '0',
  `fname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firm` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street1` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street2` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uniqkey` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uniqkey_time` timestamp NULL DEFAULT NULL,
  UNIQUE KEY `admin_id` (`admin_id`),
  UNIQUE KEY `admin_name` (`admin_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`name`, `value`) VALUES
('PORT_AMAVIS', '10024;tcp;AMaVis;0;1;localhost'),
('PORT_DNS', '53;tcp;DNS;1;0;'),
('PORT_FTP', '21;tcp;FTP;1;0;'),
('PORT_HTTP', '80;tcp;HTTP;1;0;'),
('PORT_HTTPS', '443;tcp;HTTPS;0;0;'),
('PORT_IMAP', '143;tcp;IMAP;1;0;'),
('PORT_IMAP-SSL', '993;tcp;IMAP-SSL;0;0;'),
('PORT_POLICYD-WEIGHT', '12525;tcp;POLICYD-WEIGHT;1;1;localhost'),
('PORT_POP3', '110;tcp;POP3;1;0;'),
('PORT_POP3-SSL', '995;tcp;POP3-SSL;0;0;'),
('PORT_POSTGREY', '10023;tcp;POSTGREY;1;1;localhost'),
('PORT_SMTP', '25;tcp;SMTP;1;0;'),
('PORT_SMTP-SSL', '465;tcp;SMTP-SSL;1;0;'),
('PORT_SPAMASSASSIN', '783;tcp;SPAMASSASSIN;0;1;localhost'),
('PORT_SSH', '22;tcp;SSH;1;1;'),
('PORT_TELNET', '23;tcp;TELNET;1;0;'),
('SHOW_COMPRESSION_SIZE', '1'),
('PREVENT_EXTERNAL_LOGIN_ADMIN', '1'),
('PREVENT_EXTERNAL_LOGIN_RESELLER', '1'),
('PREVENT_EXTERNAL_LOGIN_CLIENT', '1'),
('SSL_KEY', ''),
('SSL_CERT', ''),
('SSL_STATUS',0),
('MIGRATION_ENABLED',0),
('DATABASE_REVISION', '58');

-- --------------------------------------------------------

--
-- Table structure for table `crontabs`
--

CREATE TABLE IF NOT EXISTS `cronjobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `minute` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `hour` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `dayofmonth` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `month` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `dayofweek` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `command` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `active` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `description` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status_msg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `domain`
--

CREATE TABLE IF NOT EXISTS `domain` (
  `domain_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain_gid` int(10) unsigned NOT NULL DEFAULT '0',
  `domain_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `domain_admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `domain_created_id` int(10) unsigned NOT NULL DEFAULT '0',
  `domain_created` int(10) unsigned NOT NULL DEFAULT '0',
  `domain_expires` int(10) unsigned NOT NULL DEFAULT '0',
  `domain_last_modified` int(10) unsigned NOT NULL DEFAULT '0',
  `domain_mailacc_limit` int(11) DEFAULT NULL,
  `domain_ftpacc_limit` int(11) DEFAULT NULL,
  `domain_traffic_limit` bigint(20) DEFAULT NULL,
  `domain_sqld_limit` int(11) DEFAULT NULL,
  `domain_sqlu_limit` int(11) DEFAULT NULL,
  `status` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_msg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `domain_alias_limit` int(11) DEFAULT NULL,
  `domain_subd_limit` int(11) DEFAULT NULL,
  `domain_ip_id` int(10) unsigned DEFAULT NULL,
  `domain_disk_limit` bigint(20) unsigned DEFAULT NULL,
  `domain_disk_usage` bigint(20) unsigned DEFAULT NULL,
  `domain_php` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `domain_php_config` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '8M;2M',
  `domain_php_edit` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `domain_cgi` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `allowbackup` varchar(8) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'full',
  `domain_dns` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `domain_ssl` varchar(15) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `ssl_key` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ssl_cert` varchar(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ssl_status` int(1) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `domain_id` (`domain_id`),
  UNIQUE KEY `domain_name` (`domain_name`),
  KEY `i_domain_admin_id` (`domain_admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `domain_aliasses`
--

CREATE TABLE IF NOT EXISTS `domain_aliasses` (
  `alias_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned DEFAULT NULL,
  `alias_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_msg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alias_mount` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alias_ip_id` int(10) unsigned DEFAULT NULL,
  `url_forward` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`alias_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `domain_dns`
--

CREATE TABLE IF NOT EXISTS `domain_dns` (
  `domain_dns_id` int(11) NOT NULL AUTO_INCREMENT,
  `domain_id` int(11) NOT NULL,
  `alias_id` int(11) NOT NULL,
  `domain_dns` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `domain_class` enum('IN','CH','HS') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'IN',
  `domain_type` enum('A','AAAA','CERT','CNAME','DNAME','GPOS','KEY','KX','MX','NAPTR','NSAP','NS','NXT','PTR','PX','SIG','SRV','TXT') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'A',
  `domain_text` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `protected` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  PRIMARY KEY (`domain_dns_id`),
  UNIQUE KEY `domain_id` (`domain_id`,`alias_id`,`domain_dns`,`domain_class`,`domain_type`,`domain_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `domain_traffic`
--

CREATE TABLE IF NOT EXISTS `domain_traffic` (
  `domain_id` int(10) unsigned NOT NULL DEFAULT '0',
  `dtraff_time` bigint(20) unsigned NOT NULL DEFAULT '0',
  `dtraff_web_in` bigint(20) unsigned DEFAULT NULL,
  `dtraff_web_out` bigint(20) unsigned DEFAULT NULL,
  `dtraff_ftp_in` bigint(20) unsigned DEFAULT NULL,
  `dtraff_ftp_out` bigint(20) unsigned DEFAULT NULL,
  `dtraff_mail` bigint(20) unsigned DEFAULT NULL,
  `dtraff_pop` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`domain_id`,`dtraff_time`),
  KEY `i_domain_id` (`domain_id`),
  KEY `i_dtraff_time` (`dtraff_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_tpls`
--

CREATE TABLE IF NOT EXISTS `email_tpls` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subject` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `error_pages`
--

CREATE TABLE IF NOT EXISTS `error_pages` (
  `ep_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `error_401` text COLLATE utf8_unicode_ci NOT NULL,
  `error_403` text COLLATE utf8_unicode_ci NOT NULL,
  `error_404` text COLLATE utf8_unicode_ci NOT NULL,
  `error_500` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ep_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ftp_group`
--

CREATE TABLE IF NOT EXISTS `ftp_group` (
  `groupname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gid` int(10) unsigned NOT NULL DEFAULT '0',
  `members` text COLLATE utf8_unicode_ci,
  UNIQUE KEY `groupname` (`groupname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ftp_log`
--

CREATE TABLE IF NOT EXISTS `ftp_log` (
  `ftp_log_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ftp_log_size` bigint(20) NOT NULL,
  `ftp_log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ftp_log_type` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `ftp_log_user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`ftp_log_file`,`ftp_log_size`,`ftp_log_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ftp_users`
--

CREATE TABLE IF NOT EXISTS `ftp_users` (
  `userid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `passwd` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `net2ftppasswd` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `gid` int(10) unsigned NOT NULL DEFAULT '0',
  `shell` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `homedir` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hosting_plans`
--

CREATE TABLE IF NOT EXISTS `hosting_plans` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reseller_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `props` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `setup_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `payment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` int(10) unsigned NOT NULL DEFAULT '0',
  `tos` blob NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `htaccess`
--

CREATE TABLE IF NOT EXISTS `htaccess` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dmn_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `group_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_msg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `htaccess_groups`
--

CREATE TABLE IF NOT EXISTS `htaccess_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dmn_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ugroup` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `members` text COLLATE utf8_unicode_ci,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_msg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `htaccess_users`
--

CREATE TABLE IF NOT EXISTS `htaccess_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dmn_id` int(10) unsigned NOT NULL DEFAULT '0',
  `uname` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `upass` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_msg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `log_message` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE IF NOT EXISTS `login` (
  `session_id` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ipaddr` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastaccess` int(10) unsigned DEFAULT NULL,
  `login_count` tinyint(1) DEFAULT '0',
  `captcha_count` tinyint(1) DEFAULT '0',
  `user_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mail_users`
--

CREATE TABLE IF NOT EXISTS `mail_users` (
  `mail_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mail_acc` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mail_pass` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mail_forward` text COLLATE utf8_unicode_ci,
  `domain_id` int(10) unsigned DEFAULT NULL,
  `mail_type` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sub_id` int(10) unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_msg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `quota` int(10) DEFAULT '10485760',
  `mail_addr` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`mail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `plan_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `domain_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `customer_id` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firm` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `zip` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `state` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street1` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `street2` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders_settings`
--

CREATE TABLE IF NOT EXISTS `orders_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `header` text COLLATE utf8_unicode_ci,
  `footer` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotalimits`
--

CREATE TABLE IF NOT EXISTS `quotalimits` (
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `quota_type` enum('user','group','class','all') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `per_session` enum('false','true') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'false',
  `limit_type` enum('soft','hard') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'soft',
  `bytes_in_avail` float NOT NULL DEFAULT '0',
  `bytes_out_avail` float NOT NULL DEFAULT '0',
  `bytes_xfer_avail` float NOT NULL DEFAULT '0',
  `files_in_avail` int(10) unsigned NOT NULL DEFAULT '0',
  `files_out_avail` int(10) unsigned NOT NULL DEFAULT '0',
  `files_xfer_avail` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotatallies`
--

CREATE TABLE IF NOT EXISTS `quotatallies` (
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `quota_type` enum('user','group','class','all') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'user',
  `bytes_in_used` float NOT NULL DEFAULT '0',
  `bytes_out_used` float NOT NULL DEFAULT '0',
  `bytes_xfer_used` float NOT NULL DEFAULT '0',
  `files_in_used` int(10) unsigned NOT NULL DEFAULT '0',
  `files_out_used` int(10) unsigned NOT NULL DEFAULT '0',
  `files_xfer_used` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reseller_props`
--

CREATE TABLE IF NOT EXISTS `reseller_props` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reseller_id` int(10) unsigned NOT NULL DEFAULT '0',
  `current_dmn_cnt` int(11) DEFAULT NULL,
  `max_dmn_cnt` int(11) DEFAULT NULL,
  `current_sub_cnt` int(11) DEFAULT NULL,
  `max_sub_cnt` int(11) DEFAULT NULL,
  `current_als_cnt` int(11) DEFAULT NULL,
  `max_als_cnt` int(11) DEFAULT NULL,
  `current_mail_cnt` int(11) DEFAULT NULL,
  `max_mail_cnt` int(11) DEFAULT NULL,
  `current_ftp_cnt` int(11) DEFAULT NULL,
  `max_ftp_cnt` int(11) DEFAULT NULL,
  `current_sql_db_cnt` int(11) DEFAULT NULL,
  `max_sql_db_cnt` int(11) DEFAULT NULL,
  `current_sql_user_cnt` int(11) DEFAULT NULL,
  `max_sql_user_cnt` int(11) DEFAULT NULL,
  `current_disk_amnt` int(11) DEFAULT NULL,
  `max_disk_amnt` int(11) DEFAULT NULL,
  `current_traff_amnt` int(11) DEFAULT NULL,
  `max_traff_amnt` int(11) DEFAULT NULL,
  `support_system` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  `customer_id` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `reseller_ips` text COLLATE utf8_unicode_ci,
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `server_ips`
--

CREATE TABLE IF NOT EXISTS `server_ips` (
  `ip_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_number` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip_number_v6` varchar(39) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip_domain` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip_alias` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip_card` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip_ssl_domain_id` int(10) DEFAULT NULL,
  `ip_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  UNIQUE KEY `ip_id` (`ip_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `server_traffic`
--

CREATE TABLE IF NOT EXISTS `server_traffic` (
  `traff_time` int(10) unsigned NOT NULL DEFAULT '0',
  `bytes_in` bigint(20) unsigned DEFAULT NULL,
  `bytes_out` bigint(20) unsigned DEFAULT NULL,
  `bytes_mail_in` bigint(20) unsigned DEFAULT NULL,
  `bytes_mail_out` bigint(20) unsigned DEFAULT NULL,
  `bytes_pop_in` bigint(20) unsigned DEFAULT NULL,
  `bytes_pop_out` bigint(20) unsigned DEFAULT NULL,
  `bytes_web_in` bigint(20) unsigned DEFAULT NULL,
  `bytes_web_out` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`traff_time`),
  KEY `traff_time` (`traff_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sql_database`
--

CREATE TABLE IF NOT EXISTS `sql_database` (
  `sqld_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned DEFAULT '0',
  `sqld_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'n/a',
  UNIQUE KEY `sqld_id` (`sqld_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sql_user`
--

CREATE TABLE IF NOT EXISTS `sql_user` (
  `sqlu_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sqld_id` int(10) unsigned DEFAULT '0',
  `sqlu_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT 'n/a',
  `sqlu_pass` varchar(64) COLLATE utf8_unicode_ci DEFAULT 'n/a',
  UNIQUE KEY `sqlu_id` (`sqlu_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `straff_settings`
--

CREATE TABLE IF NOT EXISTS `straff_settings` (
  `straff_max` int(10) unsigned DEFAULT NULL,
  `straff_warn` int(10) unsigned DEFAULT NULL,
  `straff_email` int(10) unsigned DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `straff_settings`
--

INSERT INTO `straff_settings` (`straff_max`, `straff_warn`, `straff_email`) VALUES (0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `subdomain`
--

CREATE TABLE IF NOT EXISTS `subdomain` (
  `subdomain_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_id` int(10) unsigned DEFAULT NULL,
  `subdomain_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subdomain_mount` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subdomain_url_forward` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_msg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`subdomain_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subdomain_alias`
--

CREATE TABLE IF NOT EXISTS `subdomain_alias` (
  `subdomain_alias_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias_id` int(10) unsigned DEFAULT NULL,
  `subdomain_id` int(10) unsigned DEFAULT NULL,
  `subdomain_alias_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subdomain_alias_mount` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subdomain_alias_url_forward` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status_msg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`subdomain_alias_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
  `ticket_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_level` int(10) DEFAULT NULL,
  `ticket_from` int(10) unsigned DEFAULT NULL,
  `ticket_to` int(10) unsigned DEFAULT NULL,
  `ticket_status` int(10) unsigned DEFAULT NULL,
  `ticket_reply` int(10) unsigned DEFAULT NULL,
  `ticket_urgency` int(10) unsigned DEFAULT NULL,
  `ticket_date` int(10) unsigned DEFAULT NULL,
  `ticket_subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ticket_message` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`ticket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_gui_props`
--

CREATE TABLE IF NOT EXISTS `user_gui_props` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lang` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `layout` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
