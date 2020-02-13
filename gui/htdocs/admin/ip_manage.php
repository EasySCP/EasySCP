<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2020 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'admin/ip_manage.tpl';

$interfaces = new EasySCP_NetworkCard();

show_Network_Cards($tpl, $interfaces);

add_ip($tpl);

show_IPs($tpl);

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP - Admin/IP manage'),
		'TR_SETTINGS'		=> tr('Settings'),
		'MANAGE_IPS'		=> tr('Manage IPs'),
		'TR_AVAILABLE_IPS'	=> tr('Available IPs'),
		'TR_IP'				=> tr('IP'),
		'TR_IPv6'			=> tr('IPv6'),
		'TR_DOMAIN'			=> tr('Domain'),
		'TR_ALIAS'			=> tr('Alias'),
		'TR_ACTION'			=> tr('Action'),
		'TR_NETWORK_CARD'	=> tr('Network interface'),
		'TR_ADD'			=> tr('Add'),
		'TR_ADD_NEW_IP'		=> tr('Add new IP'),
		'TR_MESSAGE_DELETE'	=> tr('Are you sure you want to delete this IP: %s?', true, '%s')
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_settings.tpl');
gen_admin_menu($tpl, 'admin/menu_settings.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

function gen_ip_action($ip_id, $status) {

	$cfg = EasySCP_Registry::get('Config');

	if ($status == $cfg->ITEM_OK_STATUS) {
		return array(tr('Remove IP'), 'ip_delete.php?delete_id=' . $ip_id);
	} else {
		return array(tr('N/A'), '#');
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function show_IPs($tpl) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			*
		FROM
			`server_ips`
	";
	$rs = exec_query($sql, $query);

	$single = false;

	if ($rs->recordCount() < 2) {
		$single = true;
	}

	while (!$rs->EOF) {
		list($ip_action, $ip_action_script) = gen_ip_action($rs->fields['ip_id'], $rs->fields['ip_status']);

		$tpl->append(
			array(
				'IP'			=> $rs->fields['ip_number'],
				'IPv6'			=> $rs->fields['ip_number_v6'],
				'DOMAIN'		=> tohtml($rs->fields['ip_domain']),
				'ALIAS'			=> tohtml($rs->fields['ip_alias']),
				'NETWORK_CARD'	=> ($rs->fields['ip_card'] === NULL) ? '' : tohtml($rs->fields['ip_card'])
			)
		);

		if ($single == true) {
			$tpl->append('IP_ACTION', false);
		} else {
			$tpl->append(
				array(
					'IP_DELETE_SHOW'	=> '',
					'IP_ACTION'			=> ($cfg->BASE_SERVER_IP == $rs->fields['ip_number']) ? false : $ip_action,
					'IP_ACTION_SCRIPT'	=> ($cfg->BASE_SERVER_IP == $rs->fields['ip_number']) ? '#' : $ip_action_script
				)
			);
		}


		$rs->moveNext();
	} // end while
}

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function add_ip($tpl) {

	$cfg = EasySCP_Registry::get('Config');

	if (isset($_POST['uaction']) && $_POST['uaction'] === 'add_ip') {
		if (check_user_data()) {

			$sql_param = array(
				':ip_number'		=> trim($_POST['ip_number_1']) . '.' . trim($_POST['ip_number_2']) . '.' . trim($_POST['ip_number_3']) . '.' . trim($_POST['ip_number_4']),
				':ip_number_v6'	 	=> trim($_POST['ipv6']),
				':ip_domain'		=> htmlspecialchars(trim($_POST['domain']), ENT_QUOTES, 'UTF-8'),
				':ip_alias'			=> htmlspecialchars(trim($_POST['alias']), ENT_QUOTES, 'UTF-8'),
				':ip_card'			=> htmlspecialchars(trim($_POST['ip_card']), ENT_QUOTES, 'UTF-8'),
				':ip_ssl_domain_id'	=> NULL,
				// TODO Check IP ADD Status
				// ':ip_status'		=> $cfg->ITEM_ADD_STATUS
				':ip_status'		=> $cfg->ITEM_OK_STATUS

			);

			$sql_query = "
				INSERT INTO
					server_ips (ip_number, ip_number_v6, ip_domain, ip_alias, ip_card, ip_ssl_domain_id, ip_status)
				VALUES
					(:ip_number, :ip_number_v6, :ip_domain, :ip_alias, :ip_card, :ip_ssl_domain_id, :ip_status)
			";

			DB::prepare($sql_query);
			DB::execute($sql_param)->closeCursor();

			// todo Prüfen wie man das zukünftig behandeln soll
			// send_request();

			set_page_message(tr('New IP was added!'), 'success');

			write_log('{'.$_SESSION['user_logged'].'}: adds new IPv4 address: {'.trim($_POST['ip_number_1']) . '.' . trim($_POST['ip_number_2']) . '.' . trim($_POST['ip_number_3']) . '.' . trim($_POST['ip_number_4']).'}!');

			if (isset($_POST['ipv6']) && $_POST['ipv6'] != ''){
				write_log('{'.$_SESSION['user_logged'].'}: adds new IPv6 address: {'.trim($_POST['ipv6']).'}!');
			}

			$sucess = true;
		}
	}

	if (!isset($sucess) && isset($_POST['ip_number_1'])) {
		$tpl->assign(
			array(
				'VALUE_IP1'		=> tohtml($_POST['ip_number_1']),
				'VALUE_IP2'		=> tohtml($_POST['ip_number_2']),
				'VALUE_IP3'		=> tohtml($_POST['ip_number_3']),
				'VALUE_IP4'		=> tohtml($_POST['ip_number_4']),
				'VALUE_IPv6'	=> tohtml($_POST['ipv6']),
				'VALUE_DOMAIN'	=> clean_input($_POST['domain'], true),
				'VALUE_ALIAS'	=> clean_input($_POST['alias'], true),
			)
		);
	} else {
		$tpl->assign(
			array(
				'VALUE_IP1'		=> '',
				'VALUE_IP2'		=> '',
				'VALUE_IP3'		=> '',
				'VALUE_IP4'		=> '',
				'VALUE_IPv6'	=> '',
				'VALUE_DOMAIN'	=> '',
				'VALUE_ALIAS'	=> '',
			)
		);
	}
}

function check_user_data() {
	global $interfaces;

	$ip_number = trim($_POST['ip_number_1']) . '.' . trim($_POST['ip_number_2']) . '.' . trim($_POST['ip_number_3']) . '.' . trim($_POST['ip_number_4']);

	$err_msg = '_off_';

	if (filter_var($ip_number, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
		$err_msg = tr('Wrong IP number!');
	} elseif (clean_input($_POST['domain']) == '') {
		$err_msg = tr('Please specify domain!');
	} elseif (clean_input($_POST['alias']) == '') {
		$err_msg = tr('Please specify alias!');
	} elseif (IP_exists()) {
		$err_msg = tr('This IP already exist!');
	} elseif (!in_array(clean_input($_POST['ip_card']), $interfaces->getAvailableInterface())) {
		$err_msg = tr('Please select nework interface!');
	}

	if ($err_msg == '_off_') {
		return true;
	} else {
		set_page_message($err_msg, 'error');
		return false;
	}
}

function IP_exists() {

	$sql_param = array(
		':ip_number' => trim($_POST['ip_number_1'])	. '.' . trim($_POST['ip_number_2'])	. '.' . trim($_POST['ip_number_3'])	. '.' . trim($_POST['ip_number_4'])
	);

	$sql_query = "
		SELECT
			*
		FROM
			server_ips
		WHERE
			ip_number = :ip_number;
		";

	DB::prepare($sql_query);
	$rs = DB::execute($sql_param);

	if ($rs->rowCount() == 0) {
		return false;
	}
	return true;
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_NetworkCard $interfaces
 */
function show_Network_Cards($tpl, $interfaces) {

	if ($interfaces->getErrors() != '') {
		set_page_message($interfaces->getErrors(), 'error');
	}
	if ($interfaces->getAvailableInterface() != array()) {
		foreach ($interfaces->getAvailableInterface() as $interface) {
			$tpl->append(
				array(
					'NETWORK_CARDS'	=> $interface
				)
			);
		}
	} else {
		$tpl->assign(
			array(
				'NETWORK_CARDS'	=> ''
			)
		);
	}
}
?>