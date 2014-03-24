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
$template = 'admin/settings_server_traffic.tpl';

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'					=> tr('EasySCP - Admin/Server Traffic Settings'),
		'TR_MODIFY'						=> tr('Modify'),
		'TR_SERVER_TRAFFIC_SETTINGS'	=> tr('Server traffic settings'),
		'TR_SET_SERVER_TRAFFIC_SETTINGS'=> tr('Set server traffic (0 for unlimited)'),
		'TR_MAX_TRAFFIC'				=> tr('Max traffic [MB]'),
		'TR_WARNING'					=> tr('Warning traffic [MB]'),
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_settings.tpl');
gen_admin_menu($tpl, 'admin/menu_settings.tpl');

update_server_settings($sql);

generate_server_data($tpl, $sql);

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

function update_server_settings($sql) {

	if (!isset($_POST['uaction']) && !isset($_POST['uaction'])) {
		return;
	}

	$max_traffic = clean_input($_POST['max_traffic']);

	$traffic_warning = $_POST['traffic_warning'];

	if (!is_numeric($max_traffic) || !is_numeric($traffic_warning)) {
		set_page_message(tr('Wrong data input!'), 'warning');
	}

	if ($traffic_warning > $max_traffic) {
		set_page_message(
			tr('Warning traffic is bigger than max traffic!'),
			'warning'
		);
		return;
	}

	if ($max_traffic < 0) {
		$max_traffic = 0;
	}
	if ($traffic_warning < 0) {
		$traffic_warning = 0;
	}

	$query = "
		UPDATE
			`straff_settings`
		SET
			`straff_max` = ?,
			`straff_warn` = ?
	";
	exec_query($sql, $query, array($max_traffic, $traffic_warning));

	set_page_message(
		tr('Server traffic settings updated successfully!'),
		'success'
	);
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 */
function generate_server_data($tpl, $sql) {

	$query = "
		SELECT
			`straff_max`,
			`straff_warn`
		FROM
			`straff_settings`
	";

	$rs = exec_query($sql, $query);

	$tpl->assign(
		array(
			'MAX_TRAFFIC' => $rs->fields['straff_max'],
			'TRAFFIC_WARNING' => $rs->fields['straff_warn'],
		)
	);
}
?>