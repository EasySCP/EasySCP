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

// Avoid unneeded generation during Ajax request
if(!is_xhr()) {
	$tpl = EasySCP_TemplateEngine::getInstance();
	$template = 'client/alias_add.tpl';

	// static page messages
	gen_logged_from($tpl);

	check_permissions($tpl);

	$tpl->assign(
		array(
			'TR_PAGE_TITLE'				=> tr('EasySCP Client : Add Alias'),
			'TR_MANAGE_DOMAIN_ALIAS'	=> tr('Manage domain alias'),
			'TR_ADD_ALIAS'				=> tr('Add domain alias'),
			'TR_DOMAIN_NAME'			=> tr('Domain name'),
			'TR_DOMAIN_ACCOUNT'			=> tr('User account'),
			'TR_MOUNT_POINT'			=> tr('Directory mount point'),
			'TR_DOMAIN_IP'				=> tr('Domain IP'),
			'TR_FORWARD'				=> tr('Forward to URL'),
			'TR_ADD'					=> tr('Add alias'),
			'TR_DMN_HELP'				=> tr("You do not need 'www.' EasySCP will add it on its own."),
			'TR_JS_EMPTYDATA'			=> tr("Empty data or wrong field!"),
			'TR_JS_WDNAME'				=> tr("Wrong domain name!"),
			'TR_JS_MPOINTERROR'			=> tr("Please write mount point!"),
			'TR_ENABLE_FWD'				=> tr("Enable Forward"),
			'TR_ENABLE'					=> tr("Enable"),
			'TR_DISABLE'				=> tr("Disable"),
			'TR_PREFIX_HTTP'			=> 'http://',
			'TR_PREFIX_HTTPS'			=> 'https://',
			'TR_PREFIX_FTP'				=> 'ftp://'
		)
	);

	gen_client_mainmenu($tpl, 'client/main_menu_manage_domains.tpl');
	gen_client_menu($tpl, 'client/menu_manage_domains.tpl');

	check_client_domainalias_counts($sql, $_SESSION['user_id']);
}

$err_txt = '_off_';

// Dispatch Request
if(isset($_POST['uaction'])) {
	if($_POST['uaction'] == 'toASCII') { // Ajax request
		header('Content-Type: text/plain; charset=utf-8');
		header('Cache-Control: no-cache, private');
		// backward compatibility for HTTP/1.0
		header('Pragma: no-cache');
		header("HTTP/1.0 200 Ok");

		// Todo check return value here before echo...
		echo "/".encode_idna(strtolower($_POST['domain']));
		exit;
	} elseif($_POST['uaction'] == 'add_alias') {
		add_domain_alias($err_txt);
	} else {
		throw new EasySCP_Exception(tr("Error: unknown action!" . " " . $_POST['uaction']));
	}
} else { // Default view
	init_empty_data();
}

gen_al_page($tpl, $_SESSION['user_id']);
gen_page_msg($tpl, $err_txt);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

/*
 * Begin function declaration lines
 */

/**
 *
 * @param <type> $sql
 * @param <type> $user_id
 */
function check_client_domainalias_counts($sql, $user_id) {

	$dmn_props = get_domain_default_props($user_id);

	$als_cnt = get_domain_running_als_cnt($sql, $dmn_props['domain_id']);

	if ($dmn_props['domain_alias_limit'] != 0 && $als_cnt >= $dmn_props['domain_alias_limit']) {
		set_page_message(tr('Domain alias limit reached!'), 'warning');
		user_goto('domains_manage.php');
	}
}
/**
 *
 * @global string $cr_user_id
 * @global string $alias_name
 * @global string $domain_ip
 * @global string $forward
 * @global string $mount_point
 */
function init_empty_data() {
	global $cr_user_id, $alias_name, $domain_ip, $forward, $mount_point;

	$cr_user_id = $alias_name = $domain_ip = $forward = $mount_point = '';

} // End of init_empty_data()


/**
 * Show data fields
 *
 * @global string $alias_name
 * @global string $forward
 * @global string $forward_prefix
 * @global string $mount_point
 * @param EasySCP_TemplateEngine $tpl
 * @param int $reseller_id
 */
function gen_al_page($tpl, $reseller_id) {

	global $alias_name, $forward, $forward_prefix, $mount_point;

	$cfg = EasySCP_Registry::get('Config');

	if (isset($_POST['status']) && $_POST['status'] == 1) {

		$forward_prefix = clean_input($_POST['forward_prefix']);

		$check_en = $cfg->HTML_CHECKED;
		$check_dis = '';
		$forward = encode_idna(strtolower(clean_input($_POST['forward'])));

		$tpl->assign(
			array(
				'READONLY_FORWARD'	=> '',
				'DISABLE_FORWARD'	=> '',
				'HTTP_YES'			=> ($forward_prefix === 'http://') ? $cfg->HTML_SELECTED : '',
				'HTTPS_YES'			=> ($forward_prefix === 'https://') ? $cfg->HTML_SELECTED : '',
				'FTP_YES'			=> ($forward_prefix === 'ftp://') ? $cfg->HTML_SELECTED : ''
			)
		);
	} else {
		$check_en = '';
		$check_dis = $cfg->HTML_CHECKED;
		$forward = '';

		$tpl->assign(
			array(
				'READONLY_FORWARD'	=> $cfg->HTML_READONLY,
				'DISABLE_FORWARD'	=> $cfg->HTML_DISABLED,
				'HTTP_YES'			=> '',
				'HTTPS_YES'			=> '',
				'FTP_YES'			=> ''
				)
			);
	}

	$tpl->assign(
		array(
			'DOMAIN'	=> tohtml(decode_idna($alias_name)),
			'FORWARD'	=> tohtml($forward),
			'CHECK_EN'	=> $check_en,
			'CHECK_DIS' => $check_dis,
		)
	);

} // End of gen_al_page()

/**
 *
 * @global <type> $cr_user_id
 * @global <type> $alias_name
 * @global <type> $domain_ip
 * @global <type> $forward
 * @global <type> $forward_prefix
 * @global <type> $mount_point
 * @global <type> $validation_err_msg
 * @param <type> $err_al
 * @return <type>
 */
function add_domain_alias(&$err_al) {

	global $cr_user_id, $alias_name, $domain_ip, $forward, $forward_prefix,
		$mount_point, $validation_err_msg;

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$cr_user_id = get_user_domain_id($_SESSION['user_id']);
	$alias_name	= strtolower($_POST['ndomain_name']);
//	$mount_point = array_encode_idna(strtolower($_POST['ndomain_mpoint']), true);

	if ($_POST['status'] == 1) {
		$forward = encode_idna(strtolower(clean_input($_POST['forward'])));
		$forward_prefix = clean_input($_POST['forward_prefix']);
	} else {
		$forward = 'no';
		$forward_prefix = '';
	}

	$query = "
		SELECT
			`domain_ip_id`
		FROM
			`domain`
		WHERE
			`domain_id` = ?
	";

	$rs = exec_query($sql, $query, $cr_user_id);
	$domain_ip = $rs->fields['domain_ip_id'];

	// First check if input string is a valid domain names
	if (!validates_dname($alias_name)) {
		$err_al = $validation_err_msg;
		return;
	}

	// Should be perfomed after domain names syntax validation now
	$alias_name = encode_idna($alias_name);

	if (easyscp_domain_exists($alias_name, 0)) {
	 $err_al = tr('Domain with that name already exists on the system!');
//	} else if (!validates_mpoint($mount_point) && $mount_point != '/') {
//		$err_al = tr("Incorrect mount point syntax");
	} else if ($alias_name == $cfg->BASE_SERVER_VHOST) {
		$err_al = tr('Master domain cannot be used!');
	} else if ($_POST['status'] == 1) {
		$aurl = @parse_url($forward_prefix.decode_idna($forward));
		if ($aurl === false) {
			$err_al = tr("Wrong address in forward URL!");
		} else {
			$domain = $aurl['host'];
			if (substr_count($domain, '.') <= 2) {
				$ret = validates_dname($domain);
			} else {
				$ret = validates_dname($domain, true);
			}
			
			if (!$ret) {
				$err_al = tr("Wrong domain part in forward URL!");
			} else {
				$domain = encode_idna($aurl['host']);
				$forward = $aurl['scheme'].'://';
				if (isset($aurl['user'])) {
					$forward .= $aurl['user'] . (isset($aurl['pass']) ? ':' . $aurl['pass'] : '') .'@';
				}
				$forward .= $domain;
				if (isset($aurl['port'])) {
					$forward .= ':'.$aurl['port'];
				}
				if (isset($aurl['path'])) {
					$forward .= $aurl['path'];
				} else {
					$forward .= '/';
				}
				if (isset($aurl['query'])) {
					$forward .= '?'.$aurl['query'];
				}
				if (isset($aurl['fragment'])) {
					$forward .= '#'.$aurl['fragment'];
				}
			}
		}
	} else {
		$query = "
			SELECT
				`domain_id`
			FROM
				`domain_aliasses`
			WHERE
				`alias_name` = ?
		;";
		$res = exec_query($sql, $query, $alias_name);

		$query = "
			SELECT
				`domain_id`
			FROM
				`domain`
			WHERE
				`domain_name` = ?
		;";
		$res2 = exec_query($sql, $query, $alias_name);

		if ($res->rowCount() > 0 || $res2->rowCount() > 0) {
			// we already have domain with this name
			$err_al = tr("Domain with this name already exist");
		}

//		$query = "
//			SELECT
//				COUNT(`subdomain_id`) AS cnt
//			FROM
//				`subdomain`
//			WHERE
//					`domain_id` = ?
//				AND
//					`subdomain_mount` = ?
//		;";
//		$subdomres = exec_query($sql, $query, array($cr_user_id, $mount_point));
//		$subdomdata = $subdomres->fetchRow();
//
//		$query = "
//			SELECT
//				COUNT(`subdomain_alias_id`) AS alscnt
//			FROM
//				`subdomain_alias`
//			WHERE
//					`alias_id`
//				IN (
//					SELECT
//						`alias_id`
//					FROM
//						`domain_aliasses`
//					WHERE
//						`domain_id` = ?
//				)
//				AND
//					`subdomain_alias_mount` = ?
//		;";
//		$alssubdomres = exec_query($sql, $query, array($cr_user_id, $mount_point));
//		$alssubdomdata = $alssubdomres->fetchRow();
//
//		if ($subdomdata['cnt'] > 0 || $alssubdomdata['alscnt'] > 0) {
//			$err_al = tr("There is a subdomain with the same mount point!");
//		}
	}

	if ('_off_' !== $err_al) {
		return;
	}

	// Begin add new alias domain

	$status = $cfg->ITEM_ORDERED_STATUS;

	$query = "
		INSERT INTO
			`domain_aliasses` (
				`domain_id`, `alias_name`, `alias_mount`, `status`,
				`alias_ip_id`, `url_forward`
			)
		VALUES
			(?, ?, ?, ?, ?, ?)
	;";
	exec_query($sql, $query, array($cr_user_id, $alias_name, $mount_point, $status, $domain_ip, $forward));

	$dmn_id = $sql->insertId();
	
	AddDefaultDNSEntries(0, $dmn_id, $alias_name, $domain_ip);

	update_reseller_c_props(get_reseller_id($cr_user_id));

	$admin_login = $_SESSION['user_logged'];

	if ($status == $cfg->ITEM_ORDERED_STATUS) {
		// notify the reseller:
		send_alias_order_email($alias_name);

		write_log("$admin_login: add domain alias for activation: $alias_name.");
		set_page_message(
			tr('Alias scheduled for activation!'),
			'success'
		);
	} else {
		// TODO: Check
//		send_request('110 DOMAIN alias '.$dmn_id);
		write_log("$admin_login: domain alias scheduled for addition: $alias_name.");
		set_page_message(
			tr('Alias scheduled for addition!'),
			'success'
		);
	}

	user_goto('domains_manage.php');
} // End of add_domain_alias();

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param string $error_txt
 */
function gen_page_msg($tpl, $error_txt) {

	if ($error_txt != '_off_') {
		$tpl->assign('MESSAGE', $error_txt);
		$tpl->assign('MSG_TYPE', 'error');
	}

} // End of gen_page_msg()
?>