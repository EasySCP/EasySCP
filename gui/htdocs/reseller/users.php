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

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'reseller/users.tpl';

// TODO: comment!
unset($_SESSION['dmn_name']);
unset($_SESSION['ch_hpprops']);
unset($_SESSION['local_data']);
unset($_SESSION['dmn_ip']);
unset($_SESSION['dmn_id']);
unset($GLOBALS['dmn_name']);
unset($GLOBALS['ch_hpprops']);
unset($GLOBALS['local_data']);
unset($GLOBALS['user_add3_added']);
unset($GLOBALS['user_add3_added']);
unset($GLOBALS['dmn_ip']);
unset($GLOBALS['dmn_id']);

if (isset($cfg->HOSTING_PLANS_LEVEL)
		&& $cfg->HOSTING_PLANS_LEVEL === 'admin') {
	$tpl->assign('EDIT_OPTION', '');
}

generate_users_list($tpl, $_SESSION['user_id']);
check_externel_events($tpl);

// static page messages
gen_logged_from($tpl);

$crnt_month = date("m");
$crnt_year = date("Y");

$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Users'),
		'TR_MANAGE_USERS'			=> tr('Manage users'),
		'TR_USERS'					=> tr('Users'),
		'TR_USER_STATUS'			=> tr('Status'),
		'TR_DETAILS'				=> tr('Details'),
		'TR_SEARCH'					=> tr('Search'),
		'TR_USERNAME'				=> tr('Username'),
		'TR_ACTION'					=> tr('Actions'),
		'TR_CREATION_DATE'			=> tr('Creation date'),
		'TR_EXPIRE_DATE'			=> tr('Expire date'),
		'TR_CHANGE_USER_INTERFACE'	=> tr('Switch to user interface'),
		'TR_BACK'					=> tr('Back'),
		'TR_TITLE_BACK'				=> tr('Return to previous menu'),
		'TR_TABLE_NAME'				=> tr('Users list'),
		'TR_MESSAGE_CHANGE_STATUS'	=> tr('Are you sure you want to change the status of %s?', true, '%s'),
		'TR_MESSAGE_DELETE_ACCOUNT'	=> tr('Are you sure you want to delete %s?', true, '%s'),
		'TR_STAT'					=> tr('Stats'),
		'VL_MONTH'					=> $crnt_month,
		'VL_YEAR'					=> $crnt_year,
		'TR_EDIT_DOMAIN'			=> tr('Edit Domain'),
		'TR_EDIT_USER'				=> tr('Edit User'),
		'TR_BW_USAGE'				=> tr('Bandwidth'),
		'TR_DISK_USAGE'				=> tr('Disk'),
		'TR_DELETE'					=> tr('Delete')
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_users_manage.tpl');
gen_reseller_menu($tpl, 'reseller/menu_users_manage.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// Begin function block

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $admin_id
 */
function generate_users_list($tpl, $admin_id) {

	$sql = EasySCP_Registry::get('Db');
	$cfg = EasySCP_Registry::get('Config');

	$rows_per_page = $cfg->DOMAIN_ROWS_PER_PAGE;

	if (isset($_POST['details']) && !empty($_POST['details'])) {
		$_SESSION['details'] = $_POST['details'];
	} else {
		if (!isset($_SESSION['details'])) {
			$_SESSION['details'] = "hide";
		}
	}

    if (isset($_GET['psi']) && $_GET['psi'] == 'last') {
        if (isset($_SESSION['search_page'])) {
            $_GET['psi'] = $_SESSION['search_page'];
        } else {
            unset($_GET['psi']);
        }
    }

	// Search request generated?
	if (isset($_POST['search_for']) && !empty($_POST['search_for'])) {
		$_SESSION['search_for'] = trim(clean_input($_POST['search_for']));

		$_SESSION['search_common'] = $_POST['search_common'];

		$_SESSION['search_status'] = $_POST['search_status'];

		$start_index = 0;
	} else {
        $start_index = isset($_GET['psi']) ? (int)$_GET['psi'] : 0;
        
		if (isset($_SESSION['search_for']) && !isset($_GET['psi'])) {
			// He have not got scroll through patient records.
			unset($_SESSION['search_for']);
			unset($_SESSION['search_common']);
			unset($_SESSION['search_status']);
		} 
	}

    $_SESSION['search_page'] = $start_index;

	$search_query = '';
	$count_query = '';

	if (isset($_SESSION['search_for'])) {
		gen_manage_domain_query($search_query,
			$count_query,
			$admin_id,
			$start_index,
			$rows_per_page,
			$_SESSION['search_for'],
			$_SESSION['search_common'],
			$_SESSION['search_status']
		);

		gen_manage_domain_search_options($tpl, $_SESSION['search_for'], $_SESSION['search_common'], $_SESSION['search_status']);
	} else {
		gen_manage_domain_query($search_query,
			$count_query,
			$admin_id,
			$start_index,
			$rows_per_page,
			'n/a',
			'n/a',
			'n/a'
		);

		gen_manage_domain_search_options($tpl, 'n/a', 'n/a', 'n/a');
	}

	$rs = execute_query($sql, $count_query);

	$records_count = $rs->fields['cnt'];

	$rs = execute_query($sql, $search_query);

	if ($records_count == 0) {
		if (isset($_SESSION['search_for'])) {
			$tpl->assign(
				array(
					'USERS_LIST'		=> '',
					'SCROLL_PREV'		=> '',
					'SCROLL_NEXT'		=> '',
					'TR_VIEW_DETAILS'	=> tr('View aliases'),
					'SHOW_DETAILS'		=> tr("Show")
				)
			);

			set_page_message(
				tr('Not found user records matching the search criteria!'),
				'info'
			);

			unset($_SESSION['search_for']);
			unset($_SESSION['search_common']);
			unset($_SESSION['search_status']);
		} else {
			$tpl->assign(
				array(
					'USERS_LIST' => '',
					'SCROLL_PREV' => '',
					'SCROLL_NEXT' => '',
					'TR_VIEW_DETAILS' => tr('View aliases'),
					'SHOW_DETAILS' => tr("Show")
				)
			);

			set_page_message(tr('You have no users.'), 'info');
		}
	} else {
		$prev_si = $start_index - $rows_per_page;

		if ($start_index == 0) {
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

		if ($next_si + 1 > $records_count) {
			$tpl->assign('SCROLL_NEXT', '');
		} else {
			$tpl->assign(
				array(
					'SCROLL_NEXT_GRAY' => '',
					'NEXT_PSI' => $next_si
				)
			);
		}

		while (!$rs->EOF) {
			if ($rs->fields['status'] == $cfg->ITEM_OK_STATUS) {
				$status_icon = "ok";
			} else if ($rs->fields['status'] == $cfg->ITEM_DISABLED_STATUS) {
				$status_icon = "disabled";
			} else if (
					$rs->fields['status'] == $cfg->ITEM_ADD_STATUS ||
					$rs->fields['status'] == $cfg->ITEM_CHANGE_STATUS ||
					$rs->fields['status'] == $cfg->ITEM_TOENABLE_STATUS ||
					$rs->fields['status'] == $cfg->ITEM_RESTORE_STATUS ||
					$rs->fields['status'] == $cfg->ITEM_TODISABLED_STATUS ||
					$rs->fields['status'] == $cfg->ITEM_DELETE_STATUS
				){
				$status_icon = "reload";
			} else {
				$status_icon = "error";
			}
			$status_url = $rs->fields['domain_id'];

			$tpl->append(
				array(
					'STATUS_ICON' => $status_icon,
					'URL_CHANGE_STATUS' => $status_url,
				)
			);

			$admin_name = decode_idna($rs->fields['domain_name']);

			$dom_created = $rs->fields['domain_created'];

			$dom_expires = $rs->fields['domain_expires'];

			if ($dom_created == 0) {
				$dom_created = tr('N/A');
			} else {
				$dom_created = date($cfg->DATE_FORMAT, $dom_created);
			}

			if ($dom_expires == 0) {
				$dom_expires = tr('Not Set');
			} else {
				$dom_expires = date($cfg->DATE_FORMAT, $dom_expires);
			}

			$tpl->append(
				array(
					'CREATION_DATE'	=> $dom_created,
					'EXPIRE_DATE'	=> $dom_expires,
					'DOMAIN_ID'		=> $rs->fields['domain_id'],
					'NAME'			=> tohtml($admin_name),
					'USER_ID'		=> $rs->fields['domain_admin_id'],
					'DISK_USAGE'	=> ($rs->fields['domain_disk_limit'])
						? tr('%1$s of %2$s MB', round($rs->fields['domain_disk_usage'] / 1024 / 1024,1), $rs->fields['domain_disk_limit'])
						: tr('%1$s of <strong>unlimited</strong> MB', round($rs->fields['domain_disk_usage'] / 1024 / 1024,1))
				)
			);

			gen_domain_details($tpl, $sql, $rs->fields['domain_id']);
			$rs->moveNext();
		}

	}
}

function check_externel_events($tpl) {

	global $externel_event;

	if (isset($_SESSION["user_add3_added"])) {
		if ($_SESSION["user_add3_added"] === '_yes_') {
			set_page_message(tr('User added!'), 'success');

			$externel_event = '_on_';
			unset($_SESSION["user_add3_added"]);
		}
	} else if (isset($_SESSION["edit"])) {
		if ($_SESSION["edit"] === '_yes_') {
			set_page_message(tr('User data updated sucessfully!'), 'success');
		} else {
			set_page_message(tr('User data not updated sucessfully!'), 'error');
		}
		unset($_SESSION["edit"]);
	} else if (isset($_SESSION["user_has_domain"])) {
		if ($_SESSION["user_has_domain"] == '_yes_') {
			set_page_message(
				tr('This user has domain records!<br />First remove the domains from the system!'),
				'error'
			);
		}

		unset($_SESSION["user_has_domain"]);
	} else if (isset($_SESSION['user_deleted'])) {
		if ($_SESSION['user_deleted'] == '_yes_') {
			set_page_message(tr('User terminated!'), 'success');
		} else {
			set_page_message(tr('User not terminated!') , 'error');
		}

		unset($_SESSION['user_deleted']);
	}
}
?>