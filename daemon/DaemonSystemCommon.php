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

class DaemonSystemCommon {
	/**
	 * @param string $config
	 * @return mixed
	 */
	public static function rebuildConfig($config = 'system'){
		System_Daemon::debug('Starting "DaemonSystemCommon::rebuildConfig" subprocess.');

		switch ($config) {
			case 'DNS':
				System_Daemon::debug('Starting "DNS" subprocess.');

				$SavePDNSConfig = DaemonConfigDNS::SavePDNSConfig();
				if ($SavePDNSConfig !== true){
					return $SavePDNSConfig;
				}

				System_Daemon::debug('Finished "DNS" subprocess.');

				break;
			case 'FTP':
				System_Daemon::debug('Starting "FTP" subprocess.');

				$SaveProFTPdConfig = DaemonConfigFTP::SaveProFTPdConfig();
				if ($SaveProFTPdConfig !== true){
					return $SaveProFTPdConfig;
				}

				System_Daemon::debug('Finished "FTP" subprocess.');

				break;
			case 'MTA':
				System_Daemon::debug('Starting "MTA" subprocess.');

				$SaveMTAConfig = DaemonConfigMail::SaveMTAConfig();
				if ($SaveMTAConfig !== true){
					return $SaveMTAConfig;
				}

				System_Daemon::debug('Finished "MTA" subprocess.');

				break;
			case 'PMA':
				System_Daemon::debug('Starting "PMA" subprocess.');

				DaemonConfigTools::SavePMAConfig();

				System_Daemon::debug('Finished "PMA" subprocess.');

				break;
			case 'RC':
				System_Daemon::debug('Starting "RC" subprocess.');

				DaemonConfigTools::SaveRCConfig();

				System_Daemon::debug('Finished "RC" subprocess.');

				break;
			case 'system':
				System_Daemon::debug('Starting "system" subprocess.');

				$SavePDNSConfig = DaemonConfigDNS::SavePDNSConfig();
				if ($SavePDNSConfig !== true){
					return $SavePDNSConfig;
				}

				$SaveProFTPdConfig = DaemonConfigFTP::SaveProFTPdConfig();
				if ($SaveProFTPdConfig !== true){
					return $SaveProFTPdConfig;
				}

				$SaveMTAConfig = DaemonConfigMail::SaveMTAConfig();
				if ($SaveMTAConfig !== true){
					return $SaveMTAConfig;
				}

				System_Daemon::debug('Finished "system" subprocess.');

				break;
			default:
				System_Daemon::warning("Don't know what to do with ".$config);
				return false;
		}

		System_Daemon::debug('Finished "DaemonSystemCommon::rebuildConfig" subprocess.');

		return true;
	}
}
?>