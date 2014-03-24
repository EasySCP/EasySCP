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

if (isset($_GET['del_id']) && !empty($_GET['del_id'])) {
	$del_id = $_GET['del_id'];
} else {
	$_SESSION['orderaldel'] = '_no_';
	user_goto('domains_manage.php');
}

$domainId = get_user_domain_id($sql, $_SESSION['user_id']);

$query = "
	DELETE FROM
		`domain_aliasses`
	WHERE
		`alias_id` = ?
	AND
		`domain_id` = ?
	AND
		`status` = ?
	";
$rs = exec_query($sql, $query, array($domainAliasId, $domainId, $cfg->ITEM_ORDERED_STATUS));

user_goto('domains_manage.php');
?>