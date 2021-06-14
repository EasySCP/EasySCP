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
$template = 'client/protected_group_add.tpl';

padd_group($tpl, $sql, get_user_domain_id($_SESSION['user_id']));

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

function padd_group($tpl, $sql, $dmn_id) {

	$cfg = EasySCP_Registry::get('Config');

	if (isset($_POST['uaction']) && $_POST['uaction'] == 'add_group') {
		// we have to add the group
		if (isset($_POST['groupname'])) {
			if (!validates_username($_POST['groupname'])) {
				set_page_message(tr('Invalid group name!'), 'warning');
				return;
			}

			$groupname = $_POST['groupname'];

			$sql_param = array(
				'groupname'	=> $groupname,
				'dmn_id'	=> $dmn_id
			);
		
			$sql_query = "
				SELECT
					`id`
				FROM
					`htaccess_groups`
				WHERE
					`ugroup` = :groupname
				AND
					`dmn_id` = :dmn_id;
			";
		
			DB::prepare($sql_query);
			$stmt = DB::execute($sql_param);

			if ($stmt->rowCount() == 0) {
				$change_status = $cfg->ITEM_ADD_STATUS;

				$sql_param = array(
					'dmn_id'	=> $dmn_id,
					'ugroup'	=> $groupname,
					'status'	=> $change_status
				);
			
				$sql_query = "
					INSERT INTO `htaccess_groups`
						(`dmn_id`, `ugroup`, `status`)
					VALUES
						(:dmn_id, :ugroup, :status);
				";
			
				DB::prepare($sql_query);
				DB::execute($sql_param);

				send_request('110 DOMAIN htaccess ' . $dmn_id);

				$admin_login = $_SESSION['user_logged'];
				write_log("$admin_login: add group (protected areas): $groupname");
				user_goto('protected_user_manage.php');
			} else {
				set_page_message(tr('Group already exists!'), 'error');
				return;
			}
		} else {
			set_page_message(tr('Invalid group name!'), 'error');
			return;
		}
	} else {
		return;
	}
}
?>