<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2019 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'client/protected_user_manage.tpl';

// static page messages
gen_logged_from($tpl);
check_permissions($tpl);

$dmn_id = get_user_domain_id($_SESSION['user_id']);

gen_pusres($tpl, $sql, $dmn_id);

gen_pgroups($tpl, $sql, $dmn_id);

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
		'TR_GROUP'				=> tr('Group'),
		'TR_GROUPS'				=> tr('Groups'),
		'TR_PASSWORD'			=> tr('Password'),
		'TR_STATUS'				=> tr('Status'),
		'TR_PASSWORD_REPEAT'	=> tr('Repeat password'),
		'TR_MESSAGE_DELETE'		=> tr('Are you sure you want to delete %s?', true, '%s')
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
function gen_pusres($tpl, $sql, &$dmn_id) {
	$cfg = EasySCP_Registry::get('Config');

	$query = "
		SELECT
			*
		FROM
			`htaccess_users`
		WHERE
			`dmn_id` = ?
		ORDER BY
			`dmn_id` DESC
	";

	$rs = exec_query($sql, $query, $dmn_id);

	if ($rs->recordCount() == 0) {
		$tpl->assign(
				array(
					'USER_MESSAGE'	=>	tr('You have no users!')
				)
			);
	} else {
		while (!$rs->EOF) {
			$tpl->append(
				array(
					'UNAME'					=> tohtml($rs->fields['uname']),
					'USTATUS'				=> translate_dmn_status($rs->fields['status']),
					'USER_ID'				=> $rs->fields['id'],
					'USER_DELETE'			=> tr('Delete'),
					'USER_DELETE_SCRIPT'	=> ($rs->fields['status'] === $cfg->ITEM_OK_STATUS) ? "action_delete('protected_user_delete.php?uname=".$rs->fields['id']."', '".tohtml($rs->fields['uname'])."')" : tr('N/A'),
					'USER_EDIT'				=> tr('Edit'),
					'USER_EDIT_SCRIPT'		=> "protected_user_edit.php?uname=".tohtml($rs->fields['id'])
				)
			);

			$rs->moveNext();

		}
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $dmn_id
 */
function gen_pgroups($tpl, $sql, &$dmn_id) {
	$cfg = EasySCP_Registry::get('Config');

	$query = "
		SELECT
			*
		FROM
			`htaccess_groups`
		WHERE
			`dmn_id` = ?
		ORDER BY
			`dmn_id` DESC
	";

	$rs = exec_query($sql, $query, $dmn_id);

	if ($rs->recordCount() == 0) {
		$tpl->assign('GROUP_MESSAGE', tr('You have no groups!'));
	} else {
		while (!$rs->EOF) {
			$tpl->append(
				array(
					'GNAME'					=> tohtml($rs->fields['ugroup']),
					'GSTATUS'				=> translate_dmn_status($rs->fields['status']),
					'GROUP_ID'				=> $rs->fields['id'],
					'GROUP_DELETE'			=> tr('Delete'),
					'GROUP_DELETE_SCRIPT'	=> ($rs->fields['status'] === $cfg->ITEM_OK_STATUS && $rs->fields['ugroup'] != $cfg->AWSTATS_GROUP_AUTH) ? "action_delete('protected_group_delete.php?gname=".$rs->fields['id']."', '".$rs->fields['ugroup']."')" : tr('N/A')
				)
			);

			if ($rs->fields['members'] != '') {
				$group_members = '';
				$members = explode(',', $rs->fields['members']);
				$cnt_members = count($members);

				for ($i = 0; $i < $cnt_members; $i++) {
					$query = "
						SELECT
							`uname`
						FROM
							`htaccess_users`
						WHERE
							`id` = ?
					";

					$rs_members = exec_query($sql, $query, $members[$i]);

					if ($cnt_members == 1 || $cnt_members == $i + 1) {
						$group_members .=  tohtml($rs_members->fields['uname']);
					} else {
						$group_members .=  tohtml($rs_members->fields['uname']) . ', ';
					}
				}
				$tpl->append('MEMBER', $group_members);
			} else {
				$tpl->append('MEMBER', '');
			}

			$rs->moveNext();
		}
	}
}
?>
