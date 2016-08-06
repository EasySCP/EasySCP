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

$dmn_id = get_user_domain_id($_SESSION['user_id']);


if (isset($_GET['gname'])
	&& $_GET['gname'] !== ''
	&& is_numeric($_GET['gname'])) {
	$group_id = $_GET['gname'];
} else {
	user_goto('protected_areas.php');
}

$change_status = $cfg->ITEM_DELETE_STATUS;
$awstats_auth = $cfg->AWSTATS_GROUP_AUTH;

$sql_param = array(
	'change_status'	=> $change_status,
	'group_id'		=> $group_id,
	'dmn_id'		=> $dmn_id,
	'awstats_auth'	=> $awstats_auth
);

$sql_query = "
	UPDATE
		`htaccess_groups`
	SET
		`status` = :change_status
	WHERE
		`id` = :group_id
	AND
		`dmn_id` = :dmn_id
	AND
		`ugroup` != :awstats_auth;
";

DB::prepare($sql_query);
DB::execute($sql_param);

$sql_param = array(
	'dmn_id' => $dmn_id
);

$sql_query = "
	SELECT
		*
	FROM
		`htaccess`
	WHERE
		`dmn_id` = :dmn_id;
";

DB::prepare($sql_query);
$stmt = DB::execute($sql_param);

foreach ($stmt as $row) {

	$ht_id = $row['id'];
	$grp_id = $row['group_id'];

	$grp_id_splited = explode(',', $grp_id);

	$key = array_search($group_id,$grp_id_splited);
	if ($key !== false) {
		unset($grp_id_splited[$key]);
		if (count($grp_id_splited) == 0) {
			$status = $cfg->ITEM_DELETE_STATUS;
		} else {
			$grp_id = implode(",", $grp_id_splited);
			$status = $cfg->ITEM_CHANGE_STATUS;
		}
		
		$sql_param = array(
			'grp_id'	=> $grp_id,
			'status'	=> $status,
			'ht_id'		=> $ht_id
		);
		
		$sql_query = "
			UPDATE
				`htaccess`
			SET
				`group_id` = :grp_id,
				`status` = :status
			WHERE
				`id` = :ht_id;
		";
		
		DB::prepare($sql_query);
		DB::execute($sql_param);
		
	}

}

send_request('110 DOMAIN htaccess ' . $dmn_id);

write_log($_SESSION['user_logged'].": deletes group ID (protected areas): $group_id");
user_goto('protected_user_manage.php');
