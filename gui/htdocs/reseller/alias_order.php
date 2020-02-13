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

if (isset($_GET['action']) && $_GET['action'] === "delete") {

	if (isset($_GET['del_id']) && !empty($_GET['del_id'])) {
		$del_id = $_GET['del_id'];
	} else {
		$_SESSION['orderaldel'] = '_no_';
		user_goto('alias.php');
	}

	$query = "DELETE FROM `domain_aliasses` WHERE `alias_id` = ?";
	$rs = exec_query($sql, $query, $del_id);

	// delete "ordered"/pending email accounts
	$domain_id = who_owns_this($del_id, 'als_id', true);
	$query = "DELETE FROM `mail_users` WHERE `sub_id` = ? AND `domain_id` = ? AND `status` = ? AND `mail_type` LIKE 'alias%'";
	$rs = exec_query($sql, $query, array($del_id, $domain_id, $cfg->ITEM_ORDERED_STATUS));

	user_goto('alias.php');

} else if (isset($_GET['action']) && $_GET['action'] === "activate") {

	if (isset($_GET['act_id']) && !empty($_GET['act_id']))
		$act_id = $_GET['act_id'];
	else {
		$_SESSION['orderalact'] = '_no_';
		user_goto('alias.php');
	}
	$query = "SELECT `alias_name` FROM `domain_aliasses` WHERE `alias_id` = ?";
	$rs = exec_query($sql, $query, $act_id);
	if ($rs->recordCount() == 0) {
		user_goto('alias.php');
	}
	$alias_name = $rs->fields['alias_name'];

	$query = "UPDATE `domain_aliasses` SET `status` = '$cfg->ITEM_ADD_STATUS' WHERE `alias_id` = ?";
	$rs = exec_query($sql, $query, $act_id);

	$domain_id = who_owns_this($act_id, 'als_id', true);
	$query = 'SELECT `email` FROM `admin`, `domain` WHERE `admin`.`admin_id` = `domain`.`domain_admin_id` AND `domain`.`domain_id` = ?';
	$rs = exec_query($sql, $query, $domain_id);
	if ($rs->recordCount() == 0) {
		user_goto('alias.php');
	}
	$user_email = $rs->fields['email'];
	// Create the 3 default addresses if wanted
	if ($cfg->CREATE_DEFAULT_EMAIL_ADDRESSES) client_mail_add_default_accounts($domain_id, $user_email, $alias_name, 'alias', $act_id);

	// enable "ordered"/pending email accounts
	// ??? are there pending mail_addresses ???, joximu
	$query = "UPDATE `mail_users` SET `status` = ? WHERE `sub_id` = ? AND `domain_id` = ? AND `status` = ? AND `mail_type` LIKE 'alias%'";
	$rs = exec_query($sql, $query, array($cfg->ITEM_ADD_STATUS, $act_id, $domain_id, $cfg->ITEM_ORDERED_STATUS));

	send_request('110 DOMAIN alias '.$act_id);
	send_request('130 MAIL '.$domain_id);

	$admin_login = $_SESSION['user_logged'];

	write_log("$admin_login: domain alias activated: $alias_name.");

	set_page_message(tr('Alias scheduled for activation!'), 'success');

	$_SESSION['orderalact'] = '_yes_';
	user_goto('alias.php');

} else {
	user_goto('alias.php');
}
