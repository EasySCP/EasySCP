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

if (isset($_GET['id']) && $_GET['id'] !== '') {
	$als_id = $_GET['id'];
	$dmn_id = get_user_domain_id($_SESSION['user_id']);

	$query = "
		SELECT
			`alias_id`
			`alias_name`
		FROM
			`domain_aliasses`
		WHERE
			`domain_id` = ?
		AND
			`alias_id` = ?
	";

	$rs = exec_query($sql, $query, array($dmn_id, $als_id));
	$alias_name = $rs->fields['alias_name'];

	if ($rs->recordCount() == 0) {
		user_goto('domains_manage.php');
	}

	// check for subdomains
	$query = "
		SELECT
			COUNT(`subdomain_alias_id`) AS `count`
		FROM
			`subdomain_alias`
		WHERE
			`alias_id` = ?
	";

	$rs = exec_query($sql, $query, $als_id);
	if ($rs->fields['count'] > 0) {
		set_page_message(
			tr('Domain alias you are trying to remove has subdomains!<br />First remove them!'),
			'error'
		);
		user_goto('domains_manage.php');
	}

	// check for mail accounts
	$query = "
		SELECT
			COUNT(`mail_id`) AS `cnt`
		FROM
			`mail_users`
		WHERE
			(`sub_id` = ?
			AND
			`mail_type` LIKE '%alias_%')
		OR
			(`sub_id` IN (SELECT `subdomain_alias_id` FROM `subdomain_alias` WHERE `alias_id` = ?)
			AND
			`mail_type` LIKE '%alssub_%')
	";

	$rs = exec_query($sql, $query, array($als_id, $als_id));

	if ($rs->fields['cnt'] > 0) {
		set_page_message(
			tr('Domain alias you are trying to remove has email accounts!<br />First remove them!'),
			'error'
		);
		user_goto('domains_manage.php');
	}

	// check for ftp accounts
	$query = "
		SELECT
			COUNT(`fg`.`gid`) AS `ftpnum`
		FROM
			`ftp_group` `fg`,
			`domain` `dmn`,
			`domain_aliasses` `d`
		WHERE
			`d`.`alias_id` = ?
		AND
			`fg`.`groupname` = `dmn`.`domain_name`
		AND
			`fg`.`members` RLIKE `d`.`alias_name`
		AND
			`d`.`domain_id` = `dmn`.`domain_id`
	";

	$rs = exec_query($sql, $query, $als_id);
	if ($rs->fields['ftpnum'] > 0) {
		set_page_message(
			tr('Domain alias you are trying to remove has FTP accounts!<br />First remove them!'),
			'error'
		);
		user_goto('domains_manage.php');
	}

	$query = "
		UPDATE
			`domain_aliasses`
		SET
			`status` = 'delete'
		WHERE
			`alias_id` = ?
	";

	$rs = exec_query($sql, $query, $als_id);

	update_reseller_c_props(get_reseller_id($dmn_id));

	send_request('110 DOMAIN alias '.$als_id);
	write_log($_SESSION['user_logged'].": delete alias ".$alias_name."!");
	set_page_message(tr('Alias scheduled for deletion!'), 'success');
	user_goto('domains_manage.php');
} else {
	user_goto('domains_manage.php');
}
