<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'admin/manage_reseller_owners.tpl';

// static page messages
update_reseller_owner();

gen_reseller_table($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Admin/Manage users/Reseller assignment'),
		'TR_RESELLER_ASSIGNMENT' => tr('Reseller assignment'),
		'TR_RESELLER_USERS'		=> tr('Reseller users'),
		'TR_NUMBER'				=> tr('No.'),
		'TR_MARK'				=> tr('Mark'),
		'TR_RESELLER_NAME'		=> tr('Reseller name'),
		'TR_OWNER'				=> tr('Owner'),
		'TR_TO_ADMIN'			=> tr('To Admin'),
		'TR_MOVE'				=> tr('Move')
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_users_manage.tpl');
gen_admin_menu($tpl, 'admin/menu_users_manage.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/**
 * @todo check if it's useful to have the table admin two times in the same query
 * @param EasySCP_TemplateEngine $tpl
 */
function gen_reseller_table($tpl) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			t1.`admin_id`, t1.`admin_name`, t2.`admin_name` AS created_by
		FROM
			`admin` AS t1,
			`admin` AS t2
		WHERE
			t1.`admin_type` = 'reseller'
		AND
			t1.`created_by` = t2.`admin_id`
		ORDER BY
			`created_by`,
			`admin_id`
	";

	$rs = exec_query($sql, $query);

	$i = 0;

	if ($rs->recordCount() == 0) {
		set_page_message(tr('Reseller list is empty!'), 'info');
	} else {
		while (!$rs->EOF) {

			$admin_id = $rs->fields['admin_id'];

			$admin_id_var_name = "admin_id_".$admin_id;

			$tpl->append(
				array(
					'NUMBER' => $i + 1,
					'RESELLER_NAME' => tohtml($rs->fields['admin_name']),
					'OWNER' => tohtml($rs->fields['created_by']),
					'CKB_NAME' => $admin_id_var_name,
				)
			);


			$rs->moveNext();

			$i++;
		}


		$tpl->assign('PAGE_MESSAGE', '');
	}

	$query = "
		SELECT
			`admin_id`, `admin_name`
		FROM
			`admin`
		WHERE
			`admin_type` = 'admin'
		ORDER BY
			`admin_name`
	";

	$rs = exec_query($sql, $query);

	while (!$rs->EOF) {

		if ((isset($_POST['uaction']) && $_POST['uaction'] === 'reseller_owner')
			&& (isset($_POST['dest_admin'])
				&& $_POST['dest_admin'] == $rs->fields['admin_id'])) {
			$selected = $cfg->HTML_SELECTED;
		} else {
			$selected = '';
		}

		$tpl->append(
			array(
				'OPTION'	=> tohtml($rs->fields['admin_name']),
				'VALUE'		=> $rs->fields['admin_id'],
				'SELECTED'	=> $selected
			)
		);


		$rs->moveNext();
	}


	$tpl->assign('PAGE_MESSAGE', '');
}

function update_reseller_owner() {
	$sql = EasySCP_Registry::get('Db');

	if (isset($_POST['uaction']) && $_POST['uaction'] === 'reseller_owner') {
		$query = "
			SELECT
				`admin_id`
			FROM
				`admin`
			WHERE
				`admin_type` = 'reseller'
			ORDER BY
				`admin_name`
		";

		$rs = execute_query($sql, $query);

		while (!$rs->EOF) {
			$admin_id = $rs->fields['admin_id'];

			$admin_id_var_name = "admin_id_$admin_id";

			if (isset($_POST[$admin_id_var_name]) && $_POST[$admin_id_var_name] === 'on') {
				$dest_admin = $_POST['dest_admin'];

				$query = "
					UPDATE
						`admin`
					SET
						`created_by` = ?
					WHERE
						`admin_id` = ?
				";

				exec_query($sql, $query, array($dest_admin, $admin_id));
			}

			$rs->moveNext();
		}
	}
}
?>