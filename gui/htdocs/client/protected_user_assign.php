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
$template = 'client/protected_user_assign.tpl';

$dmn_id = get_user_domain_id($_SESSION['user_id']);

add_user_to_group($tpl, $sql, $dmn_id);

delete_user_from_group($tpl, $sql, $dmn_id);

gen_user_assign($tpl, $sql, $dmn_id);

// static page messages
gen_logged_from($tpl);
check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('Protected areas'),
		'TR_DELETE'				=> tr('Delete'),
		'TR_USER_ASSIGN'		=> tr('User assign'),
		'TR_ALLREADY'			=> tr('Already in:'),
		'TR_MEMBER_OF_GROUP'	=> tr('Member of group:'),
		'TR_BACK'				=> tr('Back'),
		'TR_REMOVE'				=> tr('Remove'),
		'TR_ADD'				=> tr('Add'),
		'TR_SELECT_GROUP'		=> tr('Select group:')
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

/*
 * functions
 */

function get_htuser_name($sql, &$uuser_id, &$dmn_id) {
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

	if ($rs->recordCount() == 0) {
		user_goto('protected_user_manage.php');
	} else {
		return $rs->fields['uname'];
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $dmn_id
 */
function gen_user_assign($tpl, $sql, &$dmn_id) {
	if (isset($_GET['uname'])
			&& $_GET['uname'] !== ''
			&& is_numeric($_GET['uname'])) {
		$uuser_id = $_GET['uname'];

		$tpl->assign('UNAME', tohtml(get_htuser_name($sql, $uuser_id, $dmn_id)));
		$tpl->assign('UID', $uuser_id);
	} else if (isset($_POST['nadmin_name'])
			&& !empty($_POST['nadmin_name'])
			&& is_numeric($_POST['nadmin_name'])) {
		$uuser_id = $_POST['nadmin_name'];

		$tpl->assign('UNAME', tohtml(get_htuser_name($sql, $uuser_id, $dmn_id)));
		$tpl->assign('UID', $uuser_id);
	} else {
		user_goto('protected_user_manage.php');
	}
	// get groups
	$query = "
		SELECT
			*
		FROM
			`htaccess_groups`
		WHERE
			`dmn_id` = ?
	";

	$rs = exec_query($sql, $query, $dmn_id);

	if ($rs->recordCount() == 0) {
		set_page_message(tr('You have no groups!'), 'info');
		user_goto('protected_user_manage.php');
	} else {
		$added_in = 0;
		$not_added_in = 0;

		while (!$rs->EOF) {
			$group_id = $rs->fields['id'];
			$group_name = $rs->fields['ugroup'];
			$members = $rs->fields['members'];

			$members = explode(",", $members);
			$grp_in = 0;
			// let's generete all groups wher the user is assigned
			for ($i = 0, $cnt_members = count($members); $i < $cnt_members; $i++) {
				if ($uuser_id == $members[$i]) {
					$tpl->append(
						array(
							'GRP_IN' => tohtml($group_name),
							'GRP_IN_ID' => $group_id,
						)
					);

					$grp_in = $group_id;
					$added_in++;
				}
			}
			if ($grp_in !== $group_id) {
				$tpl->append(
					array(
						'GRP_NAME' => tohtml($group_name),
						'GRP_ID' => $group_id,
					)
				);
				$not_added_in++;
			}

			$rs->moveNext();
		}
		// generate add/remove buttons
		if ($added_in != 0) {
			$tpl->assign('IN_GROUP', true);
		}
		if ($not_added_in != 0) {
			$tpl->assign('NOT_IN_GROUP', true);
		}
	}
}

function add_user_to_group($tpl, $sql, &$dmn_id) {

	$cfg = EasySCP_Registry::get('Config');

	if (isset($_POST['uaction']) && $_POST['uaction'] == 'add'
			&& isset($_POST['groups']) && !empty($_POST['groups'])
			&& isset($_POST['nadmin_name']) && is_numeric($_POST['groups'])
			&& is_numeric($_POST['nadmin_name'])) {
		$uuser_id = clean_input($_POST['nadmin_name']);
		$group_id = $_POST['groups'];

		$query = "
			SELECT
				`id`,
				`ugroup`,
				`members`
			FROM
				`htaccess_groups`
			WHERE
				`dmn_id` = ?
			AND
				`id` = ?
		";

		$rs = exec_query($sql, $query, array($dmn_id, $group_id));

		$members = $rs->fields['members'];
		if ($members == '') {
			$members = $uuser_id;
		} else {
			$members = $members . "," . $uuser_id;
		}

		$change_status = $cfg->ITEM_CHANGE_STATUS;

		$update_query = "
			UPDATE
				`htaccess_groups`
			SET
				`members` = ?,
				`status` = ?
			WHERE
				`id` = ?
			AND
				`dmn_id` = ?
		";

		exec_query($sql, $update_query, array($members, $change_status, $group_id, $dmn_id));

		send_request('110 DOMAIN htaccess ' . $dmn_id);
		set_page_message(
			tr('User was assigned to the %s group', $rs->fields['ugroup']),
			'info'
		);
	} else {
		return;
	}
}

function delete_user_from_group($tpl, $sql, &$dmn_id) {

	$cfg = EasySCP_Registry::get('Config');

	if (isset($_POST['uaction']) && $_POST['uaction'] == 'remove'
			&& isset($_POST['groups_in']) && !empty($_POST['groups_in'])
			&& isset($_POST['nadmin_name']) && is_numeric($_POST['groups_in'])
			&& is_numeric($_POST['nadmin_name'])) {
		$group_id = $_POST['groups_in'];
		$uuser_id = clean_input($_POST['nadmin_name']);

		$query = "
			SELECT
				`id`,
				`ugroup`,
				`members`
			FROM
				`htaccess_groups`
			WHERE
				`dmn_id` = ?
			AND
				`id` = ?
		";

		$rs = exec_query($sql, $query, array($dmn_id, $group_id));

		$members = explode(',', $rs->fields['members']);
		$key = array_search($uuser_id, $members);
		if ($key !== false) {
			unset($members[$key]);
			$members = implode(",", $members);
			$change_status = $cfg->ITEM_CHANGE_STATUS;
			$update_query = "
				UPDATE
					`htaccess_groups`
				SET
					`members` = ?,
					`status` = ?
				WHERE
					`id` = ?
				AND
					`dmn_id` = ?
			";

			exec_query($sql, $update_query, array($members, $change_status, $group_id, $dmn_id));
			send_request('110 DOMAIN htaccess ' . $dmn_id);

			set_page_message(
				tr('User was deleted from the %s group.', $rs->fields['ugroup']),
				'info'
			);
		} else {
			return;
		}
	} else {
		return;
	}
}

// ** end of funcfions
?>