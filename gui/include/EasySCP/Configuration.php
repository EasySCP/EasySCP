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

/**
 * Class EasySCP_Configuration is the new EasySCP_Configuration.
 *
 * @category	EasySCP
 * @package		EasySCP_Configuriation
 * @copyright 	2010-2017 by EasySCP | http://www.easyscp.net
 * @author 		EasySCP Team
 */
class EasySCP_Configuration {

	/**
	 * Save the reference to current instance if exists
	 *
	 * @var Object $instance
	 */
	private static $instance = false;

	/**
	 * Save the current EasySCP_Configuration values
	 *
	 * @var Array $CONFIG
	 */
	private $CONFIG = array();

	/**
	 * Get an EasySCP_Configuration instance
	 *
	 * Returns a reference to a EasySCP_Configuration instance
	 * Creating it if it doesn't already exist.
	 *
	 * @return Object EasySCP_Configuration_Instance An EasySCP_Configuration instance
	 */
	public static function getInstance()
	{
		if(self::$instance === false)
		{
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct() {
		// $this->set ('ABC', '123');
		// $this->set ('XYZ', '789');

		$this->ABC = '123';
		$this->XYZ = '789';
	}
	private function __clone() {}

	/**
	 * Adds a key->value pair to the EasySCP_Configuration if it not exists
	 * Change a key->value pair in the EasySCP_Configuration if it exists
	 *
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value)
	{
		$this->CONFIG[$key] = $value;
	}

	/**
	 * Returns a key->value pair from the EasySCP_Configuration
	 *
	 * @param $key
	 * @return string value if $key exists
	 */
	public function __get($key)
	{
		if(array_key_exists($key, $this->CONFIG))
		{
			return $this->CONFIG[$key];
		} else {
			return null;
		}
	}

	public function loadIni($file) {
		$this->CONFIG = parse_ini_file($file, true);
	}

	public function setEnvironment($env) {
		$this->environment = $env;
	}

	public function printConfig() {
		var_dump($this->CONFIG);
	}
}
?>