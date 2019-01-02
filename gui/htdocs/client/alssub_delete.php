<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2019 by Easy Server Control Panel - http://www.easyscp.net
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

if (isset($_GET['id']) && $_GET['id'] !== '') {
	$sub_id = $_GET['id'];
	$dmn_id = get_user_domain_id($_SESSION['user_id']);

	$query = "
		SELECT
			`subdomain_alias_id`,
			`subdomain_alias_name`,
			`subdomain_alias`.`alias_id`
		FROM
			`subdomain_alias` JOIN `domain_aliasses`
		ON
			`subdomain_alias`.`alias_id` = `domain_aliasses`.`alias_id`
		WHERE
			`domain_id` = ?
		AND
			`subdomain_alias_id` = ?
	";

	$rs = exec_query($sql, $query, array($dmn_id, $sub_id));
	$sub_name = $rs->fields['subdomain_alias_name'];
	$als_id = $rs->fields['alias_id'];

	if ($rs->recordCount() == 0) {
		user_goto('domains_manage.php');
	}

	// check for mail accounts
	// TODO use prepared statement for constants
	$query = "SELECT COUNT(`mail_id`) AS cnt FROM `mail_users` WHERE (`mail_type` LIKE '".MT_ALSSUB_MAIL."%' OR `mail_type` = '".MT_ALSSUB_FORWARD."') AND `sub_id` = ?";
	$rs = exec_query($sql, $query, $sub_id);

	if ($rs->fields['cnt'] > 0) {
		set_page_message(
			tr('Subdomain you are trying to remove has email accounts !<br />First remove them!'),
			'error'
		);
		user_goto('domains_manage.php');
	}

	$query = "
		UPDATE
			`subdomain_alias`
		SET
			`status` = 'delete'
		WHERE
			`subdomain_alias_id` = ?
	";

	$rs = exec_query($sql, $query, $sub_id);
	
	$query = "
		UPDATE
			domain_aliasses
		SET
			status = 'change'
		WHERE
			alias_id = ?
	";
	$rs = exec_query($sql, $query, $als_id);
	
	send_request('110 DOMAIN alias '.$als_id);
	write_log($_SESSION['user_logged'].": delete alias subdomain: ".$sub_name);
	set_page_message(tr('Alias subdomain scheduled for deletion!'), 'success');
	user_goto('domains_manage.php');

} else {
	user_goto('domains_manage.php');
}
