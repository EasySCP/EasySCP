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

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();

$template = 'reseller/alias.tpl';

// static page messages
gen_logged_from($tpl);

$err_txt = "_off_";

generate_als_list($tpl, $_SESSION['user_id'], $err_txt);

generate_als_messages($tpl, $err_txt);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP - Manage Domain/Alias'),
		'TR_MANAGE_ALIAS'	=> tr('Manage alias'),
		'TR_NAME'			=> tr('Name'),
		'TR_REAL_DOMAIN'	=> tr('Real domain'),
		'TR_FORWARD'		=> tr('Forward'),
		'TR_STATUS'			=> tr('Status'),
		'TR_ACTION'			=> tr('Action'),
		'TR_ADD_ALIAS'		=> tr('Add alias'),
		'TR_MESSAGE_DELETE'	=> tr('Are you sure you want to delete %s?', true, '%s')
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

// Function declaration

/**
 * Generate domain alias list
 *
 * @todo Use prepared statements (min. with placeholders like ":search_for")
 * @param EasySCP_TemplateEngine $tpl
 * @param int $reseller_id
 * @param string $als_err
 */
function generate_als_list($tpl, $reseller_id, &$als_err) {
	$sql = EasySCP_Registry::get('Db');
	$cfg = EasySCP_Registry::get('Config');

	list(,,,,,,$uals_current) = generate_reseller_user_props($reseller_id);
	list(,,,,,$rals_max) = get_reseller_default_props($sql, $reseller_id);

	if ($uals_current >= $rals_max && $rals_max != "0") {
		$tpl->assign('ALS_ADD_BUTTON', '');
	}

	$start_index = 0;

	$rows_per_page = $cfg->DOMAIN_ROWS_PER_PAGE;

	$current_psi = 0;
	$_SESSION['search_for'] = '';
	$search_common = '';
	$search_for = '';

	if (isset($_GET['psi'])) {
		$start_index = $_GET['psi'];
		$current_psi = $_GET['psi'];
	}

	if (isset($_POST['uaction']) && !empty($_POST['uaction'])) {

		$_SESSION['search_for'] = trim(clean_input($_POST['search_for']));
		$_SESSION['search_common'] = $_POST['search_common'];
		$search_for = $_SESSION['search_for'];
		$search_common = $_SESSION['search_common'];

	} else {

		if (isset($_SESSION['search_for']) && !isset($_GET['psi'])) {
			unset($_SESSION['search_for']);
			unset($_SESSION['search_common']);
		}
	}
	$tpl->assign(
		array(
			'PSI'				=> $current_psi,
			'SEARCH_FOR'		=> tohtml($search_for),
			'TR_SEARCH'			=> tr('Search'),
			'M_ALIAS_NAME'		=> tr('Alias name'),
			'M_ACCOUNT_NAME'	=> tr('Account name'),
		)
	);

	if (isset($_SESSION['search_for']) && $_SESSION['search_for'] != '') {
		if (isset($search_common) && $search_common == 'alias_name') {
			$query = "
				SELECT
					t1.*,
					t2.`domain_id`,
					t2.`domain_name`,
					t2.`domain_created_id`
				FROM
					`domain_aliasses` AS t1,
					`domain` AS t2
				WHERE
					`alias_name` RLIKE '$search_for'
				AND
					t2.`domain_created_id` = ?
				AND
					t1.`domain_id` = t2.`domain_id`
				ORDER BY
					t1.`alias_name` ASC
				LIMIT
					$start_index, $rows_per_page
			";
			// count query
			$count_query = "
				SELECT
					COUNT(`alias_id`) AS cnt
				FROM
					`domain_aliasses` AS t1,
					`domain` AS t2
				WHERE
					t2.`domain_created_id` = ?
				AND
					`alias_name` RLIKE '$search_for'
				AND
					t1.`domain_id` = t2.`domain_id`
			";
		} else {
			$query = "
				SELECT
					t1.*,
					t2.`domain_id`,
					t2.`domain_name`,
					t2.`domain_created_id`
				FROM
					`domain_aliasses` AS t1,
					`domain` AS t2
				WHERE
					t2.`domain_name` RLIKE '$search_for'
				AND
					t1.`domain_id` = t2.`domain_id`
				AND
					t2.`domain_created_id` = ?
				ORDER BY
					t1.`alias_name` ASC
				LIMIT
					$start_index, $rows_per_page
			";
			// count query
			$count_query = "
				SELECT
					COUNT(`alias_id`) AS cnt
				FROM
					`domain_aliasses` AS t1,
					`domain` AS t2
				WHERE
					t2.`domain_created_id` = ?
				AND
					t2.`domain_name` RLIKE '$search_for'
				AND
					t1.`domain_id` = t2.`domain_id`
			";
		}
	} else {
		$query = "
			SELECT
				t1.*,
				t2.`domain_id`,
				t2.`domain_name`,
				t2.`domain_created_id`
			FROM
				`domain_aliasses` AS t1,
				`domain` AS t2
			WHERE
				t1.`domain_id` = t2.`domain_id`
			AND
				t2.`domain_created_id` = ?
			ORDER BY
				t1.`alias_name` ASC
			LIMIT
				$start_index, $rows_per_page
		";
		// count query
		$count_query = "
			SELECT
				COUNT(`alias_id`) AS cnt
			FROM
				`domain_aliasses` AS t1,
				`domain` AS t2
			WHERE
				t1.`domain_id` = t2.domain_id
			AND
				t2.`domain_created_id` = ?
		";
	}
	// let's count
	$rs = exec_query($sql, $count_query, $reseller_id);
	$records_count = $rs->fields['cnt'];
	// Get all alias records
	$rs = exec_query($sql, $query, $reseller_id);

	if ($records_count == 0) {
		if (isset($_SESSION['search_for']) && $_SESSION['search_for'] != '') {
			$tpl->assign(
				array(
					'TABLE_LIST'				=> '',
					'USERS_LIST'				=> '',
					'SCROLL_PREV'				=> '',
					'SCROLL_NEXT'				=> '',
					'M_DOMAIN_NAME_SELECTED'	=> '',
					'M_ACCOUN_NAME_SELECTED'	=> ''
				)
			);
		} else {
			$tpl->assign(
				array(
					'TABLE_LIST'	=> '',
					'TABLE_HEADER'	=> '',
					'USERS_LIST'	=> '',
					'SCROLL_PREV'	=> '',
					'SCROLL_NEXT'	=> '',
				)
			);
		}

		if (isset($_SESSION['search_for'])) {
			$als_err = tr('Not found user records matching the search criteria!');
		} else {
			if (isset($_SESSION['almax'])) {
				if ($_SESSION['almax'] === '_yes_')
					$als_err = tr('Domain alias limit reached!');
				else
					$als_err = tr('You have no alias records.');

				unset($_SESSION['almax']);
			} else {
				$als_err = tr('You have no alias records.');
			}
		}
		return;
	} else {
		$prev_si = $start_index - $rows_per_page;

		if ($start_index == 0) {
			$tpl->assign('SCROLL_PREV', '');
		} else {
			$tpl->assign(
				array(
					'SCROLL_PREV_GRAY'	=> '',
					'PREV_PSI'			=> $prev_si
				)
			);
		}

		$next_si = $start_index + $rows_per_page;

		if ($next_si + 1 > $records_count) {
			$tpl->assign('SCROLL_NEXT', '');
		} else {
			$tpl->assign(
				array(
					'SCROLL_NEXT_GRAY'	=> '',
					'NEXT_PSI'			=> $next_si
				)
			);
		}
	}

	if (isset($_SESSION['search_common']) && $_SESSION['search_common'] === 'account_name') {
		$domain_name_selected = '';
		$account_name_selected = $cfg->HTML_SELECTED;
	} else {
		$domain_name_selected = $cfg->HTML_SELECTED;
		$account_name_selected = '';
	}

	$tpl->assign(
		array(
			'M_DOMAIN_NAME_SELECTED'	=> $domain_name_selected,
			'M_ACCOUN_NAME_SELECTED'	=> $account_name_selected
		)
	);

	while (!$rs->EOF) {
		$als_id = $rs->fields['alias_id'];
		$als_name = $rs->fields['alias_name'];
		$als_mount_point = ($rs->fields['alias_mount'] != '')
			? $rs->fields['alias_mount']
			: '/';
		$als_status = $rs->fields['status'];
		$als_ip_id = $rs->fields['alias_ip_id'];
		$als_fwd = $rs->fields['url_forward'];
		$show_als_fwd = ($als_fwd == 'no') ? "-" : $als_fwd;

		$domain_name = decode_idna($rs->fields['domain_name']);

		$query = "SELECT `ip_number`, `ip_domain` FROM `server_ips` WHERE `ip_id` = ?";

		$alsip_r = exec_query($sql, $query, $als_ip_id);
		$alsip_d = $alsip_r->fetchRow();

		$als_ip = $alsip_d['ip_number'];
		$als_ip_name = $alsip_d['ip_domain'];

		if ($als_status === $cfg->ITEM_OK_STATUS) {
			$delete_link = "alias_delete.php?del_id=" . $als_id;
			$edit_link = "alias_edit.php?edit_id=" . $als_id;
			$action_text = tr("Delete");
			$edit_text = tr("Edit");
		} else if ($als_status === $cfg->ITEM_ORDERED_STATUS) {
			$delete_link = "alias_order.php?action=delete&amp;del_id=".$als_id;
			$edit_link = "alias_order.php?action=activate&amp;act_id=".$als_id;
			$action_text = tr("Delete order");
			$edit_text = tr("Activate");
		} else {
			$delete_link = "#";
			$edit_link = "#";
			$action_text = tr('N/A');
			$edit_text = tr('N/A');
		}
		$als_status = translate_dmn_status($als_status);
		$als_name = decode_idna($als_name);
		$show_als_fwd = decode_idna($show_als_fwd);

		$tpl->append(
			array(
				'NAME'						=> tohtml($als_name),
				'ALIAS_IP'					=> tohtml("$als_ip ($als_ip_name)"),
				'REAL_DOMAIN'				=> tohtml($domain_name),
				'REAL_DOMAIN_MOUNT'			=> tohtml($als_mount_point),
				'FORWARD'					=> tohtml($show_als_fwd),
				'STATUS'					=> $als_status,
				'ID'						=> $als_id,
				'DELETE'					=> $action_text,
				'DELETE_LINK'				=> $delete_link,
				'EDIT_LINK'					=> $edit_link,
				'EDIT'						=> $edit_text
			)
		);

		$rs->moveNext();
	}
} // End of generate_als_list()

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param string $als_err
 */
function generate_als_messages($tpl, $als_err) {
	if ($als_err != '_off_') {
		set_page_message(
			$als_err,
			'info'
		);
		return;
	} else if (isset($_SESSION["dahavemail"])) {
		set_page_message(
			tr('Domain alias you are trying to remove has email accounts !<br>First remove them!'),
			'error'
		);
		unset($_SESSION['dahavemail']);
	} else if (isset($_SESSION["dahaveftp"])) {
		set_page_message(
			tr('Domain alias you are trying to remove has FTP accounts!<br>First remove them!'),
			'error'
		);
		unset($_SESSION['dahavemail']);
	} else if (isset($_SESSION["aldel"])) {
		if ('_yes_' === $_SESSION['aldel']){
			set_page_message(
				tr('Domain alias added for termination!'),
				'success'
			);
		} else {
			set_page_message(
				tr('Domain alias not added for termination!'),
				'info'
			);
		}
		unset($_SESSION['aldel']);
	} else if (isset($_SESSION['aladd'])) {
		if ('_yes_' === $_SESSION['aladd']){
			set_page_message(
				tr('Domain alias added!'),
				'success'
			);
		} else {
			set_page_message(
				tr('Domain alias not added!'),
				'info'
			);
		}
		unset($_SESSION['aladd']);
	} else if (isset($_SESSION['aledit'])) {
		if ('_yes_' === $_SESSION['aledit']){
			set_page_message(
				tr('Domain alias modified!'),
				'success'
			);
		} else {
			set_page_message(
				tr('Domain alias not modified!'),
				'info'
			);
		}
		unset($_SESSION['aledit']);
	} else if (isset($_SESSION['orderaldel'])) {
		if ('_no_' === $_SESSION['orderaldel']) {
			set_page_message(
				tr('Ordered domain alias not deleted!'),
				'info'
			);
		}
		unset($_SESSION['orderaldel']);
	} else if (isset($_SESSION['orderalact'])) {
		if ('_yes_' === $_SESSION['orderalact']){
			set_page_message(
				tr('Ordered domain alias activated!'),
				'success'
			);
		} else {
			set_page_message(
				tr('Ordered domain alias not activated!'),
				'info'
			);
		}
		unset($_SESSION['orderalact']);
	} else if (isset($_SESSION['almax'])) {
		if ('_yes_' === $_SESSION['almax']){
			set_page_message(
				tr('Domain alias limit reached!'),
				'warning'
			);
		}
		unset($_SESSION['almax']);
	}
} // End of generate_als_messages()
