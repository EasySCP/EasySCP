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

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

// Avoid unneeded generation during Ajax request
if (!is_xhr()) {
	$tpl = EasySCP_TemplateEngine::getInstance();
	$template = 'reseller/user_add4.tpl';

	// static page messages
	gen_logged_from($tpl);

	$tpl->assign(
		array(
			'TR_PAGE_TITLE'				=> tr('EasySCP - User/Add user'),
			'TR_MANAGE_DOMAIN_ALIAS'	=> tr('Manage domain alias'),
			'TR_ADD_ALIAS'				=> tr('Add domain alias'),
			'TR_DOMAIN_NAME'			=> tr('Domain name'),
			'TR_DOMAIN_ACCOUNT'			=> tr('User account'),
			'TR_MOUNT_POINT'			=> tr('Directory mount point'),
			'TR_DOMAIN_IP'				=> tr('Domain IP'),
			'TR_DMN_HELP'				=> tr("You do not need 'www.' EasySCP will add it on its own."),
			'TR_FORWARD'				=> tr('Forward to URL'),
			'TR_ADD'					=> tr('Add alias'),
			'TR_DOMAIN_ALIAS'			=> tr('Domain alias'),
			'TR_STATUS'					=> tr('Status'),
			'TR_ADD_USER'				=> tr('Add user'),
			'TR_GO_USERS'				=> tr('Done'),
			'TR_ENABLE_FWD'				=> tr("Enable Forward"),
			'TR_ENABLE'					=> tr("Enable"),
			'TR_DISABLE'				=> tr("Disable"),
			'TR_PREFIX_HTTP'			=> 'http://',
			'TR_PREFIX_HTTPS'			=> 'https://',
			'TR_PREFIX_FTP'				=> 'ftp://'
		)
	);

	gen_reseller_mainmenu($tpl, 'reseller/main_menu_users_manage.tpl');
	gen_reseller_menu($tpl, 'reseller/menu_users_manage.tpl');

	if (isset($_SESSION['dmn_id']) && $_SESSION['dmn_id'] !== '') {
		$domain_id = $_SESSION['dmn_id'];
		$reseller_id = $_SESSION['user_id'];

		$query = "
			SELECT
				domain_id, status
			FROM
				domain
			WHERE
				domain_id = ?
			AND
				domain_created_id = ?
		;";

		$result = exec_query($sql, $query, array($domain_id, $reseller_id));

		if ($result->recordCount() == 0) {
			set_page_message(
				tr('User does not exist or you do not have permission to access this interface!'),
				'warning'
			);

			// Back to the users page
			user_goto('users.php?psi=last');
		} else {
			$row = $result->fetchRow();
			$dmn_status = $row['status'];

			if ($dmn_status != $cfg->ITEM_OK_STATUS && $dmn_status != $cfg->ITEM_ADD_STATUS) {
				set_page_message(
					tr('System error with Domain ID: %d', $domain_id),
					'error'
				);

				// Back to the users page
				user_goto('users.php?psi=last');
			}
		}
	} else {
		set_page_message(
			tr('User does not exist or you do not have permission to access this interface!'),
			'warning'
		);
		user_goto('users.php?psi=last');
	}
}

$err_txt = '_off_';

// Dispatch Request
if (isset($_POST['uaction'])) {
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
	if(isset($_SESSION['alias_added_succesfully'])) {
		set_page_message(tr('Domain alias added!'), 'success');
		unset($_SESSION['alias_added_succesfully']);
	}
}

gen_al_page($tpl, $_SESSION['user_id']);
gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

// Begin function declaration lines

/**
 * Initializes global variables to avoid warnings
 * 
 * @global string $cr_user_id
 * @global string $alias_name
 * @global string $domain_ip
 * @global <type> $forward
 * @global <type> $forward_prefix
 * @global string $mount_point
 * @global EasySCP_TemplateEngine $tpl 
 */
function init_empty_data() {
	global $cr_user_id, $alias_name, $domain_ip, $forward, $forward_prefix,
		$mount_point, $tpl;

	$cfg = EasySCP_Registry::get('Config');

	$cr_user_id = $alias_name = $domain_ip = $forward = $mount_point = '';

	if (isset($_POST['status']) && $_POST['status'] == 1) {
		$forward_prefix = clean_input($_POST['forward_prefix']);
		if ($_POST['status'] == 1) {
			$check_en = $cfg->HTML_CHECKED;
			$check_dis = '';
			$forward = encode_idna(strtolower(clean_input($_POST['forward'])));
			$tpl->assign(
				array(
					'READONLY_FORWARD'	=> '',
					'DISABLE_FORWARD'	=> '',
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
				)
			);
		}
		$tpl->assign(
			array(
				'HTTP_YES'	=> ($forward_prefix === 'http://') ? $cfg->HTML_SELECTED : '',
				'HTTPS_YES' => ($forward_prefix === 'https://') ? $cfg->HTML_SELECTED : '',
				'FTP_YES'	=> ($forward_prefix === 'ftp://') ? $cfg->HTML_SELECTED : ''
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
				'HTTP_YES'			=>	'',
				'HTTPS_YES'			=>	'',
				'FTP_YES'			=>	''
			)
		);
	}

	$tpl->assign(
		array(
			'DOMAIN'	=> !empty($_POST) ? strtolower(clean_input($_POST['ndomain_name'], true)) : '',
			'MP'		=> !empty($_POST) ? strtolower(clean_input($_POST['ndomain_mpoint'], true)) : '',
			'FORWARD'	=> tohtml(encode_idna($forward)),
			'CHECK_EN'	=> $check_en,
			'CHECK_DIS' => $check_dis,
		)
	);
} // End of init_empty_data()

/**
 * Show data fields
 *
 * @global <type> $alias_name
 * @global  $forward
 * @global  $forward_prefix
 * @global string $mount_point
 * @param EasySCP_TemplateEngine $tpl
 * @param int $reseller_id
 */
function gen_al_page($tpl, $reseller_id) {
	global $alias_name, $forward, $forward_prefix, $mount_point;

	$sql = EasySCP_Registry::get('Db');

	$dmn_id = $_SESSION['dmn_id'];

	$query = "
		SELECT
			`alias_id`,
			`alias_name`,
			`status`,
			`url_forward`
		FROM
			`domain_aliasses`
		WHERE
			`domain_id` = ?
	;";

	$rs = exec_query($sql, $query, $dmn_id);

	if ($rs->recordCount() == 0) {
		$tpl->assign('ALIAS_LIST', '');
	} else {
		while (!$rs->EOF) {
			$tpl->append(
				array(
					'DOMAIN_ALIAS'	=> tohtml(decode_idna($rs->fields['alias_name'])),
					'STATUS'		=> translate_dmn_status($rs->fields['status']),
					'FORWARD_URL'	=> ($rs->fields['url_forward'] == 'no') ? "-" : $rs->fields['url_forward']
				)
			);

			$rs->moveNext();
		}
	}
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

	$cr_user_id = $dmn_id = $_SESSION['dmn_id'];
	$alias_name = strtolower(clean_input($_POST['ndomain_name']));
	$domain_ip = $_SESSION['dmn_ip'];
	$mount_point = array_encode_idna(strtolower($_POST['ndomain_mpoint']), true);

	if ($_POST['status'] == 1) {
		$forward = encode_idna(strtolower(clean_input($_POST['forward'])));
		$forward_prefix = clean_input($_POST['forward_prefix']);
	} else {
		$forward = 'no';
		$forward_prefix = '';
	}

	// Check if input string is a valid domain names
	if (!validates_dname($alias_name)) {
		set_page_message($validation_err_msg, 'warning');
		return;
	}

	// Should be perfomed after domain names syntax validation now
	$alias_name = encode_idna($alias_name);

	if (easyscp_domain_exists($alias_name, $_SESSION['user_id'])) {
		$err_al = tr('Domain with that name already exists on the system!');
	} else if (!validates_mpoint($mount_point) && $mount_point != '/') {
		$err_al = tr("Incorrect mount point syntax");
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
			// we already have a domain with this name
			$err_al = tr("Domain with this name already exist");
		}

		if (mount_point_exists($dmn_id, $mount_point)) {
			$err_al = tr('Mount point already in use!');
		}
	}

	if ('_off_' !== $err_al) {
		set_page_message($err_al, 'error');
		return;
	}
	// Begin add new alias domain
	$query = "
		INSERT INTO
			`domain_aliasses` (
				`domain_id`, `alias_name`, `alias_mount`, `status`,
				`alias_ip_id`, `url_forward`
			)
		VALUES
			(?, ?, ?, ?, ?, ?)
	;";
	exec_query($sql, $query,
		array(
			$cr_user_id, $alias_name, $mount_point, $cfg->ITEM_ADD_STATUS,
			$domain_ip, $forward
		)
	);

	$alias_id = $sql->insertId();
	
	update_reseller_c_props(get_reseller_id($cr_user_id));

	send_request('110 DOMAIN alias '.$alias_id);
	$admin_login = $_SESSION['user_logged'];
	write_log("$admin_login: add domain alias: $alias_name");

	$_SESSION['alias_added_succesfully'] = 1;
	user_goto('user_add4.php?accout='.$cr_user_id);
} // End of add_domain_alias();

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param string $error_txt
 */
function gen_page_msg($tpl, $error_txt) {
	if ($error_txt != '_off_') {
		$tpl->assign('MESSAGE', $error_txt);
	} else {
		$tpl->assign('PAGE_MESSAGE', '');
	}
} // End of gen_page_msg()
?>