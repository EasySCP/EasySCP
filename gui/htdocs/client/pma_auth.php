<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2020 by Easy Server Control Panel - http://www.easyscp.net
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

/**
 * Script short description:
 *
 * This script allows PhpMyAdmin authentication from EasySCP
 */

/**
 * Main program
 */

// Include all needed libraries and process to the EasySCP initialization
require '../../include/easyscp-lib.php';

// Check login
check_login(__FILE__);

/**
 *  Dispatches the request
 */
if(isset($_GET['id'])) {
	if(!pmaAuth((int) $_GET['id'])) {
		user_goto('sql_manage.php');
	}
} else {
	user_goto('/index.php');
}

/**
 * Functions
 */

/**
 * Get database login credentials
 *
 * @since  1.0.7
 * @access private
 * @param  int $dbUserId Database user unique identifier
 * @return mixed Array that contains login credentials or FALSE on failure
 */
function _getLoginCredentials($dbUserId) {

	$sql_query = "
		SELECT
			`sqlu_name`, `sqlu_pass`
		FROM
			`sql_user`, `sql_database`, `domain`
		WHERE
			`sql_user`.`sqld_id` = `sql_database`.`sqld_id`
		AND
			`sql_user`.`sqlu_id` = :sqlu_id
		AND
			`sql_database`.`domain_id` = `domain`.`domain_id`
		AND
			`domain`.`domain_admin_id` = :domain_admin_id;
	";

	$sql_param = array(
		'sqlu_id'			=> $dbUserId,
		'domain_admin_id'	=> $_SESSION['user_id']
	);

	DB::prepare($sql_query);
	$row = DB::execute($sql_param, true);

	if(isset($row['sqlu_name'])) {
		return array(
			$row['sqlu_name'],
			DB::decrypt_data($row['sqlu_pass'])
		);
	} else {
		return false;
	}
}

/**
 * Creates all cookies for PhpMyAdmin
 *
 * @since  1.0.7
 * @access private
 * @param  array $cookies Array that contains cookies definitions for PMA
 * @return void
 */
function _pmaCreateCookies($cookies) {

	foreach($cookies as $cookie) {
		header("Set-Cookie: $cookie", false);
	}
}

/**
 * PhpMyAdmin authentication
 *
 * @since  1.0.7
 * @param  int $dbUserId Database user unique identifier
 * @return bool TRUE on success, FALSE otherwise
 */
function pmaAuth($dbUserId) {

	$credentials = _getLoginCredentials($dbUserId);

	if($credentials) {
		$data = http_build_query(
			array(
				'pma_username' => $credentials[0],
				'pma_password' => stripcslashes($credentials[1])
			)
		);
	} else {
		set_page_message(tr('Unknown SQL user id!'), 'error');

		return false;
	}

	// Prepares PhpMyadmin absolute Uri to use
	//if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) {
	//	$port = ($_SERVER['SERVER_PORT'] != '443')
	//		? ':' . $_SERVER['SERVER_PORT'] : '';
	//
	//	$pmaUri = "https://{$_SERVER['SERVER_NAME']}$port/pma/";
	//} else {
	//	$port = ($_SERVER['SERVER_PORT'] != '80')
	//		? ':' . $_SERVER['SERVER_PORT'] : '';
	//
	//	$pmaUri = "http://{$_SERVER['SERVER_NAME']}$port/pma/";
	//}

	// Set stream context (http) options
	//stream_context_get_default(
	//	array(
	//		'http' => array(
	//			'method' => 'POST',
	//			'header' => "Host: {$_SERVER['SERVER_NAME']}$port\r\n" .
	//				"Content-Type: application/x-www-form-urlencoded\r\n" .
	//				'Content-Length: ' . strlen($data) . "\r\n" .
	//				"Connection: close\r\n\r\n",
	//			'content' => $data,
	//			'user_agent' => 'Mozilla/5.0',
	//			'max_redirects' => 1
	//		)
	//	)
	//);

	// Gets the headers from PhpMyAdmin
	//$headers = get_headers($pmaUri, true);
	//
	//if(!$headers || !isset($headers['Location'])) {
	//	set_page_message(tr('An error occurred while authentication!'), 'error');
	//	return false;
	//} else {
	//	_pmaCreateCookies($headers['Set-Cookie']);
	//	header("Location: {$headers['Location']}");
	//}
	
	/* Need to have cookie visible from parent directory */
	session_set_cookie_params(0, '/', '', true, true);

	/* Create signon session */
	$session_name = 'EasySCP';
	session_name($session_name);

	// Uncomment and change the following line to match your $cfg['SessionSavePath']
	//session_save_path('/foobar');
	@session_start();

	/* Store there credentials */
	$_SESSION['PMA_single_signon_user'] = $credentials[0];
	$_SESSION['PMA_single_signon_password'] = stripcslashes($credentials[1]);
	$id = session_id();

	/* Close that session */
	@session_write_close();

	/* Redirect to phpMyAdmin (should use absolute URL here!) */
	header('Location: /pma/index.php?server=2');

	return true;
}
?>