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
$template = 'admin/user_delete.tpl';

if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
	if (validate_user_deletion(intval($_GET['delete_id']))) {
		delete_user(intval($_GET['delete_id']));
	} else {
		user_goto('manage_users.php');
	}
} else if (isset($_GET['domain_id']) && is_numeric($_GET['domain_id'])) {
	validate_domain_deletion($tpl, intval($_GET['domain_id']));
} else if (isset($_POST['domain_id']) && is_numeric($_POST['domain_id'])
	&& isset($_POST['delete']) && $_POST['delete'] == 1) {
	delete_domain((int)$_POST['domain_id'], 'manage_users.php');
} else {
	if (isset($_POST['domain_id']) && is_numeric($_POST['domain_id'])) {
		set_page_message(tr('Domain deletion was aborted.'), 'info');
	} else {
		set_page_message(tr('Invlaid domain ID!'), 'error');
	}
	user_goto('manage_users.php');
}

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('EasySCP - Delete Domain')
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_users_manage.tpl');
gen_admin_menu($tpl, 'admin/menu_users_manage.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

/**
 * Delete user
 * @param integer $user_id User ID to delete
 */
function delete_user($user_id) {

	global $sql;
	$cfg = EasySCP_Registry::get('Config');

	$query = "
		SELECT
			`admin_type`
		FROM
			`admin`
		WHERE
			`admin_id` = ?
	;";
	$res = exec_query($sql, $query, $user_id);
	$data = $res->fetchRow();
	$type = $data['admin_type'];
	if (empty($type) || $type == 'user') {
		set_page_message(tr('Invalid user ID!'), 'error');
		user_goto('manage_users.php');
	}

	if ($type == 'reseller') {
		// delete reseller props
		$query = "DELETE FROM `reseller_props` WHERE `reseller_id` = ?";
		exec_query($sql, $query, $user_id);
		// delete hosting plans
		$query = "DELETE FROM `hosting_plans` WHERE `reseller_id` = ?";
		exec_query($sql, $query, $user_id);
	}

	// Delete user_gui_props:
	$query = "DELETE FROM `user_gui_props` WHERE `user_id` = ?";
	exec_query($sql, $query, $user_id);

	// Delete EasySCP login:
	$query = "DELETE FROM `admin` WHERE `admin_id` = ?";
	exec_query($sql, $query, $user_id);

	write_log($_SESSION['user_logged'] .": deletes user " . $user_id);

	$_SESSION['ddel'] = '_yes_';
	user_goto('manage_users.php');
}

/**
 * Validate if delete process is valid
 * @param integer $user_id User-ID to delete
 * @return boolean true = deletion can be done
 */
function validate_user_deletion($user_id) {

	global $sql;

	$result = false;

	// check if there are domains created by user
	$query = "SELECT COUNT(`domain_id`) AS `num_domains` FROM `domain` WHERE `domain_created_id` = ?;";
	$res = exec_query($sql, $query, $user_id);
	$data = $res->fetchRow();
	if ($data['num_domains'] == 0) {
		$query = "SELECT `admin_type` FROM `admin` WHERE `admin_id` = ?;";
		$res = exec_query($sql, $query, $user_id);
		$data = $res->fetchRow();
		$type = $data['admin_type'];
		if ($type == 'admin' || $type == 'reseller') {
			$result = true;
		} else {
			set_page_message(tr('Invalid user ID!'), 'error');
		}
	} else {
		set_page_message(tr('There are active domains of reseller/admin!'), 'error');
	}

	return $result;
}

/**
 * Validate domain deletion, display all items to delete
 * @param EasySCP_TemplateEngine $tpl
 * @param integer $domain_id
 */
function validate_domain_deletion($tpl, $domain_id) {

	$sql = EasySCP_Registry::get('Db');

	// check for domain owns
	$query = "SELECT `domain_id`, `domain_name`, `domain_created_id` FROM `domain` WHERE `domain_id` = ?;";
	$res = exec_query($sql, $query, $domain_id);
	$data = $res->fetchRow();
	if ($data['domain_id'] == 0) {
		set_page_message(tr('Wrong domain ID!'), 'error');
		user_goto('manage_users.php');
	}

	$tpl->assign(
		array(
			'TR_DELETE_DOMAIN'					=> tr('Delete domain'),
			'TR_DOMAIN_SUMMARY'					=> tr('Domain summary:'),
			'TR_DOMAIN_EMAILS'					=> tr('Domain e-mails:'),
			'TR_DOMAIN_FTPS'					=> tr('Domain FTP accounts:'),
			'TR_DOMAIN_ALIASES'					=> tr('Domain aliases:'),
			'TR_DOMAIN_SUBS'					=> tr('Domain subdomains:'),
			'TR_DOMAIN_DBS'						=> tr('Domain databases:'),
			'TR_REALLY_WANT_TO_DELETE_DOMAIN'	=> tr('Do you really want to delete the entire domain? This operation cannot be undone!'),
			'TR_BUTTON_DELETE'					=> tr('Delete domain'),
			'TR_YES_DELETE_DOMAIN'				=> tr('Yes, delete the domain.'),
			'DOMAIN_NAME'						=> tohtml($data['domain_name']),
			'DOMAIN_ID'							=> $data['domain_id']
		)
	);

	// check for mail acc in MAIN domain
	$query = "SELECT * FROM `mail_users` WHERE `domain_id` = ?";
	$res = exec_query($sql, $query, $domain_id);
	if (!$res->EOF) {
		while (!$res->EOF) {

			// Create mail type's text
			$mail_types = explode(',', $res->fields['mail_type']);
			$mdisplay_a = array();
			foreach ($mail_types as $mtype) {
				$mdisplay_a[] = user_trans_mail_type($mtype);
			}
			$mdisplay_txt = implode(', ', $mdisplay_a);

			$tpl->append(
				array(
					'MAIL_ADDR' => tohtml($res->fields['mail_addr']),
					'MAIL_TYPE' => $mdisplay_txt
				)
			);

			$res->moveNext();
		}
	}

	// check for ftp acc in MAIN domain
	$query = "SELECT `ftp_users`.* FROM `ftp_users`, `domain` WHERE `domain`.`domain_id` = ? AND `ftp_users`.`uid` = `domain`.`domain_uid`;";
	$res = exec_query($sql, $query, $domain_id);
	if (!$res->EOF) {
		while (!$res->EOF) {

			$tpl->append(
				array(
					'FTP_USER' => tohtml($res->fields['userid']),
					'FTP_HOME' => tohtml($res->fields['homedir'])
				)
			);

			$res->moveNext();
		}
	}

	// check for alias domains
	$alias_a = array();
	$query = "SELECT * FROM `domain_aliasses` WHERE `domain_id` = ?";
	$res = exec_query($sql, $query, $domain_id);
	if (!$res->EOF) {
		while (!$res->EOF) {
			$alias_a[] = $res->fields['alias_id'];

			$tpl->append(
				array(
					'ALS_NAME'	=> tohtml($res->fields['alias_name']),
					'ALS_MNT'	=> tohtml($res->fields['alias_mount'])
				)
			);

			$res->moveNext();
		}
	}

	// check for subdomains
	$any_sub_found = false;
	$query = "SELECT * FROM `subdomain` WHERE `domain_id` = ?";
	$res = exec_query($sql, $query, $domain_id);
	while (!$res->EOF) {
		$any_sub_found = true;
		$tpl->append(
			array(
				'SUB_NAME'	=> tohtml($res->fields['subdomain_name']),
				'SUB_MNT'	=> tohtml($res->fields['subdomain_mount'])
			)
		);

		$res->moveNext();
	}

	// Check subdomain_alias
	if (count($alias_a) > 0) {
		$query = "SELECT * FROM `subdomain_alias` WHERE `alias_id` IN (";
		$query .= implode(',', $alias_a);
		$query .= ")";
		$res = exec_query($sql, $query);
		while (!$res->EOF) {
			$tpl->append(
				array(
					'SUB_NAME'	=> tohtml($res->fields['subdomain_alias_name']),
					'SUB_MNT'	=> tohtml($res->fields['subdomain_alias_mount'])
				)
			);

			$res->moveNext();
		}
	}

	// Check for databases and -users
	$query = "SELECT * FROM `sql_database` WHERE `domain_id` = ?;";
	$res = exec_query($sql, $query, $domain_id);
	if (!$res->EOF) {

		while (!$res->EOF) {

			$query = "SELECT * FROM `sql_user` WHERE `sqld_id` = ?;";
			$ures = exec_query($sql, $query, $res->fields['sqld_id']);

			$users_a = array();
			while (!$ures->EOF) {
				$users_a[] = $ures->fields['sqlu_name'];
				$ures->moveNext();
			}
			$users_txt = implode(', ', $users_a);

			$tpl->append(
				array(
					'DB_NAME'	=> tohtml($res->fields['sqld_name']),
					'DB_USERS'	=> tohtml($users_txt)
				)
			);

			$res->moveNext();
		}
	}
}
?>