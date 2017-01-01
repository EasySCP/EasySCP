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
 * Class EasySCP_Update_Version implements the EasySCP_Update abstract class for
 * future online version update functions
 *
 * @category	EasySCP
 * @package		EasySCP_Update
 * @copyright 	2010-2012 by EasySCP | http://www.easyscp.net
 * @author 		EasySCP Team
 */
class EasySCP_Update_Version extends EasySCP_Update {

	/**
	 * EasySCP_Update_Version instance
	 *
	 * @var EasySCP_Update_Version
	 */
	protected static $_instance = null;

	/**
	 * Database variable name for the update version
	 *
	 * @var string
	 */
	protected $_databaseVariableName = 'VERSION_UPDATE';

	/**
	 * Error message string
	 *
	 * @var string
	 */
	protected $_errorMessage = 'Version update %s failed';

	/**
	 * Gets a EasySCP_Update_Version instance
	 *
	 * @return EasySCP_Update_Version
	 */
	public static function getInstance() {

		if (is_null(self::$_instance)) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Return the current EasySCP installed version
	 *
	 * @return int Current EasySCP installed version
	 */
	protected function _getCurrentVersion() {

		$cfg = EasySCP_Registry::get('Config');

		return (int) $cfg->BuildDate;
	}

	/**
	 * Gets the last available EasySCP version
	 *
	 * @return bool|int Returns the last EasySCP version available or FALSE on
	 * failure
	 * @todo Rename this function name that don't reflects the real purpose
	 */
	protected function _getNextVersion() {

		$last_update = "http://www.easyscp.net/latest2.txt";
		ini_set('user_agent', 'Mozilla/5.0');
		$timeout = 2;
		$old_timeout = ini_set('default_socket_timeout', $timeout);
		$dh2 = @fopen($last_update, 'r');
		ini_set('default_socket_timeout', $old_timeout);

		if (!is_resource($dh2)) {
			$this->_addErrorMessage(
				tr("Couldn't check for updates! Website not reachable."),
				'error'
			);

			return false;
		}

		$last_update_result = (int) fread($dh2, 8);
		fclose($dh2);

		return $last_update_result;
	}

	/**
	 * Check for EasySCP update
	 *
	 * @return boolean TRUE if a new EasySCP version is available FALSE otherwise
	 * @todo Rename this function name that don't reflects the real purpose
	 */
	public function checkUpdateExists() {

		return ($this->_getNextVersion() > $this->_currentVersion)
			? true : false;
	}

	/**
	 * Should be documented
	 *
	 * @param  $version
	 * @return string
	 */
	protected function _returnFunctionName($version) {

		return 'dummyFunctionThatAllwaysExists';
	}

	/**
	 * Should be documented
	 *
	 * @param  $engine_run_request
	 * @return void
	 */
	protected function dummyFunctionThatAllwaysExists(&$engine_run_request) {
		// uncomment when engine part will be ready
		/*
		$dbConfig = EasySCP_Registry::get(DbConfig);
		$dbConfig->VERSION_UPDATE = $this->getNextVersion();
		$engine_run_request = true;
		 */
	}
}
