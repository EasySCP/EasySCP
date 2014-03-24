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

class DaemonCore extends DaemonCoreCommon {
	/**
	 * Handles DaemonCore requests.
	 *
	 * @param string $Input
	 * @return boolean
	 */
	public static function Start($Input) {
		System_Daemon::debug('Starting "DaemonCore::Start" subprocess.');

		$retVal = null;
		$Input = explode(" ", $Input, 2);
		switch ($Input[0]) {
			case 'checkAll':
				$retVal = self::checkAllData();
				break;
			case 'Restart':
				System_Daemon::info('Running Restart subprocess.');
				SocketHandler::Close();
				System_Daemon::restart();
				break;
			case 'SaveConfig':
				System_Daemon::debug('Running SaveConfig subprocess.');

				if (isset($Input[1]) && is_array($Input[1])) {
					foreach(json_decode(trim($Input[1])) as $name => $wert){
						if (isset(DaemonConfig::$cfg->$name)){
							DaemonConfig::$cfg->$name = $wert;
						}
					}
				}

				DaemonConfig::Save();
				DaemonConfig::SaveOldConfig();

				System_Daemon::debug('Finished SaveConfig subprocess.');

				$retVal = true;
				break;
			case 'Setup':
				if ( file_exists(dirname(__FILE__). '/DaemonCoreSetup.php'))
				{
					require_once(dirname(__FILE__) . '/DaemonCoreSetup.php');
					if(isset($Input[1]) && $Input[1] != ''){
						$retVal = Setup($Input[1]);
					}
				} else {
					$retVal = false;
				}
				break;
			default:
				System_Daemon::warning("Don't know what to do with " . $Input[0]);
				$retVal = false;
				break;
		}

		System_Daemon::debug('Finished "DaemonCore::Start" subprocess.');

		return $retVal;
	}

	/**
	 * Checks all data eg. Domain, Mail.
	 *
	 * @return boolean
	 */
	private static function checkAllData() {
		System_Daemon::debug('Starting "checkAllData" subprocess.');

		$retVal = self::checkAllDataDomain();
		if($retVal !== true){
			$msg = 'Checking of domains failed';
			System_Daemon::debug($msg);
			return $msg.'<br />'.$retVal;
		}

		$retVal = self::checkAllDataAlias();
		if($retVal !== true){
			$msg = 'Checking of alias failed';
			System_Daemon::debug($msg);
			return $msg.'<br />'.$retVal;
		}

		$retVal = self::checkAllDataMail();
		if($retVal !== true){
			$msg = 'Checking of mail accounts failed';
			System_Daemon::debug($msg);
			return $msg.'<br />'.$retVal;
		}

		$retVal = self::checkAllDataHTAccess();
		if($retVal !== true){
			$msg = 'Checking of htaccess related data failed';
			System_Daemon::debug($msg);
			return $msg.'<br />'.$retVal;
		}

		// Fake aufruf um den Apache neu zu starten
		$retVal = DaemonDomain::Start('reload', true);
		if($retVal !== true){
			return $retVal;
		}

		System_Daemon::debug('Finished "checkAllData" subprocess.');

		return $retVal;
	}
}
?>