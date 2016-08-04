<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'client/protected_user_add.tpl';

padd_user(get_user_domain_id($_SESSION['user_id']));

// static page messages
gen_logged_from($tpl);
check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Client/Webtools'),
		'TR_HTACCESS'			=> tr('Protected areas'),
		'TR_ACTION'				=> tr('Action'),
		'TR_USER_MANAGE'		=> tr('Manage user'),
		'TR_USERS'				=> tr('User'),
		'TR_USERNAME'			=> tr('Username'),
		'TR_ADD_USER'			=> tr('Add user'),
		'TR_GROUPNAME'			=> tr('Group name'),
		'TR_GROUP_MEMBERS'		=> tr('Group members'),
		'TR_ADD_GROUP'			=> tr('Add group'),
		'TR_EDIT'				=> tr('Edit'),
		'TR_GROUP'				=> tr('Group'),
		'TR_DELETE'				=> tr('Delete'),
		'TR_GROUPS'				=> tr('Groups'),
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

function padd_user($dmn_id) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	if (isset($_POST['uaction']) && $_POST['uaction'] == 'add_user') {
		// we have to add the user
		if (isset($_POST['username']) && isset($_POST['pass']) && isset($_POST['pass_rep'])) {
			if (!validates_username($_POST['username'])) {
				set_page_message(tr('Wrong username!'), 'warning');
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
			if ($_POST['pass'] !== $_POST['pass_rep']) {
				set_page_message(tr('Passwords do not match!'), 'warning');
				return;
			}
			$status = $cfg->ITEM_ADD_STATUS;

			$uname = clean_input($_POST['username']);

			$upass = crypt_user_pass_with_salt($_POST['pass']);

			$query = "
				SELECT
					`id`
				FROM
					`htaccess_users`
				WHERE
					`uname` = ?
				AND
					`dmn_id` = ?
			";
			$rs = exec_query($sql, $query, array($uname, $dmn_id));

			if ($rs->recordCount() == 0) {

				$query = "
					INSERT INTO `htaccess_users`
						(`dmn_id`, `uname`, `upass`, `status`)
					VALUES
						(?, ?, ?, ?)
				";
				exec_query($sql, $query, array($dmn_id, $uname, $upass, $status));

				send_request('110 DOMAIN htaccess ' . $dmn_id);

				$admin_login = $_SESSION['user_logged'];
				write_log("$admin_login: add user (protected areas): $uname");
				user_goto('protected_user_manage.php');
			} else {
				set_page_message(tr('User already exist !'), 'error');
				return;
			}
		}
	} else {
		return;
	}
}
?>