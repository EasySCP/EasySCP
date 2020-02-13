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
$template = 'admin/sessions_manage.tpl';

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Admin/Manage Sessions'),
		'TR_MANAGE_USER_SESSIONS'	=> tr('Manage user sessions'),
		'TR_USERNAME'				=> tr('Username'),
		'TR_USERTYPE'				=> tr('User type'),
		'TR_LOGIN_ON'				=> tr('Last access'),
		'TR_OPTIONS'				=> tr('Options'),
		'TR_DELETE'					=> tr('Kill session')
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_users_manage.tpl', true);
gen_admin_menu($tpl, 'admin/menu_users_manage.tpl', true);

kill_session();

gen_user_sessions($tpl);

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

function kill_session() {

	$sql = EasySCP_Registry::get('Db');

	if (isset($_GET['kill']) && $_GET['kill'] !== ''
		&& $_GET['kill'] !== $_SESSION['user_logged']) {
		$admin_name = $_GET['kill'];
		$query = "
			DELETE FROM
				`login`
			WHERE
				`session_id` = ?
		";

		exec_query($sql, $query, $admin_name);
		set_page_message(tr('User session was killed!'), 'info');
		write_log($_SESSION['user_logged'] . ": killed user session: $admin_name!");
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function gen_user_sessions($tpl) {

	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			*
		FROM
			`login`
	";

	$rs = exec_query($sql, $query);

	while (!$rs->EOF) {
		if ($rs->fields['user_name'] === NULL) {
			$tpl->append(
				array(
					'ADMIN_USERNAME' => tr('Unknown'),
					'LOGIN_TIME' => date("G:i:s", $rs->fields['lastaccess'])
				)
			);
		} else {
			$tpl->append(
				array(
					'ADMIN_USERNAME' => $rs->fields['user_name'],
					'LOGIN_TIME' => date("G:i:s", $rs->fields['lastaccess'])
				)
			);
		}

		$sess_id = session_id();

		if ($sess_id === $rs->fields['session_id']) {
			$tpl->append('KILL_LINK', 'sessions_manage.php');
		} else {
			$tpl->append('KILL_LINK', 'sessions_manage.php?kill=' . $rs->fields['session_id']);
		}


		$rs->moveNext();
	}
}
?>