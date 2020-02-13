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
 * This script allows net2ftp authentication from EasySCP
 */

/*******************************************************************************
 * Functions
 */

/**
 * Get FTP login credentials
 *
 * @access private
 * @param  string $userId FTP User
 * @return mixed Array that contains login credentials or FALSE on failure
 */
function _getLoginCredentials($userId) {

	$sql_query = "
		SELECT
			`userid`, `net2ftppasswd`
		FROM
			`ftp_users`, `domain`
		WHERE
				`ftp_users`.`uid` = `domain`.`domain_uid`
			AND
				`ftp_users`.`userid` = :userid
			AND
				`domain`.`domain_admin_id` = :domain_admin_id;
	";

	$sql_param = array(
		'userid'			=> $userId,
		'domain_admin_id'	=> $_SESSION['user_id']
	);

	DB::prepare($sql_query);
	$row = DB::execute($sql_param, true);

	if(isset($row['userid'])) {
		return array(
			$row['userid'],
			DB::decrypt_data($row['net2ftppasswd'])
		);
	} else {
		return false;
	}
}

/**
 * Creates all cookies for net2ftp
 *
 * @author William Lightning <kassah@gmail.com>
 * @since  1.1.0
 * @access private
 * @param  array $cookies Array that contains cookies definitions for net2ftp
 * @return void
 */
function _net2ftpCreateCookies($cookies) {

	foreach($cookies as $cookie) {
		header("Set-Cookie: $cookie", false);
	}
}

/**
 * net2ftp authentication
 *
 * @author William Lightning <kassah@gmail.com>
 * @since  1.1.0
 * @param  int $userId ftp username
 * @return bool TRUE on success, FALSE otherwise
 */
function net2ftpAuth($userId) {

	$credentials = _getLoginCredentials($userId);

	if($credentials) {
		$data = http_build_query(
			array(
				'username'		=> $credentials[0],
				'password'		=> stripcslashes($credentials[1]),
				'ftpserver'		=> '127.0.0.1',
				'ftpserverport'	=> '21',
				'directory'		=> '',
				'language'		=> 'en',
				'ftpmode'		=> 'automatic',
				'state'			=> 'browse',
				'state2'		=> 'main'
			)
		);
	} else {
		set_page_message(tr('Error: Unknown FTP user id!'));

		return false;
	}

	// Prepares net2ftp absolute URI to use
	if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) {
		$port = ($_SERVER['SERVER_PORT'] != '443')
			? ':' . $_SERVER['SERVER_PORT'] : '';

		$net2ftpUri = "https://{$_SERVER['SERVER_NAME']}$port/ftp/";
	} else {
		$port = ($_SERVER['SERVER_PORT'] != '80')
			? ':' . $_SERVER['SERVER_PORT'] : '';

		$net2ftpUri = "http://{$_SERVER['SERVER_NAME']}$port/ftp/";
	}

	// Set stream context (http) options
	stream_context_get_default(
		array(
			'http' => array(
				'method' => 'POST',
				'header' => "Host: {$_SERVER['SERVER_NAME']}$port\r\n" .
					"Content-Type: application/x-www-form-urlencoded\r\n" .
					'Content-Length: ' . strlen($data) . "\r\n" .
					"Connection: close\r\n\r\n",
				'content' => $data,
				'user_agent' => $_SERVER["HTTP_USER_AGENT"],
				'max_redirects' => 1
			)
		)
	);

	// Gets the headers from PhpMyAdmin
	$headers = get_headers($net2ftpUri, true);

	// Absolute minimum I could get a listing with.
	$url = $net2ftpUri.'?ftpserver=127.0.0.1&username='.urlencode($userId).'&state=browse&state2=main';

	_net2ftpCreateCookies($headers['Set-Cookie']);
	header("Location: {$url}");

	return true;
}

/*******************************************************************************
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
	if(!net2ftpAuth($_GET['id'])) {
		user_goto('ftp_accounts.php');
	}
} else {
	user_goto('/index.php');
}
?>