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
$template = 'reseller/settings_welcome_mail.tpl';

$user_id = $_SESSION['user_id'];

$data = get_welcome_email($user_id, 'user');

if (isset($_POST['uaction']) && $_POST['uaction'] == 'email_setup') {
	$data['subject'] = clean_input($_POST['auto_subject']);

	$data['message'] = clean_input($_POST['auto_message']);

	if ($data['subject'] == '') {
		set_page_message(tr('Please specify a subject!'), 'warning');
	} else if ($data['message'] == '') {
		set_page_message(tr('Please specify message!'), 'warning');
	} else {
		set_welcome_email($user_id, $data);

		set_page_message (tr('Auto email template data updated!'), 'info');
	}
}

// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('EasySCP - Reseller/Manage users/Email setup'),
		'TR_EMAIL_SETUP' => tr('Email setup'),
		'TR_MESSAGE_TEMPLATE_INFO' => tr('Message template info'),
		'TR_USER_LOGIN_NAME' => tr('User login (system) name'),
		'TR_USER_PASSWORD' => tr('User password'),
		'TR_USER_REAL_NAME' => tr('User real (first and last) name'),
		'TR_MESSAGE_TEMPLATE' => tr('Message template'),
		'TR_SUBJECT' => tr('Subject'),
		'TR_MESSAGE' => tr('Message'),
		'TR_SENDER_EMAIL' => tr('Senders email'),
		'TR_SENDER_NAME' => tr('Senders name'),
		'TR_APPLY_CHANGES' => tr('Apply changes'),
		'TR_USERTYPE' => tr('User type (admin, reseller, user)'),
		'TR_BASE_SERVER_VHOST' => tr('URL to this admin panel'),
		'TR_BASE_SERVER_VHOST_PREFIX' => tr('URL protocol'),
		'SUBJECT_VALUE' => tohtml($data['subject']),
		'MESSAGE_VALUE' => tohtml($data['message']),
		'SENDER_EMAIL_VALUE' => tohtml($data['sender_email']),
		'SENDER_NAME_VALUE' => tohtml($data['sender_name'])
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_users_manage.tpl');
gen_reseller_menu($tpl, 'reseller/menu_users_manage.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();
?>