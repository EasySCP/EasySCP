<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
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

$interfaces=new EasySCP_NetworkCard();

show_Network_Cards($tpl, $interfaces);

add_ip($tpl, $sql);

show_IPs($tpl, $sql);

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP - Admin/IP manage'),
		'TR_SETTINGS'		=> tr('Settings'),
		'MANAGE_IPS'		=> tr('Manage IPs'),
		'TR_AVAILABLE_IPS'	=> tr('Available IPs'),
		'TR_IP'				=> tr('IP'),
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
 * @param EasySCP_Database $sql
 */
function show_IPs($tpl, $sql) {

	$cfg = EasySCP_Registry::get('Config');

	$query = "
		SELECT
			*
		FROM
			`server_ips`
	";
	$rs = exec_query($sql, $query);

	$row = 1;
	$single = false;

	if ($rs->recordCount() < 2) {
		$single = true;
	}

	while (!$rs->EOF) {
		$tpl->assign('IP_CLASS', ($row++ % 2 == 0) ? 'content' : 'content2');

		list($ip_action, $ip_action_script) = gen_ip_action($rs->fields['ip_id'], $rs->fields['ip_status']);

		$tpl->append(
			array(
				'IP'			=> $rs->fields['ip_number'],
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
 * @param EasySCP_Database $sql
 */
function add_ip($tpl, $sql) {

	global $ip_number, $domain, $alias, $ip_card;
	$cfg = EasySCP_Registry::get('Config');

	if (isset($_POST['uaction']) && $_POST['uaction'] === 'add_ip') {
		if (check_user_data()) {

			$query = "
				INSERT INTO `server_ips`
					(`ip_number`, `ip_domain`, `ip_alias`, `ip_card`,
					`ip_ssl_domain_id`, `ip_status`)
				VALUES
					(?, ?, ?, ?, ?, ?)
			";
			exec_query($sql, $query, array($ip_number, htmlspecialchars($domain, ENT_QUOTES, "UTF-8"),
			htmlspecialchars($alias, ENT_QUOTES, "UTF-8"), htmlspecialchars($ip_card, ENT_QUOTES, "UTF-8"), NULL, $cfg->ITEM_ADD_STATUS));

			send_request();

			set_page_message(tr('New IP was added!'), 'success');

			write_log("{$_SESSION['user_logged']}: adds new IPv4 address: {$ip_number}!");

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
				'VALUE_DOMAIN'	=> '',
				'VALUE_ALIAS'	=> '',
			)
		);
	}
}

function check_user_data() {
	global $ip_number, $interfaces;

	$ip_number = trim($_POST['ip_number_1'])
		. '.' . trim($_POST['ip_number_2'])
		. '.' . trim($_POST['ip_number_3'])
		. '.' . trim($_POST['ip_number_4']);

	global $domain, $alias, $ip_card;

	$domain = clean_input($_POST['domain']);
	$alias = clean_input($_POST['alias']);
	$ip_card = clean_input($_POST['ip_card']);

	$err_msg = '_off_';

	if (filter_var($ip_number, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
		$err_msg = tr('Wrong IP number!');
	} elseif ($domain == '') {
		$err_msg = tr('Please specify domain!');
	} elseif ($alias == '') {
		$err_msg = tr('Please specify alias!');
	} elseif (IP_exists()) {
		$err_msg = tr('This IP already exist!');
	} elseif (!in_array($ip_card, $interfaces->getAvailableInterface())) {
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

	$sql = EasySCP_Registry::get('Db');

	global $ip_number;

	$query = "
		SELECT
			*
		FROM
			`server_ips`
		WHERE
			`ip_number` = ?
	";

	$rs = exec_query($sql, $query, $ip_number);

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