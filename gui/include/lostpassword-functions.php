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

function check_gd() {
	return function_exists('imagecreatetruecolor');
}

/**
 * @todo use file_exists in try-catch block
 */
function captcha_fontfile_exists() {

	$cfg = EasySCP_Registry::get('Config');

	return file_exists($cfg->LOSTPASSWORD_CAPTCHA_FONT);
}

function createImage($strSessionVar) {

	$cfg = EasySCP_Registry::get('Config');

	$rgBgColor = $cfg->LOSTPASSWORD_CAPTCHA_BGCOLOR;
	$rgTextColor = $cfg->LOSTPASSWORD_CAPTCHA_TEXTCOLOR;

	$x = $cfg->LOSTPASSWORD_CAPTCHA_WIDTH;
	$y = $cfg->LOSTPASSWORD_CAPTCHA_HEIGHT;

	$font = $cfg->LOSTPASSWORD_CAPTCHA_FONT;

	$iRandVal = strrand(8, $strSessionVar);

	$im = imagecreate($x, $y) or die('Cannot initialize new GD image stream.');

	// Set background color
	imagecolorallocate($im, $rgBgColor[0],
		$rgBgColor[1],
		$rgBgColor[2]);

	$text_color = imagecolorallocate($im, $rgTextColor[0],
		$rgTextColor[1],
		$rgTextColor[2]);

	$white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);

	imagettftext($im, 34, 0, 5, 50,
		$text_color,
		$font,
		$iRandVal);
	// some obfuscation
	for ($i = 0; $i < 3; $i++) {
		$x1 = mt_rand(0, $x - 1);

		$y1 = mt_rand(0, round($y / 10, 0));

		$x2 = mt_rand(0, round($x / 10, 0));

		$y2 = mt_rand(0, $y - 1);

		imageline($im, $x1, $y1, $x2, $y2, $white);

		$x1 = mt_rand(0, $x - 1);

		$y1 = $y - mt_rand(1, round($y / 10, 0));

		$x2 = $x - mt_rand(1, round($x / 10, 0));

		$y2 = mt_rand(0, $y - 1);

		imageline($im, $x1, $y1, $x2, $y2, $white);
	}
	// send Header
	header("Content-type: image/png");
	// create and send PNG image
	imagepng($im);
	// destroy image from server
	imagedestroy($im);
}

function strrand($length, $strSessionVar) {
	$str = '';

	while (strlen($str) < $length) {
		$random = mt_rand(48, 122);

		if (preg_match('/[2-47-9A-HKMNPRTWUYa-hkmnp-rtwuy]/', chr($random))) {
			$str .= chr($random);
		}
	}

	$_SESSION[$strSessionVar] = $str;

	return $_SESSION[$strSessionVar];
}

function removeOldKeys($ttl) {
	$sql = EasySCP_Registry::get('Db');

	$boundary = date('Y-m-d H:i:s', time() - $ttl * 60);

	$query = "
		UPDATE
			`admin`
		SET
			`uniqkey` = NULL,
			`uniqkey_time` = NULL
		WHERE
			`uniqkey_time` < ?
	";

	exec_query($sql, $query, $boundary);
}

function setUniqKey($admin_name, $uniqkey) {
	$sql = EasySCP_Registry::get('Db');

	$timestamp = date('Y-m-d H:i:s', time());

	$query = "
		UPDATE
			`admin`
		SET
			`uniqkey` = ?,
			`uniqkey_time` = ?
		WHERE
			`admin_name` = ?
	";

	exec_query($sql, $query, array($uniqkey, $timestamp, $admin_name));
}

function setPassword($uniqkey, $upass) {
	$sql = EasySCP_Registry::get('Db');

	if ($uniqkey == '') {
		die();
	}

	$query = "
		UPDATE
			`admin`
		SET
			`admin_pass` = ?
		WHERE
			`uniqkey` = ?
	";

	exec_query($sql, $query, array(crypt_user_pass($upass), $uniqkey));
}

function uniqkeyexists($uniqkey) {
	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			`uniqkey`
		FROM
			`admin`
		WHERE
			`uniqkey` = ?
	";

	$res = exec_query($sql, $query, $uniqkey);

	return ($res->recordCount() != 0) ? true : false;
}

/**
 * @todo use more secure hash algorithm (see PHP mcrypt extension)
 */
function uniqkeygen() {
	$uniqkey = '';

	while ((uniqkeyexists($uniqkey)) || (!$uniqkey)) {
		$uniqkey = md5(uniqid(mt_rand()));
	}

	return $uniqkey;
}

function sendpassword($uniqkey) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			`admin_name`, `created_by`, `fname`, `lname`, `email`
		FROM
			`admin`
		WHERE
			`uniqkey` = ?
	";

	$res = exec_query($sql, $query, $uniqkey);

	if ($res->recordCount() == 1) {
		$admin_name = $res->fields['admin_name'];

		$created_by = $res->fields['created_by'];

		$admin_fname = $res->fields['fname'];

		$admin_lname = $res->fields['lname'];

		$to = $res->fields['email'];

		$upass = passgen();

		setPassword($uniqkey, $upass);

		write_log('Lostpassword: ' . $admin_name . ': password updated');

		$query = "
			UPDATE
				`admin`
			SET
				`uniqkey` = ?,
				`uniqkey_time` = ?
			WHERE
				`uniqkey` = ?
		";

		exec_query($sql, $query, array('', '', $uniqkey));

		if ($created_by == 0) { $created_by = 1; }

		$data = get_lostpassword_password_email($created_by);

		$from_name = $data['sender_name'];

		$from_email = $data['sender_email'];

		$subject = $data['subject'];

		$message = $data['message'];

		$base_vhost = $cfg->BASE_SERVER_VHOST;

		$base_vhost_prefix = $cfg->BASE_SERVER_VHOST_PREFIX;

		if ($from_name) {
			$from = '"' . $from_name . '" <' . $from_email . '>';
		} else {
			$from = $from_email;
		}

		$search = array();
		$replace = array();

		$search [] = '{USERNAME}';
		$replace[] = $admin_name;
		$search [] = '{NAME}';
		$replace[] = $admin_fname . " " . $admin_lname;
		$search [] = '{PASSWORD}';
		$replace[] = $upass;
		$search [] = '{BASE_SERVER_VHOST}';
		$replace[] = $base_vhost;
		$search [] = '{BASE_SERVER_VHOST_PREFIX}';
		$replace[] = $base_vhost_prefix;

		$subject = str_replace($search, $replace, $subject);
		$message = str_replace($search, $replace, $message);

		$headers = 'From: ' . $from . "\n";

		$headers .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 7bit\n";

		$headers .= 'X-Mailer: EasySCP lostpassword mailer';

		$mail_result = mail($to, $subject, $message, $headers);

		$mail_status = ($mail_result) ? 'OK' : 'NOT OK';

        $from = tohtml($from);

		write_log("Lostpassword activated: To: |$to|, From: |$from|, Status: |$mail_status| !", E_USER_NOTICE);

		return true;
	}

	return false;
}

function requestpassword($admin_name) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$query = "
		SELECT
			`created_by`, `fname`, `lname`, `email`
		FROM
			`admin`
		WHERE
			`admin_name` = ?
	";

	$res = exec_query($sql, $query, $admin_name);

	if ($res->recordCount() == 0) {
		return false;
	}

	$created_by = $res->fields['created_by'];
	$admin_fname = $res->fields['fname'];
	$admin_lname = $res->fields['lname'];
	$to = $res->fields['email'];

	$uniqkey = uniqkeygen();

	setUniqKey($admin_name, $uniqkey);

	write_log("Lostpassword: " . $admin_name . ": uniqkey created", E_USER_NOTICE);

	if ($created_by == 0) {
		$created_by = 1;
	}

	$data = get_lostpassword_activation_email($created_by);

	$from_name = $data['sender_name'];
	$from_email = $data['sender_email'];
	$subject = $data['subject'];
	$message = $data['message'];

	$base_vhost = $cfg->BASE_SERVER_VHOST;
	$base_vhost_prefix = $cfg->BASE_SERVER_VHOST_PREFIX;

	if ($from_name) {
		$from = '"' . $from_name . "\" <" . $from_email . ">";
	} else {
		$from = $from_email;
	}

	$prot = isset($_SERVER['https']) ? 'https' : 'http';
	$link = $prot . '://' . $_SERVER["HTTP_HOST"] . $_SERVER["PHP_SELF"] . '?key=' . $uniqkey;

	$search = array();
	$replace = array();

	$search [] = '{USERNAME}';
	$replace[] = $admin_name;
	$search [] = '{NAME}';
	$replace[] = $admin_fname . " " . $admin_lname;
	$search [] = '{LINK}';
	$replace[] = $link;
	$search [] = '{BASE_SERVER_VHOST}';
	$replace[] = $base_vhost;
	$search [] = '{BASE_SERVER_VHOST_PREFIX}';
	$replace[] = $base_vhost_prefix;

	$subject = str_replace($search, $replace, $subject);
	$message = str_replace($search, $replace, $message);

	$headers = 'From: ' . $from . "\n";
	$headers .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 8bit\n";
	$headers .= 'X-Mailer: EasySCP lostpassword mailer';

	$mail_result = mail($to, mb_encode_mimeheader($subject, 'UTF-8'), $message, $headers);

	$mail_status = ($mail_result) ? 'OK' : 'NOT OK';

    $from = tohtml($from);

	write_log("Lostpassword send: To: |$to|, From: |$from|, Status: |$mail_status| !", E_USER_NOTICE);

	return true;
}
?>