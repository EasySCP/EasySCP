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
$template = 'admin/easyscp_updates.tpl';

if (isset($_POST['execute_iana_update'])){
	if (send_request('160 SYSTEM updateIana')){
		set_page_message(tr('Successfully updated IANA TLD list!'), 'success');
	} else {
		set_page_message(tr('Update of IANA TLD list failed!'), 'warning');
	}
}

$dbUpdate = EasySCP_Update_Database::getInstance();

if(isset($_POST['execute']) && $_POST['execute'] == 'update') {

	// Execute all available db updates and redirect back to easyscp_updates.php

	if(!$dbUpdate->executeUpdates()) {
		throw new EasySCP_Exception($dbUpdate->getErrorMessage());
	}

	header('Location: ' . $_SERVER['PHP_SELF']);
}

// Processing updates. Please wait. Page will reload after updates has been processed.

get_update_infos($tpl);
get_db_update_infos($tpl,$dbUpdate);

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Virtual Hosting Control System'),
		'TR_UPDATES_TITLE'			=> tr('EasySCP updates'),
		'TR_AVAILABLE_UPDATES'		=> tr('Available EasySCP updates'),
		'TR_EXECUTE_UPDATE'			=> tr('Execute updates'),
		'TR_DB_UPDATES_TITLE'		=> tr('Database updates'),
		'TR_DB_AVAILABLE_UPDATES'	=> tr('Available database updates'),
		'TR_UPDATE'					=> tr('Update'),
		'TR_INFOS'					=> tr('Update details'),
		'TR_IANA_UPDATES_TITLE'		=> tr('Update TLD list from IANA'),
		'TR_IANA_LAST_UPDATE'		=> tr('The Iana TLD database was last updated on'),
	)
);

getIanaLastUpdate($tpl);

gen_admin_mainmenu($tpl, 'admin/main_menu_system_tools.tpl');
gen_admin_menu($tpl, 'admin/menu_system_tools.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

function getIanaLastUpdate($tpl){
	$sql_query = "
	    SELECT
			*
		FROM
			config
		WHERE
			name = 'IANA_LAST_UPDATE'
	";

	$rs = DB::query($sql_query,true);
	
	$date = date( 'd. m. Y H:i:s', $rs['value'] );
	
	$tpl->assign(
		array(
			'IANA_LAST_UPDATE'	=> $date,
		)
	);
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @return void
 */
function get_update_infos($tpl) {

	$cfg = EasySCP_Registry::get('Config');

	if (!$cfg->CHECK_FOR_UPDATES) {
		$tpl->assign(
			array(
				'UPDATE_MESSAGE'	=> tr('Update checking is disabled!') .'<br />'. tr('Enable update at') . ' <a href="settings.php">' . tr('Settings') . '</a>.',
				'UPDATE_MSG_TYPE'	=> 'info'
			)
		);
	} else {
		if (EasyUpdate::checkUpdate()) {
			$tpl->assign(
				array(
					'UPDATE'=> tr('New EasySCP update is now available'),
					'INFOS'	=> tr('Get it at') . ' <a href="http://www.easyscp.net" class="link">http://www.easyscp.net</a>'
				)
			);
		} else {
			$tpl->assign(
				array(
					'UPDATE_MESSAGE'	=> tr('No new EasySCP updates available'),
					'UPDATE_MSG_TYPE'	=> 'info'
				)
			);
		}

		if (EasySCP_Update_Version::getInstance()->getErrorMessage() != "") {
			$tpl->assign(
				array(
					'UPDATE_MESSAGE'	=> EasySCP_Update_Version::getInstance()->getErrorMessage(),
					'UPDATE_MSG_TYPE'	=> 'error'
				)
			);
		}
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Update_Database $dbUpdate
 * @return void
 */
function get_db_update_infos($tpl, $dbUpdate) {

	if($dbUpdate->checkUpdateExists()) {
		$tpl->assign(
			array(
				'DB_UPDATE'			=> tr('New Database update is now available'),
				'DB_INFOS'			=> tr('Do you want to execute the Updates now?')
			)
		);
	} else {
		$tpl->assign(
			array(
				'DB_UPDATE_MESSAGE'	=> tr('No database updates available'),
				'DB_UPDATE_MSG_TYPE'=> 'info'
			)
		);

	}
}
?>