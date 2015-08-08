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
$template = 'client/protected_areas.tpl';

// static page messages
gen_logged_from($tpl);
check_permissions($tpl);

$dmn_id = get_user_domain_id($_SESSION['user_id']);

gen_htaccess_entries($tpl, $sql, $dmn_id);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP - Client/Webtools'),
		'TR_HTACCESS'		=> tr('Protected areas'),
		'TR_DIRECTORY_TREE'	=> tr('Directory tree'),
		'TR_DIRS'			=> tr('Name'),
		'TR__ACTION'		=> tr('Action'),
		'TR_MANAGE_USRES'	=> tr('Manage users and groups'),
		'TR_USERS'			=> tr('User'),
		'TR_USERNAME'		=> tr('Username'),
		'TR_ADD_USER'		=> tr('Add user'),
		'TR_GROUPNAME'		=> tr('Group name'),
		'TR_GROUP_MEMBERS'	=> tr('Group members'),
		'TR_ADD_GROUP'		=> tr('Add group'),
		'TR_EDIT'			=> tr('Edit'),
		'TR_GROUP'			=> tr('Group'),
		'TR_DELETE'			=> tr('Delete'),
		'TR_MESSAGE_DELETE'	=> tr('Are you sure you want to delete %s?', true, '%s'),
		'TR_STATUS'			=> tr('Status'),
		'TR_ADD_AREA'		=> tr('Add new protected area')
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

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $dmn_id
 */
function gen_htaccess_entries($tpl, $sql, &$dmn_id) {
	$query = "
		SELECT
			*
		FROM
			`htaccess`
		WHERE
			`dmn_id` = ?
	";

	$rs = exec_query($sql, $query, $dmn_id);

	if ($rs->recordCount() == 0) {
		set_page_message(tr('You do not have protected areas'), 'info');
	} else {
		while (!$rs->EOF) {
			$auth_name = $rs->fields['auth_name'];

			$tpl->append(
				array(
					'AREA_NAME'		=> tohtml($auth_name),
					'JS_AREA_NAME'	=> addslashes($auth_name),
					'AREA_PATH'		=> tohtml($rs->fields['path']),
					'PID'			=> $rs->fields['id'],
					'STATUS'		=> translate_dmn_status($rs->fields['status'])
				)
			);
			$rs->moveNext();
		}
	}
}
?>