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
 * EasySCP Daemon system functions
 */
class DaemonSystem {
	public static function Start($Input) {
		$data = explode(" ", $Input);
		switch ($data[0]) {
			case 'direxists':
				if (is_dir($data[1])){
					System_Daemon::debug('Directory '.$data[1].' exists');
					return true;
				} else {
					System_Daemon::debug('Directory '.$data[1].' does not exist');
					return false;
				}
				break;
			case 'fileexists':
				if (is_file($data[1])){
					System_Daemon::debug('File '.$data[1].' exists');
					return true;
				} else {
					System_Daemon::debug('File '.$data[1].' does not exist');
					return false;
				}
				break;
			case 'isexecutable':
				if(self::Start('fileexists '.$data[1])){
					if (is_executable($data[1])){
						System_Daemon::debug('File '.$data[1].' is executable');
						return true;
					} else {
						System_Daemon::debug('File '.$data[1].' is not executable');
						return false;
					}
				} else {
					return false;
				}
				break;
			case 'userexists':
				exec(DaemonConfig::$cmd->CMD_ID.' -u '.$data['1'].' 2>&1', $result, $error);
				if ($error != 0){
					System_Daemon::debug('User '.$data['1'].' does not exist');
					unset($result);
					return false;
				}
				break;
			case 'cron':
				System_Daemon::debug('DaemonSystem Cronjob called ');
				$retVal = self::handleCronjob($data[1]);
				if ($retVal!==true){
					System_Daemon::warning('Failed to handle Cronjob for '.$data[1]);
					return false;
				}
				break;
			default:
				System_Daemon::warning("Don't know what to do with ".$data[0]);
				return false;
		}
		return true;
	}
	protected static function handleCronjob($userID){
		$sql_param = array(
			':user_id'	=> $userID,
			':active'	=> 'yes'
		);
		$sql_query = "
			SELECT
				*
			FROM
				cronjobs
			WHERE 
				active = :active
			AND
				user_id = :user_id
		";
		DB::prepare($sql_query);
		$cronData = DB::execute($sql_param);

		$sql_param = array(
			':user_id'	=> $userID
		);
		$sql_query = "
			SELECT 
				admin_name
			FROM
				admin
			WHERE
				admin_id = :user_id
		";
		DB::prepare($sql_query);
		$adminData = DB::execute($sql_param,true);

		$tpl_param = array('ADMIN'=>$adminData['admin_name']);
		$tpl = DaemonCommon::getTemplate($tpl_param);

		while ($cronJob = $cronData->fetch()){
			$tpl->append(
				array(
					'MINUTE'	=> $cronJob['minute'],
					'HOUR'		=> $cronJob['hour'],
					'DOM'		=> $cronJob['dayofmonth'],
					'MONTH'		=> $cronJob['month'],
					'DOW'		=> $cronJob['dayofweek'],
					'USER'		=> $cronJob['user'],
					'COMMAND'	=> $cronJob['command'],
				)
			);
		}
		// write Apache config
		$config = $tpl->fetch("tpl/cron.tpl");
		$confFile = DaemonConfig::$cfg->CRON_DIR . '/' . $adminData['admin_name'];
		System_Daemon::debug($confFile);
		$retVal = DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->CRON_GROUP, 0600, false);

		if ($retVal !== true) {
			$msg = 'Failed to write'. $confFile;
			System_Daemon::warning($msg);
			return $msg.'<br />'.$retVal;
		} else {
			System_Daemon::debug($confFile.' successfully written!');
		}
		
		$sql_param = array(
			':user_id'	=> $userID,
			':status'	=> 'ok'
		);
		$sql_query = "
			UPDATE
				cronjobs
			SET
				status = :status
			WHERE
				user_id = :user_id
			AND
				status != :status
		";
		DB::prepare($sql_query);
		$cronData = DB::execute($sql_param)->closeCursor();
		System_Daemon::debug('handleCronjob successfully ended!');
		return true;
	}
}
?>
