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
$template = 'client/ftp_add.tpl';

// dynamic page data.

gen_page_ftp_acc_props($tpl, $sql, $_SESSION['user_id']);

// static page messages
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('EasySCP - Client/Add FTP User'),
		'TR_ADD_FTP_USER' => tr('Add FTP user'),
		'TR_USERNAME' => tr('Username'),
		'TR_TO_MAIN_DOMAIN' => tr('To main domain'),
		'TR_TO_DOMAIN_ALIAS' => tr('To domain alias'),
		'TR_TO_SUBDOMAIN' => tr('To subdomain'),
		'TR_PASSWORD' => tr('Password'),
		'TR_PASSWORD_REPEAT' => tr('Repeat password'),
		'TR_USE_OTHER_DIR' => tr('Use other dir'),
		'TR_ADD' => tr('Add'),
		'CHOOSE_DIR' => tr('Choose dir'),
		'FTP_SEPARATOR' => $cfg->FTP_USERNAME_SEPARATOR
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_ftp_accounts.tpl');
gen_client_menu($tpl, 'client/menu_ftp_accounts.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

// page functions.

function get_alias_mount_point($sql, $alias_name) {
	$query = "
		SELECT
			`alias_mount`
		FROM
			`domain_aliasses`
		WHERE
			`alias_name` = ?
	";

	$rs = exec_query($sql, $query, $alias_name);
	return $rs->fields['alias_mount'];
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param string $dmn_name
 * @param string $post_check
 */
function gen_page_form_data($tpl, $dmn_name, $post_check) {

	$cfg = EasySCP_Registry::get('Config');

	$dmn_name = decode_idna($dmn_name);

	if ($post_check === 'no') {
		$tpl->assign(
			array(
				'USERNAME' => '',
				'DOMAIN_NAME' => tohtml($dmn_name),
				'DMN_TYPE_CHECKED' => $cfg->HTML_CHECKED,
				'ALS_TYPE_CHECKED' => '',
				'SUB_TYPE_CHECKED' => '',
				'OTHER_DIR' => '',
				'USE_OTHER_DIR_CHECKED' => ''
			)
		);
	} else {
		$tpl->assign(
			array(
				'USERNAME' => clean_input($_POST['username'], true),
				'DOMAIN_NAME' => tohtml($dmn_name),
				'DMN_TYPE_CHECKED' => ($_POST['dmn_type'] === 'dmn') ? $cfg->HTML_CHECKED : '',
				'ALS_TYPE_CHECKED' => ($_POST['dmn_type'] === 'als') ? $cfg->HTML_CHECKED : '',
				'SUB_TYPE_CHECKED' => ($_POST['dmn_type'] === 'sub') ? $cfg->HTML_CHECKED : '',
				'OTHER_DIR' => clean_input($_POST['other_dir'], true),
				'USE_OTHER_DIR_CHECKED' => (isset($_POST['use_other_dir']) && $_POST['use_other_dir'] === 'on') ? $cfg->HTML_CHECKED : ''
			)
		);
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $dmn_id
 * @param string $post_check
 */
function gen_dmn_als_list($tpl, $sql, $dmn_id, $post_check) {

	$cfg = EasySCP_Registry::get('Config');

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
	";

	$rs = exec_query($sql, $query, array($dmn_id, $ok_status));
	if ($rs->recordCount() != 0) {
		$first_passed = false;
		while (!$rs->EOF) {
			if ($post_check === 'yes') {
				$als_id = (!isset($_POST['als_id'])) ? '' : $_POST['als_id'];
				$als_selected = ($als_id == $rs->fields['alias_name'])
					? $cfg->HTML_SELECTED
					: '';
			} else {
				$als_selected = (!$first_passed) ? $cfg->HTML_SELECTED : '';
			}

			$als_menu_name = decode_idna($rs->fields['alias_name']);

			$tpl->append(
				array(
					'ALS_ID' => tohtml($rs->fields['alias_name']),
					'ALS_SELECTED' => $als_selected,
					'ALS_NAME' => tohtml($als_menu_name)
				)
			);

			$rs->moveNext();

			if (!$first_passed) $first_passed = true;
		}
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $dmn_id
 * @param string $dmn_name
 * @param string $post_check
 */
function gen_dmn_sub_list($tpl, $sql, $dmn_id, $dmn_name, $post_check) {

	$cfg = EasySCP_Registry::get('Config');

	$ok_status = $cfg->ITEM_OK_STATUS;
	$query = "
		SELECT
			`subdomain_id` AS sub_id, `subdomain_name` AS sub_name
		FROM
			`subdomain`
		WHERE
			`domain_id` = ?
		AND
			`status` = ?
		ORDER BY
			`subdomain_name`
	";

	$rs = exec_query($sql, $query, array($dmn_id, $ok_status));

	if ($rs->recordCount() == 0) {
		$tpl->assign(
			array(
				'SUB_ID' => 'n/a',
				'SUB_SELECTED' => $cfg->HTML_SELECTED,
				'SUB_NAME' => tr('Empty list')
			)
		);

		$tpl->assign('TO_SUBDOMAIN', '');
		$_SESSION['subdomain_count'] = "no";
	} else {
		$first_passed = false;
		while (!$rs->EOF) {
			if ($post_check === 'yes') {
				$sub_id = (!isset($_POST['sub_id'])) ? '' : $_POST['sub_id'];
				$sub_selected = ($sub_id == $rs->fields['sub_name'])
					? $cfg->HTML_SELECTED
					: '';
			} else {
				$sub_selected = (!$first_passed) ? $cfg->HTML_SELECTED : '';
			}

			$sub_menu_name = decode_idna($rs->fields['sub_name']);
			$dmn_menu_name = decode_idna($dmn_name);
			$tpl->assign(
				array(
					'SUB_ID' => tohtml($rs->fields['sub_name']),
					'SUB_SELECTED' => $sub_selected,
					'SUB_NAME' => tohtml($sub_menu_name . '.' . $dmn_menu_name)
				)
			);
			$rs->moveNext();
			if (!$first_passed) $first_passed = true;
		}
	}
}

function get_ftp_user_gid($sql, $dmn_name, $ftp_user) {

	global $last_gid, $max_gid;

	$query = "SELECT `gid`, `members` FROM `ftp_group` WHERE `groupname` = ?";

	$rs = exec_query($sql, $query, $dmn_name);

	if ($rs->recordCount() == 0) { // there is no such group. we'll need a new one.
		$temp_dmn_props = get_domain_default_props($_SESSION['user_id']);

		$query = "
			INSERT INTO ftp_group
				(`groupname`, `gid`, `members`)
			VALUES
				(?, ?, ?)
		";

		exec_query($sql, $query, array($dmn_name, $temp_dmn_props['domain_gid'], $ftp_user));
		// add entries in the quota tables
		// first check if we have it by one or other reason
		$query = "SELECT COUNT(`name`) AS cnt FROM `quotalimits` WHERE `name` = ?";
		$rs = exec_query($sql, $query, $temp_dmn_props['domain_name']);
		if ($rs->fields['cnt'] == 0) {
			// ok insert it
			if ($temp_dmn_props['domain_disk_limit'] == 0) {
				$dlim = 0;
			} else {
				$dlim = $temp_dmn_props['domain_disk_limit'] * 1024 * 1024;
			}

			$query = "
				INSERT INTO `quotalimits`
					(`name`, `quota_type`, `per_session`, `limit_type`,
					`bytes_in_avail`, `bytes_out_avail`, `bytes_xfer_avail`,
					`files_in_avail`, `files_out_avail`, `files_xfer_avail`)
				VALUES
					(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
			";

			exec_query($sql, $query, array($temp_dmn_props['domain_name'], 'group', 'false', 'hard', $dlim, 0, 0, 0, 0, 0));
		}

		return $temp_dmn_props['domain_gid'];
	} else {
		$ftp_gid = $rs->fields['gid'];
		$members = $rs->fields['members'];

		if (preg_match("/" . $ftp_user . "/", $members) == 0) {
			$members .= ",$ftp_user";
		}

		$query = "
			UPDATE
				`ftp_group`
			SET
				`members` = ?
			WHERE
				`gid` = ?
			AND
				`groupname` = ?
		";

		exec_query($sql, $query, array($members, $ftp_gid, $dmn_name));
		return $ftp_gid;
	}
}

function get_ftp_user_uid($sql, $dmn_name, $ftp_user, $ftp_user_gid) {

	global $max_uid;

	$query = "
		SELECT
			`uid`
		FROM
			`ftp_users`
		WHERE
			`userid` = ?
		AND
			`gid` = ?
	";

	$rs = exec_query($sql, $query, array($ftp_user, $ftp_user_gid));
	if ($rs->recordCount() > 0) {
		set_page_message(tr('FTP account already exists!'), 'error');
		return -1;
	}

	$temp_dmn_props = get_domain_default_props($_SESSION['user_id']);

	return $temp_dmn_props['domain_uid'];
}

function add_ftp_user($sql, $dmn_name) {

	$cfg = EasySCP_Registry::get('Config');

	$username = strtolower(clean_input($_POST['username']));

	if (!validates_username($username)) {
		set_page_message(tr("Incorrect username length or syntax!"), 'warning');
		return;
	}

	// Set default values ($ftp_home may be overwritten if user
	// has specified a mount point)
	switch ($_POST['dmn_type']) {
		// Default moint point for a domain
		case 'dmn':
			$ftp_user = $username . $cfg->FTP_USERNAME_SEPARATOR . $dmn_name;
			$ftp_home = $cfg->FTP_HOMEDIR . "/$dmn_name";
			break;
		// Default mount point for an alias domain
		case 'als':
			$ftp_user = $username . $cfg->FTP_USERNAME_SEPARATOR . $_POST['als_id'];
			$alias_mount_point = get_alias_mount_point($sql, $_POST['als_id']);
			$ftp_home = $cfg->FTP_HOMEDIR . "/$dmn_name" . $alias_mount_point;
			break;
		// Default mount point for a subdomain
		case 'sub':
			$ftp_user = $username . $cfg->FTP_USERNAME_SEPARATOR . $_POST['sub_id'] . '.' . $dmn_name;
			$ftp_home = $cfg->FTP_HOMEDIR . "/$dmn_name/" . clean_input($_POST['sub_id']);
			break;
		// Unknown domain type (?)
		default:
			set_page_message(tr('Unknown domain type'), 'error');
			return;
			break;
	}
	// User-specified mount point
	if (isset($_POST['use_other_dir']) && $_POST['use_other_dir'] === 'on') {
		$ftp_vhome = clean_input($_POST['other_dir'], false);
		// Strip possible double-slashes
		$ftp_vhome = str_replace('//', '/', $ftp_vhome);
		// Check for updirs ".."
		$res = preg_match("/\.\./", $ftp_vhome);
		if ($res !== 0) {
			set_page_message(
				tr('Incorrect mount point length or syntax'),
				'error'
			);
			return;
		}
		$ftp_home = $cfg->FTP_HOMEDIR . "/$dmn_name/" . $ftp_vhome;
		// Strip possible double-slashes
		$ftp_home = str_replace('//', '/', $ftp_home);
		// Check for $ftp_vhome existence
		// Create a virtual filesystem (it's important to use =&!)
		$vfs = new EasySCP_VirtualFileSystem($dmn_name, $sql);
		// Check for directory existence
		$res = $vfs->exists($ftp_vhome);

		if (!$res) {
			set_page_message(tr('%s does not exist', $ftp_vhome), 'error');
			return;
		}
	} // End of user-specified mount-point

	$ftp_gid = get_ftp_user_gid($sql, $dmn_name, $ftp_user);
	$ftp_uid = get_ftp_user_uid($sql, $dmn_name, $ftp_user, $ftp_gid);

	if ($ftp_uid == -1) return;

	$ftp_shell		 = $cfg->CMD_SHELL;
	$ftp_passwd		 = crypt_user_pass_with_salt($_POST['pass']);
	$ftp_loginpasswd = DB::encrypt_data($_POST['pass']);

	$query = "
		INSERT INTO ftp_users
			(`userid`, `passwd`, `net2ftppasswd`, `uid`, `gid`, `shell`, `homedir`)
		VALUES
			(?, ?, ?, ?, ?, ?, ?)
	";

	exec_query($sql, $query, array($ftp_user, $ftp_passwd, $ftp_loginpasswd, $ftp_uid, $ftp_gid, $ftp_shell, $ftp_home));

	$domain_props = get_domain_default_props($_SESSION['user_id']);
	update_reseller_c_props($domain_props['domain_created_id']);

	write_log($_SESSION['user_logged'] . ": add new FTP account: $ftp_user");
	set_page_message(tr('FTP account added!'), 'success');
	user_goto('ftp_accounts.php');
}

function check_ftp_acc_data($tpl, $sql, $dmn_id, $dmn_name) {

	$cfg = EasySCP_Registry::get('Config');

	if (!isset($_POST['username']) || $_POST['username'] === '') {
		set_page_message(tr('Please enter FTP account username!'), 'warning');
		return;
	}

	if (!isset($_POST['pass']) || empty($_POST['pass'])
		|| !isset($_POST['pass_rep'])
		|| $_POST['pass_rep'] === '') {
		set_page_message(tr('Password is missing!'), 'warning');
		return;
	}

	if ($_POST['pass'] !== $_POST['pass_rep']) {
		set_page_message(tr('Entered passwords do not match!'), 'warning');
		return;
	}

	if (!chk_password($_POST['pass'])) {
		if ($cfg->PASSWD_STRONG) {
			set_page_message(
				sprintf(
					tr('The password must be at least %s chars long and contain letters and numbers to be valid.'),
					$cfg->PASSWD_CHARS
				),
				'warning'
			);
		} else {
			set_page_message(
				sprintf(
					tr('Password data is shorter than %s signs or includes not permitted signs!'),
					$cfg->PASSWD_CHARS
				),
				'warning'
			);
		}
		return;
	}

	if ($_POST['dmn_type'] === 'sub' && $_POST['sub_id'] === 'n/a') {
		set_page_message(
			tr('Subdomain list is empty! You cannot add FTP accounts there!'),
			'warning'
		);
		return;
	}

	if ($_POST['dmn_type'] === 'als' && $_POST['als_id'] === 'n/a') {
		set_page_message(
			tr('Alias list is empty! You cannot add FTP accounts there!'),
			'warning'
		);
		return;
	}

	if (isset($_POST['use_other_dir']) && $_POST['use_other_dir'] === 'on' &&
		empty($_POST['other_dir'])) {
		set_page_message(
			tr('Please specify other FTP account dir!'),
			'warning'
		);
		return;
	}

	add_ftp_user($sql, $dmn_name);
}

function gen_page_ftp_acc_props($tpl, $sql, $user_id) {
	$dmn_props = get_domain_default_props($user_id);

	list($ftp_acc_cnt, , , ) = get_domain_running_ftp_acc_cnt($sql, $dmn_props['domain_id']);

	if ($dmn_props['domain_ftpacc_limit'] != 0 && $ftp_acc_cnt >= $dmn_props['domain_ftpacc_limit']) {
		set_page_message(tr('FTP accounts limit reached!'), 'warning');
		user_goto('ftp_accounts.php');
	} else {
		if (!isset($_POST['uaction'])) {
			gen_page_form_data($tpl, $dmn_props['domain_name'], 'no');
			gen_dmn_als_list($tpl, $sql, $dmn_props['domain_id'], 'no');
			gen_dmn_sub_list($tpl, $sql, $dmn_props['domain_id'], $dmn_props['domain_name'], 'no');
			gen_page_js($tpl);
		} else if (isset($_POST['uaction']) && $_POST['uaction'] === 'add_user') {
			gen_page_form_data($tpl, $dmn_props['domain_name'], 'yes');
			gen_dmn_als_list($tpl, $sql, $dmn_props['domain_id'], 'yes');
			gen_dmn_sub_list($tpl, $sql, $dmn_props['domain_id'], $dmn_props['domain_name'], 'yes');
			check_ftp_acc_data($tpl, $sql, $dmn_props['domain_id'], $dmn_props['domain_name']);
		}
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function gen_page_js($tpl) {

	if (isset($_SESSION['subdomain_count'])
		&& isset($_SESSION['alias_count'])) { // no subdomains and no alias
		$tpl->assign('JS_TO_SUBDOMAIN', '');
		$tpl->assign('JS_TO_ALIAS_DOMAIN', '');
		$tpl->assign('JS_TO_ALL_DOMAIN', '');
	} else if (isset($_SESSION['subdomain_count'])
		&& !isset($_SESSION['alias_count'])) { // no subdomains - alaias available
		$tpl->assign('JS_NOT_DOMAIN', '');
		$tpl->assign('JS_TO_SUBDOMAIN', '');
		$tpl->assign('JS_TO_ALL_DOMAIN', '');
	} else if (!isset($_SESSION['subdomain_count'])
		&& isset($_SESSION['alias_count'])) { // no alias - subdomain available
		$tpl->assign('JS_NOT_DOMAIN', '');
		$tpl->assign('JS_TO_ALIAS_DOMAIN', '');
		$tpl->assign('JS_TO_ALL_DOMAIN', '');
	} else { // there are subdomains and aliases
		$tpl->assign('JS_NOT_DOMAIN', '');
		$tpl->assign('JS_TO_SUBDOMAIN', '');
		$tpl->assign('JS_TO_ALIAS_DOMAIN', '');
	}

	unset($GLOBALS['subdomain_count']);
	unset($GLOBALS['alias_count']);
	unset($_SESSION['subdomain_count']);
	unset($_SESSION['alias_count']);
}
?>