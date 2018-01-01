<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2018 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'client/sql_manage.tpl';

$count = -1;

// common page data.

// check User sql permission
if (isset($_SESSION['sql_support']) && $_SESSION['sql_support'] == "no") {
	user_goto('index.php');
}


// dynamic page data.

gen_db_list($tpl, $sql, $_SESSION['user_id']);

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Client/Manage SQL'),
		'TR_MANAGE_SQL'			=> tr('Manage SQL'),
		'TR_DELETE'				=> tr('Delete'),
		'TR_DATABASE'			=> tr('Database Name and Users'),
		'TR_CHANGE_PASSWORD'	=> tr('Change password'),
		'TR_ACTION'				=> tr('Action'),
		'TR_PHP_MYADMIN'		=> tr('phpMyAdmin'),
		'TR_DATABASE_USERS'		=> tr('Database users'),
		'TR_ADD_USER'			=> tr('Add SQL user'),
		'TR_EXECUTE_QUERY'		=> tr('Execute query'),
		'TR_CHANGE_PASSWORD'	=> tr('Change password'),
		'TR_LOGIN_PMA'			=> tr('Login phpMyAdmin'),
		'TR_MESSAGE_DELETE'		=> tr('This database will be permanently deleted. This process cannot be recovered. All users linked to this database will also be deleted if not linked to another database. Are you sure you want to delete %s?', true, '%s')
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

// page functions.

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $db_id
 */
function gen_db_user_list($tpl, $sql, $db_id) {

	global $count;

	$query = "
		SELECT
			`sqlu_id`, `sqlu_name`
		FROM
			`sql_user`
		WHERE
			`sqld_id` = ?
		ORDER BY
			`sqlu_name`
	";

	$rs = exec_query($sql, $query, $db_id);

	$users = array();
	if ($rs->recordCount() > 0) {
		while (!$rs->EOF) {
			$count++;
			$user_id = $rs->fields['sqlu_id'];
			$user_mysql = $rs->fields['sqlu_name'];
			$users[] =
				array(
					'DB_USER'	=> tohtml($user_mysql),
					'DB_USER_JS'=> tojs($user_mysql),
					'USER_ID'	=> $user_id
				);
			$rs->moveNext();
		}
	}
	$tpl->append( 'DB_USERLIST', $users );
	return count($users);
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $user_id
 */
function gen_db_list($tpl, $sql, $user_id) {

	$dmn_id = get_user_domain_id($user_id);

	$query = "
		SELECT
			`sqld_id`, `sqld_name`
		FROM
			`sql_database`
		WHERE
			`domain_id` = ?
		ORDER BY
			`sqld_name`
	";

	$rs = exec_query($sql, $query, $dmn_id);

	if ($rs->recordCount() == 0) {
		set_page_message(tr('Database list is empty!'), 'info');
		$tpl->assign('DB_LIST', '');
	} else {
		while (!$rs->EOF) {
			$db_id = $rs->fields['sqld_id'];
			$db_name = $rs->fields['sqld_name'];
			$num = gen_db_user_list($tpl, $sql, $db_id);
			$tpl->append(
				array(
					'DB_ID'			=> $db_id,
					'DB_NAME'		=> tohtml($db_name),
					'DB_NAME_JS'	=> tojs($db_name),
					'DB_MSG'		=> $num ? '' : tr('Database user list is empty!')
				)
			);
			$rs->moveNext();
		}
	}
}
?>
