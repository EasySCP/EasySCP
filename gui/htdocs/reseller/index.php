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

$cfg = EasySCP_Registry::get('Config');

check_login(__FILE__);

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'reseller/index.tpl';

// dynamic page data
generate_page_data($tpl, $_SESSION['user_id'], $_SESSION['user_logged']);

gen_messages_table($tpl, $_SESSION['user_id']);

gen_system_message($tpl);

// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP - Reseller/Main Index'),
		'TR_TRAFFIC_USAGE'	=> tr('Traffic usage'),
		'TR_DISK_USAGE'		=> tr ('Disk usage')
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_general_information.tpl');
gen_reseller_menu($tpl, 'reseller/menu_general_information.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// page functions

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
			(`ticket_status` IN ('1', '4')
			AND
			`ticket_level` = 1) OR
			(`ticket_status` IN ('2')
			AND
			`ticket_level` = 2)
		AND
			`ticket_reply` = 0
	;";

	$rs = exec_query($sql, $query, array($user_id, $user_id));

	$num_question = $rs->fields('cnum');

	if ($num_question == 0) {
		$tpl->assign(array('MSG_ENTRY' => ''));
	} else {
		$tpl->assign(
			array(
				'TR_NEW_MSGS'	=> tr('You have <strong>%d</strong> new support questions', $num_question),
				'NEW_MSG_TYPE'	=> 'info',
				'TR_VIEW'		=> tr('View')
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
	if (0 !== $max_usage) {
		list($percent, $bars) = calc_bars($usage, $max_usage, $bars_max);
		$traffic_usage_data = tr('%1$s%% [%2$s of %3$s]', $percent, sizeit($usage), sizeit($max_usage));
	} else {
		$percent = 0;
		$bars = 0;
		$traffic_usage_data = tr('%1$s%% [%2$s of unlimited]', $percent, sizeit($usage), sizeit($max_usage));
	}

	$tpl->assign(
		array(
			'TRAFFIC_USAGE_DATA' => $traffic_usage_data,
			'TRAFFIC_BARS'       => $bars,
			'TRAFFIC_PERCENT'    => $percent,
		)
	);
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param float $usage
 * @param float $max_usage
 * @param float $bars_max
 */
function gen_disk_usage($tpl, $usage, $max_usage, $bars_max) {
	if (0 !== $max_usage) {
		list($percent, $bars) = calc_bars($usage, $max_usage, $bars_max);
		$traffic_usage_data = tr('%1$s%% [%2$s of %3$s]', $percent, sizeit($usage), sizeit($max_usage));
	} else {
		$percent = 0;
		$bars = 0;
		$traffic_usage_data = tr('%1$s%% [%2$s of unlimited]', $percent, sizeit($usage));
	}

	$tpl->assign(
		array(
			'DISK_USAGE_DATA' => $traffic_usage_data,
			'DISK_BARS'       => $bars,
			'DISK_PERCENT'    => $percent,
		)
	);
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $reseller_id
 * @param string $reseller_name
 */
function generate_page_data($tpl, $reseller_id, $reseller_name) {
	global $crnt_month, $crnt_year;

	$sql = EasySCP_Registry::get('Db');

	$crnt_month = date("m");
	$crnt_year = date("Y");
	// global
	$tmpArr = get_reseller_default_props($sql, $reseller_id);
	if ($tmpArr != NULL) { // there are data in db
		list($rdmn_current, $rdmn_max,
			$rsub_current, $rsub_max,
			$rals_current, $rals_max,
			$rmail_current, $rmail_max,
			$rftp_current, $rftp_max,
			$rsql_db_current, $rsql_db_max,
			$rsql_user_current, $rsql_user_max,
			$rtraff_current, $rtraff_max,
			$rdisk_current, $rdisk_max
		) = $tmpArr;
	} else {
		list($rdmn_current, $rdmn_max,
			$rsub_current, $rsub_max,
			$rals_current, $rals_max,
			$rmail_current, $rmail_max,
			$rftp_current, $rftp_max,
			$rsql_db_current, $rsql_db_max,
			$rsql_user_current, $rsql_user_max,
			$rtraff_current, $rtraff_max,
			$rdisk_current, $rdisk_max
		) = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	}

	list($udmn_current,,,$usub_current,,,$uals_current,,,$umail_current,,,
		$uftp_current,,,$usql_db_current,,,$usql_user_current,,,$utraff_current,
		,,$udisk_current
	) = generate_reseller_user_props($reseller_id);

	// Convert into MB values
	$rtraff_max = $rtraff_max * 1024 * 1024;
	$rtraff_current = $rtraff_current * 1024 * 1024;
	$rdisk_max = $rdisk_max * 1024 * 1024;
	$rdisk_current = $rdisk_current * 1024 * 1024;

	if ($rtraff_max != 0) {
		$traff_percent = sprintf("%.2f", 100 * $utraff_current / $rtraff_max);
	} else {
		$traff_percent = 0;
	}

	gen_traff_usage($tpl, $utraff_current, $rtraff_max, 400);

	gen_disk_usage($tpl, $udisk_current, $rdisk_max, 400);

	if ($rtraff_max > 0) {
		if ($utraff_current > $rtraff_max) {
			$tpl->assign('TR_TRAFFIC_WARNING', tr('You are exceeding your traffic limit!'));
		}
	}
	
	// warning HDD Usage
	if ($rdisk_max > 0) {
		if ($udisk_current > $rdisk_max) {
			$tpl->assign('TR_DISK_WARNING', tr('You are exceeding your disk limit!'));
		}
	}

	$tpl->assign(
		array(
			"ACCOUNT_NAME"		=> tr("Account name"),
			"GENERAL_INFO"		=> tr("General information"),
			"DOMAINS"			=> tr("User accounts"),
			"SUBDOMAINS"		=> tr("Subdomains"),
			"ALIASES"			=> tr("Aliases"),
			"MAIL_ACCOUNTS"		=> tr("Mail account"),
			"TR_FTP_ACCOUNTS"	=> tr("FTP account"),
			"SQL_DATABASES"		=> tr("SQL databases"),
			"SQL_USERS"			=> tr("SQL users"),
			"TRAFFIC"			=> tr("Traffic"),
			"DISK"				=> tr("Disk"),
			"TR_EXTRAS"			=> tr("Extras")
		)
	);

	$tpl->assign(
		array(
			'RESELLER_NAME' => tohtml($reseller_name),
			'TRAFF_PERCENT' => $traff_percent,
			'TRAFF_MSG' => ($rtraff_max)
				? tr('%1$s used / %2$s assigned of <strong>%3$s</strong>', sizeit($utraff_current), sizeit($rtraff_current), sizeit($rtraff_max))
				: tr('%1$s used / %2$s assigned of <strong>unlimited</strong>', sizeit($utraff_current), sizeit($rtraff_current)),
			'DISK_MSG' => ($rdisk_max)
				? tr('%1$s used / %2$s assigned of <strong>%3$s</strong>', sizeit($udisk_current), sizeit($rdisk_current), sizeit($rdisk_max))
				: tr('%1$s used / %2$s assigned of <strong>unlimited</strong>', sizeit($udisk_current), sizeit($rdisk_current)),
			'DMN_MSG' => ($rdmn_max)
				? tr('%1$d used / %2$d assigned of <strong>%3$d</strong>', $udmn_current, $rdmn_current, $rdmn_max)
				: tr('%1$d used / %2$d assigned of <strong>unlimited</strong>', $udmn_current, $rdmn_current),
			'SUB_MSG' => ($rsub_max > 0)
				? tr('%1$d used / %2$d assigned of <strong>%3$d</strong>', $usub_current, $rsub_current, $rsub_max)
				: (($rsub_max === "-1") ? tr('<strong>disabled</strong>') : tr('%1$d used / %2$d assigned of <strong>unlimited</strong>', $usub_current, $rsub_current)),
			'ALS_MSG' => ($rals_max > 0)
				? tr('%1$d used / %2$d assigned of <strong>%3$d</strong>', $uals_current, $rals_current, $rals_max)
				: (($rals_max === "-1") ? tr('<strong>disabled</strong>') : tr('%1$d used / %2$d assigned of <strong>unlimited</strong>', $uals_current, $rals_current)),
			'MAIL_MSG' => ($rmail_max > 0)
				? tr('%1$d used / %2$d assigned of <strong>%3$d</strong>', $umail_current, $rmail_current, $rmail_max)
				: (($rmail_max === "-1") ? tr('<strong>disabled</strong>') : tr('%1$d used / %2$d assigned of <strong>unlimited</strong>', $umail_current, $rmail_current)),
			'FTP_MSG' => ($rftp_max > 0)
				? tr('%1$d used / %2$d assigned of <strong>%3$d</strong>', $uftp_current, $rftp_current, $rftp_max)
				: (($rftp_max === "-1") ? tr('<strong>disabled</strong>') : tr('%1$d used / %2$d assigned of <strong>unlimited</strong>', $uftp_current, $rftp_current)),
			'SQL_DB_MSG' => ($rsql_db_max > 0)
				? tr('%1$d used / %2$d assigned of <strong>%3$d</strong>', $usql_db_current, $rsql_db_current, $rsql_db_max)
				: (($rsql_db_max === "-1") ? tr('<strong>disabled</strong>') : tr('%1$d used / %2$d assigned of <strong>unlimited</strong>', $usql_db_current, $rsql_db_current)),
			'SQL_USER_MSG' => ($rsql_user_max > 0)
				? tr('%1$d used / %2$d assigned of <strong>%3$d</strong>', $usql_user_current, $rsql_user_current, $rsql_user_max)
				: (($rsql_user_max === "-1") ? tr('<strong>disabled</strong>') : tr('%1$d used / %2$d assigned of <strong>unlimited</strong>', $usql_user_current, $rsql_user_current)),
			'EXTRAS' => ''
		)
	);
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $admin_id
 */
function gen_messages_table($tpl, $admin_id) {
	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			`ticket_id`
		FROM
			`tickets`
		WHERE
			(`ticket_from` = ? OR `ticket_to` = ?)
		AND
			`ticket_status` IN ('1', '4')
		AND
			`ticket_reply` = '0'
	;";
	$res = exec_query($sql, $query, array($admin_id, $admin_id));

	$questions = $res->rowCount();

	if ($questions == 0) {
		$tpl->assign(
			array(
				'TR_NO_NEW_MESSAGES' => tr('You have no new support questions!'),
				'MSG_ENTRY' => ''
			)
		);
	} else {
		$tpl->assign(
			array(
				'TR_NEW_MSGS'	=> tr('You have <strong>%d</strong> new support questions', $questions),
				'NO_MESSAGES'	=> '',
				'TR_VIEW'		=> tr('View')
			)
		);

	}
}
?>