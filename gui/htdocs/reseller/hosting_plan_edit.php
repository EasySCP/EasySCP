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

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'reseller/hosting_plan_edit.tpl';

global $hpid;

/**
 * Dynamic page process
 */

if (isset($_POST['uaction']) && ('edit_plan' === $_POST['uaction'])) {
	// Process data
	if (check_data_iscorrect($tpl)) { // Save data to db
		save_data_to_db();
	} else {
		restore_form($tpl, $sql);
	}
} else {
	// Get hosting plan id that comes for edit
	if (isset($_GET['hpid'])) {
		$hpid = $_GET['hpid'];
	}

	gen_load_ehp_page($tpl, $sql, $hpid, $_SESSION['user_id']);
}

// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Reseller/Edit hosting plan'),
		'TR_HOSTING PLAN PROPS'	=> tr('Hosting plan properties'),
		'TR_TEMPLATE_NAME'		=> tr('Template name'),
		'TR_MAX_SUBDOMAINS'		=> tr('Max subdomains<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAX_ALIASES'		=> tr('Max aliases<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAX_MAILACCOUNTS'	=> tr('Mail accounts limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAX_FTP'			=> tr('FTP accounts limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAX_SQL'			=> tr('SQL databases limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAX_SQL_USERS'		=> tr('SQL users limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAX_TRAFFIC'		=> tr('Traffic limit [MB]<br /><em>(0 unlimited)</em>'),
		'TR_DISK_LIMIT'			=> tr('Disk limit [MB]<br /><em>(0 unlimited)</em>'),
		'TR_PHP'				=> tr('PHP'),
		'TR_PHP_EDIT'			=> tr('PHP editor'),
		'TR_CGI'				=> tr('CGI / Perl'),
		'TR_SSL'				=> tr('SSL support'),
		'TR_DNS'				=> tr('Allow adding records to DNS zone'),
		'TR_BACKUP'				=> tr('Backup'),
		'TR_BACKUP_DOMAIN'		=> tr('Domain'),
		'TR_BACKUP_SQL'			=> tr('SQL'),
		'TR_BACKUP_FULL'		=> tr('Full'),
		'TR_BACKUP_NO'			=> tr('No'),
		'TR_BACKUP_COUNT'		=> tr('Count backups to disk usage'),
		'TR_APACHE_LOGS'		=> tr('Apache logfiles'),
		'TR_AWSTATS'			=> tr('AwStats'),
		'TR_YES'				=> tr('Yes'),
		'TR_NO'					=> tr('No'),
		'TR_BILLING_PROPS'		=> tr('Billing Settings'),
		'TR_PRICE'				=> tr('Price'),
		'TR_SETUP_FEE'			=> tr('Setup fee'),
		'TR_VALUE'				=> tr('Currency'),
		'TR_PAYMENT'			=> tr('Payment period'),
		'TR_STATUS'				=> tr('Available for purchasing'),
		'TR_TEMPLATE_DESCRIPTON'=> tr('Description'),
		'TR_EXAMPLE'			=> tr('(e.g. EUR)'),
		'TR_TOS_PROPS'			=> tr('Term Of Service'),
		'TR_TOS_NOTE'			=> tr('<strong>Optional:</strong> Leave this field empty if you do not want term of service for this hosting plan.'),
		'TR_TOS_DESCRIPTION'	=> tr('Text'),
		'TR_EDIT_HOSTING_PLAN'	=> tr('Update plan'),
		'TR_UPDATE_PLAN'		=> tr('Update plan')
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_hosting_plan.tpl');
gen_reseller_menu($tpl, 'reseller/menu_hosting_plan.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

/**
 * Function definitions
 */

/**
 * Restore form on any error
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 */
function restore_form($tpl, $sql) {
	$cfg = EasySCP_Registry::get('Config');

	$tpl->assign(
		array(
			'HP_NAME_VALUE' 		=> clean_input($_POST['hp_name'], true),
			'HP_DESCRIPTION_VALUE' 	=> clean_input($_POST['hp_description'], true),
			'TR_MAX_SUB_LIMITS' 	=> clean_input($_POST['hp_sub'], true),
			'TR_MAX_ALS_VALUES' 	=> clean_input($_POST['hp_als'], true),
			'HP_MAIL_VALUE' 		=> clean_input($_POST['hp_mail'], true),
			'HP_FTP_VALUE' 			=> clean_input($_POST['hp_ftp'], true),
			'HP_SQL_DB_VALUE' 		=> clean_input($_POST['hp_sql_db'], true),
			'HP_SQL_USER_VALUE' 	=> clean_input($_POST['hp_sql_user'], true),
			'HP_TRAFF_VALUE' 		=> clean_input($_POST['hp_traff'], true),
			'HP_TRAFF' 				=> clean_input($_POST['hp_traff'], true),
			'HP_DISK_VALUE' 		=> clean_input($_POST['hp_disk'], true),
			'HP_PRICE' 				=> clean_input($_POST['hp_price'], true),
			'HP_SETUPFEE' 			=> clean_input($_POST['hp_setupfee'], true),
			'HP_VALUE' 				=> clean_input($_POST['hp_value'], true),
			'HP_PAYMENT' 			=> clean_input($_POST['hp_payment'], true),
			'HP_TOS_VALUE' 			=> clean_input($_POST['hp_tos'], true),
			'TR_PHP_YES' 			=> ($_POST['php'] == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_PHP_NO' 			=> ($_POST['php'] == '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_PHPEY'				=> ($_POST['php_edit'] == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_PHPEN'				=> ($_POST['php_edit'] == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_CGI_YES' 			=> ($_POST['cgi'] == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_CGI_NO' 			=> ($_POST['cgi'] == '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_SSL_YES'			=> ($_POST['ssl'] == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_SSL_NO'				=> ($_POST['ssl'] == '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_DNS_YES'		 	=> ($_POST['dns'] == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_DNS_NO' 			=> ($_POST['dns'] == '_no_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPD' 			=> ($_POST['backup'] == '_dmn_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPS' 			=> ($_POST['backup'] == '_sql_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPF' 			=> ($_POST['backup'] == '_full_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPN' 			=> ($_POST['backup']== '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_BACKUPCOUNT_YES'	=> ($_POST['countbackup']== '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_BACKUPCOUNT_NO'		=> ($_POST['countbackup']== '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_STATUS_YES' 		=> ($_POST['status']) ? $cfg->HTML_CHECKED : '',
			'TR_STATUS_NO' 			=> (!$_POST['status']) ? $cfg->HTML_CHECKED : ''
		)
	);
} // end of function restore_form()

/**
 * Generate load data from sql for requested hosting plan
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $hpid
 * @param int $admin_id
 */
function gen_load_ehp_page($tpl, $sql, $hpid, $admin_id) {
	$cfg = EasySCP_Registry::get('Config');

	$_SESSION['hpid'] = $hpid;

	if (isset($cfg->HOSTING_PLANS_LEVEL)
		&& $cfg->HOSTING_PLANS_LEVEL === 'admin') {
		$query = "
			SELECT
				*
			FROM
				`hosting_plans`
			WHERE
				`id` = ?
			;
		";

		$res = exec_query($sql, $query, $hpid);

		$readonly = $cfg->HTML_READONLY;
		$disabled = $cfg->HTML_DISABLED;
		$edit_hp = tr('View hosting plan');

		$tpl->assign('FORM', '');

	} else {
		$query = "
			SELECT
				*
			FROM
				`hosting_plans`
			WHERE
				`reseller_id` = ?
			AND
				`id` = ?
			;
		";

		$res = exec_query($sql, $query, array($admin_id, $hpid));
		$readonly = '';
		$disabled = '';
		$edit_hp = tr('Edit hosting plan');
	}

	if ($res->rowCount() !== 1) { // Error
		user_goto('hosting_plan.php');
	}

	$data = $res->fetchRow();

	$props = unserialize($data['props']);
	$description = $data['description'];
	$price = $data['price'];
	$setup_fee = $data['setup_fee'];
	$value = $data['value'];
	$payment = $data['payment'];
	$status = $data['status'];
	$tos = $data['tos'];

	$hp_php = $props['allow_php'];
	$hp_phpe = $props['allow_phpe'];
	$hp_cgi = $props['allow_cgi'];
	$hp_sub = $props['subdomain_cnt'];
	$hp_als = $props['alias_cnt'];
	$hp_mail = $props['mail_cnt'];
	$hp_ftp = $props['ftp_cnt'];
	$hp_sql_db = $props['db_cnt'];
	$hp_sql_user = $props['sqluser_cnt'];
	$hp_traff = $props['traffic'];
	$hp_disk = $props['disk'];
	$hp_backup = $props['allow_backup'];
	$hp_countbackup = $props['disk_countbackup'];
	$hp_dns = $props['allow_dns'];
	$hp_ssl = $props['allow_ssl'];

	$hp_name = $data['name'];

	if ($description == '')
		$description = '';

	if ($tos == '') {
		$tos = '';
	}

	if ($payment == '') {
		$payment = '';
	}

	if ($value == '') {
		$value = '';
	}

	list(
		$rsub_max,
		$rals_max,
		$rmail_max,
		$rftp_max,
		$rsql_db_max,
		$rsql_user_max
		) = check_reseller_permissions($_SESSION['user_id'], 'all_permissions');

	if ($rsub_max == "-1") $tpl->assign('ALIAS_EDIT', '');
	if ($rals_max == "-1") $tpl->assign('SUBDOMAIN_EDIT', '');
	if ($rmail_max == "-1") $tpl->assign('MAIL_EDIT', '');
	if ($rftp_max == "-1") $tpl->assign('FTP_EDIT', '');
	if ($rsql_db_max == "-1") $tpl->assign('SQL_DB_EDIT', '');
	if ($rsql_user_max == "-1") $tpl->assign('SQL_USER_EDIT', '');

	$tpl->assign(
		array(
			'HP_NAME_VALUE' => tohtml($hp_name),
			'TR_EDIT_HOSTING_PLAN' => $edit_hp,
			'TR_MAX_SUB_LIMITS' => $hp_sub,
			'TR_MAX_ALS_VALUES' => $hp_als,
			'HP_MAIL_VALUE' => $hp_mail,
			'HP_FTP_VALUE' => $hp_ftp,
			'HP_SQL_DB_VALUE' => $hp_sql_db,
			'HP_SQL_USER_VALUE' => $hp_sql_user,
			'HP_TRAFF_VALUE' => $hp_traff,
			'HP_DISK_VALUE' => $hp_disk,
			'HP_DESCRIPTION_VALUE' => tohtml($description),
			'HP_PRICE' => tohtml($price),
			'HP_SETUPFEE' => tohtml($setup_fee),
			'HP_VALUE' => tohtml($value),
			'READONLY' => $readonly,
			'DISBLED' => $disabled,
			'HP_PAYMENT' => tohtml($payment),
			'HP_TOS_VALUE' => tohtml($tos),

			'TR_PHP_YES' 	=> ($hp_php == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_PHP_NO' 	=> ($hp_php == '_no_')	? $cfg->HTML_CHECKED : '',
			'TR_PHPEY'		=> ($hp_phpe === '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_PHPEN'		=> ($hp_phpe === '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_CGI_YES' 	=> ($hp_cgi == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_CGI_NO' 	=> ($hp_cgi == '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_SSL_YES'	=> ($hp_ssl == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_SSL_NO'		=> ($hp_ssl == '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_DNS_YES' 	=> ($hp_dns == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_DNS_NO' 	=> ($hp_dns == '_no_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPD' 	=> ($hp_backup == '_dmn_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPS' 	=> ($hp_backup == '_sql_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPF' 	=> ($hp_backup == '_full_') ? $cfg->HTML_CHECKED : '',
			'VL_BACKUPN' 	=> ($hp_backup == '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_BACKUPCOUNT_YES'	=> ($hp_countbackup == '_yes_') ? $cfg->HTML_CHECKED : '',
			'TR_BACKUPCOUNT_NO'	=> ($hp_countbackup == '_no_') ? $cfg->HTML_CHECKED : '',
			'TR_STATUS_YES' => ($status) ? $cfg->HTML_CHECKED : '',
			'TR_STATUS_NO' 	=> (!$status) ? $cfg->HTML_CHECKED : '',
		)
	);
} // end of gen_load_ehp_page()

/**
 * Check correction of input data
 * @param EasySCP_TemplateEngine $tpl
 */
function check_data_iscorrect($tpl) {
	global $hp_name, $hp_php, $hp_phpe, $hp_cgi, $hp_ssl;
	global $hp_sub, $hp_als, $hp_mail;
	global $hp_ftp, $hp_sql_db, $hp_sql_user;
	global $hp_traff, $hp_disk, $hp_countbackup;
	global $hpid;
	global $price, $setup_fee;
	global $hp_backup, $hp_dns;

	$ahp_error = array();
	$hp_name = clean_input($_POST['hp_name']);
	$hp_sub = clean_input($_POST['hp_sub']);
	$hp_als = clean_input($_POST['hp_als']);
	$hp_mail = clean_input($_POST['hp_mail']);
	$hp_ftp = clean_input($_POST['hp_ftp']);
	$hp_sql_db = clean_input($_POST['hp_sql_db']);
	$hp_sql_user = clean_input($_POST['hp_sql_user']);
	$hp_traff = clean_input($_POST['hp_traff']);
	$hp_disk = clean_input($_POST['hp_disk']);
	$price = clean_input($_POST['hp_price']);
	$setup_fee = clean_input($_POST['hp_setupfee']);

	if (isset($_SESSION['hpid'])) {
		$hpid = $_SESSION['hpid'];
	} else {
		$ahp_error[] = tr('Undefined reference to data!');
	}

	// put hosting plan id into session value
	$_SESSION['hpid'] = $hpid;

	// Get values from previous page and check him correction
	if (isset($_POST['php'])) {
		$hp_php = $_POST['php'];
	}

	if (isset($_POST['php_edit'])) {
		$hp_phpe = $_POST['php_edit'];
	}

	if (isset($_POST['cgi'])) {
		$hp_cgi = $_POST['cgi'];
	}

	if (isset($_POST['ssl'])) {
		$hp_ssl = $_POST['ssl'];
	}

	if (isset($_POST['dns'])) {
		$hp_dns = $_POST['dns'];
	}

    if (isset($_POST['backup'])) {
    	$hp_backup = $_POST['backup'];
    }

	if (isset($_POST['countbackup'])){
		$hp_countbackup = $_POST['countbackup'];
	}

    list(
		$rsub_max,
		$rals_max,
		$rmail_max,
		$rftp_max,
		$rsql_db_max,
		$rsql_user_max
		) = check_reseller_permissions($_SESSION['user_id'], 'all_permissions');

	if ($rsub_max == "-1") {
		$hp_sub = "-1";
	} elseif (!easyscp_limit_check($hp_sub, -1)) {
		$ahp_error[] = tr('Incorrect subdomains limit!');
	}

	if ($rals_max == "-1") {
		$hp_als = "-1";
	} elseif (!easyscp_limit_check($hp_als, -1)) {
		$ahp_error[] = tr('Incorrect aliases limit!');
	}

	if ($rmail_max == "-1") {
		$hp_mail = "-1";
	} elseif (!easyscp_limit_check($hp_mail, -1)) {
		$ahp_error[] = tr('Incorrect mail accounts limit!');
	}

	if ($rftp_max == "-1") {
		$hp_ftp = "-1";
	} elseif (!easyscp_limit_check($hp_ftp, -1)) {
		$ahp_error[] = tr('Incorrect FTP accounts limit!');
	}

	if ($rsql_db_max == "-1") {
		$hp_sql_db = "-1";
	} elseif (!easyscp_limit_check($hp_sql_db, -1)) {
		$ahp_error[] = tr('Incorrect SQL users limit!');
	} else if ($hp_sql_db == -1 && $hp_sql_user != -1) {
		$ahp_error[] = tr('SQL databases limit is <em>disabled</em>!');
	}

	if ($rsql_user_max == "-1") {
		$hp_sql_user = "-1";
	} elseif (!easyscp_limit_check($hp_sql_user, -1)) {
		$ahp_error[] = tr('Incorrect SQL databases limit!');
	} else if ($hp_sql_user == -1 && $hp_sql_db != -1) {
		$ahp_error[] = tr('SQL users limit is <em>disabled</em>!');
	}

	if (!easyscp_limit_check($hp_traff, null)) {
		$ahp_error[] = tr('Incorrect traffic limit!');
	}

	if (!easyscp_limit_check($hp_disk, null)) {
		$ahp_error[] = tr('Incorrect disk quota limit!');
	}

	if (!is_numeric($price)) {
		$ahp_error[] = tr('Price must be a number!');
	}

	if (!is_numeric($setup_fee)) {
		$ahp_error[] = tr('Setup fee must be a number!');
	}

	if (empty($ahp_error)) {
		return true;
	} else {
		set_page_message(format_message($ahp_error), 'error');
		return false;
	}
} // end of check_data_iscorrect()

/**
 * Add new host plan to DB
 */
function save_data_to_db() {
	global $tpl;
	global $hp_name, $hp_php, $hp_phpe, $hp_cgi, $hp_ssl;
	global $hp_sub, $hp_als, $hp_mail;
	global $hp_ftp, $hp_sql_db, $hp_sql_user;
	global $hp_traff, $hp_disk, $hp_countbackup;
	global $hpid;
	global $hp_backup, $hp_dns;
//	global $tos;

	$sql = EasySCP_Registry::get('Db');

	$err_msg = '';
	$description = clean_input($_POST['hp_description']);
	$price = clean_input($_POST['hp_price']);
	$setup_fee = clean_input($_POST['hp_setupfee']);
	$value = clean_input($_POST['hp_value']);
	$payment = clean_input($_POST['hp_payment']);
	$status = clean_input($_POST['status']);
	$tos = clean_input($_POST['hp_tos']);

	//$hp_props = "$hp_php;$hp_cgi;$hp_sub;$hp_als;$hp_mail;$hp_ftp;$hp_sql_db;" .
	//	"$hp_sql_user;$hp_traff;$hp_disk;$hp_backup;$hp_dns;$hp_ssl";
	
	$newProps = array(
			'allow_php'	=> $hp_php,
			'allow_phpe'=> $hp_phpe,
			'allow_cgi'	=> $hp_cgi,
			'subdomain_cnt'	=> $hp_sub,
			'alias_cnt'	=>	$hp_als,
			'mail_cnt'	=> $hp_mail,
			'ftp_cnt'	=> $hp_ftp,
			'db_cnt'	=> $hp_sql_db,
			'sqluser_cnt'	=> $hp_sql_user,
			'traffic'	=> $hp_traff,
			'disk'		=> $hp_disk,
			'disk_countbackup'	=> $hp_countbackup,
			'allow_backup'	=> $hp_backup,
			'allow_dns'	=> $hp_dns,
			'allow_ssl'	=> $hp_ssl,
		);
	$hp_props=  serialize($newProps);
	
	$admin_id = $_SESSION['user_id'];

	if (reseller_limits_check($sql, $err_msg, $admin_id, $hpid, $hp_props)) {
		if (!empty($err_msg)) {
			set_page_message($err_msg, 'error');
			restore_form($tpl, $sql);
			return false;
		} else {
			$query = "
				UPDATE
					`hosting_plans`
				SET
					`name` = ?,
					`description` = ?,
					`props` = ?,
					`price` = ?,
					`setup_fee` = ?,
					`value` = ?,
					`payment` = ?,
					`status` = ?,
					`tos` = ?
				WHERE
					`id` = ?
				;
			";

			exec_query(
				$sql,
				$query,
				array(
					$hp_name, $description, $hp_props, $price, $setup_fee,
					$value, $payment, $status, $tos, $hpid
				)
			);

			$_SESSION['hp_updated'] = '_yes_';
			user_goto('hosting_plan.php');
		}
	} else {
		set_page_message(
			tr("Hosting plan values exceed reseller maximum values!"),
			'warning'
		);

		restore_form($tpl, $sql);
		return false;
	}
} // end of save_data_to_db()
?>