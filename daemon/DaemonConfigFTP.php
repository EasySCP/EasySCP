<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2015 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * EasySCP Daemon Config Tools functions
 */

class DaemonConfigFTP {

	/**
	 * @return mixed
	 */
	public static function CreateProFTPdPass(){
		System_Daemon::debug('Starting "DaemonConfigFTP::CreateProFTPdPass" subprocess.');

		$xml = simplexml_load_file(DaemonConfig::$cfg->{'CONF_DIR'} . '/tpl/EasySCP_Config_FTP.xml');

		System_Daemon::debug('Building the new ftp config file');

		$xml->{'DB_DATABASE'}	= DB::$DB_DATABASE;
		$xml->{'DB_HOST'}		= idn_to_ascii(DaemonConfig::$cfg->{'DATABASE_HOST'});
		$xml->{'FTP_USER'}		= 'vftp';
		$xml->{'FTP_PASSWORD'}	= DB::encrypt_data(DaemonCommon::generatePassword(18));

		$handle = fopen(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_FTP.xml', "wb");
		fwrite($handle, $xml->asXML());
		fclose($handle);

		System_Daemon::debug('Create/Update Proftpd SQL user data');

		$sql_param = array(
			':DATABASE_HOST'=> $xml->{'DB_HOST'},
			':FTP_USER'		=> $xml->{'FTP_USER'},
			':FTP_PASSWORD'	=> DB::decrypt_data($xml->{'FTP_PASSWORD'})
		);

		$sql_query = "
			GRANT SELECT,INSERT,UPDATE,DELETE ON ftp_group TO :FTP_USER@:DATABASE_HOST IDENTIFIED BY :FTP_PASSWORD;
			GRANT SELECT,INSERT,UPDATE,DELETE ON ftp_log TO :FTP_USER@:DATABASE_HOST IDENTIFIED BY :FTP_PASSWORD;
			GRANT SELECT,INSERT,UPDATE,DELETE ON ftp_users TO :FTP_USER@:DATABASE_HOST IDENTIFIED BY :FTP_PASSWORD;
			GRANT SELECT,INSERT,UPDATE,DELETE ON quotalimits TO :FTP_USER@:DATABASE_HOST IDENTIFIED BY :FTP_PASSWORD;
			GRANT SELECT,INSERT,UPDATE,DELETE ON quotatallies TO :FTP_USER@:DATABASE_HOST IDENTIFIED BY :FTP_PASSWORD;
			FLUSH PRIVILEGES;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		/*

		$xml = simplexml_load_file(DaemonConfig::$cfg->{'ROOT_DIR'} . '/../setup/config.xml');

		System_Daemon::debug('Create/Update Proftpd SQL user data');

		$sql_param = array(
			':DATABASE_HOST'=> $xml->{'DB_HOST'},
			':FTP_USER'		=> $xml->{'FTP_USER'},
			':FTP_PASSWORD'	=> $xml->{'FTP_PASSWORD'}
		);

		$sql_query = "
			GRANT SELECT,INSERT,UPDATE,DELETE ON ftp_group TO :FTP_USER@:DATABASE_HOST IDENTIFIED BY :FTP_PASSWORD;
			GRANT SELECT,INSERT,UPDATE,DELETE ON ftp_log TO :FTP_USER@:DATABASE_HOST IDENTIFIED BY :FTP_PASSWORD;
			GRANT SELECT,INSERT,UPDATE,DELETE ON ftp_users TO :FTP_USER@:DATABASE_HOST IDENTIFIED BY :FTP_PASSWORD;
			GRANT SELECT,INSERT,UPDATE,DELETE ON quotalimits TO :FTP_USER@:DATABASE_HOST IDENTIFIED BY :FTP_PASSWORD;
			GRANT SELECT,INSERT,UPDATE,DELETE ON quotatallies TO :FTP_USER@:DATABASE_HOST IDENTIFIED BY :FTP_PASSWORD;
			FLUSH PRIVILEGES;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		if (!file_exists(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_FTP.xml')) {
			$ftp = simplexml_load_file(DaemonConfig::$cfg->{'CONF_DIR'} . '/tpl/EasySCP_Config_FTP.xml');

			System_Daemon::debug('Building the new ftp config file');

			$ftp->{'DB_DATABASE'}	= $xml->{'DB_DATABASE'};
			$ftp->{'DB_HOST'}		= $xml->{'DB_HOST'};
			$ftp->{'FTP_USER'}		= $xml->{'FTP_USER'};
			$ftp->{'FTP_PASSWORD'}	= DB::encrypt_data($xml->{'FTP_PASSWORD'});

			$handle = fopen(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_FTP.xml', "wb");
			fwrite($handle, $ftp->asXML());
			fclose($handle);
		}
		*/

		System_Daemon::debug('Finished "DaemonConfigFTP::CreateProFTPdPass" subprocess.');

		return true;
	}

	/**
	 * @return mixed
	 */
	public static function SaveProFTPdConfig(){
		System_Daemon::debug('Starting "DaemonConfigFTP::SaveProFTPdConfig" subprocess.');

		$xml = simplexml_load_file(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_FTP.xml');

		// Create config dir if it doesn't exists
		if (!file_exists(DaemonConfig::$cfg->{'FTPD_CONF_DIR'})){
			DaemonCommon::systemCreateDirectory(DaemonConfig::$cfg->{'FTPD_CONF_DIR'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0755);
		}

		// Backup current proftpd.conf if exists
		if (file_exists(DaemonConfig::$cfg->{'FTPD_CONF_FILE'})){
			exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'FTPD_CONF_FILE'} . ' ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/proftpd/backup/proftpd.conf' . '_' . date("Y_m_d_H_i_s"), $result, $error);
		}

		// Loading the template from /etc/easyscp/proftpd/parts/, Building the new file
		// Store the new file in working directory

		$tpl_param = array(
			'HOST_NAME' => idn_to_ascii(DaemonConfig::$cfg->{'SERVER_HOSTNAME'})
		);

		$tpl_param['UseIPv6'] = (isset(DaemonConfig::$cfg->{'BASE_SERVER_IPv6'}) && DaemonConfig::$cfg->{'BASE_SERVER_IPv6'} != '') ? 'on' : 'off';

		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch('proftpd/parts/proftpd_' . DaemonConfig::$cfg->{'DistVersion'} . '.conf');
		$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/proftpd/working/proftpd.conf';
		$tpl = NULL;
		unset($tpl);

		if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0600 )){
			return 'Error: Failed to write ' . $confFile;
		}

		// Installing the new file
		exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/proftpd/working/proftpd.conf '.DaemonConfig::$cfg->{'FTPD_CONF_FILE'}, $result, $error);

		$tpl_param = array(
			'DATABASE_NAME'		=> $xml->{'DB_DATABASE'},
			'DATABASE_HOST'		=> $xml->{'DB_HOST'},
			'DATABASE_USER'		=> $xml->{'FTP_USER'},
			'DATABASE_PASS'		=> DB::decrypt_data($xml->{'FTP_PASSWORD'}),
			'FTPD_MIN_UID'		=> DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'},
			'FTPD_MIN_GID'		=> DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}
		);

		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch('proftpd/parts/sql.conf');
		$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/proftpd/working/sql.conf';
		$tpl = NULL;
		unset($tpl);

		if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0600 )){
			return 'Error: Failed to write ' . $confFile;
		}

		// Installing the new file
		exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/proftpd/working/sql.conf '.DaemonConfig::$cfg->FTPD_SQL_CONF_FILE, $result, $error);

		if (file_exists(DaemonConfig::$cfg->{'CONF_DIR'} . '/proftpd/parts/modules_' . DaemonConfig::$cfg->{'DistVersion'} . '.conf')) {
			exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/proftpd/parts/modules_' . DaemonConfig::$cfg->{'DistVersion'} . '.conf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/proftpd/working/modules.conf', $result, $error);
			DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'} . '/proftpd/working/modules.conf', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
			exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'}.'/proftpd/working/modules.conf ' . DaemonConfig::$cfg->FTPD_MODULES_CONF_FILE, $result, $error);

		}

		$tpl_param = array(
			'APACHE_WWW_DIR'	=> DaemonConfig::$cfg->{'APACHE_WWW_DIR'}
		);

		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch('proftpd/parts/master.conf');
		$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/proftpd/working/' . DaemonConfig::$cfg->{'SERVER_HOSTNAME'} . '.conf';
		$tpl = NULL;
		unset($tpl);

		if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0600 )){
			return 'Error: Failed to write '.$confFile;
		}

		// Installing the new file
		exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/proftpd/working/' . DaemonConfig::$cfg->{'SERVER_HOSTNAME'} . '.conf ' . DaemonConfig::$cfg->{'FTPD_CONF_DIR'} . '/' . DaemonConfig::$cfg->{'SERVER_HOSTNAME'} . '.conf', $result, $error);

		System_Daemon::debug('Finished "DaemonConfigFTP::SaveProFTPdConfig" subprocess.');

		return true;
	}
}
?>