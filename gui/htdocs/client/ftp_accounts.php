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
$template = 'client/ftp_accounts.tpl';

// dynamic page data.


gen_page_lists($tpl, $sql, $_SESSION['user_id']);

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Client/Manage Users'),
		'TR_MANAGE_USERS'		=> tr('Manage users'),
		'TR_TYPE'				=> tr('Type'),
		'TR_STATUS'				=> tr('Status'),
		'TR_ACTION'				=> tr('Action'),
		'TR_TOTAL_FTP_ACCOUNTS'	=> tr('FTPs total'),
		'TR_DOMAIN'				=> tr('Domain'),
		'TR_FTP_USERS'			=> tr('FTP users'),
		'TR_FTP_ACCOUNT'		=> tr('FTP account'),
		'TR_FTP_ACTION'			=> tr('Action'),
		'TR_LOGINAS'			=> tr('Login to Net2FTP'),
		'TR_EDIT'				=> tr('Edit'),
		'TR_DELETE'				=> tr('Delete'),
		'TR_MESSAGE_DELETE'		=> tr('Are you sure you want to delete %s?', true, '%s')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_ftp_accounts.tpl');
gen_client_menu($tpl, 'client/menu_ftp_accounts.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// page functions.

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $dmn_id
 * @param string $dmn_name
 */
function gen_page_ftp_list($tpl, $sql, $dmn_id, $dmn_name) {
	$query = "
		SELECT
			`gid`,
			`members`
		FROM
			`ftp_group`
		WHERE
			`groupname` = ?
	;";

	$rs = exec_query($sql, $query, $dmn_name);

	if ($rs->recordCount() == 0) {
		$tpl->assign(
			array(
				'FTP_MSG' => tr('FTP list is empty!'),
				'FTP_MSG_TYPE' => 'info',
				'FTP_ITEM' => '',
				'FTPS_TOTAL' => '',
				'TABLE_LIST' => ''
			)
		);

	} else {
		$ftp_accs = explode(',', $rs->fields['members']);
		sort($ftp_accs);
		reset($ftp_accs);

		for ($i = 0, $cnt_ftp_accs = count($ftp_accs); $i < $cnt_ftp_accs; $i++) {
			$tpl->assign('ITEM_CLASS', ($i % 2 == 0) ? 'content' : 'content2');

			$ftp_accs_encode[$i] = decode_idna($ftp_accs[$i]);

			$query = "
				SELECT
					`net2ftppasswd`
				FROM
					`ftp_users`
				WHERE
					`userid` = ?
			;";
			$rs = exec_query($sql, $query, $ftp_accs[$i]);

			$tpl->append(
				array(
					'FTP_ACCOUNT' => tohtml($ftp_accs_encode[$i]),
					'UID' => urlencode($ftp_accs[$i]),
					'FTP_LOGIN_AVAILABLE' => !is_null($rs->fields['net2ftppasswd']),
				)
			);

		}

		$tpl->assign('TOTAL_FTP_ACCOUNTS', count($ftp_accs));
	}
}

function gen_page_lists($tpl, $sql, $user_id) {

	$dmn_props = get_domain_default_props($user_id);

	gen_page_ftp_list($tpl, $sql, $dmn_props['domain_id'], $dmn_props['domain_name']);
}
?>
