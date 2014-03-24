<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
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

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'admin/ticket_view.tpl';

// dynamic page data
if (!hasTicketSystem()) {
	user_goto('index.php');
}

if (isset($_GET['ticket_id'])) {
	$user_id = $_SESSION['user_id'];
	$ticket_id = $_GET['ticket_id'];
	$screenwidth = 1024;

	if (isset($_GET['screenwidth'])) {
		$screenwidth = $_GET['screenwidth'];
	} else if(isset($_POST['screenwidth'])) {
		$screenwidth = $_POST['screenwidth'];
	}

	if ($screenwidth < 639) {
		$screenwidth = 1024;
	}
	$tpl->assign('SCREENWIDTH', $screenwidth);

	// if status "new" or "Answer by client" set to "read"
	$status = getTicketStatus($ticket_id);
	if ($status == 1 || $status == 4) {
		changeTicketStatus($ticket_id, 3);
	}

	if (isset($_POST['uaction'])) {
		if ($_POST['uaction'] == "close") {
			// close ticket
			closeTicket($ticket_id);
		} elseif ($_POST['uaction'] == "open") {
			// open ticket
			openTicket($ticket_id);
		} elseif (empty($_POST['user_message'])) {
			// no message check->error
			set_page_message(tr('Please type your message!'), 'warning');
		} else {
			updateTicket($ticket_id, $user_id, $_POST['urgency'],
					$_POST['subject'], $_POST['user_message'], 2, 3);
			user_goto('ticket_system.php');
		}
	}

	showTicketContent($tpl, $ticket_id, $user_id, $screenwidth);
} else {
	set_page_message(tr('Ticket not found!'), 'error');
	user_goto('ticket_system.php');
}

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE' => tr('EasySCP - Client: Support System: View Ticket'),
		'TR_SUPPORT_SYSTEM' => tr('EasySCP - Admin: Support System: View Ticket'),
		'TR_VIEW_SUPPORT_TICKET' => tr('View support ticket'),
		'TR_TICKET_URGENCY' => tr('Priority'),
		'TR_TICKET_SUBJECT' => tr('Subject'),
		'TR_TICKET_DATE' => tr('Date'),
		'TR_DELETE' => tr('Delete'),
		'TR_NEW_TICKET_REPLY' => tr('Send message reply'),
		'TR_REPLY' => tr('Send reply'),
		'TR_TICKET_FROM' => tr('From'),
		'TR_OPEN_TICKETS' => tr('Open tickets'),
		'TR_CLOSED_TICKETS' => tr('Closed tickets')
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_ticket_system.tpl');
gen_admin_menu($tpl, 'admin/menu_ticket_system.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();
?>