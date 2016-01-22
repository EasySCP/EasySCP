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
$template = 'reseller/cronjob_overview.tpl';

$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Reseller/Manage cronjobs'),
		'TR_CLIENT_CRONJOBS_TITLE'	=> tr('EasySCP - Reseller/Cronjob Manager'),
		'THEME_COLOR_PATH'			=> "../themes/{$cfg->USER_INITIAL_THEME}",
		'THEME_CHARSET'				=> tr('encoding'),
		'TR_CRONJOB_OVERVIEW'		=> tr('Cronjob Overview')
	)
);

/*
 *
 * static page messages.
 *
 */

gen_reseller_mainmenu($tpl, 'reseller/main_menu_users_manage.tpl');
gen_reseller_menu($tpl, 'reseller/menu_users_manage.tpl');

gen_page_message($tpl);

gen_logged_from($tpl);

check_permissions($tpl);

EasyCron::genCronjobLlist($tpl);

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
		'TR_OWNER'					=> tr('Owner'),
	)
);

gen_page_message($tpl);

$tpl->display($template);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

unset_messages();