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

// we need to check only if all vars are OK
// admin can walk into all interfaces
if (isset($_SESSION['user_id']) && isset($_GET['to_id'])) {
	$from_id = $_SESSION['user_id'];

	$to_id = $_GET['to_id'];
	// admin logged as an other admin:
	if (isset($_SESSION['logged_from']) && isset($_SESSION['logged_from_id'])) {
		$from_id = $_SESSION['logged_from_id'];
	} else {
		$from_id = $_SESSION['user_id'];
	}

	change_user_interface($from_id, $to_id);
} else {
	user_goto('manage_users.php');
}
?>