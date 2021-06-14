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

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'reseller/user_add1.tpl';

// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Users/Add user'),
		'TR_ADD_USER'				=> tr('Add user'),
		'TR_CORE_DATA'				=> tr('Core data'),
		'TR_DOMAIN_NAME'			=> tr('Domain name'),
		'TR_DOMAIN_EXPIRE'			=> tr('Domain expire'),
		'TR_CHOOSE_HOSTING_PLAN'	=> tr('Choose hosting plan'),
		'TR_PERSONALIZE_TEMPLATE'	=> tr('Personalise template'),
		'TR_YES'					=> tr('Yes'),
		'TR_NO'						=> tr('No'),
		'TR_NEXT_STEP'				=> tr('Next step'),
		'TR_DMN_HELP'				=> tr("You do not need 'www.' EasySCP will add it on its own."),
		'TR_EXPIRE_CHECKBOX'		=> tr('or check if domain should <strong>never</strong> expire'),
		'TR_SU'						=> tr('Su'),
		'TR_MO'						=> tr('Mo'), 
		'TR_TU'						=> tr('Tu'), 
		'TR_WE'						=> tr('We'), 
		'TR_TH'						=> tr('Th'), 
		'TR_FR'						=> tr('Fr'), 
		'TR_SA'						=> tr('Sa'),
		'TR_JANUARY'				=> tr('January'),
		'TR_FEBRUARY'				=> tr('February'),
		'TR_MARCH'					=> tr('March'),
		'TR_APRIL'					=> tr('April'),
		'TR_MAY'					=> tr('May'),
		'TR_JUNE'					=> tr('June'),
		'TR_JULY'					=> tr('July'),
		'TR_AUGUST'					=> tr('August'),
		'TR_SEPTEMBER'				=> tr('September'),
		'TR_OCTOBER'				=> tr('October'),
		'TR_NOVEMBER'				=> tr('November'),
		'TR_DECEMBER'				=> tr('December'),
		'VL_DATE_FORMAT'			=> jQueryDatepickerDateFormat($cfg->DATE_FORMAT)
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_users_manage.tpl');
gen_reseller_menu($tpl, 'reseller/menu_users_manage.tpl');

if (isset($_POST['uaction'])) {
	if (!check_user_data()) {
		get_data_au1_page($tpl);
	}
} else {
	get_empty_au1_page($tpl);
}

get_hp_data_list($tpl, $_SESSION['user_id']);
gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// Function declaration path

/**
 * Check correction of entered users data
 */
function check_user_data() {
	global $dmn_name; // domain name
	global $dmn_expire; // Domain expire date
	global $dmn_chp; // choosed hosting plan
	global $dmn_pt;
	global $validation_err_msg;

	$sql = EasySCP_Registry::get('Db');
	$cfg = EasySCP_Registry::get('Config');

	// personal template
	$event_txt = '';

	if (isset($_POST['dmn_name'])) {
		$dmn_name = strtolower(trim($_POST['dmn_name']));
	}

	if (isset($_POST['dmn_expire_date'])) {
		$dmn_expire = clean_input($_POST['dmn_expire_date']);
	}

	if (isset($_POST['dmn_tpl'])) {
		$dmn_chp = $_POST['dmn_tpl'];
	}

	if (isset($_POST['chtpl'])) {
		$dmn_pt = $_POST['chtpl'];
	}

	// Check if input string is a valid domain names
	if (!validates_dname($dmn_name)) {
		set_page_message($validation_err_msg, 'warning');
		return false;
	}

	// Should be perfomed after domain names syntax validation now
	$dmn_name = encode_idna($dmn_name);

	if (easyscp_domain_exists($dmn_name, $_SESSION['user_id'])) {
		$event_txt = tr('Domain with that name already exists on the system!');
	} else if ($dmn_name == $cfg->BASE_SERVER_VHOST) {
		$event_txt = tr('Master domain cannot be used!');
	}

	// we have plans only for admins
	if (isset($cfg->HOSTING_PLANS_LEVEL)
		&& $cfg->HOSTING_PLANS_LEVEL === 'admin') {
		$dmn_pt = '_no_';
	}

	if (!empty($event_txt)) { // There are wrong input data
		set_page_message($event_txt, 'error');
		return false;
	} else if ($dmn_pt == '_yes_' || !isset($_POST['dmn_tpl'])) {
		// send through the session the data
		$_SESSION['dmn_name']	= $dmn_name;
		$_SESSION['dmn_expire_date']	= $dmn_expire;
		$_SESSION['dmn_tpl']	= $dmn_chp;
		$_SESSION['chtpl']		= $dmn_pt;
		$_SESSION['step_one']	= "_yes_";

		user_goto('user_add2.php');
	} else {
		// check if reseller limits are not touched
		$ehp_error = ''; // fill dummy to satisfy warning...
		if (reseller_limits_check($sql, $ehp_error, $_SESSION['user_id'], $dmn_chp)) {
			// send through the session the data
			$_SESSION['dmn_name']	= $dmn_name;
			$_SESSION['dmn_expire_date']	= $dmn_expire;
			$_SESSION['dmn_tpl']	= $dmn_chp;
			$_SESSION['chtpl']		= $dmn_pt;
			$_SESSION['step_one']	= "_yes_";

			user_goto('user_add3.php');
		} else {
			set_page_message(
				tr("Hosting plan values exceed reseller maximum values!"),
				'warning'
			);
			return false;
		}
	}
} // End of check_user_data()

/**
 * Show empty page
 * @param EasySCP_TemplateEngine $tpl
 */
function get_empty_au1_page($tpl) {
	$cfg = EasySCP_Registry::get('Config');

	$tpl->assign(
		array(
			'DMN_NAME_VALUE'		=> '',
			'CHTPL1_VAL'			=> '',
			'CHTPL2_VAL'			=> $cfg->HTML_CHECKED
		)
	);
} // End of get_empty_au1_page()

/**
 * Show first page of add user with data
 * @param EasySCP_TemplateEngine $tpl
 */
function get_data_au1_page($tpl) {
	global $dmn_name; // Domain name
	global $dmn_expire; // Domain expire date
	//global $dmn_chp; // choosed hosting plan;
	global $dmn_pt; // personal template

	$cfg = EasySCP_Registry::get('Config');

	$tpl->assign(
		array(
			'DMN_NAME_VALUE' 			=> tohtml($dmn_name),
			'CHTPL1_VAL' 				=> $dmn_pt === "_yes_" ? $cfg->HTML_CHECKED : '',
			'CHTPL2_VAL' 				=> $dmn_pt === "_yes_" ? '' : $cfg->HTML_CHECKED,
			'VL_DOMAIN_EXPIRE'			=> $dmn_expire,
			'VL_EXPIRE_DATE_DISABLED'	=> ($dmn_expire == 0) ? $cfg->HTML_DISABLED : '',
			'VL_EXPIRE_NEVER_SELECTED'	=> ($dmn_expire == 0) ? $cfg->HTML_CHECKED : '',
		)
	);
} // End of get_data_au1_page()

/**
 * Get list with hosting plan for selection
 * @param EasySCP_TemplateEngine $tpl
 * @param int $reseller_id
 */
function get_hp_data_list($tpl, $reseller_id) {
	global $dmn_chp;

	$sql = EasySCP_Registry::get('Db');
	$cfg = EasySCP_Registry::get('Config');

	if (isset($cfg->HOSTING_PLANS_LEVEL)
		&& $cfg->HOSTING_PLANS_LEVEL === 'admin') {
		$query = "
			SELECT
				t1.`id`,
				t1.`reseller_id`,
				t1.`name`,
				t1.`props`,
				t1.`status`,
				t2.`admin_id`,
				t2.`admin_type`
			FROM
				`hosting_plans` AS t1,
				`admin` AS t2
			WHERE
				t2.`admin_type` = ?
			AND
				t1.`reseller_id` = t2.`admin_id`
			AND
				t1.`status` = 1
			ORDER BY
				t1.`name`
		";

		$rs = exec_query($sql, $query, 'admin');
		$tpl->assign('PERSONALIZE', '');

		if ($rs->recordCount() == 0) {
			set_page_message(
				tr('You have no hosting plans. Please contact your system administrator.'),
				'info'
			);
			$tpl->assign('ADD_USER', '');
			$tpl->assign('ADD_FORM', '');
		}

	} else {
		$query = "
			SELECT
				`id`,
				`name`,
				`props`,
				`status`
			FROM
				`hosting_plans`
			WHERE
				`reseller_id` = ?
			ORDER BY
				`name`
		";

		$rs = exec_query($sql, $query, $reseller_id);
	}

	if (0 !== $rs->rowCount()) { // There are data
		while (($data = $rs->fetchRow())) {
			$dmn_chp = isset($dmn_chp) ? $dmn_chp : $data['id'];
			$tpl->append(
					array(
						'HP_NAME'	=> tohtml($data['name']),
						'CHN'		=> $data['id'],
						'CH_SEL'	=> ($data['id'] == $dmn_chp) ? $cfg->HTML_SELECTED : ''
					)
			);

		}

	} else {
		$tpl->assign('ADD_USER', '');
	}
} // End of get_hp_data_list()

?>