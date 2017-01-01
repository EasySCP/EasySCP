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

if (isset($_GET['id']) && $_GET['id'] !== '') {
	$ftp_id = $_GET['id'];
	$dmn_name = $_SESSION['user_logged'];

	$query = "
		SELECT
			`t1`.`userid`, `t1`.`uid`, `t2`.`domain_uid`
		FROM
			`ftp_users` AS `t1`, `domain` AS `t2`
		WHERE
			`t1`.`userid` = ?
		AND
			`t1`.`uid` = t2.`domain_uid`
		AND
			`t2`.`domain_name` = ?
		;
	";

	$rs = exec_query($sql, $query, array($ftp_id, $dmn_name));
	$ftp_name = $rs->fields['userid'];

	if ($rs->recordCount() == 0) {
		user_goto('ftp_accounts.php');
	}

	$query = "
		SELECT
			`t1`.`gid`, t2.`members`
		FROM
			`ftp_users` AS `t1`, `ftp_group` AS `t2`
		WHERE
			`t1`.`gid` = `t2`.`gid`
		AND
			`t1`.`userid` = ?
		;
	";

	$rs = exec_query($sql, $query, $ftp_id);

	$ftp_gid = $rs->fields['gid'];
	$ftp_members = $rs->fields['members'];
	$members = preg_replace("/$ftp_id/", "", "$ftp_members");
	$members = preg_replace("/,,/", ",", "$members");
	$members = preg_replace("/^,/", "", "$members");
	$members = preg_replace("/,$/", "", "$members");

	if (strlen($members) == 0) {
		$query = "
			DELETE FROM
				`ftp_group`
			WHERE
				`gid` = ?
			;
		";

		$rs = exec_query($sql, $query, $ftp_gid);

	} else {
		$query = "
			UPDATE
				`ftp_group`
			SET
				`members` = ?
			WHERE
				`gid` = ?
			;
		";

		$rs = exec_query($sql, $query, array($members, $ftp_gid));
	}

	$query = "
		DELETE FROM
			`ftp_users`
		WHERE
			`userid` = ?
		;
	";

	$rs = exec_query($sql, $query, $ftp_id);

	$domain_props = get_domain_default_props($_SESSION['user_id']);
	update_reseller_c_props($domain_props['domain_created_id']);

	write_log($_SESSION['user_logged'].": deletes FTP account: ".$ftp_name);
	set_page_message(tr('FTP account deleted successfully!'), 'success');
	user_goto('ftp_accounts.php');

} else {
	user_goto('ftp_accounts.php');
}
