<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'admin/cronjob_overview.tpl';

$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Admin/Manage cronjobs'),
		'TR_CLIENT_CRONJOBS_TITLE'	=> tr('EasySCP - Admin/Cronjob Manager'),
		'THEME_COLOR_PATH'			=> "../themes/{$cfg->USER_INITIAL_THEME}",
		'THEME_CHARSET'				=> tr('encoding'),
		'TR_CRONJOB_OVERVIEW'		=> tr('Cronjob Overview')
	)
);

/*
 * functions start
 */

/**
 * @todo implement this function
 */
function gen_cron_job_list($tpl) {
	$cfg = EasySCP_Registry::get('Config');
	$sql_query = "
		SELECT
			*
		FROM
			cronjobs
		ORDER BY
			name
	";
	$rs = DB::query($sql_query);
	
	if ($rs->rowCount() == 0) {
		$tpl->assign(array(
			'CRON_MSG'		=> tr('Cronjob list is empty!'),
			'CRON_MSG_TYPE'	=> 'info',
			'CRON_LIST'		=> '')
		);
	} else {
		while ($row=$rs->fetch()) {
			$tpl->append(
				array(
					'STATUS_ICON'			=> $row['active']=='yes'?'ok':'disabled',
					'CRON_NAME'				=> $row['name'],
					'CRON_DESCR'			=> $row['description'],
					'CRON_USER'				=> $row['user'],
					'CRON_STATUS'			=> translate_dmn_status($row['status']),
					'CRON_DELETE_ACTION'	=> 'cronjob_manage.php?delete_cron_id=' . $row['id'],
					'CRON_EDIT_ACTION'		=> 'cronjob_manage.php?edit_cron_id=' . $row['id'],
					'CRON_STATUS_ACTION'	=> 'cronjob_manage.php?status_cron_id=' . $row['id'],
				)
			);
//			$rs->moveNext();
		}

		$tpl->assign('SUB_MESSAGE', '');
	}
} // End of gen_cron_job_list();

/*
 * functions end
 */

/*
 *
 * static page messages.
 *
 */

gen_admin_mainmenu($tpl, 'admin/main_menu_system_tools.tpl');
gen_admin_menu($tpl, 'admin/menu_system_tools.tpl');

gen_page_message($tpl);

gen_logged_from($tpl);

check_permissions($tpl);

gen_cron_job_list($tpl);

$tpl->assign(
	array(
		'TR_CRON_MANAGER'			=> tr('Cronjob Manager'),
		'TR_MESSAGE_DELETE'			=> tr('Are you sure you want to delete %s?', true, '%s'),
		'TR_MESSAGE_CHANGE_STATUS'	=> tr('Are you sure you want to change the status of cronjob %s?','%s'),
		'TR_CRONJOBS'				=> tr('Cronjobs'),
		'TR_ACTIVE'					=> tr('Active'),
		'TR_ACTION'					=> tr('Active'),
		'TR_EDIT'					=> tr('Edit'),
		'TR_DELETE'					=> tr('Delete'),
		'TR_ADD'					=> tr('Add Cronjob'),
		'TR_CRONJOB_NAME'			=> tr('Name'),
		'TR_USER'					=> tr('User'),
		'TR_DESCR'					=> tr('Description'),
		'TR_ADMIN_OPTIONS'			=> tr('Admin options'),
		'TR_STATUS'					=> tr('Status'),
	)
);

gen_page_message($tpl);

$tpl->display($template);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

unset_messages();