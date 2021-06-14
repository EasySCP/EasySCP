<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2020 by Easy Server Control Panel - http://www.easyscp.net
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

class DaemonConfigDNS {

	/**
	 * @return mixed
	 */
	public static function CreatePDNSPass(){
		System_Daemon::debug('Starting "DaemonConfigDNS::createPDNSPass" subprocess.');

		$xml = DaemonCommon::xml_load_file(DaemonConfig::$cfg->{'CONF_DIR'} . '/tpl/EasySCP_Config_DNS.xml');

		System_Daemon::debug('Building the new pdns config file');

		$xml->{'PDNS_USER'} = 'powerdns';
		$xml->{'PDNS_PASS'} = DB::encrypt_data(DaemonCommon::generatePassword(18));
		$xml->{'HOSTNAME'} = idn_to_ascii(DaemonConfig::$cfg->{'DATABASE_HOST'},IDNA_NONTRANSITIONAL_TO_ASCII,INTL_IDNA_VARIANT_UTS46);

		$handle = fopen(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_DNS.xml', "wb");
		fwrite($handle, $xml->asXML());
		fclose($handle);

		DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_DNS.xml', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640);

		// Create/Update Powerdns control user account if needed

		System_Daemon::debug('Adding the PowerDNS control user');

		$sql_param = array(
			':PDNS_USER'=> $xml->{'PDNS_USER'},
			':PDNS_PASS'=> DB::decrypt_data($xml->{'PDNS_PASS'}),
			':HOSTNAME'	=> $xml->{'HOSTNAME'}
		);
		$sql_query = "
			GRANT ALL PRIVILEGES ON powerdns.* TO :PDNS_USER@:HOSTNAME IDENTIFIED BY :PDNS_PASS;
			FLUSH PRIVILEGES;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		$sql_param = array(
			':DATABASE_USER'=> DaemonConfig::$cfg->DATABASE_USER,
			':DATABASE_HOST'=> idn_to_ascii(DaemonConfig::$cfg->{'DATABASE_HOST'},IDNA_NONTRANSITIONAL_TO_ASCII,INTL_IDNA_VARIANT_UTS46)
		);

		$sql_query = "
			GRANT ALL PRIVILEGES ON powerdns.* TO :DATABASE_USER@:DATABASE_HOST;
			FLUSH PRIVILEGES;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		System_Daemon::debug('Finished "DaemonConfigDNS::createPDNSPass" subprocess.');

		return true;
	}

	/**
	 * @return mixed
	 */
	public static function SavePDNSConfig(){
		System_Daemon::debug('Starting "DaemonConfigDNS::SavePDNSConfig" subprocess.');

		if (!file_exists(DaemonConfig::$distro->{'PDNS_DB_DIR'}.'/')){
			DaemonCommon::systemCreateDirectory(DaemonConfig::$distro->{'PDNS_DB_DIR'}.'/', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640);
		}

		$xml = DaemonCommon::xml_load_file(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_DNS.xml');

		$tpl_param = array(
			'PDNS_USER'	=> $xml->{'PDNS_USER'},
			'PDNS_PASS'	=> DB::decrypt_data($xml->{'PDNS_PASS'}),
			'HOSTNAME'	=> $xml->{'HOSTNAME'}
		);

		switch(DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'}){
			case 'CentOS_7':
				$tpl = DaemonCommon::getTemplate($tpl_param);
				$config = $tpl->fetch('pdns/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'} . '/pdns.conf');
				$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.conf';
				$tpl = NULL;
				unset($tpl);

				if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640 )){
					return 'Error: Failed to write ' . $confFile;
				}

				break;
			case 'Debian_7':
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'} . '/pdns.conf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.conf', $result, $error);

				$tpl = DaemonCommon::getTemplate($tpl_param);
				$config = $tpl->fetch('pdns/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'} . '/pdns.local.gmysql');
				$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.local.gmysql';
				$tpl = NULL;
				unset($tpl);

				if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640 )){
					return 'Error: Failed to write ' . $confFile;
				}

				// Installing the new file
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.local.gmysql ' . DaemonConfig::$distro->{'PDNS_DB_DIR'} . '/pdns.local.gmysql', $result, $error);

				if(file_exists(DaemonConfig::$distro->{'PDNS_DB_DIR'} . '/../bindbackend.conf')) {
					unlink(DaemonConfig::$distro->{'PDNS_DB_DIR'} . '/../bindbackend.conf');
				}

				if(file_exists(DaemonConfig::$distro->{'PDNS_DB_DIR'} . '/pdns.simplebind')) {
					unlink(DaemonConfig::$distro->{'PDNS_DB_DIR'} . '/pdns.simplebind');
				}

				break;
			default:
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'} . '/pdns.conf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.conf', $result, $error);

				$tpl = DaemonCommon::getTemplate($tpl_param);
				$config = $tpl->fetch('pdns/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'} . '/pdns.local.gmysql.conf');
				$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.local.gmysql.conf';
				$tpl = NULL;
				unset($tpl);

				if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640 )){
					return 'Error: Failed to write ' . $confFile;
				}

				// Installing the new file
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.local.gmysql.conf ' . DaemonConfig::$distro->{'PDNS_DB_DIR'} . '/pdns.local.gmysql.conf', $result, $error);

				if(file_exists(DaemonConfig::$distro->{'PDNS_DB_DIR'} . '/../bindbackend.conf')) {
					unlink(DaemonConfig::$distro->{'PDNS_DB_DIR'} . '/../bindbackend.conf');
				}

				if(file_exists(DaemonConfig::$distro->{'PDNS_DB_DIR'} . '/pdns.simplebind.conf')) {
					unlink(DaemonConfig::$distro->{'PDNS_DB_DIR'} . '/pdns.simplebind.conf');
				}
		}

		// Backup current pdns.conf if exists
		if (file_exists(DaemonConfig::$distro->{'PDNS_CONF_FILE'})){
			exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$distro->{'PDNS_CONF_FILE'} . ' ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/backup/pdns.conf' . '_' . date("Y_m_d_H_i_s"), $result, $error);
		}

		DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.conf', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640);
		exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.conf ' . DaemonConfig::$distro->{'PDNS_CONF_FILE'}, $result, $error);

		System_Daemon::debug('Finished "DaemonConfigDNS::SavePDNSConfig" subprocess.');

		return true;
	}
}
?>