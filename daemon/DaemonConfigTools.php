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

class DaemonConfigTools {

	public static function SavePMAConfig(){
		System_Daemon::debug('Starting "DaemonConfigTools::SavePMAConfig" subprocess.');

		$xml = DaemonCommon::xml_load_file(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_PMA.xml');

		// Backup current phpMyAdmin conf if exists
		if (file_exists(DaemonConfig::$cfg->{'GUI_ROOT_DIR'} . '/tools/pma/config.inc.php')){
			exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'GUI_ROOT_DIR'} . '/tools/pma/config.inc.php ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pma/backup/config.inc.php' . '_' . date("Y_m_d_H_i_s"), $result, $error);
		}

		$tpl_param = array(
			'BLOWFISH'	=> $xml->{'PMA_BLOWFISH'},
			'PMA_USER'	=> $xml->{'PMA_USER'},
			'PMA_PASS'	=> DB::decrypt_data($xml->{'PMA_PASSWORD'}),
			'HOSTNAME'	=> $xml->{'DATABASE_HOST'},
			'TMP_DIR'	=> $xml->{'TMP_DIR'}
		);

		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch('pma/parts/config.inc.tpl');
		$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/pma/working/config.inc.php';
		$tpl = NULL;
		unset($tpl);

		if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640 )){
			System_Daemon::debug('Error: Failed to write ' . $confFile);
			return;
		}

		System_Daemon::debug('Storing the pma config file in the working directory');

		exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -f ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/pma/working/config.inc.php '.DaemonConfig::$cfg->{'GUI_ROOT_DIR'} . '/tools/pma', $result, $error);

		DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'GUI_ROOT_DIR'} . '/tools/pma/config.inc.php', DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'} . DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'} . DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, 0644);

		System_Daemon::debug('Finished "DaemonConfigTools::SavePMAConfig" subprocess.');
	}

	public static function SaveRCConfig(){
		System_Daemon::debug('Starting "DaemonConfigTools::SaveRCConfig" subprocess.');

		$xml = DaemonCommon::xml_load_file(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_RC.xml');

		// Backup current Roundcube conf if exists
		if (file_exists(DaemonConfig::$cfg->{'GUI_ROOT_DIR'} . '/tools/webmail/config/config.inc.php')){
			exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'GUI_ROOT_DIR'} . '/tools/webmail/config/config.inc.php ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/roundcube/backup/config.inc.php' . '_' . date("Y_m_d_H_i_s"), $result, $error);
		}

		$tpl_param = array(
			'CUBE_USER'	=> $xml->{'CUBE_USER'},
			'CUBE_PASS'	=> DB::decrypt_data($xml->{'CUBE_PASS'}),
			'HOSTNAME'	=> $xml->{'DATABASE_HOST'}
		);

		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch('roundcube/parts/config.inc.tpl');
		$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/roundcube/working/config.inc.php';
		$tpl = NULL;
		unset($tpl);

		if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640 )){
			System_Daemon::debug('Error: Failed to write ' . $confFile);
			return;
		}

		System_Daemon::debug('Storing the roundcube config files in the working directory');

		exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/roundcube/working/config.inc.php ' . DaemonConfig::$cfg->{'GUI_ROOT_DIR'} . '/tools/webmail/config', $result, $error);

		DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'GUI_ROOT_DIR'} . '/tools/webmail/config/config.inc.php', DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'} . DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'} . DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, 0644);

		System_Daemon::debug('Finished "DaemonConfigTools::SaveRCConfig" subprocess.');
	}
}
?>