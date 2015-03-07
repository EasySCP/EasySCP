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

class DaemonConfigDNS {

	/**
	 * @return mixed
	 */
	public static function SavePDNSConfig(){
		System_Daemon::debug('Starting "DaemonConfigDNS::SavePDNSConfig" subprocess.');

		$xml = simplexml_load_file(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_DNS.xml');

		$tpl_param = array(
			'PDNS_USER'	=> $xml->{'PDNS_USER'},
			'PDNS_PASS'	=> DB::decrypt_data($xml->{'PDNS_PASS'}),
			'HOSTNAME'	=> $xml->{'HOSTNAME'}
		);

		switch(DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'}){
			case 'CentOS_6':
				$tpl = DaemonCommon::getTemplate($tpl_param);
				$config = $tpl->fetch('pdns/parts/pdns.conf');
				$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.conf';
				$tpl = NULL;
				unset($tpl);

				if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640 )){
					return 'Error: Failed to write ' . $confFile;
				}

				break;
			case 'Debian_6':
				exec(DaemonConfig::$cmd->{'CMD_CP'}.' -f '.DaemonConfig::$cfg->{'CONF_DIR'}.'/pdns/parts/pdns.conf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/pdns/working/pdns.conf', $result, $error);

				$tpl = DaemonCommon::getTemplate($tpl_param);
				$config = $tpl->fetch('pdns/parts/pdns.mysql');
				$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/pdns/working/pdns.mysql';
				$tpl = NULL;
				unset($tpl);

				if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640 )){
					return 'Error: Failed to write '.$confFile;
				}

				// Installing the new file
				exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/pdns/working/pdns.mysql '.DaemonConfig::$cfg->{'PDNS_DB_DIR'}.'/pdns.mysql', $result, $error);
				break;
			case 'Debian_8':
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/parts/pdns_8.conf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.conf', $result, $error);

				$tpl = DaemonCommon::getTemplate($tpl_param);
				$config = $tpl->fetch('pdns/parts/pdns.local.gmysql.conf');
				$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.local.gmysql.conf';
				$tpl = NULL;
				unset($tpl);

				if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640 )){
					return 'Error: Failed to write ' . $confFile;
				}

				// Installing the new file
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.local.gmysql.conf ' . DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/pdns.local.gmysql.conf', $result, $error);

				if(file_exists(DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/../bindbackend.conf')) {
					unlink(DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/../bindbackend.conf');
				}

				break;
			case 'Ubuntu_14.04':
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/parts/pdns_14.04.conf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.conf', $result, $error);

				$tpl = DaemonCommon::getTemplate($tpl_param);
				$config = $tpl->fetch('pdns/parts/pdns.local.gmysql.conf');
				$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.local.gmysql.conf';
				$tpl = NULL;
				unset($tpl);

				if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640 )){
					return 'Error: Failed to write ' . $confFile;
				}

				// Installing the new file
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.local.gmysql.conf ' . DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/pdns.local.gmysql.conf', $result, $error);

				if(file_exists(DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/../bindbackend.conf')) {
					unlink(DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/../bindbackend.conf');
				}

				if(file_exists(DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/pdns.simplebind.conf')) {
					unlink(DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/pdns.simplebind.conf');
				}
				break;
			default:
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -f ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/parts/pdns.conf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.conf', $result, $error);

				$tpl = DaemonCommon::getTemplate($tpl_param);
				$config = $tpl->fetch('pdns/parts/pdns.local.gmysql');
				$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.local.gmysql';
				$tpl = NULL;
				unset($tpl);

				if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640 )){
					return 'Error: Failed to write ' . $confFile;
				}

				// Installing the new file
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.local.gmysql ' . DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/pdns.local.gmysql', $result, $error);

				if(file_exists(DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/../bindbackend.conf')) {
					unlink(DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/../bindbackend.conf');
				}
				if(file_exists(DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/pdns.simplebind')) {
					unlink(DaemonConfig::$cfg->{'PDNS_DB_DIR'} . '/pdns.simplebind');
				}

		}

		// Backup current pdns.conf if exists
		if (file_exists(DaemonConfig::$cfg->{'PDNS_CONF_FILE'})){
			exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'PDNS_CONF_FILE'} . ' ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/backup/pdns.conf' . '_' . date("Y_m_d_H_i_s"), $result, $error);
		}

		DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.conf', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640);
		exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pdns/working/pdns.conf ' . DaemonConfig::$cfg->{'PDNS_CONF_FILE'}, $result, $error);

		System_Daemon::debug('Finished "DaemonConfigDNS::SavePDNSConfig" subprocess.');

		return true;
	}
}
?>