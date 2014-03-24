<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'admin/reseller_user_statistics.tpl';

if (isset($_POST['rid']) && isset($_POST['name'])) {
	$rid = $_POST['rid'];
	$name = $_POST['name'];
} else if (isset($_GET['rid']) && isset($_GET['name'])) {
	$rid = $_GET['rid'];
	$name = $_GET['name'];
}

$year = 0;
$month = 0;

if (isset($_POST['month']) && isset($_POST['year'])) {
	$year = $_POST['year'];
	$month = $_POST['month'];
} else if (isset($_GET['month']) && isset($_GET['year'])) {
	$month = $_GET['month'];
	$year = $_GET['year'];
}

if (!is_numeric($rid) || !is_numeric($month) || !is_numeric($year)) {
	user_goto('reseller_statistics.php');
}

gen_select_lists($tpl, $month, $year);

generate_page($tpl, $rid, $name);

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'					=> tr('EasySCP - Admin/Reseller User Statistics'),
		'TR_RESELLER_STATISTICS'		=> tr('Reseller statistics table'),
		'TR_RESELLER_USER_STATISTICS'	=> tr('Reseller users table'),
		'TR_MONTH'						=> tr('Month'),
		'TR_YEAR'						=> tr('Year'),
		'TR_SHOW'						=> tr('Show'),
		'TR_NO_DOMAINS'					=> tr('This reseller has no domains.'),
		'TR_DOMAIN_NAME'				=> tr('Domain'),
		'TR_TRAFF'						=> tr('Traffic<br />usage'),
		'TR_DISK'						=> tr('Disk<br />usage'),
		'TR_WEB'						=> tr('Web<br />traffic'),
		'TR_FTP_TRAFF'					=> tr('FTP<br />traffic'),
		'TR_SMTP'						=> tr('SMTP<br />traffic'),
		'TR_POP3'						=> tr('POP3/IMAP<br />traffic'),
		'TR_SUBDOMAIN'					=> tr('Subdomain'),
		'TR_ALIAS'						=> tr('Alias'),
		'TR_MAIL'						=> tr('Mail'),
		'TR_FTP'						=> tr('FTP'),
		'TR_SQL_DB'						=> tr('SQL<br />database'),
		'TR_SQL_USER'					=> tr('SQL<br />user'),
		'VALUE_NAME'					=> tohtml($name),
		'VALUE_RID'						=> $rid
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_statistics.tpl');
gen_admin_menu($tpl, 'admin/menu_statistics.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $reseller_id
 * @param string $reseller_name
 */
function generate_page($tpl, $reseller_id, $reseller_name) {

	global $rid;
	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$start_index = 0;

	$rows_per_page = $cfg->DOMAIN_ROWS_PER_PAGE;

	if (isset($_GET['psi'])) {
		$start_index = $_GET['psi'];
	} else if (isset($_POST['psi'])) {
		$start_index = $_POST['psi'];
	}

	$tpl->assign(
		array(
			'POST_PREV_PSI' => $start_index
		)
	);

	// count query
	$count_query = "
		SELECT
			COUNT(`admin_id`) AS cnt
		FROM
			`admin`
		WHERE
			`admin_type` = 'user'
		AND
			`created_by` = ?
	";

	$query = <<<SQL_QUERY
		SELECT
			`admin_id`
		FROM
			`admin`
		WHERE
			`admin_type` = 'user'
		AND
			`created_by` = ?
		ORDER BY
			`admin_name` DESC
		LIMIT
			$start_index, $rows_per_page
SQL_QUERY;

	$rs = exec_query($sql, $count_query, $reseller_id);
	$records_count = $rs->fields['cnt'];

	$rs = exec_query($sql, $query, $reseller_id);

	$tpl->assign(
		array(
			'RESELLER_NAME' => tohtml($reseller_name),
			'RESELLER_ID' => $reseller_id
		)
	);

	if ($rs->rowCount() == 0) {
		$tpl->assign(
			array(
				'DOMAIN_LIST' => '',
				'SCROLL_PREV' => '',
				'SCROLL_NEXT' => '',
			)
		);
	} else {
		$prev_si = $start_index - $rows_per_page;

		if ($start_index == 0) {
			$tpl->assign('SCROLL_PREV', '');
		} else {
			$tpl->assign(
				array(
					'SCROLL_PREV_GRAY' => '',
					'PREV_PSI' => $prev_si,
					'RID' => $rid
				)
			);
		}

		$next_si = $start_index + $rows_per_page;

		if ($next_si + 1 > $records_count) {
			$tpl->assign('SCROLL_NEXT', '');
		} else {
			$tpl->assign(
				array(
					'SCROLL_NEXT_GRAY' => '',
					'NEXT_PSI' => $next_si,
					'RID' => $rid
				)
			);
		}

		$row = 1;

		while (!$rs->EOF) {
			$admin_id = $rs->fields['admin_id'];

			$query = "
				SELECT
					`domain_id`
				FROM
					`domain`
				WHERE
					`domain_admin_id` = ?
			;";

			$dres = exec_query ($sql, $query, $admin_id);

			generate_domain_entry($tpl, $dres->fields['domain_id'], $row++);


			$rs->moveNext();
		}
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $user_id
 * @param int $row
 */
function generate_domain_entry($tpl, $user_id, $row) {

	global $crnt_month, $crnt_year;

	list($domain_name, $domain_id, $web, $ftp, $smtp, $pop3, $utraff_current,
		$udisk_current, , ) = generate_user_traffic($user_id);

	list($usub_current, $usub_max, $uals_current, $uals_max, $umail_current,
		$umail_max,	$uftp_current, $uftp_max, $usql_db_current, $usql_db_max,
		$usql_user_current, $usql_user_max,	$utraff_max, $udisk_max
	) = generate_user_props($user_id);

	$utraff_max = $utraff_max * 1024 * 1024;
	$udisk_max = $udisk_max * 1024 * 1024;

	$traff_show_percent = calc_bar_value($utraff_current, $utraff_max, 400);
	$disk_show_percent  = calc_bar_value($udisk_current, $udisk_max, 400);

	if ($utraff_max > 0) {
		$traff_percent = (($utraff_current/$utraff_max)*100 < 99.7) ? ($utraff_current/$utraff_max)*100 : 99.7;
	} else {
		$traff_percent = 0;
	}

	if ($udisk_max > 0) {
		$disk_percent = (($udisk_current/$udisk_max)*100 < 99.7) ? ($udisk_current/$udisk_max)*100 : 99.7;
	} else {
		$disk_percent = 0;
	}

	$domain_name = decode_idna($domain_name);

	$tpl->append(
		array(
			'DOMAIN_NAME' => tohtml($domain_name),

			'MONTH' => $crnt_month,
			'YEAR' => $crnt_year,
			'DOMAIN_ID' => $domain_id,

			'TRAFF_SHOW_PERCENT' => $traff_show_percent,
			'TRAFF_PERCENT' => $traff_percent,

			'TRAFF_MSG' => ($utraff_max)
				? tr('%1$s <br/>of<br/> <strong>%2$s</strong>', sizeit($utraff_current), sizeit($utraff_max))
				: tr('%s <br/>of<br/> <strong>unlimited</strong>', sizeit($utraff_current)),


			'DISK_SHOW_PERCENT' => $disk_show_percent,
			'DISK_PERCENT' => $disk_percent,

			'DISK_MSG' => ($udisk_max)
				? tr('%1$s <br/>of<br/> <strong>%2$s</strong>', sizeit($udisk_current), sizeit($udisk_max))
				: tr('%s <br/>of<br/> <strong>unlimited</strong>', sizeit($udisk_current)),


			'WEB' => sizeit($web),
			'FTP' => sizeit($ftp),
			'SMTP' => sizeit($smtp),
			'POP3' => sizeit($pop3),

			'SUB_MSG' => ($usub_max)
				? (($usub_max > 0)
					? tr('%1$d <br/>of<br/> <strong>%2$d</strong>', sizeit($usub_current), $usub_max)
					: tr('<strong>disabled</strong>'))
				: tr('%d <br/>of<br/> <strong>unlimited</strong>', sizeit($usub_current)),

			'ALS_MSG' => ($uals_max)
				? (($uals_max > 0)
					? tr('%1$d <br/>of<br/> <strong>%2$d</strong>', sizeit($uals_current), $uals_max)
					: tr('<strong>disabled</strong>'))
				: tr('%d <br/>of<br/> <strong>unlimited</strong>', sizeit($uals_current)),

			'MAIL_MSG' => ($umail_max)
				? (($umail_max > 0)
					? tr('%1$d <br/>of<br/> <strong>%2$d</strong>', $umail_current, $umail_max)
					: tr('<strong>disabled</strong>'))
				: tr('%d <br/>of<br/> <strong>unlimited</strong>', $umail_current),

			'FTP_MSG' => ($uftp_max)
				? (($uftp_max > 0)
					? tr('%1$d <br/>of<br/> <strong>%2$d</strong>', $uftp_current, $uftp_max)
					: tr('<strong>disabled</strong>'))
				: tr('%d <br/>of<br/> <strong>unlimited</strong>', $uftp_current),

			'SQL_DB_MSG' => ($usql_db_max)
				? (($usql_db_max > 0)
					? tr('%1$d <br/>of<br/> <strong>%2$d</strong>', $usql_db_current, $usql_db_max)
					: tr('<strong>disabled</strong>'))
				: tr('%d <br/>of<br/> <strong>unlimited</strong>', $usql_db_current),
			'SQL_USER_MSG' => ($usql_user_max)
				? (($usql_user_max > 0)
					? tr('%1$d <br/>of<br/> <strong>%2$d</strong>', $usql_user_current, $usql_user_max)
					: tr('<strong>disabled</strong>'))
				: tr('%d <br/>of<br/> <strong>unlimited</strong>', $usql_user_current)
		)
	);
}
?>