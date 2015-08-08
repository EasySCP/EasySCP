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
$template = 'client/sql_database_add.tpl';

// dynamic page data

check_sql_permissions($sql, $_SESSION['user_id']);

gen_page_post_data($tpl);

add_sql_database($sql, $_SESSION['user_id']);

// static page messages
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('EasySCP - Client/Add SQL Database'),
		'TR_ADD_DATABASE' => tr('Add SQL database'),
		'TR_DB_NAME' => tr('Database name'),
		'TR_USE_DMN_ID' => tr('Use numeric ID'),
		'TR_START_ID_POS' => tr('Before the name'),
		'TR_END_ID_POS' => tr('After the name'),
		'TR_ADD' => tr('Add')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_manage_sql.tpl');
gen_client_menu($tpl, 'client/menu_manage_sql.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

// page functions

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function gen_page_post_data($tpl) {

	$cfg = EasySCP_Registry::get('Config');

	if ($cfg->MYSQL_PREFIX === 'yes') {
		$tpl->assign('MYSQL_PREFIX_NO', true);

		if ($cfg->MYSQL_PREFIX_TYPE === 'behind') {
			$tpl->assign('MYSQL_PREFIX_BEHIND', true);
		} else {
			$tpl->assign('MYSQL_PREFIX_INFRONT', true);
		}
	} else {
		$tpl->assign('MYSQL_PREFIX_YES', true);
		$tpl->assign('MYSQL_PREFIX_ALL', true);
	}

	if (isset($_POST['uaction']) && $_POST['uaction'] === 'add_db') {
		$tpl->assign(
			array(
				'DB_NAME' => clean_input($_POST['db_name'], true),
				'USE_DMN_ID' => (isset($_POST['use_dmn_id']) && $_POST['use_dmn_id'] === 'on') ? $cfg->HTML_CHECKED : '',
				'START_ID_POS_CHECKED' => (isset($_POST['id_pos']) && $_POST['id_pos'] !== 'end') ? $cfg->HTML_CHECKED : '',
				'END_ID_POS_CHECKED' => (isset($_POST['id_pos']) && $_POST['id_pos'] === 'end') ? $cfg->HTML_CHECKED : ''
			)
		);
	} else {
		$tpl->assign(
			array(
				'DB_NAME' => '',
				'USE_DMN_ID' => '',
				'START_ID_POS_CHECKED' => $cfg->HTML_CHECKED,
				'END_ID_POS_CHECKED' => ''
			)
		);
	}
}

/**
 * Check if a database with same name already exists
 *
 * @param  EasySCP_Database $sql EasySCP_Database instance
 * @param  string $db_name database name to be checked
 * @return boolean TRUE if database exists, false otherwise
 */
function check_db_name($sql, $db_name) {

	$rs = exec_query($sql, 'SHOW DATABASES');

	while (!$rs->EOF) {
		if ($db_name == $rs->fields['Database']) {
			return true;
		}

		$rs->moveNext();
	}

	return false;
}

function add_sql_database($sql, $user_id) {

	$cfg = EasySCP_Registry::get('Config');

	if (!isset($_POST['uaction'])) return;

	// let's generate database name.

	if (empty($_POST['db_name'])) {
		set_page_message(tr('Please specify a database name!'), 'warning');
		return;
	}

	$dmn_id = get_user_domain_id($user_id);

	if (isset($_POST['use_dmn_id']) && $_POST['use_dmn_id'] === 'on') {

		// we'll use domain_id in the name of the database;
		if (isset($_POST['id_pos']) && $_POST['id_pos'] === 'start') {
			$db_name = $dmn_id . "_" . clean_input($_POST['db_name']);
		} else if (isset($_POST['id_pos']) && $_POST['id_pos'] === 'end') {
			$db_name = clean_input($_POST['db_name']) . "_" . $dmn_id;
		}
	} else {
		$db_name = clean_input($_POST['db_name']);
	}

	if (strlen($db_name) > $cfg->MAX_SQL_DATABASE_LENGTH) {
		set_page_message(tr('Database name is too long!'), 'warning');
		return;
	}

	// have we such database in the system!?
	if (check_db_name($sql, $db_name)) {
		set_page_message(
			tr('Specified database name already exists!'),
			'warning'
		);
		return;
	}
	// are wildcards used?
	if (preg_match("/[%|\?]+/", $db_name)) {
		set_page_message(
			tr('Wildcards such as %% and ? are not allowed!'),
			'warning'
		);
		return;
	}

	DB::query('CREATE DATABASE IF NOT EXISTS `' . $db_name . '` DEFAULT CHARACTER SET ' . EasyConfig::$cfg->DATABASE_DEFAULT_CHARACTER_SET . ' COLLATE ' . EasyConfig::$cfg->DATABASE_DEFAULT_COLLATE . ';')->closeCursor();

	$sql_param = array(
		':domain_id'	=> $dmn_id,
		':sqld_name'	=> $db_name
	);

	$sql_query = "
		INSERT INTO
			sql_database (domain_id, sqld_name, status)
		VALUES
			(:domain_id, :sqld_name, 'ok');
	";

	DB::prepare($sql_query);
	DB::execute($sql_param)->closeCursor();

	update_reseller_c_props(get_reseller_id($dmn_id));

	write_log($_SESSION['user_logged'] . ": adds new SQL database: " . tohtml($db_name));
	set_page_message(tr('SQL database created successfully!'), 'info');
	user_goto('sql_manage.php');
}

/**
 * check user sql permission
 */
function check_sql_permissions($sql, $user_id) {
	if (isset($_SESSION['sql_support']) && $_SESSION['sql_support'] == "no") {
		header("Location: index.php");
	}

	$dmn_props = get_domain_default_props($user_id);

	list($sqld_acc_cnt) = get_domain_running_sql_acc_cnt($sql, $dmn_props['domain_id']);

	if ($dmn_props['domain_sqld_limit'] != 0 && $sqld_acc_cnt >= $dmn_props['domain_sqld_limit']) {
		set_page_message(tr('SQL accounts limit reached!'), 'warning');
		user_goto('sql_manage.php');
	}
}
?>
