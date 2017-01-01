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

if (!isset($_GET['domain_id'])) {
	user_goto('manage_users.php');
}

if (!is_numeric($_GET['domain_id'])) {
	user_goto('manage_users.php');
}

// so we have domain id and let's disable or enable it
$domain_id = $_GET['domain_id'];

// check status to know if have to disable or enable it
$query = "
	SELECT
		domain_name, status
	FROM
		domain
	WHERE
		domain_id = ?
";

$rs = exec_query($sql, $query, $domain_id);

$location = 'admin';

if ($rs->fields['status'] == $cfg->ITEM_OK_STATUS) {

		//disable_domain($sql, $domain_id, $rs->fields['domain_name']);
		$action = 'disable';
		change_domain_status($sql, $domain_id, $rs->fields['domain_name'], $action, $location);

} else if ($rs->fields['status'] == $cfg->ITEM_DISABLED_STATUS) {

	//enable_domain($sql, $domain_id, $rs->fields['domain_name']);
	$action = 'enable';
	change_domain_status($sql, $domain_id, $rs->fields['domain_name'], $action, $location);

} else {
	user_goto('manage_users.php');
}
