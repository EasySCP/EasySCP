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
$template = 'client/password_change.tpl';

if (isset($_POST['uaction']) && $_POST['uaction'] === 'updt_pass') {
	if (empty($_POST['pass']) || empty($_POST['pass_rep']) || empty($_POST['curr_pass'])) {
		set_page_message(tr('Please fill up all data fields!'), 'warning');
	} else if ($_POST['pass'] !== $_POST['pass_rep']) {
		set_page_message(tr('Passwords do not match!'), 'warning');
	} else if (!chk_password($_POST['pass'])) {
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
	} else if (!check_udata($_SESSION['user_id'], $_POST['curr_pass'])) {
		set_page_message(tr('The current password is wrong!'), 'warning');
	} else {
		$upass = crypt_user_pass($_POST['pass']);

		$_SESSION['user_pass'] = $upass;

		$user_id = $_SESSION['user_id'];

		$query = "
			UPDATE
				`admin`
			SET
				`admin_pass` = ?
			WHERE
				`admin_id` = ?
		";

		$rs = exec_query($sql, $query, array($upass, $user_id));
		write_log($_SESSION['user_logged'] . ": update password!");
		set_page_message(tr('User password updated successfully!'), 'success');
	}
}

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Client/Change Password'),
		'TR_CHANGE_PASSWORD' 	=> tr('Change password'),
		'TR_PASSWORD_DATA' 		=> tr('Password data'),
		'TR_PASSWORD' 			=> tr('Password'),
		'TR_PASSWORD_REPEAT' 	=> tr('Repeat password'),
		'TR_UPDATE_PASSWORD' 	=> tr('Update password'),
		'TR_CURR_PASSWORD' 		=> tr('Current password'),
		// The entries below are for Demo versions only
		'PASSWORD_DISABLED'		=> tr('Password change is deactivated!'),
		'DEMO_VERSION'			=> tr('Demo Version!')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_general_information.tpl');
gen_client_menu($tpl, 'client/menu_general_information.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

function check_udata($id, $pass) {

	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			`admin_id`, `admin_pass`
		FROM
			`admin`
		WHERE
			`admin_id` = ?
		AND
			`admin_pass` = ?
	";

	$rs = exec_query($sql, $query, array($id, md5($pass)));

	return (($rs->recordCount()) != 1) ? false : true;
}
?>