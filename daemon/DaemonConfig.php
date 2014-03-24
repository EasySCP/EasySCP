<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * EasySCP Daemon Config functions
 */

class DaemonConfig {
	static $cfg;
	static $cmd;

	public static function Reload(){
		// unset(self::$cfg);
		self::$cfg = simplexml_load_file('/etc/easyscp/EasySCP_Config.xml');
	}

	public static function Save(){
		$handle = fopen('/etc/easyscp/EasySCP_Config.xml', "wb");
		fwrite($handle, self::$cfg->asXML());
		fclose($handle);

		$handle = NULL;
		unset($handle);
	}

	public static function SaveOldConfig(){
		$tpl_param = array(
			'BuildDate'					=> self::$cfg->BuildDate,
			'DistName'					=> self::$cfg->DistName,
			'Version'					=> self::$cfg->Version,
			'DEFAULT_ADMIN_ADDRESS'		=> self::$cfg->DEFAULT_ADMIN_ADDRESS,
			'SERVER_HOSTNAME'			=> self::$cfg->SERVER_HOSTNAME,
			'BASE_SERVER_IP'			=> self::$cfg->BASE_SERVER_IP,
			'BASE_SERVER_VHOST'			=> self::$cfg->BASE_SERVER_VHOST,
			'BASE_SERVER_VHOST_PREFIX'	=> self::$cfg->BASE_SERVER_VHOST_PREFIX,
			'DATABASE_HOST'				=> self::$cfg->DATABASE_HOST,
			'DATABASE_NAME'				=> self::$cfg->DATABASE_NAME,
			'DATABASE_PASSWORD'			=> self::$cfg->DATABASE_PASSWORD,
			'DATABASE_USER'				=> self::$cfg->DATABASE_USER,
			'PHP_TIMEZONE'				=> self::$cfg->PHP_TIMEZONE,
			'SECONDARY_DNS'				=> self::$cfg->Secondary_DNS,
			'LOCAL_DNS_RESOLVER'		=> self::$cfg->LOCAL_DNS_RESOLVER,
			'AWSTATS_ACTIVE'			=> self::$cfg->AWSTATS_ACTIVE,
			'AWSTATS_MODE'				=> self::$cfg->AWSTATS_MODE,
			'APACHE_SUEXEC_MIN_GID'		=> self::$cfg->APACHE_SUEXEC_MIN_GID,
			'APACHE_SUEXEC_MIN_UID'		=> self::$cfg->APACHE_SUEXEC_MIN_UID,
			'MTA_MAILBOX_MIN_UID'		=> self::$cfg->MTA_MAILBOX_MIN_UID,
			'MTA_MAILBOX_UID'			=> self::$cfg->MTA_MAILBOX_UID,
			'MTA_MAILBOX_GID'			=> self::$cfg->MTA_MAILBOX_GID,
			'MYSQL_PREFIX'				=> self::$cfg->MYSQL_PREFIX,
			'MYSQL_PREFIX_TYPE'			=> self::$cfg->MYSQL_PREFIX_TYPE,
			'DEBUG'						=> self::$cfg->DEBUG

		);
		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch('tpl/easyscp.conf');
		$confFile = DaemonConfig::$cfg->CONF_DIR . '/easyscp.conf';

		if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644 )){
			return 'Error: Failed to write '.$confFile;
		}

		$tpl = NULL;
		unset($tpl);

		return 'Ok';
	}

	public static function SavePMAConfig(){
		$xml = simplexml_load_file(DaemonConfig::$cfg->CONF_DIR . '/EasySCP_Config_PMA.xml');

		// Backup current phpMyAdmin conf if exists
		if (file_exists(DaemonConfig::$cfg->GUI_ROOT_DIR.'/tools/pma/config.inc.php')){
			exec(DaemonConfig::$cmd->CMD_CP.' -pf '.DaemonConfig::$cfg->GUI_ROOT_DIR.'/tools/pma/config.inc.php '.DaemonConfig::$cfg->CONF_DIR.'/pma/backup/config.inc.php'.'_'.date("Y_m_d_H_i_s"), $result, $error);
		}

		$tpl_param = array(
			'BLOWFISH'	=> $xml->PMA_BLOWFISH,
			'PMA_USER'	=> $xml->PMA_USER,
			'PMA_PASS'	=> $xml->PMA_PASSWORD,
			'HOSTNAME'	=> $xml->DATABASE_HOST,
			'TMP_DIR'	=> $xml->TMP_DIR
		);

		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch('pma/parts/config.inc.tpl');
		$confFile = DaemonConfig::$cfg->CONF_DIR . '/pma/working/config.inc.php';
		$tpl = NULL;
		unset($tpl);

		if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0640 )){
			System_Daemon::debug('Error: Failed to write '.$confFile);
			return;
		}

		System_Daemon::debug('Storing the pma config file in the working directory');

		exec(DaemonConfig::$cmd->CMD_CP.' -f '.DaemonConfig::$cfg->CONF_DIR.'/pma/working/config.inc.php '.DaemonConfig::$cfg->GUI_ROOT_DIR.'/tools/pma', $result, $error);

		DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->GUI_ROOT_DIR.'/tools/pma/config.inc.php', DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF.DaemonConfig::$cfg->APACHE_SUEXEC_MIN_GID, DaemonConfig::$cfg->APACHE_GROUP, 0440);
	}

	public static function SaveRCConfig(){
		$xml = simplexml_load_file(DaemonConfig::$cfg->CONF_DIR . '/EasySCP_Config_RC.xml');

		// Backup current Roundcube conf if exists
		if (file_exists(DaemonConfig::$cfg->GUI_ROOT_DIR.'/tools/webmail/config/db.inc.php')){
			exec(DaemonConfig::$cmd->CMD_CP.' -pf '.DaemonConfig::$cfg->GUI_ROOT_DIR.'/tools/webmail/config/db.inc.php '.DaemonConfig::$cfg->CONF_DIR.'/roundcube/backup/db.inc.php'.'_'.date("Y_m_d_H_i_s"), $result, $error);
		}
		if (file_exists(DaemonConfig::$cfg->GUI_ROOT_DIR.'/tools/webmail/config/main.inc.php')){
			exec(DaemonConfig::$cmd->CMD_CP.' -pf '.DaemonConfig::$cfg->GUI_ROOT_DIR.'/tools/webmail/config/main.inc.php '.DaemonConfig::$cfg->CONF_DIR.'/roundcube/backup/main.inc.php'.'_'.date("Y_m_d_H_i_s"), $result, $error);
		}

		$tpl_param = array(
			'CUBE_USER'	=> $xml->CUBE_USER,
			'CUBE_PASS'	=> $xml->CUBE_PASS,
			'HOSTNAME'	=> $xml->DATABASE_HOST
		);

		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch('roundcube/parts/db.inc.tpl');
		$confFile = DaemonConfig::$cfg->CONF_DIR . '/roundcube/working/db.inc.php';
		$tpl = NULL;
		unset($tpl);

		if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0640 )){
			System_Daemon::debug('Error: Failed to write '.$confFile);
			return;
		}

		System_Daemon::debug('Storing the roundcube config files in the working directory');

		exec(DaemonConfig::$cmd->CMD_CP.' -f '.DaemonConfig::$cfg->CONF_DIR.'/roundcube/working/db.inc.php '.DaemonConfig::$cfg->GUI_ROOT_DIR.'/tools/webmail/config', $result, $error);
		exec(DaemonConfig::$cmd->CMD_CP.' -f '.DaemonConfig::$cfg->CONF_DIR.'/roundcube/working/main.inc.php '.DaemonConfig::$cfg->GUI_ROOT_DIR.'/tools/webmail/config', $result, $error);

		DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->GUI_ROOT_DIR.'/tools/webmail/config/db.inc.php', DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF.DaemonConfig::$cfg->APACHE_SUEXEC_MIN_GID, DaemonConfig::$cfg->APACHE_GROUP, 0440);
		DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->GUI_ROOT_DIR.'/tools/webmail/config/main.inc.php', DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF.DaemonConfig::$cfg->APACHE_SUEXEC_MIN_GID, DaemonConfig::$cfg->APACHE_GROUP, 0440);
	}
}

DaemonConfig::$cfg = simplexml_load_file('/etc/easyscp/EasySCP_Config.xml');
DaemonConfig::$cmd = simplexml_load_file('/etc/easyscp/EasySCP_CMD.xml');
?>