<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2010 by ispCP | http://isp-control.net
 * @copyright 	2010-2019 by Easy Server Control Panel - http://www.easyscp.net
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 *
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Original Code is "VHCS - Virtual Hosting Control System".
 *
 * The Initial Developer of the Original Code is moleSoftware GmbH.
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 *
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 *
 * Portions created by the EasySCP Team are Copyright (C) 2010-2019 by
 * Easy Server Control Panel. All Rights Reserved.
 */

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param string $menu_file
 */
function gen_admin_mainmenu($tpl, $menu_file) {

	$cfg = EasySCP_Registry::get('Config');

	$tpl->assign(
		array(
			'TR_MENU_GENERAL_INFORMATION'	=> tr('General information'),
			'TR_MENU_MANAGE_USERS'			=> tr('Manage users'),
			'TR_MENU_HOSTING_PLANS'			=> tr('Manage hosting plans'),
			'TR_MENU_SYSTEM_TOOLS'			=> tr('System tools'),
			'TR_MENU_STATISTICS'			=> tr('Statistics'),
			'TR_MENU_SUPPORT_SYSTEM'		=> tr('Support system'),
			'TR_MENU_SETTINGS'				=> tr('Settings')
		)
	);

	if($cfg->EasySCP_SUPPORT_SYSTEM) {
		$tpl->assign('SUPPORT_SYSTEM', true);
	}

	if(strtolower($cfg->HOSTING_PLANS_LEVEL) == 'admin') {
		$tpl->assign('HOSTING_PLANS', true);
	}

	$tpl->assign('MAIN_MENU', $menu_file);
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param string $menu_file
 */
function gen_admin_menu($tpl, $menu_file) {

	$cfg = EasySCP_Registry::get('Config');

	$tpl->assign(
		array(
			'TR_MENU_OVERVIEW'				=> tr('Overview'),
			'TR_MENU_CHANGE_PASSWORD'		=> tr('Change password'),
			'TR_MENU_CHANGE_PERSONAL_DATA'	=> tr('Change personal data'),
			'TR_MENU_LANGUAGE'				=> tr('Language'),
			'TR_MENU_SERVER_STATUS'			=> tr('Server status'),
			'TR_MENU_ADMIN_LOG'				=> tr('Admin log'),

			'TR_MENU_ADD_ADMIN'				=> tr('Add admin'),
			'TR_MENU_ADD_RESELLER'			=> tr('Add reseller'),
			'TR_MENU_RESELLER_ASIGNMENT'	=> tr('Reseller assignment'),
			'TR_MENU_USER_ASIGNMENT'		=> tr('User assignment'),
			'TR_MENU_CIRCULAR'				=> tr('Email marketing'),
			'TR_MENU_MANAGE_SESSIONS'		=> tr('User sessions'),

			'TR_MAINTENANCEMODE'			=> tr('Maintenance mode'),
			'TR_MANAGE_SSL'					=> tr('Manage SSL certificate'),
			'TR_MENU_EasySCP_UPDATE'		=> tr('EasySCP updates'),
			'TR_MENU_EasySCP_DEBUGGER'		=> tr('EasySCP debugger'),
			'TR_MENU_ROOTKIT_LOG'			=> tr('Rootkit Log'),
			'TR_MENU_CRONJOB_OVERVIEW'		=> tr('Cronjob Overview'),
			'TR_MENU_CRONJOB_ADD'			=> tr('Add Cronjob'),

			'TR_SERVER_STATISTICS'			=> tr('Server statistics'),
			'TR_MENU_RESELLER_STATISTICS'	=> tr('Reseller statistics'),
			'TR_MENU_IP_USAGE'				=> tr('IP Usage'),

			'TR_OPEN_TICKETS'				=> tr('Open tickets'),
			'TR_CLOSED_TICKETS'				=> tr('Closed tickets'),

			'TR_GENERAL_SETTINGS'			=> tr('General settings'),
			'TR_MENU_MANAGE_IPS'			=> tr('Manage IPs'),
			'TR_MENU_SERVER_TRAFFIC_SETTINGS'=> tr('Server traffic settings'),
			'TR_MENU_EMAIL_SETUP'			=> tr('Email setup'),
			'TR_MENU_LOSTPW_EMAIL'			=> tr('Lostpw email setup'),
			'TR_SERVERPORTS'				=> tr('Server ports'),

			'TR_MENU_LOGOUT'				=> tr('Logout'),
			'VERSION'						=> EasyConfig::$cfg->{'Version'},
			'BUILDDATE'						=> EasyConfig::$cfg->{'BuildDate'}
		)
	);

	if($cfg->EasySCP_SUPPORT_SYSTEM) {
		$tpl->assign('SUPPORT_SYSTEM', true);
	}

	if(strtolower($cfg->HOSTING_PLANS_LEVEL) == 'admin') {
		$tpl->assign('HOSTING_PLANS', true);
	}

	$tpl->assign('MENU', $menu_file);
}

function get_sql_user_count() {

	$sql_query = "
		SELECT DISTINCT
			sqlu_name
		FROM
			sql_user;
	";

	$rs = DB::query($sql_query);

	return $rs->rowCount();
}

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function get_admin_general_info($tpl) {

	$tpl->assign(
		array(
			'TR_GENERAL_INFORMATION'=> tr('General information'),
			'TR_ACCOUNT_NAME'		=> tr('Account name'),
			'TR_ADMIN_USERS'		=> tr('Admin users'),
			'TR_RESELLER_USERS'		=> tr('Reseller users'),
			'TR_NORMAL_USERS'		=> tr('Normal users'),
			'TR_DOMAINS'			=> tr('Domains'),
			'TR_SUBDOMAINS'			=> tr('Subdomains'),
			'TR_DOMAINS_ALIASES'	=> tr('Domain aliases'),
			'TR_MAIL_ACCOUNTS'		=> tr('Mail accounts'),
			'TR_FTP_ACCOUNTS'		=> tr('FTP accounts'),
			'TR_SQL_DATABASES'		=> tr('SQL databases'),
			'TR_SQL_USERS'			=> tr('SQL users'),
			'TR_SYSTEM_MESSAGES'	=> tr('System messages'),
			'TR_NO_NEW_MESSAGES'	=> tr('No new messages'),
			'TR_SERVER_TRAFFIC'		=> tr('Server traffic')
		)
	);

	$show_total_emails = records_count('mail_users', "mail_type NOT RLIKE '_catchall'", '');

	$tpl->assign(
		array(
			'ACCOUNT_NAME'		=> $_SESSION['user_logged'],
			'ADMIN_USERS'		=> records_count('admin', 'admin_type', 'admin'),
			'RESELLER_USERS'	=> records_count('admin', 'admin_type', 'reseller'),
			'NORMAL_USERS'		=> records_count('admin', 'admin_type', 'user'),
			'DOMAINS'			=> records_count('domain', '', ''),
			'SUBDOMAINS'		=> records_count('subdomain', '', '') + records_count('subdomain_alias', 'subdomain_alias_id', '', ''),
			'DOMAINS_ALIASES'	=> records_count('domain_aliasses', '', ''),
			'MAIL_ACCOUNTS'		=> $show_total_emails,
			'FTP_ACCOUNTS'		=> records_count('ftp_users', '', ''),
			'SQL_DATABASES'		=> records_count('sql_database', '', ''),
			'SQL_USERS'			=> get_sql_user_count()
		)
	);
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @return void
 */
function gen_admin_list($tpl, $sql) {

	$cfg = EasySCP_Registry::get('Config');

	$query = "
		SELECT
			t1.admin_id,
			t1.admin_name,
			t1.domain_created,
			IFNULL(t2.admin_name, '') AS created_by
		FROM
			admin AS t1
		LEFT JOIN
			admin AS t2 ON t1.created_by = t2.admin_id
		WHERE
			t1.admin_type = 'admin'
		ORDER BY
			t1.admin_name
		ASC
		;
	";

	$rs = exec_query($sql, $query);

	if($rs->recordCount() == 0) {
		$tpl->assign(
			array(
				'ADMIN_MESSAGE' => tr('Administrators list is empty!'),
				'ADMIN_LIST' => ''
			)
		);
	} else {
		$tpl->assign(
			array(
				'TR_ADMIN_USERNAME' => tr('Username'),
				'TR_ADMIN_CREATED_ON' => tr('Creation date'),
				'TR_ADMIN_CREATED_BY' => tr('Created by'),
				'TR_ADMIN_OPTIONS' => tr('Options')
			)
		);

		while(!$rs->EOF) {
			$admin_created = $rs->fields['domain_created'];

			if($admin_created == 0) {
				$admin_created = tr('N/A');
			} else {
				$date_formt = $cfg->DATE_FORMAT;
				$admin_created = date($date_formt, $admin_created);
			}

			if($rs->fields['created_by'] == '' ||
				$rs->fields['admin_id'] == $_SESSION['user_id']) {
				$tpl->append(
					array(
						'ADMIN_DELETE_SHOW' => false,
						'URL_DELETE_ADMIN'	=> ''
					)
				);
			} else {
				$tpl->append(
					array(
						'ADMIN_DELETE_SHOW'	=> true,
						'URL_DELETE_ADMIN'	=> 'user_delete.php?delete_id=' . $rs->fields['admin_id'] . '&amp;delete_username=' . $rs->fields['admin_name']
					)
				);

			}

			$tpl->append(
				array(
					'ADMIN_USERNAME'	=> tohtml($rs->fields['admin_name']),
					'ADMIN_CREATED_ON'	=> tohtml($admin_created),
					'ADMIN_CREATED_BY'	=> (!is_null($rs->fields['created_by'])) ? tohtml($rs->fields['created_by']) : tr("System"),
					'URL_EDIT_ADMIN'	=> 'admin_edit.php?edit_id=' . $rs->fields['admin_id']
				)
			);

			$rs->moveNext();
		}
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 */
function gen_reseller_list($tpl, $sql) {

	$cfg = EasySCP_Registry::get('Config');

	$query = "
		SELECT
			`t1`.`admin_id`, `t1`.`admin_name`, `t1`.`domain_created`,
			IFNULL(t2.`admin_name`, '') AS created_by
		FROM
			`admin` AS `t1`
		LEFT JOIN
			`admin` AS `t2` ON `t1`.`created_by` = t2.`admin_id`
		WHERE
			`t1`.`admin_type` = 'reseller'
		ORDER BY
			`t1`.`admin_name`
		ASC
		;
	";

	$rs = exec_query($sql, $query);

	if($rs->recordCount() == 0) {
		$tpl->assign(
			array(
				'RSL_MESSAGE' => tr('Resellers list is empty!'),
				'RSL_LIST' => ''
			)
		);
	} else {
		$tpl->assign(
			array(
				'TR_RSL_USERNAME' => tr('Username'),
				'TR_RSL_CREATED_BY' => tr('Created by'),
				'TR_RSL_OPTIONS' => tr('Options')
			)
		);

		while(!$rs->EOF) {
			if($rs->fields['created_by'] == '') {
				$tpl->append(
					array(
						'RSL_DELETE_LINK' => '',
					)
				);
			} else {
				$tpl->append(
					array(
						'RSL_DELETE_SHOW' => '',
						'URL_DELETE_RSL' => 'user_delete.php?delete_id=' . $rs->fields['admin_id'] . '&amp;delete_username=' . $rs->fields['admin_name'],
						'URL_CHANGE_INTERFACE_RSL' => 'change_user_interface.php?to_id=' . $rs->fields['admin_id']
					)
				);
			}

			$reseller_created = $rs->fields['domain_created'];

			if($reseller_created == 0) {
				$reseller_created = tr('N/A');
			} else {
				$date_formt = $cfg->DATE_FORMAT;
				$reseller_created = date($date_formt, $reseller_created);
			}

			$tpl->append(
				array(
					'RSL_USERNAME' => tohtml($rs->fields['admin_name']),
					'RESELLER_CREATED_ON' => tohtml($reseller_created),
					'RSL_CREATED_BY' => tohtml($rs->fields['created_by']),
					'URL_EDIT_RSL' => 'reseller_edit.php?edit_id=' . $rs->fields['admin_id']
				)
			);

			$rs->moveNext();
		}
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 */
function gen_user_list($tpl, $sql) {

	$cfg = EasySCP_Registry::get('Config');

	$start_index = 0;
	$rows_per_page = $cfg->DOMAIN_ROWS_PER_PAGE;

	if(isset($_GET['psi'])) {
		$start_index = $_GET['psi'];
	}

	// Search request generated ?!
	if(isset($_POST['uaction']) && !empty($_POST['uaction'])) {
		$_SESSION['search_for'] = trim(clean_input($_POST['search_for']));
		$_SESSION['search_common'] = $_POST['search_common'];
		$_SESSION['search_status'] = $_POST['search_status'];
		$start_index = 0;
	} elseif(isset($_SESSION['search_for']) && !isset($_GET['psi'])) {
		// He have not got scroll through patient records.
		unset($_SESSION['search_for']);
		unset($_SESSION['search_common']);
		unset($_SESSION['search_status']);
	}

	$search_query = '';
	$count_query = '';

	if(isset($_SESSION['search_for'])) {
		gen_admin_domain_query(
			$search_query,
			$count_query,
			$start_index,
			$rows_per_page,
			$_SESSION['search_for'],
			$_SESSION['search_common'],
			$_SESSION['search_status']
		);

		gen_admin_domain_search_options(
			$tpl,
			$_SESSION['search_for'],
			$_SESSION['search_common'],
			$_SESSION['search_status']
		);

		$rs = exec_query($sql, $count_query);
	} else {
		gen_admin_domain_query(
			$search_query,
			$count_query,
			$start_index,
			$rows_per_page,
			'n/a',
			'n/a',
			'n/a'
		);

		gen_admin_domain_search_options($tpl, 'n/a', 'n/a', 'n/a');

		$rs = exec_query($sql, $count_query);
	}

	$records_count = $rs->fields['cnt'];
	$rs = execute_query($sql, $search_query);

	if($rs->recordCount() == 0) {
		if(isset($_SESSION['search_for'])) {
			$tpl->assign(
				array(
					'USR_MESSAGE' =>
					tr('Not found user records matching the search criteria!'),
					'USR_LIST' => '',
					'SCROLL_PREV' => '',
					'SCROLL_NEXT' => '',
					'TR_VIEW_DETAILS' => tr('view aliases'),
					'SHOW_DETAILS' => 'show',
				)
			);

			unset($_SESSION['search_for']);
			unset($_SESSION['search_common']);
			unset($_SESSION['search_status']);
		} else {
			$tpl->assign(
				array(
					'USR_MESSAGE' => tr('Users list is empty!'),
					'USR_LIST' => '',
					'SCROLL_PREV' => '',
					'SCROLL_NEXT' => '',
					'TR_VIEW_DETAILS' => tr('view aliases'),
					'SHOW_DETAILS' => 'show',
				)
			);
		}

	} else {
		$prev_si = $start_index - $rows_per_page;

		if($start_index == 0) {
			$tpl->assign('SCROLL_PREV', '');
		} else {
			$tpl->assign(
				array(
					'SCROLL_PREV_GRAY' => '',
					'PREV_PSI' => $prev_si
				)
			);
		}

		$next_si = $start_index + $rows_per_page;

		if($next_si + 1 > $records_count) {
			$tpl->assign('SCROLL_NEXT', '');
		} else {
			$tpl->assign(
				array(
					'SCROLL_NEXT_GRAY' => '',
					'NEXT_PSI' => $next_si
				)
			);
		}

		$tpl->assign(
			array(
				'TR_USR_USERNAME' => tr('Username'),
				'TR_USR_CREATED_BY' => tr('Created by'),
				'TR_USR_OPTIONS' => tr('Options'),
				'TR_USER_STATUS' => tr('Status'),
				'TR_DETAILS' => tr('Details'),
				'TR_EDIT_DOMAIN' => tr('Edit domain'),
				'TR_EDIT_USR' => tr('Edit user')
			)
		);

		while(!$rs->EOF) {
			// user status icon
			$domain_created_id = $rs->fields['domain_created_id'];

			$query = "
				SELECT
					`admin_name`
				FROM
					`admin`
				WHERE
					`admin_id` = ?
				ORDER BY
					`admin_name`
				ASC
				;
			";

			$rs2 = exec_query($sql, $query, $domain_created_id);

			if(!isset($rs2->fields['admin_name'])) {
				$created_by_name = tr('N/A');
			} else {
				$created_by_name = $rs2->fields['admin_name'];
			}

			// Get disk usage by user
			$tpl->append(
				array(
					'USR_DELETE_SHOW' => '',
					'DOMAIN_ID' => $rs->fields['domain_id'],
					'URL_DELETE_USR' => 'user_delete.php?domain_id=' .
							$rs->fields['domain_id'],
					'URL_CHANGE_INTERFACE' => 'change_user_interface.php?to_id=' .
							$rs->fields['domain_admin_id']
				)
			);

			if($rs->fields['status'] == $cfg->ITEM_OK_STATUS) {
				$status_icon = 'ok';
				$status_url = 'domain_status_change.php?domain_id=' .
					$rs->fields['domain_id'];
			} elseif($rs->fields['status'] == $cfg->ITEM_DISABLED_STATUS) {
				$status_icon = 'disabled';
				$status_url = 'domain_status_change.php?domain_id=' .
					$rs->fields['domain_id'];
			} elseif($rs->fields['status'] == $cfg->ITEM_ADD_STATUS
				|| $rs->fields['status'] == $cfg->ITEM_RESTORE_STATUS
				|| $rs->fields['status'] == $cfg->ITEM_CHANGE_STATUS
				|| $rs->fields['status'] == $cfg->ITEM_TOENABLE_STATUS
				|| $rs->fields['status'] == $cfg->ITEM_TODISABLED_STATUS
				|| $rs->fields['status'] == $cfg->ITEM_DELETE_STATUS) {

				$status_icon = 'reload';
				$status_url = '#';
			} else {
				$status_icon = 'error';
				$status_url = 'domain_details.php?domain_id=' .
					$rs->fields['domain_id'];
			}

			$tpl->append(
				array(
					'STATUS_ICON' => $status_icon,
					'URL_CHANGE_STATUS' => $status_url,
				)
			);

			// end of user status icon
			$admin_name = decode_idna($rs->fields['domain_name']);
			$domain_created = $rs->fields['domain_created'];

			if($domain_created == 0) {
				$domain_created = tr('N/A');
			} else {
				$date_formt = $cfg->DATE_FORMAT;
				$domain_created = date($date_formt, $domain_created);
			}

			$domain_expires = $rs->fields['domain_expires'];

			if($domain_expires == 0) {
				$domain_expires = tr('Not Set');
			} else {
				$date_formt = $cfg->DATE_FORMAT;
				$domain_expires = date($date_formt, $domain_expires);
			}

			$tpl->append(
				array(
					'USR_USERNAME' => tohtml($admin_name),
					'USER_CREATED_ON' => tohtml($domain_created),
					'USER_EXPIRES_ON' => $domain_expires,
					'USR_CREATED_BY' => tohtml($created_by_name),
					'USR_OPTIONS' => '',
					'URL_EDIT_USR' => 'admin_edit.php?edit_id=' .
						$rs->fields['domain_admin_id']
				)
			);

			gen_domain_details($tpl, $sql, $rs->fields['domain_id']);
			$rs->moveNext();
		}
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function get_admin_manage_users($tpl) {

	$sql = EasySCP_Registry::get('Db');

	$tpl->assign(
		array(
			'TR_MANAGE_USERS'	=> tr('Manage users'),
			'TR_ADMINISTRATORS' => tr('Administrators'),
			'TR_RESELLERS'		=> tr('Resellers'),
			'TR_USERS'			=> tr('Users'),
			'TR_SEARCH'			=> tr('Search'),
			'TR_CREATED_ON'		=> tr('Creation date'),
			'TR_EXPIRES_ON'		=> tr('Expire date'),
			'TR_MESSAGE_DELETE'	=> tr('Are you sure you want to delete %s?', true, '%s'),
			'TR_EDIT'			=> tr('Edit'),
			'TR_DELETE'			=> tr('Delete'),
			'TR_CHANGE_USER_INTERFACE' => tr('Switch to user interface'),
			'TR_MESSAGE_CHANGE_STATUS' => tr('Are you sure you want to change the status of domain account?', true)
		)
	);

	gen_admin_list($tpl, $sql);
	gen_reseller_list($tpl, $sql);
	gen_user_list($tpl, $sql);
}

function generate_reseller_props($reseller_id) {

	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			*
		FROM
			`reseller_props`
		WHERE
			`reseller_id` = ?
		;
	";

	$rs = exec_query($sql, $query, $reseller_id);

	if($rs->rowCount() == 0) {
		return array_fill(0, 18, 0);
	}

	return array(
		$rs->fields['current_dmn_cnt'],
		$rs->fields['max_dmn_cnt'],
		$rs->fields['current_sub_cnt'],
		$rs->fields['max_sub_cnt'],
		$rs->fields['current_als_cnt'],
		$rs->fields['max_als_cnt'],
		$rs->fields['current_mail_cnt'],
		$rs->fields['max_mail_cnt'],
		$rs->fields['current_ftp_cnt'],
		$rs->fields['max_ftp_cnt'],
		$rs->fields['current_sql_db_cnt'],
		$rs->fields['max_sql_db_cnt'],
		$rs->fields['current_sql_user_cnt'],
		$rs->fields['max_sql_user_cnt'],
		$rs->fields['current_traff_amnt'],
		$rs->fields['max_traff_amnt'],
		$rs->fields['current_disk_amnt'],
		$rs->fields['max_disk_amnt']
	);
}

function generate_reseller_users_props($reseller_id) {

	$sql = EasySCP_Registry::get('Db');

	$rdmn_current = 0;
	$rdmn_max = 0;
	$rdmn_uf = '_off_';
	$rsub_current = 0;
	$rsub_max = 0;
	$rsub_uf = '_off_';
	$rals_current = 0;
	$rals_max = 0;
	$rals_uf = '_off_';
	$rmail_current = 0;
	$rmail_max = 0;
	$rmail_uf = '_off_';
	$rftp_current = 0;
	$rftp_max = 0;
	$rftp_uf = '_off_';
	$rsql_db_current = 0;
	$rsql_db_max = 0;
	$rsql_db_uf = '_off_';
	$rsql_user_current = 0;
	$rsql_user_max = 0;
	$rsql_user_uf = '_off_';
	$rtraff_current = 0;
	$rtraff_max = 0;
	$rtraff_uf = '_off_';
	$rdisk_current = 0;
	$rdisk_max = 0;
	$rdisk_uf = '_off_';

	$query = "
		SELECT
			`admin_id`
		FROM
			`admin`
		WHERE
			`created_by` = ?
		;
	";

	$rs = exec_query($sql, $query, $reseller_id);

	if($rs->rowCount() == 0) {
		return array(
			$rdmn_current, $rdmn_max, $rdmn_uf,
			$rsub_current, $rsub_max, $rsub_uf,
			$rals_current, $rals_max, $rals_uf,
			$rmail_current, $rmail_max, $rmail_uf,
			$rftp_current, $rftp_max, $rftp_uf,
			$rsql_db_current, $rsql_db_max, $rsql_db_uf,
			$rsql_user_current, $rsql_user_max, $rsql_user_uf,
			$rtraff_current, $rtraff_max, $rtraff_uf,
			$rdisk_current, $rdisk_max, $rdisk_uf
		);
	}

	while(!$rs->EOF) {
		$admin_id = $rs->fields['admin_id'];

		$query = "
			SELECT
				`domain_id`
			FROM
				`domain`
			WHERE
				`domain_admin_id` = ?
			;
		";

		$dres = exec_query($sql, $query, $admin_id);
		$user_id = $dres->fields['domain_id'];

		list($sub_current, $sub_max, $als_current, $als_max, $mail_current,
			$mail_max, $ftp_current, $ftp_max, $sql_db_current, $sql_db_max,
			$sql_user_current, $sql_user_max, $traff_max, $disk_max
		) = generate_user_props($user_id);

		//list($a, $b, $c, $d, $e, $f, $traff_current, $disk_current, $g, $h
		//) = generate_user_traffic($user_id);
		list(,,,,,,$traff_current, $disk_current) =
			generate_user_traffic($user_id);

		$rdmn_current += 1;

		if($sub_max != -1) {
			if($sub_max == 0) {
				$rsub_uf = '_on_';
			}

			$rsub_current += $sub_current;
			$rsub_max += ($sub_max > 0) ? $sub_max : 0;
		}

		if($als_max != -1) {
			if($als_max == 0) {
				$rals_uf = '_on_';
			}

			$rals_current += $als_current;
			$rals_max += ($als_max > 0) ? $als_max : 0;
		}

		if($mail_max == 0) {
			$rmail_uf = '_on_';
		}

		$rmail_current += $mail_current;
		$rmail_max += ($mail_max > 0) ? $mail_max : 0;

		if($ftp_max == 0) {
			$rftp_uf = '_on_';
		}

		$rftp_current += $ftp_current;
		$rftp_max += ($ftp_max > 0) ? $ftp_max : 0;

		if($sql_db_max != -1) {
			if($sql_db_max == 0) {
				$rsql_db_uf = '_on_';
			}

			$rsql_db_current += $sql_db_current;
			$rsql_db_max += ($sql_db_max > 0) ? $sql_db_max : 0;
		}

		if($sql_user_max != -1) {
			if($sql_user_max == 0) {
				$rsql_user_uf = '_on_';
			}

			$rsql_user_current += $sql_user_current;
			$rsql_user_max += ($sql_user_max > 0) ? $sql_user_max : 0;
		}

		if($traff_max == 0) {
			$rtraff_uf = '_on_';
		}

		$rtraff_current += $traff_current;
		$rtraff_max += $traff_max;

		if($disk_max == 0) {
			$rdisk_uf = '_on_';
		}

		$rdisk_current += $disk_current;
		$rdisk_max += $disk_max;
		$rs->moveNext();
	}

	return array(
		$rdmn_current, $rdmn_max, $rdmn_uf,
		$rsub_current, $rsub_max, $rsub_uf,
		$rals_current, $rals_max, $rals_uf,
		$rmail_current, $rmail_max, $rmail_uf,
		$rftp_current, $rftp_max, $rftp_uf,
		$rsql_db_current, $rsql_db_max, $rsql_db_uf,
		$rsql_user_current, $rsql_user_max, $rsql_user_uf,
		$rtraff_current, $rtraff_max, $rtraff_uf,
		$rdisk_current, $rdisk_max, $rdisk_uf
	);
}

/**
* @todo explain or replace the hack
*/
function generate_user_props($user_id) {

	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			*
		FROM
			`domain`
		WHERE
			`domain_id` = ?
		;
	";

	$rs = exec_query($sql, $query, $user_id);

	if($rs->rowCount() == 0) {
		return array_fill(0, 14, 0);
	}

	$sub_current = records_count('subdomain', 'domain_id', $user_id);
	$sub_max = $rs->fields['domain_subd_limit'];
	$als_current = records_count('domain_aliasses', 'domain_id', $user_id);
	$als_max = $rs->fields['domain_alias_limit'];

	$mail_current = records_count(
		'mail_users',
		"mail_type NOT RLIKE '_catchall' AND domain_id",
		$user_id
	);

	$mail_max = $rs->fields['domain_mailacc_limit'];

	$ftp_current = sub_records_rlike_count(
		'domain_name', 'domain', 'domain_id', $user_id, 'userid', 'ftp_users',
		'userid', '@', ''
	);

	$ftp_current += sub_records_rlike_count(
		'alias_name', 'domain_aliasses', 'domain_id', $user_id, 'userid',
		'ftp_users', 'userid', '@', ''
	);

	$ftp_max = $rs->fields['domain_ftpacc_limit'];
	$sql_db_current = records_count('sql_database', 'domain_id', $user_id);
	$sql_db_max = $rs->fields['domain_sqld_limit'];

	$sql_user_current = sub_records_count(
		'sqld_id', 'sql_database', 'domain_id', $user_id, 'sqlu_id', 'sql_user',
		'sqld_id', 'sqlu_name', ''
	);

	$sql_user_max = $rs->fields['domain_sqlu_limit'];
	$traff_max = $rs->fields['domain_traffic_limit'];
	$disk_max = $rs->fields['domain_disk_limit'];

	return array(
		$sub_current, $sub_max, $als_current, $als_max, $mail_current, $mail_max,
		$ftp_current, $ftp_max, $sql_db_current, $sql_db_max, $sql_user_current,
		$sql_user_max, $traff_max, $disk_max
	);
}

/**
 * @todo implement check for dynamic table/row in SQL query
 * @param $table
 * @param $where
 * @param $value
 *
 * @return
 */
function records_count($table, $where, $value) {

	if($where != '') {
		if($value != '') {
			$sql_param = array(
				':where' => $where,
				':value' => $value
			);

			$sql_query = "
				SELECT
					COUNT(*) AS cnt
				FROM
					".$table."
				WHERE
					:where = :value;
			";
		} else {
			$sql_param = array(
					':where' => $where
			);

			$sql_query = "
				SELECT
					COUNT(*) AS cnt
				FROM
					".$table."
				WHERE
					:where;
			";
		}
		DB::prepare($sql_query);
		$row = DB::execute($sql_param, true);
	} else {
		$sql_query = "
			SELECT
				COUNT(*) AS cnt
			FROM
				".$table.";
		";
		$row = DB::query($sql_query, true);
	}

	return $row['cnt'];
}

/**
* @todo implement check for dynamic table/row in SQL query
*/
function sub_records_count($field, $table, $where, $value, $subfield, $subtable,
	$subwhere, $subgroupname) {

	$sql = EasySCP_Registry::get('Db');

	if($where != '') {
		$query = "
			SELECT
				$field AS `field`
			FROM
				$table
			WHERE
				$where = ?
			;
		";

		$rs = exec_query($sql, $query, $value);
	} else {
		$query = "
			SELECT
				$field AS `field`
			FROM
				$table
			;
		";

		$rs = exec_query($sql, $query);
	}

	$result = 0;

	if($rs->rowCount() == 0) {
		return $result;
	}

	if($subgroupname != '') {
		$sqld_ids = array();

		while(!$rs->EOF) {
			array_push($sqld_ids, $rs->fields['field']);
			$rs->moveNext();
		}

		$sqld_ids = implode(',', $sqld_ids);

		if($subwhere != '') {
			$query = "
				SELECT COUNT(DISTINCT $subgroupname) AS `cnt`
				FROM
					$subtable
				WHERE
					`sqld_id` IN ($sqld_ids)
				;
			";

			$subres = exec_query($sql, $query);
			$result = $subres->fields['cnt'];
		} else {
			return $result;
		}
	} else {
		while(!$rs->EOF) {
			$contents = $rs->fields['field'];

			if($subwhere != '') {
				$query = "
					SELECT COUNT(*) AS `cnt`
					FROM
						$subtable
					WHERE
						$subwhere = ?
					;
				";
			} else {
				return $result;
			}

			$subres = exec_query($sql, $query, $contents);
			$result += $subres->fields['cnt'];
			$rs->moveNext();
		}
	}

	return $result;
}

function generate_user_traffic($user_id) {

	global $crnt_month, $crnt_year;
	$sql = EasySCP_Registry::get('Db');

	$from_timestamp = mktime(0, 0, 0, $crnt_month, 1, $crnt_year);

	if($crnt_month == 12) {
		$to_timestamp = mktime(0, 0, 0, 1, 1, $crnt_year + 1);
	} else {
		$to_timestamp = mktime(0, 0, 0, $crnt_month + 1, 1, $crnt_year);
	}

	$query = "
		SELECT
			`domain_id`,
			IFNULL(`domain_disk_usage`, 0) AS `domain_disk_usage`,
			IFNULL(`domain_traffic_limit`, 0) AS `domain_traffic_limit`,
			IFNULL(`domain_disk_limit`, 0) AS `domain_disk_limit`,
			`domain_name`
		FROM
			`domain`
		WHERE
			`domain_id` = ?
		ORDER BY
			`domain_name`
		;
	";

	$rs = exec_query($sql, $query, $user_id);

	if($rs->rowCount() == 0 || $rs->rowCount() > 1) {
		write_log(
			'TRAFFIC WARNING: ' . $rs->fields['domain_name'] .
				' manages incorrect number of domains: ' . $rs->rowCount()
		);

		return array('n/a', 0, 0, 0, 0, 0, 0, 0, 0, 0);
	} else {
		$domain_id = $rs->fields['domain_id'];
		$domain_disk_usage = $rs->fields['domain_disk_usage'];
		$domain_traff_limit = $rs->fields['domain_traffic_limit'];
		$domain_disk_limit = $rs->fields['domain_disk_limit'];
		$domain_name = $rs->fields['domain_name'];

		$query = "
			SELECT
				IFNULL(SUM(`dtraff_web_in`), 0) + IFNULL(SUM(`dtraff_web_out`), 0) AS web,
				IFNULL(SUM(`dtraff_ftp_in`), 0) + IFNULL(SUM(`dtraff_ftp_out`), 0) AS ftp,
				IFNULL(SUM(`dtraff_mail`), 0) AS smtp,
				IFNULL(SUM(`dtraff_pop`), 0) AS pop,
				IFNULL(SUM(`dtraff_web_in`), 0) +
				IFNULL(SUM(`dtraff_web_out`), 0) +
				IFNULL(SUM(`dtraff_ftp_in`), 0) +
				IFNULL(SUM(`dtraff_ftp_out`), 0) +
				IFNULL(SUM(`dtraff_mail`), 0) +
				IFNULL(SUM(`dtraff_pop`), 0) AS total
			FROM
				`domain_traffic`
			WHERE
				`domain_id` = ?
			AND
				`dtraff_time` >= ?
			AND
				`dtraff_time` < ?
			;
		";

		$rs1 = exec_query(
			$sql, $query, array($domain_id, $from_timestamp, $to_timestamp)
		);

		return array(
			$domain_name,
			$domain_id,
			$rs1->fields['web'],
			$rs1->fields['ftp'],
			$rs1->fields['smtp'],
			$rs1->fields['pop'],
			$rs1->fields['total'],
			$domain_disk_usage,
			$domain_traff_limit,
			$domain_disk_limit
		);
	}
}

/**
 * @todo implement check for dynamic table/row in SQL query
 */
function sub_records_rlike_count($field, $table, $where, $value, $subfield,
	$subtable, $subwhere, $a, $b) {

	$sql = EasySCP_Registry::get('Db');

	if($where != '') {
		$query = "
			SELECT
				$field AS `field`
			FROM
				$table
			WHERE
				$where = ?
			;
		";

		$rs = exec_query($sql, $query, $value);
	} else {
		$query = "
			SELECT
				$field AS `field`
			FROM
				$table
			;
		";

		$rs = exec_query($sql, $query);
	}

	$result = 0;

	if($rs->rowCount() == 0) {
		return $result;
	}

	while(!$rs->EOF) {
		$contents = $rs->fields['field'];

		if($subwhere != '') {
			$query = "
				SELECT COUNT(*) AS `cnt`
				FROM
					$subtable
				WHERE
					$subwhere
				RLIKE
					?
				;
			";
		} else {
			return $result;
		}

		$subres = exec_query($sql, $query, $a . $contents . $b);
		$result += $subres->fields['cnt'];
		$rs->moveNext();
	}

	return $result;
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $user_month
 * @param int $user_year
 */
function gen_select_lists($tpl, $user_month, $user_year) {

	global $crnt_month, $crnt_year;
	$cfg = EasySCP_Registry::get('Config');

	if($user_month != '' || $user_year != '') {
		$crnt_month = $user_month;
		$crnt_year = $user_year;
	} else {
		$crnt_month = date('m');
		$crnt_year = date('Y');
	}

	for($i = 1 ; $i <= 12 ; $i++) {
		$tpl->append(
			array(
				'MONTH_SELECTED' => ($i == $crnt_month) ? $cfg->HTML_SELECTED : '',
				'MONTH_VALUE' => $i
			)
		);

	}

	for($i = $crnt_year - 1 ; $i <= $crnt_year + 1 ; $i++) {
		$tpl->append(
			array(
				'YEAR_SELECTED' => ($i == $crnt_year) ? $cfg->HTML_SELECTED : '',
				'YEAR_VALUE' => $i
			)
		);

	}
}

function get_user_name($user_id) {

	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			`admin_name`
		FROM
			`admin`
		WHERE
			`admin_id` = ?
		;
	";

	$rs = exec_query($sql, $query, $user_id);

	return $rs->fields('admin_name');
}

/**
 * Calaculates the length of a graphical bar shown as statistic
 *
 * @param int $value		current value
 * @param int $value_max	maximum value to reach
 * @param int $bar_width	maximum size of the bar in px
 * @return int				bar length in percent
 */
function calc_bar_value($value, $value_max, $bar_width) {

	if($value_max == 0) {
		return 0;
	} else {
		$ret_value = ($value * 100) / $value_max;
		return ($ret_value > 100) ? $bar_width : round($ret_value, 3);
	}
}

/**
* log function
*/
function write_log($msg, $level = E_USER_WARNING) {

	$cfg = EasySCP_Registry::get('Config');

	if(isset($_SERVER['REMOTE_ADDR'])) {
		$client_ip = $_SERVER['REMOTE_ADDR'];
	} else {
		$client_ip = "unknown";
	}

	$msg = htmlentities($msg, ENT_NOQUOTES, tr('encoding'), false);

	$msg = replace_html($msg, ENT_COMPAT, tr('encoding')).'<br /><small>User IP: '.$client_ip.'</small>';

	$sql_param = array(
		':log_message'	=> $msg
	);

	$sql_query = "
		INSERT INTO
			log (log_time, log_message)
		VALUES
			(NOW(), :log_message);
		";
	DB::prepare($sql_query);
	DB::execute($sql_param)->closeCursor();

	$msg = strip_tags(str_replace('<br />', "\n", $msg));

	// now send email if DEFAULT_ADMIN_ADDRESS != ''
	if(EasyConfig::$cfg->{'DEFAULT_ADMIN_ADDRESS'} != '' && $level <= $cfg->LOG_LEVEL) {
		global $default_hostname, $default_base_server_ip, $Version, $BuildDate,
		$admin_login;
		$admin_email = EasyConfig::$cfg->{'DEFAULT_ADMIN_ADDRESS'};
		$default_hostname = EasyConfig::$cfg->{'SERVER_HOSTNAME'};
		$default_base_server_ip = EasyConfig::$cfg->{'BASE_SERVER_IP'};
		$Version = EasyConfig::$cfg->{'Version'};
		$BuildDate = EasyConfig::$cfg->{'BuildDate'};
		$subject = "EasySCP $Version on $default_hostname ($default_base_server_ip)";
		$to = EasyConfig::$cfg->{'DEFAULT_ADMIN_ADDRESS'};
		$message = <<<AUTO_LOG_MSG

EasySCP Log

Server: $default_hostname ($default_base_server_ip)
Version: EasySCP $Version ($BuildDate)

Message: ----------------[BEGIN]--------------------------

$msg

Message: ----------------[END]----------------------------

AUTO_LOG_MSG;

		$headers = "From: \"EasySCP Logging Daemon\" <" . $admin_email . ">\n";
		$headers .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 7bit\n";
		$headers .= "X-Mailer: EasySCP $Version Logging Mailer";
		$mail_result = mail($to, $subject, $message, $headers);

		// Reduce admin log entries by only logging email notification if not
		// successful
		if(!$mail_result) {
			$mail_status = ($mail_result) ? 'OK' : 'NOT OK';
			$sql_param = array(
				':log_message'	=> "$admin_login: Logging Daemon Mail To: |$to|, " . "From: |$admin_email|, Status: |$mail_status|!"
			);

			$sql_query = "
				INSERT INTO
					log (log_time, log_message)
				VALUES
					(NOW(), :log_message);
			";
			DB::prepare($sql_query);
			DB::execute($sql_param)->closeCursor();
		}
	}
}

function send_add_user_auto_msg($admin_id, $uname, $upass, $uemail, $ufname,
	$ulname, $utype, $gender = '') {

	$cfg = EasySCP_Registry::get('Config');

	$admin_login = $_SESSION['user_logged'];
	$data = get_welcome_email($admin_id, 'user');
	$from_name = $data['sender_name'];
	$from_email = $data['sender_email'];
	$message = $data['message'];
	$base_vhost = $cfg->BASE_SERVER_VHOST;

	if($from_name) {
		$from = '"' . mb_encode_mimeheader($from_name, 'UTF-8') . "\" <" . $from_email . ">";
	} else {
		$from = $from_email;
	}

	if($ufname && $ulname) {
		$to = '"' . mb_encode_mimeheader($ufname . ' ' . $ulname, 'UTF-8') . "\" <" . $uemail . ">";
		$name = "$ufname $ulname";
	} else {
		$name = $uname;
		$to = $uemail;
	}

	$username = $uname;
	$password = $upass;
	$subject = $data['subject'];
	$search = array();
	$replace = array();
	$search [] = '{USERNAME}';
	$replace[] = decode_idna($username);
	$search [] = '{USERTYPE}';
	$replace[] = $utype;
	$search [] = '{NAME}';
	$replace[] = decode_idna($name);
	$search [] = '{PASSWORD}';
	$replace[] = $password;
	$search [] = '{BASE_SERVER_VHOST}';
	$replace[] = $base_vhost;
	$search [] = '{BASE_SERVER_VHOST_PREFIX}';
	$replace[] = $cfg->BASE_SERVER_VHOST_PREFIX;
	$subject = str_replace($search, $replace, $subject);
	$message = str_replace($search, $replace, $message);
	$subject = mb_encode_mimeheader($subject, 'UTF-8');
	$headers = "From: " . $from . "\n";
	$headers .= "MIME-Version: 1.0\nContent-Type: text/plain; " .
		"charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
	$headers .= "X-Mailer: EasySCP {$cfg->Version} Service Mailer";
	$mail_result = mail($to, $subject, $message, $headers);
	$mail_status = ($mail_result) ? 'OK' : 'NOT OK';

    $name = tohtml($name);
    $from_name = tohtml($from_name);

	write_log("$admin_login: Auto Add User To: |$name <$uemail>|, From: " .
		"|$from_name <$from_email>|, Status: |$mail_status|!");
}

function update_reseller_props($reseller_id, $props) {

	$sql = EasySCP_Registry::get('Db');

	if($props == '') {
		return;
	}

	list($dmn_current, $dmn_max, $sub_current, $sub_max, $als_current, $als_max,
		$mail_current, $mail_max, $ftp_current, $ftp_max, $sql_db_current,
		$sql_db_max,$sql_user_current, $sql_user_max, $traff_current, $traff_max,
		$disk_current, $disk_max
	) = explode(";", $props);

	$query = "
		UPDATE
			`reseller_props`
		SET
			`current_dmn_cnt` = ?,
			`max_dmn_cnt` = ?,
			`current_sub_cnt` = ?,
			`max_sub_cnt` = ?,
			`current_als_cnt` = ?,
			`max_als_cnt` = ?,
			`current_mail_cnt` = ?,
			`max_mail_cnt` = ?,
			`current_ftp_cnt` = ?,
			`max_ftp_cnt` = ?,
			`current_sql_db_cnt` = ?,
			`max_sql_db_cnt` = ?,
			`current_sql_user_cnt` = ?,
			`max_sql_user_cnt` = ?,
			`current_traff_amnt` = ?,
			`max_traff_amnt` = ?,
			`current_disk_amnt` = ?,
			`max_disk_amnt` = ?
		WHERE
			`reseller_id` = ?
		;
	";

	$res = exec_query(
		$sql,
		$query,
		array(
			$dmn_current, $dmn_max, $sub_current, $sub_max, $als_current,
			$als_max, $mail_current, $mail_max, $ftp_current, $ftp_max,
			$sql_db_current, $sql_db_max, $sql_user_current, $sql_user_max,
			$traff_current, $traff_max, $disk_current, $disk_max,
			$reseller_id
		)
	);

	return $res;
}

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function gen_logged_from($tpl) {

	if(isset($_SESSION['logged_from']) && isset($_SESSION['logged_from_id'])) {
		$tpl->assign(
			array(
				'YOU_ARE_LOGGED_AS' => tr(
					'%1$s you are now logged as %2$s',
					$_SESSION['logged_from'],
					decode_idna($_SESSION['user_logged'])
				),
				'TR_GO_BACK' => tr('Go back')
			)
		);

	}
}

function change_domain_status($domain_id, $domain_name, $action, $location) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	if($action == 'disable') {
		$new_status = $cfg->ITEM_TODISABLED_STATUS;
	} else if($action == 'enable') {
		$new_status = $cfg->ITEM_TOENABLE_STATUS;
	} else {
		return;
	}

	// TODO Prüfen wie man das mit den Mails behandeln soll == abschalten / nicht abschalten

	$query = "
		SELECT
			`mail_id`,
			`mail_pass`,
			`mail_type`
		FROM
			`mail_users`
		WHERE
			`domain_id` = ?
		;
	";

	$rs = exec_query($sql, $query, $domain_id);

	while(!$rs->EOF) {
		$mail_id = $rs->fields['mail_id'];
		$mail_pass = $rs->fields['mail_pass'];
		$mail_type = $rs->fields['mail_type'];

		if($cfg->HARD_MAIL_SUSPENSION) {
			$mail_status = $new_status;
		} else {
			if($action == 'disable') {
				$timestamp = time();
				$pass_prefix = substr(md5($timestamp), 0, 4);
				if(preg_match('/^' . MT_NORMAL_MAIL . '/', $mail_type)
					|| preg_match('/^' . MT_ALIAS_MAIL . '/', $mail_type)
					|| preg_match('/^' . MT_SUBDOM_MAIL . '/', $mail_type)
					|| preg_match('/^' . MT_ALSSUB_MAIL . '/', $mail_type)) {

					$mail_pass = DB::decrypt_data($mail_pass);
					$mail_pass = $pass_prefix . $mail_pass;
					$mail_pass = DB::encrypt_data($mail_pass);
				}
			} else if($action == 'enable') {
				if(preg_match('/^' . MT_NORMAL_MAIL . '/', $mail_type)
					|| preg_match('/^' . MT_ALIAS_MAIL . '/', $mail_type)
					|| preg_match('/^' . MT_SUBDOM_MAIL . '/', $mail_type)
					|| preg_match('/^' . MT_ALSSUB_MAIL . '/', $mail_type)) {

					$mail_pass = DB::decrypt_data($mail_pass);
					$mail_pass = substr($mail_pass, 4, 50);
					$mail_pass = DB::encrypt_data($mail_pass);
				}
			} else {
				return;
			}

			$mail_status = $cfg->ITEM_CHANGE_STATUS;
		}

		$query = "
			UPDATE
				`mail_users`
			SET
				`mail_pass` = ?,
				`status` = ?
			WHERE
				`mail_id` = ?
			;
		";

		// NXW: Unused result so..
		// $rs2 = exec_query(
		//	$sql, $query, array($mail_pass, $mail_status, $mail_id)
		//);
		exec_query(
			$sql, $query, array($mail_pass, $mail_status, $mail_id)
		);

		$rs->moveNext();
	}

	send_request('130 MAIL '. $domain_id);

	$query = "
		UPDATE
			domain
		SET
			status = ?
		WHERE
			domain_id = ?
		;
	";

	exec_query($sql, $query, array($new_status, $domain_id));

	send_request('110 DOMAIN domain '. $domain_id);

	$query = "
		UPDATE
			domain_aliasses
		SET
			status = ?
		WHERE
			domain_id = ?
		;
	";

	exec_query($sql, $query, array($new_status, $domain_id));

	send_request('110 DOMAIN alias '. $domain_id);

	// let's get back to user overview after the system changes are finished
	$user_logged = $_SESSION['user_logged'];
	update_reseller_c_props(get_reseller_id($domain_id));

	if($action == 'disable') {
		write_log("$user_logged: suspended domain: $domain_name");
		$_SESSION['user_disabled'] = 1;
	} else if($action == 'enable') {
		write_log("$user_logged: enabled domain: $domain_name");
		$_SESSION['user_enabled'] = 1;
	} else {
		return;
	}

	if($location == 'admin') {
		header('Location: manage_users.php');
	} else if($location == 'reseller') {
		header('Location: users.php?psi=last');
	}

	die();
}

/**
* @todo use db prepared statements
* @todo cleanup and/or comment confusing query salad
*/
function gen_admin_domain_query(&$search_query, &$count_query, $start_index,
	$rows_per_page, $search_for, $search_common, $search_status) {

	if($search_for == 'n/a' && $search_common == 'n/a' &&
		$search_status == 'n/a') {

		// We have pure list query;
		$count_query = "
			SELECT COUNT(*) AS `cnt`
			FROM
				`domain`
			;
		";

		$search_query = "
			SELECT
				*
			FROM
				`domain`
			ORDER BY
				`domain_name`
			ASC LIMIT
				$start_index,
				$rows_per_page
			;
		";
	} else if($search_for === '' && $search_status != '') {
		if($search_status == 'all') {
			$add_query = '';
		} else {
			$add_query = " WHERE `status` = '$search_status';
			";
		}

		$count_query = "
			SELECT COUNT(*) AS
				`cnt`
			FROM
				`domain`
				$add_query
			;
		";

		$search_query = "
			SELECT
				*
			FROM
				`domain`
			$add_query
			ORDER BY
				`domain_name`
			ASC LIMIT
				$start_index,
				$rows_per_page
			;
		";
	} else if($search_for != '') {
		if($search_common == 'domain_name') {

			$add_query = " WHERE `admin_name` RLIKE '$search_for' %s";

		} elseif($search_common == 'customer_id') {

			$add_query = " WHERE `customer_id` RLIKE '$search_for' %s";

		} elseif($search_common == 'lname') {

			$add_query = "WHERE (`lname` RLIKE '$search_for' OR `fname` " .
				"RLIKE '$search_for') %s";

		} elseif($search_common == 'firm') {

			$add_query = "WHERE `firm` RLIKE '$search_for' %s";

		} else if($search_common == 'city') {

			$add_query = "WHERE `city` RLIKE '$search_for' %s";

		} else if($search_common == 'state') {

			$add_query = "WHERE `state` ,RLIKE '$search_for' %s ";

		} elseif($search_common == 'country') {

			$add_query = "WHERE `country` RLIKE '$search_for' %s";

		}

		if($search_status != 'all') {
			$add_query = sprintf(
				$add_query,
				" AND t2.`status` = '$search_status'"
			);

			$count_query = "
				SELECT
					COUNT(*) AS cnt
				FROM
					`admin` AS t1,
					`domain` AS t2
				$add_query
				AND
					t1.`admin_id` = t2.`domain_admin_id`
				;
			";
		} else {
			$add_query = sprintf($add_query, ' ');

			$count_query = "
				SELECT
					COUNT(*) AS cnt
				FROM
					`admin`
					$add_query
				;
			";
		}

		$search_query = "
			SELECT
				t1.`admin_id`, t2.*
			FROM
				`admin` AS t1,
				`domain` AS t2
				$add_query
			AND
				t1.`admin_id` = t2.`domain_admin_id`
			ORDER BY
				t2.`domain_name` ASC
			LIMIT
				$start_index,
				$rows_per_page
			;
		";
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param string $search_for
 * @param string $search_common
 * @param string $search_status
 */
function gen_admin_domain_search_options($tpl, $search_for, $search_common,
	$search_status) {

	$cfg = EasySCP_Registry::get('Config');

	if($search_for == 'n/a' && $search_common == 'n/a' &&
		$search_status == 'n/a') {

		// we have no search and let's generate search fields empty
		$domain_selected = $cfg->HTML_SELECTED;
		$customerid_selected = '';
		$lastname_selected = '';
		$company_selected = '';
		$city_selected = '';
		$state_selected = '';
		$country_selected = '';
		$all_selected = $cfg->HTML_SELECTED;
		$ok_selected = '';
		$suspended_selected = '';
	}

	if($search_common == 'domain_name') {
		$domain_selected = $cfg->HTML_SELECTED;
		$customerid_selected = '';
		$lastname_selected = '';
		$company_selected = '';
		$city_selected = '';
		$state_selected = '';
		$country_selected = '';
	} elseif($search_common == 'customer_id') {
		$domain_selected = '';
		$customerid_selected = $cfg->HTML_SELECTED;
		$lastname_selected = '';
		$company_selected = '';
		$city_selected = '';
		$state_selected = '';
		$country_selected = '';
	} elseif($search_common == 'lname') {
		$domain_selected = '';
		$customerid_selected = '';
		$lastname_selected = $cfg->HTML_SELECTED;
		$company_selected = '';
		$city_selected = '';
		$state_selected = '';
		$country_selected = '';
	} elseif($search_common === 'firm') {
		$domain_selected = '';
		$customerid_selected = '';
		$lastname_selected = '';
		$company_selected = $cfg->HTML_SELECTED;
		$city_selected = '';
		$state_selected = '';
		$country_selected = '';
	} elseif($search_common == 'city') {
		$domain_selected = '';
		$customerid_selected = '';
		$lastname_selected = '';
		$company_selected = '';
		$city_selected = $cfg->HTML_SELECTED;
		$state_selected = '';
		$country_selected = '';
	} elseif($search_common == 'state') {
		$domain_selected = '';
		$customerid_selected = '';
		$lastname_selected = '';
		$company_selected = '';
		$city_selected = '';
		$state_selected = $cfg->HTML_SELECTED;
		$country_selected = '';
	} elseif($search_common == 'country') {
		$domain_selected = '';
		$customerid_selected = '';
		$lastname_selected = '';
		$company_selected = '';
		$city_selected = '';
		$state_selected = '';
		$country_selected = $cfg->HTML_SELECTED;
	}

	if($search_status == 'all') {
		$all_selected = $cfg->HTML_SELECTED;
		$ok_selected = '';
		$suspended_selected = '';
	} elseif($search_status == 'ok') {
		$all_selected = '';
		$ok_selected = $cfg->HTML_SELECTED;
		$suspended_selected = '';
	} elseif($search_status == 'disabled') {
		$all_selected = '';
		$ok_selected = '';
		$suspended_selected = $cfg->HTML_SELECTED;
	}

	if($search_for == 'n/a' || $search_for === '') {
		$tpl->assign(
			array('SEARCH_FOR' => '')
		);
	} else {
		$tpl->assign(
			array('SEARCH_FOR' => $search_for)
		);
	}

	$tpl->assign(
		array(
			'M_DOMAIN_NAME' => tr('Domain name'),
			'M_CUSTOMER_ID' => tr('Customer ID'),
			'M_LAST_NAME' => tr('Last name'),
			'M_COMPANY' => tr('Company'),
			'M_CITY' => tr('City'),
			'M_STATE' => tr('State/Province'),
			'M_COUNTRY' => tr('Country'),
			'M_ALL' => tr('All'),
			'M_OK' => tr('OK'),
			'M_SUSPENDED' => tr('Suspended'),
			'M_ERROR' => tr('Error'),
			// selected area
			'M_DOMAIN_NAME_SELECTED' => $domain_selected,
			'M_CUSTOMER_ID_SELECTED' => $customerid_selected,
			'M_LAST_NAME_SELECTED' => $lastname_selected,
			'M_COMPANY_SELECTED' => $company_selected,
			'M_CITY_SELECTED' => $city_selected,
			'M_STATE_SELECTED' => $state_selected,
			'M_COUNTRY_SELECTED' => $country_selected,
			'M_ALL_SELECTED' => $all_selected,
			'M_OK_SELECTED' => $ok_selected,
			'M_SUSPENDED_SELECTED' => $suspended_selected,
		)
	);
}

/**
* Delete domain with all sub items (usage in admin and reseller)
* @param integer $domain_id
* @param string $goto users.php or manage_users.php
* @param boolean $breseller double check by reseller=current user
*/
function delete_domain($domain_id, $goto, $breseller = false) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	// Get uid and gid of domain user
	$query = "
		SELECT
			`domain_uid`,
			`domain_gid`,
			`domain_admin_id`,
			`domain_name`,
			`domain_created_id`
		FROM
			`domain`
		WHERE
			`domain_id` = ?
	";

	if($breseller) {
		$reseller_id = $_SESSION['user_id'];
		$query .= " AND `domain_created_id` = ?";
		$res = exec_query($sql, $query, array($domain_id, $reseller_id));
	} else {
		$res = exec_query($sql, $query, $domain_id);
	}

	$data = $res->fetchRow();

	if(empty($data['domain_uid']) || empty($data['domain_admin_id'])) {
		set_page_message(tr('Wrong domain ID!'), 'error');
		user_goto($goto);
	}

	$domain_admin_id = $data['domain_admin_id'];
	$domain_name = $data['domain_name'];
	$domain_uid = $data['domain_uid'];
	$domain_gid = $data['domain_gid'];

	if(!$breseller) {
		$reseller_id = $data['domain_created_id'];
	}

	// Mail users:
	$query = "
		UPDATE
			`mail_users`
		SET
			`status` = ?
		WHERE
			`domain_id` = ?
		;
	";

	exec_query($sql, $query, array($cfg->ITEM_DELETE_STATUS, $domain_id));
	send_request('130 MAIL '.$domain_id);

	// Delete all protected areas related data (areas, groups and users)
	$query = "
		DELETE
			`areas`,
			`users`,
			`groups`
		FROM
			`domain` AS `customer`
		LEFT JOIN
			`htaccess` AS `areas` ON `areas`.`dmn_id` = `customer`.`domain_id`
		LEFT JOIN
			`htaccess_users` AS `users` ON `users`.`dmn_id` = `customer`.`domain_id`
		LEFT JOIN
			`htaccess_groups` AS `groups` ON `groups`.`dmn_id` = `customer`.`domain_id`
		WHERE
			`customer`.`domain_id` = ?
		;
	";

	exec_query($sql, $query, $domain_id);

	// Delete subdomain aliases:
	$alias_a = array();

	$query = "
		SELECT
			`alias_id`
		FROM
			`domain_aliasses`
		WHERE
			`domain_id` = ?
		;
	";

	$res = exec_query($sql, $query, $domain_id);

	while(!$res->EOF) {
		$alias_a[] = $res->fields['alias_id'];
		$res->moveNext();
	}

	if(count($alias_a) > 0) {
		$query = "
			UPDATE
				`subdomain_alias`
			SET
				`status` = ?
			WHERE
				`alias_id` IN (
		";
		$query .= implode(',', $alias_a);
		$query .= ")";

		exec_query($sql, $query, $cfg->ITEM_DELETE_STATUS);
	}

	// Delete SQL databases and users
	$query = "
		SELECT
			`sqld_id`
		FROM
			`sql_database`
		WHERE
			`domain_id` = ?
		;
	";

	$res = exec_query($sql, $query, $domain_id);

	while(!$res->EOF) {
		delete_sql_database($sql, $domain_id, $res->fields['sqld_id']);
		$res->moveNext();
	}

	// Domain aliases:
	$query = "
		UPDATE
			domain_aliasses
		SET
			status =  ?
		WHERE
			domain_id = ?
		;
	";

	exec_query($sql, $query, array($cfg->ITEM_DELETE_STATUS, $domain_id));

	// Remove domain traffic
	$query = "
		DELETE FROM
			`domain_traffic`
		WHERE
			`domain_id` = ?
		;
	";

	exec_query($sql, $query, $domain_id);

	// Set domain deletion status
	$query = "
		UPDATE
			`domain`
		SET
			`status` = 'delete'
		WHERE
		`domain_id` = ?
		;
	";

	exec_query($sql, $query, $domain_id);

	// Set domain subdomains deletion status
	$query = "
		UPDATE
			`subdomain`
		SET
			`status` = ?
		WHERE
			`domain_id` = ?
		;
	";

	exec_query($sql, $query, array($cfg->ITEM_DELETE_STATUS, $domain_id));

	// --- Activate daemon ---
	send_request('110 DOMAIN domain '.$domain_id);

	// Delete FTP users:
	$query = "
		DELETE FROM
			`ftp_users`
		WHERE
			`uid` = ?
		;
	";

	exec_query($sql, $query, $domain_uid);

	// Delete FTP groups:
	$query = "
		DELETE FROM
			`ftp_group`
		WHERE
			`gid` = ?
		;
	";

	exec_query($sql, $query, $domain_gid);

	// Delete EasySCP login:
	$query = "
		DELETE FROM
			`admin`
		WHERE
			`admin_id` = ?
		;
	";

	exec_query($sql, $query, $domain_admin_id);

	// Delete the quota section:
	$query = "
		DELETE FROM
			`quotalimits`
		WHERE
			`name` = ?
		;
	";

	exec_query($sql, $query, $domain_name);

	// Delete the quota section:
	$query = "
		DELETE FROM
			`quotatallies`
		WHERE
			`name` = ?
		;
	";

	exec_query($sql, $query, $domain_name);

	// Remove support tickets:
	$query = "
		DELETE FROM
			`tickets`
		WHERE
			ticket_from = ?
		OR
			ticket_to = ?
		;
	";

	exec_query($sql, $query, array($domain_admin_id, $domain_admin_id));

	// Delete user gui properties
	$query = "
		DELETE FROM
			`user_gui_props`
		WHERE
			`user_id` = ?
		;
	";

	exec_query($sql, $query, $domain_admin_id);
	write_log($_SESSION['user_logged'] . ': deletes domain ' . $domain_name);
	update_reseller_c_props($reseller_id);
	$_SESSION['ddel'] = '_yes_';
	user_goto($goto);
}
?>
