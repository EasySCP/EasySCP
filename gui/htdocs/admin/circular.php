<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2017 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'admin/circular.tpl';

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Admin - Email Marketing'),
		'TR_CIRCULAR'				=> tr('Email marketing'),
		'TR_CORE_DATA'				=> tr('Core data'),
		'TR_SEND_TO'				=> tr('Send message to'),
		'TR_ALL_USERS'				=> tr('All users'),
		'TR_ALL_RESELLERS'			=> tr('All resellers'),
		'TR_ALL_USERS_AND_RESELLERS'=> tr('All users & resellers'),
		'TR_MESSAGE_SUBJECT'		=> tr('Message subject'),
		'TR_MESSAGE_TEXT'			=> tr('Message'),
		'TR_ADDITIONAL_DATA'		=> tr('Additional data'),
		'TR_SENDER_EMAIL'			=> tr('Senders email'),
		'TR_SENDER_NAME'			=> tr('Senders name'),
		'TR_SEND_MESSAGE'			=> tr('Send message'),
		'TR_SENDER_NAME'			=> tr('Senders name')
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_users_manage.tpl');
gen_admin_menu($tpl, 'admin/menu_users_manage.tpl');

send_circular($tpl);

gen_page_data($tpl);

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/**
 * @param EasySCP_TemplateEngine $tpl
 *
 * @return void
 */
function gen_page_data($tpl) {

	$sql = EasySCP_Registry::get('Db');

	if (isset($_POST['uaction']) && $_POST['uaction'] === 'send_circular') {
		$tpl->assign(
			array(
				'MESSAGE_SUBJECT'	=> clean_input($_POST['msg_subject'], true),
				'MESSAGE_TEXT'		=> clean_input($_POST['msg_text'], true),
				'SENDER_EMAIL'		=> clean_input($_POST['sender_email'], true),
				'SENDER_NAME'		=> clean_input($_POST['sender_name'], true)
			)
		);
	} else {
		$user_id = $_SESSION['user_id'];

		$query = "
			SELECT
				`fname`, `lname`, `email`
			FROM
				`admin`
			WHERE
				`admin_id` = ?
			GROUP BY
				`email`
		";

		$rs = exec_query($sql, $query, $user_id);

		if (isset($rs->fields['fname']) && isset($rs->fields['lname'])) {
			$sender_name = $rs->fields['fname'] . ' ' . $rs->fields['lname'];
		} elseif (isset($rs->fields['fname']) && !isset($rs->fields['lname'])) {
			$sender_name = $rs->fields['fname'];
		} elseif (!isset($rs->fields['fname']) && isset($rs->fields['lname'])) {
			$sender_name = $rs->fields['lname'];
		} else {
			$sender_name = '';
		}

		$tpl->assign(
			array(
				'MESSAGE_SUBJECT'	=> '',
				'MESSAGE_TEXT'		=> '',
				'SENDER_EMAIL'		=> tohtml($rs->fields['email']),
				'SENDER_NAME'		=> tohtml($sender_name)
			)
		);
	}
}

function check_user_data() {
	global $msg_subject, $msg_text, $sender_email, $sender_name;

	$err_message = '';

	$msg_subject = clean_input($_POST['msg_subject'], false);
	$msg_text = clean_input($_POST['msg_text'], false);
	$sender_email = clean_input($_POST['sender_email'], false);
	$sender_name = clean_input($_POST['sender_name'], false);

	if (empty($msg_subject)) {
		$err_message .= tr('Please specify a message subject!') . '<br />';
	}
	if (empty($msg_text)) {
		$err_message .= tr('Please specify a message content!') . '<br />';
	}
	if (empty($sender_name)) {
		$err_message .= tr('Please specify a sender name!') . '<br />';
	}
	if (empty($sender_email)) {
		$err_message .= tr('Please specify a sender email!') . '<br />';
	} else if (!chk_email($sender_email)) {
		$err_message .= tr("Incorrect email length or syntax!");
	}

	if (!empty($err_message)) {
		set_page_message($err_message, 'warning');

		return false;
	} else {
		return true;
	}
}

function send_reseller_message() {

	$sql = EasySCP_Registry::get('Db');

	$user_id = $_SESSION['user_id'];

	$msg_subject = clean_input($_POST['msg_subject'], false);
	$msg_text = clean_input($_POST['msg_text'], false);
	$sender_email = clean_input($_POST['sender_email'], false);
	$sender_name = clean_input($_POST['sender_name'], false);

	$query = "
		SELECT
			`admin_id`, `fname`, `lname`, `email`
		FROM
			`admin`
		WHERE
			`admin_type` = 'reseller' AND `created_by` = ?
		GROUP BY
			`email`
	";

	$rs = exec_query($sql, $query, $user_id);

	while (!$rs->EOF) {
		if ($_POST['rcpt_to'] == 'rslrs' || $_POST['rcpt_to'] == 'usrs_rslrs') {
			$to = mb_encode_mimeheader(
				$rs->fields['fname'] . " " . $rs->fields['lname'], 'UTF-8') .
				" <" . $rs->fields['email'] . ">";
			send_circular_email(
				$to, mb_encode_mimeheader($sender_name, 'UTF-8') . " <$sender_email>",
				$msg_subject, $msg_text);
		}

		if ($_POST['rcpt_to'] == 'usrs' || $_POST['rcpt_to'] == 'usrs_rslrs') {
			send_reseller_users_message($sql, $rs->fields['admin_id']);
		}

		$rs->moveNext();
	}

	set_page_message(
		tr('You send email to your users successfully!'),
		'success'
	);
	write_log('Mass email was sent from ' . tohtml($sender_name) . '<' . $sender_email . '>!');
}

function send_circular($tpl) {
	if (isset($_POST['uaction']) && $_POST['uaction'] === 'send_circular') {
		if (check_user_data()) {
			send_reseller_message();
			unset($_POST['uaction']);
			gen_page_data($tpl);
		}
	}
}

function send_reseller_users_message($sql, $admin_id) {

	$msg_subject = clean_input($_POST['msg_subject'], false);
	$msg_text = clean_input($_POST['msg_text'], false);
	$sender_email = clean_input($_POST['sender_email'], false);
	$sender_name = clean_input($_POST['sender_name'], false);

	$query = "
		SELECT
			`fname`, `lname`, `email`
		FROM
			`admin`
		WHERE
			`admin_type` = 'user' AND `created_by` = ?
		GROUP BY
			`email`
	";

	$rs = exec_query($sql, $query, $admin_id);

	while (!$rs->EOF) {
		$to = "\"" . mb_encode_mimeheader($rs->fields['fname'] . " " . $rs->fields['lname'], 'UTF-8') .
			"\" <" . $rs->fields['email'] . ">";
		send_circular_email(
			$to, "\"" . mb_encode_mimeheader($sender_name, 'UTF-8') .
			"\" <" . $sender_email . ">", $msg_subject, $msg_text);
		$rs->moveNext();
	}
}

function send_circular_email($to, $from, $subject, $message) {
	$subject = mb_encode_mimeheader($subject, 'UTF-8');

	$headers = "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
	$headers .= "From: " . $from . "\n";
	$headers .= "X-Mailer: EasySCP marketing mailer";

	mail($to, $subject, $message, $headers);
}
?>