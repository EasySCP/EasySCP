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

if (isset($_GET['hpid']) && is_numeric($_GET['hpid']))
	$hpid = $_GET['hpid'];
else {
	$_SESSION['hp_deleted'] = '_no_';
	user_goto('hosting_plan.php');
}

// Check if there is no order for this plan
$res = exec_query($sql, "SELECT COUNT(`id`) FROM `orders` WHERE `plan_id` = ?", $hpid);
$data = $res->fetchRow();

if ($data['0'] > 0) {
	$_SESSION['hp_deleted_ordererror'] = '_yes_';
	user_goto('hosting_plan.php');
}

// Try to delete hosting plan from db
$query = "DELETE FROM `hosting_plans` WHERE `id` = ? AND `reseller_id` = ?";
$res = exec_query($sql, $query, array($hpid, $_SESSION['user_id']));

$_SESSION['hp_deleted'] = '_yes_';

user_goto('hosting_plan.php');
