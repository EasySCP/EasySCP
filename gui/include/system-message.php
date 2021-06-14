<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2010 by ispCP | http://isp-control.net
 * @copyright 	2010-2020 by Easy Server Control Panel - http://www.easyscp.net
 * @version 	SVN: $Id$
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 *
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Original Code is "VHCS - Virtual Hosting Control System".
 *
 * The Initial Developer of the Original Code is moleSoftware GmbH.
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 *
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 *
 * Portions created by the EasySCP Team are Copyright (C) 2010-2020 by
 * Easy Server Control Panel. All Rights Reserved.
 */

/**
 * Generates a page message if something terribly goes wrong.
 *
 * @param String $msg Message Content
 * @param String $type Message Type (notice, warning, error, success)
 * @param string $backButtonDestination Destination where to go on back link click
 *
 * @throws EasySCP_Exception
 */
function system_message($msg, $type = 'error', $backButtonDestination = '') {

	$cfg = EasySCP_Registry::get('Config');

	$theme_color = (isset($_SESSION['user_theme']))
		? $_SESSION['user_theme'] : $cfg->USER_INITIAL_THEME;

	if (empty($backButtonDestination)) {
		$backButtonDestination = "javascript:history.go(-1)";
	}

	$tpl = EasySCP_TemplateEngine::getInstance();

	// If we are on the login page, path will be like this
	$template = 'system-message.tpl';

	if (!is_file($tpl->get_template_dir().'/'.$template)) {
		// But if we're inside the panel it will be like this
		$template = '../system-message.tpl';
	}
	if (!is_file($tpl->get_template_dir().'/'.$template)) {
		// And if we don't find the template, we'll just displaying error
		// message
		throw new EasySCP_Exception($msg);
	}

	// Small workaround to be able to use the system_message() function during
	// EasySCP initialization process without i18n support
	if (function_exists('tr')) {
		$tpl->assign(
			array(
				'TR_PAGE_TITLE' => tr('EasySCP Error'),
				'TR_BACK' => tr('Back'),
				'TR_ERROR_MESSAGE' => tr('Error Message'),
				'MESSAGE' => $msg,
				'MSG_TYPE' => $type,
				'BACKBUTTONDESTINATION' => $backButtonDestination,
				'TR_LOGIN' => tr('Login'),
				'TR_USERNAME' => tr('Username'),
				'TR_PASSWORD' => tr('Password'),
				'TR_LOSTPW' => tr('Lost password')
			)
		);
	} else {
		$tpl->assign(
			array(
				'TR_PAGE_TITLE' => 'EasySCP Error',
				'TR_BACK' => 'Back',
				'TR_ERROR_MESSAGE' => 'Error Message',
				'MESSAGE' => $msg,
				'MSG_TYPE' => $type,
				'BACKBUTTONDESTINATION' => $backButtonDestination,
				'TR_LOGIN' => 'Login',
				'TR_USERNAME' => 'Username',
				'TR_PASSWORD' => 'Password',
				'TR_LOSTPW' => 'Lost password'
			)
		);
	}

	$tpl->display($template);

	exit;
}
?>