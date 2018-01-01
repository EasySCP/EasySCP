<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2018 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'client/backup.tpl';

// dynamic page data.

send_backup_restore_request($sql, $_SESSION['user_id']);

// static page messages.
gen_logged_from($tpl);
check_permissions($tpl);

if ($cfg->ZIP == "gzip") {
	$name = "backup_YYYY_MM_DD.tar.gz";
} else if ($cfg->ZIP == "bzip2") {
	$name = "backup_YYYY_MM_DD.tar.bz2";
} else { // Config::getInstance()->get('ZIP') == "lzma"
	$name = "backup_YYYY_MM_DD.tar.lzma";
}

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Client/Daily Backup'),
		'TR_BACKUP'				=> tr('Backup'),
		'TR_DAILY_BACKUP'		=> tr('Daily backup'),
		'TR_DOWNLOAD_DIRECTION'	=> tr("Instructions to download today's backup"),
		'TR_FTP_LOG_ON'			=> tr('Login with your FTP account'),
		'TR_SWITCH_TO_BACKUP'	=> tr('Switch to backups/ directory'),
		'TR_DOWNLOAD_FILE'		=> tr('Download the files stored in this directory'),
		'TR_USUALY_NAMED'		=> tr('(usually named') . ' ' . $name . ')',
		'TR_RESTORE_BACKUP'		=> tr('Restore backup'),
		'TR_RESTORE_DIRECTIONS'	=> tr('Click the Restore button and the system will restore the last daily backup'),
		'TR_RESTORE'			=> tr('Restore'),
		'TR_CONFIRM_MESSAGE'	=> tr('Are you sure you want to restore the backup?')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_webtools.tpl');
gen_client_menu($tpl, 'client/menu_webtools.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// page functions.

function send_backup_restore_request($sql, $user_id) {
	if (isset($_POST['uaction']) && $_POST['uaction'] === 'bk_restore') {

		$query = "
			UPDATE
				`domain`
			SET
				`status` = 'restore'
			WHERE
				`domain_admin_id` = ?
		";

		exec_query($sql, $query, $user_id);

		send_request();
		write_log($_SESSION['user_logged'] . ": restore backup files.");
		set_page_message(
			tr('Backup archive scheduled for restoring!'),
			'success'
		);
	}
}
?>