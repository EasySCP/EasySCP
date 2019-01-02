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
$template = 'admin/server_statistic_day.tpl';

global $month, $year, $day;

if (isset($_GET['month']) && isset($_GET['year']) && isset($_GET['day'])
	&& is_numeric($_GET['month']) && is_numeric($_GET['year'])
	&& is_numeric($_GET['day'])) {
	$year = $_GET['year'];
	$month = $_GET['month'];
	$day = $_GET['day'];
} else {
	user_goto('server_statistic.php');
}

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Admin/Server day stats'),
		'TR_SERVER_STATISTICS'		=> tr('Server statistics'),
		'TR_SERVER_DAY_STATISTICS'	=> tr('Server day statistics'),
		'TR_MONTH'					=> tr('Month:'),
		'TR_YEAR'					=> tr('Year:'),
		'TR_DAY'					=> tr('Day:'),
		'TR_HOUR'					=> tr('Hour'),
		'TR_WEB_IN'					=> tr('Web in'),
		'TR_WEB_OUT'				=> tr('Web out'),
		'TR_SMTP_IN'				=> tr('SMTP in'),
		'TR_SMTP_OUT'				=> tr('SMTP out'),
		'TR_POP_IN'					=> tr('POP3/IMAP in'),
		'TR_POP_OUT'				=> tr('POP3/IMAP out'),
		'TR_OTHER_IN'				=> tr('Other in'),
		'TR_OTHER_OUT'				=> tr('Other out'),
		'TR_ALL_IN'					=> tr('All in'),
		'TR_ALL_OUT'				=> tr('All out'),
		'TR_ALL'					=> tr('All'),
		'TR_BACK'					=> tr('Back'),
		'MONTH'						=> $month,
		'YEAR'						=> $year,
		'DAY'						=> $day
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_statistics.tpl');
gen_admin_menu($tpl, 'admin/menu_statistics.tpl');

gen_page_message($tpl);
generate_page ($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function generate_page($tpl) {
	$sql = EasySCP_Registry::get('Db');
	global $month, $year, $day;

	$all[0] = 0;
	$all[1] = 0;
	$all[2] = 0;
	$all[3] = 0;
	$all[4] = 0;
	$all[5] = 0;
	$all[6] = 0;
	$all[7] = 0;

	$all_other_in = 0;
	$all_other_out = 0;

	$ftm = mktime(0, 0, 0, $month, $day, $year);
	$ltm = mktime(0, 0, 0, $month, $day+1, $year);

	$query = "
		SELECT
			COUNT(`bytes_in`) AS cnt
		FROM
			`server_traffic`
		WHERE
			`traff_time` > ? AND `traff_time` <= ?
	";

	$rs = exec_query($sql, $query, array($ftm, $ltm));

	$dnum = $rs->fields['cnt'];

	$query = "
		SELECT
			`traff_time` AS ttime,
			`bytes_in` AS sbin,
			`bytes_out` AS sbout,
			`bytes_mail_in` AS smbin,
			`bytes_mail_out` AS smbout,
			`bytes_pop_in` AS spbin,
			`bytes_pop_out` AS spbout,
			`bytes_web_in` AS swbin,
			`bytes_web_out` AS swbout
		FROM
			`server_traffic`
		WHERE
			`traff_time` > ? AND `traff_time` <= ?
	";

	$rs1 = exec_query($sql, $query, array($ftm, $ltm));

	if ($dnum != 0) {
		for ($i = 0; $i < $dnum; $i++) {
			// make it in kb mb or bytes :)
			$ttime = date('H:i', $rs1->fields['ttime']);

			// make other traffic
			$other_in = $rs1->fields['sbin'] - ($rs1->fields['swbin'] + $rs1->fields['smbin'] + $rs1->fields['spbin']);
			$other_out = $rs1->fields['sbout'] - ($rs1->fields['swbout'] + $rs1->fields['smbout'] + $rs1->fields['spbout']);

			$tpl->append(
				array(
					'HOUR'		=> $ttime,
					'WEB_IN'	=> sizeit($rs1->fields['swbin']),
					'WEB_OUT'	=> sizeit($rs1->fields['swbout']),
					'SMTP_IN'	=> sizeit($rs1->fields['smbin']),
					'SMTP_OUT'	=> sizeit($rs1->fields['smbout']),
					'POP_IN'	=> sizeit($rs1->fields['spbin']),
					'POP_OUT'	=> sizeit($rs1->fields['spbout']),
					'OTHER_IN'	=> sizeit($other_in),
					'OTHER_OUT'	=> sizeit($other_out),
					'ALL_IN'	=> sizeit($rs1->fields['sbin']),
					'ALL_OUT'	=> sizeit($rs1->fields['sbout']),
					'ALL'		=> sizeit($rs1->fields['sbin'] + $rs1->fields['sbout'])
				)
			);

			$all[0] = $all[0] + $rs1->fields['swbin'];
			$all[1] = $all[1] + $rs1->fields['swbout'];
			$all[2] = $all[2] + $rs1->fields['smbin'];
			$all[3] = $all[3] + $rs1->fields['smbout'];
			$all[4] = $all[4] + $rs1->fields['spbin'];
			$all[5] = $all[5] + $rs1->fields['spbout'];
			$all[6] = $all[6] + $rs1->fields['sbin'];
			$all[7] = $all[7] + $rs1->fields['sbout'];


			$rs1->moveNext();
		} // end for
		$all_other_in = $all[6] - ($all[0] + $all[2] + $all[4]);
		$all_other_out = $all[7] - ($all[1] + $all[3] + $all[5]);
	}

	$tpl->assign(
		array(
			'WEB_IN_ALL'	=> sizeit($all[0]),
			'WEB_OUT_ALL'	=> sizeit($all[1]),
			'SMTP_IN_ALL'	=> sizeit($all[2]),
			'SMTP_OUT_ALL'	=> sizeit($all[3]),
			'POP_IN_ALL'	=> sizeit($all[4]),
			'POP_OUT_ALL'	=> sizeit($all[5]),
			'OTHER_IN_ALL'	=> sizeit($all_other_in),
			'OTHER_OUT_ALL'	=> sizeit($all_other_out),
			'ALL_IN_ALL'	=> sizeit($all[6]),
			'ALL_OUT_ALL'	=> sizeit($all[7]),
			'ALL_ALL'		=> sizeit($all[6] + $all[7])
		)
	);
}
?>