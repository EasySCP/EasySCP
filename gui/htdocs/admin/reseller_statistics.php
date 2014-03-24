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

$template = 'admin/reseller_statistics.tpl';

$year = 0;
$month = 0;

if (isset($_POST['month']) && isset($_POST['year'])) {
	$year = $_POST['year'];

	$month = $_POST['month'];
} else if (isset($_GET['month']) && isset($_GET['year'])) {
	$month = $_GET['month'];

	$year = $_GET['year'];
}

$crnt_month = '';
$crnt_year = '';

generate_page ($tpl);

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Admin/Reseller statistics'),
		'TR_RESELLER_STATISTICS'=> tr('Reseller statistics table'),
		'TR_MONTH'				=> tr('Month'),
		'TR_YEAR'				=> tr('Year'),
		'TR_SHOW'				=> tr('Show'),
		'TR_RESELLER_NAME'		=> tr('Reseller name'),
		'TR_TRAFF'				=> tr('Traffic'),
		'TR_DISK'				=> tr('Disk'),
		'TR_DOMAIN'				=> tr('Domain'),
		'TR_SUBDOMAIN'			=> tr('Subdomain'),
		'TR_ALIAS'				=> tr('Alias'),
		'TR_MAIL'				=> tr('Mail'),
		'TR_FTP'				=> tr('FTP'),
		'TR_SQL_DB'				=> tr('SQL Database'),
		'TR_SQL_USER'			=> tr('SQL User')
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
 */
function generate_page($tpl) {

	global $month, $year;

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$start_index = 0;

	$rows_per_page = $cfg->DOMAIN_ROWS_PER_PAGE;

	if (isset($_GET['psi']) && is_numeric($_GET['psi'])) {
		$start_index = $_GET['psi'];
	} else if (isset($_POST['psi']) && is_numeric($_GET['psi'])) {
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
			`admin_type` = 'reseller'
	";

	$query = "
		SELECT
			`admin_id`, `admin_name`
		FROM
			`admin`
		WHERE
			`admin_type` = 'reseller'
		ORDER BY
			`admin_name` DESC
		LIMIT
			$start_index, $rows_per_page;
	";

	$rs = exec_query($sql, $count_query);
	$records_count = $rs->fields['cnt'];

	$rs = exec_query($sql, $query);

	if ($rs->rowCount() == 0) {
		set_page_message(tr('There are no resellers in your system!'), 'info');
		return;
	} else {
		$prev_si = $start_index - $rows_per_page;

		if ($start_index == 0) {
			$tpl->assign('SCROLL_PREV', '');
		} else {
			$tpl->assign(
				array(
					'SCROLL_PREV_GRAY'	=> '',
					'PREV_PSI'			=> $prev_si
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
					'NEXT_PSI' => $next_si
				)
			);
		}

		gen_select_lists($tpl, @$month, @$year);

		$row = 1;

		while (!$rs->EOF) {
			generate_reseller_entry($tpl, $rs->fields['admin_id'], $rs->fields['admin_name'], $row++);

			$rs->moveNext();
		}
	}

}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $reseller_id
 * @param string $reseller_name
 * @param int $row
 * @return void
 */
function generate_reseller_entry($tpl, $reseller_id, $reseller_name, $row) {
	global $crnt_month, $crnt_year;

	list($rdmn_current, $rdmn_max,
		$rsub_current, $rsub_max,
		$rals_current, $rals_max,
		$rmail_current, $rmail_max,
		$rftp_current, $rftp_max,
		$rsql_db_current, $rsql_db_max,
		$rsql_user_current, $rsql_user_max,
		$rtraff_current, $rtraff_max,
		$rdisk_current, $rdisk_max
	) = generate_reseller_props($reseller_id);

	list($udmn_current, , ,
		$usub_current, , ,
		$uals_current, , ,
		$umail_current, , ,
		$uftp_current, , ,
		$usql_db_current, , ,
		$usql_user_current, , ,
		$utraff_current, , ,
		$udisk_current, ,
	) = generate_reseller_users_props($reseller_id);

	$rtraff_max = $rtraff_max * 1024 * 1024;
	$rtraff_current = $rtraff_current * 1024 * 1024;
	$rdisk_max = $rdisk_max * 1024 * 1024;
	$rdisk_current = $rdisk_current * 1024 * 1024;

	$traff_show_percent = calc_bar_value($utraff_current, $rtraff_max, 400);
	$disk_show_percent  = calc_bar_value($udisk_current, $rdisk_max, 400);

	if ($rtraff_max > 0) {
		$traff_percent = (($utraff_current/$rtraff_max)*100 < 99.7) ? ($utraff_current/$rtraff_max)*100 : 99.7;
	} else {
		$traff_percent = 0;
	}

	if ($rdisk_max > 0) {
		$disk_percent = (($udisk_current/$rdisk_max)*100 < 99.7) ? ($udisk_current/$rdisk_max)*100 : 99.7;
	} else {
		$disk_percent = 0;
	}

	$tpl->append(
		array(
			'RESELLER_NAME' => tohtml($reseller_name),
			'RESELLER_ID' => $reseller_id,
			'MONTH' => $crnt_month,
			'YEAR' => $crnt_year,

			'TRAFF_SHOW_PERCENT' => $traff_show_percent,
			'TRAFF_PERCENT' => $traff_percent,

			'TRAFF_MSG' => ($rtraff_max)
				? tr('%1$s / %2$s <br/>of<br/> <strong>%3$s</strong>', sizeit($utraff_current), sizeit($rtraff_current), sizeit($rtraff_max))
				: tr('%1$s / %2$s <br/>of<br/> <strong>unlimited</strong>', sizeit($utraff_current), sizeit($rtraff_current)),

			'DISK_SHOW_PERCENT' => $disk_show_percent,
			'DISK_PERCENT' => $disk_percent,

			'DISK_MSG' => ($rdisk_max)
				? tr('%1$s / %2$s <br/>of<br/> <strong>%3$s</strong>', sizeit($udisk_current), sizeit($rdisk_current), sizeit($rdisk_max))
				: tr('%1$s / %2$s <br/>of<br/> <strong>unlimited</strong>', sizeit($udisk_current), sizeit($rdisk_current)),

			'DMN_MSG' => ($rdmn_max)
				? tr('%1$d / %2$d <br/>of<br/> <strong>%3$d</strong>', $udmn_current, $rdmn_current, $rdmn_max)
				: tr('%1$d / %2$d <br/>of<br/> <strong>unlimited</strong>', $udmn_current, $rdmn_current),

			'SUB_MSG' => ($rsub_max > 0)
				? tr('%1$d / %2$d <br/>of<br/> <strong>%3$d</strong>', $usub_current, $rsub_current, $rsub_max)
				: (($rsub_max === "-1") ? tr('<strong>disabled</strong>') : tr('%1$d / %2$d <br/>of<br/> <strong>unlimited</strong>', $usub_current, $rsub_current)),

			'ALS_MSG' => ($rals_max > 0)
				? tr('%1$d / %2$d <br/>of<br/> <strong>%3$d</strong>', $uals_current, $rals_current, $rals_max)
				: (($rals_max === "-1") ? tr('<strong>disabled</strong>') : tr('%1$d / %2$d <br/>of<br/> <strong>unlimited</strong>', $uals_current, $rals_current)),

			'MAIL_MSG' => ($rmail_max > 0)
				? tr('%1$d / %2$d <br/>of<br/> <strong>%3$d</strong>', $umail_current, $rmail_current, $rmail_max)
				: (($rmail_max === "-1") ? tr('<strong>disabled</strong>') : tr('%1$d / %2$d <br/>of<br/> <strong>unlimited</strong>', $umail_current, $rmail_current)),

			'FTP_MSG' => ($rftp_max > 0)
				? tr('%1$d / %2$d <br/>of<br/> <strong>%3$d</strong>', $uftp_current, $rftp_current, $rftp_max)
				: (($rftp_max === "-1") ? tr('<strong>disabled</strong>') : tr('%1$d / %2$d <br/>of<br/> <strong>unlimited</strong>', $uftp_current, $rftp_current)),

			'SQL_DB_MSG' => ($rsql_db_max > 0)
				? tr('%1$d / %2$d <br/>of<br/> <strong>%3$d</strong>', $usql_db_current, $rsql_db_current, $rsql_db_max)
				: (($rsql_db_max === "-1") ? tr('<strong>disabled</strong>') : tr('%1$d / %2$d <br/>of<br/> <strong>unlimited</strong>', $usql_db_current, $rsql_db_current)),

			'SQL_USER_MSG' => ($rsql_user_max > 0)
				? tr('%1$d / %2$d <br/>of<br/> <strong>%3$d</strong>', $usql_user_current, $rsql_user_current, $rsql_user_max)
				: (($rsql_user_max === "-1") ? tr('<strong>disabled</strong>') : tr('%1$d / %2$d <br/>of<br/> <strong>unlimited</strong>', $usql_user_current, $rsql_user_current))
		)
	);

}
?>