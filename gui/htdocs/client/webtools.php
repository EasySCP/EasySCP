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

include '../../include/easyscp-lib.php';

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'client/webtools.tpl';

// static page messages
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Client/Webtools'),
		'TR_WEBTOOLS'			=> tr('Webtools'),
		'TR_BACKUP'				=> tr('Backup'),
		'TR_ERROR_PAGES'		=> tr('Error pages'),
		'TR_ERROR_PAGES_TEXT'	=> tr('Customize error pages for your domain'),
		'TR_BACKUP_TEXT'		=> tr('Backup and restore settings'),
		'TR_WEBMAIL_TEXT'		=> tr('Access your mail through the web interface'),
		'TR_FILEMANAGER_TEXT'	=> tr('Access your files through the web interface'),
		'TR_AWSTATS_TEXT'		=> tr('Access your Awstats statistics'),
		'TR_HTACCESS_TEXT'		=> tr('Manage protected areas, users and groups')
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
?>