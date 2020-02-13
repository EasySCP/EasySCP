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

if ($_POST['a'] == 'sendRequest'){
	$out = '';

	$socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
	if ($socket < 0) {$out = "socket_create() failed.\n";}

	$result = socket_connect($socket, '/var/run/easyscp_daemon/easyscp_daemon.sock');
	if ($result == false) {$out = "socket_connect() failed.\n";	}

	socket_read($socket, 1024, PHP_NORMAL_READ);

	switch($_POST['b']){
		case '100 CORE Restart':
			socket_write($socket, trim($_POST['b']) . "\n", strlen(trim($_POST['b']) . "\n"));
			break;
		case '100 CORE Setup EasySCP_Finish_Setup':
			socket_write($socket, trim($_POST['b']) . "\n", strlen(trim($_POST['b']) . "\n"));
			break;
		default:
			socket_write($socket, trim($_POST['b']) . "\n", strlen(trim($_POST['b']) . "\n"));
			$out = trim(socket_read($socket, 1024, PHP_NORMAL_READ));
	}

	socket_shutdown($socket, 2);
	socket_close($socket);

	if ($out != ''){
		echo $out;
	}

}
?>