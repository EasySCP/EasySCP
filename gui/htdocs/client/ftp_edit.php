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

if (isset($_GET['id'])) {
	$ftp_acc = $_GET['id'];
} else if (isset($_POST['id'])) {
	$ftp_acc = $_POST['id'];
} else {
	user_goto('ftp_accounts.php');
}

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'client/ftp_edit.tpl';

// dynamic page data.

$query = "
	SELECT
		`domain_name`
	FROM
		`domain`
	WHERE
		`domain_admin_id` = ?
";

$rs = exec_query($sql, $query, $_SESSION['user_id']);

$dmn_name = $rs->fields['domain_name'];

check_ftp_perms($sql, $ftp_acc);
gen_page_dynamic_data($tpl, $sql, $ftp_acc);
update_ftp_account($sql, $ftp_acc, $dmn_name);

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Client/Edit FTP Account'),
		'TR_EDIT_FTP_USER' 		=> tr('Edit FTP user'),
		'TR_FTP_ACCOUNT' 		=> tr('FTP account'),
		'TR_PASSWORD' 			=> tr('Password'),
		'TR_PASSWORD_REPEAT' 	=> tr('Repeat password'),
		'TR_USE_OTHER_DIR' 		=> tr('Use other dir'),
		'TR_EDIT' 				=> tr('Save changes'),
		'CHOOSE_DIR' 			=> tr('Choose dir'),
		// The entries below are for Demo versions only
		'PASSWORD_DISABLED'		=> tr('Password change is deactivated!'),
		'DEMO_VERSION'			=> tr('Demo Version!')
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
 * @param string $ftp_acc
 */
function gen_page_dynamic_data($tpl, $sql, $ftp_acc) {

	$cfg = EasySCP_Registry::get('Config');

	$query = "
		SELECT
			`homedir`
		FROM
			`ftp_users`
		WHERE
			`userid` = ?
	";

	$rs = exec_query($sql, $query, $ftp_acc);

	$homedir = $rs->fields['homedir'];
	$domain_ftp = $_SESSION['user_logged'];
	$nftp_dir = $cfg->FTP_HOMEDIR . "/" . $domain_ftp;

	if ($nftp_dir == $homedir) {
		$odir = '';
		$oins = '';
	} else {
		$odir = $cfg->HTML_CHECKED;
		$oins = substr($homedir, strlen($nftp_dir));
	}

	$tpl->assign(
		array(
			'FTP_ACCOUNT' => $ftp_acc,
			'ID' => $ftp_acc,
			'USE_OTHER_DIR_CHECKED' => $odir,
			'OTHER_DIR' => $oins
		)
	);
}

function update_ftp_account($sql, $ftp_acc, $dmn_name) {

	global $other_dir;
	$cfg = EasySCP_Registry::get('Config');

	// Create a virtual filesystem (it's important to use =&!)
	$vfs = new EasySCP_VirtualFileSystem($dmn_name, $sql);

	if (isset($_POST['uaction']) && $_POST['uaction'] === 'edit_user') {
		if (!empty($_POST['pass']) || !empty($_POST['pass_rep'])) {
			if ($_POST['pass'] !== $_POST['pass_rep']) {
				set_page_message(
					tr('Entered passwords do not match!'),
					'warning'
				);
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

			$pass		= crypt_user_pass_with_salt($_POST['pass']);
			$loginpass	= DB::encrypt_data($_POST['pass']);

			if (isset($_POST['use_other_dir']) && $_POST['use_other_dir'] === 'on') {

				$other_dir = clean_input($_POST['other_dir']);

				$rs = $vfs->exists($other_dir);
				if (!$rs) {
					set_page_message(
						tr('%s does not exist', clean_input($_POST['other_dir'])),
						'warning'
					);
					return;
				} // domain_id

				// append the full path (vfs is always checking per ftp so it's logged
				// in in the root of the user (no absolute paths are allowed here!)

				$other_dir = $cfg->FTP_HOMEDIR . "/" . $_SESSION['user_logged']
							. clean_input($_POST['other_dir']);

				$query = "
					UPDATE
						`ftp_users`
					SET
						`passwd` = ?,
						`net2ftppasswd` = ?,
						`homedir` = ?
					WHERE
						`userid` = ?
				";

				$param = array($pass, $loginpass, $other_dir, $ftp_acc);
			} else {
				$query = "
					UPDATE
						`ftp_users`
					SET
						`passwd` = ?,
						`net2ftppasswd` = ?
					WHERE
						`userid` = ?
				";
				$param = array($pass, $loginpass, $ftp_acc);
			}
			exec_query($sql, $query, $param);

			write_log($_SESSION['user_logged'] . ": updated FTP " . $ftp_acc . " account data");
			set_page_message(tr('FTP account data updated!'), 'success');
			user_goto('ftp_accounts.php');
		} else {
			if (isset($_POST['use_other_dir']) && $_POST['use_other_dir'] === 'on') {
				$other_dir = clean_input($_POST['other_dir']);
				// Strip possible double-slashes
				$other_dir = str_replace('//', '/', $other_dir);
				// Check for updirs ".."
				$res = preg_match("/\.\./", $other_dir);
				if ($res !== 0) {
					set_page_message(
						tr('Incorrect mount point length or syntax'),
						'warning'
					);
					return;
				}

				// Check for $other_dir existence
				// Create a virtual filesystem (it's important to use =&!)
				$vfs = new EasySCP_VirtualFileSystem($dmn_name, $sql);
				// Check for directory existence
				$res = $vfs->exists($other_dir);
				if (!$res) {
					set_page_message(
						tr('%s does not exist', $other_dir),
						'error'
					);
					return;
				}
				$other_dir = $cfg->FTP_HOMEDIR . "/" . $_SESSION['user_logged'] . $other_dir;
			} else { // End of user-specified mount-point

				$other_dir = $cfg->FTP_HOMEDIR . "/" . $_SESSION['user_logged'];

			}
			$query = "
				UPDATE
					`ftp_users`
				SET
					`homedir` = ?
				WHERE
					`userid` = ?
			";

			exec_query($sql, $query, array($other_dir, $ftp_acc));
			set_page_message(tr('FTP account data updated!'), 'success');
			user_goto('ftp_accounts.php');
		}
	}
}
?>