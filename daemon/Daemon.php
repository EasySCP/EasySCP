#!/usr/bin/php -q

<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2020 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

if (isset($_SERVER['argv'])){
	$out = '';

	switch($_SERVER['argv'][1]) {
		case 'Control':
			$socket_file = '/var/run/easyscp_control/easyscp_control.sock';
			break;

		case 'Daemon':
			$socket_file = '/var/run/easyscp_daemon/easyscp_daemon.sock';
			break;

		default:
			$socket_file = '';
	}

	if (file_exists($socket_file)){
		$socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
		if ($socket < 0) {$out = "socket_create() failed.\n";}

		$result = socket_connect($socket, $socket_file);
		if ($result == false) {$out = "socket_connect() failed.\n";	}

		socket_read($socket, 1024, PHP_NORMAL_READ);

		socket_write($socket, trim($_SERVER['argv'][2]) . "\n", strlen(trim($_SERVER['argv'][2]) . "\n"));

		if($_SERVER['argv'][1] == 'Daemon'){
			$out = trim(socket_read($socket, 1024, PHP_NORMAL_READ));
		}

		socket_shutdown($socket, 2);
		socket_close($socket);
	}

	if ($out != ''){
		echo $out;
	}
}
?>