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

// Get a reference to the Config object
$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'admin/settings.tpl';

if (isset($_POST['uaction']) && $_POST['uaction'] == 'apply') {

	$lostpwd = $_POST['lostpassword'];
	$lostpwd_timeout = clean_input($_POST['lostpassword_timeout']);
	$pwd_chars = clean_input($_POST['passwd_chars']);
	$pwd_strong = $_POST['passwd_strong'];
	$bruteforce = $_POST['bruteforce'];
	$bruteforce_between = $_POST['bruteforce_between'];
	$bruteforce_max_login = clean_input($_POST['bruteforce_max_login']);
	$bruteforce_block_time = clean_input($_POST['bruteforce_block_time']);
	$bruteforce_between_time = clean_input($_POST['bruteforce_between_time']);
	$bruteforce_max_capcha = clean_input($_POST['bruteforce_max_capcha']);
	$create_default_emails = $_POST['create_default_email_addresses'];
	$hard_mail_suspension = $_POST['hard_mail_suspension'];
	$user_initial_lang = $_POST['def_language'];
	$user_initial_theme = $_POST['def_theme'];
	$user_php_version = $_POST['php_version'];
	$support_system = $_POST['support_system'];
	// $hosting_plan_level = $_POST['hosting_plan_level'];
	$domain_rows_per_page = clean_input($_POST['domain_rows_per_page']);
	$checkforupdate = $_POST['checkforupdate'];
	// $custom_orderpanel_id = clean_input($_POST['coid']);
	$tld_strict_validation = $_POST['tld_strict_validation'];
	$sld_strict_validation = $_POST['sld_strict_validation'];
	$max_dnames_labels = clean_input($_POST['max_dnames_labels']);
	$max_subdnames_labels = clean_input($_POST['max_subdnames_labels']);
	$log_level = defined($_POST['log_level']) ? constant($_POST['log_level']) : false;
	// $migration_enabled = $_POST['migration_enabled'];

	if ((!is_number($lostpwd_timeout))
		|| (!is_number($pwd_chars))
		|| (!is_number($bruteforce_max_login))
		|| (!is_number($bruteforce_block_time))
		|| (!is_number($bruteforce_between_time))
		|| (!is_number($bruteforce_max_capcha))
		|| (!is_number($domain_rows_per_page))
		|| (!is_number($max_dnames_labels))
		|| (!is_number($max_subdnames_labels))) {
		set_page_message(tr('Only positive numbers are allowed !'), 'warning');
	} else if ($domain_rows_per_page < 1) {
		$domain_rows_per_page = 1;
	} else if ($max_dnames_labels < 1) {
		$max_dnames_labels = 1;
	} else if ($max_subdnames_labels < 1) {
		$max_subdnames_labels = 1;
	} else {

		// Get a reference to the DB_Config Objects
		$db_cfg = EasySCP_Registry::get('Db_Config');

		$db_cfg->LOSTPASSWORD = $lostpwd;
		$db_cfg->LOSTPASSWORD_TIMEOUT = $lostpwd_timeout;
		$db_cfg->PASSWD_CHARS = $pwd_chars;
		$db_cfg->PASSWD_STRONG = $pwd_strong;
		$db_cfg->BRUTEFORCE  = $bruteforce;
		$db_cfg->BRUTEFORCE_BETWEEN = $bruteforce_between;
		$db_cfg->BRUTEFORCE_MAX_LOGIN = $bruteforce_max_login;
		$db_cfg->BRUTEFORCE_BLOCK_TIME = $bruteforce_block_time;
		$db_cfg->BRUTEFORCE_BETWEEN_TIME = $bruteforce_between_time;
		$db_cfg->BRUTEFORCE_MAX_CAPTCHA = $bruteforce_max_capcha;
		$db_cfg->CREATE_DEFAULT_EMAIL_ADDRESSES = $create_default_emails;
		$db_cfg->HARD_MAIL_SUSPENSION = $hard_mail_suspension;
		$db_cfg->USER_INITIAL_LANG = $user_initial_lang;
		$db_cfg->USER_INITIAL_THEME = $user_initial_theme;
		$db_cfg->PHP_VERSION = $user_php_version;
		$db_cfg->EasySCP_SUPPORT_SYSTEM = $support_system;
		// $db_cfg->HOSTING_PLANS_LEVEL = $hosting_plan_level;
		$db_cfg->DOMAIN_ROWS_PER_PAGE = $domain_rows_per_page;
		$db_cfg->LOG_LEVEL = $log_level;
		$db_cfg->CHECK_FOR_UPDATES = $checkforupdate;
		$db_cfg->TLD_STRICT_VALIDATION = $tld_strict_validation;
		$db_cfg->SLD_STRICT_VALIDATION = $sld_strict_validation;
		$db_cfg->MAX_DNAMES_LABELS = $max_dnames_labels;
		$db_cfg->MAX_SUBDNAMES_LABELS = $max_subdnames_labels;

		$cfg->replaceWith($db_cfg);

		// gets the number of queries that were been executed
		$updt_count = $db_cfg->countQueries('update');
		$new_count = $db_cfg->countQueries('insert');

		// Updated or new config parameters
		if ($updt_count == 0 && $new_count == 0) {
			set_page_message(tr("Nothing has been changed!"), 'info');
		} elseif ($updt_count == 1) {
			set_page_message(
				tr('%d configuration parameter updated!', $updt_count),
				'success'
			);
		} elseif ($updt_count > 1) {
			set_page_message(
				tr('%d configuration parameters updated!', $updt_count),
				'success'
			);
		} elseif ($new_count == 1) {
			set_page_message(
				tr('%d configuration parameter created!', $new_count),
				'success'
			);
		} elseif ($new_count > 1) {
			set_page_message(
				tr('%d configuration parameters created!', $new_count),
				'success'
			);
		}
		send_request('110 DOMAIN master');
	}

	user_goto('settings.php');
}

$tpl->assign(
	array(
		'LOSTPASSWORD_TIMEOUT_VALUE'	=> $cfg->LOSTPASSWORD_TIMEOUT,
		'PASSWD_CHARS'					=> $cfg->PASSWD_CHARS,
		'BRUTEFORCE_MAX_LOGIN_VALUE'	=> $cfg->BRUTEFORCE_MAX_LOGIN,
		'BRUTEFORCE_BLOCK_TIME_VALUE'	=> $cfg->BRUTEFORCE_BLOCK_TIME,
		'BRUTEFORCE_BETWEEN_TIME_VALUE'	=> $cfg->BRUTEFORCE_BETWEEN_TIME,
		'BRUTEFORCE_MAX_CAPTCHA'		=> $cfg->BRUTEFORCE_MAX_CAPTCHA,
		'DOMAIN_ROWS_PER_PAGE'			=> $cfg->DOMAIN_ROWS_PER_PAGE,
		'MAX_DNAMES_LABELS_VALUE'		=> $cfg->MAX_DNAMES_LABELS,
		'MAX_SUBDNAMES_LABELS_VALUE'	=> $cfg->MAX_SUBDNAMES_LABELS
	)
);

gen_def_language($cfg->USER_INITIAL_LANG);
gen_def_theme();

// Grab the value only once to improve performances
$html_selected = $cfg->HTML_SELECTED;

if ($cfg->LOSTPASSWORD) {
	$tpl->assign('LOSTPASSWORD_SELECTED_ON', $html_selected);
	$tpl->assign('LOSTPASSWORD_SELECTED_OFF', '');
} else {
	$tpl->assign('LOSTPASSWORD_SELECTED_ON', '');
	$tpl->assign('LOSTPASSWORD_SELECTED_OFF', $html_selected);
}

if ($cfg->PASSWD_STRONG) {
	$tpl->assign('PASSWD_STRONG_ON', $html_selected);
	$tpl->assign('PASSWD_STRONG_OFF', '');
} else {
	$tpl->assign('PASSWD_STRONG_ON', '');
	$tpl->assign('PASSWD_STRONG_OFF',  $html_selected);
}

if ($cfg->BRUTEFORCE) {
	$tpl->assign('BRUTEFORCE_SELECTED_ON', $html_selected);
	$tpl->assign('BRUTEFORCE_SELECTED_OFF', '');
} else {
	$tpl->assign('BRUTEFORCE_SELECTED_ON', '');
	$tpl->assign('BRUTEFORCE_SELECTED_OFF', $html_selected);
}

if ($cfg->BRUTEFORCE_BETWEEN) {
	$tpl->assign('BRUTEFORCE_BETWEEN_SELECTED_ON', $html_selected);
	$tpl->assign('BRUTEFORCE_BETWEEN_SELECTED_OFF', '');
} else {
	$tpl->assign('BRUTEFORCE_BETWEEN_SELECTED_ON', '');
	$tpl->assign('BRUTEFORCE_BETWEEN_SELECTED_OFF', $html_selected);
}

if ($cfg->EasySCP_SUPPORT_SYSTEM) {
	$tpl->assign('SUPPORT_SYSTEM_SELECTED_ON', $html_selected);
	$tpl->assign('SUPPORT_SYSTEM_SELECTED_OFF', '');
} else {
	$tpl->assign('SUPPORT_SYSTEM_SELECTED_ON', '');
	$tpl->assign('SUPPORT_SYSTEM_SELECTED_OFF', $html_selected);
}

if ($cfg->TLD_STRICT_VALIDATION) {
	$tpl->assign('TLD_STRICT_VALIDATION_ON', $html_selected);
	$tpl->assign('TLD_STRICT_VALIDATION_OFF', '');
} else {
	$tpl->assign('TLD_STRICT_VALIDATION_ON', '');
	$tpl->assign('TLD_STRICT_VALIDATION_OFF', $html_selected);
}

if ($cfg->SLD_STRICT_VALIDATION) {
	$tpl->assign('SLD_STRICT_VALIDATION_ON', $html_selected);
	$tpl->assign('SLD_STRICT_VALIDATION_OFF', '');
} else {
	$tpl->assign('SLD_STRICT_VALIDATION_ON', '');
	$tpl->assign('SLD_STRICT_VALIDATION_OFF', $html_selected);
}

if ($cfg->CREATE_DEFAULT_EMAIL_ADDRESSES) {
	$tpl->assign('CREATE_DEFAULT_EMAIL_ADDRESSES_ON', $html_selected);
	$tpl->assign('CREATE_DEFAULT_EMAIL_ADDRESSES_OFF', '');
} else {
	$tpl->assign('CREATE_DEFAULT_EMAIL_ADDRESSES_ON', '');
	$tpl->assign('CREATE_DEFAULT_EMAIL_ADDRESSES_OFF', $html_selected);
}

if ($cfg->HARD_MAIL_SUSPENSION) {
	$tpl->assign('HARD_MAIL_SUSPENSION_ON', $html_selected);
	$tpl->assign('HARD_MAIL_SUSPENSION_OFF', '');
} else {
	$tpl->assign('HARD_MAIL_SUSPENSION_ON', '');
	$tpl->assign('HARD_MAIL_SUSPENSION_OFF', $html_selected);
}

if ($cfg->HOSTING_PLANS_LEVEL == 'admin') {
	$tpl->assign('HOSTING_PLANS_LEVEL_ADMIN', $html_selected);
	$tpl->assign('HOSTING_PLANS_LEVEL_RESELLER', '');
} else {
	$tpl->assign('HOSTING_PLANS_LEVEL_ADMIN', '');
	$tpl->assign('HOSTING_PLANS_LEVEL_RESELLER', $html_selected);
}

if ($cfg->CHECK_FOR_UPDATES) {
	$tpl->assign('CHECK_FOR_UPDATES_SELECTED_ON', $html_selected);
	$tpl->assign('CHECK_FOR_UPDATES_SELECTED_OFF', '');
} else {
	$tpl->assign('CHECK_FOR_UPDATES_SELECTED_ON', '');
	$tpl->assign('CHECK_FOR_UPDATES_SELECTED_OFF', $html_selected);
}

switch ($cfg->LOG_LEVEL) {
	case false:
		$tpl->assign('LOG_LEVEL_SELECTED_OFF', $html_selected);
		$tpl->assign('LOG_LEVEL_SELECTED_NOTICE', '');
		$tpl->assign('LOG_LEVEL_SELECTED_WARNING', '');
		$tpl->assign('LOG_LEVEL_SELECTED_ERROR', '');
		break;
	case E_USER_NOTICE:
		$tpl->assign('LOG_LEVEL_SELECTED_OFF', '');
		$tpl->assign('LOG_LEVEL_SELECTED_NOTICE', $html_selected);
		$tpl->assign('LOG_LEVEL_SELECTED_WARNING', '');
		$tpl->assign('LOG_LEVEL_SELECTED_ERROR', '');
		break;
	case E_USER_WARNING:
		$tpl->assign('LOG_LEVEL_SELECTED_OFF', '');
		$tpl->assign('LOG_LEVEL_SELECTED_NOTICE', '');
		$tpl->assign('LOG_LEVEL_SELECTED_WARNING', $html_selected);
		$tpl->assign('LOG_LEVEL_SELECTED_ERROR', '');
		break;
	default:
		$tpl->assign('LOG_LEVEL_SELECTED_OFF', '');
		$tpl->assign('LOG_LEVEL_SELECTED_NOTICE', '');
		$tpl->assign('LOG_LEVEL_SELECTED_WARNING', '');
		$tpl->assign('LOG_LEVEL_SELECTED_ERROR', $html_selected);
} // end switch

/*
if ($cfg->MIGRATION_ENABLED) {
	$tpl->assign('MIGRATION_ENABLED_SELECTED_ON', $html_selected);
	$tpl->assign('MIGRATION_ENABLED_SELECTED_OFF', '');
} else {
	$tpl->assign('MIGRATION_ENABLED_SELECTED_ON', '');
	$tpl->assign('MIGRATION_ENABLED_SELECTED_OFF', $html_selected);
}
*/
$entry = 0;
foreach (EasyConfig::$php->PHP_Entry as $PHP_Entry) {
	if (isset($PHP_Entry->SRV_PHP) && isset($PHP_Entry->PHP_NAME)) {
		$php_selected = ($cfg->PHP_VERSION == $entry) ? $html_selected : '';
		$tpl->append(
			array(
				'PHP_VERSION_VALUE'		=> $entry,
				'PHP_VERSION_SELECTED'	=> $php_selected,
				'PHP_VERSION_NAME'		=> $PHP_Entry->PHP_NAME)
			);
	}
	$entry = $entry + 1;
}
		
// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'						=> tr('EasySCP - Admin/Settings'),
		'TR_GENERAL_SETTINGS'				=> tr('General settings'),
		'TR_SETTINGS'						=> tr('Settings'),
		'TR_MESSAGE'						=> tr('Message'),
		'TR_LOSTPASSWORD'					=> tr('Lost password'),
		'TR_LOSTPASSWORD_TIMEOUT'			=> tr('Activation link expire time (minutes)'),
		'TR_PASSWORD_SETTINGS'				=> tr('Password settings'),
		'TR_PASSWD_STRONG'					=> tr('Use strong Passwords'),
		'TR_PASSWD_CHARS'					=> tr('Password length'),
		'TR_BRUTEFORCE'						=> tr('Bruteforce detection'),
		'TR_BRUTEFORCE_BETWEEN'				=> tr('Block time between logins'),
		'TR_BRUTEFORCE_MAX_LOGIN'			=> tr('Max number of login attempts'),
		'TR_BRUTEFORCE_BLOCK_TIME'			=> tr('Blocktime (minutes)'),
		'TR_BRUTEFORCE_BETWEEN_TIME'		=> tr('Block time between logins (seconds)'),
		'TR_BRUTEFORCE_MAX_CAPTCHA'			=> tr('Max number of CAPTCHA validation attempts'),
		'TR_OTHER_SETTINGS'					=> tr('Other settings'),
		'TR_MAIL_SETTINGS'					=> tr('E-Mail settings'),
		'TR_CREATE_DEFAULT_EMAIL_ADDRESSES'	=> tr('Create default E-Mail addresses'),
		'TR_HARD_MAIL_SUSPENSION'			=> tr('E-Mail accounts are hard suspended'),
		'TR_USER_INITIAL_LANG'				=> tr('Panel default language'),
		'TR_USER_INITIAL_THEME'				=> tr('Panel default theme'),
		'TR_USER_PHP_VERSION'				=> tr('PHP version'),
		'TR_SUPPORT_SYSTEM'					=> tr('Support system'),
		'TR_ENABLED'						=> tr('Enabled'),
		'TR_DISABLED'						=> tr('Disabled'),
		'TR_APPLY_CHANGES'					=> tr('Apply changes'),
		'TR_SERVERPORTS'					=> tr('Server ports'),
		'TR_HOSTING_PLANS_LEVEL'			=> tr('Hosting plans available for'),
		'TR_ADMIN'							=> tr('Admin'),
		'TR_RESELLER'						=> tr('Reseller'),
		'TR_DOMAIN_ROWS_PER_PAGE'			=> tr('Domains per page'),
		'TR_LOG_LEVEL'						=> tr('Log Level'),
		'TR_E_USER_OFF'						=> tr('Disabled'),
		'TR_E_USER_NOTICE'					=> tr('Notices, Warnings and Errors'),
		'TR_E_USER_WARNING'					=> tr('Warnings and Errors'),
		'TR_E_USER_ERROR'					=> tr('Errors'),
		'TR_CHECK_FOR_UPDATES'				=> tr('Check for update'),
		'TR_DNAMES_VALIDATION_SETTINGS'		=> tr('Domain names validation'),
		'TR_TLD_STRICT_VALIDATION'			=> tr('Top Level Domain name strict validation'),
		'TR_TLD_STRICT_VALIDATION_HELP'		=> tr('Only Top Level Domains (TLD) listed in IANA root zone database can be used.'),
		'TR_SLD_STRICT_VALIDATION'			=> tr('Second Level Domain name strict validation'),
		'TR_SLD_STRICT_VALIDATION_HELP'		=> tr('Single letter Second Level Domains (SLD) are not allowed under the most Top Level Domains (TLD). There is a small list of exceptions, e.g. the TLD .de.'),
		'TR_MAX_DNAMES_LABELS'				=> tr('Maximal number of labels for domain names<br />(<em>Excluding SLD & TLD</em>)'),
		'TR_MAX_SUBDNAMES_LABELS'			=> tr('Maximal number of labels for subdomains')
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_settings.tpl');
gen_admin_menu($tpl, 'admin/menu_settings.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();
?>