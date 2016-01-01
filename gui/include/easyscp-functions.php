<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2010 by ispCP | http://isp-control.net
 * @copyright 	2010-2016 by Easy Server Control Panel - http://www.easyscp.net
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
 * Portions created by the EasySCP Team are Copyright (C) 2010-2012 by
 * Easy Server Control Panel. All Rights Reserved.
 */

/**
 * Reads line from the socket resource
 *
 * @param resource &$socket
 * @return string A line read from the socket resource
 */
function read_line(&$socket) {

	$line = '';

	do {
		$ch = socket_read($socket, 1);
		$line = $line . $ch;
	} while ($ch != "\r" && $ch != "\n");

	return $line;
}


/**
 * Send request to the daemon
 *
 * @param string $execute
 * @return string Daemon answer
 * @todo Remove error operator
 */
function send_request($execute) {

	// @$socket = socket_create (AF_INET, SOCK_STREAM, 0);
	@$socket = socket_create (AF_UNIX, SOCK_STREAM, 0);
	if ($socket < 0) {
		$errno = "socket_create() failed.\n";
		return $errno;
	}

	// @$result = socket_connect ($socket, '127.0.0.1', 9876);
	@$result = socket_connect($socket, EasyConfig::$cfg->SOCK_EASYSCPD);
	if ($result == false) {
		$errno = "socket_connect() failed.\n";
		return $errno;
	}

	// read one line with welcome string
	socket_read($socket, 1024, PHP_NORMAL_READ);
	// $out = read_line($socket);

	// send reg check query
	// $query = $execute . "\r\n";
	$query = trim($execute) . "\n";
	socket_write($socket, $query, strlen ($query));

	// read answer from the daemon
	$out = socket_read($socket, 10240, PHP_NORMAL_READ);

	socket_shutdown($socket, 2);
	socket_close($socket);

	// sleep(1);
	// todo: prüfen ob das noch benötigt wird. Wenn keine Fehler mehr auftreten kann es entfernt werden
	// usleep(250);

	return trim($out);
}

/**
 * Updates domain expiration date
 *
 * @param int $user_id Customer id
 * @param int $domain_new_expire New expiration date
 * @return void
 */
function update_expire_date($user_id, $domain_new_expire) {

	$db = EasySCP_Registry::get('Db');

	$query = "
		UPDATE
			`domain`
		SET
			`domain_expires` = ?
		WHERE
			`domain_id` = ?
		;
	";

	exec_query(
		$db, $query,
		array($domain_new_expire, $user_id)
	);
}

/**
 * Updates customer properties
 *
 * @param int $user_id Customer id
 * @param string $props New properties values
 * @return void
 */
function update_user_props($user_id, $props) {

	/**
	 * @var EasySCP_Config_Handler_File $cfg
	 */
	$cfg = EasySCP_Registry::get('Config');

	/**
	 * @var EasySCP_Database $db
	 */
	$db = EasySCP_Registry::get('Db');

	list(
		,$sub_max,,$als_max,,$mail_max,,$ftp_max,,$sql_db_max,,$sql_user_max,
		$traff_max,$disk_max,$domain_php,$domain_php_edit,$domain_cgi,$domain_ssl,,$domain_dns,$countbackup
	) = explode (';', $props);

	// have to check if PHP and/or CGI and/or IP change
	$domain_last_modified = time();

	$query = "
		SELECT
			`domain_name`
		FROM
			`domain`
		WHERE
			`domain_id` = ?
		AND
			`domain_php` = ?
		AND
			`domain_php_edit` = ?
		AND
			`domain_cgi` = ?
		AND
			`domain_ssl` = ?
		AND
			`domain_dns` = ?
		;
	";

	$rs = exec_query(
		$db, $query, array($user_id, $domain_php, $domain_php_edit, $domain_cgi, $domain_ssl, $domain_dns)
	);

	if ($rs->recordCount() == 0) {
		// mama mia, we have to rebuild the system entry for this domain
		// and also all domain alias and subdomains

		$update_status = $cfg->ITEM_CHANGE_STATUS;

		// update the domain
		$sql_param = array(
			':domain_last_modified'		=> $domain_last_modified,
			':domain_mailacc_limit'		=> $mail_max,
			':domain_ftpacc_limit'		=> $ftp_max,
			':domain_traffic_limit'		=> $traff_max,
			':domain_sqld_limit'		=> $sql_db_max,
			':domain_sqlu_limit'		=> $sql_user_max,
			':domain_status'			=> $update_status,
			':domain_alias_limit'		=> $als_max,
			':domain_subd_limit'		=> $sub_max,
			':domain_disk_limit'		=> $disk_max,
			':domain_php'				=> $domain_php,
			':domain_php_edit'			=> $domain_php_edit,
			':domain_cgi'				=> $domain_cgi,
			':domain_ssl'				=> $domain_ssl,
			':domain_dns'				=> $domain_dns,
			':domain_id'				=> $user_id,
			':domain_disk_countbackup'	=> $countbackup
		);

		$sql_query = "
			UPDATE
				domain
			SET
				domain_last_modified = :domain_last_modified,
				domain_mailacc_limit = :domain_mailacc_limit,
				domain_ftpacc_limit = :domain_ftpacc_limit,
				domain_traffic_limit = :domain_traffic_limit,
				domain_sqld_limit = :domain_sqld_limit,
				domain_sqlu_limit = :domain_sqlu_limit,
				status = :domain_status,
				domain_alias_limit = :domain_alias_limit,
				domain_subd_limit = :domain_subd_limit,
				domain_disk_limit = :domain_disk_limit,
				domain_php = :domain_php,
				domain_php_edit = :domain_php_edit,
				domain_cgi = :domain_cgi,
				domain_ssl = :domain_ssl,
				domain_dns = :domain_dns,
				domain_disk_countbackup = :domain_disk_countbackup
			WHERE
				domain_id = :domain_id;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param);

		// let's update all alias domains for this domain

		$query = "
			UPDATE
				domain_aliasses
			SET
				status = ?
			WHERE
				domain_id = ?
			;
		";

		exec_query($db, $query, array($update_status, $user_id));

		// let's update all subdomains for this domain
		$query = "
			UPDATE
				`subdomain`
			SET
				`status` = ?
			WHERE
				`domain_id` = ?
			;
		";

		exec_query($db, $query, array($update_status, $user_id));

		// let's update all alias subdomains for this domain
		$query = "
			UPDATE
				`subdomain_alias`
			SET
				`status` = ?
			WHERE
				`alias_id` IN (
					SELECT
						`alias_id`
					FROM
						`domain_aliasses`
					WHERE
						`domain_id` = ?
				)
			;
		";

		exec_query($db, $query, array($update_status, $user_id));

		// Send request to the EasySCP daemon
		send_request('100 CORE checkAll');

	} else {

		// we do not have IP and/or PHP and/or CGI changes
		// we have to update only the domain props and not
		// to rebuild system entries
		// update the domain
		$sql_param = array(
			':domain_mailacc_limit'		=> $mail_max,
			':domain_ftpacc_limit'		=> $ftp_max,
			':domain_traffic_limit'		=> $traff_max,
			':domain_sqld_limit'		=> $sql_db_max,
			':domain_sqlu_limit'		=> $sql_user_max,
			':domain_alias_limit'		=> $als_max,
			':domain_subd_limit'		=> $sub_max,
			':domain_disk_limit'		=> $disk_max,
			':domain_disk_countbackup'	=> $countbackup,
			':domain_id'				=> $user_id
		);

		$sql_query = "
			UPDATE
				domain
			SET
				domain_mailacc_limit = :domain_mailacc_limit,
				domain_ftpacc_limit = :domain_ftpacc_limit,
				domain_traffic_limit = :domain_traffic_limit,
				domain_sqld_limit = :domain_sqld_limit,
				domain_sqlu_limit = :domain_sqlu_limit,
				domain_alias_limit = :domain_alias_limit,
				domain_subd_limit = :domain_subd_limit,
				domain_disk_limit = :domain_disk_limit,
				domain_disk_countbackup = :domain_disk_countbackup
			WHERE
				domain_id = :domain_id;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param);

	}
}

/**
 * Should be documented
 *
 * @param  $arr
 * @param bool $asPath
 * @return string
 */
function array_decode_idna($arr, $asPath = false) {

	if ($asPath && !is_array($arr)) {
		return implode('/', array_decode_idna(explode('/', $arr)));
	}

	foreach ($arr as $k => $v) {
		$arr[$k] = decode_idna($v);
	}

	return $arr;
}

/**
 * Should be documented
 *
 * @param $arr
 * @param bool $asPath
 * @return string
 */
function array_encode_idna($arr, $asPath = false) {

	if ($asPath && !is_array($arr)) {
		return implode('/', array_encode_idna(explode('/', $arr)));
	}

	foreach ($arr as $k => $v) {
		if (strpos($v, 'xn--') === 0) {
			$arr[$k] = $v;
		} else {
			$arr[$k] = encode_idna($v);
		}
	}
	return $arr;
}

/**
 * Decodes a String from IDNA format to UTF8
 *
 * @param  $input
 * @return string
 */
function decode_idna($input) {

	if (function_exists('idn_to_utf8')) {
		// return idn_to_utf8($input, IDNA_USE_STD3_RULES);
		return idn_to_utf8($input);
	} else {

		$IDNA = new Net_IDNA2();
		$output = $IDNA->decode($input);

		return ($output == false) ? $input : $output;
	}
}

/**
 * Encodes a String from IDNA format ASCII
 *
 * @param  $input
 * @return string
 */
function encode_idna($input) {

	if (function_exists('idn_to_ascii')) {
		return idn_to_ascii($input);
	} else {

		$IDNA = new Net_IDNA2();
		$output = $IDNA->encode($input);

		return $output;
	}
}

/**
 * Check wether a given String is a number or not.
 *
 * @param $integer Number to be checked
 * @return boolean TRUE if number, FALSE otherwise
 */
function is_number($number) {
	return (bool) preg_match('/^[0-9]+$/D', $number);
}

/**
 * Should be documented
 *
 * @param  $string string to be checked
 * @return bool TRUE if basic string, FALSE otherwise
 */
function is_basicString($string) {
	if (preg_match('/^[\w\-]+$/D', $string)) {
		return true;
	}
	return false;
}

/**
 * Should be documented
 *
 * @return void
 */
function unset_messages() {

	$glToUnset = array();
	$glToUnset[] = 'user_page_message';
	$glToUnset[] = 'user_updated';
	$glToUnset[] = 'user_updated';
	$glToUnset[] = 'dmn_tpl';
	$glToUnset[] = 'chtpl';
	$glToUnset[] = 'step_one';
	$glToUnset[] = 'step_two_data';
	$glToUnset[] = 'ch_hpprops';
	$glToUnset[] = 'user_add3_added';
	$glToUnset[] = 'user_has_domain';
	$glToUnset[] = 'local_data';
	$glToUnset[] = 'reseller_added';
	$glToUnset[] = 'user_added';
	$glToUnset[] = 'aladd';
	$glToUnset[] = 'edit_ID';
	$glToUnset[] = 'hp_added';
	$glToUnset[] = 'aldel';
	$glToUnset[] = 'hpid';
	$glToUnset[] = 'user_deleted';
	$glToUnset[] = 'hdomain';
	$glToUnset[] = 'aledit';
	$glToUnset[] = 'acreated_by';
	$glToUnset[] = 'dhavesub';
	$glToUnset[] = 'ddel';
	$glToUnset[] = 'dhavealias';
	$glToUnset[] = 'dhavealias';
	$glToUnset[] = 'dadel';
	$glToUnset[] = 'local_data';

	foreach ($glToUnset as $toUnset) {
		if (array_key_exists($toUnset, $GLOBALS)) {
			unset($GLOBALS[$toUnset]);
		}
	}

	$sessToUnset = array();
	$sessToUnset[] = 'reseller_added';
	$sessToUnset[] = 'dmn_name';
	$sessToUnset[] = 'dmn_tpl';
	$sessToUnset[] = 'chtpl';
	$sessToUnset[] = 'step_one';
	$sessToUnset[] = 'step_two_data';
	$sessToUnset[] = 'ch_hpprops';
	$sessToUnset[] = 'user_add3_added';
	$sessToUnset[] = 'user_has_domain';
	$sessToUnset[] = 'user_added';
	$sessToUnset[] = 'aladd';
	$sessToUnset[] = 'edit_ID';
	$sessToUnset[] = 'hp_added';
	$sessToUnset[] = 'aldel';
	$sessToUnset[] = 'hpid';
	$sessToUnset[] = 'user_deleted';
	$sessToUnset[] = 'hdomain';
	$sessToUnset[] = 'aledit';
	$sessToUnset[] = 'acreated_by';
	$sessToUnset[] = 'dhavesub';
	$sessToUnset[] = 'ddel';
	$sessToUnset[] = 'dhavealias';
	$sessToUnset[] = 'dadel';
	$sessToUnset[] = 'local_data';

	foreach ($sessToUnset as $toUnset) {
		if (array_key_exists($toUnset, $_SESSION)) {
			unset($_SESSION[$toUnset]);
		}
	}
}

/**
 * Checks for XMLHttpRequest
 *
 * Returns true if the request‘s "X-Requested-With" header
 * contains "XMLHttpRequest".
 *
 * Note: jQuery and Prototype Javascript libraries sends this
 * header with every Ajax request.
 *
 * @author Laurent Declercq (nuxwin) <laurent.declercq@ispcp.net>
 * @Since r2587
 * @return boolean TRUE if the request‘s "X-Requested-With" header
 *  contains "XMLHttpRequest", FALSE otherwise
 * @todo Move to future Request class
 */
function is_xhr() {

	if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
		stristr($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest') !== false) {
		return true;
	}

	return false;
}

/**
 * Check if a data is serialized
 *
 * @since 1.0.7
 * @author Laurent Declercq (nuxwin) <laurent.declercq@ispcp.net>
 * @param mixed $data Data to be checked
 * @return boolean TRUE if serialized data, FALSE otherwise
 */
function is_serialized($data) {

	if (!is_string($data)) {
		return false;
	}

	$data = trim($data);

	if ('N;' == $data) {
		return true;
	}

	if(preg_match("/^[aOs]:[0-9]+:.*[;}]\$/s", $data) ||
		preg_match("/^[bid]:[0-9.E-]+;\$/", $data)) {

		return true;
	}

	return false;
}

/**
 * Decrypt database password
 *
 * @throws EasySCP_Exception
 * @param string $db_pass Encrypted database password
 * @return string Decrypted database password
 * @todo Remove error operator
 */
function decrypt_db_password($db_pass) {

	if ($db_pass == '') {
		return '';
	}

	if (extension_loaded('mcrypt')) {

		$text = @base64_decode($db_pass . "\n");
		$td = @mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
		$key = EasySCP_Registry::get('MCRYPT_KEY');
		$iv = EasySCP_Registry::get('MCRYPT_IV');

		// Initialize encryption
		@mcrypt_generic_init($td, $key, $iv);
		// Decrypt encrypted string
		$decrypted = @mdecrypt_generic($td, $text);
		@mcrypt_module_close($td);

		// Show string
		return trim($decrypted);
	} else {
		throw new EasySCP_Exception(
			"Error: PHP extension 'mcrypt' not loaded!"
		);
	}
}

/**
 * Encrypt database password
 *
 * @throws EasySCP_Exception
 * @param string $db_pass Database password
 * @return string Encrypted database password
 * @todo Remove error operator
 */
function encrypt_db_password($db_pass) {

	if (extension_loaded('mcrypt')) {

		$td = @mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');
		$key = EasySCP_Registry::get('MCRYPT_KEY');
		$iv = EasySCP_Registry::get('MCRYPT_IV');

		// compatibility with used perl pads
		$block_size = @mcrypt_enc_get_block_size($td);
		$strlen = strlen($db_pass);

		$pads = $block_size-$strlen % $block_size;

		$db_pass .= str_repeat(' ', $pads);

		// Initialize encryption
		@mcrypt_generic_init($td, $key, $iv);
		// Encrypt string
		$encrypted = @mcrypt_generic($td, $db_pass);
		@mcrypt_generic_deinit($td);
		@mcrypt_module_close($td);

		$text = @base64_encode("$encrypted");

		// Show encrypted string
		return trim($text);
	} else {
		throw new EasySCP_Exception(
			tr("ERROR: PHP extension 'mcrypt' not loaded!")
		);
	}
}
