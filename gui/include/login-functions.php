<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2010 by ispCP | http://isp-control.net
 * @copyright 	2010-2012 by Easy Server Control Panel - http://www.easyscp.net
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
 * Performs actions needed on session timeout
 */
function do_session_timeout() {

	$cfg = EasySCP_Registry::get('Config');

	$sql_param = array(
		':lastaccess' => time() - $cfg->SESSION_TIMEOUT * 60
	);

	$sql_query = "
		DELETE FROM
			login
		WHERE
			lastaccess < :lastaccess
	";

	DB::prepare($sql_query);
	DB::execute($sql_param)->closeCursor();

	if (!session_exists(session_id())) {
		unset($_SESSION['user_logged']);
		unset_user_login_data();
	}
}

/**
 * Checks if an session already exists and the IP address is matching
 *
 * @param string $sess_id User session id from cookie
 * @return TRUE if session is valid
 */
function session_exists($sess_id) {

	$sql_param = array(
			':session_id'	=> $sess_id,
			':ipaddr'		=> getipaddr()
	);

	$sql_query = "
		SELECT
			session_id, ipaddr
		FROM
			login
		WHERE
			session_id = :session_id
		AND
			ipaddr = :ipaddr;
	";

	DB::prepare($sql_query);
	$rs = DB::execute($sql_param);

	return ($rs->rowCount() == 0) ? false : true;
}

/**
 * Returns the user's IP address
 *
 * @return string User's Ip address
 * @todo adding proxy detection
 */
function getipaddr() {
	return $_SERVER['REMOTE_ADDR'];
}

/**
 * Initiates the login
 */
function init_login() {

	$cfg = EasySCP_Registry::get('Config');

	// just make sure to expire counters in case BRUTEFORCE is turned off
	unblock($cfg->BRUTEFORCE_BLOCK_TIME);

	if ($cfg->BRUTEFORCE) {
		is_ipaddr_blocked(null, 'bruteforce', true);
	}
}

/**
 * Checks if an user account name exists
 *
 * @param  string $username User Account name
 * @return boolean TRUE if the user Account name exists, FALSE otherwise
 */
function username_exists($username) {

	$sql_param = array(
		':admin_name'	=> $username
	);

	$sql_query = "
		SELECT
			admin_id
		FROM
			admin
		WHERE
			admin_name = :admin_name;
	";

	DB::prepare($sql_query);
	$rs = DB::execute($sql_param);

	return ($rs->rowCount() == 0) ? false : true;
}

/**
 * Returns the stored data related to an user account
 *
 * @param string $username User Account name
 * @return array An array that contains user account related data
 */
function get_userdata($username) {

	$sql_param = array(
		':admin_name'	=> $username
	);

	$sql_query = "
		SELECT
			*
		FROM
			admin
		WHERE
			admin_name = :admin_name;
	";

	DB::prepare($sql_query);
	return DB::execute($sql_param, true);
}

/**
 * Checks if an user account is expired
 *
 * @param  string $username User account name
 * @return boolean TRUE if the user account is not expired, FALSE otherwise
 */
function is_userdomain_expired($username) {

	$sql = EasySCP_Registry::get('Db');

	$udata = get_userdata($username);

	if (!is_array($udata)) {
		return false;
	}

	if ($udata['admin_type'] != 'user') {
		return true;
	}

	$query = "
		SELECT
			`domain_expires`
		FROM
			`domain`
		WHERE
			`domain_admin_id` = ?
	;";

	$res = exec_query($sql, $query, $udata['admin_id']);

	$row = $res->fetchRow();

	$result = false;
	if (!empty($row['domain_expires'])) {
		if (time() > $row['domain_expires'])
			$result = true;
	}

	return $result;
}

/**
 * Checks if an user account's status is 'ok'
 *
 * @param string $username User account name to be checked
 * @return boolean TRUE if the user account's status is 'ok', FALSE otherwise
 */
function is_userdomain_ok($username) {

	$cfg = EasySCP_Registry::get('Config');

	$udata = get_userdata($username);

	if (!is_array($udata)) {
		return false;
	}

	if ($udata['admin_type'] != 'user') {
		return true;
	}

	$sql_param = array(
		':domain_admin_id'	=> $udata['admin_id']
	);

	$sql_query = "
		SELECT
			status
		FROM
			domain
		WHERE
			domain_admin_id = :domain_admin_id;
	";

	DB::prepare($sql_query);
	$row = DB::execute($sql_param, true);

	return ($row['status'] == $cfg->ITEM_OK_STATUS);
}

/**
 * Unblock IP address
 *
 * @param  $timeout
 * @param string $type
 * @return void
 * @throws EasySCP_Exception
 */
function unblock($timeout = null, $type = 'bruteforce') {

	$cfg = EasySCP_Registry::get('Config');

	if (is_null($timeout)) {
		$timeout = $cfg->BRUTEFORCE_BLOCK_TIME;
	}

	$timeout = time() - ($timeout * 60);

	switch ($type) {
		case 'bruteforce':
			$sql_param = array(
				':login_count'=> $cfg->BRUTEFORCE_MAX_LOGIN,
				':lastaccess' => $timeout
			);

			$sql_query = "
				UPDATE
					login
				SET
					login_count = '1'
				WHERE
					login_count >= :login_count
				AND
					lastaccess < :lastaccess
				AND
					user_name is NULL;
			";

			break;
		case 'captcha':
			$sql_param = array(
					':captcha_count'=> $cfg->BRUTEFORCE_MAX_CAPTCHA,
					':lastaccess'	=> $timeout
			);

			$sql_query = "
				UPDATE
					login
				SET
					captcha_count = '1'
				WHERE
					captcha_count >= :captcha_count
				AND
					lastaccess < :lastaccess
				AND
					user_name is NULL
			;";

			break;
		default:
			write_log(
				'FIXME: ' . __FILE__ . ':' . __LINE__ . "\n" .
				'Unknown unblock reason ' . $type
			);
			throw new EasySCP_Exception('FIXME: ' . __FILE__ . ':' . __LINE__);
	}

	if(isset($sql_param) && isset($sql_query)){
		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();
	}
}

/**
 * Checks if an user IP address is blocked
 *
 * @param string $ipaddr IP Address to be checked
 * @param string $type Checking type (bruteforce|captcha)
 * @param bool $autodeny
 * @return boolean TRUE if the user Ip address is blocked, FALSE otherwise
 * @throws EasySCP_Exception
 */
function is_ipaddr_blocked($ipaddr = null, $type = 'bruteforce',
	$autodeny = false) {

	$cfg = EasySCP_Registry::get('Config');

	if (is_null($ipaddr)) {
		$ipaddr = getipaddr();
	}

	switch ($type) {
		case 'bruteforce':
			$sql_param = array(
				':ipaddr' => $ipaddr,
				':login_count' => $cfg->BRUTEFORCE_MAX_LOGIN
			);

			$sql_query = "
				SELECT
					*
				FROM
					login
				WHERE
					ipaddr = :ipaddr
				AND
					login_count = :login_count;
			";

			break;
		case 'captcha':
			$sql_param = array(
				':ipaddr' => $ipaddr,
				':captcha_count' => $cfg->BRUTEFORCE_MAX_CAPTCHA
			);

			$sql_query = "
				SELECT
					*
				FROM
					login
				WHERE
					ipaddr = :ipaddr
				AND
					captcha_count = :captcha_count;
			";

			break;
		default:
			write_log(
				'FIXME: ' . __FILE__ . ':' . __LINE__ . "\n" .
				'Unknown unblock reason ' . $type
			);
			throw new EasySCP_Exception('FIXME: ' . __FILE__ . ':' . __LINE__);
	}

	DB::prepare($sql_query);
	$rs = DB::execute($sql_param);

	if ($rs->rowCount() == 0) {
		return false;
	} else if (!$autodeny) {
		return true;
	}

	deny_access();
	return;
}

/**
 * Determine if the user should wait for login
 *
 * @param string $ipaddr
 * @param boolean $displayMessage
 * @return TRUE if...
 */
function shall_user_wait($ipaddr = null, $displayMessage = true) {

	$cfg = EasySCP_Registry::get('Config');

	if (!$cfg->BRUTEFORCE) {
		return false;
	}
	if (is_null($ipaddr)) {
		$ipaddr = getipaddr();
	}

	$sql_param = array(
		':ipaddr' => $ipaddr
	);

	$sql_query = "
		SELECT
			lastaccess
		FROM
			login
		WHERE
			ipaddr = :ipaddr
		AND
			user_name is NULL
	;";

	DB::prepare($sql_query);
	$rs = DB::execute($sql_param);

	if ($rs->rowCount() == 0) {
		return false;
	}

	$data = $rs->fetch();

	$lastaccess = $data['lastaccess'];

	if ($cfg->BRUTEFORCE_BETWEEN) {
		$btime = $lastaccess + $cfg->BRUTEFORCE_BETWEEN_TIME;
	} else {
		return false;
	}

	if ($btime > time()) {
		if ($displayMessage) {
			$baseServerVHostPrefix = (isset($_SERVER['HTTPS'])) ? "https://" : "http://";
			$backButtonDestination = $baseServerVHostPrefix . $cfg->BASE_SERVER_VHOST;

			system_message(
				tr('You have to wait %d seconds.', $btime - time()),
				'warning',
				$backButtonDestination
			);
		}

		return true;
	}

	return false;
}

/**
 * This function checks for bruteforce attemps on the login and lost-password
 * functions. As a result it blocks the coresponding IP address for a given time
 * span. (Default: 30 sec. between the retries, 30 min. after three retries.)
 *
 * @param string $ipaddr
 * @param string $type
 */
function check_ipaddr($ipaddr = null, $type = 'bruteforce') {

	$cfg = EasySCP_Registry::get('Config');

	if (is_null($ipaddr)) {
		$ipaddr = getipaddr();
	}

	$sess_id = session_id();

	$sql_param = array(
		':ipaddr' => $ipaddr
	);

	$sql_query = "
		SELECT
			session_id, ipaddr, user_name, lastaccess, login_count, captcha_count
		FROM
			login
		WHERE
			ipaddr = :ipaddr
		AND
			user_name IS NULL
		;
	";

	DB::prepare($sql_query);
	$rs = DB::execute($sql_param);

	// There is no record, add one
	if ($rs->rowCount() == 0) {
		$sql_param = array(
			':session_id'	=> $sess_id,
			':ipaddr'		=> $ipaddr,
			':login_count'	=> (int) ($type == 'bruteforce'),
			':captcha_count'=> (int) ($type == 'captcha')
		);

		$sql_query = "
			REPLACE INTO
				login (session_id, ipaddr, lastaccess, login_count, captcha_count)
			VALUES
				(:session_id, :ipaddr, UNIX_TIMESTAMP(), :login_count, :captcha_count);
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

	} elseif($cfg->BRUTEFORCE) {

		$data = $rs->fetch();

		$lastaccess = $data['lastaccess'];
		$logincount = $data['login_count'];
		$captchacount = $data['captcha_count'];

		if ($type == 'bruteforce' && $logincount > $cfg->BRUTEFORCE_MAX_LOGIN) {
			block_ipaddr($ipaddr, 'Login');
		}
		if ($type == 'captcha' && $captchacount > $cfg->BRUTEFORCE_MAX_CAPTCHA) {
			block_ipaddr($ipaddr, 'CAPTCHA');
		}

		if ($cfg->BRUTEFORCE_BETWEEN) {
			$btime = $lastaccess + $cfg->BRUTEFORCE_BETWEEN_TIME;
		} else {
			$btime = 0;
		}

		// Updating Timer for Bruteforce or Captcha
		if ($btime < time()) {
			if ($type == 'bruteforce') {
				$sql_param = array(
					':ipaddr' => $ipaddr
				);

				$sql_query = "
					UPDATE
						login
					SET
						lastaccess = UNIX_TIMESTAMP(),
						login_count = login_count+1
					WHERE
						ipaddr = :ipaddr
					AND
						user_name IS NULL;
				";
			} else if ($type == 'captcha') {
				$sql_param = array(
						':ipaddr' => $ipaddr
				);

				$sql_query = "
					UPDATE
						login
					SET
						lastaccess = UNIX_TIMESTAMP(),
						captcha_count = captcha_count+1
					WHERE
						ipaddr = :ipaddr
					AND
						user_name IS NULL;
				";
			}

			DB::prepare($sql_query);
			DB::execute($sql_param)->closeCursor();
		} else {
			$baseServerVHostPrefix = (isset($_SERVER['HTTPS'])) ? "https://" : "http://";
			$backButtonDestination = $baseServerVHostPrefix . $cfg->BASE_SERVER_VHOST;

			write_log(
				"Login error, <strong><em>$ipaddr</em></strong> wait " . ($btime - time()) .
				" seconds", E_USER_NOTICE
			);

			system_message(
				tr('You have to wait %d seconds.', $btime - time()),
				'warning',
				$backButtonDestination
			);
		}
	}
}

/**
 * Block an user's IP address
 *
 * @param string $ipaddr User's IP address
 * @param string $type
 */
function block_ipaddr($ipaddr, $type = 'General') {

	$cfg = EasySCP_Registry::get('Config');

	write_log(
			"$type protection, <strong><em> " .
			tohtml($ipaddr) .
			"</em></strong> blocked for " . $cfg->BRUTEFORCE_BLOCK_TIME . " minutes."
	);

	deny_access();
    return;
}

/**
 * Display an informational deny message
 *
 * @return void
 */
function deny_access() {

	$cfg = EasySCP_Registry::get('Config');

	$baseServerVHostPrefix = (isset($_SERVER['HTTPS'])) ? "https://" : "http://";
	$backButtonDestination = $baseServerVHostPrefix . $cfg->BASE_SERVER_VHOST;

	system_message(
		tr('You have been blocked for %d minutes.',	$cfg->BRUTEFORCE_BLOCK_TIME),
		'warning',
		$backButtonDestination
	);
}

/**
 * Should be documented
 *
 * @param  $uname User account name
 * @param  $upass User account password
 * @return boolean
 * @todo use more secure hash algorithm (see PHP mcrypt extension)
 */
function register_user($uname, $upass) {

	$cfg = EasySCP_Registry::get('Config');

	$baseServerVHostPrefix = (isset($_SERVER['HTTPS'])) ? "https://" : "http://";
	$backButtonDestination = $baseServerVHostPrefix . $cfg->BASE_SERVER_VHOST;

	check_ipaddr();

	if (!username_exists($uname)) {
		write_log("Login error, <strong><em>".tohtml($uname)."</em></strong> unknown username");
		system_message(
			tr('You entered an incorrect username/password.'),
			'warning',
			$backButtonDestination
		);

		return false;
	}

	$udata = get_userdata($uname);

	if ((EasySCP_Update_Database::getInstance()->checkUpdateExists() ||
		($cfg->MAINTENANCEMODE)) && $udata['admin_type'] != 'admin') {

		write_log(
			"Login error, <strong><em>" . $uname .
			"</em></strong> system currently in maintenance mode"
		);

		system_message(
			tr('System is currently under maintenance! Only administrators can log in.'),
			'info'
		);

		return false;
	}

	if (crypt($upass, $udata['admin_pass']) == $udata['admin_pass']
		|| md5($upass) == $udata['admin_pass']) {

		if (isset($_SESSION['user_logged'])) {
			write_log(
				tr("%s user already logged or session sharing problem! Aborting...",
					$uname)
			);

			system_message(
				tr('User already logged or session sharing problem! Aborting...'),
				'error'
			);

			unset_user_login_data();

			return false;
		}

		if (!is_userdomain_ok($uname)) {
			write_log(tr("%s's account status is not ok!", $uname));
			system_message(
				tr("%s's account status is not ok!", $uname),
				'error'
			);

			return false;
		}

		if ($udata['admin_type'] == 'user' && is_userdomain_expired($uname)) {
			write_log(tr("%s's domain expired!", $uname));
			system_message(
				tr("%s's domain expired!", $uname),
				'info'
			);

			return false;
		}

		$sql_param = array(
			':user_name'	=> $uname,
			':lastaccess'	=> time(),
			':session_id'	=> session_id()
		);

		$sql_query = "
			UPDATE
				login
			SET
				user_name = :user_name,
				lastaccess = :lastaccess
			WHERE
				session_id = :session_id;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		$_SESSION['user_logged'] = $udata['admin_name'];
		$_SESSION['user_pass'] = $udata['admin_pass'];
		$_SESSION['user_type'] = $udata['admin_type'];
		$_SESSION['user_id'] = $udata['admin_id'];
		$_SESSION['user_email'] = $udata['email'];
		$_SESSION['user_created_by'] = $udata['created_by'];
		$_SESSION['user_login_time'] = time();

		write_log($uname." logged in.");

		return true;
	} else {
		write_log($uname . ' entered incorrect password.');
		system_message(
			tr('You entered an incorrect username/password.'),
			'warning',
			$backButtonDestination
		);

		return false;
	}
}

/**
 * Check user login
 *
 * @return boolean
 */
function check_user_login() {

	$cfg = EasySCP_Registry::get('Config');

	$sess_id = session_id();
	// kill timed out sessions
	do_session_timeout();
	$user_logged = isset($_SESSION['user_logged']) ? $_SESSION['user_logged'] : false;

	if (!$user_logged) {
		return false;
	}

	$sql_param = array(
		':admin_name'	=> $user_logged,
		':admin_pass'	=> $_SESSION['user_pass'],
		':admin_type'	=> $_SESSION['user_type'],
		':admin_id'		=> $_SESSION['user_id'],
		':session_id'	=> $sess_id
	);

	// verify session data with database
	$sql_query = "
		SELECT
			*
		FROM
			admin, login
		WHERE
			admin.admin_name = :admin_name
		AND
			admin.admin_pass = :admin_pass
		AND
			admin.admin_type = :admin_type
		AND
			admin.admin_id = :admin_id
		AND
			login.session_id = :session_id;
	";

	DB::prepare($sql_query);
	$rs = DB::execute($sql_param);

	if ($rs->rowCount() != 1) {
		write_log("Detected session manipulation on ".$user_logged."'s session!");
		unset_user_login_data();

		return false;
	}

	if ((EasySCP_Update_Database::getInstance()->checkUpdateExists() ||
		($cfg->MAINTENANCEMODE)) && $_SESSION['user_type'] != 'admin') {
		unset_user_login_data();
		write_log(
			"System is currently in maintenance mode. Logging out <strong><em>" .
			$user_logged . "</em></strong>"
		);

		user_goto('/index.php');
	}

	// if user login data correct - update session and lastaccess
	$_SESSION['user_login_time'] = time();

	$sql_param = array(
		':lastaccess'	=> time(),
		':session_id'	=> $sess_id
	);

	$sql_query = "
		UPDATE
			login
		SET
			lastaccess = :lastaccess
		WHERE
			session_id = :session_id
	;";

	DB::prepare($sql_query);
	DB::execute($sql_param);

	return true;
}

/**
 * check for valid user login
 *
 * @param string $fName Full file path (ie. the magic __FILE__ constant value)
 */
function check_login($fName = null) {

	// session-type check:
	if (!check_user_login()) {

		if (is_xhr()) {
			header('HTTP/1.0 403 Forbidden');
			exit();
		}

		user_goto('/index.php');
	}

	if (!is_null($fName)) {

		// TODO: Prüfen ob das so ok ist. Das '\\' wird bei Windows Pfaden benötigt.
		// $levels = explode('/', realpath(dirname($fName)));
		// $levels = explode('\\', realpath(dirname($fName)));
		// $level = $levels[count($levels) - 1];
		$level = basename(dirname($fName));

		$userType = ($_SESSION['user_type'] == 'user')
			? 'client' : $_SESSION['user_type'];

		if($userType != $level) {
			if($userType != 'admin' &&
				(!isset($_SESSION['logged_from']) ||
					$_SESSION['logged_from'] != 'admin' )) {

				$userLoggued = isset($_SESSION['logged_from'])
					? $_SESSION['logged_from'] : $_SESSION['user_logged'];

				write_log(
					'Warning! user |' . $userLoggued . '| requested |' .
						tohtml($_SERVER['REQUEST_URI']) . '| with REQUEST_METHOD |' .
							$_SERVER['REQUEST_METHOD'] . '|'
				);
			}

			user_goto('/index.php');
		}
	}
}

/**
 * Switch between user's interfaces
 *
 * This function allows to switch bettwen user's interfaces for admin and
 * reseller user accounts.
 *
 * @param  $from_id User's id that want switch to an other user's interface
 * @param  $to_id User identifier that represents the destination interface
 * @return void
 */
function change_user_interface($from_id, $to_id) {

	$sql = EasySCP_Registry::get('Db');

	$index = null;

	while (1) { // used to easily exit
		$query = "
			SELECT
				`admin_id`,
				`admin_name`,
				`admin_pass`,
				`admin_type`,
				`email`,
				`created_by`
			FROM
				`admin`
			WHERE
				binary `admin_id` = ?
		;";


		$rs_from = exec_query($sql, $query, $from_id);
		$rs_to = exec_query($sql, $query, $to_id);

		if (($rs_from->recordCount()) != 1 || ($rs_to->recordCount()) != 1) {
			set_page_message(
				tr('User does not exist or you do not have permission to access this interface!'),
				'warning'
			);
			break;
		}

		$from_udata = $rs_from->fetchRow();
		$to_udata = $rs_to->fetchRow();

		if (!is_userdomain_ok($to_udata['admin_name'])) {
			set_page_message(
				tr(
					"%s's account status is not ok!",
					decode_idna($to_udata['admin_name'])
				),
				'warning'
			);
			break;
		}

		$to_admin_type = strtolower($to_udata['admin_type']);
		$from_admin_type = strtolower($from_udata['admin_type']);

		$allowed_changes = array();

		$allowed_changes['admin']['admin'] = 'manage_users.php';
		$allowed_changes['admin']['BACK'] = 'manage_users.php';
		$allowed_changes['admin']['reseller'] = 'index.php';
		$allowed_changes['admin']['user'] = 'index.php';
		$allowed_changes['reseller']['user'] = 'index.php';
		$allowed_changes['reseller']['BACK'] = 'users.php?psi=last';

		if (!isset($allowed_changes[$from_admin_type][$to_admin_type]) ||
			($to_admin_type == $from_admin_type &&
				$from_admin_type != 'admin')) {

			if (isset($_SESSION['logged_from_id'])
				&& $_SESSION['logged_from_id'] == $to_id) {
				$index = $allowed_changes[$to_admin_type]['BACK'];
			} else {
				set_page_message(
					tr('You do not have permission to access this interface!'),
					'warning'
				);
				break;
			}
		}

		$index = $index ? $index : $allowed_changes[$from_admin_type][$to_admin_type];

		unset_user_login_data();

		if (($to_admin_type != 'admin' && ((isset($_SESSION['logged_from_id']) &&
			$_SESSION['logged_from_id'] != $to_id) ||
			!isset($_SESSION['logged_from_id']))) ||
			($from_admin_type == 'admin' && $to_admin_type == 'admin')) {

			$_SESSION['logged_from'] = $from_udata['admin_name'];
			$_SESSION['logged_from_id'] = $from_udata['admin_id'];

		}

		// Ticket 830 - remove the 'logged_from' if back from user
		if ($from_admin_type == 'user') {
			// maybe integrated in the construction above...
			unset($_SESSION['logged_from']);
			unset($_SESSION['logged_from_id']);
		}

		// we gonna kill all sessions and globals if user get back to admin level
		unset($_SESSION['admin_name']);
		unset($_SESSION['admin_id']);

		unset($GLOBALS['admin_name']);
		unset($GLOBALS['admin_id']);

		// no more sessions and globals to kill - they were always killed -
		// rest in peace

		$_SESSION['user_logged'] = $to_udata['admin_name'];
		$_SESSION['user_pass'] = $to_udata['admin_pass'];
		$_SESSION['user_type'] = $to_udata['admin_type'];
		$_SESSION['user_id'] = $to_udata['admin_id'];
		$_SESSION['user_email'] = $to_udata['email'];
		$_SESSION['user_created_by'] = $to_udata['created_by'];
		$_SESSION['user_login_time'] = time();

		$query = "
			INSERT INTO login
				(`session_id`, `ipaddr`, `user_name`, `lastaccess`)
			VALUES
				(?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE
				`lastaccess` = ?
		;";

		exec_query($sql, $query, array(session_id(), getipaddr(),
			$to_udata['admin_name'], $_SESSION['user_login_time'],
			$_SESSION['user_login_time']));

		write_log(
			sprintf(
				"%s changes into %s's interface",
				decode_idna($from_udata['admin_name']),
				decode_idna($to_udata['admin_name'])
			)
		);
		break;
	}

	redirect_to_level_page($index);
}

/**
 * Unset user login data
 *
 * @return void
 */
function unset_user_login_data() {

	if (isset($_SESSION['user_logged'])) {
		$sql_param = array(
				':session_id'	=> session_id(),
				':user_name'	=> $_SESSION['user_logged']
		);

		$sql_query = "
			DELETE FROM
				login
			WHERE
				session_id = :session_id
			AND
				user_name = :user_name
		";
		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();
	}

	$_SESSION = array();
}

/**
 * Redirects to user level page
 *
 * @param String $file
 * @param boolean $force
 * @return boolean false if $_SESSION['user_type'] not set and $force == false
 */
function redirect_to_level_page($file = 'index.php', $force = false) {

	if (!isset($_SESSION['user_type']) && !$force)
		return false;

	$user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : '';

	switch ($user_type) {
		case 'user':
			$user_type = 'client';
		case 'admin':
		case 'reseller':
			user_goto('/' . $user_type . '/' . $file);
			break;
		default:
			user_goto('/index.php');
	}
}
?>