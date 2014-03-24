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
$template = 'admin/easyscp_updates.tpl';

$dbUpdate = EasySCP_Update_Database::getInstance();

if(isset($_POST['execute']) && $_POST['execute'] == 'update') {

	// Execute all available db updates and redirect back to easyscp_updates.php

	if(!$dbUpdate->executeUpdates()) {
		throw new EasySCP_Exception($dbUpdate->getErrorMessage());
	}

	header('Location: ' . $_SERVER['PHP_SELF']);
}

// Processing updates. Please wait. Page will reload after updates has been processed.

if(isset($_POST['execute_migration']) && $_POST['execute_migration'] == 'migrate') {

	$tpl->assign(
		array(
			'MIGRATION_ENABLED'		=> true
		)
	);

	// Backup the current Databse
	backupCurrentDatabase();

	// Import the old ispCP Database
	importOldDatabase();

	// Convert old Data
	convertOldData();

	// Finish the Migration process and log out
	finishMigration();

	$tpl->assign(
		array(
			'MIGRATION_MESSAGE'	=> tr('Migration Successful'),
			'MIGRATION_MSG_TYPE'=> 'success'
		)
	);
	// header('Location: ' . $_SERVER['PHP_SELF']);
} else {
	get_migration_infos($tpl);
}

get_update_infos($tpl);
get_db_update_infos($tpl,$dbUpdate);

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Virtual Hosting Control System'),
		'TR_UPDATES_TITLE'			=> tr('EasySCP updates'),
		'TR_AVAILABLE_UPDATES'		=> tr('Available EasySCP updates'),
		'TR_DB_UPDATES_TITLE'		=> tr('Database updates'),
		'TR_DB_AVAILABLE_UPDATES'	=> tr('Available database updates'),
		'TR_UPDATE'					=> tr('Update'),
		'TR_INFOS'					=> tr('Update details'),
		'TR_MIGRATION_TITLE'		=> tr('Data Migration/Import from other Panels'),

	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_system_tools.tpl');
gen_admin_menu($tpl, 'admin/menu_system_tools.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

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
				'DB_INFOS'			=> tr('Do you want to execute the Updates now?'),
				'TR_EXECUTE_UPDATE'	=> tr('Execute updates')
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

/**
 * @param EasySCP_TemplateEngine $tpl
 * @return void
 */
function get_migration_infos($tpl) {
	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	// if ($cfg->MIGRATION_ENABLED) {
	if (isset($cfg->MIGRATION_ENABLED) && $cfg->MIGRATION_ENABLED) {
		$tpl->assign(
			array(
				'MIGRATION_ENABLED'		=> true
			)
		);

		$oldDatabase = false;
		$rs = exec_query($sql, 'show databases;');

		while(!$rs->EOF) {
			if ($rs->fields['Database'] == 'ispcp'){
				$oldDatabase = true;
			}
			$rs->moveNext();
		}

		if (!$oldDatabase) {
			$tpl->assign(
				array(
					'MIGRATION_MESSAGE'	=> tr('Error: Unable to found the ispCP Database!'),
					'MIGRATION_MSG_TYPE'=> 'error'
				)
			);
			return;
		}

		$oldConfig = array();
		$oldConfigFile = '/etc/ispcp/ispcp.conf';

		if (!file_exists($oldConfigFile)) {
			$tpl->assign(
				array(
					'MIGRATION_MESSAGE'	=> tr('Error: Unable to open the configuration file `%1$s`!', $oldConfigFile),
					'MIGRATION_MSG_TYPE'=> 'error'
				)
			);
			return;
		} else {
			$fd = @file_get_contents($oldConfigFile);

			$lines = explode(PHP_EOL, $fd);

			foreach ($lines as $line) {
				if (!empty($line) && $line[0] != '#' && strpos($line, '=')) {
					list($key, $value) = explode('=', $line, 2);

					$oldConfig[trim($key)] = trim($value);
				}
			}
		}

		switch($oldConfig['Version']){
			case '1.0.7 OMEGA':
				$oldDBKeysFile = '/var/www/ispcp/gui/include/ispcp-db-keys.php';
				break;
			default:
				$oldDBKeysFile = '/etc/ispcp/ispcp-keys.conf';
		}

		if (!file_exists($oldDBKeysFile)) {
			$tpl->assign(
				array(
					'MIGRATION_MESSAGE'	=> "Error: Unable to open the db key file `{$oldDBKeysFile}`!",
					'MIGRATION_MSG_TYPE'=> 'error'
				)
			);
			return;
		}

		$tpl->assign(
			array(
				'MIGRATION_AVAILABLE'	=> true,
				'TR_MIGRATION_AVAILABLE'=> tr('Available migrations'),
				'TR_EXECUTE_MIGRATION'	=> tr('Execute migration'),
				'TR_MIGRATION_INFOS'	=> 'Migration from ispCP '.$oldConfig['Version'].' is available',
				'MIGRATION_VERSION'		=> $oldConfig['Version'],
				'MIGRATION_INFOS'		=> 'All existing data will be removed!'
			)
		);
	}
}

/**
 *
 */
function backupCurrentDatabase() {
	$sql = EasySCP_Registry::get('Db');

	$query = "
		CREATE DATABASE `easyscp_org` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
		RENAME TABLE `easyscp`.`admin` TO `easyscp_org`.`admin`;
		RENAME TABLE `easyscp`.`autoreplies_log` TO `easyscp_org`.`autoreplies_log`;
		RENAME TABLE `easyscp`.`auto_num` TO `easyscp_org`.`auto_num`;
		RENAME TABLE `easyscp`.`config` TO `easyscp_org`.`config`;
		RENAME TABLE `easyscp`.`custom_menus` TO `easyscp_org`.`custom_menus`;
		RENAME TABLE `easyscp`.`domain` TO `easyscp_org`.`domain`;
		RENAME TABLE `easyscp`.`domain_aliasses` TO `easyscp_org`.`domain_aliasses`;
		RENAME TABLE `easyscp`.`domain_dns` TO `easyscp_org`.`domain_dns`;
		RENAME TABLE `easyscp`.`domain_traffic` TO `easyscp_org`.`domain_traffic`;
		RENAME TABLE `easyscp`.`email_tpls` TO `easyscp_org`.`email_tpls`;
		RENAME TABLE `easyscp`.`error_pages` TO `easyscp_org`.`error_pages`;
		RENAME TABLE `easyscp`.`ftp_group` TO `easyscp_org`.`ftp_group`;
		RENAME TABLE `easyscp`.`ftp_users` TO `easyscp_org`.`ftp_users`;
		RENAME TABLE `easyscp`.`hosting_plans` TO `easyscp_org`.`hosting_plans`;
		RENAME TABLE `easyscp`.`htaccess` TO `easyscp_org`.`htaccess`;
		RENAME TABLE `easyscp`.`htaccess_groups` TO `easyscp_org`.`htaccess_groups`;
		RENAME TABLE `easyscp`.`htaccess_users` TO `easyscp_org`.`htaccess_users`;
		RENAME TABLE `easyscp`.`log` TO `easyscp_org`.`log`;
		RENAME TABLE `easyscp`.`login` TO `easyscp_org`.`login`;
		RENAME TABLE `easyscp`.`mail_users` TO `easyscp_org`.`mail_users`;
		RENAME TABLE `easyscp`.`orders` TO `easyscp_org`.`orders`;
		RENAME TABLE `easyscp`.`orders_settings` TO `easyscp_org`.`orders_settings`;
		RENAME TABLE `easyscp`.`quotalimits` TO `easyscp_org`.`quotalimits`;
		RENAME TABLE `easyscp`.`quotatallies` TO `easyscp_org`.`quotatallies`;
		RENAME TABLE `easyscp`.`reseller_props` TO `easyscp_org`.`reseller_props`;
		RENAME TABLE `easyscp`.`server_ips` TO `easyscp_org`.`server_ips`;
		RENAME TABLE `easyscp`.`server_traffic` TO `easyscp_org`.`server_traffic`;
		RENAME TABLE `easyscp`.`sql_database` TO `easyscp_org`.`sql_database`;
		RENAME TABLE `easyscp`.`sql_user` TO `easyscp_org`.`sql_user`;
		RENAME TABLE `easyscp`.`straff_settings` TO `easyscp_org`.`straff_settings`;
		RENAME TABLE `easyscp`.`subdomain` TO `easyscp_org`.`subdomain`;
		RENAME TABLE `easyscp`.`subdomain_alias` TO `easyscp_org`.`subdomain_alias`;
		RENAME TABLE `easyscp`.`tickets` TO `easyscp_org`.`tickets`;
		RENAME TABLE `easyscp`.`user_gui_props` TO `easyscp_org`.`user_gui_props`;
		DROP DATABASE `easyscp`;
	";

	exec_query($sql, $query);
}
/**
 *
 */
function importOldDatabase() {
	$sql = EasySCP_Registry::get('Db');

	$query = "
		CREATE DATABASE `easyscp` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
		RENAME TABLE `ispcp`.`admin` TO `easyscp`.`admin`;
		RENAME TABLE `ispcp`.`autoreplies_log` TO `easyscp`.`autoreplies_log`;
		RENAME TABLE `ispcp`.`auto_num` TO `easyscp`.`auto_num`;
		RENAME TABLE `ispcp`.`config` TO `easyscp`.`config`;
		RENAME TABLE `ispcp`.`custom_menus` TO `easyscp`.`custom_menus`;
		RENAME TABLE `ispcp`.`domain` TO `easyscp`.`domain`;
		RENAME TABLE `ispcp`.`domain_aliasses` TO `easyscp`.`domain_aliasses`;
		RENAME TABLE `ispcp`.`domain_dns` TO `easyscp`.`domain_dns`;
		RENAME TABLE `ispcp`.`domain_traffic` TO `easyscp`.`domain_traffic`;
		RENAME TABLE `ispcp`.`email_tpls` TO `easyscp`.`email_tpls`;
		RENAME TABLE `ispcp`.`error_pages` TO `easyscp`.`error_pages`;
		RENAME TABLE `ispcp`.`ftp_group` TO `easyscp`.`ftp_group`;
		RENAME TABLE `ispcp`.`ftp_users` TO `easyscp`.`ftp_users`;
		RENAME TABLE `ispcp`.`hosting_plans` TO `easyscp`.`hosting_plans`;
		RENAME TABLE `ispcp`.`htaccess` TO `easyscp`.`htaccess`;
		RENAME TABLE `ispcp`.`htaccess_groups` TO `easyscp`.`htaccess_groups`;
		RENAME TABLE `ispcp`.`htaccess_users` TO `easyscp`.`htaccess_users`;
		RENAME TABLE `ispcp`.`log` TO `easyscp`.`log`;
		RENAME TABLE `ispcp`.`login` TO `easyscp`.`login`;
		RENAME TABLE `ispcp`.`mail_users` TO `easyscp`.`mail_users`;
		RENAME TABLE `ispcp`.`orders` TO `easyscp`.`orders`;
		RENAME TABLE `ispcp`.`orders_settings` TO `easyscp`.`orders_settings`;
		RENAME TABLE `ispcp`.`quotalimits` TO `easyscp`.`quotalimits`;
		RENAME TABLE `ispcp`.`quotatallies` TO `easyscp`.`quotatallies`;
		RENAME TABLE `ispcp`.`reseller_props` TO `easyscp`.`reseller_props`;
		RENAME TABLE `ispcp`.`server_ips` TO `easyscp`.`server_ips`;
		RENAME TABLE `ispcp`.`server_traffic` TO `easyscp`.`server_traffic`;
		RENAME TABLE `ispcp`.`sql_database` TO `easyscp`.`sql_database`;
		RENAME TABLE `ispcp`.`sql_user` TO `easyscp`.`sql_user`;
		RENAME TABLE `ispcp`.`straff_settings` TO `easyscp`.`straff_settings`;
		RENAME TABLE `ispcp`.`subdomain` TO `easyscp`.`subdomain`;
		RENAME TABLE `ispcp`.`subdomain_alias` TO `easyscp`.`subdomain_alias`;
		RENAME TABLE `ispcp`.`suexec_props` TO `easyscp`.`suexec_props`;
		RENAME TABLE `ispcp`.`tickets` TO `easyscp`.`tickets`;
		RENAME TABLE `ispcp`.`user_gui_props` TO `easyscp`.`user_gui_props`;
		DROP DATABASE `ispcp`;
	";

	exec_query($sql, $query);
}
/**
 *
 */
function convertOldData() {
	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');
	$ispcp_db_pass_key = '';
	$ispcp_db_pass_iv = '';

	switch($_POST['migration_version']){
		case '1.0.7 OMEGA':
			$oldDBKeysFile = '/var/www/ispcp/gui/include/ispcp-db-keys.php';
			require_once($oldDBKeysFile);
			break;
		default:
			$oldDBKeysFile = '/etc/ispcp/ispcp-keys.conf';
			$lines = file($oldDBKeysFile);
			foreach ($lines as $line) {
				$pos = strpos($line, '=');
				if ($pos > 0) {
					$key = trim(substr($line, 0, $pos));
					$value = trim(substr($line, $pos + 1));

					if ($key == 'DB_PASS_KEY') {
						$ispcp_db_pass_key = $value;
					} elseif ($key == 'DB_PASS_IV') {
						$ispcp_db_pass_iv = $value;
					}
				}
			}
	}

	$td = @mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
	$key = $ispcp_db_pass_key;
	$iv = $ispcp_db_pass_iv;
	$data = array();
	$temp = '';

	$query = "
		use easyscp;
	";

	exec_query($sql, $query);

	$query = "
		SELECT mail_id, mail_pass FROM `mail_users`;
	";

	$rs = exec_query($sql, $query);

	while(!$rs->EOF) {
		if ($rs->fields['mail_pass'] != '_no_'){
			// Initialize encryption
			@mcrypt_generic_init($td, $key, $iv);

			$text = @base64_decode($rs->fields['mail_pass'] . "\n");

			// Decrypt encrypted string
			$temp = $rs->fields['mail_id'];
			$data[$temp] = trim(@mdecrypt_generic($td, $text));

			@mcrypt_generic_deinit($td);
		}
		$rs->moveNext();
	}

	// Close encryption
	@mcrypt_module_close($td);

	foreach ($data as $mail_id => $mail_pass){
		$pass = encrypt_db_password(trim($mail_pass));
		$status = $cfg->ITEM_CHANGE_STATUS;
		$query = "UPDATE `mail_users` SET `mail_pass` = ?, `status` = ? WHERE `mail_id` = ?";
		exec_query($sql, $query, array($pass, $status, $mail_id));
	}
}

/**
 *
 */
function finishMigration() {
	$cfg = EasySCP_Registry::get('Config');

	DB::setDatabase();

	$sql_query = "
		UPDATE domain SET status = '$cfg->ITEM_ADD_STATUS';
		UPDATE domain_aliasses SET status = '$cfg->ITEM_ADD_STATUS';
		UPDATE mail_users SET status = '$cfg->ITEM_ADD_STATUS';
		UPDATE htaccess SET status = '$cfg->ITEM_ADD_STATUS';
		UPDATE htaccess_groups SET status = '$cfg->ITEM_ADD_STATUS';
		UPDATE htaccess_users SET status = '$cfg->ITEM_ADD_STATUS';
		UPDATE subdomain SET status = '$cfg->ITEM_ADD_STATUS';
		UPDATE subdomain_alias SET status = '$cfg->ITEM_ADD_STATUS';
		UPDATE user_gui_props SET lang = '', layout = '';
	";

	DB::query($sql_query)->closeCursor();

	send_request();
}
?>