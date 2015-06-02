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

$cfg = EasySCP_Registry::get('Config');
$cfg2 = EasySCP_Configuration::getInstance();

$cfg2->PREVENT_EXTERNAL_LOGIN_ADMIN = 'blablub';
$cfg2->DUMP_GUI_DEBUG = 'blablub';

check_login(__FILE__);

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'admin/index.tpl';

get_admin_general_info($tpl);

get_update_infos($tpl);

gen_server_trafic($tpl);

gen_system_message($tpl);

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('EasySCP - Admin/Main Index')
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_general_information.tpl');
gen_admin_menu($tpl, 'admin/menu_general_information.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/**
 * @param EasySCP_TemplateEngine $tpl
 *
 * @return void
 */
function gen_system_message($tpl) {

	$sql_param = array(
		':user_id'	=> $_SESSION['user_id']
	);

	$sql_query = "
		SELECT
			COUNT(ticket_id) AS cnum
		FROM
			tickets
		WHERE
			ticket_to = :user_id
		AND
			ticket_status IN ('1', '4')
		AND
			ticket_reply = 0;
	";

	DB::prepare($sql_query);
	$row = DB::execute($sql_param, true);

	$num_question = (isset($row['cnum'])) ? $row['cnum'] : 0;

	if ($num_question != 0) {
		$tpl->assign(
			array(
				'TR_NEW_MSGS'	=> tr('You have <strong>%d</strong> new support questions', $num_question),
				'NEW_MSG_TYPE'	=> 'info'
			)
		);
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @return void
 */
function get_update_infos($tpl) {

	$cfg = EasySCP_Registry::get('Config');

	if (EasySCP_Update_Database::getInstance()->checkUpdateExists()) {
		$tpl->assign(
			array(
				'DATABASE_UPDATE'	=> '<a href="easyscp_updates.php" class="link">' . tr('A database update is available') . '</a>',
				'DATABASE_MSG_TYPE'	=> 'info'
			)
		);
	}

	if (!$cfg->CHECK_FOR_UPDATES) {
		$tpl->assign(
			array(
				'UPDATE'		=> tr('Update checking is disabled!'),
				'UPDATE_TYPE'	=> 'info'
			)
		);
		return false;
	}

	if (EasyUpdate::checkUpdate()) {
		$tpl->assign(
			array(
				'UPDATE'		=> '<a href="easyscp_updates.php" class="link">' . tr('New EasySCP update is now available') . '</a>',
				'UPDATE_TYPE'	=> 'info'
			)
		);
	} else {
		if (EasySCP_Update_Version::getInstance()->getErrorMessage() != "") {
			$tpl->assign(
				array(
					'UPDATE'		=> EasySCP_Update_Version::getInstance()->getErrorMessage(),
					'UPDATE_TYPE'	=> 'error'
				)
			);
		}
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @return void
 */
function gen_server_trafic($tpl) {

	$sql_query = "
		SELECT
			straff_max, straff_warn
		FROM
			straff_settings;
	";

	$row = DB::query($sql_query, true);

	$straff_max  = $row['straff_max'] * 1024 * 1024;
	$straff_warn = $row['straff_warn'] * 1024 * 1024;

	$sql_param = array(
		':fdofmnth'	=> mktime(0, 0, 0, date("m"), 1, date("Y")),
		':ldofmnth'	=> mktime(1, 0, 0, date("m") + 1, 0, date("Y"))
	);

	$query = "
		SELECT
			IFNULL((SUM(bytes_in) + SUM(bytes_out)), 0) AS traffic
		FROM
			server_traffic
		WHERE
			traff_time > :fdofmnth
		AND
			traff_time < :ldofmnth;
	";

	DB::prepare($sql_query);
	$row = DB::execute($sql_param, true);

	$traff = (isset($row['traffic'])) ? $row['traffic'] : 0;

	$mtraff = sprintf("%.2f", $traff);

	if ($straff_max == 0) {
		$pr = 0;
	} else {
		$pr = ($traff / $straff_max) * 100;
	}

	if (($straff_max != 0 || $straff_max != '') && ($mtraff > $straff_max)) {
		$tpl->assign(
			array(
				'TR_TRAFFIC_WARNING' => tr('You are exceeding your traffic limit!')
			)
		);
	} else if(($straff_warn != 0 || $straff_warn != '') && ($mtraff > $straff_warn)) {
		$tpl->assign(
			array(
				'TR_TRAFFIC_WARNING' => tr('You traffic limit will be reached soon!')
			)
		);
	} else {
		$tpl->assign('TRAFF_WARN', '');
	}

	$bar_value = calc_bar_value($traff, $straff_max, 400);

	$percent = 0;
	if ($straff_max == 0) {
		$traff_msg = tr('%1$d%% [%2$s of unlimited]', $pr, sizeit($mtraff));
	} else {
		$traff_msg = tr('%1$d%% [%2$s of %3$s]', $pr, sizeit($mtraff), sizeit($straff_max));
		$percent = (($traff/$straff_max)*100 < 99.7) ? ($traff/$straff_max)*100 : 99.7;
	}

	$tpl->assign(
		array(
			'TRAFFIC_WARNING'	=> $traff_msg,
			'BAR_VALUE'			=> $bar_value,
			'TRAFFIC_PERCENT'	=> $percent
		)
	);
}
?>