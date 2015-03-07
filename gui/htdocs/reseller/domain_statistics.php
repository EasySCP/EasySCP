<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2015 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'reseller/domain_statistics.tpl';

if (isset($_POST['domain_id'])) {
	$domain_id = $_POST['domain_id'];
} else if (isset($_GET['domain_id'])) {
	$domain_id = $_GET['domain_id'];
}

$month = date('m');
$year = date('Y');

if (isset($_POST['month']) && isset($_POST['year'])) {
	$year = intval($_POST['year']);
	$month = intval($_POST['month']);
} else if (isset($_GET['month']) && isset($_GET['year'])) {
	$month = intval($_GET['month']);
	$year = intval($_GET['year']);
}

if (!is_numeric($domain_id) || !is_numeric($month) || !is_numeric($year)) {
	user_goto('reseller_statistics.php');
}

gen_select_lists($tpl, $month, $year);

generate_page($tpl, $month, $year, $domain_id);

// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Domain Statistics Data'),
		'TR_DOMAIN_STATISTICS'	=> tr('Domain statistics'),
		'TR_MONTH'				=> tr('Month'),
		'TR_YEAR'				=> tr('Year'),
		'TR_SHOW'				=> tr('Show'),
		'TR_WEB_TRAFFIC'		=> tr('Web traffic'),
		'TR_FTP_TRAFFIC'		=> tr('FTP traffic'),
		'TR_SMTP_TRAFFIC'		=> tr('SMTP traffic'),
		'TR_POP3_TRAFFIC'		=> tr('POP3/IMAP traffic'),
		'TR_ALL_TRAFFIC'		=> tr('All traffic'),
		'TR_ALL'				=> tr('All'),
		'TR_DAY'				=> tr('Day')
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_statistics.tpl');
gen_reseller_menu($tpl, 'reseller/menu_statistics.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

function get_domain_trafic($from, $to, $domain_id) {
	$sql = EasySCP_Registry::get('Db');
	$reseller_id = $_SESSION['user_id'];
	$query = "
		SELECT
			`domain_id`
		FROM
			`domain`
		WHERE
			`domain_id` = ? AND `domain_created_id` = ?
	";

	$rs = exec_query($sql, $query, array($domain_id, $reseller_id));
	if ($rs->recordCount() == 0) {
		set_page_message(
			tr('User does not exist or you do not have permission to access this interface!'),
			'error'
		);
		user_goto('user_statistics.php');
	}

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
 * @param int $domain_id
 */
function generate_page($tpl, $month, $year, $domain_id) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	if ($month == date('m') && $year == date('Y')) {
		$curday = date('j');
	} else {
		$tmp = mktime(1, 0, 0, $month + 1, 0, $year);
		$curday = date('j', $tmp);
	}

	$sum_web = 0;
	$sum_ftp = 0;
	$sum_mail= 0;
	$sum_pop = 0;

	for ($i = 1; $i <= $curday; $i++) {
		$ftm = mktime(0, 0, 0, $month, $i, $year);

		$ltm = mktime(23, 59, 59, $month, $i, $year);

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

		list($web_trf,
			$ftp_trf,
			$pop_trf,
			$smtp_trf) = get_domain_trafic($ftm, $ltm, $domain_id);

		if ($web_trf == 0 && $ftp_trf == 0 && $smtp_trf == 0 && $pop_trf == 0) {
			$tpl->append(
				array(
					'DATE' => date($cfg->DATE_FORMAT, strtotime($year . "-" . $month . "-" . $i)),
					'WEB_TRAFFIC' => 0,
					'FTP_TRAFFIC' => 0,
					'SMTP_TRAFFIC' => 0,
					'POP3_TRAFFIC' => 0,
					'ALL_TRAFFIC' => 0
				)
			);
		} else {
			$sum_web += $web_trf;
			$sum_ftp += $ftp_trf;
			$sum_mail += $smtp_trf;
			$sum_pop += $pop_trf;

			$tpl->append(
				array(
					'DATE' => date($cfg->DATE_FORMAT, strtotime($year . "-" . $month . "-" . $i)),
					'WEB_TRAFFIC' => sizeit($web_trf),
					'FTP_TRAFFIC' => sizeit($ftp_trf),
					'SMTP_TRAFFIC' => sizeit($smtp_trf),
					'POP3_TRAFFIC' => sizeit($pop_trf),
					'ALL_TRAFFIC' => sizeit($web_trf + $ftp_trf + $smtp_trf + $pop_trf)
				)
			);
		}

		$tpl->assign(
			array(
				'MONTH' => $month,
				'YEAR' => $year,
				'DOMAIN_ID' => $domain_id,
				'ALL_WEB_TRAFFIC' => sizeit($sum_web),
				'ALL_FTP_TRAFFIC' => sizeit($sum_ftp),
				'ALL_SMTP_TRAFFIC' => sizeit($sum_mail),
				'ALL_POP3_TRAFFIC' => sizeit($sum_pop),
				'ALL_ALL_TRAFFIC' => sizeit($sum_web + $sum_ftp + $sum_mail + $sum_pop)
			)
		);

	}
}
?>