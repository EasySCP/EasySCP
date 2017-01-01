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

require '../include/easyscp-lib.php';

$cfg = EasySCP_Registry::get('Config');

if (!$cfg->LOSTPASSWORD) {
	throw new EasySCP_Exception_Production(
		tr('Retrieving lost passwords is currently not possible')
	);
}

// check if GD library is available
if (!check_gd()) {
	throw new EasySCP_Exception(tr("GD PHP Extension not loaded!"));
}

// check if captch fonts exist
if (!captcha_fontfile_exists()) {
	throw new EasySCP_Exception(tr("Captcha fontfile not found!"));
}

// remove old unique keys
removeOldKeys($cfg->LOSTPASSWORD_TIMEOUT);

if (isset($_SESSION['user_theme'])) {
	$theme_color = $_SESSION['user_theme'];
} else {
	$theme_color = $cfg->USER_INITIAL_THEME;
}

$tpl = EasySCP_TemplateEngine::getInstance();
$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Virtual Hosting Control System'),
		'TR_WEBMAIL_SSL_LINK'	=> 'webmail',
		'TR_FTP_SSL_LINK'		=> 'ftp',
		'TR_PMA_SSL_LINK'		=> 'pma'
	)
);

// Key request has been triggered
if (isset($_GET['key']) && !empty($_GET['key'])) {
	check_input($_GET['key']);

	$template = 'lostpassword_message.tpl';

	if (sendpassword($_GET['key'])) {
		$tpl->assign(
			array(
				'TR_MESSAGE'	=> tr('Your new password has been sent.'),
				'TR_LINK'		=> '<a href="index.php" class="button">' . tr('Login') . '</a>'
			)
		);
	} else {
		$tpl->assign(
			array(
				'TR_MESSAGE'	=> tr('New password could not be sent.'),
				'TR_LINK'		=> '<a href="index.php" class="button">' . tr('Login') . '</a>'
			)
		);
	}

} elseif (isset($_POST['uname'])) {
	check_ipaddr(getipaddr(), 'captcha');

	$template = 'lostpassword_message.tpl';

	if ((!empty($_POST['uname'])) && isset($_SESSION['image']) &&
			isset($_POST['capcode'])) {
		check_input(trim($_POST['uname']));
		check_input($_POST['capcode']);

		if ($_SESSION['image'] == $_POST['capcode'] && requestpassword($_POST['uname'])) {
			$tpl->assign(
				array(
					'TR_MESSAGE'	=> tr('Your password request has been initiated. You will receive an email with instructions to complete the process. This reset request will expire in %s minutes.', $cfg->LOSTPASSWORD_TIMEOUT),
					'TR_LINK'		=> '<a href="index.php" class="button">' . tr('Back') . '</a>'
				)
			);
		} else {
			$tpl->assign(
				array(
					'TR_MESSAGE'	=> tr('User or security code was incorrect!'),
					'TR_LINK'		=> '<a href="lostpassword.php" class="button">' . tr('Retry') . '</a>'
				)
			);
		}
	} else {
		$tpl->assign(
			array(
				'TR_MESSAGE'	=> tr('Please fill out all required fields!'),
				'TR_LINK'		=> '<a href="lostpassword.php" class="button">' . tr('Retry') . '</a>'
			)
		);
	}
} else {

	unblock($cfg->BRUTEFORCE_BLOCK_TIME, 'captcha');
	is_ipaddr_blocked(null, 'captcha', true);

	$template = 'lostpassword.tpl';
	$tpl->assign(
		array(
			'TR_CAPCODE'				=> tr('Security code'),
			'TR_IMGCAPCODE_DESCRIPTION'	=> tr('(To avoid abuse, we ask you to write the combination of letters on the above picture into the field "Security code")'),
			'TR_IMGCAPCODE'				=> '<img src="imagecode.php" style="border: none;height: '. $cfg->LOSTPASSWORD_CAPTCHA_HEIGHT .'px;width: '. $cfg->LOSTPASSWORD_CAPTCHA_WIDTH .'px;" alt="captcha image" />',
			'TR_USERNAME'				=> tr('Username'),
			'TR_SEND'					=> tr('Request password'),
			'TR_BACK'					=> tr('Back')
		)
	);

}

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);
?>