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

$cfg = EasySCP_Registry::get('Config');

check_login(__FILE__);

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'client/subdomain_add.tpl';

// common page data

/*
// check user subdomain permission
if (isset($_SESSION['subdomain_support']) && $_SESSION['subdomain_support'] == "no") {
	header('Location: index.php');
}
*/

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
		echo "/".encode_idna(strtolower($_POST['subdomain']));
		exit;
	} elseif($_POST['uaction'] == 'add_subd') {
		$dmn_name = check_subdomain_permissions($_SESSION['user_id']);
		gen_user_add_subdomain_data($tpl, $_SESSION['user_id']);
		check_subdomain_data($err_txt, $_SESSION['user_id'], $dmn_name);
	} else {
		throw new EasySCP_Exception(tr("Error: unknown action!" . " " . $_POST['uaction']));
	}
} else { // Default view
	gen_user_add_subdomain_data($tpl, $_SESSION['user_id']);
}

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'						=> tr('EasySCP - Client/Add Subdomain'),
		'TR_ADD_SUBDOMAIN'					=> tr('Add subdomain'),
		'TR_SUBDOMAIN_DATA'					=> tr('Subdomain data'),
		'TR_SUBDOMAIN_NAME'					=> tr('Subdomain name'),
		'TR_DIR_TREE_SUBDOMAIN_MOUNT_POINT'	=> tr('Directory tree mount point'),
		'TR_SUBDOMAIN_ASSIGNMENT'			=> tr('Select an available subdomain'),
		'TR_FORWARD'						=> tr('Forward to URL'),
		'TR_ADD'							=> tr('Add'),
		'TR_DMN_HELP'						=> tr("You do not need 'www.' EasySCP will add it on its own."),
		'TR_ENABLE_FWD'						=> tr('Enable Forward'),
		'TR_ENABLE'							=> tr('Enable'),
		'TR_DISABLE'						=> tr('Disable'),
		'TR_PREFIX_HTTP'					=> 'http://',
		'TR_PREFIX_HTTPS'					=> 'https://',
		'TR_PREFIX_FTP'						=> 'ftp://',
		'TR_MNT_POINT_HELP'					=> tr('Path is relativ to your root directory. The mount point will contain a subfolder named htdocs.'),
		'TR_SUBDMN_ASSIGN_HELP'				=> tr('A new alias subdomain has to be assigned to an existing real subdomain.'),
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_manage_domains.tpl');
gen_client_menu($tpl, 'client/menu_manage_domains.tpl');

gen_page_msg($tpl, $err_txt);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);


// page functions.
/**
 *
 * @param EasySCP_TemplateEngine $tpl
 * @param string $error_txt
 */
function gen_page_msg($tpl, $error_txt) {

	if ($error_txt != '_off_') {
		$tpl->assign('MESSAGE', $error_txt);
		$tpl->assign('MSG_TYPE', 'error');
	}
}

/**
 *
 * @param <type> $user_id
 * @return <type>
 */
function check_subdomain_permissions($user_id) {
	$sql = EasySCP_Registry::get('Db');

	//TODO: check proper handling for alias subdomain
	$dmn_props = get_domain_default_props($user_id);

	$dmn_id = $dmn_props['domain_id'];
	$dmn_name = $dmn_props['domain_name'];
	$dmn_subd_limit = $dmn_props['domain_subd_limit'];

	$sub_cnt = get_domain_running_sub_cnt($sql, $dmn_id);

	if ($dmn_subd_limit != 0 && $sub_cnt >= $dmn_subd_limit) {
		set_page_message(tr('Subdomains limit reached!'), 'warning');
		user_goto('domains_manage.php');
	}

	if (@$_POST['dmn_type'] == 'als') {
		$query_alias = "
			SELECT
				`alias_name`
			FROM
				`domain_aliasses`
			WHERE
				`alias_id` = ?
		;";
		$rs = exec_query($sql, $query_alias, $_POST['als_id']);
		return $rs->fields['alias_name'];
	}
	return $dmn_name; // Will be used in subdmn_exists()
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $user_id
 */
function gen_user_add_subdomain_data($tpl, $user_id) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$subdomain_name = $subdomain_mnt_pt = $forward = $forward_prefix = '';

	$query = "
		SELECT
			`domain_name`,
			`domain_id`
		FROM
			`domain`
		WHERE
			`domain_admin_id` = ?
	;";

	$rs = exec_query($sql, $query, $user_id);
	$domainname = decode_idna($rs->fields['domain_name']);
	$tpl->assign(
		array(
			'DOMAIN_NAME'		=> '.' . tohtml($domainname),
			'SUB_DMN_CHECKED'	=> $cfg->HTML_CHECKED,
			'SUB_ALS_CHECKED'	=> ''
		)
	);
	gen_dmn_als_list($tpl, $rs->fields['domain_id'], 'no');
	gen_subdmn_list($tpl, $rs->fields['domain_id'], 'no');
	
	if (isset($_POST['uaction']) && $_POST['uaction'] === 'add_subd') {
		if($_POST['status'] == 1) {
			$forward_prefix = clean_input($_POST['forward_prefix']);
			$check_en = 'checked="checked"';
			$check_dis = '';
			$forward = strtolower(clean_input($_POST['forward']));
			$tpl->assign(
				array(
					'READONLY_FORWARD' => '',
					'DISABLE_FORWARD' => ''
				)
			);
		} else {
			$check_en = '';
			$check_dis = 'checked="checked"';
			$forward = '';
			$tpl->assign(
				array(
					'READONLY_FORWARD'	=> $cfg->HTML_READONLY,
					'DISABLE_FORWARD'	=> $cfg->HTML_DISABLED
				)
			);
		}
		$tpl->assign(
			array(
				'HTTP_YES'		=> ($forward_prefix === 'http://') ? $cfg->HTML_SELECTED : '',
				'HTTPS_YES'		=> ($forward_prefix === 'https://') ? $cfg->HTML_SELECTED : '',
				'FTP_YES'		=> ($forward_prefix === 'ftp://') ? $cfg->HTML_SELECTED : ''
			)
		);
		if ($_POST['dmn_type']=='dmn'){
			$subdomain_name = clean_input($_POST['subdomain_name']);
			$subdomain_mnt_pt = array_encode_idna(clean_input($_POST['subdomain_mnt_pt']), true);
		} else {
			$query = "
				SELECT
					subdomain_name,
					subdomain_mount
				FROM
					subdomain
				WHERE
					subdomain_id = ?
			;";
			$rs = exec_query($sql, $query, $_POST['subdmn_id']);
			$subdomain_name = $rs->fields['subdomain_name'];
			$_POST['subdomain_name'] = $subdomain_name;
			$subdomain_mnt_pt = $rs->fields['subdomain_mount'];
			$_POST['subdomain_mnt_pt'] = $subdomain_mnt_pt;
		}
	} else {
		$check_en = '';
		$check_dis = 'checked="checked"';
		$forward = '';
		$tpl->assign(
			array(
				'READONLY_FORWARD'	=> $cfg->HTML_READONLY,
				'DISABLE_FORWARD'	=> $cfg->HTML_DISABLED,
				'HTTP_YES'		=> '',
				'HTTPS_YES'		=> '',
				'FTP_YES'		=> ''
			)
		);
	}
	$tpl->assign(
		array(
			'SUBDOMAIN_NAME' => $subdomain_name,
			'SUBDOMAIN_MOUNT_POINT' => $subdomain_mnt_pt,
			'FORWARD'	=> $forward,
			'CHECK_EN'	=> $check_en,
			'CHECK_DIS' => $check_dis
		)
	);
}

/**
 *
 * @param EasySCP_TemplateEngine $tpl
 * @param int $dmn_id
 * @param string $post_check
 */
function gen_dmn_als_list($tpl, $dmn_id, $post_check) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$ok_status = $cfg->ITEM_OK_STATUS;

	$query = "
		SELECT
			`alias_id`, `alias_name`
		FROM
			`domain_aliasses`
		WHERE
			`domain_id` = ?
		AND
			`status` = ?
		ORDER BY
			`alias_name`
	;";

	$rs = exec_query($sql, $query, array($dmn_id, $ok_status));
	if ($rs->recordCount() == 0) {
//		$tpl->assign(
//			array(
//				'ALS_ID' => '0',
//				'ALS_SELECTED' => $cfg->HTML_SELECTED,
//				'ALS_NAME' => tr('Empty list')
//			)
//		);
//		$tpl->assign('TO_ALIAS_DOMAIN', '');
		$_SESSION['alias_count'] = "no";
	} else {
		$first_passed = false;
		while (!$rs->EOF) {
			if ($post_check === 'yes') {
				$als_id = (!isset($_POST['als_id'])) ? '' : $_POST['als_id'];
				$als_selected = ($als_id == $rs->fields['alias_id']) ? $cfg->HTML_SELECTED : '';
			} else {
				$als_selected = (!$first_passed) ? $cfg->HTML_SELECTED : '';
			}

			$alias_name = decode_idna($rs->fields['alias_name']);
			$tpl->append(
				array(
					'ALS_ID'		=> $rs->fields['alias_id'],
					'ALS_SELECTED'	=> $als_selected,
					'ALS_NAME'		=> tohtml($alias_name)
				)
			);
			$rs->moveNext();

			if (!$first_passed) {
				$first_passed = true;
			}
		}
	}
}

/**
 *
 * @param EasySCP_TemplateEngine $tpl
 * @param int $dmn_id
 * @param string $post_check
 */
function gen_subdmn_list($tpl, $dmn_id, $post_check) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$ok_status = $cfg->ITEM_OK_STATUS;

	$query = "
		SELECT
			`subdomain_id`, `subdomain_name`
		FROM
			`subdomain`
		WHERE
			`domain_id` = ?
		AND
			`status` = ?
		ORDER BY
			`subdomain_name`
	;";

	$rs = exec_query($sql, $query, array($dmn_id, $ok_status));
	if ($rs->recordCount() != 0) {
		$first_passed = false;
		while (!$rs->EOF) {
			if ($post_check === 'yes') {
				$subdmn_id = (!isset($_POST['subdmn_id'])) ? '' : $_POST['subdmn_id'];
				$subdmn_selected = ($subdmn_id == $rs->fields['subdomain_id']) ? $cfg->HTML_SELECTED : '';
			} else {
				$subdmn_selected = (!$first_passed) ? $cfg->HTML_SELECTED : '';
			}

			$subdomain_name = decode_idna($rs->fields['subdomain_name']);
			$tpl->append(
				array(
					'SUBDMN_ID'			=> $rs->fields['subdomain_id'],
					'SUBDMN_SELECTED'	=> $subdmn_selected,
					'SUBDMN_NAME'		=> tohtml($subdomain_name)
				)
			);
			$rs->moveNext();

			if (!$first_passed) {
				$first_passed = true;
			}
		}
	}
}

/**
 *
 * @global <type> $dmn_name
 * @param <type> $user_id
 * @param <type> $domain_id
 * @param <type> $sub_name
 * @return <type>
 */
function subdmn_exists($user_id, $domain_id, $sub_name) {
	global $dmn_name;

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	if ($_POST['dmn_type'] == 'als') {
		$query_subdomain = "
			SELECT
				COUNT(`subdomain_alias_id`) AS cnt
			FROM
				`subdomain_alias`
			WHERE
				`alias_id` = ?
			AND
				`subdomain_alias_name` = ?
		;";

		$query_domain = "
			SELECT
				COUNT(`alias_id`) AS cnt
			FROM
				`domain_aliasses`
			WHERE
				`alias_name` = ?
		;";
	} else {
		$query_subdomain = "
			SELECT
				COUNT(`subdomain_id`) AS cnt
			FROM
				`subdomain`
			WHERE
				`domain_id` = ?
			AND
				`subdomain_name` = ?
		;";

		$query_domain = "
			SELECT
				COUNT(`domain_id`) AS cnt
			FROM
				`domain`
			WHERE
				`domain_name` = ?
		;";
	}
	$domain_name = $sub_name . "." . $dmn_name;

	$rs_subdomain = exec_query($sql, $query_subdomain, array($domain_id, $sub_name));
	$rs_domain = exec_query($sql, $query_domain, array($domain_name));

	$std_subs = array(
		'www', 'mail', 'webmail', 'pop', 'pop3', 'imap', 'smtp', 'pma', 'relay',
		'ftp', 'ns1', 'ns2', 'localhost'
	);

	if ($rs_subdomain->fields['cnt'] == 0
		&& $rs_domain->fields['cnt'] == 0
		&& !in_array($sub_name, $std_subs)
		&& $cfg->BASE_SERVER_VHOST != $domain_name
	) {
		return false;
	}

	return true;
}

/**
 * @param int $user_id
 * @param int $domain_id
 * @param <type> $sub_name
 * @param <type> $sub_mnt_pt
 * @param <type> $forward
 */
function subdomain_schedule($user_id, $domain_id, $sub_name, $sub_mnt_pt, $forward, $sub_id=null) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$status_add = $cfg->ITEM_ADD_STATUS;

	if ($_POST['dmn_type'] == 'als') {
		$query = "
			INSERT INTO
				subdomain_alias
					(alias_id,
					subdomain_alias_name,
					subdomain_alias_mount,
					subdomain_alias_url_forward,
					status,subdomain_id)
			VALUES
				(?, ?, ?, ?, ?, ?)
		;";
		exec_query($sql, $query, array($domain_id, $sub_name, $sub_mnt_pt, $forward, $status_add, $sub_id));
	} else {
		$query = "
			INSERT INTO
				`subdomain`
					(`domain_id`,
					`subdomain_name`,
					`subdomain_mount`,
					`subdomain_url_forward`,
					`status`)
			VALUES
				(?, ?, ?, ?, ?)
		;";
		exec_query($sql, $query, array($domain_id, $sub_name, $sub_mnt_pt, $forward, $status_add));
	}

	update_reseller_c_props(get_reseller_id($domain_id));

//	$subdomain_id = $sql->insertId();

	// We do not need to create the default mail addresses, subdomains are
	// related to their domains.

	write_log($_SESSION['user_logged'] . ": adds new subdomain: " . $sub_name);
	if ($_POST['dmn_type'] == 'als') {
		send_request('110 DOMAIN alias '. $domain_id);
	} else {
		send_request('110 DOMAIN domain '. $domain_id);
	}
}

/**
 * @global <type> $validation_err_msg
 * @param $err_sub
 * @param int $user_id
 * @param $dmn_name
 * @return void <type>
 */
function check_subdomain_data(&$err_sub, $user_id, $dmn_name) {
	global $validation_err_msg;

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');
//	$vfs = new EasySCP_VirtualFileSystem($dmn_name, $sql);

	$dmn_id = $domain_id = get_user_domain_id($user_id);

	if (isset($_POST['uaction']) && $_POST['uaction'] === 'add_subd') {

		if (empty($_POST['subdomain_name'])) {
			 $err_sub = tr('Please specify subdomain name!');
			return;
		}
		$sub_name = strtolower($_POST['subdomain_name']);

		if ($_POST['status'] == 1) {
			$forward = clean_input($_POST['forward']);
			$forward_prefix = clean_input($_POST['forward_prefix']);
		} else {
			$forward = 'no';
			$forward_prefix = '';
		}

		// Should be perfomed after domain names syntax validation now
		//$sub_name = encode_idna($sub_name);

		if (isset($_POST['subdomain_mnt_pt']) && $_POST['subdomain_mnt_pt'] !== '') {
			$sub_mnt_pt = array_encode_idna(strtolower($_POST['subdomain_mnt_pt']), true);
		} else {
			$sub_mnt_pt = "/";
		}

		if ($_POST['dmn_type'] === 'als') {

			if (!isset($_POST['als_id'])) {
				$err_sub = tr('No valid alias domain selected!');
				return;
			}

//			$query_alias = "
//				SELECT
//					`alias_mount`
//				FROM
//					`domain_aliasses`
//				WHERE
//					`alias_id` = ?
//			;";
//
//			$rs = exec_query($sql, $query_alias, $_POST['als_id']);
//
//			$als_mnt = $rs->fields['alias_mount'];

			$query_dmn = "
				SELECT
					domain_name
				FROM 
					domain_aliasses,
					domain
				WHERE
					domain_aliasses.domain_id = domain.domain_id
				AND
					alias_id = ?
			";
			
			$dmn_rs = exec_query($sql, $query_dmn, $_POST['als_id']);
			
			$master_dmn_name = $dmn_rs->fields['domain_name'];
			if ($sub_mnt_pt[0] != '/')
				$sub_mnt_pt = '/'.$sub_mnt_pt;

//			$sub_mnt_pt = $als_mnt.$sub_mnt_pt;
			$sub_mnt_pt = str_replace('//', '/', $sub_mnt_pt);
			$domain_id = $_POST['als_id'];
			$sub_mnt_path = $cfg->APACHE_WWW_DIR . '/' .$master_dmn_name . $sub_mnt_pt;
		} else {
			$sub_mnt_path = $cfg->APACHE_WWW_DIR . '/' . $dmn_name . $sub_mnt_pt;
		}

		// First check if input string is a valid domain names
		if (!validates_subdname($sub_name, decode_idna($dmn_name))) {
			$err_sub = $validation_err_msg;
			return;
		}

		// Should be perfomed after domain names syntax validation now
		$sub_name = encode_idna($sub_name);

		if (subdmn_exists($user_id, $domain_id, $sub_name)) {
			$err_sub = tr('Subdomain already exists or is not allowed!');
		} elseif ($_POST['dmn_type']!='als' && mount_point_exists($dmn_id, array_encode_idna($sub_mnt_pt, true))) {
			$err_sub = tr('Mount point already in use!');
		} elseif ($_POST['dmn_type']!='als' && send_request('160 SYSTEM direxists ' . array_encode_idna($sub_mnt_path, true))) {
			$err_sub = tr("Can't use an existing folder as mount point!");
//		}elseif ($vfs->exists($sub_mnt_pt)) {
//			$err_sub = tr("Can't use an existing folder as mount point!");
		} elseif ($_POST['dmn_type']!='als' && !validates_mpoint($sub_mnt_pt)) {
			$err_sub = tr('Incorrect mount point syntax!');
		} elseif ($_POST['status'] == 1) {
			$surl = @parse_url($forward_prefix.decode_idna($forward));
			if ($surl === false) {
				$err_sub = tr('Wrong domain part in forward URL!');
			} else {
				$domain = $surl['host'];
				if (substr_count($domain, '.') <= 2) {
					$ret = validates_dname($domain);
				} else {
					$ret = validates_dname($domain, true);
				}

				if (!$ret) {
					$err_sub = tr('Wrong domain part in forward URL!');
				} else {
					$domain = encode_idna($surl['host']);
					$forward = $surl['scheme'].'://';
					if (isset($surl['user'])) {
						$forward .= $surl['user'] . (isset($surl['pass']) ? ':' . $surl['pass'] : '') .'@';
					}
					$forward .= $domain;
					if (isset($surl['port'])) {
						$forward .= ':'.$surl['port'];
					}
					if (isset($surl['path'])) {
						$forward .= $surl['path'];
					} else {
						$forward .= '/';
					}
					if (isset($surl['query'])) {
						$forward .= '?'.$surl['query'];
					}
					if (isset($surl['fragment'])) {
						$forward .= '#'.$surl['fragment'];
					}
				}
			}
		} else {
			// now let's fix the mountpoint
			$sub_mnt_pt = array_encode_idna($sub_mnt_pt, true);
		}
		if ('_off_' !== $err_sub) {
			return;
		}
		$subdomain_id = (isset($_POST['subdmn_id'])) ? $_POST['subdmn_id'] : NULL;
		subdomain_schedule($user_id, $domain_id, $sub_name, $sub_mnt_pt, $forward, $subdomain_id);
		set_page_message(tr('Subdomain scheduled for addition!'), 'success');
		user_goto('domains_manage.php');
	}
}
?>
