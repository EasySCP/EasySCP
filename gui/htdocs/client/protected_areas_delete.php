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

if (isset($_GET['id']) && $_GET['id'] !== '') {

	$id = $_GET['id'];
	$dmn_id = get_user_domain_id($sql, $_SESSION['user_id']);

	$sql_param = array(
		':id'		=> $id,
		':dmn_id'	=> $dmn_id
	);

	$sql_query = "
		SELECT
			status
		FROM
			htaccess
		WHERE
			id = :id
		AND
			dmn_id = :dmn_id
		";

	DB::prepare($sql_query);
	$row = DB::execute($sql_param, true);

	if ($row['status'] !== $cfg->ITEM_OK_STATUS) {
		set_page_message(
			tr('Protected area status should be OK if you want to delete it!'),
			'error'
		);
		user_goto('protected_areas.php');
	}

	$sql_param = array(
		':status'	=> $cfg->ITEM_DELETE_STATUS,
		':id'		=> $id,
		':dmn_id'	=> $dmn_id
	);

	$sql_query = "
		UPDATE
			htaccess
		SET
			status = :status
		WHERE
			id = :id
		AND
			dmn_id = :dmn_id
		";

	DB::prepare($sql_query);
	DB::execute($sql_param);

	send_request('110 DOMAIN htaccess ' . $dmn_id);

	write_log($_SESSION['user_logged'].": deletes protected area with ID: ".$_GET['id']);
	set_page_message(tr('Protected area deleted successfully!'), 'success');
	user_goto('protected_areas.php');
} else {
	set_page_message(tr('Permission deny!'), 'error');
	user_goto('protected_areas.php');
}
?>