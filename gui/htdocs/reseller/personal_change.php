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
$template = 'reseller/personal_change.tpl';

if (isset($_POST['uaction']) && $_POST['uaction'] === 'updt_data') {
	update_reseller_personal_data($_SESSION['user_id']);
}

gen_reseller_personal_data($tpl, $_SESSION['user_id']);


// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Reseller/Change Personal Data'),
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
		'TR_UPDATE_DATA'			=> tr('Update data'),
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_general_information.tpl');
gen_reseller_menu($tpl, 'reseller/menu_general_information.tpl');

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
function gen_reseller_personal_data($tpl, $user_id) {
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
			'FIRST_NAME'	=> (($rs->fields['fname'] == null)		? '' : tohtml($rs->fields['fname'])),
			'LAST_NAME'		=> (($rs->fields['lname'] == null)		? '' : tohtml($rs->fields['lname'])),
			'FIRM'			=> (($rs->fields['firm'] == null)		? '' : tohtml($rs->fields['firm'])),
			'ZIP'			=> (($rs->fields['zip'] == null)		? '' : tohtml($rs->fields['zip'])),
			'CITY'			=> (($rs->fields['city'] == null)		? '' : tohtml($rs->fields['city'])),
			'STATE'			=> (($rs->fields['state'] == null)		? '' : tohtml($rs->fields['state'])),
			'COUNTRY'		=> (($rs->fields['country'] == null)	? '' : tohtml($rs->fields['country'])),
			'STREET_1'		=> (($rs->fields['street1'] == null)	? '' : tohtml($rs->fields['street1'])),
			'STREET_2'		=> (($rs->fields['street2'] == null)	? '' : tohtml($rs->fields['street2'])),
			'EMAIL'			=> (($rs->fields['email'] == null)		? '' : tohtml($rs->fields['email'])),
			'PHONE'			=> (($rs->fields['phone'] == null)		? '' : tohtml($rs->fields['phone'])),
			'FAX'			=> (($rs->fields['fax'] == null)		? '' : tohtml($rs->fields['fax'])),
			'VL_MALE'		=> (($rs->fields['gender'] == 'M')		? $cfg->HTML_SELECTED : ''),
			'VL_FEMALE'		=> (($rs->fields['gender'] == 'F')		? $cfg->HTML_SELECTED : ''),
			'VL_UNKNOWN'	=> ((($rs->fields['gender'] == 'U') || (empty($rs->fields['gender']))) ? $cfg->HTML_SELECTED : '')
		)
	);
}

function update_reseller_personal_data($user_id) {

	$sql_param = array(
		':fname'		=> clean_input($_POST['fname']),
		':lname'		=> clean_input($_POST['lname']),
		':firm'			=> clean_input($_POST['firm']),
		':zip'			=> clean_input($_POST['zip']),
		':city'			=> clean_input($_POST['city']),
		':state'		=> clean_input($_POST['state']),
		':country'		=> clean_input($_POST['country']),
		':email'		=> clean_input($_POST['email']),
		':phone'		=> clean_input($_POST['phone']),
		':fax'			=> clean_input($_POST['fax']),
		':street1'		=> clean_input($_POST['street1']),
		':street2'		=> clean_input($_POST['street2']),
		':gender'		=> $_POST['gender'],
		':admin_id'		=> $user_id
	);

	$sql_query = "
		UPDATE
			domain_aliasses
		SET
			fname	= :fname,
			lname	= :lname,
			firm	= :firm,
			zip		= :zip,
			city	= :city,
			state	= :state,
			country	= :country,
			email	= :email,
			phone	= :phone,
			fax		= :fax,
			street1	= :street1,
			street2	= :street2,
			gender	= :gender
		WHERE
			admin_id = :alias_id
	";

	DB::prepare($sql_query);
	DB::execute($sql_param)->closeCursor();

	set_page_message(tr('Personal data updated successfully!'), 'success');
}
?>
