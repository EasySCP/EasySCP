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
$template = 'client/personal_change.tpl';

if (isset($_POST['uaction']) && $_POST['uaction'] === 'updt_data') {
	update_user_personal_data($_SESSION['user_id']);
}

gen_user_personal_data($tpl, $_SESSION['user_id']);

// static page messages
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Client/Change Personal Data'),
		'TR_CHANGE_PERSONAL_DATA'	=> tr('Change personal data'),
		'TR_PERSONAL_DATA'			=> tr('Personal data'),
		'TR_FIRST_NAME'				=> tr('First name'),
		'TR_LAST_NAME'				=> tr('Last name'),
		'TR_COMPANY'				=> tr('Company'),
		'TR_ZIP_POSTAL_CODE'		=> tr('Zip/Postal code'),
		'TR_CITY'					=> tr('City'),
		'TR_STATE'					=> tr('State/Province'),
		'TR_COUNTRY'				=> tr('Country'),
		'TR_STREET_1'				=> tr('Street 1'),
		'TR_STREET_2'				=> tr('Street 2'),
		'TR_EMAIL'					=> tr('Email'),
		'TR_PHONE'					=> tr('Phone'),
		'TR_FAX'					=> tr('Fax'),
		'TR_GENDER'					=> tr('Gender'),
		'TR_MALE'					=> tr('Male'),
		'TR_FEMALE'					=> tr('Female'),
		'TR_UNKNOWN'				=> tr('Unknown'),
		'TR_UPDATE_DATA'			=> tr('Update data')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_general_information.tpl');
gen_client_menu($tpl, 'client/menu_general_information.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $user_id
 */
function gen_user_personal_data($tpl, $user_id) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			`fname`,
			`lname`,
			`gender`,
			`firm`,
			`zip`,
			`city`,
			`state`,
			`country`,
			`street1`,
			`street2`,
			`email`,
			`phone`,
			`fax`
		FROM
			`admin`
		WHERE
			`admin_id` = ?
	";

	$rs = exec_query($sql, $query, $user_id);
	$tpl->assign(
		array(
			'FIRST_NAME'	=> empty($rs->fields['fname']) ? '' : tohtml($rs->fields['fname']),
			'LAST_NAME'		=> empty($rs->fields['lname']) ? '' : tohtml($rs->fields['lname']),
			'FIRM'			=> empty($rs->fields['firm']) ? '' : tohtml($rs->fields['firm']),
			'ZIP'			=> empty($rs->fields['zip']) ? '' : tohtml($rs->fields['zip']),
			'CITY'			=> empty($rs->fields['city']) ? '' : tohtml($rs->fields['city']),
			'STATE'			=> empty($rs->fields['state']) ? '' : tohtml($rs->fields['state']),
			'COUNTRY'		=> empty($rs->fields['country']) ? '' : tohtml($rs->fields['country']),
			'STREET_1'		=> empty($rs->fields['street1']) ? '' : tohtml($rs->fields['street1']),
			'STREET_2'		=> empty($rs->fields['street2']) ? '' : tohtml($rs->fields['street2']),
			'EMAIL'			=> empty($rs->fields['email']) ? '' : tohtml($rs->fields['email']),
			'PHONE'			=> empty($rs->fields['phone']) ? '' : tohtml($rs->fields['phone']),
			'FAX'			=> empty($rs->fields['fax']) ? '' : tohtml($rs->fields['fax']),
			'VL_MALE'		=> (($rs->fields['gender'] == 'M') ? $cfg->HTML_SELECTED : ''),
			'VL_FEMALE'		=> (($rs->fields['gender'] == 'F') ? $cfg->HTML_SELECTED : ''),
			'VL_UNKNOWN'	=> ((($rs->fields['gender'] == 'U') || (empty($rs->fields['gender']))) ? $cfg->HTML_SELECTED : '')
		)
	);
}

function update_user_personal_data($user_id) {

	$sql = EasySCP_Registry::get('Db');

	$fname = clean_input($_POST['fname']);
	$lname = clean_input($_POST['lname']);
	$gender = $_POST['gender'];
	$firm = clean_input($_POST['firm']);
	$zip = clean_input($_POST['zip']);
	$city = clean_input($_POST['city']);
	$state = clean_input($_POST['state']);
	$country = clean_input($_POST['country']);
	$street1 = clean_input($_POST['street1']);
	$street2 = clean_input($_POST['street2']);
	$email = clean_input($_POST['email']);
	$phone = clean_input($_POST['phone']);
	$fax = clean_input($_POST['fax']);

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
			`street1` = ?,
			`street2` = ?,
			`email` = ?,
			`phone` = ?,
			`fax` = ?,
			`gender` = ?
		WHERE
			`admin_id` = ?
	";

	exec_query($sql, $query, array($fname, $lname, $firm, $zip, $city, $state, $country, $street1, $street2, $email, $phone, $fax, $gender, $user_id));

	write_log($_SESSION['user_logged'] . ": update personal data");
	set_page_message(tr('Personal data updated successfully!'), 'success');
}
?>