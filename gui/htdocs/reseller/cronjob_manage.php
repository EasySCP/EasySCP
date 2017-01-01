<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2017 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

require '../../include/easyscp-lib.php';

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'reseller/cronjob_manage.tpl';

// static page messages
$tpl->assign(
	array(
		'TR_CLIENT_CRONJOBS_TITLE'	=> tr('EasySCP - Reseller/Cronjob Manager'),
	)
);

if (isset($_GET['delete_cron_id']) && is_numeric($_GET['delete_cron_id'])) {
	EasyCron::deleteCronJob($_GET['delete_cron_id']);
	user_goto('cronjob_overview.php');
}
if (isset($_GET['status_cron_id']) && is_numeric($_GET['status_cron_id'])) {
	EasyCron::toggleCronStatus($_GET['status_cron_id']);
	user_goto('cronjob_overview.php');
}
/*
 *
 * static page messages.
 *
 */
gen_reseller_mainmenu($tpl, 'reseller/main_menu_users_manage.tpl');
gen_reseller_menu($tpl, 'reseller/menu_users_manage.tpl');

gen_logged_from($tpl);

check_permissions($tpl);
if (isset($_POST['uaction']) && $_POST['uaction'] === 'add_cronjob') {
	EasyCron::addCronJob();
	user_goto('cronjob_overview.php');
}

$tpl->assign(
	array(
		'TR_ACTIVE'			=> tr('Active'),
		'TR_COMMAND'		=> tr('Command to run:'),
		'TR_CRON_SCHEDULE'	=> tr('Cronjob schedule'),
		'TR_DAY'			=> tr('Day(s):'),
		'TR_DESCRIPTION'	=> tr('Description'),
		'TR_EXPERT_MODE'	=> tr('Expert mode'),
		'TR_CRON_SIMPLE'	=> tr('Simple schedule'),
		'TR_CRON_DATETIME'	=> tr('Select date/time below'),
		'TR_SIMPLE_SCHEDULE'=> tr('Simple schedule'),
		'TR_HOUR'			=> tr('Hour(s):'),
		'TR_MIN'			=> tr('Minute(s):'),
		'TR_MONTHS'			=> tr('Month(s):'),
		'TR_NAME'			=> tr('Name'),
		'TR_NO'				=> tr('No'),
		'TR_PAGE_TITLE'		=> tr('EasySCP - Admin/Manage cronjobs'),
		'TR_RESET'			=> tr('Reset'),
		'TR_USER'			=> tr('User'),
		'TR_WEEKDAYS'		=> tr('Weekday(s):'),
		'TR_YES'			=> tr('Yes'),
	)
);
if (isset($_GET['edit_cron_id']) && is_numeric($_GET['edit_cron_id'])) {

	$rs = EasyCron::getCronJobByID($_GET['edit_cron_id']);

	if ($rs->rowCount() <= 0) {
		user_goto('cronjob_overview.php');
	} else {
		$row = $rs->fetch();
		$scheduleSplit = explode(' ', $row['schedule']);
		if (count($scheduleSplit) == 5) {
			$tpl->assign(
				array(
					'MINUTE_EXPERT'				=> $scheduleSplit[0],
					'DOM_EXPERT'				=> $scheduleSplit[2],
					'HOUR_EXPERT'				=> $scheduleSplit[1],
					'MONTH_EXPERT'				=> $scheduleSplit[3],
					'DOW_EXPERT'				=> $scheduleSplit[4],
				)
			);
			$minutes = explode(',', $scheduleSplit[0]);
			$days = explode(',', $scheduleSplit[2]);
			$hours = explode(',', $scheduleSplit[1]);
			$months = explode(',', $scheduleSplit[3]);
			$weekdays = explode(',', $scheduleSplit[4]);
		} else {
			$tpl->assign(
				array(
					'MINUTE_EXPERT'				=> '*',
					'DOM_EXPERT'				=> '*',
					'HOUR_EXPERT'				=> '*',
					'MONTH_EXPERT'				=> '*',
					'DOW_EXPERT'				=> '*',
				)
			);
			$minutes = array('*');
			$days = array('*');
			$hours = array('*');
			$months = array('*');
			$weekdays = array('*');
		}
		$tpl->assign(
			array(
				'TR_ADD'					=> tr('Save'),
				'TR_ADD_CRONJOB'			=> tr('Edit Cronjob'),
				'NAME'						=> $row['name'],
				'DESCRIPTION'				=> $row['description'],
				'CRON_CMD'					=> $row['command'],
				'CRON_ID'					=> $row['id'],
				'ACTIVE_YES_SELECTED'		=> $row['active']=='yes'?$cfg->HTML_SELECTED:'',
				'ACTIVE_NO_SELECTED'		=> $row['active']=='no'?$cfg->HTML_SELECTED:'',
				'SIMPLE_SELECT'				=>'',
			)
		);
		$schedule = $row['schedule'];
		$user = $row['user'];
	}
} else {
	$tpl->assign(
		array(
			'TR_ADD'			=> tr('Add'),
			'TR_ADD_CRONJOB'	=> tr('Add Cronjob'),
			'NAME'				=> '',
			'DESCRIPTION'		=> '',
			'CRON_CMD'			=> '',
			'CRON_ID'			=> '',
			'MINUTE_EXPERT'		=> '',
			'DOM_EXPERT'		=> '',
			'HOUR_EXPERT'		=> '',
			'MONTH_EXPERT'		=> '',
			'DOW_EXPERT'		=> '',
			'ACTIVE_YES_SELECTED'	=> $cfg->HTML_SELECTED,
			'ACTIVE_NO_SELECTED'	=> '',
			'SIMPLE_SELECTED'		=>'',
		)
	);
	$minutes = array('*');
	$days = array('*');
	$hours = array('*');
	$months = array('*');
	$weekdays = array('*');
	$user = '';
	$schedule = '';
}
EasyCron::detectExpertMode($tpl, $schedule);
EasyCron::genMinuteSelect($tpl, $minutes);
EasyCron::genHourSelect($tpl, $hours);
EasyCron::genDayOfMonthSelect($tpl, $days);
EasyCron::genDayOfWeekSelect($tpl, $weekdays);
EasyCron::genMonthSelect($tpl, $months);
EasyCron::genUserSelect($tpl, $user);
EasyCron::genSimpleSelect($tpl, $schedule);

gen_page_message($tpl);

$tpl->display($template);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

unset_messages();
