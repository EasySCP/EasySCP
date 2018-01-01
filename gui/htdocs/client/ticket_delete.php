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

$reseller_id = $_SESSION['user_created_by'];

if (!hasTicketSystem($reseller_id)) {
	user_goto('index.php');
}

$back_url = 'ticket_system.php';
$user_id = $_SESSION['user_id'];

if (isset($_GET['ticket_id']) && $_GET['ticket_id'] != '') {

	$ticket_id = $_GET['ticket_id'];
	$user_id = $_SESSION['user_id'];

	$query = "
		SELECT
			`ticket_status`
		FROM
			`tickets`
		WHERE
			`ticket_id` = ?
		AND
			(`ticket_from` = ? OR `ticket_to` = ?)
	;";

	$rs = exec_query($sql, $query, array($ticket_id, $user_id, $user_id));

	if ($rs->recordCount() == 0) {
		user_goto('ticket_system.php');
	}

	$back_url = (getTicketStatus($ticket_id) == 0) ?
		'ticket_closed.php' : 'ticket_system.php';

	deleteTicket($ticket_id);

	write_log(sprintf("%s: deletes support ticket %d", $_SESSION['user_logged'],
			$ticket_id));
	set_page_message(tr('Support ticket deleted successfully!'), 'info');
} elseif (isset($_GET['delete']) && $_GET['delete'] == 'open') {

	deleteTickets('open', $user_id);

	write_log(sprintf("%s: deletes all open support tickets.", $_SESSION['user_logged']));
	set_page_message(
		tr('All open support tickets deleted successfully!'),
		'info'
	);
} elseif (isset($_GET['delete']) && $_GET['delete'] == 'closed') {

	deleteTickets('closed', $user_id);

	write_log(sprintf("%s: deletes all closed support ticket.", $_SESSION['user_logged']));
	set_page_message(
		tr('All closed support tickets deleted successfully!'),
		'info'
	);
	$back_url = 'ticket_closed.php';
}

user_goto($back_url);
?>