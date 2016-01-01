<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'client/subdomain_edit.tpl';

// "Modify" button has been pressed
if (isset($_POST['uaction']) && ($_POST['uaction'] === 'modify')) {
	if (isset($_GET['edit_id'])) {
		$editid = $_GET['edit_id'];
	} else if (isset($_SESSION['edit_ID'])) {
		$editid = $_SESSION['edit_ID'];
	} else {
		unset($_SESSION['edit_ID']);

		$_SESSION['subedit'] = '_no_';
		user_goto('domains_manage.php');
	}
	// Get subdomain type
	if (isset($_POST['dmn_type'])) {
		$dmntype = $_POST['dmn_type'];
	} else {
		unset($_SESSION['edit_ID']);

		$_SESSION['subedit'] = '_no_';
		user_goto('domains_manage.php');
	}
	$dmn_id = $_POST['dmn_id'];
	// Save data to db
	if (check_fwd_data($tpl, $sql, $editid, $dmntype, $dmn_id)) {
		$_SESSION['subedit'] = '_yes_';
		user_goto('domains_manage.php');
	}
} else {
	// Get user id that comes for edit
	if (isset($_GET['edit_id'])) {
		$editid = $_GET['edit_id'];
	}

	// Get subdomain type
	if (isset($_GET['dmn_type'])) {
		$dmntype = $_GET['dmn_type'];
	} else {
		user_goto('domains_manage.php');
	}

	$_SESSION['edit_ID'] = $editid;
	$tpl->assign('PAGE_MESSAGE', '');
}

// static page messages
gen_logged_from($tpl);
$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Manage Subdomain/Edit Subdomain'),
		'TR_MANAGE_SUBDOMAIN'	=> tr('Manage subdomain'),
		'TR_EDIT_SUBDOMAIN'		=> tr('Edit subdomain'),
		'TR_SUBDOMAIN_NAME'		=> tr('Subdomain name'),
		'TR_FORWARD'			=> tr('Forward to URL'),
		'TR_MOUNT_POINT'		=> tr('Mount Point'),
		'TR_MODIFY'				=> tr('Modify'),
		'TR_CANCEL'				=> tr('Cancel'),
		'TR_ENABLE_FWD'			=> tr('Enable Forward'),
		'TR_ENABLE'				=> tr('Enable'),
		'TR_DISABLE'			=> tr('Disable'),
		'TR_PREFIX_HTTP'		=> 'http://',
		'TR_PREFIX_HTTPS'		=> 'https://',
		'TR_PREFIX_FTP'			=> 'ftp://'
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_manage_domains.tpl');
gen_client_menu($tpl, 'client/menu_manage_domains.tpl');

gen_editsubdomain_page($tpl, $sql, $editid, $dmntype);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// Begin function block

/**
 * Show user data
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $edit_id
 * @param string $dmn_type
 */
function gen_editsubdomain_page($tpl, $sql, $edit_id, $dmn_type) {
	// Get data from sql
	$dmn_props = get_domain_default_props($_SESSION['user_id']);

	$domain_id = $dmn_props['domain_id'];
	$domain_name = $dmn_props['domain_name'];

	if ($dmn_type === 'dmn') {
		$query = '
			SELECT
				*
			FROM
				`subdomain`
			WHERE
				`subdomain_id` = ?
			AND
				`domain_id` = ?
		';
		$res = exec_query($sql, $query, array($edit_id, $domain_id));
	} else {
		$query = '
			SELECT
				t1.subdomain_alias_name AS subdomain_name,
				t1.subdomain_alias_mount AS subdomain_mount,
				t1.subdomain_alias_url_forward AS subdomain_url_forward,
				t2.alias_name AS domain_name,
				t2.alias_id as domain_id
			FROM
				subdomain_alias t1
			LEFT JOIN
				(domain_aliasses AS t2) ON (t1.alias_id = t2.alias_id)
			WHERE
				t1.alias_id IN (SELECT alias_id FROM domain_aliasses WHERE domain_id = ?)
			AND
				subdomain_alias_id = ?
		';
		$res = exec_query($sql, $query, array($domain_id, $edit_id));
	}

	if ($res->RecordCount() <= 0) {
		$_SESSION['subedit'] = '_no_';
		user_goto('domains_manage.php');
	}
	$data = $res->FetchRow();

	if ($dmn_type === 'als') {
		$domain_name = $data['domain_name'];
		$dmn_id = $data['domain_id'];
	} else {
		$dmn_id = $domain_id;
	}

	if (isset($_POST['uaction']) && ($_POST['uaction'] == 'modify')) {
		$url_forward = clean_input($_POST['forward']);
	} else {
		$url_forward = decode_idna(preg_replace('(ftp://|https://|http://)', '', $data['subdomain_url_forward']));

		if ($data['subdomain_url_forward'] == 'no') {
			$check_en		= '';
			$check_dis		= 'checked="checked"';
			$url_forward	= '';
			$tpl->assign(
				array(
					'READONLY_FORWARD'	=> ' readonly',
					'DISABLE_FORWARD'	=> ' disabled="disabled"',
					'HTTP_YES'			=> '',
					'HTTPS_YES'			=> '',
					'FTP_YES'			=> ''
				)
			);
		} else {
			$check_en	= 'checked="checked"';
			$check_dis	= '';
			$tpl->assign(
				array(
					'READONLY_FORWARD'	=> '',
					'DISABLE_FORWARD'	=> '',
					'HTTP_YES'			=> (preg_match('/http:\/\//', $data['subdomain_url_forward'])) ? 'selected="selected"' : '',
					'HTTPS_YES'			=> (preg_match('/https:\/\//', $data['subdomain_url_forward'])) ? 'selected="selected"' : '',
					'FTP_YES'			=> (preg_match('/ftp:\/\//', $data['subdomain_url_forward'])) ? 'selected="selected"' : ''
				)
			);
		}
		$tpl->assign(
			array(
				'CHECK_EN'	=> $check_en,
				'CHECK_DIS'	=> $check_dis
			)
		);
	}
	// Fill in the fields
	$tpl->assign(
		array(
			'SUBDOMAIN_NAME'	=> decode_idna($data['subdomain_name']) . '.' . $domain_name,
			'FORWARD'			=> $url_forward,
			'MOUNT_POINT'		=> $data['subdomain_mount'],
			'ID'				=> $edit_id,
			'DMN_TYPE'			=> $dmn_type,
			'DMN_ID'			=> $dmn_id
		)
	);

}

/**
 * Check input data
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $subdomain_id
 * @param string $dmn_type
 */
function check_fwd_data($tpl, $sql, $subdomain_id, $dmn_type, $dmn_id) {

	$forward_url = clean_input($_POST['forward']);
	// unset errors
	$ed_error = '_off_';

	if (isset($_POST['status']) && $_POST['status'] == 1) {
		$forward_prefix = clean_input($_POST['forward_prefix']);
		$surl = @parse_url($forward_prefix.decode_idna($forward_url));
		$domain = $surl['host'];
		if (substr_count($domain, '.') <= 2) {
			$ret = validates_dname($domain);
		} else {
			$ret = validates_dname($domain, true);
		}
		if (!$ret) {
			$ed_error = tr('Wrong domain part in forward URL!');
		} else {
			$forward_url = encode_idna($forward_prefix.$forward_url);
		}
		$check_en = 'checked="checked"';
		$check_dis = '';
		$tpl->assign(
			array(
				'FORWARD'	=> $forward_url,
				'HTTP_YES'	=> ($forward_prefix === 'http://') ? 'selected="selected"' : '',
				'HTTPS_YES'	=> ($forward_prefix === 'https://') ? 'selected="selected"' : '',
				'FTP_YES'	=> ($forward_prefix === 'ftp://') ? 'selected="selected"' : '',
				'CHECK_EN'	=> $check_en,
				'CHECK_DIS'	=> $check_dis,
			)
		);
	} else {
		$check_en = '';
		$check_dis = 'checked="checked"';
		$forward_url = 'no';
		$tpl->assign(
			array(
				'READONLY_FORWARD'	=> ' readonly',
				'DISABLE_FORWARD'	=> ' disabled="disabled"',
				'CHECK_EN'			=> $check_en,
				'CHECK_DIS'			=> $check_dis,
			)
		);
	}
	if ($ed_error === '_off_') {
		if ($dmn_type === 'dmn') {
			$subdomainQuery = '
				UPDATE
					`subdomain`
				SET
					`subdomain_url_forward` = ?,
					`status` = ?
				 WHERE
					`subdomain_id` = ?
			';
			$domainQuery = '
				UPDATE
					domain
				SET
					status = ?
				where 
					domain_id = ?
			';
		} else {
			$subdomainQuery = '
				UPDATE
					`subdomain_alias`
				SET
					`subdomain_alias_url_forward` = ?,
					`status` = ?
				WHERE
					`subdomain_alias_id` = ?
			';
			$domainQuery = '
				UPDATE
					domain_aliasses
				SET
					status = ?
				where 
					alias_id = ?
			';
		}

		exec_query($sql, $subdomainQuery, array($forward_url, EasySCP_Registry::get('Config')->ITEM_CHANGE_STATUS, $subdomain_id));
		exec_query($sql, $domainQuery, array(EasySCP_Registry::get('Config')->ITEM_CHANGE_STATUS, $dmn_id));

		if ($_POST['dmn_type'] == 'als') {
			send_request('110 DOMAIN alias '. $dmn_id);
		} else {
			send_request('110 DOMAIN domain '. $dmn_id);
		}

		$admin_login = $_SESSION['user_logged'];
		write_log("$admin_login: change domain alias forward: " . $subdomain_id);
		unset($_SESSION['edit_ID']);
		$tpl->assign('MESSAGE', '');
		return true;
	} else {
		$tpl->assign('MESSAGE', $ed_error);
		return false;
	}
}
?>