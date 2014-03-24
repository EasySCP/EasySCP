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

if (isset($_GET['id']) && $_GET['id'] !== '') {
	global $delete_id;
	$delete_id = $_GET['id'];
} else {
	user_goto('mail_accounts.php');
}

// Test if we have a proper delete_id.
if (!isset($delete_id)) {
	user_goto('mail_accounts.php');
}

if (!is_numeric($delete_id)) {
	user_goto('mail_accounts.php');
}

$dmn_name = $_SESSION['user_logged'];

$query = "
	SELECT
		t1.`mail_id`, t2.`domain_id`, t2.`domain_name`
	FROM
		`mail_users` AS t1,
		`domain` AS t2
	WHERE
		t1.`mail_id` = ?
	AND
		t1.`domain_id` = t2.`domain_id`
	AND
		t2.`domain_name` = ?
";

$rs = exec_query($sql, $query, array($delete_id, $dmn_name));
if ($rs->recordCount() == 0) {
	user_goto('mail_accounts.php');
}

// check for catchall assigment !!
$query = "SELECT `mail_acc`, `domain_id`, `sub_id`, `mail_type` FROM `mail_users` WHERE `mail_id` = ?";
$res = exec_query($sql, $query, $delete_id);
$data = $res->fetchRow();

if (preg_match("/".MT_NORMAL_MAIL."/", $data['mail_type']) || preg_match("/".MT_NORMAL_FORWARD."/", $data['mail_type'])) {
	// mail to normal domain
	// global $domain_name;
	$mail_name = $data['mail_acc'] . '@' . $_SESSION['user_logged']; //$domain_name;
} else if (preg_match("/".MT_ALIAS_MAIL."/", $data['mail_type']) || preg_match("/".MT_ALIAS_FORWARD."/", $data['mail_type'])) {
	// mail to domain alias
	$res_tmp = exec_query($sql, "SELECT `alias_name` FROM `domain_aliasses` WHERE `alias_id` = ?", $data['sub_id']);
	$dat_tmp = $res_tmp->fetchRow();
	$mail_name = $data['mail_acc'] . '@' . $dat_tmp['alias_name'];
} else if (preg_match("/".MT_SUBDOM_MAIL."/", $data['mail_type']) || preg_match("/".MT_SUBDOM_FORWARD."/", $data['mail_type'])) {
	// mail to subdomain
	$res_tmp = exec_query($sql, "SELECT `subdomain_name` FROM `subdomain` WHERE `subdomain_id` = ?", $data['sub_id']);
	$dat_tmp = $res_tmp->fetchRow();
	$mail_name = $data['mail_acc'] . '@' . $dat_tmp['subdomain_name'].'.'.$dmn_name;
} else if (preg_match("/".MT_ALSSUB_MAIL."/", $data['mail_type']) || preg_match("/".MT_ALSSUB_FORWARD."/", $data['mail_type'])) {
	// mail to subdomain
	$res_tmp = exec_query($sql, "SELECT `subdomain_alias_name`, `alias_name` FROM `subdomain_alias` AS t1, `domain_aliasses` AS t2 WHERE t1.`alias_id` = t2.`alias_id` AND `subdomain_alias_id` = ?", $data['sub_id']);
	$dat_tmp = $res_tmp->fetchRow();
	$mail_name = $data['mail_acc'] . '@' . $dat_tmp['subdomain_alias_name'].'.'.$dat_tmp['alias_name'];
}

$query = "SELECT `mail_id` FROM `mail_users` WHERE `mail_acc` = ? OR `mail_acc` LIKE ? OR `mail_acc` LIKE ? OR `mail_acc` LIKE ?";
$res_tmp = exec_query($sql, $query, array($mail_name, "$mail_name,%", "%,$mail_name,%", "%,$mail_name"));
$num = $res_tmp->rowCount();
if ($num > 0) {
	set_page_message(
		tr('First delete the CatchAll account for this email!'),
		'warning'
	);
	$_SESSION['catchall_assigned'] = 1;
	user_goto('mail_accounts.php');
}

$sql_param = array(
	':status'	=> $cfg->ITEM_DELETE_STATUS,
	':mail_id'	=> $delete_id
);
$sql_query = "
	UPDATE
		`mail_users`
	SET
		`status` = :status
	WHERE
		`mail_id` = :mail_id
";
DB::prepare($sql_query);
DB::execute($sql_param);

update_reseller_c_props(get_reseller_id($data['domain_id']));

send_request('130 MAIL '.$data['domain_id']);
$admin_login = decode_idna($_SESSION['user_logged']);
write_log("$admin_login: deletes mail account: " . $mail_name);
$_SESSION['maildel'] = 1;

user_goto('mail_accounts.php');
