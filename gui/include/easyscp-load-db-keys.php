<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 *
 * @copyright 	2006-2010 by ispCP | http://isp-control.net
 * @copyright 	2010-2017 by Easy Server Control Panel - http://www.easyscp.net
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
 * The Original Code is "ispCP ω (OMEGA) a Virtual Hosting Control Panel".
 *
 * The Initial Developer of the Original Code is ispCP Team.
 * Portions created by Initial Developer are Copyright (C) 2006-2011 by
 * isp Control Panel. All Rights Reserved.
 *
 * Portions created by the EasySCP Team are Copyright (C) 2010-2012 by
 * Easy Server Control Panel. All Rights Reserved.
 */

/**
 * Load EasySCP database encrypted password
 * @param string $easyscpKeyFile EasySCP key file
 * @return array element 0 = db_pass_key, element 1 = db_pass_iv
 */
function EasySCP_loadDBKeys($easyscpKeyFile) {

	$lines = file($easyscpKeyFile);
	foreach ($lines as $line) {
		$pos = strpos($line, '=');
		if ($pos > 0) {
			$key = trim(substr($line, 0, $pos));
			$value = trim(substr($line, $pos + 1));

			if ($key == 'DB_PASS_KEY') {
				$db_pass_key = $value;
			} elseif ($key == 'DB_PASS_IV') {
				$db_pass_iv = $value;
			}
		}
	}

	return array($db_pass_key, $db_pass_iv);
}

function EasySCP_getKeyFile() {
	switch (PHP_OS) {
			case 'FreeBSD':
			case 'OpenBSD':
			case 'NetBSD':
					$easyscp_etc_dir = '/usr/local/etc/easyscp/easyscp-keys.conf';
					break;
			default:
					$easyscp_etc_dir = '/etc/easyscp/easyscp-keys.conf';
	}

	return $easyscp_etc_dir;
}

$easyscpKeyFile = EasySCP_getKeyFile();

if (file_exists($easyscpKeyFile)) {
	// load relevant keys from new configuration file
	list($easyscp_db_pass_key, $easyscp_db_pass_iv) = EasySCP_loadDBKeys($easyscpKeyFile);
} else {
	// old style include
	require_once INCLUDEPATH . '/easyscp-db-keys.php';
}
