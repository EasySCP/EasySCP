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

if (isset($_GET['edit_id']) && !empty($_GET['edit_id'])) {

	$cfg = EasySCP_Registry::get('Config');

	$dns_id = (int) $_GET['edit_id'];
	$dmn_id = get_user_domain_id($sql, $_SESSION['user_id']);
	
	if (!check_dns_record_owned($_SESSION['user_id'], $_GET['edit_id'])) {
		user_goto('dns_overview.php');
	}
	
	$sql_param = array(
		'record_id' => $_GET['edit_id'],
	);
	
	
	$sql_query = "
		SELECT
			`protected`
		FROM
			`powerdns`.`records`
		WHERE
			`id` = :record_id
	";
	
	DB::prepare($sql_query);
	$row = DB::execute($sql_param, true);
	if ($row['protected']==1) {
		set_page_message(
			tr('You are not allowed to remove this DNS record!'),
			'error'
		);
		user_goto('dns_overview.php');
	}
	
	$sql_query = "
		DELETE FROM
			`powerdns`.`records`
		WHERE
			`id` = :record_id
	";
	

	DB::prepare($sql_query);
	if (DB::execute($sql_param)) {
		set_page_message(tr('Custom DNS record scheduled for deletion!'), 'success');
		user_goto('dns_overview.php');
	}
}

//  Back to the main page
user_goto('dns_overview.php');
