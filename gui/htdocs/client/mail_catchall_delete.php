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

if (isset($_GET['id']) && $_GET['id'] !== '') {
	$mail_id = $_GET['id'];
	$item_delete_status = $cfg->ITEM_DELETE_STATUS;
	$dmn_id = get_user_domain_id($_SESSION['user_id']);

	$query = "
		SELECT
			`mail_id`
		FROM
			`mail_users`
		WHERE
			`domain_id` = ?
		AND
			`mail_id` = ?
	";

	$rs = exec_query($sql, $query, array($dmn_id, $mail_id));

	if ($rs->recordCount() == 0) {
		user_goto('mail_catchall.php');
	}

	$query = "
		UPDATE
			`mail_users`
		SET
			`status` = ?
		WHERE
			`mail_id` = ?
	";

	$rs = exec_query($sql, $query, array($item_delete_status, $mail_id));

	send_request('130 MAIL '.$dmn_id);
	write_log($_SESSION['user_logged'].': deletes email catch all!');
	set_page_message(tr('Catch all account scheduled for deletion!'), 'success');
	user_goto('mail_catchall.php');

} else {
	user_goto('mail_catchall.php');
}
