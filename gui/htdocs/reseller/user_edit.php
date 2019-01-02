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

$cfg = EasySCP_Registry::get('Config');

check_login(__FILE__);

if (isset($_GET['edit_id'])) {
	$edit_id = $_GET['edit_id'];
} else if (isset($_POST['edit_id'])) {
	$edit_id = $_POST['edit_id'];
} else {
	user_goto('users.php?psi=last');
}

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'reseller/user_edit.tpl';

// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Users/Edit'),
		'TR_EDIT_USER'			=> tr('Edit user'),
		'TR_CORE_DATA'			=> tr('Core data'),
		'TR_USERNAME'			=> tr('Username'),
		'TR_PASSWORD'			=> tr('Password'),
		'TR_REP_PASSWORD'		=> tr('Repeat password'),
		'TR_DMN_IP'				=> tr('Domain IP'),
		'TR_USREMAIL'			=> tr('Email'),
		'TR_ADDITIONAL_DATA'	=> tr('Additional data'),
		'TR_CUSTOMER_ID'		=> tr('Customer ID'),
		'TR_FIRSTNAME'			=> tr('First name'),
		'TR_LASTNAME'			=> tr('Last name'),
		'TR_COMPANY'			=> tr('Company'),
		'TR_POST_CODE'			=> tr('Zip/Postal code'),
		'TR_CITY'				=> tr('City'),
		'TR_STATE'				=> tr('State/Province'),
		'TR_COUNTRY'			=> tr('Country'),
		'TR_STREET1'			=> tr('Street 1'),
		'TR_STREET2'			=> tr('Street 2'),
		'TR_MAIL'				=> tr('Email'),
		'TR_PHONE'				=> tr('Phone'),
		'TR_FAX'				=> tr('Fax'),
		'TR_GENDER'				=> tr('Gender'),
		'TR_MALE'				=> tr('Male'),
		'TR_FEMALE'				=> tr('Female'),
		'TR_UNKNOWN'			=> tr('Unknown'),
		'EDIT_ID'				=> $edit_id,
		'TR_BTN_ADD_USER'		=> tr('Submit changes'),
		'TR_MANAGE_USERS'		=> tr('Manage users'),
		'TR_USERS'				=> tr('Users'),
		'TR_NO'					=> tr('No.'),
		'TR_ACTION'				=> tr('Action'),
		'TR_BACK'				=> tr('Back'),
		'TR_TITLE_BACK'			=> tr('Return to previous menu'),
		'TR_TABLE_NAME'			=> tr('Users list'),
		'TR_SEND_DATA'			=> tr('Send new login data'),
		'TR_PASSWORD_GENERATE'	=> tr('Generate password'),

		// The entries below are for Demo versions only
		'PASSWORD_DISABLED'		=> tr('Password change is deactivated!'),
		'DEMO_VERSION'			=> tr('Demo Version!')
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_users_manage.tpl');
gen_reseller_menu($tpl, 'reseller/menu_users_manage.tpl');

if (isset($_POST['genpass'])) {
	$tpl->assign('VAL_PASSWORD', passgen());
} else {
	$tpl->assign('VAL_PASSWORD', '');
}

if (isset($_POST['Submit'])
	&& isset($_POST['uaction'])
	&& ('save_changes' === $_POST['uaction'])) {
	// Process data

	if (isset($_SESSION['edit_ID'])) {
		$hpid = $_SESSION['edit_ID'];
	} else {
		$_SESSION['edit'] = '_no_';
		user_goto('users.php?psi=last');
	}

	if (isset($_SESSION['user_name'])) {
		$dmn_user_name = $_SESSION['user_name'];
	} else {
		$_SESSION['edit'] = '_no_';
		user_goto('users.php?psi=last');
	}

	if (check_ruser_data($tpl, '_yes_')) { // Save data to db
		update_data_in_db($hpid);
	}

} else {
	// Get user id that comes for edit
	$hpid = $edit_id;
	load_user_data_page($hpid);
	$_SESSION['edit_ID'] = $hpid;

}
gen_edituser_page($tpl);
gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

/*
 * Begin function block
 */

/**
 * Load data from sql
 */
function load_user_data_page($user_id) {
	global $dmn_user_name;
	global $user_email, $customer_id, $first_name;
	global $last_name, $firm, $zip, $gender;
	global $city, $state, $country, $street_one;
	global $street_two, $mail, $phone;
	global $fax;

	$sql = EasySCP_Registry::get('Db');

	$reseller_id = $_SESSION['user_id'];

	$query = "
		SELECT
			`admin_name`, `created_by`, `fname`, `lname`, `firm`, `zip`,
			`city`, `state`, `country`, `email`, `phone`, `fax`, `street1`,
			`street2`, `customer_id`, `gender`
		FROM
			`admin`
		WHERE
			`admin_id` = ?
		AND
			`created_by` = ?
	";

	$res = exec_query($sql, $query, array($user_id, $reseller_id));
	$data = $res->fetchRow();

	if ($res->recordCount() == 0) {
		set_page_message(
			tr('User does not exist or you do not have permission to access this interface!'),
			'warning'
		);
		user_goto('users.php?psi=last');
	} else {
		// Get data from sql
		$_SESSION['user_name'] = $data['admin_name'];

		$dmn_user_name	= $data['admin_name'];
		$user_email		= $data['email'];
		$customer_id	= $data['customer_id'];
		$first_name		= $data['fname'];
		$last_name		= $data['lname'];
		$gender			= $data['gender'];
		$firm			= $data['firm'];
		$zip			= $data['zip'];
		$city			= $data['city'];
		$state			= $data['state'];
		$country		= $data['country'];
		$street_one		= $data['street1'];
		$street_two		= $data['street2'];
		$mail			= $data['email'];
		$phone			= $data['phone'];
		$fax			= $data['fax'];
	}

} // End of gen_load_ehp_page()


/**
 * Show user data
 * @param EasySCP_TemplateEngine $tpl
 */
function gen_edituser_page($tpl) {
	global $dmn_user_name, $user_email, $customer_id, $first_name, $last_name,
		$firm, $zip, $gender, $city, $state, $country, $street_one, $street_two,
		$phone, $fax;

	$cfg = EasySCP_Registry::get('Config');

	if ($customer_id == NULL) {
		$customer_id = '';
	}

	// Fill in the fields
	$tpl->assign(
		array(
			'VL_USERNAME' => tohtml(decode_idna($dmn_user_name)),
			'VL_MAIL' => empty($user_email) ? '' : tohtml($user_email),
			'VL_USR_ID' => empty($customer_id) ? '' : tohtml($customer_id),
			'VL_USR_NAME' => empty($first_name) ? '' : tohtml($first_name),
			'VL_LAST_USRNAME' => empty($last_name) ? '' : tohtml($last_name),
			'VL_USR_FIRM' => empty($firm) ? '' : tohtml($firm),
			'VL_USR_POSTCODE' => empty($zip) ? '' : tohtml($zip),
			'VL_USRCITY' => empty($city) ? '' : tohtml($city),
			'VL_USRSTATE' => empty($state) ? '' : tohtml($state),
			'VL_COUNTRY' => empty($country) ? '' : tohtml($country),
			'VL_STREET1' => empty($street_one) ? '' : tohtml($street_one),
			'VL_STREET2' => empty($street_two) ? '' : tohtml($street_two),
			'VL_MALE' => ($gender == 'M') ? $cfg->HTML_SELECTED : '',
			'VL_FEMALE' => ($gender == 'F') ? $cfg->HTML_SELECTED : '',
			'VL_UNKNOWN' => ($gender == 'U') ? $cfg->HTML_SELECTED : '',
			'VL_PHONE' => empty($phone) ? '' : tohtml($phone),
			'VL_FAX' => empty($fax) ? '' : tohtml($fax)
		)
	);

	generate_ip_list($tpl, $_SESSION['user_id']);

} // End of gen_edituser_page()


/**
 * Function to update changes into db
 */
function update_data_in_db($hpid) {

	global $dmn_user_name, $user_email, $customer_id, $first_name, $last_name,
		$firm, $zip, $gender, $city, $state, $country, $street_one, $street_two,
		$phone, $fax, $inpass, $admin_login;

	$sql = EasySCP_Registry::get('Db');
	$cfg = EasySCP_Registry::get('Config');

	$reseller_id = $_SESSION['user_id'];

	$first_name	= clean_input($first_name);
	$last_name	= clean_input($last_name);
	$firm		= clean_input($firm);
	$gender		= clean_input($gender);
	$zip		= clean_input($zip);
	$city		= clean_input($city);
	$state		= clean_input($state);
	$country	= clean_input($country);
	$phone		= clean_input($phone);
	$fax		= clean_input($fax);
	$street_one	= clean_input($street_one);
	$street_two	= clean_input($street_two);

	if (empty($inpass)) {
		// Save without password
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
				`gender` = ?,
				`customer_id` = ?
			WHERE
				`admin_id` = ?
			AND
				`created_by` = ?
		";
		exec_query($sql, $query, array(
			$first_name,
			$last_name,
			$firm,
			$zip,
			$city,
			$state,
			$country,
			$user_email,
			$phone,
			$fax,
			$street_one,
			$street_two,
			$gender,
			$customer_id,
			$hpid,
			$reseller_id)
		);
	} else {
		// Change password
		if (!chk_password($_POST['userpassword'])) {
			if (isset($cfg->PASSWD_STRONG)){
				set_page_message(
					sprintf(
						tr('The password must be at least %s chars long and contain letters and numbers to be valid.'), $cfg->PASSWD_CHARS
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
			user_goto('user_edit.php?edit_id=' . $hpid);
		}

		if ($_POST['userpassword'] != $_POST['userpassword_repeat']) {
			set_page_message(tr('Entered passwords do not match!'), 'warning');

			user_goto('user_edit.php?edit_id=' . $hpid);
		}
		$pure_user_pass = $inpass;

		$inpass = crypt_user_pass($inpass);

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
				`gender` = ?,
				`customer_id` = ?
			WHERE
				`admin_id` = ?
			AND
				`created_by` = ?
		";
		exec_query($sql, $query, array(
			$inpass,
			$first_name,
			$last_name,
			$firm,
			$zip,
			$city,
			$state,
			$country,
			$user_email,
			$phone,
			$fax,
			$street_one,
			$street_two,
			$gender,
			$customer_id,
			$hpid,
			$reseller_id)
		);

		// Kill any existing session of the edited user
		$admin_name = get_user_name($hpid);
		$query = "
			DELETE FROM
				`login`
			WHERE
				`user_name` = ?
		";

		$rs = exec_query($sql, $query, $admin_name);
			if ($rs->recordCount() != 0) {
				set_page_message(tr('User session was killed!'), 'info');
				write_log($_SESSION['user_logged'] . " killed ".$admin_name."'s session because of password change");
		}
	}

	$admin_login = $_SESSION['user_logged'];
	write_log("$admin_login changes data/password for $dmn_user_name!");

	if (isset($_POST['send_data']) && !empty($inpass)) {
		send_add_user_auto_msg(
			$reseller_id,
			$dmn_user_name,
			$pure_user_pass,
			$user_email,
			$first_name,
			$last_name,
			tr('Domain account')
		);
	}

	unset($_SESSION['edit_ID']);
	unset($_SESSION['user_name']);

	$_SESSION['edit'] = "_yes_";
	user_goto('users.php?psi=last');
} // End of update_data_in_db()
