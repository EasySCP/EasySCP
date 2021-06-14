<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2020 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'admin/manage_users.tpl';

if (isset($_POST['details']) && !empty($_POST['details'])) {
	$_SESSION['details'] = $_POST['details'];
} else {
	if (!isset($_SESSION['details'])) {
		$_SESSION['details'] = "hide";
	}
}

if (isset($_SESSION['user_added'])) {
	unset($_SESSION['user_added']);

	set_page_message(tr('User added successfully'), 'success');
}

if (isset($_SESSION['reseller_added'])) {
	unset($_SESSION['reseller_added']);

	set_page_message(tr('Reseller added successfully'), 'success');
}

if (isset($_SESSION['user_updated'])) {
	unset($_SESSION['user_updated']);

	set_page_message(tr('User updated successfully'), 'success');
}

if (isset($_SESSION['user_deleted'])) {
	unset($_SESSION['user_deleted']);

	set_page_message(tr('User deleted successfully'), 'success');
}

if (isset($_SESSION['email_updated'])) {
	unset($_SESSION['email_updated']);

	set_page_message(tr('Email Updated successfully'), 'success');
}

if (isset($_SESSION['hdomain'])) {
	unset($_SESSION['hdomain']);

	set_page_message(tr('This user has a domain!<br />To delete the user first delete the domain!'), 'warning');
}

if (isset($_SESSION['user_disabled'])) {
	unset($_SESSION['user_disabled']);

	set_page_message(tr('User disabled successfully'), 'success');
}

if (isset($_SESSION['user_enabled'])) {
	unset($_SESSION['user_enabled']);

	set_page_message(tr('User enabled successfully'), 'success');
}

get_admin_manage_users($tpl);

if (!$cfg->exists('HOSTING_PLANS_LEVEL') || strtolower($cfg->HOSTING_PLANS_LEVEL) === 'admin') {
	$tpl->assign('EDIT_OPTION', true);
}

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('EasySCP - Admin/Manage Users')
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_users_manage.tpl');
gen_admin_menu($tpl, 'admin/menu_users_manage.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();
?>