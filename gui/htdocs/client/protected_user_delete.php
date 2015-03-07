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

$dmn_id = get_user_domain_id($sql, $_SESSION['user_id']);

if (isset($_GET['uname'])
	&& $_GET['uname'] !== ''
	&& is_numeric($_GET['uname'])) {
	$uuser_id = $_GET['uname'];
} else {
	user_goto('protected_areas.php');
}

$query = "
	SELECT
		`uname`
	FROM
		`htaccess_users`
	WHERE
		`dmn_id` = ?
	AND
		`id` = ?
";

$rs = exec_query($sql, $query, array($dmn_id, $uuser_id));
$uname = $rs->fields['uname'];

$change_status = $cfg->ITEM_DELETE_STATUS;
// let's delete the user from the SQL
$query = "
	UPDATE
		`htaccess_users`
	SET
		`status` = ?
	WHERE
		`id` = ?
	AND
		`dmn_id` = ?
";

$rs = exec_query($sql, $query, array($change_status, $uuser_id, $dmn_id));

// let's delete this user if assigned to a group
$query = "
	SELECT
		`id`,
		`members`
	FROM
		`htaccess_groups`
	WHERE
		`dmn_id` = ?
";
$rs = exec_query($sql, $query, $dmn_id);

 if ($rs->recordCount() !== 0) {

	 while (!$rs->EOF) {
		$members = explode(',',$rs->fields['members']);
		$group_id = $rs->fields['id'];
		$key = array_search($uuser_id, $members);
		if ($key !== false) {
			unset($members[$key]);
			$members = implode(",", $members);
			$change_status = $cfg->ITEM_CHANGE_STATUS;
			$update_query = "
				UPDATE
					`htaccess_groups`
				SET
					`members` = ?,
					`status` = ?
				WHERE
					`id` = ?
			";
			$rs_update = exec_query($sql, $update_query, array($members, $change_status, $group_id));
		}
		$rs->moveNext();
	 }
 }

// let's delete or update htaccess files if this user is assigned
$query = "
	SELECT
		*
	FROM
		`htaccess`
	WHERE
		`dmn_id` = ?
";

$rs = exec_query($sql, $query, $dmn_id);

while (!$rs->EOF) {
	$ht_id = $rs->fields['id'];
	$usr_id = $rs->fields['user_id'];

	$usr_id_splited = explode(',', $usr_id);

	$key = array_search($uuser_id,$usr_id_splited);
	if ($key !== false) {
		unset($usr_id_splited[$key]);
		if (count($usr_id_splited) == 0) {
			$status = $cfg->ITEM_DELETE_STATUS;
		} else {
			$usr_id = implode(",", $usr_id_splited);
			$status = $cfg->ITEM_CHANGE_STATUS;
		}
		$update_query = "
			UPDATE
				`htaccess`
			SET
				`user_id` = ?,
				`status` = ?
			WHERE
				`id` = ?
		";

		$rs_update = exec_query($sql, $update_query, array($usr_id, $status, $ht_id));
	}

	$rs->moveNext();
}

send_request('110 DOMAIN htaccess ' . $dmn_id);

$admin_login = $_SESSION['user_logged'];
write_log("$admin_login: deletes user ID (protected areas): $uname");
user_goto('protected_user_manage.php');
