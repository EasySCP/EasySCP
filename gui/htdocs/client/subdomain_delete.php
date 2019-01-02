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
			`subdomain_id`,
			`subdomain_name`
		FROM
			`subdomain`
		WHERE
			`domain_id` = ?
		AND
			`subdomain_id` = ?
	";

	$rs = exec_query($sql, $query, array($dmn_id, $sub_id));
	$sub_name = $rs->fields['subdomain_name'];

	if ($rs->recordCount() == 0) {
		user_goto('domains_manage.php');
	}

	// check for mail accounts
	$query = "SELECT COUNT(`mail_id`) AS cnt FROM `mail_users` WHERE (`mail_type` LIKE '".MT_SUBDOM_MAIL."%' OR `mail_type` = '".MT_SUBDOM_FORWARD."') AND `sub_id` = ?";
	$rs = exec_query($sql, $query, $sub_id);

	if ($rs->fields['cnt'] > 0) {
		set_page_message(
			tr('The subdomain you are trying to remove has email accounts!<br />Rremove them first!'),
			'warning'
		);
		user_goto('domains_manage.php');
	}

	// check for existing aliassubdomains
	$sql_param = array(
		':subdomain_id' => $sub_id
	);
	$query = "
		SELECT 
			COUNT(subdomain_alias_id) AS cnt
		FROM
			subdomain_alias
		WHERE
			subdomain_id = :subdomain_id
	";
	DB::prepare($query);
	$row = DB::execute($sql_param)->fetch();	
	if ($row['cnt']>0){
		set_page_message(
			tr('The subdomain you are trying to remove has aliassubdomains assigned!<br />Rremove them first!'),
			'warning'
		);
		user_goto('domains_manage.php');		
	}

	$query = "
		UPDATE
			`subdomain`
		SET
			`status` = 'delete'
		WHERE
			`subdomain_id` = ?
	";
	$rs = exec_query($sql, $query, $sub_id);

	$query = "
		UPDATE
			`domain`
		SET
			`status` = 'change'
		WHERE
			`domain_id` = ?
	";
	$rs = exec_query($sql, $query, $dmn_id);

	update_reseller_c_props(get_reseller_id($dmn_id));
	
	send_request('110 DOMAIN domain '. $dmn_id);

	write_log($_SESSION['user_logged'].": deletes subdomain: ".$sub_name);
	set_page_message(tr('Subdomain scheduled for deletion!'), 'info');
	user_goto('domains_manage.php');

} else {
	user_goto('domains_manage.php');
}
