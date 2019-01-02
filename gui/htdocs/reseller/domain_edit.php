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
$template = 'reseller/domain_edit.tpl';

if (isset($cfg->HOSTING_PLANS_LEVEL)
	&& $cfg->HOSTING_PLANS_LEVEL === 'admin') {
	user_goto('users.php?psi=last');
}

if (isset($_POST['uaction']) && ('sub_data' === $_POST['uaction'])) {

	// Process data
	if (isset($_SESSION['edit_id'])) {
		$editid = $_SESSION['edit_id'];
	} else {
		unset($_SESSION['edit_id']);
		$_SESSION['edit'] = '_no_';

		user_goto('users.php?psi=last');
	}

	if (check_user_data($_SESSION['user_id'], $editid)) { // Save data to db
		$_SESSION['dedit'] = "_yes_";
		user_goto('users.php?psi=last');
	}
	load_additional_data($_SESSION['user_id'], $editid);
} else {
	// Get user id that comes for edit
	if (isset($_GET['edit_id'])) {
		$editid = $_GET['edit_id'];
	}

	load_user_data($_SESSION['user_id'], $editid);
	$_SESSION['edit_id'] = $editid;
}

gen_editdomain_page($tpl);

// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Domain/Edit'),
		'TR_EDIT_DOMAIN'		=> tr('Edit Domain'),
		'TR_DOMAIN_PROPERTIES'	=> tr('Domain properties'),
		'TR_DOMAIN_NAME'		=> tr('Domain name'),
		'TR_DOMAIN_EXPIRE'		=> tr('Domain expire'),
		'TR_DOMAIN_IP'			=> tr('Domain IP'),
		'TR_PHP_SUPP'			=> tr('PHP support'),
		'TR_PHP_EDIT'			=> tr('PHP editor'),
		'TR_CGI_SUPP'			=> tr('CGI support'),
		'TR_SSL_SUPP'			=> tr('SSL support'),
		'TR_DNS_SUPP'			=> tr('Manual DNS support'),
		'TR_SUBDOMAINS'			=> tr('Max subdomains<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_ALIAS'				=> tr('Max aliases<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_MAIL_ACCOUNT'		=> tr('Mail accounts limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_FTP_ACCOUNTS'		=> tr('FTP accounts limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_SQL_DB'				=> tr('SQL databases limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_SQL_USERS'			=> tr('SQL users limit<br /><em>(-1 disabled, 0 unlimited)</em>'),
		'TR_TRAFFIC'			=> tr('Traffic limit [MB]<br /><em>(0 unlimited)</em>'),
		'TR_DISK'				=> tr('Disk limit [MB]<br /><em>(0 unlimited)</em>'),
		'TR_USER_NAME'			=> tr('Username'),
		'TR_BACKUP'				=> tr('Backup'),
		'TR_BACKUP_DOMAIN'		=> tr('Domain'),
		'TR_BACKUP_SQL'			=> tr('SQL'),
		'TR_BACKUP_FULL'		=> tr('Full'),
		'TR_BACKUP_NO'			=> tr('No'),
		'TR_BACKUP_COUNT'		=> tr('Count backups to disk usage'),
		'TR_UPDATE_DATA'		=> tr('Submit changes'),
		'TR_CANCEL'				=> tr('Cancel'),
		'TR_YES'				=> tr('Yes'),
		'TR_NO'					=> tr('No'),
		'TR_EXPIRE_CHECKBOX'	=> tr('or check if domain should <strong>never</strong> expire'),
		'TR_SU'					=> tr('Su'),
		'TR_MO'					=> tr('Mo'),
		'TR_TU'					=> tr('Tu'),
		'TR_WE'					=> tr('We'),
		'TR_TH'					=> tr('Th'),
		'TR_FR'					=> tr('Fr'),
		'TR_SA'					=> tr('Sa'),
		'TR_JANUARY'			=> tr('January'),
		'TR_FEBRUARY'			=> tr('February'),
		'TR_MARCH'				=> tr('March'),
		'TR_APRIL'				=> tr('April'),
		'TR_MAY'				=> tr('May'),
		'TR_JUNE'				=> tr('June'),
		'TR_JULY'				=> tr('July'),
		'TR_AUGUST'				=> tr('August'),
		'TR_SEPTEMBER'			=> tr('September'),
		'TR_OCTOBER'			=> tr('October'),
		'TR_NOVEMBER'			=> tr('November'),
		'TR_DECEMBER'			=> tr('December'),
		'VL_DATE_FORMAT'		=> jQueryDatepickerDateFormat($cfg->DATE_FORMAT)
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_users_manage.tpl');
gen_reseller_menu($tpl, 'reseller/menu_users_manage.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// Begin function block

/**
 * Load data from sql
 */
function load_user_data($user_id, $domain_id) {

	global $sub, $als, $mail, $ftp, $sql_db, $sql_user, $traff, $disk;

	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			`domain_id`
		FROM
			`domain`
		WHERE
			`domain_id` = ?
		AND
			`domain_created_id` = ?
	";

	$rs = exec_query($sql, $query, array($domain_id, $user_id));

	if ($rs->recordCount() == 0) {
		set_page_message(
			tr('User does not exist or you do not have permission to access this interface!'),
			'error'
		);

		user_goto('users.php?psi=last');
	}

	list(,$sub,,$als,,$mail,,$ftp,,$sql_db,,$sql_user,$traff,$disk) =
		generate_user_props($domain_id);

	load_additional_data($user_id, $domain_id);
} // End of load_user_data()

/**
 * Load additional data
 */
function load_additional_data($user_id, $domain_id) {
	global $domain_name, $domain_expires, $domain_ip, $php_sup, $phpe_sup;
	global $cgi_supp, $ssl_supp, $username, $allowbackup, $countbackup;
	global $dns_supp;

	$sql = EasySCP_Registry::get('Db');
	$cfg = EasySCP_Registry::get('Config');

	// Get domain data
	$query = "
		SELECT
			`domain_name`,
			`domain_expires`,
			`domain_ip_id`,
			`domain_php`,
			`domain_php_edit`,
			`domain_cgi`,
			`domain_ssl`,
			`domain_admin_id`,
			`allowbackup`,
			`domain_disk_countbackup`,
			`domain_dns`
		FROM
			`domain`
		WHERE
			`domain_id` = ?
	;";

	$res = exec_query($sql, $query, $domain_id);
	$data = $res->fetchRow();

	$domain_name = $data['domain_name'];

	$domain_expires = $data['domain_expires'];
	$_SESSION['domain_expires'] = $domain_expires;

	if ($domain_expires == 0) {
		$domain_expires = '';
	} else {
		$date_format = $cfg->DATE_FORMAT;
		$domain_expires = date($date_format, $domain_expires);
	}

	$domain_ip_id	= $data['domain_ip_id'];
	$php_sup		= $data['domain_php'];
	$phpe_sup		= $data['domain_php_edit'];
	$cgi_supp		= $data['domain_cgi'];
	$ssl_supp		= $data['domain_ssl'];
	$allowbackup	= $data['allowbackup'];
	$countbackup	= $data['domain_disk_countbackup'];
	$domain_admin_id= $data['domain_admin_id'];
	$dns_supp		= $data['domain_dns'];
	// Get IP of domain
	$query = "
		SELECT
			`ip_number`,
			`ip_domain`
		FROM
			`server_ips`
		WHERE
			`ip_id` = ?
	";

	$res = exec_query($sql, $query, $domain_ip_id);
	$data = $res->fetchRow();

	$domain_ip = $data['ip_number'] . '&nbsp;(' . $data['ip_domain'] . ')';
	// Get username of domain
	$query = "
		SELECT
			`admin_name`
		FROM
			`admin`
		WHERE
			`admin_id` = ?
		AND
			`admin_type` = 'user'
		AND
			`created_by` = ?
	";

	$res = exec_query($sql, $query, array($domain_admin_id, $user_id));
	$data = $res->fetchRow();

	$username = $data['admin_name'];
} // End of load_additional_data()

/**
 * Show user data
 * @param EasySCP_TemplateEngine $tpl
 */
function gen_editdomain_page($tpl) {
	global $domain_name, $domain_expires, $domain_ip, $php_sup, $phpe_sup;
	global $cgi_supp, $ssl_supp, $sub, $als;
	global $mail, $ftp, $sql_db;
	global $sql_user, $traff, $disk;
	global $username, $allowbackup, $countbackup;
	global $dns_supp;

	$cfg = EasySCP_Registry::get('Config');

	// Fill in the fields
	$domain_name = decode_idna($domain_name);

	$username = decode_idna($username);

	generate_ip_list($tpl, $_SESSION['user_id']);

	if ($allowbackup === 'dmn') {
		$tpl->assign(
			array(
				'BACKUP_DOMAIN' => $cfg->HTML_SELECTED,
				'BACKUP_SQL' 	=> '',
				'BACKUP_FULL' 	=> '',
				'BACKUP_NO' 	=> '',
			)
		);
	} else if ($allowbackup === 'sql')  {
		$tpl->assign(
			array(
				'BACKUP_DOMAIN' => '',
				'BACKUP_SQL' 	=> $cfg->HTML_SELECTED,
				'BACKUP_FULL' 	=> '',
				'BACKUP_NO' 	=> '',
			)
		);
	} else if ($allowbackup === 'full')  {
		$tpl->assign(
			array(
				'BACKUP_DOMAIN' => '',
				'BACKUP_SQL' 	=> '',
				'BACKUP_FULL' 	=> $cfg->HTML_SELECTED,
				'BACKUP_NO' 	=> '',
			)
		);
	} else if ($allowbackup === 'no')  {
		$tpl->assign(
			array(
				'BACKUP_DOMAIN' => '',
				'BACKUP_SQL' 	=> '',
				'BACKUP_FULL' 	=> '',
				'BACKUP_NO' 	=> $cfg->HTML_SELECTED,
			)
		);
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
			'PHP_YES'					=> ($php_sup == 'yes') ? $cfg->HTML_SELECTED : '',
			'PHP_NO'					=> ($php_sup != 'yes') ? $cfg->HTML_SELECTED : '',
			'PHP_EDIT_YES'				=> ($phpe_sup == 'yes') ? $cfg->HTML_SELECTED : '',
			'PHP_EDIT_NO'				=> ($phpe_sup != 'yes') ? $cfg->HTML_SELECTED : '',
			'CGI_YES'					=> ($cgi_supp == 'yes') ? $cfg->HTML_SELECTED : '',
			'CGI_NO'					=> ($cgi_supp != 'yes') ? $cfg->HTML_SELECTED : '',
			'SSL_YES'					=> ($ssl_supp == 'yes') ? $cfg->HTML_SELECTED : '',
			'SSL_NO'					=> ($ssl_supp != 'yes') ? $cfg->HTML_SELECTED : '',
			'DNS_YES'					=> ($dns_supp == 'yes') ? $cfg->HTML_SELECTED : '',
			'DNS_NO'					=> ($dns_supp != 'yes') ? $cfg->HTML_SELECTED : '',
			'BACKUPCOUNT_YES'			=> ($countbackup == 'yes') ? $cfg->HTML_CHECKED : '',
			'BACKUPCOUNT_NO'			=> ($countbackup != 'yes') ? $cfg->HTML_CHECKED : '',
			'VL_EXPIRE_DATE_DISABLED'	=> ($domain_expires == 0) ? $cfg->HTML_DISABLED : '',
			'VL_EXPIRE_NEVER_SELECTED'	=> ($domain_expires == 0) ? $cfg->HTML_CHECKED : '',
			'VL_DOMAIN_NAME'			=> tohtml($domain_name),
			'VL_DOMAIN_EXPIRE'			=> $domain_expires,
			'VL_DOMAIN_IP'				=> $domain_ip,
			'VL_DOM_SUB'				=> $sub,
			'VL_DOM_ALIAS'				=> $als,
			'VL_DOM_MAIL_ACCOUNT'		=> $mail,
			'VL_FTP_ACCOUNTS'			=> $ftp,
			'VL_SQL_DB'					=> $sql_db,
			'VL_SQL_USERS'				=> $sql_user,
			'VL_TRAFFIC'				=> $traff,
			'VL_DOM_DISK'				=> $disk,
			'VL_USER_NAME'				=> tohtml($username)
		)
	);
} // End of gen_editdomain_page()

/**
 * Check input data
 * @param int $reseller_id
 * @param int $user_id
 */
function check_user_data($reseller_id, $user_id) {

	$sql = EasySCP_Registry::get('Db');

	global $sub, $als, $mail, $ftp, $sql_db, $sql_user, $traff, $disk, $domain_php, $domain_php_edit, $domain_cgi, $domain_ssl, $allowbackup, $domain_dns, $domain_expires,$countbackup;

	$domain_expires_date  = (isset($_POST['dmn_expire_date'])) ? clean_input($_POST['dmn_expire_date']) : 0;
	$domain_expires_never = (isset($_POST['dmn_expire_never'])) ? $_POST['dmn_expire_never'] : "off";
	$sub 			= clean_input($_POST['dom_sub']);
	$als 			= clean_input($_POST['dom_alias']);
	$mail 			= clean_input($_POST['dom_mail_acCount']);
	$ftp 			= clean_input($_POST['dom_ftp_acCounts']);
	$sql_db 		= clean_input($_POST['dom_sqldb']);
	$sql_user 		= clean_input($_POST['dom_sql_users']);
	$traff 			= clean_input($_POST['dom_traffic']);
	$disk 			= clean_input($_POST['dom_disk']);

	// $domain_ip = $_POST['domain_ip'];
	$domain_php		= preg_replace("/\_/", "", $_POST['domain_php']);
	$domain_php_edit= preg_replace("/\_/", "", $_POST['domain_php_edit']);
	$domain_cgi		= preg_replace("/\_/", "", $_POST['domain_cgi']);
	$domain_ssl		= preg_replace("/\_/", "", $_POST['domain_ssl']);
	$domain_dns		= preg_replace("/\_/", "", $_POST['domain_dns']);
	$allowbackup	= preg_replace("/\_/", "", $_POST['backup']);
	$countbackup	= preg_replace("/\_/", "", $_POST['countbackup']);

	$ed_error = '';

	list(
		$rsub_max,
		$rals_max,
		$rmail_max,
		$rftp_max,
		$rsql_db_max,
		$rsql_user_max
		) = check_reseller_permissions($_SESSION['user_id'], 'all_permissions');

	if ($rsub_max == "-1") {
		$sub = "-1";
	} elseif (!easyscp_limit_check($sub, -1)) {
		$ed_error .= tr('Incorrect subdomains limit!');
	}

	if ($rals_max == "-1") {
		$als = "-1";
	} elseif (!easyscp_limit_check($als, -1)) {
		$ed_error .= tr('Incorrect aliases limit!');
	}

	if ($rmail_max == "-1") {
		$mail = "-1";
	} elseif (!easyscp_limit_check($mail, -1)) {
		$ed_error .= tr('Incorrect mail accounts limit!');
	}

	if ($rftp_max == "-1") {
		$ftp = "-1";
	} elseif (!easyscp_limit_check($ftp, -1)) {
		$ed_error .= tr('Incorrect FTP accounts limit!');
	}

	if ($rsql_db_max == "-1") {
		$sql_db = "-1";
	} elseif (!easyscp_limit_check($sql_db, -1)) {
		$ed_error .= tr('Incorrect SQL users limit!');
	} else if ($sql_db == -1 && $sql_user != -1) {
		$ed_error .= tr('SQL databases limit is <em>disabled</em>!');
	}

	if ($rsql_user_max == "-1") {
		$sql_user = "-1";
	} elseif (!easyscp_limit_check($sql_user, -1)) {
		$ed_error .= tr('Incorrect SQL databases limit!');
	} else if ($sql_user == -1 && $sql_db != -1) {
		$ed_error .= tr('SQL users limit is <em>disabled</em>!');
	}

	if (!easyscp_limit_check($traff, null)) {
		$ed_error .= tr('Incorrect traffic limit!');
	}
	if (!easyscp_limit_check($disk, null)) {
		$ed_error .= tr('Incorrect disk quota limit!');
	}

	list($usub_current, $usub_max,
		$uals_current, $uals_max,
		$umail_current, $umail_max,
		$uftp_current, $uftp_max,
		$usql_db_current, $usql_db_max,
		$usql_user_current, $usql_user_max,
		$utraff_max, $udisk_max
	) = generate_user_props($user_id);

	$previous_utraff_max = $utraff_max;

	list($rdmn_current, $rdmn_max,
		$rsub_current, $rsub_max,
		$rals_current, $rals_max,
		$rmail_current, $rmail_max,
		$rftp_current, $rftp_max,
		$rsql_db_current, $rsql_db_max,
		$rsql_user_current, $rsql_user_max,
		$rtraff_current, $rtraff_max,
		$rdisk_current, $rdisk_max
	) = get_reseller_default_props($sql, $reseller_id);

	list(,,,,,,$utraff_current, $udisk_current) = generate_user_traffic($user_id);

	if (empty($ed_error)) {
		calculate_user_dvals($sub, $usub_current, $usub_max, $rsub_current, $rsub_max, $ed_error, tr('Subdomain'));
		calculate_user_dvals($als, $uals_current, $uals_max, $rals_current, $rals_max, $ed_error, tr('Alias'));
		calculate_user_dvals($mail, $umail_current, $umail_max, $rmail_current, $rmail_max, $ed_error, tr('Mail'));
		calculate_user_dvals($ftp, $uftp_current, $uftp_max, $rftp_current, $rftp_max, $ed_error, tr('FTP'));
		calculate_user_dvals($sql_db, $usql_db_current, $usql_db_max, $rsql_db_current, $rsql_db_max, $ed_error, tr('SQL Database'));
	}

	if (empty($ed_error)) {
		$query = "
			SELECT
				COUNT(distinct su.sqlu_name) AS cnt
			FROM
				`sql_user` AS su,
				`sql_database` AS sd
			WHERE
				su.`sqld_id` = sd.`sqld_id`
			AND
				sd.`domain_id` = ?
		;";

		$rs = exec_query($sql, $query, $_SESSION['edit_id']);
		calculate_user_dvals($sql_user, $rs->fields['cnt'], $usql_user_max, $rsql_user_current, $rsql_user_max, $ed_error, tr('SQL User'));
	}

	if (empty($ed_error)) {
		calculate_user_dvals($traff, $utraff_current / 1024 / 1024 , $utraff_max, $rtraff_current, $rtraff_max, $ed_error, tr('Traffic'));
		calculate_user_dvals($disk, $udisk_current / 1024 / 1024, $udisk_max, $rdisk_current, $rdisk_max, $ed_error, tr('Disk'));
	}

	if (empty($ed_error)) {
		// Set domains status to 'change' to update mod_cband's limit
		if ($previous_utraff_max != $utraff_max) {
			$query = "UPDATE `domain` SET `status` = 'change' WHERE `domain_id` = ?";
			exec_query($sql, $query, $user_id);
			$query = "UPDATE `subdomain` SET `status` = 'change' WHERE `domain_id` = ?";
			exec_query($sql, $query, $user_id);

			send_request('110 DOMAIN domain '.$user_id);
		}

		$user_props  = "$usub_current;$usub_max;";
		$user_props .= "$uals_current;$uals_max;";
		$user_props .= "$umail_current;$umail_max;";
		$user_props .= "$uftp_current;$uftp_max;";
		$user_props .= "$usql_db_current;$usql_db_max;";
		$user_props .= "$usql_user_current;$usql_user_max;";
		$user_props .= "$utraff_max;";
		$user_props .= "$udisk_max;";
		// $user_props .= "$domain_ip;";
		$user_props .= "$domain_php;";
		$user_props .= "$domain_php_edit;";
		$user_props .= "$domain_cgi;";
		$user_props .= "$domain_ssl;";
		$user_props .= "$allowbackup;";
		$user_props .= "$domain_dns;";
		$user_props .= "$countbackup";
		update_user_props($user_id, $user_props);

		$domain_expires = $_SESSION['domain_expires'];

		// Set domain expire date
		if ($domain_expires_never != "on") {
			$domain_expires = strtotime($domain_expires_date);
		} else {
			$domain_expires = "0";
		}
		update_expire_date($user_id, $domain_expires);

		$reseller_props = "$rdmn_current;$rdmn_max;";
		$reseller_props .= "$rsub_current;$rsub_max;";
		$reseller_props .= "$rals_current;$rals_max;";
		$reseller_props .= "$rmail_current;$rmail_max;";
		$reseller_props .= "$rftp_current;$rftp_max;";
		$reseller_props .= "$rsql_db_current;$rsql_db_max;";
		$reseller_props .= "$rsql_user_current;$rsql_user_max;";
		$reseller_props .= "$rtraff_current;$rtraff_max;";
		$reseller_props .= "$rdisk_current;$rdisk_max";

		if (!update_reseller_props($reseller_id, $reseller_props)) {
			set_page_message(
				tr('Domain properties could not be updated!'),
				'error'
			);

			return false;
		}

		// Backup Settings
		$query = "UPDATE `domain` SET `allowbackup` = ? WHERE `domain_id` = ?";
		exec_query($sql, $query, array($allowbackup, $user_id));

		// update the sql quotas, too
		$query = "SELECT `domain_name` FROM `domain` WHERE `domain_id` = ?";
		$rs = exec_query($sql, $query, array($user_id));
		$temp_dmn_name = $rs->fields['domain_name'];

		$query = "SELECT COUNT(`name`) AS cnt FROM `quotalimits` WHERE `name` = ?";
		$rs = exec_query($sql, $query, $temp_dmn_name);
		if ($rs->fields['cnt'] > 0) {
			// we need to update it
			if ($disk == 0) {
				$dlim = 0;
			} else {
				$dlim = $disk * 1024 * 1024;
			}

			$query = "UPDATE `quotalimits` SET `bytes_in_avail` = ? WHERE `name` = ?";
			exec_query($sql, $query, array($dlim, $temp_dmn_name));
		}

		set_page_message(
			tr('Domain properties updated successfully!'),
			'success'
		);

		return true;
	} else {
		set_page_message(
			$ed_error,
			'error'
		);

		return false;
	}
} // End of check_user_data()

function calculate_user_dvals($data, $u, &$umax, &$r, $rmax, &$err, $obj) {
	if ($rmax == -1 && $umax >= 0) {
		if ($u > 0) {
			$err .= tr('The <em>%s</em> service cannot be disabled!', $obj) . tr('There are <em>%s</em> records on the system!', $obj);
			return;
		} else if ($data != -1){
			$err .= tr('The <em>%s</em> have to be disabled!', $obj) . tr('The admin has <em>%s</em> disabled on this system!', $obj);
			return;
		} else {
			$umax = $data;
		}
		return;
	} else if ($rmax == 0 && $umax == -1) {
		if ($data == -1) {
			return;
		} else if ($data == 0) {
			$umax = $data;
			return;
		} else if ($data > 0) {
			$umax = $data;
			$r += $umax;
			return;
		}
	} else if ($rmax == 0 && $umax == 0) {
		if ($data == -1) {
			if ($u > 0) {
				$err .= tr('The <em>%s</em> service cannot be disabled!', $obj) . tr('There are <em>%s</em> records on the system!', $obj);
			} else {
				$umax = $data;
			}

			return;
		} else if ($data == 0) {
			return;
		} else if ($data > 0) {
			if ($u > $data) {
				$err .= tr('The <em>%s</em> service cannot be limited!', $obj) . tr('Specified number is smaller than <em>%s</em> records, present on the system!', $obj);
			} else {
				$umax = $data;
				$r += $umax;
			}
			return;
		}
	} else if ($rmax == 0 && $umax > 0) {
		if ($data == -1) {
			if ($u > 0) {
				$err .= tr('The <em>%s</em> service cannot be disabled!', $obj) . tr('There are <em>%s</em> records on the system!', $obj);
			} else {
				$r -= $umax;
				$umax = $data;
			}
			return;
		} else if ($data == 0) {
			$r -= $umax;
			$umax = $data;
			return;
		} else if ($data > 0) {
			if ($u > $data) {
				$err .= tr('The <em>%s</em> service cannot be limited!', $obj) . tr('Specified number is smaller than <em>%s</em> records, present on the system!', $obj);
			} else {
				if ($umax > $data) {
					$data_dec = $umax - $data;
					$r -= $data_dec;
				} else {
					$data_inc = $data - $umax;
					$r += $data_inc;
				}
				$umax = $data;
			}
			return;
		}
	} else if ($rmax > 0 && $umax == -1) {
		if ($data == -1) {
			return;
		} else if ($data == 0) {
			$err .= tr('The <em>%s</em> service cannot be unlimited!', $obj) . tr('There are reseller limits for the <em>%s</em> service!', $obj);
			return;
		} else if ($data > 0) {
			if ($r + $data > $rmax) {
				$err .= tr('The <em>%s</em> service cannot be limited!', $obj) . tr('You are exceeding reseller limits for the <em>%s</em> service!', $obj);
			} else {
				$r += $data;

				$umax = $data;
			}

			return;
		}
	} else if ($rmax > 0 && $umax == 0) {
		// We can't get here! This clone is present only for sample purposes;
		throw new EasySCP_Exception(
			"FIXME: ". __FILE__ .":". __LINE__." \$data = " . $data
		);
	} else if ($rmax > 0 && $umax > 0) {
		if ($data == -1) {
			if ($u > 0) {
				$err .= tr('The <em>%s</em> service cannot be disabled!', $obj) . tr('There are <em>%s</em> records on the system!', $obj);
			} else {
				$r -= $umax;
				$umax = $data;
			}

			return;
		} else if ($data == 0) {
			$err .= tr('The <em>%s</em> service cannot be unlimited!', $obj) . tr('There are reseller limits for the <em>%s</em> service!', $obj);

			return;
		} else if ($data > 0) {
			if ($u > $data) {
				$err .= tr('The <em>%s</em> service cannot be limited!', $obj) . tr('Specified number is smaller than <em>%s</em> records, present on the system!', $obj);
			} else {
				if ($umax > $data) {
					$data_dec = $umax - $data;
					$r -= $data_dec;
				} else {
					$data_inc = $data - $umax;

					if ($r + $data_inc > $rmax) {
						$err .= tr('The <em>%s</em> service cannot be limited!', $obj) . tr('You are exceeding reseller limits for the <em>%s</em> service!', $obj);
						return;
					}

					$r += $data_inc;
				}

				$umax = $data;
			}

			return;
		}
	}
} // End of calculate_user_dvals()
?>