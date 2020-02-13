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

/**
 * @todo use db prepared statements
 */

require '../../include/easyscp-lib.php';

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'client/protected_user_edit.tpl';

$dmn_id = get_user_domain_id($_SESSION['user_id']);

if (isset($_GET['uname'])
		&& $_GET['uname'] !== ''
		&& is_numeric($_GET['uname'])) {
	$uuser_id = $_GET['uname'];

	/**
	 * @todo use DB prepared statements
	 */
	$query = "
		SELECT
			`uname`
		FROM
			`htaccess_users`
		WHERE
			`dmn_id` = '$dmn_id'
		AND
			`id` = '$uuser_id'
	";

	$rs = execute_query($sql, $query);

	if ($rs->recordCount() == 0) {
		user_goto('protected_user_manage.php');
	} else {
		$tpl->assign(
			array(
				'UNAME'	=> tohtml($rs->fields['uname']),
				'UID'	=> $uuser_id,
			)
		);
	}
} else if (isset($_POST['nadmin_name'])
		&& !empty($_POST['nadmin_name'])
		&& is_numeric($_POST['nadmin_name'])) {
	$uuser_id = clean_input($_POST['nadmin_name']);

	/**
	 * @todo use DB prepared statements
	 */
	$query = "
		SELECT
			`uname`
		FROM
			`htaccess_users`
		WHERE
			`dmn_id` = '$dmn_id'
		AND
			`id` = '$uuser_id'
	";

	$rs = execute_query($sql, $query);

	if ($rs->recordCount() == 0) {
		user_goto('protected_user_manage.php');
	} else {
		$tpl->assign(
			array(
				'UNAME'	=> tohtml($rs->fields['uname']),
				'UID'	=> $uuser_id,
			)
		);
		pedit_user($dmn_id, $uuser_id);
	}
} else {
	user_goto('protected_user_manage.php');
}

// static page messages
gen_logged_from($tpl);
check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Client/Webtools'),
		'TR_HTACCESS'			=> tr('Protected areas'),
		'TR_ACTION'				=> tr('Action'),
		'TR_UPDATE_USER'		=> tr('Update user'),
		'TR_USERS'				=> tr('User'),
		'TR_USERNAME'			=> tr('Username'),
		'TR_ADD_USER'			=> tr('Add user'),
		'TR_GROUPNAME'			=> tr('Group name'),
		'TR_GROUP_MEMBERS'		=> tr('Group members'),
		'TR_ADD_GROUP'			=> tr('Add group'),
		'TR_EDIT'				=> tr('Edit'),
		'TR_GROUP'				=> tr('Group'),
		'TR_DELETE'				=> tr('Delete'),
		'TR_UPDATE'				=> tr('Modify'),
		'TR_PASSWORD'			=> tr('Password'),
		'TR_PASSWORD_REPEAT'	=> tr('Repeat password'),
		'TR_CANCEL'				=> tr('Cancel')
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

function pedit_user(&$dmn_id, &$uuser_id) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	if (isset($_POST['uaction']) && $_POST['uaction'] == 'modify_user') {
		// we have to add the user
		if (isset($_POST['pass']) && isset($_POST['pass_rep'])) {
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
			if ($_POST['pass'] !== $_POST['pass_rep']) {
				set_page_message(tr('Passwords do not match!'), 'warning');
				return;
			}

			$nadmin_password = crypt_user_pass_with_salt($_POST['pass']);

			$change_status = $cfg->ITEM_CHANGE_STATUS;

			$query = "
				UPDATE
					`htaccess_users`
				SET
					`upass` = ?,
					`status` = ?
				WHERE
					`dmn_id` = ?
				AND
					`id` = ?
			";
			exec_query($sql, $query, array($nadmin_password, $change_status, $dmn_id, $uuser_id,));

			send_request('110 DOMAIN htaccess ' . $dmn_id);

			$query = "
				SELECT
					`uname`
				FROM
					`htaccess_users`
				WHERE
					`dmn_id` = ?
				AND
					`id` = ?
			";
			$rs = exec_query($sql, $query, array($dmn_id, $uuser_id));
			$uname = $rs->fields['uname'];

			$admin_login = $_SESSION['user_logged'];
			write_log("$admin_login: modify user ID (protected areas): $uname");
			user_goto('protected_user_manage.php');
		}
	} else {
		return;
	}
}

function check_get(&$get_input) {
	if (!is_numeric($get_input)) {
		return 0;
	} else {
		return 1;
	}
}
?>