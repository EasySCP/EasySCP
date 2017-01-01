<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2017 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * Statische Klasse für Socket zugriffe.
 *
 */
class SocketHandler {

	public static $File;
	private static $Socket;
	private static $Socket_Bind;

	static public function Create() {
		self::$Socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
		if (self::$Socket !== false)
		{
			return true;
		} else {
			return false;
		}
	}

	static public function Bind() {
		if (file_exists(self::$File)){
			unlink(self::$File);
		}
		self::$Socket_Bind = socket_bind(self::$Socket, self::$File);
		if (self::$Socket_Bind !== false)
		{
			return true;
		} else {
			return false;
		}
	}

	static public function Listen()
	{
		if (socket_listen(self::$Socket) !== FALSE)
		{
			return true;
		} else {
			return false;
		}
	}

	static public function Accept()
	{
		if ($Client = @socket_accept(self::$Socket))
		{
			return $Client;
		} else {
			return false;
		}
	}

	static public function Write($Client, $MSG)
	{
		return socket_write($Client, $MSG."\n");
	}

	static public function Read($Client)
	{
		return @socket_read($Client, 10240, PHP_NORMAL_READ);
	}

	static public function CloseClient($Client)
	{
		if ($Client != Null) {
			@socket_shutdown($Client, 2); //Schließe den Socket in beiden Richtungen
			socket_close($Client);
		}
	}

	static public function Close()
	{
		// socket_close(self::$Socket);
		unlink(self::$File);
	}

	static public function Block($block)
	{
	    if ($block === true){
		// Block socket type
		socket_set_block(self::$Socket);
	    } else {
		// Non block socket type
		socket_set_nonblock(self::$Socket);
	    }
	}
}
?>