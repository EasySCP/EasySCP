<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
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

if (isset($_GET['del_id']) && !empty($_GET['del_id']) && is_numeric($_GET['del_id'])) {
	$sql_param = array(
		'alias_id'		=> $_GET['del_id'],
		'domain_id'		=> get_user_domain_id($_SESSION['user_id']),
		'status'		=> $cfg->ITEM_ORDERED_STATUS
	);

	$sql_query = "
		DELETE FROM
			`domain_aliasses`
		WHERE
			`alias_id` = :alias_id
		AND
			`domain_id` = :domain_id
		AND
			`status` = :status;
	";

	DB::prepare($sql_query);
	DB::execute($sql_param);
} else {
	$_SESSION['orderaldel'] = '_no_';
}

user_goto('domains_manage.php');
?>