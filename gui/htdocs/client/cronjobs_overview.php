<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2015 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

require '../../include/easyscp-lib.php';

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'client/cronjobs_overview.tpl';

// static page messages
gen_logged_from($tpl);

check_permissions($tpl);

gen_cron_jobs($tpl, $sql, $_SESSION['user_id']);

$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('EasySCP - Client/Cronjob Manager'),
		'TR_CRON_MANAGER' => tr('Cronjob Manager'),
		'TR_MESSAGE_DELETE' => tr('Are you sure you want to delete %s?', true, '%s'),
		'TR_CRONJOBS' => tr('Cronjobs'),
		'TR_ACTIVE' => tr('Active'),
		'TR_ACTION' => tr('Active'),
		'TR_EDIT' => tr('Edit'),
		'TR_DELETE' => tr('Delete'),
		'TR_ADD' => tr('Add Cronjob')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_webtools.tpl');
gen_client_menu($tpl, 'client/menu_webtools.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/*
 * functions start
 */

/**
 * @todo implement this function
 */
function gen_cron_jobs($tpl, $sql, $user_id) {
} // End of gen_cron_job();

/*
 * functions end
 */
?>