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

if (isset($_GET['del_id']))
	$del_id = $_GET['del_id'];
else {
	$_SESSION['aldel'] = '_no_';
	user_goto('alias.php');
}
$reseller_id = $_SESSION['user_id'];

$query = "
	SELECT
		t1.`domain_id`, t1.`alias_id`, t1.`alias_name`,
		t2.`domain_id`, t2.`domain_created_id`
	FROM
		`domain_aliasses` AS t1,
		`domain` AS t2
	WHERE
		t1.`alias_id` = ?
	AND
		t1.`domain_id` = t2.`domain_id`
	AND
		t2.`domain_created_id` = ?
";

$rs = exec_query($sql, $query, array($del_id, $reseller_id));

if ($rs->recordCount() == 0) {
	user_goto('alias.php');
}

$alias_name = $rs->fields['alias_name'];

// check for mail acc in ALIAS domain (ALIAS MAIL) and delete them
$query = "
	UPDATE
		`mail_users`
	SET
		`status` = ?
	WHERE
		(`sub_id` = ?
		AND
		`mail_type` LIKE '%alias_%')
	OR
		(`sub_id` IN (SELECT `subdomain_alias_id` FROM `subdomain_alias` WHERE `alias_id` = ?)
		AND
		`mail_type` LIKE '%alssub_%')
";

exec_query($sql, $query, array($cfg->ITEM_DELETE_STATUS, $del_id, $del_id));

$res = exec_query($sql, "SELECT `alias_name` FROM `domain_aliasses` WHERE `alias_id` = ?", $del_id);
$dat = $res->fetchRow();

// TODO Use prepared statements
exec_query($sql, "UPDATE `subdomain_alias` SET `status` = '" . $cfg->ITEM_DELETE_STATUS . "' WHERE `alias_id` = ?", $del_id);
// TODO Use prepared statements
exec_query($sql, "UPDATE `domain_aliasses` SET `status` = '" . $cfg->ITEM_DELETE_STATUS . "' WHERE `alias_id` = ?", $del_id);

update_reseller_c_props($reseller_id);

send_request('110 DOMAIN alias '.$del_id);
$admin_login = $_SESSION['user_logged'];
write_log("$admin_login: deletes domain alias: " . $dat['alias_name']);

$_SESSION['aldel'] = '_yes_';

user_goto('alias.php');
