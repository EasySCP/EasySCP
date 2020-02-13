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
$template = 'admin/manage_reseller_users.tpl';

// static page messages
update_reseller_user();

gen_user_table($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP - Admin/Manage users/User assignment'),
		'TR_USER_ASSIGNMENT' => tr('User assignment'),
		'TR_RESELLER_USERS'	=> tr('Users'),
		'TR_NUMBER'			=> tr('No.'),
		'TR_MARK'			=> tr('Mark'),
		'TR_USER_NAME'		=> tr('User name'),
		'TR_FROM_RESELLER'	=> tr('From reseller'),
		'TR_TO_RESELLER'	=> tr('To reseller'),
		'TR_MOVE'			=> tr('Move')
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
 * @param EasySCP_TemplateEngine $tpl
 */
function gen_user_table($tpl) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			`admin_id`, `admin_name`
		FROM
			`admin`
		WHERE
			`admin_type` = 'reseller'
		ORDER BY
			`admin_name`
	";

	$rs = exec_query($sql, $query);

	if ($rs->recordCount() == 0) {
		set_page_message(tr('Reseller or user list is empty!'), 'info');
		user_goto('manage_users.php');
	}

	$reseller_id = $rs->fields['admin_id'];
	$all_resellers = array();

	while (!$rs->EOF) {

		if ((isset($_POST['uaction']) && $_POST['uaction'] === 'change_src')
			&& (isset($_POST['src_reseller']) && $_POST['src_reseller'] == $rs->fields['admin_id'])) {
			$selected = $cfg->HTML_SELECTED;
			$reseller_id = $_POST['src_reseller'];
		} else if ((isset($_POST['uaction']) && $_POST['uaction'] === 'move_user')
			&& (isset($_POST['dst_reseller']) && $_POST['dst_reseller'] == $rs->fields['admin_id'])) {
			$selected = $cfg->HTML_SELECTED;
			$reseller_id = $_POST['dst_reseller'];
		} else {
			$selected = '';
		}

		$all_resellers[] = $rs->fields['admin_id'];

		$tpl->append(
			array(
				'SRC_RSL_OPTION'	=> tohtml($rs->fields['admin_name']),
				'SRC_RSL_VALUE'		=> $rs->fields['admin_id'],
				'SRC_RSL_SELECTED'	=> $selected,
			)
		);

		$tpl->append(
			array(
				'DST_RSL_OPTION'	=> tohtml($rs->fields['admin_name']),
				'DST_RSL_VALUE'		=> $rs->fields['admin_id'],
				'DST_RSL_SELECTED'	=> ''
			)
		);

		$rs->moveNext();
	}

	if (isset($_POST['src_reseller']) && $_POST['src_reseller'] == 0) {
		$selected = $cfg->HTML_SELECTED;
		$reseller_id = 0;
	} else {
		$selected = '';
	}

	$tpl->append(
		array(
			'SRC_RSL_OPTION'	=> tr("N/A"),
			'SRC_RSL_VALUE'		=> 0,
			'SRC_RSL_SELECTED'	=> $selected,
		)
	);

	if ($reseller_id === 0) {
		$query = "
			SELECT
				`admin_id`, `admin_name`
			FROM
				`admin`
			WHERE
				`admin_type` = 'user'
			AND
				`created_by` NOT IN (?)
			ORDER BY
				`admin_name`
		";
		$not_in = implode(',', $all_resellers);
		$rs = exec_query($sql, $query, $not_in);
	} else {
		$query = "
			SELECT
				`admin_id`, `admin_name`
			FROM
				`admin`
			WHERE
				`admin_type` = 'user'
			AND
				`created_by` = ?
			ORDER BY
				`admin_name`
		";
		$rs = exec_query($sql, $query, $reseller_id);
	}


	if ($rs->recordCount() == 0) {
		set_page_message(tr('User list is empty!'), 'info');

		$tpl->assign('RESELLER_LIST', '');
	} else {
		$i = 0;
		while (!$rs->EOF) {
			$admin_id = $rs->fields['admin_id'];

			$admin_id_var_name = 'admin_id_' . $admin_id;

			$show_admin_name = decode_idna($rs->fields['admin_name']);

			$tpl->append(
				array(
					'NUMBER' => $i + 1,
					'USER_NAME' => tohtml($show_admin_name),
					'CKB_NAME' => $admin_id_var_name,
				)
			);

			$rs->moveNext();

			$i++;
		}
	}
}

function update_reseller_user() {

	if (isset($_POST['uaction'])
		&& $_POST['uaction'] === 'move_user'
		&& check_user_data()) {
		set_page_message(tr('User was moved'), 'success');
	}
}

function check_user_data() {
	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			`admin_id`
		FROM
			`admin`
		WHERE
			`admin_type` = 'user'
		ORDER BY
			`admin_name`
	";

	$rs = exec_query($sql, $query);

	$selected_users = '';

	while (!$rs->EOF) {
		$admin_id = $rs->fields['admin_id'];

		$admin_id_var_name = 'admin_id_' . $admin_id;

		if (isset($_POST[$admin_id_var_name])
			&& $_POST[$admin_id_var_name] === 'on') {
			$selected_users .= $rs->fields['admin_id'] . ';';
		}

		$rs->Movenext();
	}

	if ($selected_users == '') {
		set_page_message(tr('Please select at least one user!'), 'warning');

		return false;
	} else if ($_POST['src_reseller'] == $_POST['dst_reseller']) {
		set_page_message(
			tr('Source and destination reseller are the same!'),
			'error'
		);

		return false;
	}

	$dst_reseller = $_POST['dst_reseller'];

	$query = "
		SELECT
			`reseller_ips`
		FROM
			`reseller_props`
		WHERE
			`reseller_id` = ?
	";

	$rs = exec_query($sql, $query, $dst_reseller);

	$mru_error = '_off_';

	$dest_reseller_ips = $rs->fields['reseller_ips'];

	check_ip_sets($dest_reseller_ips, $selected_users, $mru_error);

	if ($mru_error == '_off_') {
		manage_reseller_limits($_POST['dst_reseller'], $_POST['src_reseller'], $selected_users, $mru_error);
	}

	if ($mru_error != '_off_') {
		set_page_message($mru_error, 'error');

		return false;
	}

	return true;
}

function manage_reseller_limits($dest_reseller, $src_reseller, $users, &$err) {

	$sql = EasySCP_Registry::get('Db');

	list($dest_dmn_current, $dest_dmn_max,
		$dest_sub_current, $dest_sub_max,
		$dest_als_current, $dest_als_max,
		$dest_mail_current, $dest_mail_max,
		$dest_ftp_current, $dest_ftp_max,
		$dest_sql_db_current, $dest_sql_db_max,
		$dest_sql_user_current, $dest_sql_user_max,
		$dest_traff_current, $dest_traff_max,
		$dest_disk_current, $dest_disk_max
	) = generate_reseller_props($dest_reseller);

	list($src_dmn_current, $src_dmn_max,
		$src_sub_current, $src_sub_max,
		$src_als_current, $src_als_max,
		$src_mail_current, $src_mail_max,
		$src_ftp_current, $src_ftp_max,
		$src_sql_db_current, $src_sql_db_max,
		$src_sql_user_current, $src_sql_user_max,
		$src_traff_current, $src_traff_max,
		$src_disk_current, $src_disk_max
	) = generate_reseller_props($src_reseller);

	$users_array = explode(";", $users);

	for ($i = 0, $cnt_users_array = count($users_array) - 1; $i < $cnt_users_array; $i++) {
		$query = "
			SELECT
				`domain_id`, `domain_name`
			FROM
				`domain`
			WHERE
				`domain_admin_id` = ?
		";

		$rs = exec_query($sql, $query, $users_array[$i]);

		$domain_name = $rs->fields['domain_name'];

		$domain_id = $rs->fields['domain_id'];

		list(, $sub_max,
			, $als_max,
			, $mail_max,
			, $ftp_max,
			, $sql_db_max,
			, $sql_user_max,
			$traff_max, $disk_max
		) = generate_user_props($domain_id);

		calculate_reseller_dvals($dest_dmn_current, $dest_dmn_max, $src_dmn_current, $src_dmn_max, 1, $err, 'Domain', $domain_name);

		if ($err == '_off_') {
			calculate_reseller_dvals($dest_sub_current, $dest_sub_max, $src_sub_current, $src_sub_max, $sub_max, $err, 'Subdomain', $domain_name);
			calculate_reseller_dvals($dest_als_current, $dest_als_max, $src_als_current, $src_als_max, $als_max, $err, 'Alias', $domain_name);
			calculate_reseller_dvals($dest_mail_current, $dest_mail_max, $src_mail_current, $src_mail_max, $mail_max, $err, 'Mail', $domain_name);
			calculate_reseller_dvals($dest_ftp_current, $dest_ftp_max, $src_ftp_current, $src_ftp_max, $ftp_max, $err, 'FTP', $domain_name);
			calculate_reseller_dvals($dest_sql_db_current, $dest_sql_db_max, $src_sql_db_current, $src_sql_db_max, $sql_db_max, $err, 'SQL Database', $domain_name);
			calculate_reseller_dvals($dest_sql_user_current, $dest_sql_user_max, $src_sql_user_current, $src_sql_user_max, $sql_user_max, $err, 'SQL User', $domain_name);
			calculate_reseller_dvals($dest_traff_current, $dest_traff_max, $src_traff_current, $src_traff_max, $traff_max, $err, 'Traffic', $domain_name);
			calculate_reseller_dvals($dest_disk_current, $dest_disk_max, $src_disk_current, $src_disk_max, $disk_max, $err, 'Disk', $domain_name);
		}

		if ($err != '_off_') {
			return false;
		}
	}

	// Let's Make Necessary Updates;

	$src_reseller_props = "$src_dmn_current;$src_dmn_max;";
	$src_reseller_props .= "$src_sub_current;$src_sub_max;";
	$src_reseller_props .= "$src_als_current;$src_als_max;";
	$src_reseller_props .= "$src_mail_current;$src_mail_max;";
	$src_reseller_props .= "$src_ftp_current;$src_ftp_max;";
	$src_reseller_props .= "$src_sql_db_current;$src_sql_db_max;";
	$src_reseller_props .= "$src_sql_user_current;$src_sql_user_max;";
	$src_reseller_props .= "$src_traff_current;$src_traff_max;";
	$src_reseller_props .= "$src_disk_current;$src_disk_max;";

	update_reseller_props($src_reseller, $src_reseller_props);

	$dest_reseller_props = "$dest_dmn_current;$dest_dmn_max;";
	$dest_reseller_props .= "$dest_sub_current;$dest_sub_max;";
	$dest_reseller_props .= "$dest_als_current;$dest_als_max;";
	$dest_reseller_props .= "$dest_mail_current;$dest_mail_max;";
	$dest_reseller_props .= "$dest_ftp_current;$dest_ftp_max;";
	$dest_reseller_props .= "$dest_sql_db_current;$dest_sql_db_max;";
	$dest_reseller_props .= "$dest_sql_user_current;$dest_sql_user_max;";
	$dest_reseller_props .= "$dest_traff_current;$dest_traff_max;";
	$dest_reseller_props .= "$dest_disk_current;$dest_disk_max;";

	update_reseller_props($dest_reseller, $dest_reseller_props);

	for ($i = 0, $cnt_users_array = count($users_array) - 1; $i < $cnt_users_array; $i++) {
		$query = "UPDATE `admin` SET `created_by` = ? WHERE `admin_id` = ?";
		exec_query($sql, $query, array($dest_reseller, $users_array[$i]));

		$query = "UPDATE `domain` SET `domain_created_id` = ? WHERE `domain_admin_id` = ?";
		exec_query($sql, $query, array($dest_reseller, $users_array[$i]));
	}

	return true;
}

function calculate_reseller_dvals(&$dest, $dest_max, &$src, $src_max, $umax, &$err, $obj, $uname) {
	if ($dest_max == 0 && $src_max == 0 && $umax == -1) {
		return;
	} else if ($dest_max == 0 && $src_max == 0 && $umax == 0) {
		return;
	} else if ($dest_max == 0 && $src_max == 0 && $umax > 0) {
		$src -= $umax;

		$dest += $umax;

		return;
	} else if ($dest_max == 0 && $src_max > 0 && $umax == -1) {
		return;
	} else if ($dest_max == 0 && $src_max > 0 && $umax == 0) {
		// Impossible condition;
		return;
	} else if ($dest_max == 0 && $src_max > 0 && $umax > 0) {
		$src -= $umax;

		$dest += $umax;

		return;
	} else if ($dest_max > 0 && $src_max == 0 && $umax == -1) {
		return;
	} else if ($dest_max > 0 && $src_max == 0 && $umax == 0) {
		if ($err == '_off_') {
			$err = '';
		}
		$err .= tr('<strong>%1$s</strong> has unlimited rights for a <strong>%2$s</strong> Service !<br>', $uname, $obj);

		$err .= tr('You cannot move <strong>%1$s</strong> in a destination reseller,<br>which has limits for the <strong>%2$s</strong> service!', $uname, $obj);

		return;
	} else if ($dest_max > 0 && $src_max == 0 && $umax > 0) {
		if ($dest + $umax > $dest_max) {
			if ($err == '_off_') {
				$err = '';
			}
			$err .= tr('<strong>%1$s</strong> is exceeding limits for a <strong>%2$s</strong><br>service in destination reseller!<br>', $uname, $obj);

			$err .= tr('Moving aborted!');
		} else {
			$src -= $umax;

			$dest += $umax;
		}

		return;
	} else if ($dest_max > 0 && $src_max > 0 && $umax == -1) {
		return;
	} else if ($dest_max > 0 && $src_max > 0 && $umax == 0) {
		// Impossible condition;
		return;
	} else if ($dest_max > 0 && $src_max > 0 && $umax > 0) {
		if ($dest + $umax > $dest_max) {
			if ($err == '_off_') {
				$err = '';
			}
			$err .= tr('<strong>%1$s</strong> is exceeding limits for a <strong>%2$s</strong><br>service in destination reseller!<br>', $uname, $obj);

			$err .= tr('Moving aborted!');
		} else {
			$src -= $umax;

			$dest += $umax;
		}

		return;
	}
}

function check_ip_sets($dest, $users, &$err) {

	$sql = EasySCP_Registry::get('Db');

	$users_array = explode(";", $users);

	for ($i = 0, $cnt_users_array = count($users_array); $i < $cnt_users_array; $i++) {
		$query = "
			SELECT
				`domain_name`, `domain_ip_id`
			FROM
				`domain`
			WHERE
				`domain_admin_id` = ?
		";

		$rs = exec_query($sql, $query, $users_array[$i]);

		$domain_ip_id = $rs->fields['domain_ip_id'];

		$domain_name = $rs->fields['domain_name'];

		if (!preg_match("/$domain_ip_id;/", $dest)) {
			if ($err == '_off_') {
				$err = '';
			}
			$err .= tr('<strong>%s</strong> has IP address that cannot be managed from the destination reseller !<br>This user cannot be moved!', $domain_name);

			return false;
		}
	}

	return true;
}
?>