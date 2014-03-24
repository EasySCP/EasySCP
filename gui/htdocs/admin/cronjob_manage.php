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

require '../../include/easyscp-lib.php';

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'admin/cronjob_manage.tpl';

// static page messages
$tpl->assign(
	array(
		'TR_CLIENT_CRONJOBS_TITLE'	=> tr('EasySCP - Admin/Cronjob Manager'),
		'TR_MESSAGE_EXPERT_MODE'	=> tr('Warning!\nDo you really want to change into expert mode?\nYou should know what you\'re doing.'),
		'TR_MESSAGE_NORMAL_MODE'	=> tr('Warning!\nIf you switch back to normal mode all schedule settings will be lost!')
	)
);

if (isset($_GET['delete_cron_id']) && is_numeric($_GET['delete_cron_id'])) {
	delete_cron_job($_GET['delete_cron_id']);
	user_goto('cronjob_overview.php');
}
if (isset($_GET['status_cron_id']) && is_numeric($_GET['status_cron_id'])) {
	toggle_cron_active($_GET['status_cron_id']);
	user_goto('cronjob_overview.php');
}

/**
 * @todo Implement this function
 */
function add_cron_job() {
	$cfg = EasySCP_Registry::get('Config');
	if (!send_request('160 SYSTEM isexecutable ' . $_POST['cron_cmd'])) {
		set_page_message($_POST['cron_cmd'].tr(' Command is not executable!'), 'warning');
		return false;
	}
	if(!send_request('160 SYSTEM userexists '.$_POST['user_name'])){
		set_page_message(tr('The user %s does not exist',$_POST['user_name']), 'warning');
		return false;		
	}
	if ($_POST['minute'][0]=='*' &&
		$_POST['hour'][0]=='*' &&
		$_POST['day_of_month'][0]=='*' &&
		$_POST['month'][0]=='*' &&
		$_POST['day_of_week'][0]=='*'){
		set_page_message(tr('At least one argument must differ from *'), 'warning');
		return false;
	}
	
	if ($_POST['minute'][0]=='*' && isset($_POST['minute'][1])){
		set_page_message(tr('You cannot choose * and another value for minutes'), 'warning');
		return false;
	}
	if ($_POST['hour'][0]=='*' && isset($_POST['hour'][1])){
		set_page_message(tr('You cannot choose * and another value for hours'), 'warning');
		return false;
	}
	if ($_POST['day_of_month'][0]=='*' && isset($_POST['day_of_month'][1])){
		set_page_message(tr('You cannot choose * and another value for days of month'), 'warning');
		return false;
	}
	if ($_POST['month'][0]=='*' && isset($_POST['month'][1])){
		set_page_message(tr('You cannot choose * and another value for months'), 'warning');
		return false;
	}
	if ($_POST['day_of_week'][0]=='*' && isset($_POST['day_of_week'][1])){
		set_page_message(tr('You cannot choose * and another value for weekdays'), 'warning');
		return false;
	}
	
	$minuteString = '';
	foreach ($_POST['minute'] as $key=>$minute) {
		if($key>0){
			$minuteString .= ',';
		}
		$minuteString .= $minute;
	}
	$hourString = '';
	foreach ($_POST['hour'] as $key=>$hour) {
		if($key>0){
			$hourString .= ',';
		}
		$hourString .= $hour;
	}
	$domString = '';
	foreach ($_POST['day_of_month'] as $key=>$day) {
		if($key>0){
			$domString .= ',';
		}
		$domString .= $day;
	}
	$monthString = '';
	foreach ($_POST['month'] as $key=>$month) {
		if($key>0){
			$monthString .= ',';
		}
		$monthString .= $month;
	}
	$dowString = '';
	foreach ($_POST['day_of_week'] as $key=>$day) {
		if($key>0){
			$dowString .= ',';
		}
		$dowString .= $day;
	}
	$sql_param = array(
			':id'			=> $_POST['cron_id'],
			':user_id'		=> $_SESSION['user_id'],
			':minute'		=> $minuteString,
			':hour'			=> $hourString,
			':dayofmonth'	=> $domString,
			':month'		=> $monthString,
			':dayofweek'	=> $dowString,
			':command'		=> $_POST['cron_cmd'],
			':active'		=> $_POST['active'],
			':description'	=> $_POST['description'],
			':name'			=> $_POST['name'],
			':user'			=> $_POST['user_name'],
			':status'		=> $cfg->ITEM_ADD_STATUS
	);
	$sql_query = "
		INSERT INTO cronjobs (
			id,
			user_id,
			minute,
			hour,
			dayofmonth,
			month,
			dayofweek,
			command,
			active,
			description,
			name,
			user,
			status
		) VALUES (
			:id,
			:user_id,
			:minute,
			:hour,
			:dayofmonth,
			:month,
			:dayofweek,
			:command,
			:active,
			:description,
			:name,
			:user,
			:status
		) ON DUPLICATE KEY UPDATE
			user_id		= :user_id,
			minute		= :minute,
			hour		= :hour,
			dayofmonth	= :dayofmonth,
			month		= :month,
			dayofweek	= :dayofweek,
			command		= :command,
			active		= :active,
			description	= :description,
			name		= :name,
			user		= :user,
			status		= 'change'
	";

	// Einzelne Schreibweise
	DB::prepare($sql_query);
	$rs = DB::execute($sql_param);
	$id = DB::getInstance()->lastInsertId();
//	set_page_message('ID: '.$id, 'info');
//	$retVal = send_request('160 SYSTEM cron '.$_SESSION['user_id']);
	if (send_request('160 SYSTEM cron '.$_SESSION['user_id'])){
		set_page_message(tr('Cronjob added!'), 'success');
	} else {
		set_page_message(tr('Cronjob addition failed!'), 'warning');
	}
} // End of add_cron_job()

function gen_minute_select($tpl,$minutes){
	$cfg = EasySCP_Registry::get('Config');
	$tpl->append(
		array(
			'MINUTE_VALUE'		=> '*',
			'MINUTE_TEXT'		=> tr('Every minute'),
			'MINUTE_SELECTED'	=> in_array('*',$minutes) ? $cfg->HTML_SELECTED : ''
	));
	$tpl->append(
		array(
			'MINUTE_VALUE'		=> '*/2',
			'MINUTE_TEXT'		=> tr('Every other minute'),
			'MINUTE_SELECTED'	=> in_array('*/2',$minutes) ? $cfg->HTML_SELECTED : ''
	));
	$tpl->append(
		array(
			'MINUTE_VALUE'		=> '*/5',
			'MINUTE_TEXT'		=> tr('Every five minutes'),
			'MINUTE_SELECTED'	=> in_array('*/5',$minutes) ? $cfg->HTML_SELECTED : ''
	));
	$tpl->append(
		array(
			'MINUTE_VALUE'		=> '*/10',
			'MINUTE_TEXT'		=> tr('Every ten minutes'),
			'MINUTE_SELECTED'	=> in_array('*/10',$minutes) ? $cfg->HTML_SELECTED : ''
	));
	$tpl->append(
		array(
			'MINUTE_VALUE'		=> '*/15',
			'MINUTE_TEXT'		=> tr('Every fifteen minutes'),
			'MINUTE_SELECTED'	=> in_array('*/15',$minutes) ? $cfg->HTML_SELECTED : ''
	));

	for ($i = '0'; $i < '60'; $i++) {
		$tpl->append(
			array(
				'MINUTE_VALUE'		=> $i,
				'MINUTE_TEXT'		=> $i,
				'MINUTE_SELECTED'	=> in_array($i,$minutes) ? $cfg->HTML_SELECTED : ''
		));
	}

}
function gen_hour_select($tpl,$hours){
	$cfg = EasySCP_Registry::get('Config');
	$tpl->append(
		array(
			'HOUR_VALUE'	=> '*',
			'HOUR_TEXT'		=> tr('Every hour'),
			'HOUR_SELECTED'	=> in_array('*',$hours) ? $cfg->HTML_SELECTED : ''
	));
	$tpl->append(
		array(
			'HOUR_VALUE'	=> '*/2',
			'HOUR_TEXT'		=> tr('Every other hour'),
			'HOUR_SELECTED'	=> in_array('*/2',$hours) ? $cfg->HTML_SELECTED : ''
	));
	$tpl->append(
		array(
			'HOUR_VALUE'	=> '*/4',
			'HOUR_TEXT'		=> tr('Every four hours'),
			'HOUR_SELECTED'	=> in_array('*/4',$hours) ? $cfg->HTML_SELECTED : ''
	));
	$tpl->append(
		array(
			'HOUR_VALUE'	=> '*/8',
			'HOUR_TEXT'		=> tr('Every eight hours'),
			'HOUR_SELECTED'	=> in_array('*/8',$hours) ? $cfg->HTML_SELECTED : ''
	));
	$tpl->append(
		array(
			'HOUR_VALUE'	=> '*/12',
			'HOUR_TEXT'		=> tr('Every twelve hours'),
			'HOUR_SELECTED'	=> in_array('*/12',$hours) ? $cfg->HTML_SELECTED : ''
	));

	for ($i = '0'; $i < '24'; $i++) {
		$tpl->append(
			array(
				'HOUR_VALUE'	=> $i,
				'HOUR_TEXT'		=> $i,
				'HOUR_SELECTED'	=> in_array($i,$hours) ? $cfg->HTML_SELECTED : ''
		));
	}

}
function gen_day_of_week_select($tpl,$weekdays){
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
			'DOW_VALUE'		=> '*',
			'DOW_TEXT'		=> tr('Every weekday'),
			'DOW_SELECTED'	=> in_array('*',$weekdays) ? $cfg->HTML_SELECTED : ''
	));

	for ($i = '0'; $i < '7'; $i++) {
		$tpl->append(
			array(
				'DOW_VALUE'		=> $i,
				'DOW_TEXT'		=> $day_names[$i],
				'DOW_SELECTED'	=> in_array($i,$weekdays) ? $cfg->HTML_SELECTED : ''
		));
	}

}
function gen_month_select($tpl,$months){
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
			'MONTH_VALUE'		=> '*',
			'MONTH_TEXT'		=> tr('Every month'),
			'MONTH_SELECTED'	=> in_array('*',$months) ? $cfg->HTML_SELECTED : ''
	));

	for ($i = '0'; $i <= '11'; $i++) {
		$tpl->append(
			array(
				'MONTH_VALUE'		=> $i,
				'MONTH_TEXT'		=> $month_names[$i],
				'MONTH_SELECTED'	=> in_array($i,$months) ? $cfg->HTML_SELECTED : ''
		));
	}

}
function gen_day_of_month_select($tpl,$days){
	$cfg = EasySCP_Registry::get('Config');
	$tpl->append(
		array(
			'DOM_VALUE'		=> '*',
			'DOM_TEXT'		=> tr('Every day'),
			'DOM_SELECTED'	=> in_array('*',$days) ? $cfg->HTML_SELECTED : ''
	));

	for ($i = '0'; $i <= '31'; $i++) {
		$tpl->append(
			array(
				'DOM_VALUE'		=> $i,
				'DOM_TEXT'		=> $i,
				'DOM_SELECTED'	=> in_array($i,$days) ? $cfg->HTML_SELECTED : ''
		));
	}
}
/*
 *
 * static page messages.
 *
 */

gen_admin_mainmenu($tpl, 'admin/main_menu_system_tools.tpl');
gen_admin_menu($tpl, 'admin/menu_system_tools.tpl');

gen_logged_from($tpl);

check_permissions($tpl);
if (isset($_POST['uaction']) && $_POST['uaction'] === 'add_cronjob') {
	add_cron_job();
//	if (add_cron_job()===true){
		user_goto('cronjob_overview.php');
//	} 
}

$tpl->assign(
	array(
		'TR_ACTIVE'			=> tr('Active'),
		'TR_COMMAND'		=> tr('Command to run:'),
		'TR_CRON_SCHEDULE'	=> tr('Cronjob schedule'),
		'TR_DAY'			=> tr('Day(s):'),
		'TR_DESCRIPTION'	=> tr('Description'),
		'TR_EXPERT_MODE'	=> tr('Expert mode'),
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
	$sql_param = array(
		':id'		=> $_GET['edit_cron_id']
	);
	$sql_query = "
		SELECT 
			*
		FROM
			cronjobs 
		WHERE
			id = :id
	";

	DB::prepare($sql_query);
	$rs = DB::execute($sql_param);

	if ($rs->rowCount() <= 0) {
		user_goto('cronjob_overview.php');
	} else {
		$row=$rs->fetch();
	$tpl->assign(
		array(
			'TR_ADD'			=> tr('Save'),
			'TR_ADD_CRONJOB'	=> tr('Edit Cronjob'),
			'NAME'				=> $row['name'],
			'DESCRIPTION'		=> $row['description'],
			'CRON_CMD'			=> $row['command'],
			'USER_NAME'			=> $row['user'],
			'CRON_ID'			=> $row['id'],
			'MINUTE_EXPERT'		=> $row['minute'],
			'DOM_EXPERT'		=> $row['dayofmonth'],
			'HOUR_EXPERT'		=> $row['hour'],
			'MONTH_EXPERT'		=> $row['month'],
			'DOW_EXPERT'		=> $row['dayofweek'],
			'ACTIVE_YES_SELECTED'	=> $row['active']=='yes'?$cfg->HTML_SELECTED:'',
			'ACTIVE_NO_SELECTED'	=> $row['active']=='no'?$cfg->HTML_SELECTED:'',
			'TR_MESSAGE_EXPERT_MODE'	=> tr('Warning!\nDo you really want to change into expert mode?\nYou should know what you\'re doing'),
			'TR_MESSAGE_NORMAL_MODE'	=> tr('Warning!\nIf you switch back to normal mode all schedule settings will be lost!')
		)
	);
	}
	$minutes = explode(',',$row['minute']);
	$days = explode(',',$row['dayofmonth']);
	$hours = explode(',',$row['hour']);
	$months = explode(',',$row['month']);
	$weekdays = explode(',',$row['dayofweek']);
//	if (validate_user_deletion(intval($_GET['delete_id']))) {
//		delete_user(intval($_GET['delete_id']));
//	} else {
//		user_goto('cronjob_overview.php');
//	}
} else {
	$tpl->assign(
		array(
			'TR_ADD'			=> tr('Add'),
			'TR_ADD_CRONJOB'	=> tr('Add Cronjob'),
			'NAME'				=> '',
			'DESCRIPTION'		=> '',
			'CRON_CMD'			=> '',
			'USER_NAME'			=> '',
			'CRON_ID'			=> '',
			'MINUTE_EXPERT'		=> '',
			'DOM_EXPERT'		=> '',
			'HOUR_EXPERT'		=> '',
			'MONTH_EXPERT'		=> '',
			'DOW_EXPERT'		=> '',
			'ACTIVE_YES_SELECTED'	=> $cfg->HTML_SELECTED,
			'ACTIVE_NO_SELECTED'	=> ''
		)
	);
	$minutes = array('*');
	$days = array('*');
	$hours = array('*');
	$months = array('*');
	$weekdays = array('*');
}
gen_minute_select($tpl,$minutes);
gen_hour_select($tpl,$hours);
gen_day_of_month_select($tpl,$days);
gen_day_of_week_select($tpl,$weekdays);
gen_month_select($tpl,$months);

gen_page_message($tpl);

$tpl->display($template);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

unset_messages();

function delete_cron_job($cron_id){
	$sql_param = array(
		':cron_id'	=> $cron_id
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
	$cronData = $rs->fetch();
	
	if($cronData['user_id']==$_SESSION['user_id']){
		$sql_query = "
			DELETE FROM
				cronjobs
			WHERE
				id = :cron_id
		";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param)->closeCursor();
	} else {
		set_page_message(tr('Cronjobs of other users cannot be deleted by you!'), 'error');
		return false;
	}
	if (send_request('160 SYSTEM cron '.$_SESSION['user_id'])){
		set_page_message(tr('Successfully deleted cronjob!'), 'success');
		return true;
	} else {
		set_page_message(tr('Deletion of cronjob failed!'), 'warning');
		return false;
	}
}

function toggle_cron_active($cron_id){
	$cfg = EasySCP_Registry::get('Config');

	$sql_param = array(
		':cron_id'	=> $cron_id
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
	$cronData = $rs->fetch();
	
	if($cronData['user_id']==$_SESSION['user_id']){
		$sql_param = array (
			':cron_id'	=> $cron_id,
			':status'	=> $cfg->ITEM_CHANGE_STATUS,
			':active'	=> $cronData['active']=='yes'?'no':'yes'
		);
		$sql_query = "
			UPDATE 
				cronjobs
			SET 
				active = :active,
				status = :status
			WHERE
				id = :cron_id
		";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param)->closeCursor();
	} else {
		set_page_message(tr('Cronjobs of other users cannot be modified by you!'), 'error');
		return false;
	}
	if (send_request('160 SYSTEM cron '.$_SESSION['user_id'])){
		set_page_message(tr('Successfully changed cronjob!'), 'success');
		return true;
	} else {
		set_page_message(tr('Changing cronjob failed!'), 'warning');
		return false;
	}
	
}