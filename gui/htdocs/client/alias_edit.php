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
$template = 'client/alias_edit.tpl';

// static page messages
$tpl->assign(
	array(

		'TR_PAGE_TITLE'			=> tr('EasySCP - Manage Domain Alias/Edit Alias'),
		'TR_MANAGE_DOMAIN_ALIAS'=> tr('Manage domain alias'),
		'TR_EDIT_ALIAS'			=> tr('Edit domain alias'),
		'TR_ALIAS_NAME'			=> tr('Alias name'),
		'TR_DOMAIN_IP'			=> tr('Domain IP'),
		'TR_FORWARD'			=> tr('Forward to URL'),
		'TR_MOUNT_POINT'		=> tr('Mount Point'),
		'TR_MODIFY'				=> tr('Modify'),
		'TR_CANCEL'				=> tr('Cancel'),
		'TR_ENABLE_FWD'			=> tr("Enable Forward"),
		'TR_ENABLE'				=> tr("Enable"),
		'TR_DISABLE'			=> tr("Disable"),
		'TR_PREFIX_HTTP'		=> 'http://',
		'TR_PREFIX_HTTPS'		=> 'https://',
		'TR_PREFIX_FTP'			=> 'ftp://'
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_manage_domains.tpl');
gen_client_menu($tpl, 'client/menu_manage_domains.tpl');

gen_logged_from($tpl);

// "Modify" button has been pressed
if (isset($_POST['uaction']) && ($_POST['uaction'] === 'modify')) {
	if (isset($_GET['edit_id'])) {
		$editid = $_GET['edit_id'];
	} else if (isset($_SESSION['edit_ID'])) {
		$editid = $_SESSION['edit_ID'];
	} else {
		unset($_SESSION['edit_ID']);

		$_SESSION['aledit'] = '_no_';
		user_goto('domains_manage.php');
	}
	// Save data to db
	if (check_fwd_data($tpl, $editid)) {
		$_SESSION['aledit'] = "_yes_";
		user_goto('domains_manage.php');
	}
} else {
	// Get user id that comes for edit
	if (isset($_GET['edit_id'])) {
		$editid = $_GET['edit_id'];
	}

	$_SESSION['edit_ID'] = $editid;
	$tpl->assign('PAGE_MESSAGE', "");
}
gen_editalias_page($tpl, $editid);

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// Begin function block

/**
 * Show user data
 * @param EasySCP_TemplateEngine $tpl
 * @param int $edit_id
 */
function gen_editalias_page($tpl, $edit_id) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	// Get data from sql
	$dmn_props = get_domain_default_props($_SESSION['user_id']);
	$res = exec_query($sql, "SELECT * FROM `domain_aliasses` WHERE `alias_id` = ? AND `domain_id` = ?", array($edit_id, $dmn_props['domain_id']));

	if ($res->recordCount() <= 0) {
		$_SESSION['aledit'] = '_no_';
		user_goto('domains_manage.php');
	}
	$data = $res->fetchRow();
	// Get IP data
	$ipres = exec_query($sql, "SELECT * FROM `server_ips` WHERE `ip_id` = ?", $data['alias_ip_id']);
	$ipdat = $ipres->fetchRow();
	$ip_data = $ipdat['ip_number'] . ' (' . $ipdat['ip_alias'] . ')';

	if (isset($_POST['uaction']) && ($_POST['uaction'] == 'modify')) {
		$url_forward = strtolower(clean_input($_POST['forward']));
	} else {
		$url_forward = decode_idna(preg_replace("(ftp://|https://|http://)", "", $data['url_forward']));

		if ($data["url_forward"] == "no") {
			$check_en = '';
			$check_dis = $cfg->HTML_CHECKED;
			$url_forward = '';
			$tpl->assign(
				array(
					'READONLY_FORWARD'	=> $cfg->HTML_READONLY,
					'DISABLE_FORWARD'	=> $cfg->HTML_DISABLED,
					'HTTP_YES'			=> '',
					'HTTPS_YES'			=> '',
					'FTP_YES'			=> ''
				)
			);
		} else {
			$check_en = $cfg->HTML_CHECKED;
			$check_dis = '';
			$tpl->assign(
				array(
					'READONLY_FORWARD'	=> '',
					'DISABLE_FORWARD'	=> '',
					'HTTP_YES'			=> (preg_match("/http:\/\//", $data['url_forward'])) ? $cfg->HTML_SELECTED : '',
					'HTTPS_YES'			=> (preg_match("/https:\/\//", $data['url_forward'])) ? $cfg->HTML_SELECTED : '',
					'FTP_YES'			=> (preg_match("/ftp:\/\//", $data['url_forward'])) ? $cfg->HTML_SELECTED : ''
				)
			);
		}
		$tpl->assign(
			array(
				'CHECK_EN' => $check_en,
				'CHECK_DIS' => $check_dis
			)
		);
	}
	// Fill in the fields
	$tpl->assign(
		array(
			'ALIAS_NAME'	=> tohtml(decode_idna($data['alias_name'])),
			'DOMAIN_IP'		=> $ip_data,
			'FORWARD'		=> tohtml($url_forward),
			'MOUNT_POINT'	=> tohtml($data['alias_mount']),
			'ID'			=> $edit_id
		)
	);
} // End of gen_editalias_page()

/**
 * Check input data
 * @param EasySCP_TemplateEngine $tpl
 * @param int $alias_id
 */
function check_fwd_data($tpl, $alias_id) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$forward_url = strtolower(clean_input($_POST['forward']));

	// unset errors
	$ed_error = '_off_';

	if (isset($_POST['status']) && $_POST['status'] == 1) {
		$forward_prefix = clean_input($_POST['forward_prefix']);

		// TODO Pruefen, da diese Funktion beim aendern von eintraegen einen Fehler verursacht.
		// Fehler kommt wenn die URL auf einen Slash = "/" endet bzw. wenn diese auf eine bestimmte Seite verweist z.b. http://www.example.com/neueSeite.html
		/*
		if (substr_count($forward_url, '.') <= 2) {
			$ret = validates_dname($forward_url);
		} else {
			$ret = validates_dname($forward_url, true);
		}
		if (!$ret) {
			$ed_error = tr("Wrong domain part in forward URL!");
		} else {
			$forward_url = encode_idna($forward_prefix.$forward_url);
		}
		*/
		$forward_url = encode_idna($forward_prefix.$forward_url);

		$check_en = $cfg->HTML_CHECKED;
		$check_dis = '';
		$tpl->assign(
			array(
				'FORWARD'			=> tohtml($forward_url),
				'HTTP_YES'			=> ($forward_prefix === 'http://') ? $cfg->HTML_SELECTED : '',
				'HTTPS_YES'			=> ($forward_prefix === 'https://') ? $cfg->HTML_SELECTED : '',
				'FTP_YES'			=> ($forward_prefix === 'ftp://') ? $cfg->HTML_SELECTED : '',
				'CHECK_EN'			=> $check_en,
				'CHECK_DIS'			=> $check_dis,
				'DISABLE_FORWARD'	=>	'',
				'READONLY_FORWARD'	=>	''
			)
		);
	} else {
		$check_en = '';
		$check_dis = $cfg->HTML_CHECKED;
		$forward_url = 'no';
		$tpl->assign(
			array(
				'READONLY_FORWARD' => $cfg->HTML_READONLY,
				'DISABLE_FORWARD' => $cfg->HTML_DISABLED,
				'CHECK_EN' => $check_en,
				'CHECK_DIS' => $check_dis,
			)
		);
	}

	if ($ed_error === '_off_') {
		$query = "
			UPDATE
				`domain_aliasses`
			SET
				`url_forward` = ?,
				`status` = ?
			WHERE
				`alias_id` = ?
		";
		exec_query($sql, $query, array($forward_url, $cfg->ITEM_CHANGE_STATUS, $alias_id));

		$query = "
			UPDATE
				`subdomain_alias`
			SET
				`status` = ?
			WHERE
				`alias_id` = ?
		";
		exec_query($sql, $query, array($cfg->ITEM_CHANGE_STATUS, $alias_id));

		send_request('110 DOMAIN alias '.$alias_id);

		$admin_login = $_SESSION['user_logged'];

		$rs = exec_query( $sql, "SELECT `alias_name` FROM `domain_aliasses` WHERE `alias_id` = ?", $alias_id );

		write_log("$admin_login: change domain alias forward: " . $rs->fields['alias_name']);
		unset($_SESSION['edit_ID']);
		return true;
	} else {
		set_page_message(
			$ed_error,
			'error'
		);
		return false;
	}
} // End of check_user_data()
