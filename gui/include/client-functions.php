<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2010 by ispCP | http://isp-control.net
 * @copyright 	2010-2015 by Easy Server Control Panel - http://www.easyscp.net
 * @version 	SVN: $Id$
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 *
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Original Code is "VHCS - Virtual Hosting Control System".
 *
 * The Initial Developer of the Original Code is moleSoftware GmbH.
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 *
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 *
 * Portions created by the EasySCP Team are Copyright (C) 2010-2012 by
 * Easy Server Control Panel. All Rights Reserved.
 */

function get_domain_default_props($domain_admin_id) {

	$sql_param = array(
		':domain_admin_id' => $domain_admin_id
	);

	$sql_query = "
		SELECT
			domain_id,
			domain_name,
			domain_gid,
			domain_uid,
			domain_created_id,
			domain_created,
			domain_expires,
			domain_last_modified,
			domain_mailacc_limit,
			domain_ftpacc_limit,
			domain_traffic_limit,
			domain_sqld_limit,
			domain_sqlu_limit,
			status,
			domain_alias_limit,
			domain_subd_limit,
			domain_ip_id,
			domain_disk_limit,
			domain_disk_usage,
			domain_php,
			domain_php_edit,
			domain_cgi,
			allowbackup,
			domain_dns,
			domain_ssl,
			ssl_key,
			ssl_cert,
			ssl_cacert,
			ssl_status
		FROM
			domain
		WHERE
			domain_admin_id = :domain_admin_id;
	";

	DB::prepare($sql_query);
	return DB::execute($sql_param, true);
}

function get_domain_running_sub_cnt($sql, $domain_id) {

	$query = "
		SELECT
			COUNT(*) AS cnt
		FROM
			`subdomain`
		WHERE
			`domain_id` = ?
		;
	";

	$rs = exec_query($sql, $query, $domain_id);

	$sub_count = $rs->fields['cnt'];

	$query = "
		SELECT
			COUNT(`subdomain_alias_id`) AS cnt
		FROM
			`subdomain_alias`
		WHERE
			`alias_id` IN (SELECT `alias_id` FROM `domain_aliasses` WHERE `domain_id` = ?)
		;
	";

	$rs = exec_query($sql, $query, $domain_id);

	$alssub_count = $rs->fields['cnt'];

	return $sub_count+$alssub_count;
}

function get_domain_running_als_cnt($sql, $domain_id) {

	$query = "
		SELECT
			COUNT(*) AS cnt
		FROM
			`domain_aliasses`
		WHERE
			`domain_id` = ?
		;
	";

	$rs = exec_query($sql, $query, $domain_id);

	$als_count = $rs->fields['cnt'];

	return $als_count;
}

function get_domain_running_mail_acc_cnt($sql, $domain_id) {

	$query = "
		SELECT
			COUNT(`mail_id`) AS cnt
		FROM
			`mail_users`
		WHERE
			`mail_type` RLIKE ?
		AND
			`mail_type` NOT LIKE ?
		AND
			`domain_id` = ?
	";

	$rs = exec_query($sql, $query, array('normal_', 'normal_catchall', $domain_id));
	$dmn_mail_acc = $rs->fields['cnt'];

	$rs = exec_query($sql, $query, array('alias_', 'alias_catchall', $domain_id));
	$als_mail_acc = $rs->fields['cnt'];

	$rs = exec_query($sql, $query, array('subdom_', 'subdom_catchall', $domain_id));
	$sub_mail_acc = $rs->fields['cnt'];

	$rs = exec_query($sql, $query, array('alssub_', 'alssub_catchall', $domain_id));
	$alssub_mail_acc = $rs->fields['cnt'];

	return array(
		$dmn_mail_acc + $als_mail_acc + $sub_mail_acc + $alssub_mail_acc,
		$dmn_mail_acc,
		$als_mail_acc,
		$sub_mail_acc,
		$alssub_mail_acc
	);
}

function get_domain_running_dmn_ftp_acc_cnt($sql, $domain_id) {

	$cfg = EasySCP_Registry::get('Config');
	$ftp_separator = $cfg->FTP_USERNAME_SEPARATOR;

	$query = "
		SELECT
			`domain_name`
		FROM
			`domain`
		WHERE
			`domain_id` = ?
		;
	";

	$rs = exec_query($sql, $query, $domain_id);

	$dmn_name = $rs->fields['domain_name'];

	$query = "
		SELECT
			COUNT(*) AS cnt
		FROM
			`ftp_users`
		WHERE
			`userid` LIKE ?
		;
	";

	$rs = exec_query($sql, $query, '%' . $ftp_separator . $dmn_name);

	// domain ftp account count
	return $rs->fields['cnt'];
}

function get_domain_running_sub_ftp_acc_cnt($sql, $domain_id) {

	$cfg = EasySCP_Registry::get('Config');
	$ftp_separator = $cfg->FTP_USERNAME_SEPARATOR;

	$query = "
		SELECT
			`subdomain_name`
		FROM
			`subdomain`
		WHERE
			`domain_id` = ?
		ORDER BY
			`subdomain_id`
		;
	";

	$query2 = "
		SELECT
			`domain_name`
		FROM
			`domain`
		WHERE
			`domain_id` = ?
		;
	";

	$dmn = exec_query($sql, $query2, $domain_id);
	$rs = exec_query($sql, $query, $domain_id);

	$sub_ftp_acc_cnt = 0;

	while (!$rs->EOF) {
		$sub_name = $rs->fields['subdomain_name'];

		$query = "
			SELECT
				COUNT(*) AS cnt
			FROM
				`ftp_users`
			WHERE
				`userid` LIKE ?
			;
		";

		$rs_cnt = exec_query($sql, $query, '%' . $ftp_separator . $sub_name . '.' . $dmn->fields['domain_name']);

		$sub_ftp_acc_cnt += $rs_cnt->fields['cnt'];

		$rs->moveNext();
	}

	return $sub_ftp_acc_cnt;
}

function get_domain_running_als_ftp_acc_cnt($sql, $domain_id) {

	$cfg = EasySCP_Registry::get('Config');

	$ftp_separator = $cfg->FTP_USERNAME_SEPARATOR;

	$query = "
		SELECT
			`alias_name`
		FROM
			`domain_aliasses`
		WHERE
			`domain_id` = ?
		ORDER BY
			`alias_id`
		;
	";

	$rs = exec_query($sql, $query, $domain_id);

	$als_ftp_acc_cnt = 0;

	while (!$rs->EOF) {
		$als_name = $rs->fields['alias_name'];

		$query = "
			SELECT
				COUNT(*) AS cnt
			FROM
				`ftp_users`
			WHERE
				`userid` LIKE ?
			;
		";

		$rs_cnt = exec_query($sql, $query, '%' . $ftp_separator . $als_name);

		$als_ftp_acc_cnt += $rs_cnt->fields['cnt'];

		$rs->moveNext();
	}

	return $als_ftp_acc_cnt;
}

function get_domain_running_ftp_acc_cnt($sql, $domain_id) {

	$dmn_ftp_acc_cnt = get_domain_running_dmn_ftp_acc_cnt($sql, $domain_id);
	$sub_ftp_acc_cnt = get_domain_running_sub_ftp_acc_cnt($sql, $domain_id);
	$als_ftp_acc_cnt = get_domain_running_als_ftp_acc_cnt($sql, $domain_id);

	return array(
		$dmn_ftp_acc_cnt + $sub_ftp_acc_cnt + $als_ftp_acc_cnt,
		$dmn_ftp_acc_cnt,
		$sub_ftp_acc_cnt,
		$als_ftp_acc_cnt
	);
}

function get_domain_running_sqld_acc_cnt($sql, $domain_id) {

	$query = "
		SELECT
			COUNT(*) AS cnt
		FROM
			`sql_database`
		WHERE
			`domain_id` = ?
		;
	";

	$rs = exec_query($sql, $query, $domain_id);

	$sqld_acc_cnt = $rs->fields['cnt'];

	return $sqld_acc_cnt;
}

function get_domain_running_sqlu_acc_cnt($sql, $domain_id) {

	$query = "
		SELECT DISTINCT
			t1.`sqlu_name`
		FROM
			`sql_user` AS t1, `sql_database` AS t2
		WHERE
			t2.`domain_id` = ?
		AND
			t2.`sqld_id` = t1.`sqld_id`
		;
	";

	$rs = exec_query($sql, $query, $domain_id);

	$sqlu_acc_cnt = $rs->recordCount();

	return $sqlu_acc_cnt;
}

function get_domain_running_sql_acc_cnt($sql, $domain_id) {

	$sqld_acc_cnt = get_domain_running_sqld_acc_cnt($sql, $domain_id);
	$sqlu_acc_cnt = get_domain_running_sqlu_acc_cnt($sql, $domain_id);

	return array($sqld_acc_cnt, $sqlu_acc_cnt);
}

function get_domain_running_props_cnt($domain_id) {

	$sql = EasySCP_Registry::get('Db');

	$sub_cnt = get_domain_running_sub_cnt($sql, $domain_id);
	$als_cnt = get_domain_running_als_cnt($sql, $domain_id);

	list($mail_acc_cnt) = get_domain_running_mail_acc_cnt($sql, $domain_id);
	list($ftp_acc_cnt) = get_domain_running_ftp_acc_cnt($sql, $domain_id);
	list($sqld_acc_cnt, $sqlu_acc_cnt) = get_domain_running_sql_acc_cnt($sql, $domain_id);

	return array($sub_cnt, $als_cnt, $mail_acc_cnt, $ftp_acc_cnt, $sqld_acc_cnt, $sqlu_acc_cnt);
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param string $menu_file
 */
function gen_client_mainmenu($tpl, $menu_file) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$tpl->assign(
		array(
			'TR_MENU_GENERAL_INFORMATION'	=> tr('General information'),
			'TR_MENU_MANAGE_DOMAINS'		=> tr('Manage domains'),
			'TR_MENU_EMAIL_ACCOUNTS'		=> tr('Email Accounts'),
			'TR_MENU_FTP_ACCOUNTS'			=> tr('FTP Accounts'),
			'TR_MENU_MANAGE_SQL'			=> tr('Manage SQL'),
			'TR_MENU_WEBTOOLS'				=> tr('Webtools'),
			'TR_MENU_DOMAIN_STATISTICS'		=> tr('Domain statistics'),
			'TR_MENU_SUPPORT_SYSTEM'		=> tr('Support system')
		)
	);

	$dmn_props = get_domain_default_props($_SESSION['user_id']);

	if ($dmn_props['domain_mailacc_limit'] != -1){
		$tpl->assign('ISACTIVE_EMAIL', true);
	}

	if ($dmn_props['domain_alias_limit'] != -1 || $dmn_props['domain_subd_limit'] != -1 || $dmn_props['domain_dns'] == 'yes'){
		$tpl->assign('ISACTIVE_DOMAIN', true);
	}

	if ($dmn_props['domain_ftpacc_limit'] != -1){
		$tpl->assign('ISACTIVE_FTP', true);
	}

	if ($dmn_props['domain_sqld_limit'] != -1){
		$tpl->assign('ISACTIVE_SQL', true);
	}

	if ($dmn_props['domain_ssl'] == 'yes'){
		$tpl->assign('ISACTIVE_SSL',true);
	}
        
	$query = "
		SELECT
			`support_system`
		FROM
			`reseller_props`
		WHERE
			`reseller_id` = ?
		;
	";

	$rs = exec_query($sql, $query, $_SESSION['user_created_by']);

	if ($rs->fields['support_system'] == 'yes') {
		$tpl->assign('ISACTIVE_SUPPORT', true);
	}

	$tpl->assign('MAIN_MENU', $menu_file);
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param string $menu_file
 * @return void
 */
function gen_client_menu($tpl, $menu_file) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$tpl->assign(
		array(
			'TR_MENU_OVERVIEW'				=> tr('Overview'),
			'TR_MENU_CHANGE_PASSWORD'		=> tr('Change password'),
			'TR_MENU_CHANGE_PERSONAL_DATA'	=> tr('Change personal data'),
			'TR_MENU_LANGUAGE'				=> tr('Language'),
			'TR_MENU_UPDATE_HP'				=> tr('Update Hosting Package'),

			'TR_MENU_ADD_SUBDOMAIN'			=> tr('Add subdomain'),
			'TR_MENU_ADD_ALIAS'				=> tr('Add alias'),
			'TR_MENU_MANAGE_DNS'			=> tr('Manage DNS'),
			'TR_MENU_MANAGE_SSL'			=> tr('Manage SSL certificate'),
			'TR_MENU_PHP_EDITOR'			=> tr('PHP editor'),

			'TR_MENU_ADD_MAIL_USER'			=> tr('Add mail user'),
			'TR_MENU_CATCH_ALL_MAIL'		=> tr('Catch all'),
			'WEBMAIL_PATH'					=> $cfg->WEBMAIL_PATH,
			'TR_WEBMAIL'					=> tr('Webmail'),

			'TR_MENU_ADD_FTP_USER'			=> tr('Add FTP user'),
			'FILEMANAGER_PATH'				=> $cfg->FILEMANAGER_PATH,
			'TR_FILEMANAGER'				=> tr('Filemanager'),

			'TR_MENU_ADD_SQL_DATABASE'		=> tr('Add SQL database'),
			'PMA_PATH'						=> $cfg->PMA_PATH,
			'TR_PHPMYADMIN'					=> tr('phpMyAdmin'),

			'TR_HTACCESS'					=> tr('Protected areas'),
			'TR_HTACCESS_USER'				=> tr('Group/User management'),
			'TR_MENU_ERROR_PAGES'			=> tr('Error pages'),
			'TR_MENU_DAILY_BACKUP'			=> tr('Daily backup'),

			'TR_AWSTATS'					=> tr('AwStats'),

			'TR_OPEN_TICKETS'				=> tr('Open tickets'),
			'TR_CLOSED_TICKETS'				=> tr('Closed tickets'),
			'TR_MENU_NEW_TICKET'			=> tr('New ticket'),

			'TR_MENU_LOGOUT'				=> tr('Logout'),
			'VERSION'						=> $cfg->Version,
			'BUILDDATE'						=> $cfg->BuildDate
		)
	);

	$query = "
		SELECT
			`support_system`
		FROM
			`reseller_props`
		WHERE
			`reseller_id` = ?
		;
	";

	$rs = exec_query($sql, $query, $_SESSION['user_created_by']);

	if ($rs->fields['support_system'] == 'yes') {
		$tpl->assign('SUPPORT_SYSTEM', true);
	}

	$dmn_props = get_domain_default_props($_SESSION['user_id']);

	if ($dmn_props['domain_alias_limit'] != -1 || $dmn_props['domain_subd_limit'] != -1 || $dmn_props['domain_dns'] == 'yes'){
		$tpl->assign('ISACTIVE_DOMAIN', true);
	}

	if ($dmn_props['domain_ftpacc_limit'] != -1){
		$tpl->assign('ISACTIVE_FTP', true);
	}

	if ($dmn_props['domain_sqld_limit'] != -1){
		$tpl->assign('ISACTIVE_SQL', true);
	}

	if ($dmn_props['domain_mailacc_limit'] != -1){
		$tpl->assign('ISACTIVE_EMAIL', true);
	}

	if ($dmn_props['domain_subd_limit'] != -1){
		$sub_cnt = get_domain_running_sub_cnt($sql, $dmn_props['domain_id']);
		if ($dmn_props['domain_subd_limit'] == 0 || $sub_cnt < $dmn_props['domain_subd_limit']) {
			$tpl->assign('ISACTIVE_SUBDOMAIN_MENU', true);
		}

	}

	if ($dmn_props['domain_alias_limit'] != -1){
		$als_cnt = get_domain_running_als_cnt($sql, $dmn_props['domain_id']);
		if ($dmn_props['domain_alias_limit'] == 0 || $als_cnt < $dmn_props['domain_alias_limit']) {
			$tpl->assign('ISACTIVE_ALIAS_MENU', true);
		}
	}

	if ($dmn_props['allowbackup'] == 'yes'){
		$tpl->assign('ISACTIVE_BACKUP', true);
	}

	if ($dmn_props['domain_dns'] == 'yes'){
		$tpl->assign('ISACTIVE_DNS_MENU', true);
	}

	if ($dmn_props['domain_ssl'] == 'yes'){
		$tpl->assign('ISACTIVE_SSL_MENU', true);
	}

	if ($dmn_props['domain_php_edit'] == 'yes'){
		$tpl->assign('ISACTIVE_PHP_EDITOR', true);
	}

	if ($cfg->AWSTATS_ACTIVE == 'yes') {
		$tpl->assign(
			array(
				'AWSTATS_PATH'	=> 'http://' . $_SESSION['user_logged'] . '/stats/',
				'TR_AWSTATS'	=> tr('Web statistics')
			)
		);
	}

	// Hide 'Update Hosting Package'-Button, if there are none
	$query = "
		SELECT
			`id`
		FROM
			`hosting_plans`
		WHERE
			`reseller_id` = ?
		AND
			`status` = '1'
		;
	";

	$rs = exec_query($sql, $query, $_SESSION['user_created_by']);

	if ($rs->recordCount() != 0) {
		if ($cfg->HOSTING_PLANS_LEVEL != 'admin') {
			$tpl->assign('ISACTIVE_UPDATE_HP', true);
		}
	}

	$tpl->assign('MENU', $menu_file);
}

function get_user_domain_id($user_id) {

	$sql_param = array(
		':domain_admin_id' => $user_id
	);

	$sql_query = "
		SELECT
			domain_id
		FROM
			domain
		WHERE
			domain_admin_id = :domain_admin_id
		;
	";

	DB::prepare($sql_query);
	$row = DB::execute($sql_param, true);

	return $row['domain_id'];
}

function user_trans_mail_type($mail_type) {

	if ($mail_type === MT_NORMAL_MAIL) {
		return tr('Domain mail');
	} else if ($mail_type === MT_NORMAL_FORWARD) {
		return tr('Email forward');
	} else if ($mail_type === MT_ALIAS_MAIL) {
		return tr('Alias mail');
	} else if ($mail_type === MT_ALIAS_FORWARD) {
		return tr('Alias forward');
	} else if ($mail_type === MT_SUBDOM_MAIL) {
		return tr('Subdomain mail');
	} else if ($mail_type === MT_SUBDOM_FORWARD) {
		return tr('Subdomain forward');
	} else if ($mail_type === MT_ALSSUB_MAIL) {
		return tr('Alias subdomain mail');
	} else if ($mail_type === MT_ALSSUB_FORWARD) {
		return tr('Alias subdomain forward');
	} else if ($mail_type === MT_NORMAL_CATCHALL) {
		return tr('Domain mail');
	} else if ($mail_type === MT_ALIAS_CATCHALL) {
		return tr('Domain mail');
	} else {
		return tr('Unknown type');
	}
}

/**
 * Trigger a header Redirect to the specified location
 *
 * @param String $dest destination for header redirect (path + filename + params)
 */
function user_goto($dest) {
	header('Location: ' . $dest);
	exit(
		tr(
			'Redirect was not working, please follow %s',
			'<a href="' . $dest . '">' . tr('this link') . '</a>'
		)
	);
}

function count_sql_user_by_name($sql, $sqlu_name) {

	$query = "
		SELECT
			COUNT(*) AS cnt
		FROM
			`sql_user`
		WHERE
			`sqlu_name` = ?
		;
	";

	$rs = exec_query($sql, $query, $sqlu_name);

	return $rs->fields['cnt'];
}

/**
 * @todo see dirty hack
 */
function sql_delete_user($sql, $dmn_id, $db_user_id) {

	// let's get sql user common data;
	$query = "
		SELECT
			t1.`sqld_id`, t1.`sqlu_name`, t2.`sqld_name`, t1.`sqlu_name`
		FROM
			`sql_user` AS t1,
			`sql_database` AS t2
		WHERE
			t1.`sqld_id` = t2.`sqld_id`
		AND
			t2.`domain_id` = ?
		AND
			t1.`sqlu_id` = ?
		;
	";

	$rs = exec_query($sql, $query, array($dmn_id, $db_user_id));

	if ($rs->recordCount() == 0) {
		// dirty hack admin can't delete users without database
		if ($_SESSION['user_type'] === 'admin'
			|| $_SESSION['user_type'] === 'reseller') {
			return;
		}
		user_goto('sql_manage.php');
	}

	// remove from EasySCP sql_user table.
	$query = 'DELETE FROM `sql_user` WHERE `sqlu_id` = ?';
	exec_query($sql, $query, $db_user_id);

	update_reseller_c_props(get_reseller_id($dmn_id));

	$db_name = quoteIdentifier(
			preg_replace("/([_%\?\*])/", '\\\$1', $rs->fields['sqld_name'])
		);
	$db_user_name = $rs->fields['sqlu_name'];

	if (count_sql_user_by_name($sql, $rs->fields['sqlu_name']) == 0) {

		// revoke grants on global level, if any;
		$query = "REVOKE ALL ON *.* FROM ?@?;";
		exec_query($sql, $query, array($db_user_name, '%'));
		exec_query($sql, $query, array($db_user_name, 'localhost'));

		// delete user record from mysql.user table;
		$query = "DROP USER ?@?;";
		exec_query($sql, $query, array($db_user_name, '%'));
		exec_query($sql, $query, array($db_user_name, 'localhost'));

		// flush privileges.
		$query = "FLUSH PRIVILEGES;";
		exec_query($sql, $query);
	} else {
		$query = "REVOKE ALL ON $db_name.* FROM ?@?;";
		exec_query($sql, $query, array($db_user_name, '%'));
		exec_query($sql, $query, array($db_user_name, 'localhost'));
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @return void
 */
function check_permissions($tpl) {

	if (isset($_SESSION['sql_support']) && $_SESSION['sql_support'] == "no") {
		$tpl->assign('SQL_SUPPORT', '');
	}
	if (isset($_SESSION['email_support']) && $_SESSION['email_support'] == "no") {
		$tpl->assign('ADD_EMAIL', '');
	}
	if (isset($_SESSION['subdomain_support']) && $_SESSION['subdomain_support'] == "no") {
		$tpl->assign('SUBDOMAIN_SUPPORT', '');
	}
	if (isset($_SESSION['alias_support']) && $_SESSION['alias_support'] == "no") {
		$tpl->assign('DOMAINALIAS_SUPPORT', '');
	}
	if (isset($_SESSION['subdomain_support']) && $_SESSION['subdomain_support'] == "no") {
		$tpl->assign('SUBDOMAIN_SUPPORT_CONTENT', '');
	}
	if (isset($_SESSION['alias_support']) && $_SESSION['alias_support'] == "no") {
		$tpl->assign('DOMAINALIAS_SUPPORT_CONTENT', '');
	}
	if (isset($_SESSION['alias_support']) && $_SESSION['alias_support'] == "no"
		&& isset($_SESSION['subdomain_support']) && $_SESSION['subdomain_support'] == "no") {
		$tpl->assign('DMN_MNGMNT', '');
	}
	if (isset($_SESSION['ssl_support']) && $_SESSION['sl_support'] == "no") {
		$tpl->assign('SSL_SUPPORT', '');
	}        
}

function check_usr_sql_perms($sql, $db_user_id) {

	if (who_owns_this($db_user_id, 'sqlu_id') != $_SESSION['user_id']) {
		set_page_message(
			tr('User does not exist or you do not have permission to access this interface!'),
			'warning'
		);
		user_goto('sql_manage.php');
	}
}

function check_db_sql_perms($sql, $db_id) {

	if (who_owns_this($db_id, 'sqld_id') != $_SESSION['user_id']) {
		set_page_message(
			tr('User does not exist or you do not have permission to access this interface!'),
			'warning'
		);
		user_goto('sql_manage.php');
	}
}

function check_ftp_perms($sql, $ftp_acc) {

	if (who_owns_this($ftp_acc, 'ftp_user') != $_SESSION['user_id']) {
		set_page_message(
			tr('User does not exist or you do not have permission to access this interface!'),
			'warning'
		);
		user_goto('ftp_accounts.php');
	}
}

function delete_sql_database($sql, $dmn_id, $db_id) {

	$query = "
		SELECT
			`sqld_name` AS db_name
		FROM
			`sql_database`
		WHERE
			`domain_id` = ?
		AND
			`sqld_id` = ?
		;
	";

	$rs = exec_query($sql, $query, array($dmn_id, $db_id));

	if ($rs->recordCount() == 0) {
		if ($_SESSION['user_type'] === 'admin'
			|| $_SESSION['user_type'] === 'reseller') {
			return;
		}
		user_goto('sql_manage.php');
	}

	$db_name = quoteIdentifier($rs->fields['db_name']);

	// have we any users assigned to this database;
	$query = "
		SELECT
			t2.`sqlu_id` AS db_user_id,
			t2.`sqlu_name` AS db_user_name
		FROM
			`sql_database` AS t1,
			`sql_user` AS t2
		WHERE
			t1.`sqld_id` = t2.`sqld_id`
		AND
			t1.`domain_id` = ?
		AND
			t1.`sqld_id` = ?
		;
	";

	$rs = exec_query($sql, $query, array($dmn_id, $db_id));

	if ($rs->recordCount() != 0) {
		while (!$rs->EOF) {
			$db_user_id = $rs->fields['db_user_id'];

			sql_delete_user($sql, $dmn_id, $db_user_id);

			$rs->moveNext();
		}
	}

	// drop desired database;
	$query = "DROP DATABASE IF EXISTS $db_name;";
	exec_query($sql, $query);

	write_log($_SESSION['user_logged'] . ": delete SQL database: " . tohtml($db_name));
	// delete desired database from the EasySCP sql_database table;

	$query = "
		DELETE FROM
			`sql_database`
		WHERE
			`domain_id` = ?
		AND
			`sqld_id` = ?
		;
	";

	exec_query($sql, $query, array($dmn_id, $db_id));

	update_reseller_c_props(get_reseller_id($dmn_id));
}

function get_gender_by_code($code, $nullOnBad = false) {

	switch (strtolower($code)) {
		case 'm':
		case 'M':
			return tr('Male');
		case 'f':
		case 'F':
			return tr('Female');
		default:
			return (!$nullOnBad) ? tr('Unknown') : null;
	}
}

function mount_point_exists($dmn_id, $mnt_point) {

	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			t1.`domain_id`, t2.`alias_mount`, t3.`subdomain_mount`, t4.`subdomain_alias_mount`
		FROM
			`domain` AS t1
		LEFT JOIN
			(`domain_aliasses` AS t2)
		ON
			(t1.`domain_id` = t2.`domain_id`)
		LEFT JOIN
			(`subdomain` AS t3)
		ON
			(t1.`domain_id` = t3.`domain_id`)
		LEFT JOIN
			(`subdomain_alias` AS t4)
		ON
			(t2.`alias_id` = t4.`alias_id`)
		WHERE
			t1.`domain_id` = ?
		AND
			(
				`alias_mount` = ?
			OR
				`subdomain_mount` = ?
			OR
				`subdomain_alias_mount` = ?
			)
	";

	$rs = exec_query($sql, $query, array($dmn_id, $mnt_point, $mnt_point, $mnt_point));

	if ($rs->rowCount() > 0) {
		return true;
	}
	return false;
}

function get_user_domain_ip($dmn_ip_id) {

	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			ip_number, ip_number_v6
		FROM
			server_ips
		WHERE
			ip_id = ?
		;
	";

	$rs = exec_query($sql, $query, $dmn_ip_id);

	return ($rs->fields['ip_number_v6'] != '') ? $rs->fields['ip_number'] .' / '. $rs->fields['ip_number_v6'] : $rs->fields['ip_number'];
}

function get_user_domains($user_id) {
		
	$sql_query = "
				SELECT
					`domain_id`,
					`domain_name`
				FROM
					`domain`
				WHERE
					`domain_admin_id` = :user_id
	";
	
	$sql_param = array(
		'user_id' => $user_id,
	);
	$statement = DB::prepare($sql_query);
	$stmt = DB::execute($sql_param, false);
	
	$domains = array();
		
	while ($row = $stmt->fetch()) {
		$domains[] = array(
			'alias' => 0,
			'domain_id' => $row['domain_id'],
			'domain_name' => $row['domain_name'],
		);
	}
	
	$sql_query = "
			SELECT
				`alias_id`,
				`alias_name`
			FROM
				`domain_aliasses` `da`
			INNER JOIN
				`domain` `d`
			ON
				(`d`.`domain_id`=`da`.`domain_id`)
			WHERE
				`domain_admin_id` = :user_id
	";
	
	$statement = DB::prepare($sql_query);
	$stmt = DB::execute($sql_param, false);
		
	while ($row = $stmt->fetch()) {
		$domains[] = array(
			'alias' => 1,
			'domain_id' => $row['alias_id'],
			'domain_name' => $row['alias_name'],
		);
	}
	
	return $domains;
}

/**
 * @param int $alias
 * @param int $domain_id
 * @return array $dns_records
 */
function get_dns_zone($alias=0, $domain_id) {

	$cfg = EasySCP_Registry::get('Config');
			
	$sql_param = array(
		"domain_id"	=>	$domain_id,
	);
	if ($alias==0) {
		$sql_query = "
			SELECT
				`ns`.`name` AS `domain_name`,
				`ns`.`id`,
				`r`.`id` AS `domain_dns_id`,
				`r`.`protected`,
				`r`.`name` AS `domain_dns`,
				`r`.`content` AS `domain_text`,
				`r`.`type` AS `domain_type`,
				`r`.`prio`,
				`d`.`status`,
				`ns`.`easyscp_domain_id`
			FROM
				`powerdns`.`domains` `ns`
			INNER JOIN
				`powerdns`.`records` `r`
			ON
				(`r`.`domain_id`=`ns`.`id`)
			INNER JOIN
				`domain` `d`
			ON
				(`d`.`domain_id`=`ns`.`easyscp_domain_id`)
			WHERE
				`ns`.`easyscp_domain_id` = :domain_id
			ORDER BY
				`ns`.`easyscp_domain_id`,
				`r`.`name`,
				`r`.`type`
		";	
	}
	else if ($alias==1)
	{
		$sql_query = "
			SELECT
				`ns`.`name` AS `domain_name`,
				`ns`.`id`,
				`r`.`id` AS `domain_dns_id`,
				`r`.`protected`,
				`r`.`name` AS `domain_dns`,
				`r`.`content` AS `domain_text`,
				`r`.`type` AS `domain_type`,
				`r`.`prio`,
				`da`.`status`,
				`ns`.`easyscp_domain_alias_id`
			FROM
				`powerdns`.`domains` `ns`
			INNER JOIN
				`powerdns`.`records` `r`
			ON
				(`r`.`domain_id`=`ns`.`id`)
			INNERJOIN
				`domain_aliasses` `da`
			ON
				(`da`.`alias_id`=`ns`.`easyscp_domain_alias_id`)
			WHERE
				`ns`.`easyscp_domain_alias_id` = :domain_id
			ORDER BY
				`ns`.`easyscp_domain_alias_id`,
				`r`.`name`,
				`r`.`type`
		";		
	}

	
	$statement = DB::prepare($sql_query);
	

	$dns_records = array();
	$stmt = DB::execute($sql_param, false);
		
	while ($row = $stmt->fetch()) {
			
		list($dns_action_delete, $dns_action_script_delete) = gen_user_dns_action(
			'Delete', $row['domain_dns_id'],
			($row['protected'] == 0) ? $row['status'] : $cfg->ITEM_PROTECTED_STATUS
		);
			list($dns_action_edit, $dns_action_script_edit) = gen_user_dns_action(
			'Edit', $row['domain_dns_id'],
			($row['protected'] == 0) ? $row['status'] :$cfg->ITEM_PROTECTED_STATUS
		);
	
		$domain_name = decode_idna($row['domain_name']);
		$sbd_name = $row['domain_dns'];
		if ($row['domain_type']=="MX") {
			$sbd_data = $row['prio']." ".$row['domain_text'];
		}
		else {
			$sbd_data = $row['domain_text'];	
		}
		
		$dns_records[] =
			array(
				'DNS_DOMAIN'				=> tohtml($domain_name),
				'DNS_NAME'					=> tohtml($sbd_name),
				'DNS_TYPE'					=> tohtml($row['domain_type']),
				'DNS_DATA'					=> tohtml($sbd_data),
				'DNS_ACTION_SCRIPT_DELETE'	=> tohtml($dns_action_script_delete),
				'DNS_ACTION_DELETE'			=> tohtml($dns_action_delete),
				'DNS_ACTION_SCRIPT_EDIT'	=> tohtml($dns_action_script_edit),
				'DNS_ACTION_EDIT'			=> tohtml($dns_action_edit),
				'DNS_TYPE_RECORD'			=> tr("%s record", $row['domain_type'])
			);
	}
	
	return $dns_records;
}

/**
 * @param String $action
 * @param int $dns_id
 * @param String $statis
 * @return array
 */
function gen_user_dns_action($action, $dns_id, $status) {

	$cfg = EasySCP_Registry::get('Config');

	if ($status == $cfg->ITEM_OK_STATUS) {
		return array(tr($action), 'dns_'.strtolower($action).'.php?edit_id='.$dns_id);
	} elseif($action != 'Edit' && $status == $cfg->ITEM_PROTECTED_STATUS) {
		return array(tr('N/A'), 'protected');
	}

	return array(tr('N/A'), '#');
}

function check_dns_record_owned($user_id, $record_id) {
	
	$sql_query = "
		SELECT 
			`d`.`easyscp_domain_alias_id`,
			`d`.`easyscp_domain_id`
		FROM
			`powerdns`.`domains` `d`
		INNER JOIN
			`powerdns`.`records` `r`
		ON
			(`r`.`domain_id`=`d`.`id`)
		WHERE
			`r`.`id` = :record_id
	";
	
	$sql_param = array(
		'record_id'	=> $record_id,
	);
	
	DB::prepare($sql_query);
	$row = DB::execute($sql_param, true);
	
	if ($row['easyscp_domain_alias_id'] > 0) {
		$sql_query = "
			SELECT
				COUNT(*) AS `total`
			FROM
				`domain_aliasses` `da`
			INNER JOIN
				`domain` `d`
			ON
				(`d`.`domain_id`=`da`.`domain_id`)
			WHERE
				`da`.`alias_id` = :alias_id
			AND
				`d`.`domain_admin_id` = :domain_admin_id
		";
		
		$sql_param = array(
			'alias_id'	=> $row['easyscp_domain_alias_id'],
			'domain_admin_id' => $user_id,
		);
		
		DB::prepare($sql_query);
		$row = DB::execute($sql_param, true);
		
		if ($row['total'] > 0) {
			return true;
		}
	}
	else {
		$sql_query = "
			SELECT
				COUNT(*) AS `total`
			FROM
				`domain` `d`
			WHERE
				`d`.`domain_id` = :domain_id
			AND
				`d`.`domain_admin_id` = :domain_admin_id
		";
		
		$sql_param = array(
			'domain_id'	=> $row['easyscp_domain_id'],
			'domain_admin_id' => $user_id,
		);
		
		DB::prepare($sql_query);
		$row = DB::execute($sql_param, true);
		
		if ($row['total'] > 0) {
			return true;
		}
	}
	
	return false;
}
?>