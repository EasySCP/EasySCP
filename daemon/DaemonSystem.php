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
 * EasySCP Daemon system functions
 */
class DaemonSystem extends DaemonSystemCommon {
	/**
	 * Handles DaemonSystem requests
	 *
	 * @param string $Input
	 * @return mixed
	 */
	public static function Start($Input) {
		System_Daemon::debug('Starting "DaemonSystem::Start" subprocess.');

		$data = explode(" ", $Input);
		switch ($data[0]) {
			case 'cron':
				System_Daemon::debug('Starting "DaemonSystem::cron" subprocess.');
				$retVal = self::handleCronjobsForAllUsers();
				if ($retVal!==true){
					System_Daemon::warning('Failed to handle Cronjobs');
					System_Daemon::debug('Finished "cron" subprocess.');
					return false;
				}
				System_Daemon::debug('Finished "DaemonSystem::cron" subprocess.');
				break;
			case 'direxists':
				System_Daemon::debug('Starting "DaemonSystem::direxists" subprocess.');
				if (is_dir($data[1])){
					System_Daemon::debug('Directory '.$data[1].' exists');
					System_Daemon::debug('Finished "DaemonSystem::direxists" subprocess.');
					return true;
				} else {
					System_Daemon::debug('Directory '.$data[1].' does not exist');
					System_Daemon::debug('Finished "DaemonSystem::direxists" subprocess.');
					return false;
				}
				break;
			case 'fileexists':
				System_Daemon::debug('Starting "DaemonSystem::fileexists" subprocess.');
				if (is_file($data[1])){
					System_Daemon::debug('File '.$data[1].' exists');
					System_Daemon::debug('Finished "DaemonSystem::fileexists" subprocess.');
					return true;
				} else {
					System_Daemon::debug('File '.$data[1].' does not exist');
					System_Daemon::debug('Finished "DaemonSystem::fileexists" subprocess.');
					return false;
				}
				break;
			case 'isexecutable':
				System_Daemon::debug('Starting "DaemonSystem::isexecutable" subprocess.');
				$internalCMD = explode(',', DaemonConfig::$cmd->SHELL_INTERNAL_CMDS);
				System_Daemon::debug(DaemonConfig::$cmd->SHELL_INTERNAL_CMDS);
				if (in_array($data[1], $internalCMD)){
					System_Daemon::debug('Command '.$data[1].' is an internal command');
					System_Daemon::debug('Finished "DaemonSystem::isexecutable" subprocess.');
					return true;
				}
				if(self::Start('fileexists '.$data[1])){
					if (is_executable($data[1])){
						System_Daemon::debug('File '.$data[1].' is executable');
						System_Daemon::debug('Finished "DaemonSystem::isexecutable" subprocess.');
						return true;
					} else {
						System_Daemon::debug('File '.$data[1].' is not executable');
						System_Daemon::debug('Finished "DaemonSystem::isexecutable" subprocess.');
						return false;
					}
				} else {
					System_Daemon::debug('Finished "DaemonSystem::isexecutable" subprocess.');
					return false;
				}
				break;
			case 'rebuildConfig':
				System_Daemon::debug('Starting "DaemonSystem::rebuildConfig" subprocess.');
				$rebuildConfig = DaemonConfigCommon::rebuildConfig($data[1]);
				if ($rebuildConfig !== true){
					return $rebuildConfig;
				}
				System_Daemon::debug('Finished "DaemonSystem::rebuildConfig" subprocess.');
				break;
			case 'setDebugMode':
				System_Daemon::debug('Starting "DaemonSystem::setDebugMode" subprocess.');
				if (isset($data[2]) && $data[2] != ''){
					DaemonCommon::systemSetDebugMode($data[1], $data[2]);
				} else {
					DaemonCommon::systemSetDebugMode($data[1]);
				}
				System_Daemon::debug('Finished "DaemonSystem::setDebugMode" subprocess.');
				break;
			case 'setPermissions':
				System_Daemon::debug('Starting "DaemonSystem::setPermissions" subprocess.');
				DaemonCommon::systemSetSystemPermissions();
				DaemonCommon::systemSetGUIPermissions();
				System_Daemon::debug('Finished "DaemonSystem::setPermissions" subprocess.');
				break;
			case 'updateIana':
				System_Daemon::debug('Starting "DaemonSystem::updateIana" subprocess.');
				self::updateIanaXML();
				DaemonCommon::systemSetFolderPermissions(EasyConfig_PATH . 'Iana_TLD.xml', 'root', 'root', '644');
				System_Daemon::debug('Finished "DaemonSystem::updateIana" subprocess.');
				break;
			case 'updateSystem':
				System_Daemon::debug('Starting "DaemonSystem::updateSystem" subprocess.');
				if (self::verifySystemUpdate()){
					self::handleSystemUpdate();
				}
				System_Daemon::debug('Finished "DaemonSystem::updateSystem" subprocess.');
				break;
			case 'userexists':
				System_Daemon::debug('Starting "DaemonSystem::userexists" subprocess.');
                exec(DaemonConfig::$cmd->CMD_ID.' -u '.$data['1'].' 2>&1', $result, $error);
                if ($error != 0){
                    System_Daemon::debug('User '.$data['1'].' does not exist');
                    unset($result);
                    return false;
                }
				System_Daemon::debug('Finished "DaemonSystem::userexists" subprocess.');
                break;
			default:
				System_Daemon::warning("Don't know what to do with ".$data[0]);
				return false;
		}

		System_Daemon::debug('Finished "DaemonSystem::Start" subprocess.');

		return true;
	}
}
?>
