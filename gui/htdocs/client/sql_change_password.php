<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'client/sql_change_password.tpl';

if (isset($_GET['id'])) {
	$db_user_id = $_GET['id'];
} else if (isset($_POST['id'])) {
	$db_user_id = $_POST['id'];
} else {
	user_goto('sql_manage.php');
}

// common page data

if (isset($_SESSION['sql_support']) && $_SESSION['sql_support'] == "no") {
	user_goto('index.php');
}

// dynamic page data
$db_user_name = gen_page_data($tpl, $sql, $db_user_id);
check_usr_sql_perms($sql, $db_user_id);
change_sql_user_pass($sql, $db_user_id, $db_user_name);

// static page messages
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'					=> tr('EasySCP - Client/Change SQL User Password'),
		'TR_CHANGE_SQL_USER_PASSWORD' 	=> tr('Change SQL user password'),
		'TR_USER_NAME' 					=> tr('User name'),
		'TR_PASS' 						=> tr('Password'),
		'TR_PASS_REP' 					=> tr('Repeat password'),
		'TR_CHANGE' 					=> tr('Change'),
		// The entries below are for Demo versions only
		'PASSWORD_DISABLED'				=> tr('Password change is deactivated!'),
		'DEMO_VERSION'					=> tr('Demo Version!')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_manage_sql.tpl');
gen_client_menu($tpl, 'client/menu_manage_sql.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// page functions
function change_sql_user_pass($sql, $db_user_id, $db_user_name) {

	$cfg = EasySCP_Registry::get('Config');

	if (!isset($_POST['uaction'])) {
		return;
	}

	if ($_POST['pass'] === '' && $_POST['pass_rep'] === '') {
		set_page_message(tr('Please specify user password!'), 'warning');
		return;
	}

	if ($_POST['pass'] !== $_POST['pass_rep']) {
		set_page_message(tr('Entered passwords do not match!'), 'warning');
		return;
	}

	if (strlen($_POST['pass']) > $cfg->MAX_SQL_PASS_LENGTH) {
		set_page_message(tr('User password too long!'), 'warning');
		return;
	}

	if (isset($_POST['pass'])
		&& !preg_match('/^[[:alnum:]:!\*\+\#_.-]+$/', $_POST['pass'])) {
		set_page_message(
			tr('Don\'t use special chars like "@, $, %..." in the password!'),
			'warning'
		);
		return;
	}

	if (!chk_password($_POST['pass'])) {
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
		return;
	}

	$user_pass = $_POST['pass'];

	// update user pass in the EasySCP sql_user table;
	$query = "
		UPDATE
			`sql_user`
		SET
			`sqlu_pass` = ?
		WHERE
			`sqlu_name` = ?
	";

	exec_query($sql, $query, array(encrypt_db_password($user_pass), $db_user_name));

	// update user pass in the mysql system tables;
	// TODO use prepared statement for $user_pass
	$query = "SET PASSWORD FOR '$db_user_name'@'%' = PASSWORD('$user_pass')";

	execute_query($sql, $query);
	// TODO use prepared statement for $user_pass
	$query = "SET PASSWORD FOR '$db_user_name'@localhost = PASSWORD('$user_pass')";
	execute_query($sql, $query);

	write_log($_SESSION['user_logged'] . ": update SQL user password: " . tohtml($db_user_name));
	set_page_message(tr('SQL user password was successfully changed!'), 'warning');
	user_goto('sql_manage.php');
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $db_user_id
 * @return mixed
 */
function gen_page_data($tpl, $sql, $db_user_id) {
	$query = "
		SELECT
			`sqlu_name`
		FROM
			`sql_user`
		WHERE
			`sqlu_id` = ?
	";

	$rs = exec_query($sql, $query, $db_user_id);
	$tpl->assign(
		array(
			'USER_NAME' => tohtml($rs->fields['sqlu_name']),
			'ID' => $db_user_id
		)
	);
	return $rs->fields['sqlu_name'];
}
?>