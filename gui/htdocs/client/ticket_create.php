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

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'common/ticket_create.tpl';

// dynamic page data

$reseller_id = $_SESSION['user_created_by'];

if (!hasTicketSystem($reseller_id)) {
	user_goto('index.php');
}

if (isset($_POST['uaction'])) {
	if (empty($_POST['subj'])) {
		set_page_message(tr('Please specify message subject!'), 'warning');
	} else if (empty($_POST['user_message'])) {
		set_page_message(tr('Please type your message!'), 'warning');
	} else {
		createTicket($_SESSION['user_id'], $_SESSION['user_created_by'], $_POST['urgency'], $_POST['subj'], $_POST['user_message'], 1);
		user_goto('ticket_system.php');
	}
}

$userdata = array(
	'OPT_URGENCY_1' => '',
	'OPT_URGENCY_2' => '',
	'OPT_URGENCY_3' => '',
	'OPT_URGENCY_4' => ''
);

if (isset($_POST['urgency'])) {
	$userdata['URGENCY'] = intval($_POST['urgency']);
} else {
	$userdata['URGENCY'] = 2;
}

switch ($userdata['URGENCY']) {
	case 1:
		$userdata['OPT_URGENCY_1'] = $cfg->HTML_SELECTED;
		break;
	case 3:
		$userdata['OPT_URGENCY_3'] = $cfg->HTML_SELECTED;
		break;
	case 4:
		$userdata['OPT_URGENCY_4'] = $cfg->HTML_SELECTED;
		break;
	default:
		$userdata['OPT_URGENCY_2'] = $cfg->HTML_SELECTED;
}

$userdata['SUBJECT'] = isset($_POST['subj']) ? clean_input($_POST['subj'], true) : '';
$userdata['USER_MESSAGE'] = isset($_POST['user_message']) ? clean_input($_POST['user_message'], true) : '';
$tpl->assign($userdata);

// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP - Support system - New ticket'),
		'TR_NEW_TICKET'		=> tr('New ticket'),
		'TR_LOW'			=> tr('Low'),
		'TR_MEDIUM'			=> tr('Medium'),
		'TR_HIGH'			=> tr('High'),
		'TR_VERI_HIGH'		=> tr('Very high'),
		'TR_URGENCY'		=> tr('Priority'),
		'TR_EMAIL'			=> tr('Email'),
		'TR_SUBJECT'		=> tr('Subject'),
		'TR_YOUR_MESSAGE'	=> tr('Your message'),
		'TR_SEND_MESSAGE'	=> tr('Send message')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_ticket_system.tpl');
gen_client_menu($tpl, 'client/menu_ticket_system.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();
?>