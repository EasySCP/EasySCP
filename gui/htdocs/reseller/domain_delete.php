<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

require '../../include/easyscp-lib.php';

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'reseller/domain_delete.tpl';

if (isset($_SESSION['ddel']) && $_SESSION['ddel'] == "_yes_") {
	unset($_SESSION["ddel"]);
	user_goto('users.php?psi=last');
}

if (isset($_GET['domain_id']) && is_numeric($_GET['domain_id'])) {
	validate_domain_deletion(intval($_GET['domain_id']));
} else if (isset($_POST['domain_id']) && is_numeric($_POST['domain_id'])
	&& isset($_POST['delete']) && $_POST['delete'] == 1) {
	delete_domain((int)$_POST['domain_id'], 'users.php?psi=last', true);
} else {
	set_page_message(tr('Wrong domain ID!'), 'error');
	user_goto('users.php?psi=last');
}

// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('EasySCP - Delete Domain')
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_users_manage.tpl');
gen_reseller_menu($tpl, 'reseller/menu_users_manage.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

/**
 * Validate domain deletion, display all items to delete
 * @param integer $domain_id
 */
function validate_domain_deletion($domain_id) {
	global $tpl, $sql;

	$reseller = $_SESSION['user_id'];

	// check for domain owns
	$query = "SELECT `domain_id`, `domain_name` FROM `domain` WHERE `domain_id` = ? AND `domain_created_id` = ?";
	$res = exec_query($sql, $query, array($domain_id, $reseller));
	$data = $res->fetchRow();
	if ($data['domain_id'] == 0) {
		set_page_message(tr('Wrong domain ID!'), 'error');
		user_goto('users.php?psi=last');
	}

	$tpl->assign(
		array(
			'TR_DELETE_DOMAIN'	=> tr('Delete domain'),
			'TR_DOMAIN_SUMMARY'	=> tr('Domain summary:'),
			'TR_DOMAIN_EMAILS'	=> tr('Domain e-mails:'),
			'TR_DOMAIN_FTPS'	=> tr('Domain FTP accounts:'),
			'TR_DOMAIN_ALIASES'	=> tr('Domain aliases:'),
			'TR_DOMAIN_SUBS'	=> tr('Domain subdomains:'),
			'TR_DOMAIN_DBS'		=> tr('Domain databases:'),
			'TR_REALLY_WANT_TO_DELETE_DOMAIN'	=> tr('Do you really want to delete the entire domain? This operation cannot be undone!'),
			'TR_BUTTON_DELETE'	=> tr('Delete domain'),
			'TR_YES_DELETE_DOMAIN'	=> tr('Yes, delete the domain.'),
			'DOMAIN_NAME'		=> decode_idna($data['domain_name']),
			'DOMAIN_ID'			=> $data['domain_id']
		)
	);

	// check for mail acc in MAIN domain
	$query = "SELECT * FROM `mail_users` WHERE `domain_id` = ?";
	$res = exec_query($sql, $query, $domain_id);
	if (!$res->EOF) {
		while (!$res->EOF) {

			// Create mail type's text
			$mail_types = explode(',', $res->fields['mail_type']);
			$mdisplay_a = array();
			foreach ($mail_types as $mtype) {
				$mdisplay_a[] = user_trans_mail_type($mtype);
			}
			$mdisplay_txt = implode(', ', $mdisplay_a);

			$tpl->append(
				array(
					'MAIL_ADDR' => decode_idna($res->fields['mail_addr']),
					'MAIL_TYPE' => $mdisplay_txt
				)
			);

			$res->moveNext();
		}
	}

	// check for ftp acc in MAIN domain
	$query = "SELECT `ftp_users`.* FROM `ftp_users`, `domain` WHERE `domain`.`domain_id` = ? AND `ftp_users`.`uid` = `domain`.`domain_uid`";
	$res = exec_query($sql, $query, $domain_id);
	if (!$res->EOF) {
		while (!$res->EOF) {

			$tpl->append(
				array(
					'FTP_USER' => decode_idna($res->fields['userid']),
					'FTP_HOME' => tohtml($res->fields['homedir'])
				)
			);

			$res->moveNext();
		}
	}

	// check for alias domains
	$alias_a = array();
	$query = "SELECT * FROM `domain_aliasses` WHERE `domain_id` = ?";
	$res = exec_query($sql, $query, $domain_id);
	if (!$res->EOF) {
		while (!$res->EOF) {
			$alias_a[] = $res->fields['alias_id'];

			$tpl->append(
				array(
					'ALS_NAME' => decode_idna($res->fields['alias_name']),
					'ALS_MNT' => tohtml($res->fields['alias_mount'])
				)
			);

			$res->moveNext();
		}
	}

	// check for subdomains
	$any_sub_found = false;
	$query = "SELECT * FROM `subdomain` WHERE `domain_id` = ?";
	$res = exec_query($sql, $query, $domain_id);
	while (!$res->EOF) {
		$any_sub_found = true;
		$tpl->append(
			array(
				'SUB_NAME' => tohtml($res->fields['subdomain_name']),
				'SUB_MNT' => tohtml($res->fields['subdomain_mount'])
			)
		);

		$res->moveNext();
	}

	// Check subdomain_alias
	if (count($alias_a) > 0) {
		$query = "SELECT * FROM `subdomain_alias` WHERE `alias_id` IN (";
		$query .= implode(',', $alias_a);
		$query .= ")";
		$res = exec_query($sql, $query);
		while (!$res->EOF) {
			$tpl->append(
				array(
					'SUB_NAME' => tohtml($res->fields['subdomain_alias_name']),
					'SUB_MNT' => tohtml($res->fields['subdomain_alias_mount'])
				)
			);

			$res->moveNext();
		}
	}

	// Check for databases and -users
	$query = "SELECT * FROM `sql_database` WHERE `domain_id` = ?";
	$res = exec_query($sql, $query, $domain_id);
	if (!$res->EOF) {

		while (!$res->EOF) {

			$query = "SELECT * FROM `sql_user` WHERE `sqld_id` = ?";
			$ures = exec_query($sql, $query, $res->fields['sqld_id']);

			$users_a = array();
			while (!$ures->EOF) {
				$users_a[] = $ures->fields['sqlu_name'];
				$ures->moveNext();
			}
			$users_txt = implode(', ', $users_a);

			$tpl->append(
				array(
					'DB_NAME' => tohtml($res->fields['sqld_name']),
					'DB_USERS' => tohtml($users_txt)
				)
			);

			$res->moveNext();
		}
	}
}
?>