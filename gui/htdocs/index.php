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

require_once '../include/easyscp-lib.php';

$cfg = EasySCP_Registry::get('Config');

if (isset($_GET['logout'])) {
	unset_user_login_data();
}

do_session_timeout();

init_login();

if (isset($_POST['uname']) && !empty($_POST['uname']) && isset($_POST['upass']) && !empty($_POST['upass'])) {

	check_input(trim($_POST['uname']));
	check_input(trim($_POST['upass']));

	$uname = encode_idna($_POST['uname']);

	if (register_user($uname, $_POST['upass'])) {
		redirect_to_level_page();
	}

	user_goto('index.php');
}

if (check_user_login() && !redirect_to_level_page()) {
	unset_user_login_data();
}

shall_user_wait();

$theme_color = isset($_SESSION['user_theme'])
	? $_SESSION['user_theme']
	: $cfg->USER_INITIAL_THEME;

$tpl = EasySCP_TemplateEngine::getInstance();

if (($cfg->MAINTENANCEMODE
		|| EasySCP_Update_Database::getInstance()->checkUpdateExists())
	&& !isset($_POST['admin']) ) {

	$template = 'maintenancemode.tpl';
	$tpl->assign(
		array(
			'TR_PAGE_TITLE'		=> tr('EasySCP a Virtual Hosting Control System'),
			'TR_MESSAGE'		=> nl2br(tohtml($cfg->MAINTENANCEMODE_MESSAGE)),
			'TR_ADMINLOGIN'		=> tr('Administrator login'),
			// @todo: make this configurable by easyscp-lib
			'TR_SSL_LINK'		=> isset($_SERVER['HTTPS']) ? 'http://' . htmlentities($_SERVER['HTTP_HOST']) : 'https://' . htmlentities($_SERVER['HTTP_HOST']),
			'TR_SSL_IMAGE'		=> isset($_SERVER['HTTPS']) ? 'lock.png' : 'unlock.png',
			'TR_SSL_DESCRIPTION'	=> !isset($_SERVER['HTTPS']) ? tr('Secure Connection') : tr('Normal Connection')
		)
	);
} else {

	$template = 'index.tpl';

	$tpl->assign(
		array(
			'TR_PAGE_TITLE'		=> tr('EasySCP a Virtual Hosting Control System'),
			'TR_LOGIN'			=> tr('Login'),
			'TR_USERNAME'		=> tr('Username'),
			'TR_PASSWORD'		=> tr('Password'),
			'TR_LOGIN_INFO'		=> tr('Please enter your login information'),
			// @todo: make this configurable by easyscp-lib
			'TR_SSL_LINK'		=> isset($_SERVER['HTTPS']) ? 'http://' . htmlentities($_SERVER['HTTP_HOST']) : 'https://' . htmlentities($_SERVER['HTTP_HOST']),
			'TR_SSL_IMAGE'		=> isset($_SERVER['HTTPS']) ? 'lock.png' : 'unlock.png',
			'TR_SSL_DESCRIPTION'	=> !isset($_SERVER['HTTPS']) ? tr('Secure Connection') : tr('Normal Connection')
		)
	);

}

if ($cfg->LOSTPASSWORD) {
	$tpl->assign('TR_LOSTPW', tr('Lost password'));
} else {
	$tpl->assign('TR_LOSTPW', '');
}

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);
?>