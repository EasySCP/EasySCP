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
$template = 'client/error_pages.tpl';

// common page data.

$domain = $_SESSION['user_logged'];
$domain = "http://www." . $domain;

// dynamic page data.

update_error_page($sql);

// static page messages.
gen_logged_from($tpl);
check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP - Client/Manage Error Custom Pages'),
		'DOMAIN'			=> $domain,
		'TR_ERROR_401'		=> tr('Error 401 (unauthorised)'),
		'TR_ERROR_403'		=> tr('Error 403 (forbidden)'),
		'TR_ERROR_404'		=> tr('Error 404 (not found)'),
		'TR_ERROR_500'		=> tr('Error 500 (internal server error)'),
		'TR_ERROR_503'		=> tr('Error 503 (service unavailable)'),
		'TR_ERROR_PAGES'	=> tr('Error pages'),
		'TR_EDIT'			=> tr('Edit'),
		'TR_VIEW'			=> tr('View')
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

// page functions.

function write_error_page($sql, $eid) {

	$error = $_POST['error'];
	$file = '/errors/' . $eid . '.html';
	$vfs = new EasySCP_VirtualFileSystem($_SESSION['user_logged'], $sql);

	return $vfs->put($file, $error);
}

function update_error_page($sql) {

	if (isset($_POST['uaction']) && $_POST['uaction'] === 'updt_error') {
		$eid = intval($_POST['eid']);

		if (in_array($eid, array(401, 402, 403, 404, 500, 503))
			&& write_error_page($sql, $eid)) {
			set_page_message(tr('Custom error page was updated!'), 'success');
		} else {
			set_page_message(
				tr('System error - custom error page was NOT updated!'),
				'error'
			);
		}
	}
}
?>