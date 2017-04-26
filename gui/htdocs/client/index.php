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

$cfg = EasySCP_Registry::get('Config');

check_login(__FILE__);

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'client/index.tpl';

$theme_color = $cfg->USER_INITIAL_THEME;

$dmn_props = get_domain_default_props($_SESSION['user_id']);

list(
		$sub_cnt,
		$als_cnt,
		$mail_acc_cnt,
		$ftp_acc_cnt,
		$sqld_acc_cnt,
		$sqlu_acc_cnt
	) = get_domain_running_props_cnt($dmn_props['domain_id']);

$dtraff_pr = 0;
$dmn_traff_usage = 0;
$dmn_traff_limit = $dmn_props['domain_traffic_limit'] * 1024 * 1024;

list($dtraff_pr, $dmn_traff_usage) = make_traff_usage($_SESSION['user_id']);

$dmn_disk_limit = $dmn_props['domain_disk_limit'] * 1024 * 1024;

gen_traff_usage($tpl, $dmn_traff_usage * 1024 * 1024, $dmn_traff_limit, 400);

gen_disk_usage($tpl, $dmn_props['domain_disk_usage'], $dmn_disk_limit, 400);

gen_user_messages_label($tpl, $_SESSION['user_id']);

check_user_permissions(
	$tpl, $dmn_props['domain_sqld_limit'], $dmn_props['domain_sqlu_limit'], $dmn_props['domain_php'],
	$dmn_props['domain_cgi'], $dmn_props['domain_ssl'], $dmn_props['allowbackup'], $dmn_props['domain_dns'],
	$dmn_props['domain_subd_limit'], $dmn_props['domain_alias_limit'], $dmn_props['domain_mailacc_limit']
);

$account_name = decode_idna($_SESSION['user_logged']);

if ($dmn_props['domain_expires'] == 0) {
	$dmn_expires_date = tr('Not Set');
} else {
	$date_formt = $cfg->DATE_FORMAT;
	$dmn_expires_date = "( <strong style=\"text-decoration:underline;\">".date($date_formt, $dmn_props['domain_expires'])."</strong> )";
}

list(
	$years,
	$month,
	$days,
	$hours,
	$minutes,
	$seconds
		) = gen_remain_time($dmn_props['domain_expires']);

if (time() < $dmn_props['domain_expires']) {
	if (($years > 0) && ($month > 0) && ($days <= 14)) {
		$tpl->assign(
			array('DMN_EXPIRES' => $years." Years, ".$month." Month, ".$days." Days")
		);
	} else {
		$tpl->assign(
			array('DMN_EXPIRES' => '<span style="color:red">'.$years." Years, " .
									$month." Month, ".$days." Days</span>")
		);
	}
} else if ($dmn_props['domain_expires'] != 0) {
	$tpl->assign(
		array('DMN_EXPIRES' => '<span style="color:red">' .
								tr("This Domain is expired")."</span> ")
	);
} else {
	$tpl->assign(
		array('DMN_EXPIRES' => "")
	);
}

$tpl->assign(
	array(
		'ACCOUNT_NAME'		=> tohtml($account_name),
		'DOMAIN_IP' 		=> get_user_domain_ip($dmn_props['domain_ip_id']),
		'DOMAIN_ALS_URL' 	=> 'http://' . $cfg->APACHE_SUEXEC_USER_PREF . $dmn_props['domain_uid'] . '.' . $cfg->BASE_SERVER_VHOST,
		'MAIN_DOMAIN'		=> tohtml($dmn_props['domain_name']),
		'DMN_EXPIRES_DATE'	=> $dmn_expires_date,
		'SUBDOMAINS'		=> gen_num_limit_msg($sub_cnt, $dmn_props['domain_subd_limit']),
		'DOMAIN_ALIASES'	=> gen_num_limit_msg($als_cnt, $dmn_props['domain_alias_limit']),
		'MAIL_ACCOUNTS'		=> gen_num_limit_msg($mail_acc_cnt, $dmn_props['domain_mailacc_limit']),
		'FTP_ACCOUNTS'		=> gen_num_limit_msg($ftp_acc_cnt, $dmn_props['domain_ftpacc_limit']),
		'SQL_DATABASES'		=> gen_num_limit_msg($sqld_acc_cnt, $dmn_props['domain_sqld_limit']),
		'SQL_USERS'			=> gen_num_limit_msg($sqlu_acc_cnt, $dmn_props['domain_sqlu_limit'])
	)
);

switch(EasyConfig::$cfg->{'DistName'}) {
	case 'CentOS':
		$tpl->assign('MYSQL_SUPPORT', ($dmn_props['domain_sqld_limit'] != -1 && $dmn_props['domain_sqlu_limit'] != -1) ? tr('Yes') . ' / MariaDB ' . substr(DB::getInstance()->getAttribute(PDO::ATTR_SERVER_VERSION), 0, strpos(DB::getInstance()->getAttribute(PDO::ATTR_SERVER_VERSION), '-')) : tr('No')	);
		break;
	default:
		$tpl->assign('MYSQL_SUPPORT', ($dmn_props['domain_sqld_limit'] != -1 && $dmn_props['domain_sqlu_limit'] != -1) ? tr('Yes') . ' / MySQL ' . DB::getInstance()->getAttribute(PDO::ATTR_SERVER_VERSION) : tr('No')	);
}

// static page messages.
gen_logged_from($tpl);

gen_system_message($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Client/Main Index'),
		'TR_GENERAL_INFORMATION' 	=> tr('General information'),
		'TR_ACCOUNT_NAME'			=> tr('Account name'),
		'TR_DOMAIN_EXPIRE' 			=> tr('Domain expire'),
		'TR_MAIN_DOMAIN'			=> tr('Main domain'),
		'TR_PHP_SUPPORT' 			=> tr('PHP support'),
		'TR_CGI_SUPPORT' 			=> tr('CGI support'),
		'TR_SSL_SUPPORT'			=> tr('SSL support'),
		'TR_DNS_SUPPORT' 			=> tr('Manual DNS support'),
		'TR_BACKUP_SUPPORT' 		=> tr('Backup support'),
		'TR_MYSQL_SUPPORT' 			=> tr('SQL support'),
		'TR_SUBDOMAINS' 			=> tr('Subdomains'),
		'TR_DOMAIN_ALIASES' 		=> tr('Domain aliases'),
		'TR_MAIL_ACCOUNTS' 			=> tr('Mail accounts'),
		'TR_FTP_ACCOUNTS' 			=> tr('FTP accounts'),
		'TR_SQL_DATABASES' 			=> tr('SQL databases'),
		'TR_SQL_USERS' 				=> tr('SQL users'),
		'TR_MESSAGES' 				=> tr('Support system'),
		'TR_LANGUAGE' 				=> tr('Language'),
		'TR_CHOOSE_DEFAULT_LANGUAGE'=> tr('Choose default language'),
		'TR_SAVE' 					=> tr('Save'),
		'TR_TRAFFIC_USAGE' 			=> tr('Traffic usage'),
		'TR_DISK_USAGE'				=> tr('Disk usage'),
		'TR_DMN_TMP_ACCESS'			=> tr('Alternative URL to reach your website')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_general_information.tpl');
gen_client_menu($tpl, 'client/menu_general_information.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

function gen_num_limit_msg($num, $limit) {
	if ($limit == -1) {
		return tr('disabled');
	}
	if ($limit == 0) {
		return $num . '&nbsp;/&nbsp;' . tr('unlimited');
	}
	return $num . '&nbsp;/&nbsp;' . $limit;
}

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function gen_system_message($tpl) {
	$sql = EasySCP_Registry::get('Db');
	$user_id = $_SESSION['user_id'];

	$query = "
		SELECT
			COUNT(`ticket_id`) AS cnum
		FROM
			`tickets`
		WHERE
			(`ticket_to` = ? OR `ticket_from` = ?)
		AND
			`ticket_status` IN ('2')
		AND
			`ticket_reply` = 0
	";

	$rs = exec_query($sql, $query, array($user_id, $user_id));

	$num_question = $rs->fields('cnum');

	if ($num_question == 0) {
		$tpl->assign(array('MSG_ENTRY' => ''));
	} else {
		$tpl->assign(
			array(
				'TR_NEW_MSGS' => tr('You have <strong>%d</strong> new answer to your support questions', $num_question),
				'NEW_MSG_TYPE' => 'info',
				'TR_VIEW' => tr('View')
			)
		);
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param float $usage
 * @param float $max_usage
 * @param float $bars_max
 */
function gen_traff_usage($tpl, $usage, $max_usage, $bars_max) {
	list($percent, $bars) = calc_bars($usage, $max_usage, $bars_max);
	if ($max_usage != 0) {
		$traffic_usage_data = tr('%1$d%% [%2$s of %3$s]', $percent, sizeit($usage), sizeit($max_usage));
	} else {
		$traffic_usage_data = tr('%1$d%% [%2$s of unlimited]', $percent, sizeit($usage));
	}

	$tpl->assign(
		array(
			'TRAFFIC_USAGE_DATA' => $traffic_usage_data,
			'TRAFFIC_BARS'	   => $bars,
			'TRAFFIC_PERCENT'	=> $percent,
		)
	);

	if ($max_usage != 0 && $usage > $max_usage) {
		$tpl->assign('TR_TRAFFIC_WARNING', tr('You are exceeding your traffic limit!'));
	} else {
		$tpl->assign('TRAFF_WARN', '');
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param float $usage
 * @param float $max_usage
 * @param float $bars_max
 */
function gen_disk_usage($tpl, $usage, $max_usage, $bars_max) {
	list($percent, $bars) = calc_bars($usage, $max_usage, $bars_max);

	if ($max_usage != 0) {
		$traffic_usage_data = tr('%1$s%% [%2$s of %3$s]', $percent, sizeit($usage), sizeit($max_usage));
	} else {
		$traffic_usage_data = tr('%1$s%% [%2$s of unlimited]', $percent, sizeit($usage));
	}

	$tpl->assign(
		array(
			'DISK_USAGE_DATA' => $traffic_usage_data,
			'DISK_BARS'	   => $bars,
			'DISK_PERCENT'	=> $percent,
		)
	);
	if ($max_usage != 0 && $usage > $max_usage) {
		$tpl->assign('TR_DISK_WARNING', tr('You are exceeding your disk limit!'));
	} else {
		$tpl->assign('DISK_WARN', '');
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $dmn_sqld_limit
 * @param int $dmn_sqlu_limit
 * @param string $dmn_php
 * @param string $dmn_cgi
 * @param string $backup
 * @param string $dns
 * @param int $dmn_subd_limit
 * @param int $als_cnt
 * @param int $dmn_mailacc_limit
 */
function check_user_permissions($tpl, $dmn_sqld_limit, $dmn_sqlu_limit, $dmn_php,
	$dmn_cgi,$dmn_ssl,$backup, $dns, $dmn_subd_limit, $als_cnt, $dmn_mailacc_limit) {

	// check if mail accouts available are available for this user
	if ($dmn_mailacc_limit == -1) {
		$_SESSION['email_support'] = "no";
		$tpl->assign('T_MAILS_SUPPORT', '');
	} else {

	}

	// check if alias are available for this user
	if ($als_cnt == -1) {
		$_SESSION['alias_support'] = "no";
		$tpl->assign('T_ALIAS_SUPPORT', '');
	} else {

	}

	// check if subdomains are available for this user
	if ($dmn_subd_limit == -1) {
		$_SESSION['subdomain_support'] = "no";
		$tpl->assign('T_SDM_SUPPORT', '');
	} else {

	}

	// check if SQL Support is available for this user
	if ($dmn_sqld_limit == -1 || $dmn_sqlu_limit == -1) {
		$_SESSION['sql_support'] = "no";
		$tpl->assign('SQL_SUPPORT', '');
		$tpl->assign('T_SQL1_SUPPORT', '');
		$tpl->assign('T_SQL2_SUPPORT', '');
	} else {

	}

	// check if PHP Support is available for this user
	if ($dmn_php == 'yes') {
		switch(EasyConfig::$cfg->{'DistName'}) {
			case 'CentOS':
				$tpl->assign( array('PHP_SUPPORT' => tr('Yes') . ' / PHP ' . PHP_VERSION));
				break;
			default:
				$tpl->assign( array('PHP_SUPPORT' => tr('Yes') . ' / PHP ' . substr(PHP_VERSION, 0, strpos(PHP_VERSION, '-'))));
		}
	}

	// check if CGI Support is available for this user
	if ($dmn_cgi == 'yes') {
		$tpl->assign( array('CGI_SUPPORT' => tr('Yes')));
	}

	// check if SSL Support is available for this user
	if ($dmn_ssl == 'yes') {
		$tpl->assign( array('SSL_SUPPORT' => tr('Yes')));
	}        
        
	// Check if Backup support is available for this user
	switch($backup){
	case "full":
		$tpl->assign( array('BACKUP_SUPPORT' => tr('Full')));
		break;
	case "sql":
		$tpl->assign( array('BACKUP_SUPPORT' => tr('SQL')));
		break;
	case "domain":
		$tpl->assign( array('BACKUP_SUPPORT' => tr('Domain')));
		break;
	default:
		$tpl->assign('T_BACKUP_SUPPORT', '');
	}
	/*
	if ($tpl->is_namespace('BACKUP_SUPPORT')) {

	}
	*/

	// Check if Manual DNS support is available for this user
	if ($dns == 'no') {
		$tpl->assign('T_DNS_SUPPORT', '');
	} else {
		$tpl->assign(
		array('DNS_SUPPORT' => tr('Yes')));

	}

} // end check_user_permissions()

/**
 * Calculate the usage traffic
 * @param int $domain_id
 * @return array percent, value
 */
function make_traff_usage($domain_id) {
	$sql = EasySCP_Registry::get('Db');

	$res = exec_query($sql, "SELECT `domain_id` FROM `domain` WHERE `domain_admin_id` = ?", $domain_id);
	$dom_id = $res->fetchRow();
	$domain_id = $dom_id['domain_id'];

	$res = exec_query($sql, "SELECT `domain_traffic_limit` FROM `domain` WHERE `domain_id` = ?", $domain_id);
	$dat = $res->fetchRow();

	$fdofmnth = mktime(0, 0, 0, date("m"), 1, date("Y"));
	$ldofmnth = mktime(1, 0, 0, date("m") + 1, 0, date("Y"));
	$res = exec_query($sql,
		"SELECT IFNULL(SUM(`dtraff_web_in`) + SUM(`dtraff_web_out`) + SUM(`dtraff_ftp_in`) + SUM(`dtraff_ftp_out`) + SUM(`dtraff_mail`) + SUM(`dtraff_pop`), 0) "
		. "AS traffic FROM `domain_traffic` " . "WHERE `domain_id` = ? AND `dtraff_time` > ? AND `dtraff_time` < ?",
		array($domain_id, $fdofmnth, $ldofmnth));
	$data = $res->fetchRow();
	$traff = ($data['traffic'] / 1024) / 1024;

	if ($dat['domain_traffic_limit'] == 0) {
		$pr = 0;
	} else {
		$pr = ($traff / $dat['domain_traffic_limit']) * 100;
		$pr = sprintf("%.2f", $pr);
	}

	return array($pr, $traff);

} // End of make_traff_usage()

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $user_id
 */
function gen_user_messages_label($tpl, $user_id) {
	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			COUNT(`ticket_id`) AS cnum
		FROM
			`tickets`
		WHERE
			`ticket_from` = ?
		AND
			`ticket_status` = '2'
	";

	$rs = exec_query($sql, $query, $user_id);
	$num_question = $rs->fields('cnum');

	if ($num_question == 0) {
		$tpl->assign(
			array(
				'TR_NO_NEW_MESSAGES' => tr('You have no new support questions!'),
				'MSG_ENTRY' => ''
			)
		);
	} else {
		$tpl->assign(
			array(
				'NO_MESSAGES' => '',
				'TR_NEW_MSGS' => tr('You have <strong>%d</strong> new support questions', $num_question),
				'TR_VIEW' => tr('View')
			)
		);
	}
}

function gen_remain_time($dbtime){

        // needed for calculation
        $mi	= 60;
        $h	= $mi * $mi;
        $d	= $h * 24;
        $mo = $d * 30;
        $y	= $d * 365;

        // calculation of: years, month, days, hours, minutes, seconds
        $difftime = $dbtime - time();
        $years = floor($difftime / $y);
        $difftime = $difftime % $y;
        $month = floor($difftime / $mo);
        $difftime = $difftime % $mo;
        $days = floor($difftime / $d);
        $difftime = $difftime % $d;
        $hours = floor($difftime / $h);
        $difftime = $difftime % $h;
        $minutes = floor($difftime / $mi);
		 $difftime = $difftime % $mi;
        $seconds = $difftime;

        // put into array and return
        return array($years, $month, $days, $hours, $minutes, $seconds);
}
?>