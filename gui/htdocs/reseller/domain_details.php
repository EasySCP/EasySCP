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
$template = 'reseller/domain_details.tpl';

if (isset($cfg->HOSTING_PLANS_LEVEL) && $cfg->HOSTING_PLANS_LEVEL === 'admin') {
	$tpl->assign('EDIT_OPTION', '');
}

// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Domain/Details'),
		'TR_DOMAIN_DETAILS'		=> tr('Domain details'),
		'TR_DOMAIN_NAME'		=> tr('Domain name'),
		'TR_DOMAIN_IP'			=> tr('Domain IP'),
		'TR_STATUS'				=> tr('Status'),
		'TR_PHP_SUPP'			=> tr('PHP support'),
		'TR_CGI_SUPP'			=> tr('CGI support'),
		'TR_BACKUP_SUPPORT'		=> tr('Backup support'),
		'TR_DNS_SUPP'			=> tr('Manual DNS support'),
		'TR_MYSQL_SUPP'			=> tr('MySQL support'),
		'TR_TRAFFIC'			=> tr('Traffic in MB'),
		'TR_DISK'				=> tr('Disk in MB'),
		'TR_FEATURE'			=> tr('Feature'),
		'TR_USED'				=> tr('Used'),
		'TR_LIMIT'				=> tr('Limit'),
		'TR_MAIL_ACCOUNTS'		=> tr('Mail accounts'),
		'TR_FTP_ACCOUNTS'		=> tr('FTP accounts'),
		'TR_SQL_DB_ACCOUNTS'	=> tr('SQL databases'),
		'TR_SQL_USER_ACCOUNTS'	=> tr('SQL users'),
		'TR_SUBDOM_ACCOUNTS'	=> tr('Subdomains'),
		'TR_DOMALIAS_ACCOUNTS'	=> tr('Domain aliases'),
		'TR_UPDATE_DATA'		=> tr('Submit changes'),
		'TR_BACK'				=> tr('Back'),
		'TR_EDIT'				=> tr('Edit')
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_users_manage.tpl');
gen_reseller_menu($tpl, 'reseller/menu_users_manage.tpl');

gen_page_message($tpl);
// Get user id that comes for manage domain
if (!isset($_GET['domain_id'])) {
	user_goto('users.php?psi=last');
}

$editid = $_GET['domain_id'];
gen_detaildom_page($tpl, $_SESSION['user_id'], $editid);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// Begin function block

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $user_id
 * @param int $domain_id
 */
function gen_detaildom_page($tpl, $user_id, $domain_id) {
	$sql = EasySCP_Registry::get('Db');

	// Get domain data
	$query = "
		SELECT
			*,
			IFNULL(`domain_disk_usage`, 0) AS domain_disk_usage
		FROM
			`domain`
		WHERE
			`domain_id` = ?
	";

	$res = exec_query($sql, $query, $domain_id);

	$data = $res->fetchRow();

	if ($res->recordCount() <= 0) {
		user_goto('users.php?psi=last');
	}
	// Get admin data
	$created_by = $_SESSION['user_id'];
	$query = "SELECT `admin_name` FROM `admin` WHERE `admin_id` = ? AND `created_by` = ?";
	$res1 = exec_query($sql, $query, array($data['domain_admin_id'], $created_by));

	// NXW: Unused variable so...
	// $data1 = $res1->fetchRow();
	$res1->fetchRow();
	if ($res1->recordCount() <= 0) {
		user_goto('users.php?psi=last');
	}
	// Get IP info
	$query = "SELECT * FROM `server_ips` WHERE `ip_id` = ?";
	$ipres = exec_query($sql, $query, $data['domain_ip_id']);
	$ipres->fetchRow();
	// Get staus name
	$dstatus = translate_dmn_status($data['status']);

	// Traffic diagram
	$fdofmnth = mktime(0, 0, 0, date("m"), 1, date("Y"));
	$ldofmnth = mktime(1, 0, 0, date("m") + 1, 0, date("Y"));
	$query = "SELECT
			IFNULL(SUM(`dtraff_web_in`), 0) AS dtraff_web_in,
			IFNULL(SUM(`dtraff_web_out`), 0) AS dtraff_web_out,
			IFNULL(SUM(`dtraff_ftp_in`), 0) AS dtraff_ftp_in,
			IFNULL(SUM(`dtraff_ftp_out`), 0) AS dtraff_ftp_out,
			IFNULL(SUM(`dtraff_mail`), 0) AS dtraff_mail,
			IFNULL(SUM(`dtraff_pop`),0) AS dtraff_pop
		FROM
			`domain_traffic`
		WHERE
			`domain_id` = ?
		AND
			`dtraff_time` > ?
		AND
			`dtraff_time` < ?
	";
	$res7 = exec_query($sql, $query, array($data['domain_id'], $fdofmnth, $ldofmnth));
	$dtraff = $res7->fetchRow();

	$sumtraff = $dtraff['dtraff_web_in'] + $dtraff['dtraff_web_out'] + $dtraff['dtraff_ftp_in'] + $dtraff['dtraff_ftp_out'] + $dtraff['dtraff_mail'] + $dtraff['dtraff_pop'];

	// NXW: Unused variables so ...
	/*
	$dtraffmb = sprintf("%.1f", ($sumtraff / 1024) / 1024);
	$month = date("m");
	$year = date("Y");
	*/

	$query = "SELECT * FROM `server_ips` WHERE `ip_id` = ?";
	$res8 = exec_query($sql, $query, $data['domain_ip_id']);
	$ipdat = $res8->fetchRow();

	$domain_traffic_limit = $data['domain_traffic_limit'];
	$domain_all_traffic = $sumtraff;

	$traffic_percent = ($domain_traffic_limit != 0) ? sprintf("%.2f", 100 * $domain_all_traffic / ($domain_traffic_limit * 1024 * 1024)) : 0;

	// Get disk status
	$domdu = $data['domain_disk_usage'];
	$domdl = $data['domain_disk_limit'];

	$domduh = sizeit($domdu);

	$disk_percent = ($domdl != 0) ? sprintf("%.2f", 100 * $domdu / ($domdl * 1024 * 1024)) : 0;
		
	// Get current mail count
	$query = "SELECT COUNT(`mail_id`) AS mcnt "
			. "FROM `mail_users` "
			. "WHERE `domain_id` = ? "
			. "AND `mail_type` NOT RLIKE '_catchall'";
	$res6 = exec_query($sql, $query, $data['domain_id']);

	$dat3 = $res6->fetchRow();
	$mail_limit = translate_limit_value($data['domain_mailacc_limit']);
	// FTP stat
	$query = "SELECT `gid` FROM `ftp_group` WHERE `groupname` = ?";
	$res4 = exec_query($sql, $query, $data['domain_name']);
	$ftp_gnum = $res4->rowCount();
	if ($ftp_gnum == 0) {
		$used_ftp_acc = 0;
	} else {
		$dat1 = $res4->fetchRow();
		$query = "SELECT COUNT(*) AS ftp_cnt FROM `ftp_users` WHERE `gid` = ?";
		$res5 = exec_query($sql, $query, $dat1['gid']);
		$dat2 = $res5->fetchRow();

		$used_ftp_acc = $dat2['ftp_cnt'];
	}
	$ftp_limit = translate_limit_value($data['domain_ftpacc_limit']);
	// Get sql database count
	$query = "SELECT COUNT(*) AS dnum FROM `sql_database` WHERE `domain_id` = ?";
	$res = exec_query($sql, $query, $data['domain_id']);
	$dat5 = $res->fetchRow();
	$sql_db = translate_limit_value($data['domain_sqld_limit']);
	// Get sql users count
	$query = "SELECT COUNT(u.`sqlu_id`) AS ucnt FROM sql_user u, sql_database d WHERE u.`sqld_id` = d.`sqld_id` AND d.`domain_id` = ?";
	$res = exec_query($sql, $query, $data['domain_id']);
	$dat6 = $res->fetchRow();
	$sql_users = translate_limit_value($data['domain_sqlu_limit']);
	// Get subdomain
	$query = "SELECT COUNT(`subdomain_id`) AS sub_num FROM `subdomain` WHERE `domain_id` = ?";
	$res1 = exec_query($sql, $query, $domain_id);
	$sub_num_data = $res1->fetchRow();
	$query = "SELECT COUNT(`subdomain_alias_id`) AS sub_num FROM `subdomain_alias` WHERE `alias_id` IN (SELECT `alias_id` FROM `domain_aliasses` WHERE `domain_id` = ?)";
	$res1 = exec_query($sql, $query, $domain_id);
	$alssub_num_data = $res1->fetchRow();
	$sub_dom = translate_limit_value($data['domain_subd_limit']);
	// Get domain aliases
	$query = "SELECT COUNT(*) AS alias_num FROM `domain_aliasses` WHERE `domain_id` = ?";
	$res1 = exec_query($sql, $query, $domain_id);
	$alias_num_data = $res1->fetchRow();

	// Check if Backup support is available for this user
	switch($data['allowbackup']){
    case "full":
        $tpl->assign( array('VL_BACKUP_SUPPORT' => tr('Full')));
        break;
    case "sql":
        $tpl->assign( array('VL_BACKUP_SUPPORT' => tr('SQL')));
        break;
    case "dmn":
        $tpl->assign( array('VL_BACKUP_SUPPORT' => tr('Domain')));
        break;
    default:
        $tpl->assign( array('VL_BACKUP_SUPPORT' => tr('No')));
    }

	$dom_alias = translate_limit_value($data['domain_alias_limit']);
	// Fill in the fields
	$tpl->assign(
		array(
			'DOMAIN_ID'					=> $data['domain_id'],
			'VL_DOMAIN_NAME'			=> tohtml(decode_idna($data['domain_name'])),
			'VL_DOMAIN_IP'				=> tohtml($ipdat['ip_number'] . ' (' . $ipdat['ip_alias'] . ')'),
			'VL_STATUS'					=> $dstatus,
			'VL_PHP_SUPP'				=> ($data['domain_php'] == 'yes') ? tr('Enabled') : tr('Disabled'),
			'VL_CGI_SUPP'				=> ($data['domain_cgi'] == 'yes') ? tr('Enabled') : tr('Disabled'),
			'VL_DNS_SUPP'				=> ($data['domain_dns'] == 'yes') ? tr('Enabled') : tr('Disabled'),
			'VL_MYSQL_SUPP'				=> ($data['domain_sqld_limit'] >= 0) ? tr('Enabled') : tr('Disabled'),
			'VL_TRAFFIC_PERCENT'		=> $traffic_percent,
			'VL_TRAFFIC_USED'			=> sizeit($domain_all_traffic),
			'VL_TRAFFIC_LIMIT'			=> ($data['domain_traffic_limit'] > 0) ? tr(sizeit($domain_traffic_limit, 'MB')) : tr('unlimited MB'),
			'VL_DISK_PERCENT'			=> $disk_percent,
			'VL_DISK_USED'				=> $domduh,
			'VL_DISK_LIMIT'				=> ($data['domain_disk_limit'] > 0) ? tr(sizeit($data['domain_disk_limit'], 'MB')) : tr('unlimited MB'),
			'VL_MAIL_ACCOUNTS_USED'		=> $dat3['mcnt'],
			'VL_MAIL_ACCOUNTS_LIIT'		=> $mail_limit,
			'VL_FTP_ACCOUNTS_USED'		=> $used_ftp_acc,
			'VL_FTP_ACCOUNTS_LIIT'		=> $ftp_limit,
			'VL_SQL_DB_ACCOUNTS_USED'	=> $dat5['dnum'],
			'VL_SQL_DB_ACCOUNTS_LIIT'	=> $sql_db,
			'VL_SQL_USER_ACCOUNTS_USED'	=> $dat6['ucnt'],
			'VL_SQL_USER_ACCOUNTS_LIIT'	=> $sql_users,
			'VL_SUBDOM_ACCOUNTS_USED'	=> $sub_num_data['sub_num'] + $alssub_num_data['sub_num'],
			'VL_SUBDOM_ACCOUNTS_LIIT'	=> $sub_dom,
			'VL_DOMALIAS_ACCOUNTS_USED'	=> $alias_num_data['alias_num'],
			'VL_DOMALIAS_ACCOUNTS_LIIT'	=> $dom_alias
		)
	);
} // end of load_user_data();
?>