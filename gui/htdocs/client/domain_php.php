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

if (isset($_POST['uaction']) && $_POST['uaction'] === 'save_lang') {
	update_user_language();
}

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'client/language.tpl';

if (isset($_POST['uaction']) && $_POST['uaction'] === 'save_lang') {
	set_page_message(
		tr('User language updated successfully!'),
		'success'
	);
}

gen_def_language($cfg->USER_SELECTED_LANG);

// static page messages
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Client/Change Language'),
		'TR_LANGUAGE'				=> tr('Language'),
		'TR_CHOOSE_DEFAULT_LANGUAGE'=> tr('Choose default language'),
		'TR_SAVE'					=> tr('Save')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_general_information.tpl');
gen_client_menu($tpl, 'client/menu_general_information.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();
?>