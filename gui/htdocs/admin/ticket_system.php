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

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'common/ticket_system.tpl';

// dynamic page data
if (!hasTicketSystem()) {
	user_goto('index.php');
}
if (isset($_GET['psi'])) {
	$start = $_GET['psi'];
} else {
	$start = 0;
}

generateTicketList($tpl, $_SESSION['user_id'], $start,$cfg->DOMAIN_ROWS_PER_PAGE, 'admin', 'open');

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Client/Enable Mail Autoresponder'),
		'TR_SUPPORT_SYSTEM'		=> tr('Support system'),
		'TR_SUPPORT_TICKETS'	=> tr('Support tickets'),
		'TR_TICKET_FROM'		=> tr('From'),
		'TR_STATUS'				=> tr('Status'),
		'TR_NEW'				=> ' ',
		'TR_ACTION'				=> tr('Action'),
		'TR_URGENCY'			=> tr('Priority'),
		'TR_SUBJECT'			=> tr('Subject'),
		'TR_LAST_DATA'			=> tr('Last reply'),
		'TR_DELETE_ALL'			=> tr('Delete all'),
		'TR_OPEN_TICKETS'		=> tr('Open tickets'),
		'TR_CLOSED_TICKETS'		=> tr('Closed tickets'),
		'TR_DELETE'				=> tr('Delete'),
		'TR_MESSAGE_DELETE'		=> tr('Are you sure you want to delete %s?', true, '%s'),
		'TR_EDIT'				=> tr('Edit')
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