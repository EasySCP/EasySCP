<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2017 by Easy Server Control Panel - http://www.easyscp.net
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

if (isset($_GET['edit_id'])) {
	$edit_id = $_GET['edit_id'];
} else if (isset($_POST['edit_id'])) {
	$edit_id = $_POST['edit_id'];
} else {
	user_goto('manage_users.php');
}

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'admin/admin_edit.tpl';

if ($edit_id == $_SESSION['user_id']) {
	user_goto('personal_change.php');
}

if (isset($_POST['uaction']) && $_POST['uaction'] === 'edit_user') {
	update_data($edit_id);
}

$sql_param = array(
	':admin_id' => $edit_id
);

$sql_query = "
	SELECT
		`admin_name`,
		`admin_type`,
		`fname`,
		`lname`,
		`firm`,
		`zip`,
		`city`,
		`state`,
		`country`,
		`phone`,
		`fax`,
		`street1`,
		`street2`,
		`email`,
		`gender`
	FROM
		`admin`
	WHERE
		`admin_id` = :admin_id;
";

DB::prepare($sql_query);
$row = DB::execute($sql_param, true);

if ($row === false) {
	user_goto('manage_users.php');
}

if (isset($_POST['genpass'])) {
	$tpl->assign('VAL_PASSWORD', passgen());
} else {
	$tpl->assign('VAL_PASSWORD', '');
}

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'					=> ($row['admin_type'] == 'admin' ? tr('EasySCP - Admin/Manage users/Edit Administrator') : tr('EasySCP - Admin/Manage users/Edit User')),
		'TR_EMPTY_OR_WORNG_DATA'		=> tr('Empty data or wrong field!'),
		'TR_PASSWORD_NOT_MATCH'			=> tr("Passwords don't match!"),
		'TR_EDIT_ADMIN'					=> ($row['admin_type'] == 'admin' ? tr('Edit admin') : tr('Edit user')),
		'TR_CORE_DATA'					=> tr('Core data'),
		'TR_USERNAME'					=> tr('Username'),
		'TR_PASSWORD'					=> tr('Password'),
		'TR_PASSWORD_REPEAT'			=> tr('Repeat password'),
		'TR_EMAIL'						=> tr('Email'),
		'TR_ADDITIONAL_DATA'			=> tr('Additional data'),
		'TR_FIRST_NAME'					=> tr('First name'),
		'TR_LAST_NAME'					=> tr('Last name'),
		'TR_COMPANY'					=> tr('Company'),
		'TR_ZIP_POSTAL_CODE'			=> tr('Zip/Postal code'),
		'TR_CITY'						=> tr('City'),
		'TR_STATE_PROVINCE'				=> tr('State/Province'),
		'TR_COUNTRY'					=> tr('Country'),
		'TR_STREET_1'					=> tr('Street 1'),
		'TR_STREET_2'					=> tr('Street 2'),
		'TR_PHONE'						=> tr('Phone'),
		'TR_FAX'						=> tr('Fax'),
		'TR_GENDER'						=> tr('Gender'),
		'TR_MALE'						=> tr('Male'),
		'TR_FEMALE'						=> tr('Female'),
		'TR_UNKNOWN'					=> tr('Unknown'),
		'TR_UPDATE'						=> tr('Update'),
		'TR_SEND_DATA'					=> tr('Send new login data'),
		'TR_PASSWORD_GENERATE'			=> tr('Generate password'),
		'FIRST_NAME'					=> empty($row['fname']) ? '' : tohtml($row['fname']),
		'LAST_NAME'						=> empty($row['lname']) ? '' : tohtml($row['lname']),
		'FIRM'							=> empty($row['firm']) ? '' : tohtml($row['firm']),
		'ZIP'							=> empty($row['zip']) ? '' : tohtml($row['zip']),
		'CITY'							=> empty($row['city']) ? '' : tohtml($row['city']),
		'STATE_PROVINCE'				=> empty($row['state']) ? '' : tohtml($row['state']),
		'COUNTRY'						=> empty($row['country']) ? '' : tohtml($row['country']),
		'STREET_1'						=> empty($row['street1']) ? '' : tohtml($row['street1']),
		'STREET_2'						=> empty($row['street2']) ? '' : tohtml($row['street2']),
		'PHONE'							=> empty($row['phone']) ? '' : tohtml($row['phone']),
		'FAX'							=> empty($row['fax']) ? '' : tohtml($row['fax']),
		'USERNAME'						=> tohtml(decode_idna($row['admin_name'])),
		'EMAIL'							=> tohtml($row['email']),
		'VL_MALE'						=> (($row['gender'] === 'M') ? $cfg->HTML_SELECTED : ''),
		'VL_FEMALE'						=> (($row['gender'] === 'F') ? $cfg->HTML_SELECTED : ''),
		'VL_UNKNOWN'					=> ((($row['gender'] === 'U') || (empty($row['gender']))) ? $cfg->HTML_SELECTED : ''),
		'EDIT_ID'						=> $edit_id,
		// The entries below are for Demo versions only
		'PASSWORD_DISABLED'				=> tr('Password change is disabled!'),
		'DEMO_VERSION'					=> tr('Demo Version!')
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

function update_data($edit_id) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	if (check_user_data()) {
		$user_id	= $_SESSION['user_id'];
		$fname		= clean_input($_POST['fname']);
		$lname		= clean_input($_POST['lname']);
		$firm		= clean_input($_POST['firm']);
		$gender		= clean_input($_POST['gender']);
		$zip		= clean_input($_POST['zip']);
		$city		= clean_input($_POST['city']);
		$state		= clean_input($_POST['state']);
		$country	= clean_input($_POST['country']);
		$email		= clean_input($_POST['email']);
		$phone		= clean_input($_POST['phone']);
		$fax		= clean_input($_POST['fax']);
		$street1	= clean_input($_POST['street1']);
		$street2	= clean_input($_POST['street2']);

		if (empty($_POST['pass'])) {
			$query = "
				UPDATE
					`admin`
				SET
					`fname` = ?,
					`lname` = ?,
					`firm` = ?,
					`zip` = ?,
					`city` = ?,
					`state` = ?,
					`country` = ?,
					`email` = ?,
					`phone` = ?,
					`fax` = ?,
					`street1` = ?,
					`street2` = ?,
					`gender` = ?
				WHERE
					`admin_id` = ?
			";
			exec_query($sql, $query, array($fname,
					$lname,
					$firm,
					$zip,
					$city,
					$state,
					$country,
					$email,
					$phone,
					$fax,
					$street1,
					$street2,
					$gender,
					$edit_id));
		} else {
			$edit_id = $_POST['edit_id'];

			if ($_POST['pass'] != $_POST['pass_rep']) {
				set_page_message(
					tr("Entered passwords do not match!"),
					'warning'
				);

				user_goto('admin_edit.php?edit_id=' . $edit_id);
			}

			if (!chk_password($_POST['pass'])) {
				if ($cfg->PASSWD_STRONG) {
					set_page_message(
						sprintf(
							tr('The password must be at least %s chars long and contain letters and numbers to be valid.'),
							$cfg->PASSWD_CHARS
						),
						'warning'
					);
				} else {
					set_page_message(
						sprintf(
							tr('Password data is shorter than %s signs or includes not permitted signs!'),
							$cfg->PASSWD_CHARS
						),
						'warning'
					);
				}
				user_goto('admin_edit.php?edit_id=' . $edit_id);
			}

			$upass = crypt_user_pass($_POST['pass']);

			$query = "
				UPDATE
					`admin`
				SET
					`admin_pass` = ?,
					`fname` = ?,
					`lname` = ?,
					`firm` = ?,
					`zip` = ?,
					`city` = ?,
					`state` = ?,
					`country` = ?,
					`email` = ?,
					`phone` = ?,
					`fax` = ?,
					`street1` = ?,
					`street2` = ?,
					`gender` = ?
				WHERE
					`admin_id` = ?
			";

			exec_query($sql, $query, array($upass,
					$fname,
					$lname,
					$firm,
					$zip,
					$city,
					$state,
					$country,
					$email,
					$phone,
					$fax,
					$street1,
					$street2,
					$gender,
					$edit_id));

			// Kill any existing session of the edited user

			$admin_name = get_user_name($edit_id);
			$query = "
				DELETE FROM
					`login`
				WHERE
					`user_name` = ?
			";

			$rs = exec_query($sql, $query, $admin_name);
			if ($rs->recordCount() != 0) {
				set_page_message(tr('User session was killed!'), 'info');
				write_log($_SESSION['user_logged'] . " killed " . $admin_name . "'s session because of password change");
			}
		}

		$edit_username = clean_input($_POST['edit_username']);

		$user_logged = $_SESSION['user_logged'];

		write_log("$user_logged: changes data/password for $edit_username!");

		if (isset($_POST['send_data']) && !empty($_POST['pass'])) {
			$query = "SELECT admin_type FROM admin WHERE admin_id='" . addslashes(htmlspecialchars($edit_id)) . "'";

			$res = exec_query($sql, $query);

			if ($res->fields['admin_type'] == 'admin') {
				$admin_type = tr('Administrator');
			} else if ($res->fields['admin_type'] == 'reseller') {
				$admin_type = tr('Reseller');
			} else {
				$admin_type = tr('Domain account');
			}

			send_add_user_auto_msg ($user_id,
				$edit_username,
				clean_input($_POST['pass']),
				clean_input($_POST['email']),
				clean_input($_POST['fname']),
				clean_input($_POST['lname']),
				tr($admin_type),
				$gender);
		}

		$_SESSION['user_updated'] = 1;

		user_goto('manage_users.php');
	}
}

function check_user_data() {
	if (!chk_email($_POST['email'])) {
		set_page_message(tr('Incorrect email length or syntax!'), 'warning');

		return false;
	}

	return true;
}
?>
