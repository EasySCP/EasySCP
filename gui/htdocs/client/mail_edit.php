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
$template = 'client/mail_edit.tpl';

// dynamic page data.
edit_mail_account($tpl);

if (update_email_pass() && update_email_forward()) {
	set_page_message(tr("Mail were updated successfully!"), 'success');
	$sql_param = array(
		':mail_id' => $_GET['id']
	);
	$sql_query = "
		SELECT
			domain_id
		FROM
			mail_users
		WHERE
			mail_id = :mail_id
	";

	// Einzelne Schreibweise
	DB::prepare($sql_query);
	$row = DB::execute($sql_param, true);
	send_request('130 MAIL '.$row['domain_id']);
	user_goto('mail_accounts.php');
}

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Manage Mail and FTP / Edit mail account'),
		'TR_EDIT_EMAIL_ACCOUNT'	=> tr('Edit email account'),
		'TR_SAVE'				=> tr('Save'),
		'TR_PASSWORD'			=> tr('Password'),
		'TR_PASSWORD_REPEAT'	=> tr('Repeat password'),
		'TR_FORWARD_MAIL'		=> tr('Forward mail'),
		'TR_FORWARD_TO'			=> tr('Forward to'),
		'TR_FWD_HELP'			=> tr("Separate multiple email addresses with a line-break."),
		'TR_EDIT'				=> tr('Edit')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_email_accounts.tpl');
gen_client_menu($tpl, 'client/menu_email_accounts.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// page functions

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function edit_mail_account($tpl) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	if (!isset($_GET['id']) || $_GET['id'] === '' || !is_numeric($_GET['id'])) {
		set_page_message(tr('Email account not found!'), 'error');
		user_goto('mail_accounts.php');
	} else {
		$mail_id = $_GET['id'];
	}

	$dmn_name = $_SESSION['user_logged'];

	$query = "
		SELECT
			t1.*, t2.`domain_id`, t2.`domain_name`
		FROM
			`mail_users` AS t1,
			`domain` AS t2
		WHERE
			t1.`mail_id` = ?
		AND
			t2.`domain_id` = t1.`domain_id`
		AND
			t2.`domain_name` = ?
	";

	$rs = exec_query($sql, $query, array($mail_id, $dmn_name));

	if ($rs->recordCount() == 0) {
		set_page_message(
			tr('User does not exist or you do not have permission to access this interface!'),
			'error'
		);
		user_goto('mail_accounts.php');
	} else {
		$mail_acc = $rs->fields['mail_acc'];
		$domain_id = $rs->fields['domain_id'];
		$mail_type_list = $rs->fields['mail_type'];
		$mail_forward = $rs->fields['mail_forward'];
		$sub_id = $rs->fields['sub_id'];

		foreach (explode(',', $mail_type_list) as $mail_type) {
			if ($mail_type == MT_NORMAL_MAIL) {
				$mtype[] = 1;
				$res1 = exec_query($sql, "SELECT `domain_name` FROM `domain` WHERE `domain_id` = ?", $domain_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $tmp1['domain_name'];
			} else if ($mail_type == MT_NORMAL_FORWARD) {
				$mtype[] = 4;
				$res1 = exec_query($sql, "SELECT `domain_name` FROM `domain` WHERE `domain_id` = ?", $domain_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $tmp1['domain_name'];
			} else if ($mail_type == MT_ALIAS_MAIL) {
				$mtype[] = 2;
				$res1 = exec_query($sql, "SELECT `alias_name` FROM `domain_aliasses` WHERE `alias_id` = ?", $sub_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $tmp1['alias_name'];
			} else if ($mail_type == MT_ALIAS_FORWARD) {
				$mtype[] = 5;
				$res1 = exec_query($sql, "SELECT `alias_name` FROM `domain_aliasses` WHERE `alias_id` = ?", $sub_id);
				$tmp1 = $res1->fetchRow();
				$maildomain = $tmp1['alias_name'];
			} else if ($mail_type == MT_SUBDOM_MAIL) {
				$mtype[] = 3;
				$res1 = exec_query($sql, "SELECT `subdomain_name` FROM `subdomain` WHERE `subdomain_id` = ?", $sub_id);
				$tmp1 = $res1->fetchRow();
				$maildomain = $tmp1['subdomain_name'];
				$res1 = exec_query($sql, "SELECT `domain_name` FROM `domain` WHERE `domain_id` = ?", $domain_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $maildomain . "." . $tmp1['domain_name'];
			} else if ($mail_type == MT_SUBDOM_FORWARD) {
				$mtype[] = 6;
				$res1 = exec_query($sql, "SELECT `subdomain_name` FROM `subdomain` WHERE `subdomain_id` = ?", $sub_id);
				$tmp1 = $res1->fetchRow();
				$maildomain = $tmp1['subdomain_name'];
				$res1 = exec_query($sql, "SELECT `domain_name` FROM `domain` WHERE `domain_id` = ?", $domain_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $maildomain . "." . $tmp1['domain_name'];
			} else if ($mail_type == MT_ALSSUB_MAIL) {
				$mtype[] = 7;
				$res1 = exec_query($sql, "SELECT `subdomain_alias_name`, `alias_id` FROM `subdomain_alias` WHERE `subdomain_alias_id` = ?", $sub_id);
				$tmp1 = $res1->fetchRow();
				$maildomain = $tmp1['subdomain_alias_name'];
				$alias_id = $tmp1['alias_id'];
				$res1 = exec_query($sql, "SELECT `alias_name` FROM `domain_aliasses` WHERE `alias_id` = ?", $alias_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $maildomain . "." . $tmp1['alias_name'];
			} else if ($mail_type == MT_ALSSUB_FORWARD) {
				$mtype[] = 8;
				$res1 = exec_query($sql, "SELECT `subdomain_alias_name`, `alias_id` FROM `subdomain_alias` WHERE `subdomain_alias_id` = ?", $sub_id);
				$tmp1 = $res1->fetchRow();
				$maildomain = $tmp1['subdomain_alias_name'];
				$alias_id = $tmp1['alias_id'];
				$res1 = exec_query($sql, "SELECT `alias_name` FROM `domain_aliasses` WHERE `alias_id` = ?", $alias_id);
				$tmp1 = $res1->fetchRow(0);
				$maildomain = $maildomain . "." . $tmp1['alias_name'];
			}
		}

		if (isset($_POST['forward_list'])) {
			$mail_forward = clean_input($_POST['forward_list']);
		}
		$mail_acc = decode_idna($mail_acc);
		$maildomain = decode_idna($maildomain);
		$tpl->assign(
			array(
				'EMAIL_ACCOUNT'	=> tohtml($mail_acc . "@" . $maildomain),
				'FORWARD_LIST'	=> str_replace(',', "\n", tohtml($mail_forward)),
				'MTYPE'			=> implode(',', $mtype),
				'MAIL_TYPE'		=> $mail_type_list,
				'MAIL_ID'		=> $mail_id
			)
		);

		if (($mail_forward !== '_no_') && (count($mtype) > 1)) {
			$tpl->assign(
				array(
					'ACTION'				=> 'update_pass,update_forward',
					'FORWARD_MAIL'			=> true,
					'FORWARD_MAIL_CHECKED'	=> $cfg->HTML_CHECKED,
					'FORWARD_LIST_DISABLED'	=> 'false',
					'NORMAL_MAIL'			=> true
				)
			);
		} else if ($mail_forward === '_no_') {
			$tpl->assign(
				array(
					'ACTION'				=> 'update_pass',
					'NORMAL_MAIL'			=> true
				)
			);
		} else {
			$tpl->assign(
				array(
					'ACTION'				=> 'update_forward',
					'FORWARD_MAIL'			=> true,
					'FORWARD_MAIL_CHECKED'	=> $cfg->HTML_CHECKED,
					'FORWARD_LIST_DISABLED'	=> 'false'
				)
			);
		}
	}
}

function update_email_pass() {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	if (!isset($_POST['uaction'])) {
		return false;
	}
	if (preg_match('/update_pass/', $_POST['uaction']) == 0) {
		return true;
	}
	if (preg_match('/update_forward/', $_POST['uaction']) == 1 || isset($_POST['mail_forward'])) {
		// The user only wants to update the forward list, not the password
		if ($_POST['pass'] === '' && $_POST['pass_rep'] === '') {
			return true;
		}
	}

	$pass = clean_input($_POST['pass']);
	$pass_rep = clean_input($_POST['pass_rep']);
	$mail_id = $_GET['id'];
	$mail_account = clean_input($_POST['mail_account']);

	if (trim($pass) === '' || trim($pass_rep) === '' || $mail_id === '' || !is_numeric($mail_id)) {
		set_page_message(tr('Password data is missing!'), 'warning');
		return false;
	} else if ($pass !== $pass_rep) {
		set_page_message(tr('Entered passwords differ!'), 'warning');
		return false;
	} else if (!chk_password($pass, 50, "/[`\xb4'\"\\\\\x01-\x1f\015\012|<>^]/i")) { // Not permitted chars
		if ($cfg->PASSWD_STRONG) {
			set_page_message(
				sprintf(
					tr('The password must be at least %s chars long and contain letters and numbers to be valid.'),
					$cfg->PASSWD_CHARS
				),
				'warning'
			);
		} else {
			set_page_message(
				sprintf(
					tr('Password data is shorter than %s signs or includes not permitted signs!'),
					$cfg->PASSWD_CHARS
				),
				'warning'
			);
		}
		return false;
	} else {
		$pass = encrypt_db_password($pass);
		$status = $cfg->ITEM_CHANGE_STATUS;
		$query = "UPDATE `mail_users` SET `mail_pass` = ?, `status` = ? WHERE `mail_id` = ?";
		exec_query($sql, $query, array($pass, $status, $mail_id));
		write_log($_SESSION['user_logged'] . ": change mail account password: $mail_account");
		return true;
	}
}

function update_email_forward() {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	if (!isset($_POST['uaction'])) {
		return false;
	}
	if (preg_match('/update_forward/', $_POST['uaction']) == 0
		&& !isset($_POST['mail_forward'])) {
		return true;
	}

	$mail_account = $_POST['mail_account'];
	$mail_id = $_GET['id'];
	$forward_list = clean_input($_POST['forward_list']);
	$mail_accs = array();

	if (isset($_POST['mail_forward'])
		|| $_POST['uaction'] == 'update_forward') {
		$faray = preg_split ('/[\n\s,]+/', $forward_list);

		foreach ($faray as $value) {
			$value = trim($value);
			if (!chk_email($value) && $value !== '' || $value === '') {
				set_page_message(tr("Mail forward list error!"), 'error');
				return false;
			}
			$mail_accs[] = encode_idna($value);;
		}

		$forward_list = implode(',', $mail_accs);

		// Check if the mail type doesn't contain xxx_forward and append it
		if (preg_match('/_forward/', $_POST['mail_type']) == 0) {
			// Get mail account type and append the corresponding xxx_forward
			if ($_POST['mail_type'] == MT_NORMAL_MAIL) {
				$mail_type = $_POST['mail_type'] . ',' . MT_NORMAL_FORWARD;
			} else if ($_POST['mail_type'] == MT_ALIAS_MAIL) {
				$mail_type = $_POST['mail_type'] . ',' . MT_ALIAS_FORWARD;
			} else if ($_POST['mail_type'] == MT_SUBDOM_MAIL) {
				$mail_type = $_POST['mail_type'] . ',' . MT_SUBDOM_FORWARD;
			} else if ($_POST['mail_type'] == MT_ALSSUB_MAIL) {
				$mail_type = $_POST['mail_type'] . ',' . MT_ALSSUB_FORWARD;
			}
		} else {
			// The mail type already contains xxx_forward, so we can use $_POST['mail_type']
			$mail_type = $_POST['mail_type'];
		}
	} else {
		$forward_list = '_no_';
		// Check if mail type was a forward type and remove it
		if (preg_match('/_forward/', $_POST['mail_type']) == 1) {
			$mail_type = preg_replace('/,[a-z]+_forward$/', '', $_POST['mail_type']);
		}
	}

	$status = $cfg->ITEM_CHANGE_STATUS;

	$query = "UPDATE `mail_users` SET `mail_forward` = ?, `mail_type` = ?, `status` = ? WHERE `mail_id` = ?";

	exec_query($sql, $query, array($forward_list, $mail_type, $status, $mail_id));

	write_log($_SESSION['user_logged'] . ": change mail forward: $mail_account");
	return true;
}
?>
