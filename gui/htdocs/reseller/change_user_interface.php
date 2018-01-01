<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2018 by Easy Server Control Panel - http://www.easyscp.net
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

// let's back to admin interface - am I admin or what ? :-)

if (isset($_SESSION['logged_from']) && isset($_SESSION['logged_from_id'])
	&& isset($_GET['action']) && $_GET['action'] == "go_back") {
	change_user_interface($_SESSION['user_id'], $_SESSION['logged_from_id']);
} else if (isset($_SESSION['user_id']) && isset($_GET['to_id'])) {

	$to_id = $_GET['to_id'];

	// admin logged as reseller:
	if (isset($_SESSION['logged_from']) && isset($_SESSION['logged_from_id'])) {
		$from_id = $_SESSION['logged_from_id'];
	} else { // reseller:

		$from_id = $_SESSION['user_id'];

		if (who_owns_this($to_id, 'client') != $from_id) {

			set_page_message(
				tr('User does not exist or you do not have permission to access this interface!'),
				'error'
			);

			user_goto('users.php?psi=last');
		}

	}

    // Remember some data
    if (isset($_SESSION['search_for'])) {
        $_SESSION['uistack'] = array('search_for' => $_SESSION['search_for']);

        if (isset($_SESSION['search_status'])) {
            $_SESSION['uistack']['search_status'] = $_SESSION['search_status'];
        }
        if (isset($_SESSION['search_common'])) {
            $_SESSION['uistack']['search_common'] = $_SESSION['search_common'];
        }
        if (isset($_SESSION['search_page'])) {
            $_SESSION['uistack']['search_page'] = $_SESSION['search_page'];
        }
    }

	change_user_interface($from_id, $to_id);

} else {
	user_goto('index.php');
}
