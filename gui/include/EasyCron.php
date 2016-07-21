<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link        http://www.easyscp.net
 * @author        EasySCP Team
 */

/**
 * EasySCP Cronjob functions
 */
class EasyCron
{
	/**
	 * Generate query and select all cronjobs for users
	 * @param $adminType
	 * @param $userID
	 * @return mixed
	 * @throws Exception
	 */
	public static function getCronjobs($adminType, $userID)
	{
		$sql_query = "
		SELECT
			admin_name, c.*
		FROM
			cronjobs c,
			admin
		WHERE
			c.user_id = admin_id
	";
		switch ($adminType) {
			case 'admin':
				break;
			case 'reseller':
				$sql_query .= "
				AND (c.user_id IN (
					SELECT
						admin_id
					FROM
						admin
					WHERE
						created_by = :user_id
					)
					OR
						c.user_id = :user_id
					)
			";
				break;
			default:
				$sql_query .= "
				AND c.user_id = :user_id
			";
				break;
		}
		$sql_query .= "
		ORDER BY
			admin_name, name";

		$sql_param = array(
			':user_id' => $userID,
		);

		DB::prepare($sql_query);
		$rs = DB::execute($sql_param);

		return $rs;
	}

	/**
	 * Get cronjob from DB using ID
	 * @param $cron_id
	 * @return mixed
	 */
	public static function getCronJobByID($cron_id)
	{
		$sql_param = array(
			':cron_id' => $cron_id
		);
		$sql_query = "
		SELECT
			*
		FROM
			cronjobs
		WHERE
			id = :cron_id;
	";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param);
		return $rs;
	}

	/**
	 * Delete cronjob by given ID and run daemon
	 * @param  $cron_id
	 * @return boolean
	 */
	public static function deleteCronJob($cron_id)
	{
		$rs = self::getCronJobByID($cron_id);
		$cronData = $rs->fetch();

		$sql_param = array(
			':cron_id' => $cron_id
		);

		if ($cronData['user_id'] == $_SESSION['user_id']) {
			$sql_query = "
			DELETE FROM
				cronjobs
			WHERE
				id = :cron_id
		";
			DB::prepare($sql_query);
			DB::execute($sql_param)->closeCursor();
		} else {
			set_page_message(tr('Cronjobs of other users cannot be deleted by you!'), 'error');
			return false;
		}
		if (send_request('160 SYSTEM cron')) {
			set_page_message(tr('Successfully deleted cronjob!'), 'success');
			return true;
		} else {
			set_page_message(tr('Deletion of cronjob failed!'), 'warning');
			return false;
		}
	}

	/**
	 * Toggle activation status of a cronjob and run daemon
	 * @param $cron_id
	 * @return boolean
	 */
	public static function toggleCronStatus($cron_id)
	{
		$rs = self::getCronJobByID($cron_id);
		$cronData = $rs->fetch();

		if ($cronData['user_id'] == $_SESSION['user_id']) {
			$sql_param = array(
				':cron_id' => $cron_id,
				':active' => $cronData['active'] == 'yes' ? 'no' : 'yes'
			);
			$sql_query = "
			UPDATE
				cronjobs
			SET
				active = :active
			WHERE
				id = :cron_id
		";
			DB::prepare($sql_query);
			DB::execute($sql_param)->closeCursor();
		} else {
			set_page_message(tr('Cronjobs of other users cannot be modified by you!'), 'error');
			return false;
		}
		if (send_request('160 SYSTEM cron')) {
			set_page_message(tr('Successfully changed cronjob!'), 'success');
			return true;
		} else {
			set_page_message(tr('Changing cronjob failed!'), 'warning');
			return false;
		}
	}

	/**
	 * Store cronjob information to database and run daemon
	 * @return boolean
	 */
	public static function addCronJob()
	{
		// determine if all commands separated by ; are executable
		$cronCmds = explode(";", filter_input(INPUT_POST, 'cron_cmd'));

		$cronIsExecutable = true;
		foreach ($cronCmds as $command) {
			$commandSplitted = explode(" ", trim($command));
			if (!send_request('160 SYSTEM isexecutable ' . $commandSplitted[0])) {
				set_page_message($commandSplitted[0] . tr(' Command is not executable!'), 'warning');
				$cronIsExecutable = false;
			}
		}
		if (!$cronIsExecutable) {
			return false;
		}
		$userName = $_POST['user_name'];
		if (!send_request('160 SYSTEM userexists ' . $userName[0])) {
			set_page_message(tr('The user %s does not exist', $userName[0]), 'warning');
			return false;
		}
		if ($_POST['expert_mode'] > 0) {
			if ($_POST['minute'][0] == '*' &&
				$_POST['hour'][0] == '*' &&
				$_POST['day_of_month'][0] == '*' &&
				$_POST['month'][0] == '*' &&
				$_POST['day_of_week'][0] == '*'
			) {
				set_page_message(tr('At least one argument must differ from *'), 'warning');
				return false;
			}

			if ($_POST['minute'][0] == '*' && isset($_POST['minute'][1])) {
				set_page_message(tr('You cannot choose * and another value for minutes'), 'warning');
				return false;
			}
			if ($_POST['hour'][0] == '*' && isset($_POST['hour'][1])) {
				set_page_message(tr('You cannot choose * and another value for hours'), 'warning');
				return false;
			}
			if ($_POST['day_of_month'][0] == '*' && isset($_POST['day_of_month'][1])) {
				set_page_message(tr('You cannot choose * and another value for days of month'), 'warning');
				return false;
			}
			if ($_POST['month'][0] == '*' && isset($_POST['month'][1])) {
				set_page_message(tr('You cannot choose * and another value for months'), 'warning');
				return false;
			}
			if ($_POST['day_of_week'][0] == '*' && isset($_POST['day_of_week'][1])) {
				set_page_message(tr('You cannot choose * and another value for weekdays'), 'warning');
				return false;
			}

			$minuteString = '';
			foreach ($_POST['minute'] as $key => $minute) {
				if ($key > 0) {
					$minuteString .= ',';
				}
				$minuteString .= $minute;
			}
			$hourString = '';
			foreach ($_POST['hour'] as $key => $hour) {
				if ($key > 0) {
					$hourString .= ',';
				}
				$hourString .= $hour;
			}
			$domString = '';
			foreach ($_POST['day_of_month'] as $key => $day) {
				if ($key > 0) {
					$domString .= ',';
				}
				$domString .= $day;
			}
			$monthString = '';
			foreach ($_POST['month'] as $key => $month) {
				if ($key > 0) {
					$monthString .= ',';
				}
				$monthString .= $month;
			}
			$dowString = '';
			foreach ($_POST['day_of_week'] as $key => $day) {
				if ($key > 0) {
					$dowString .= ',';
				}
				$dowString .= $day;
			}
			$schedule = $minuteString . " " . $hourString . " " . $domString . " " . $monthString . " " . $dowString;
		} else {
			$schedule = $_POST['schedule'][0];
		}
		$sql_param = array(
			':id' => $_POST['cron_id'],
			':user_id' => $_SESSION['user_id'],
			':schedule' => $schedule,
			':command' => $_POST['cron_cmd'],
			':active' => $_POST['active'],
			':description' => $_POST['description'],
			':name' => $_POST['name'],
			':user' => $userName[0]);
		$sql_query = "
		INSERT INTO cronjobs (
			id,
			user_id,
			schedule,
			command,
			active,
			description,
			name,
			user
		) VALUES (
			:id,
			:user_id,
			:schedule,
			:command,
			:active,
			:description,
			:name,
			:user
		) ON DUPLICATE KEY UPDATE
			schedule	= :schedule,
			command		= :command,
			active		= :active,
			description	= :description,
			name		= :name,
			user		= :user
	";

		DB::prepare($sql_query);
		DB::execute($sql_param);
		DB::getInstance()->lastInsertId();
		if (send_request('160 SYSTEM cron')) {
			set_page_message(tr('Cronjob added!'), 'success');
		} else {
			set_page_message(tr('Cronjob addition failed!'), 'warning');
		}
		return true;
	} // End of add_cron_job()

	/**
	 * Generate dropdown select for minutes
	 * @param $tpl
	 * @param $minutes
	 */
	public static function genMinuteSelect($tpl, $minutes)
	{
		$cfg = EasySCP_Registry::get('Config');
		$tpl->append(
			array(
				'MINUTE_VALUE' => '*',
				'MINUTE_TEXT' => tr('Every minute'),
				'MINUTE_SELECTED' => in_array('*', $minutes) ? $cfg->HTML_SELECTED : ''
			));
		$tpl->append(
			array(
				'MINUTE_VALUE' => '*/2',
				'MINUTE_TEXT' => tr('Every other minute'),
				'MINUTE_SELECTED' => in_array('*/2', $minutes) ? $cfg->HTML_SELECTED : ''
			));
		$tpl->append(
			array(
				'MINUTE_VALUE' => '*/5',
				'MINUTE_TEXT' => tr('Every five minutes'),
				'MINUTE_SELECTED' => in_array('*/5', $minutes) ? $cfg->HTML_SELECTED : ''
			));
		$tpl->append(
			array(
				'MINUTE_VALUE' => '*/10',
				'MINUTE_TEXT' => tr('Every ten minutes'),
				'MINUTE_SELECTED' => in_array('*/10', $minutes) ? $cfg->HTML_SELECTED : ''
			));
		$tpl->append(
			array(
				'MINUTE_VALUE' => '*/15',
				'MINUTE_TEXT' => tr('Every fifteen minutes'),
				'MINUTE_SELECTED' => in_array('*/15', $minutes) ? $cfg->HTML_SELECTED : ''
			));

		for ($i = '0'; $i < '60'; $i++) {
			$tpl->append(
				array(
					'MINUTE_VALUE' => $i,
					'MINUTE_TEXT' => $i,
					'MINUTE_SELECTED' => in_array($i, $minutes) ? $cfg->HTML_SELECTED : ''
				));
		}

	}

	/**
	 * Generate dropdown select for hours
	 * @param $tpl
	 * @param $hours
	 */
	public static function genHourSelect($tpl, $hours)
	{
		$cfg = EasySCP_Registry::get('Config');
		$tpl->append(
			array(
				'HOUR_VALUE' => '*',
				'HOUR_TEXT' => tr('Every hour'),
				'HOUR_SELECTED' => in_array('*', $hours) ? $cfg->HTML_SELECTED : ''
			));
		$tpl->append(
			array(
				'HOUR_VALUE' => '*/2',
				'HOUR_TEXT' => tr('Every other hour'),
				'HOUR_SELECTED' => in_array('*/2', $hours) ? $cfg->HTML_SELECTED : ''
			));
		$tpl->append(
			array(
				'HOUR_VALUE' => '*/4',
				'HOUR_TEXT' => tr('Every four hours'),
				'HOUR_SELECTED' => in_array('*/4', $hours) ? $cfg->HTML_SELECTED : ''
			));
		$tpl->append(
			array(
				'HOUR_VALUE' => '*/8',
				'HOUR_TEXT' => tr('Every eight hours'),
				'HOUR_SELECTED' => in_array('*/8', $hours) ? $cfg->HTML_SELECTED : ''
			));
		$tpl->append(
			array(
				'HOUR_VALUE' => '*/12',
				'HOUR_TEXT' => tr('Every twelve hours'),
				'HOUR_SELECTED' => in_array('*/12', $hours) ? $cfg->HTML_SELECTED : ''
			));

		for ($i = '0'; $i < '24'; $i++) {
			$tpl->append(
				array(
					'HOUR_VALUE' => $i,
					'HOUR_TEXT' => $i,
					'HOUR_SELECTED' => in_array($i, $hours) ? $cfg->HTML_SELECTED : ''
				));
		}

	}

	/**
	 * Generate dropdown select for days of week
	 * @param $tpl
	 * @param $weekdays
	 */
	public static function genDayOfWeekSelect($tpl, $weekdays)
	{
		$cfg = EasySCP_Registry::get('Config');
		$day_names = array(
			tr('Sunday'),
			tr('Monday'),
			tr('Tuesday'),
			tr('Wednesday'),
			tr('Thursday'),
			tr('Friday'),
			tr('Saturday'),
		);
		$tpl->append(
			array(
				'DOW_VALUE' => '*',
				'DOW_TEXT' => tr('Every weekday'),
				'DOW_SELECTED' => in_array('*', $weekdays) ? $cfg->HTML_SELECTED : ''
			));

		for ($i = '0'; $i < '7'; $i++) {
			$tpl->append(
				array(
					'DOW_VALUE' => $i,
					'DOW_TEXT' => $day_names[$i],
					'DOW_SELECTED' => in_array($i, $weekdays) ? $cfg->HTML_SELECTED : ''
				));
		}

	}

	/**
	 * Generate dropdown select for months
	 * @param $tpl
	 * @param $months
	 */
	public static function genMonthSelect($tpl, $months)
	{
		$cfg = EasySCP_Registry::get('Config');
		$month_names = array(
			tr('January'),
			tr('February'),
			tr('March'),
			tr('April'),
			tr('May'),
			tr('June'),
			tr('July'),
			tr('August'),
			tr('September'),
			tr('October'),
			tr('November'),
			tr('December'),
		);
		$tpl->append(
			array(
				'MONTH_VALUE' => '*',
				'MONTH_TEXT' => tr('Every month'),
				'MONTH_SELECTED' => in_array('*', $months) ? $cfg->HTML_SELECTED : ''
			));

		for ($i = '0'; $i <= '11'; $i++) {
			$tpl->append(
				array(
					'MONTH_VALUE' => $i,
					'MONTH_TEXT' => $month_names[$i],
					'MONTH_SELECTED' => in_array($i, $months) ? $cfg->HTML_SELECTED : ''
				));
		}

	}

	/**
	 * Generate dropdown select for day of month
	 * @param $tpl
	 * @param $days
	 */
	public static function genDayOfMonthSelect($tpl, $days)
	{
		$cfg = EasySCP_Registry::get('Config');
		$tpl->append(
			array(
				'DOM_VALUE' => '*',
				'DOM_TEXT' => tr('Every day'),
				'DOM_SELECTED' => in_array('*', $days) ? $cfg->HTML_SELECTED : ''
			));

		for ($i = '0'; $i <= '31'; $i++) {
			$tpl->append(
				array(
					'DOM_VALUE' => $i,
					'DOM_TEXT' => $i,
					'DOM_SELECTED' => in_array($i, $days) ? $cfg->HTML_SELECTED : ''
				));
		}
	}

	/**
	 * Generate dropdown select of allowed users
	 * @global $cfg
	 * @param $tpl
	 * @param $user
	 */
	public static function genUserSelect($tpl, $user)
	{
		global $cfg;
		// user nobody can be used by all users
		$tpl->append(
			array(
				'USER_ID' => 'nobody',
				'USER_NAME' => 'nobody',
				'USER_SELECTED' => $user === 'nobody' ? $cfg->HTML_SELECTED : ''
			)
		);
		if ($_SESSION['user_type'] === 'admin') {
			self::genAdminSelect($tpl, $user);
		}

		$rs = self::getAllowedUsers($_SESSION['user_type']);

		if ($rs->rowCount() != 0) {
			while ($row = $rs->fetch()) {
				$userName = $cfg->APACHE_SUEXEC_USER_PREF . $row['domain_uid'];
				$tpl->append(
					array(
						'USER_ID' => $userName,
						'USER_NAME' => $userName,
						'USER_SELECTED' => $user === $userName ? $cfg->HTML_SELECTED : '',
					)
				);
			}
		}
	}

	/**
	 * Add users allowed by Administrators to dropdown
	 * @global $cfg
	 * @param $tpl
	 * @param $user
	 */
	public static function genAdminSelect($tpl, $user)
	{
		global $cfg;
		$tpl->append(
			array(
				'USER_ID' => 'root',
				'USER_NAME' => 'root',
				'USER_SELECTED' => $user === 'root' ? $cfg->HTML_SELECTED : ''
			)
		);
		$userName = $cfg->APACHE_SUEXEC_USER_PREF . $cfg->APACHE_SUEXEC_MIN_UID;
		$tpl->append(
			array(
				'USER_ID' => $userName,
				'USER_NAME' => $userName,
				'USER_SELECTED' => $user === $userName ? $cfg->HTML_SELECTED : ''
			)
		);
	}

	/**
	 * Get a list of allowed user id's for admin type
	 * @param $adminType
	 * @return mixed
	 */
	public static function getAllowedUsers($adminType)
	{
		$sql_query = "
		SELECT
			domain_uid
		FROM
			domain
	";
		switch ($adminType) {
			case 'admin':
				break;
			case 'reseller':
				$sql_query .= "
				WHERE
					domain_created_id = :user_id;
			";
				break;
			default:
				$sql_query .= "
				WHERE domain_admin_id = :user_id
			";
				break;
		}
		$sql_query .= "
		ORDER BY
			domain_uid";

		$sql_param = array(
			':user_id' => $_SESSION['user_id'],
		);

		DB::prepare($sql_query);
		$rs = DB::execute($sql_param);

		return $rs;
	}

	/**
	 * Detect of expert mode
	 * @param $tpl
	 * @param $schedule
	 */
	public static function detectExpertMode($tpl, $schedule)
	{
		if (strpos($schedule, '@') !== false) {
			$tpl->assign(
				array(
					'EXPERT_MODE_SIMPLE_CHECKED' => 'checked="checked"',
					'EXPERT_MODE_DATETIME_CHECKED' => '',
					'EXPERT_MODE_EXPERT_CHECKED' => ''
				)
			);
		} else if (strpos($schedule, '/') !== false) {
			$tpl->assign(
				array(
					'EXPERT_MODE_SIMPLE_CHECKED' => '',
					'EXPERT_MODE_DATETIME_CHECKED' => '',
					'EXPERT_MODE_EXPERT_CHECKED' => 'checked="checked"'
				)
			);
		} else {
			$tpl->assign(
				array(
					'EXPERT_MODE_SIMPLE_CHECKED' => '',
					'EXPERT_MODE_DATETIME_CHECKED' => 'checked="checked"',
					'EXPERT_MODE_EXPERT_CHECKED' => ''
				)
			);
		}
	}

	/**
	 * Generate dropdown select for simple scheduling
	 * @param $tpl
	 * @param $schedule
	 */
	public static function genSimpleSelect($tpl, $schedule)
	{
		$cfg = EasySCP_Registry::get('Config');
		$simple_cron = array(
			'@hourly' => tr('Hourly'),
			'@daily' => tr('Daily'),
			'@weekly' => tr('Weekly'),
			'@monthly' => tr('Monthly'),
			'@yearly' => tr('Yearly'),
			'@reboot' => tr('After reboot'),
		);
		foreach ($simple_cron as $key => $value) {
			if (array_key_exists($schedule, $simple_cron)) {
				$tpl->append(
					array(
						'SIMPLE_VALUE' => $key,
						'SIMPLE_TEXT' => $value,
						'SIMPLE_SELECTED' => $simple_cron[$schedule] === $value ? $cfg->HTML_SELECTED : ''
					));
			} else {
				$tpl->append(
					array(
						'SIMPLE_VALUE' => $key,
						'SIMPLE_TEXT' => $value,
						'SIMPLE_SELECTED' => ''
					)
				);
			}
		}
	}

	/**
	 * Generate query for user type
	 * @param $adminType
	 * @return mixed
	 * @throws Exception
	 */
	public static function getCronjobQuery($adminType)
	{
		$sql_query = "
			SELECT
				*
			FROM
				cronjobs
		";
		switch ($adminType) {
			case 'admin':
				break;
			case 'reseller':
				$sql_query .= "
				WHERE
					user_id IN (SELECT
							admin_id
						FROM
							admin
						WHERE
							created_by = :user_id);
			";
				break;
			default:
				$sql_query .= "
					WHERE user_id = :user_id
				";
				break;
		}
		$sql_query .= "
			ORDER BY
				user_id, name
		";

		$sql_param = array(
			':user_id' => $_SESSION['user_id'],
		);

		DB::prepare($sql_query);
		$rs = DB::execute($sql_param);

		return $rs;
	}
	/**
	 * Generate list of all available cronjobs
	 * @param $tpl
	 */
	public static function genCronjobList($tpl)
	{
		$rs = EasyCron::getCronjobs($_SESSION['user_type'], $_SESSION['user_id']);

		if ($rs->rowCount() == 0) {
			$tpl->assign(array(
					'CRON_MSG' => tr('Cronjob list is empty!'),
					'CRON_MSG_TYPE' => 'info',
					'CRON_LIST' => '')
			);
		} else {
			while ($row = $rs->fetch()) {
				$tpl->append(
					array(
						'STATUS_ICON'			=> $row['active']=='yes'?'ok':'disabled',
						'CRON_OWNER'			=> $row['admin_name'],
						'CRON_NAME'				=> $row['name'],
						'CRON_DESCR'			=> $row['description'],
						'CRON_USER'				=> $row['user'],
						'CRON_DELETE_ACTION'	=> 'cronjob_manage.php?delete_cron_id=' . $row['id'],
						'CRON_EDIT_ACTION'		=> 'cronjob_manage.php?edit_cron_id=' . $row['id'],
						'CRON_STATUS_ACTION'	=> 'cronjob_manage.php?status_cron_id=' . $row['id'],
					)
				);
			}

			$tpl->assign('SUB_MESSAGE', '');
		}
	} // End of gen_cron_job_list();

}