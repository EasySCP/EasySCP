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
$template = 'client/domain_statistics.tpl';

// dynamic page data.
$current_month = date('m');
$current_year = date('Y');

list($current_month, $current_year) = gen_page_post_data($tpl, $current_month, $current_year);
gen_dmn_traff_list($tpl, $current_month, $current_year, $_SESSION['user_id']);

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Client/Domain Statistics'),
		'TR_DOMAIN_STATISTICS'	=> tr('Domain statistics'),
		'DOMAIN_URL'			=> 'http://' . $_SESSION['user_logged'] . '/stats/',
		'TR_AWSTATS'			=> tr('Web Stats'),
		'TR_MONTH'				=> tr('Month'),
		'TR_YEAR'				=> tr('Year'),
		'TR_SHOW'				=> tr('Show'),
		'TR_DATE'				=> tr('Date'),
		'TR_WEB_TRAFF_IN'		=> tr('Web in'),
		'TR_WEB_TRAFF_OUT'		=> tr('Web out'),
//		'TR_WEB_TRAFF' => tr('WEB'),
		'TR_FTP_TRAFF'			=> tr('FTP'),
//		'TR_SMTP_TRAFF' => tr('SMTP'),
//		'TR_POP_TRAFF' => tr('POP3/IMAP'),
		'TR_SUM'				=> tr('Sum'),
		'TR_ALL'				=> tr('Total')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_statistics.tpl');
gen_client_menu($tpl, 'client/menu_statistics.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// page functions.

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $month
 * @param int $year
 */
function gen_page_date($tpl, $month, $year) {

	$cfg = EasySCP_Registry::get('Config');

	for ($i = 1; $i <= 12; $i++) {
		$tpl->append(
			array(
				'MONTH_SELECTED' => ($i == $month) ? $cfg->HTML_SELECTED : '',
				'MONTH' => $i
			)
		);
	}

	for ($i = $year - 1; $i <= $year + 1; $i++) {
		$tpl->append(
			array(
				'YEAR_SELECTED' => ($i == $year) ? $cfg->HTML_SELECTED : '',
				'YEAR' => $i
			)
		);
	}
}

function gen_page_post_data($tpl, $current_month, $current_year) {

	if (isset($_POST['uaction']) && $_POST['uaction'] === 'show_traff') {
		$current_month = $_POST['month'];
		$current_year = $_POST['year'];
	}

	gen_page_date($tpl, $current_month, $current_year);
	return array($current_month, $current_year);
}

function get_domain_trafic($from, $to, $domain_id) {

	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			IFNULL(SUM(`dtraff_web_in`), 0) AS web_dr_in,
			IFNULL(SUM(`dtraff_web_out`), 0) AS web_dr_out,
			IFNULL(SUM(`dtraff_ftp_in`), 0) AS ftp_dr_in,
			IFNULL(SUM(`dtraff_ftp_out`), 0) AS ftp_dr_out,
			IFNULL(SUM(`dtraff_mail`), 0) AS mail_dr,
			IFNULL(SUM(`dtraff_pop`), 0) AS pop_dr
		FROM
			`domain_traffic`
		WHERE
			`domain_id` = ?
		AND
			`dtraff_time` >= ?
		AND
			`dtraff_time` <= ?
	";

	$rs = exec_query($sql, $query, array($domain_id, $from, $to));

	if ($rs->recordCount() == 0) {
		return array(0, 0, 0, 0, 0, 0);
	} else {
		return array(
			$rs->fields['web_dr_in'],
			$rs->fields['web_dr_out'],
			$rs->fields['ftp_dr_in'],
			$rs->fields['ftp_dr_out'],
			$rs->fields['pop_dr'],
			$rs->fields['mail_dr']
		);
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $month
 * @param int $year
 * @param int $user_id
 */
function gen_dmn_traff_list($tpl, $month, $year, $user_id) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			`domain_id`
		FROM
			`domain`
		WHERE
			`domain_admin_id` = ?
	";

	$rs = exec_query($sql, $query, $user_id);
	$domain_id = $rs->fields('domain_id');

	if ($month == date('m') && $year == date('Y')) {
		$curday = date('j');
	} else {
		$tmp = mktime(1, 0, 0, $month + 1, 0, $year);
		$curday = date('j', $tmp);
	}

	$sum_web_in = 0;
	$sum_web_out = 0;
	$sum_ftp = 0;
	$sum_mail= 0;
	$sum_pop = 0;

	for ($i = 1; $i <= $curday; $i++) {
		$ftm = mktime(0, 0, 0, $month, $i, $year);

		$ltm = mktime(23, 59, 59, $month, $i, $year);

		/*
		$query = "
			SELECT
				`dtraff_web_in`, `dtraff_web_out`, `dtraff_ftp_in`, `dtraff_ftp_out`, `dtraff_mail`, `dtraff_pop`, `dtraff_time`
			FROM
				`domain_traffic`
			WHERE
				`domain_id` = ?
			AND
				`dtraff_time` >= ?
			AND
				`dtraff_time` <= ?
		";

		exec_query($sql, $query, array($domain_id, $ftm, $ltm));
		*/

		list($web_trf_in,
			$web_trf_out,
			$ftp_trf_in,
			$ftp_trf_out,
			$pop_trf,
			$smtp_trf) = get_domain_trafic($ftm, $ltm, $domain_id);


		$sum_web_in += $web_trf_in;
		$sum_web_out += $web_trf_out;
		$sum_ftp += $ftp_trf_in;
		$sum_ftp += $ftp_trf_out;
		$sum_mail += $smtp_trf;
		$sum_pop += $pop_trf;

		$date_formt = $cfg->DATE_FORMAT;

		$tpl->append(
			array(
				'DATE' => date($date_formt, strtotime($year . "-" . $month . "-" . $i)),
				'WEB_TRAFFIC_IN' => sizeit($web_trf_in),
				'WEB_TRAFFIC_OUT' => sizeit($web_trf_out),
				'FTP_TRAFFIC' => sizeit($ftp_trf_in + $ftp_trf_out),
				'SMTP_TRAFFIC' => sizeit($smtp_trf),
				'POP3_TRAFFIC' => sizeit($pop_trf),
				'SUM_TRAFFIC' => sizeit($web_trf_in + $web_trf_out + $ftp_trf_in + $ftp_trf_out + $smtp_trf + $pop_trf),
			)
		);
	}

	$tpl->assign(
		array(
			'WEB_ALL_IN' => sizeit($sum_web_in),
			'WEB_ALL_OUT' => sizeit($sum_web_out),
			'FTP_ALL' => sizeit($sum_ftp),
			'SMTP_ALL' => sizeit($sum_mail),
			'POP3_ALL' => sizeit($sum_pop),
			'SUM_ALL' => sizeit($sum_web_in + $sum_web_out + $sum_ftp + $sum_mail + $sum_pop)
		)
	);
}
?>