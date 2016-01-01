<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * Class for database updates
 *
 * @category	EasySCP
 * @package		EasySCP_Update
 */
class EasySCP_Update_Database extends EasySCP_Update {

	/**
	 * EasySCP_Update_Database instance
	 *
	 * @var EasySCP_Update_Database
	 */
	protected static $_instance = null;

	/**
	 * The database variable name for the update version
	 *
	 * @var string
	 */
	protected $_databaseVariableName = 'DATABASE_REVISION';

	/**
	 * The update functions prefix
	 *
	 * @var string
	 */
	protected $_functionName = '_databaseUpdate_';

	/**
	 * Default error message for updates that have failed
	 *
	 * @var string
	 */
	protected $_errorMessage = 'Database update %s failed';

	/**
	 * Get an EasySCP_Update_Database instance
	 *
	 * @return EasySCP_Update_Database An EasySCP_Update_Database instance
	 */
	public static function getInstance() {

		if (is_null(self::$_instance)) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/*
	 * Insert the update functions below this entry. The revision has to be
	 * ascending and unique. Each databaseUpdate function has to return a array,
	 * even if the array contains only one entry.
	 */

	/**
	 * Remove unused table 'suexec_props'
	 *
	 * @return array
	 */
	protected function _databaseUpdate_46() {

		$sqlUpd = array();

		$sqlUpd[] = "
			DROP TABLE IF EXISTS
				`suexec_props`
			;
		";

		return $sqlUpd;
	}

	/**
	 * Updated standard user quota to 100MB
	 *
	 * @return array
	 */
	protected function _databaseUpdate_47() {

		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE
				`mail_users`
			CHANGE
				`quota` `quota` INT( 10 ) NULL DEFAULT '104857600'
		;";
		$sqlUpd[] = "
			UPDATE
				`mail_users`
			SET
				`quota` = '104857600'
			WHERE
				`quota` = '10485760';
		;";

		return $sqlUpd;
	}

	/**
	 * Adds needed field to ftp_users table to allow single sign on to net2ftp
	 *
	 * @return array
	 */
	protected function _databaseUpdate_48() {
		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE
				`ftp_users`
			ADD
				`net2ftppasswd` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL
			AFTER
				`passwd`;
		";

		return $sqlUpd;
	}

	/**
	 * Remove unused column 'user_gui_props.logo'
	 *
	 * @author Markus Szywon <markus.szywon@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_49() {
		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE
				`user_gui_props`
  			DROP
				`logo`;
		";

		return $sqlUpd;
	}

	/**
	 * Adds menu_icon field to custom_menus table to allow different icons on custom buttons
	 *
	 * @author Markus Szywon <markus.szywon@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_50() {
		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE
				`custom_menus`
			ADD
				`menu_icon` VARCHAR( 200 ) NULL;
		";

		return $sqlUpd;
	}

	/**
	 * Adds field to enable/disable migration from GUI
	 *
	 * @author Markus Szywon <markus.szywon@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_51() {
		$sqlUpd = array();

		$sqlUpd[] = "
			INSERT INTO
				`config` (name, value)
			VALUES
				('MIGRATION_ENABLED', 0)
			;
		";

		return $sqlUpd;
	}

	/**
	 * Adds database fields for SSL configuration
	 *
	 * @author Tom Winterhalder <tom.winterhalder@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_52(){
		$sqlUpd = array();

		$sqlUpd[] = "
			INSERT INTO
				`config` (name, value)
			VALUES
				('SSL_KEY', ''),
				('SSL_CERT',''),
				('SSL_STATUS','0')
			;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`domain`
			ADD
				`domain_ssl` VARCHAR(15) NOT NULL DEFAULT 'No',
			ADD
				`ssl_key` VARCHAR(5000) NULL DEFAULT NULL,
			ADD
				`ssl_cert` VARCHAR(5000) NULL DEFAULT NULL,
			ADD
				`ssl_status` INT(1) unsigned NOT NULL DEFAULT '0';
		";

		return $sqlUpd;
	}

	/**
	 * Adds PHP Editor fields
	 *
	 * @author Markus Szywon <markus.szywon@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_53(){
		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE
				`domain`
			CHANGE
			 	`domain_php` `domain_php` VARCHAR(3) NOT NULL DEFAULT 'no',
			 CHANGE
			 	`domain_cgi` `domain_cgi` VARCHAR(3) NOT NULL DEFAULT 'no',
			 CHANGE
			 	`domain_dns` `domain_dns` VARCHAR(3) NOT NULL DEFAULT 'no',
			 ADD
			 	`domain_php_config` varchar(30) collate utf8_unicode_ci NOT NULL DEFAULT '8M;2M' AFTER `domain_php`,
			 ADD
			 	`domain_php_edit` VARCHAR(3) NOT NULL DEFAULT 'no' AFTER `domain_php_config`;
		";

		return $sqlUpd;
	}

	/**
	 * Adds Status MSG fields
	 *
	 * @author Markus Szywon <markus.szywon@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_54(){
		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE
				`domain`
			CHANGE
			 	`domain_status` `status` VARCHAR(15) default NULL,
			 ADD
			 	`status_msg` varchar(255) collate utf8_unicode_ci default NULL AFTER status;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`domain_aliasses`
			CHANGE
			 	`alias_status` `status` VARCHAR(15) default NULL,
			 ADD
			 	`status_msg` varchar(255) collate utf8_unicode_ci default NULL AFTER status;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`htaccess`
			CHANGE
			 	`status` `status` VARCHAR(15) default NULL,
			 ADD
			 	`status_msg` varchar(255) collate utf8_unicode_ci default NULL AFTER status;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`htaccess_groups`
			CHANGE
			 	`status` `status` VARCHAR(15) default NULL,
			 ADD
			 	`status_msg` varchar(255) collate utf8_unicode_ci default NULL AFTER status;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`htaccess_users`
			CHANGE
			 	`status` `status` VARCHAR(15) default NULL,
			 ADD
			 	`status_msg` varchar(255) collate utf8_unicode_ci default NULL AFTER status;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`mail_users`
			CHANGE
			 	`status` `status` VARCHAR(15) default NULL,
			 ADD
			 	`status_msg` varchar(255) collate utf8_unicode_ci default NULL AFTER status;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`subdomain`
			CHANGE
			 	`subdomain_status` `status` VARCHAR(15) default NULL,
			 ADD
			 	`status_msg` varchar(255) collate utf8_unicode_ci default NULL AFTER status;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`subdomain_alias`
			CHANGE
			 	`subdomain_alias_status` `status` VARCHAR(15) default NULL,
			 ADD
			 	`status_msg` varchar(255) collate utf8_unicode_ci default NULL AFTER status;
		";

		return $sqlUpd;
	}

	/**
	 * Change domain_traffic table
	 * Add ftp_log table
	 *
	 * @author Markus Szywon <markus.szywon@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_55(){
		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE
				`domain_traffic`
			DROP
				`dtraff_id`,
			CHANGE
				`dtraff_web` `dtraff_web_in` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
			ADD
				`dtraff_web_out` BIGINT UNSIGNED NOT NULL DEFAULT '0' AFTER `dtraff_web_in`,
			CHANGE
				`dtraff_ftp` `dtraff_ftp_in` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT '0',
			ADD
				`dtraff_ftp_out` BIGINT UNSIGNED NOT NULL DEFAULT '0' AFTER `dtraff_ftp_in`,
			ADD PRIMARY KEY
				( `domain_id` , `dtraff_time` );
		";

		$sqlUpd[] = "
			CREATE TABLE IF NOT EXISTS
				`ftp_log` (
					`ftp_log_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					`ftp_log_size` bigint(20) NOT NULL,
					`ftp_log_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
					`ftp_log_user` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
					PRIMARY KEY (`ftp_log_file`,`ftp_log_size`,`ftp_log_time`)
				)
				ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		";

		return $sqlUpd;
	}

	/**
	 * Adds database field for subdomain_alias
	 *
	 * @author Tom Winterhalder <tom.winterhalder@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_56(){
		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE 
				`subdomain_alias` 
			ADD 
				`subdomain_id` INT( 10 ) UNSIGNED NULL 
			AFTER 
				`alias_id` ;
		";

		return $sqlUpd;
	}

	/**
	 * Remove unused column 'mail_auto_respond' and 'mail_auto_respond_text'
	 *
	 * @author Markus Szywon <markus.szywon@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_57(){
		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE
				`ftp_log`
			ADD
				`ftp_log_type` varchar(4) COLLATE utf8_unicode_ci NOT NULL
			AFTER
				`ftp_log_time` ;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`mail_users`
  			DROP
				`mail_auto_respond`;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`mail_users`
  			DROP
				`mail_auto_respond_text`;
		";

		return $sqlUpd;
	}

	/**
	 * Add/Update database field for ipv6 support
	 *
	 * @author Markus Szywon <markus.szywon@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_58(){
		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE
				`server_ips`
			CHANGE
				`ip_number` `ip_number` varchar(15) COLLATE 'utf8_unicode_ci' NULL;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`server_ips`
			ADD
				`ip_number_v6` varchar(39) COLLATE utf8_unicode_ci NOT NULL
			AFTER
				`ip_number`;
		";

		return $sqlUpd;
	}

	/**
	 * Adds database fields for SSL configuration
	 *
	 * @author Markus Szywon <markus.szywon@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_59(){
		$sqlUpd = array();

		$sqlUpd[] = "
			INSERT INTO
				`config` (name, value)
			VALUES
				('SSL_CACERT', '')
			;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`domain`
			ADD
				`ssl_cacert` varchar(5000) COLLATE utf8_unicode_ci NULL DEFAULT NULL AFTER ssl_cert;
		";

		return $sqlUpd;
	}

	/**
	 * Adds status and status msg fields for sql data
	 * Update status fields
	 *
	 * @author Markus Szywon <markus.szywon@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_60(){
		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE
				`htaccess`
			CHANGE
				`status` `status` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`htaccess_groups`
			CHANGE
				`status` `status` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`htaccess_users`
			CHANGE
				`status` `status` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`mail_users`
			CHANGE
				`status` `status` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
		";

		$sqlUpd[] = "
			ALTER TABLE
				`sql_database`
			ADD
				`status` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
			ADD
				`status_msg` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;
		";

		$sqlUpd[] = "
			UPDATE
				`sql_database`
			SET
				`status` = 'ok';
		";

		$sqlUpd[] = "
			ALTER TABLE
				`sql_user`
			ADD
				`status` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
			ADD
				`status_msg` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;
		";

		$sqlUpd[] = "
			UPDATE
				`sql_user`
			SET
				`status` = 'ok';
		";

		return $sqlUpd;
	}

	/**
	 * Add/Update database field for php support
	 *
	 * @author Markus Szywon <markus.szywon@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_61(){
		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE
				`domain`
			CHANGE
				`domain_php_config` `domain_php_config` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '32M;8M';
		";

		return $sqlUpd;
	}

	/**
	 * Adds database fields for MTA SSL configuration
	 *
	 * @author Markus Szywon <markus.szywon@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_62(){
		$sqlUpd = array();

		$sqlUpd[] = "
			INSERT INTO
				`config` (name, value)
			VALUES
				('MTA_SSL_KEY', ''),
				('MTA_SSL_CERT',''),
				('MTA_SSL_CACERT', ''),
				('MTA_SSL_STATUS','0')
			;
		";

		return $sqlUpd;
	}
	
	/**
	 * Add/Update database field to count backup support
	 *
	 * @author Tom Winterhalder <tom.winterhalder@easyscp.net>
	 * @return array
	 */
	protected function _databaseUpdate_63() {
		
		$sqlUpd = array();

		$sqlUpd[] = "
			ALTER TABLE 
				`domain`
			ADD
				`domain_disk_countbackup` VARCHAR(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no'
			AFTER
				`domain_disk_usage`
		";

		$sqlUpd[] = "
			ALTER TABLE 
				`cronjobs` 
			ADD 
				`schedule` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
			AFTER 
				`user_id`
		";

		$sqlUpd[] = "SET SQL_SAFE_UPDATES = 0";
		
		$sqlUpd[] = "
			UPDATE 
				`cronjobs`
			SET 
				`schedule` = concat(`minute`,' ',`hour` ,' ',`dayofmonth`,' ',`month`,' ',`dayofweek`)
			";
		
		$sqlUpd[] = "SET SQL_SAFE_UPDATES = 1";
		
		$sqlUpd[] = "
			ALTER TABLE 
				`cronjobs`
			DROP 
				`minute`,
			DROP 
				`hour`,
			DROP 
				`dayofmonth`,
			DROP 
				`month`,
			DROP 
				`dayofweek`
			";

		$sqlUpd[] = "
			ALTER TABLE 
				`cronjobs` 
			ADD UNIQUE 
				`schedcmd` 
				(
					`schedule`(50) ,
					`command`(50)
				)
			";
		return $sqlUpd;
	}

	/*
	 * DO NOT CHANGE ANYTHING BELOW THIS LINE!
	 */
}