<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2018 by Easy Server Control Panel - http://www.easyscp.net
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
	static $distro;

	public static function Rebuild(){
		$xml_new = simplexml_load_file(DaemonConfig::$cfg->{'CONF_DIR'} . '/tpl/EasySCP_Config.xml');

		foreach ($xml_new->children() as $child){
			$temp = $child->getName();
			if (isset(self::$cfg->$temp)){
				$xml_new->$temp = self::$cfg->$temp;
			}
		}

		$xml_new->{'BuildDate'} = file_get_contents(DaemonConfig::$cfg->{'CONF_DIR'} . '/BUILD');
		$xml_new->{'Version'} = file_get_contents(DaemonConfig::$cfg->{'CONF_DIR'} . '/VERSION');

		$handle = fopen(EasyConfig_PATH . '/EasySCP_Config.xml', "wb");
		fwrite($handle, $xml_new->asXML());
		fclose($handle);

		$handle = NULL;
		unset($handle);

		return true;
	}

	public static function Reload(){
		// unset(self::$cfg);
		self::$cfg = simplexml_load_file(EasyConfig_PATH . '/EasySCP_Config.xml');
	}

	public static function Save(){
		System_Daemon::debug('Starting "DaemonConfig::Save" subprocess.');

		$handle = fopen(EasyConfig_PATH . '/EasySCP_Config.xml', "wb");
		fwrite($handle, self::$cfg->asXML());
		fclose($handle);

		$handle = NULL;
		unset($handle);

		System_Daemon::debug('Finished "DaemonConfig::Save" subprocess.');
	}

	public static function SaveOldConfig(){
		System_Daemon::debug('Starting "DaemonConfig::SaveOldConfig" subprocess.');

		$tpl_param = array(
			'BuildDate'					=> self::$cfg->{'BuildDate'},
			'DistName'					=> self::$cfg->{'DistName'},
			'Version'					=> self::$cfg->{'Version'},
			'DEFAULT_ADMIN_ADDRESS'		=> self::$cfg->{'DEFAULT_ADMIN_ADDRESS'},
			'SERVER_HOSTNAME'			=> self::$cfg->{'SERVER_HOSTNAME'},
			'BASE_SERVER_IP'			=> self::$cfg->{'BASE_SERVER_IP'},
			'BASE_SERVER_VHOST'			=> self::$cfg->{'BASE_SERVER_VHOST'},
			'BASE_SERVER_VHOST_PREFIX'	=> self::$cfg->{'BASE_SERVER_VHOST_PREFIX'},
			'DATABASE_HOST'				=> self::$cfg->{'DATABASE_HOST'},
			'DATABASE_NAME'				=> self::$cfg->{'DATABASE_NAME'},
			'DATABASE_PASSWORD'			=> self::$cfg->{'DATABASE_PASSWORD'},
			'DATABASE_USER'				=> self::$cfg->{'DATABASE_USER'},
			'PHP_TIMEZONE'				=> self::$cfg->{'PHP_TIMEZONE'},
			'SECONDARY_DNS'				=> self::$cfg->{'Secondary_DNS'},
			'LOCAL_DNS_RESOLVER'		=> self::$cfg->{'LOCAL_DNS_RESOLVER'},
			'AWSTATS_ACTIVE'			=> self::$cfg->{'AWSTATS_ACTIVE'},
			'AWSTATS_MODE'				=> self::$cfg->{'AWSTATS_MODE'},
			'APACHE_SUEXEC_MIN_GID'		=> self::$cfg->{'APACHE_SUEXEC_MIN_GID'},
			'APACHE_SUEXEC_MIN_UID'		=> self::$cfg->{'APACHE_SUEXEC_MIN_UID'},
			'MTA_MAILBOX_MIN_UID'		=> self::$cfg->{'MTA_MAILBOX_MIN_UID'},
			'MTA_MAILBOX_UID'			=> self::$cfg->{'MTA_MAILBOX_UID'},
			'MTA_MAILBOX_GID'			=> self::$cfg->{'MTA_MAILBOX_GID'},
			'MYSQL_PREFIX'				=> self::$cfg->{'MYSQL_PREFIX'},
			'MYSQL_PREFIX_TYPE'			=> self::$cfg->{'MYSQL_PREFIX_TYPE'},
			'DEBUG'						=> self::$cfg->{'DEBUG'}

		);
		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch('tpl/easyscp.conf');
		$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/easyscp.conf';

		if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
			return 'Error: Failed to write '.$confFile;
		}

		$tpl = NULL;
		unset($tpl);

		System_Daemon::debug('Finished "DaemonConfig::SaveOldConfig" subprocess.');

		return 'Ok';
	}
}

DaemonConfig::$cfg = simplexml_load_file(EasyConfig_PATH . '/EasySCP_Config.xml');
DaemonConfig::$cmd = simplexml_load_file(EasyConfig_PATH . '/EasySCP_CMD.xml');
DaemonConfig::$distro = simplexml_load_file(EasyConfig_PATH . '/EasySCP_Distro.xml');
?>