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
$template = 'admin/settings_lostpassword.tpl';

$user_id = $_SESSION['user_id'];

$selected_on = '';
$selected_off = '';

$data_1 = get_lostpassword_activation_email($user_id);
$data_2 = get_lostpassword_password_email($user_id);

if (isset($_POST['uaction']) && $_POST['uaction'] == 'apply') {

	$err_message = '';

	$data_1['subject'] = clean_input($_POST['subject1'], false);
	$data_1['message'] = clean_input($_POST['message1'], false);
	$data_2['subject'] = clean_input($_POST['subject2'], false);
	$data_2['message'] = clean_input($_POST['message2'], false);

	if (empty($data_1['subject']) || empty($data_2['subject'])) {
		$err_message = tr('Please specify a subject!');
	}
	if (empty($data_1['message']) || empty($data_2['message'])) {
		$err_message = tr('Please specify message!');
	}

	if (!empty($err_message)) {
		set_page_message($err_message, 'warning');
	} else {
		set_lostpassword_activation_email($user_id, $data_1);
		set_lostpassword_password_email($user_id, $data_2);
		set_page_message(tr('Auto email template data updated!'), 'info');
	}
}

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('EasySCP - Admin/Lostpw email setup'),
		'TR_LOSTPW_EMAIL' => tr('Lost password e-mail'),
		'TR_MESSAGE_TEMPLATE_INFO' => tr('Message template info'),
		'TR_MESSAGE_TEMPLATE' => tr('Message template'),
		'SUBJECT_VALUE1' => clean_input($data_1['subject'], true),
		'MESSAGE_VALUE1' => tohtml($data_1['message']),
		'SUBJECT_VALUE2' => clean_input($data_2['subject'], true),
		'MESSAGE_VALUE2' => tohtml($data_2['message']),
		'SENDER_EMAIL_VALUE' => tohtml($data_1['sender_email']),
		'SENDER_NAME_VALUE' => tohtml($data_1['sender_name']),
		'TR_ACTIVATION_EMAIL' => tr('Activation E-Mail'),
		'TR_PASSWORD_EMAIL' => tr('Password E-Mail'),
		'TR_USER_LOGIN_NAME' => tr('User login (system) name'),
		'TR_USER_PASSWORD' => tr('User password'),
		'TR_USER_REAL_NAME' => tr('User (first and last) name'),
		'TR_LOSTPW_LINK' => tr('Lost password link'),
		'TR_SUBJECT' => tr('Subject'),
		'TR_MESSAGE' => tr('Message'),
		'TR_SENDER_EMAIL' => tr('Senders email'),
		'TR_SENDER_NAME' => tr('Senders name'),
		'TR_APPLY_CHANGES' => tr('Apply changes'),
		'TR_BASE_SERVER_VHOST' => tr('URL to this admin panel'),
		'TR_BASE_SERVER_VHOST_PREFIX' => tr('URL protocol')
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_settings.tpl');
gen_admin_menu($tpl, 'admin/menu_settings.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();
?>