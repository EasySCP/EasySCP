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
$template = 'client/error_edit.tpl';

// dynamic page data.

if (!isset($_GET['eid'])) {
	set_page_message(tr('Server error - please choose error page'), 'error');
	user_goto('error_pages.php');
} else {
	$eid = intval($_GET['eid']);
}

if ($eid == 401 || $eid == 403 || $eid == 404 || $eid == 500 || $eid == 503) {
	gen_error_page_data($tpl, $sql, $_GET['eid']);
} else {
	$tpl->assign(
		array(
			'ERROR' => tr('Server error - please choose error page'),
			'EID' => '0'
		)
	);
}

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Client/Manage Error Custom Pages'),
		'TR_ERROR_EDIT_PAGE'	=> tr('Edit error page'),
		'TR_SAVE'				=> tr('Save'),
		'TR_CANCEL'				=> tr('Cancel'),
		'EID'					=> $eid
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_webtools.tpl');
gen_client_menu($tpl, 'client/menu_webtools.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $user_id
 * @param string $eid
 */
function gen_error_page_data($tpl, $sql, $eid) {

	$domain = $_SESSION['user_logged'];

	// Check if we already have an error page
	$vfs = new EasySCP_VirtualFileSystem($domain, $sql);
	$error = $vfs->get('/errors/' . $eid . '.html');

	if (false !== $error) {
		// We already have an error page, return it
		$tpl->assign(array('ERROR' => tohtml($error)));
		return;
	}
	// No error page
	$tpl->assign(array('ERROR' => ''));
}
?>