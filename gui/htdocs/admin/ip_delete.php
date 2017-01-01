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

// Test if we have a proper delete_id.
if (!isset($_GET['delete_id'])) {
	user_goto('ip_manage.php');
}

if (!is_numeric($_GET['delete_id'])) {
	set_page_message(tr('You cannot delete the last active IP address!'), 'error');
	user_goto('ip_manage.php');
}

$delete_id = $_GET['delete_id'];

// check for domains that use this IP
$query = "
	SELECT
		COUNT(`domain_id`) AS dcnt
	FROM
		`domain`
	WHERE
		`domain_ip_id` = ?
";

$rs = exec_query($sql, $query, $delete_id);

if ($rs->fields['dcnt'] > 0) {
	// ERROR - we have domain(s) that use this IP

	set_page_message(tr('You have a domain using this IP!'), 'error');

	user_goto('ip_manage.php');
}
// check if the IP is assigned to reseller
$query = "SELECT `reseller_ips` FROM `reseller_props`";

$res = exec_query($sql, $query);

while (($data = $res->fetchRow())) {
	if (preg_match("/$delete_id;/", $data['reseller_ips'])) {
		set_page_message(tr('You have a reseller using this IP!'), 'error');
		user_goto('ip_manage.php');
	}
}

$query = "
	SELECT
		*
	FROM
		`server_ips`
	WHERE
		`ip_id` = ?
";

$rs = exec_query($sql, $query, $delete_id);

$user_logged = $_SESSION['user_logged'];

$ip_number = $rs->fields['ip_number'];

write_log("$user_logged: deletes IP address $ip_number");

// delete it !
$sql_param = array(
	':ip_id'		=> $delete_id
);

$sql_query = "
	DELETE FROM
		server_ips
	WHERE
		ip_id = :ip_id
";
DB::prepare($sql_query);
DB::execute($sql_param)->closeCursor();

/*
$query = "
	UPDATE
		`server_ips`
	SET
		`ip_status` = ?
	WHERE
		`ip_id` = ?
	LIMIT 1
";

$rs = exec_query($sql, $query, array($cfg->ITEM_DELETE_STATUS, $delete_id));
*/

// send_request();

set_page_message(tr('IP was deleted!'), 'success');

user_goto('ip_manage.php');
