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
$template = 'admin/reseller_add.tpl';

// static page messages
$reseller_ips = get_server_ip($tpl);

add_reseller($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Admin/Manage users/Add reseller'),
		'TR_ADD_RESELLER'			=> tr('Add reseller'),
		'TR_CORE_DATA'				=> tr('Core data'),
		'TR_USERNAME'				=> tr('Username'),
		'TR_PASSWORD'				=> tr('Password'),
		'TR_PASSWORD_REPEAT'		=> tr('Repeat password'),
		'TR_EMAIL'					=> tr('Email'),
		'TR_MAX_DOMAIN_COUNT'		=> tr('Domains limit<br /><em>(0 unlimited)</em>'),
		'TR_MAX_SUBDOMAIN_COUNT'	=> tr('Subdomains limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAX_ALIASES_COUNT'		=> tr('Aliases limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAX_MAIL_USERS_COUNT'	=> tr('Mail accounts limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAX_FTP_USERS_COUNT'	=> tr('FTP accounts limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAX_SQLDB_COUNT'		=> tr('SQL databases limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAX_SQL_USERS_COUNT'	=> tr('SQL users limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAX_TRAFFIC_AMOUNT'		=> tr('Traffic limit [MB]<br /><em>(0 unlimited)</em>'),
		'TR_MAX_DISK_AMOUNT'		=> tr('Disk limit [MB]<br /><em>(0 unlimited)</em>'),
		'TR_PHP'					=> tr('PHP'),
		'TR_PERL_CGI'				=> tr('CGI / Perl'),
		'TR_JSP'					=> tr('JSP'),
		'TR_SSI'					=> tr('SSI'),
		'TR_FRONTPAGE_EXT'			=> tr('Frontpage extensions'),
		'TR_BACKUP_RESTORE'			=> tr('Backup and restore'),
		'TR_CUSTOM_ERROR_PAGES'		=> tr('Custom error pages'),
		'TR_PROTECTED_AREAS'		=> tr('Protected areas'),
		'TR_WEBMAIL'				=> tr('Webmail'),
		'TR_DIR_LIST'				=> tr('Directory listing'),
		'TR_APACHE_LOGFILES'		=> tr('Apache logfiles'),
		'TR_AWSTATS'				=> tr('AwStats'),
		'TR_LOGO_UPLOAD'			=> tr('Logo upload'),
		'TR_YES'					=> tr('Yes'),
		'TR_NO'						=> tr('No'),
		'TR_SUPPORT_SYSTEM'			=> tr('Support system'),

		'TR_RESELLER_IPS'			=> tr('Reseller IPs'),

		'TR_ADDITIONAL_DATA'		=> tr('Additional data'),
		'TR_CUSTOMER_ID'			=> tr('Customer ID'),
		'TR_FIRST_NAME'				=> tr('First name'),
		'TR_LAST_NAME'				=> tr('Last name'),
		'TR_GENDER'					=> tr('Gender'),
		'TR_MALE'					=> tr('Male'),
		'TR_FEMALE'					=> tr('Female'),
		'TR_UNKNOWN'				=> tr('Unknown'),
		'TR_COMPANY'				=> tr('Company'),
		'TR_ZIP_POSTAL_CODE'		=> tr('Zip/Postal code'),
		'TR_CITY'					=> tr('City'),
		'TR_STATE'					=> tr('State/Province'),
		'TR_COUNTRY'				=> tr('Country'),
		'TR_STREET_1'				=> tr('Street 1'),
		'TR_STREET_2'				=> tr('Street 2'),
		'TR_PHONE'					=> tr('Phone'),
		'TR_FAX'					=> tr('Fax'),
		'TR_ADD'					=> tr('Add'),
		'GENPAS'					=> passgen()
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
 * Get Server IPs
 * @param EasySCP_TemplateEngine $tpl
 */
function get_server_ip($tpl) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			`ip_id`, `ip_number`, `ip_domain`
		FROM
			`server_ips`
		ORDER BY
			`ip_number`
	";

	$rs = exec_query($sql, $query);

	$i = 0;

	$reseller_ips = '';

	if ($rs->recordCount() == 0) {
		$tpl->assign(
			array(
				'RSL_IP_MESSAGE' => tr('Reseller IP list is empty!'),
				'RSL_IP_MESSAGE_TYPE' => 'info'
			)
		);

	} else {
		$tpl->assign(
			array(
				'TR_RSL_IP_NUMBER' => tr('No.'),
				'TR_RSL_IP_ASSIGN' => tr('Assign'),
				'TR_RSL_IP_LABEL' => tr('Label'),
				'TR_RSL_IP_IP' => tr('Number'),
			)
		);
		while (!$rs->EOF) {
			$ip_id = $rs->fields['ip_id'];

			$ip_var_name = "ip_$ip_id";

			if (isset($_POST[$ip_var_name]) && $_POST[$ip_var_name] == 'asgned') {
				$ip_item_assigned = $cfg->HTML_CHECKED;

				$reseller_ips .= "$ip_id;";
			} else {
				$ip_item_assigned = '';
			}

			$tpl->append(
				array(
					'RSL_IP_NUMBER' => $i + 1,
					'RSL_IP_LABEL' => $rs->fields['ip_domain'],
					'RSL_IP_IP' => $rs->fields['ip_number'],
					'RSL_IP_CKB_NAME' => $ip_var_name,
					'RSL_IP_CKB_VALUE' => 'asgned',
					'RSL_IP_ITEM_ASSIGNED' => $ip_item_assigned,
				)
			);


			$rs->moveNext();

			$i++;
		}
	}

	return $reseller_ips;
}

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function add_reseller($tpl) {

	global $reseller_ips;
	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	if (isset($_POST['uaction']) && $_POST['uaction'] === 'add_reseller') {
		if (check_user_data()) {
			$upass = crypt_user_pass($_POST['pass']);

			$user_id = $_SESSION['user_id'];

			$username = clean_input($_POST['username']);
			$fname = clean_input($_POST['fname']);
			$lname = clean_input($_POST['lname']);
			$gender = clean_input($_POST['gender']);
			$firm = clean_input($_POST['firm']);
			$zip = clean_input($_POST['zip']);
			$city = clean_input($_POST['city']);
			$state = clean_input($_POST['state']);
			$country = clean_input($_POST['country']);
			$email = clean_input($_POST['email']);
			$phone = clean_input($_POST['phone']);
			$fax = clean_input($_POST['fax']);
			$street1 = clean_input($_POST['street1']);
			$street2 = clean_input($_POST['street2']);

			$query = "
				INSERT INTO `admin` (
					`admin_name`,
					`admin_pass`,
					`admin_type`,
					`domain_created`,
					`created_by`,
					`fname`,
					`lname`,
					`firm`,
					`zip`,
					`city`,
					`state`,
					`country`,
					`email`,
					`phone`,
					`fax`,
					`street1`,
					`street2`,
					`gender`
				) VALUES (
					?,
					?,
					'reseller',
					unix_timestamp(),
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?,
					?
				)
			";
			exec_query($sql, $query, array($username,
					$upass,
					$user_id,
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
					$gender)
			);

			$new_admin_id = $sql->insertId();

			$user_logged = $_SESSION['user_logged'];

			write_log("$user_logged: add reseller: $username");

			// $user_def_lang = $cfg->USER_INITIAL_LANG;
			$user_def_lang = '';
			// $user_theme_color = $cfg->USER_INITIAL_THEME;
			$user_theme_color = '';

			$query = "
				INSERT INTO `user_gui_props`
					(
					`user_id`,
					`lang`,
					`layout`
					)
				VALUES
					(?, ?, ?)
			";

			exec_query($sql, $query, array($new_admin_id,
					$user_def_lang,
					$user_theme_color)
			);

			/*
			 * 'reseller_props' table entry;
			 */

			$nreseller_max_domain_cnt = clean_input($_POST['nreseller_max_domain_cnt']);
			$nreseller_max_subdomain_cnt = clean_input($_POST['nreseller_max_subdomain_cnt']);
			$nreseller_max_alias_cnt = clean_input($_POST['nreseller_max_alias_cnt']);
			$nreseller_max_mail_cnt = clean_input($_POST['nreseller_max_mail_cnt']);
			$nreseller_max_ftp_cnt = clean_input($_POST['nreseller_max_ftp_cnt']);
			$nreseller_max_sql_db_cnt = clean_input($_POST['nreseller_max_sql_db_cnt']);
			$nreseller_max_sql_user_cnt = clean_input($_POST['nreseller_max_sql_user_cnt']);
			$nreseller_max_traffic = clean_input($_POST['nreseller_max_traffic']);
			$nreseller_max_disk = clean_input($_POST['nreseller_max_disk']);
			$customer_id = clean_input($_POST['customer_id']);
			$support_system = clean_input($_POST['support_system']);

			$query = "
				INSERT INTO `reseller_props` (
					`reseller_id`, `reseller_ips`,
					`max_dmn_cnt`, `current_dmn_cnt`,
					`max_sub_cnt`, `current_sub_cnt`,
					`max_als_cnt`, `current_als_cnt`,
					`max_mail_cnt`, `current_mail_cnt`,
					`max_ftp_cnt`, `current_ftp_cnt`,
					`max_sql_db_cnt`, `current_sql_db_cnt`,
					`max_sql_user_cnt`, `current_sql_user_cnt`,
					`max_traff_amnt`, `current_traff_amnt`,
					`max_disk_amnt`, `current_disk_amnt`,
					`support_system`, `customer_id`
				) VALUES (
					?, ?,
					?, '0',
					?, '0',
					?, '0',
					?, '0',
					?, '0',
					?, '0',
					?, '0',
					?, '0',
					?, '0',
					?, ?
				)
				";

			exec_query($sql, $query, array($new_admin_id, $reseller_ips,
					$nreseller_max_domain_cnt,
					$nreseller_max_subdomain_cnt,
					$nreseller_max_alias_cnt,
					$nreseller_max_mail_cnt,
					$nreseller_max_ftp_cnt,
					$nreseller_max_sql_db_cnt,
					$nreseller_max_sql_user_cnt,
					$nreseller_max_traffic,
					$nreseller_max_disk,
					$support_system,
					$customer_id)
			);

			send_add_user_auto_msg($user_id,
				clean_input($_POST['username']),
				$_POST['pass'],
				clean_input($_POST['email']),
				clean_input($_POST['fname']),
				clean_input($_POST['lname']),
				tr('Reseller'),
				$gender
			);

			$_SESSION['reseller_added'] = 1;

			user_goto('manage_users.php');
		} else {
			$tpl->assign(
				array(
					'EMAIL' => clean_input($_POST['email'], true),
					'USERNAME' => clean_input($_POST['username'], true),
					'FIRST_NAME' => clean_input($_POST['fname'], true),
					'CUSTOMER_ID' => clean_input($_POST['customer_id'], true),
					'LAST_NAME' => clean_input($_POST['lname'], true),
					'FIRM' => clean_input($_POST['firm'], true),
					'ZIP' => clean_input($_POST['zip'], true),
					'CITY' => clean_input($_POST['city'], true),
					'STATE' => clean_input($_POST['state'], true),
					'COUNTRY' => clean_input($_POST['country'], true),
					'STREET_1' => clean_input($_POST['street1'], true),
					'STREET_2' => clean_input($_POST['street2'], true),
					'PHONE' => clean_input($_POST['phone'], true),
					'FAX' => clean_input($_POST['fax'], true),
					'VL_MALE' => (($_POST['gender'] == 'M') ? $cfg->HTML_SELECTED : ''),
					'VL_FEMALE' => (($_POST['gender'] == 'F') ? $cfg->HTML_SELECTED : ''),
					'VL_UNKNOWN' => ((($_POST['gender'] == 'U') || (empty($_POST['gender']))) ? $cfg->HTML_SELECTED : ''),

					'MAX_DOMAIN_COUNT' => clean_input($_POST['nreseller_max_domain_cnt'], true),
					'MAX_SUBDOMAIN_COUNT' => clean_input($_POST['nreseller_max_subdomain_cnt'], true),
					'MAX_ALIASES_COUNT' => clean_input($_POST['nreseller_max_alias_cnt'], true),
					'MAX_MAIL_USERS_COUNT' => clean_input($_POST['nreseller_max_mail_cnt'], true),
					'MAX_FTP_USERS_COUNT' => clean_input($_POST['nreseller_max_ftp_cnt'], true),
					'MAX_SQLDB_COUNT' => clean_input($_POST['nreseller_max_sql_db_cnt'], true),
					'MAX_SQL_USERS_COUNT' => clean_input($_POST['nreseller_max_sql_user_cnt'], true),
					'MAX_TRAFFIC_AMOUNT' => clean_input($_POST['nreseller_max_traffic'], true),
					'MAX_DISK_AMOUNT' => clean_input($_POST['nreseller_max_disk'], true),
					'SUPPORT_SYSTEM_YES' => (($_POST['support_system'] == 'yes') ? $cfg->HTML_SELECTED : ''),
					'SUPPORT_SYSTEM_NO' => (($_POST['support_system'] == 'no') ? $cfg->HTML_SELECTED : ''),
					'SUPPORT_SYSTEM' => clean_input($_POST['support_system'], true)
				)
			);
		}
	} // not add
	else {
		$tpl->assign(
			array(
				'EMAIL' => '',
				'USERNAME' => '',

				'FIRST_NAME' => '',
				'CUSTOMER_ID' => '',
				'LAST_NAME' => '',
				'FIRM' => '',
				'ZIP' => '',
				'CITY' => '',
				'STATE' => '',
				'COUNTRY' => '',
				'STREET_1' => '',
				'STREET_2' => '',
				'PHONE' => '',
				'FAX' => '',
				'VL_MALE' => '',
				'VL_FEMALE' => '',
				'VL_UNKNOWN' => $cfg->HTML_SELECTED,

				'MAX_DOMAIN_COUNT' => '',
				'MAX_SUBDOMAIN_COUNT' => '',
				'MAX_ALIASES_COUNT' => '',
				'MAX_MAIL_USERS_COUNT' => '',
				'MAX_FTP_USERS_COUNT' => '',
				'MAX_SQLDB_COUNT' => '',
				'MAX_SQL_USERS_COUNT' => '',
				'MAX_TRAFFIC_AMOUNT' => '',
				'MAX_DISK_AMOUNT' => '',
				'SUPPORT_SYSTEM_YES' => $cfg->HTML_SELECTED,
				'SUPPORT_SYSTEM_NO' => ''
			)
		);
	}
}

function check_user_data() {

	global $reseller_ips;

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$username = clean_input($_POST['username']);

	$query = "
		SELECT
			`admin_id`
		FROM
			`admin`
		WHERE
			`admin_name` = ?
	;";

	$rs = exec_query($sql, $query, $username);

	if ($rs->recordCount() != 0) {
		set_page_message(tr('This user name already exist!'), 'warning');
		return false;
	}
	if (!validates_username(clean_input($_POST['username']))) {
		set_page_message(tr("Incorrect username length or syntax!"), 'warning');
		return false;
	}
	if (!chk_password($_POST['pass'])) {
		if ($cfg->PASSWD_STRONG) {
			set_page_message(
				sprintf(
					tr('The password must be at least %s long and contain letters and numbers to be valid.'),
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
		return false;
	}
	if ($_POST['pass'] != $_POST['pass_rep']) {
		set_page_message(tr("Entered passwords do not match!"), 'warning');
		return false;
	}
	if (!chk_email(clean_input($_POST['email']))) {
		set_page_message(tr("Incorrect email syntax!"), 'warning');
		return false;
	}
	if (!easyscp_limit_check($_POST['nreseller_max_domain_cnt'], null)) {
		set_page_message(tr("Incorrect domains limit!"), 'warning');
		return false;
	}
	if (!easyscp_limit_check($_POST['nreseller_max_subdomain_cnt'], -1)) {
		set_page_message(tr("Incorrect subdomains limit!"), 'warning');
		return false;
	}
	if (!easyscp_limit_check($_POST['nreseller_max_alias_cnt'], -1)) {
		set_page_message(tr('Incorrect aliases limit!'), 'warning');
		return false;
	}
	if (!easyscp_limit_check($_POST['nreseller_max_ftp_cnt'], -1)) {
		set_page_message(tr('Incorrect FTP accounts limit!'), 'warning');
		return false;
	}
	if (!easyscp_limit_check($_POST['nreseller_max_mail_cnt'], -1)) {
		set_page_message(tr('Incorrect mail accounts limit!'), 'warning');
		return false;
	}
	if (!easyscp_limit_check($_POST['nreseller_max_sql_db_cnt'], -1)) {
		set_page_message(tr('Incorrect SQL databases limit!'), 'warning');
		return false;
	} else if ($_POST['nreseller_max_sql_db_cnt'] == -1
		&& $_POST['nreseller_max_sql_user_cnt'] != -1) {
		set_page_message(
			tr('SQL databases limit is <em>disabled</em> but SQL users limit not!'),
			'warning'
		);
		return false;
	}
	if (!easyscp_limit_check($_POST['nreseller_max_sql_user_cnt'], -1)) {
		set_page_message(tr('Incorrect SQL users limit!'), 'warning');
		return false;
	} else if ($_POST['nreseller_max_sql_db_cnt'] != -1
		&& $_POST['nreseller_max_sql_user_cnt'] == -1) {
		set_page_message(
			tr('SQL users limit is <em>disabled</em> but SQL databases limit not!'),
			'warning'
		);
		return false;
	}
	if (!easyscp_limit_check($_POST['nreseller_max_traffic'], null)) {
		set_page_message(tr('Incorrect traffic limit!'), 'warning');
		return false;
	}
	if (!easyscp_limit_check($_POST['nreseller_max_disk'], null)) {
		set_page_message(tr('Incorrect disk quota limit!'), 'warning');
		return false;
	}
	if ($reseller_ips == '') {
		set_page_message(
			tr('You must assign at least one IP number for a reseller!'),
			'warning'
		);
		return false;
	}
	return true;
}
?>