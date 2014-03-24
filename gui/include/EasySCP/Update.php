<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
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
 * Abstract class to implement update functions
 *
 * @category	EasySCP
 * @package     EasySCP_Update
 * @copyright 	2010-2012 by EasySCP | http://www.easyscp.net
 * @author 		EasySCP Team
 */
abstract class EasySCP_Update {

	/**
	 * Version of the last update that was applied
	 *
	 * @var int
	 */
	protected $_currentVersion = 0;

	/**
	 * Error messages for updates that have failed
	 *
	 * @var string
	 */
	protected $_errorMessages = '';

	/**
	 * Database variable name for the update version
	 *
	 * @var string
	 */
	protected $_databaseVariableName = '';

	/**
	 * Update functions prefix
	 *
	 * @var string
	 */
	protected $_functionName = '';

	/**
	 * Error message for updates that have failed
	 *
	 * @var string
	 */
	protected $_errorMessage = '';

	/**
	 * This class implements the singleton design pattern
	 *
	 * @return void
	 */
	protected function __construct() {

		$this->_currentVersion = $this->_getCurrentVersion();
	}

	/**
	 * This class implements the singleton design pattern
	 *
	 * @return void
	 */
	protected function __clone() {}

	/**
	 * Returns the version of the last update that was applied
	 *
	 * @return int Last update that was applied
	 */
	protected function _getCurrentVersion() {

		$dbConfig = EasySCP_Registry::get('Db_Config');

		return (int) $dbConfig->get($this->_databaseVariableName);
	}

	/**
	 * Returns the version of the next update
	 *
	 * @return int The version of the next update
	 */
	protected function _getNextVersion() {

		return $this->_currentVersion + 1;
	}

	/**
	 * Checks if a new update is available
	 *
	 * @return boolean TRUE if a new update is available, FALSE otherwise
	 */
	public function checkUpdateExists() {

		$functionName = $this->_returnFunctionName($this->_getNextVersion());

		return (method_exists($this, $functionName)) ? true : false;
	}

	/**
	 * Returns the name of the function that provides the update
	 *
	 * @return string Update function name
	 */
	protected function _returnFunctionName($version) {

		return $this->_functionName . $version;
	}

	/**
	 * Sends a request to the EasySCP daemon
	 *
	 * @return void
	 */
	protected function _sendEngineRequest() {

		send_request();
	}

	/**
	 * Adds a new message in the errors messages cache
	 *
	 * @return void
	 */
	protected function _addErrorMessage($message) {

		$this->_errorMessages .= $message;
	}

	/**
	 * Accessor for error messages
	 *
	 * @return string Error messages
	 */
	public function getErrorMessage() {

		return $this->_errorMessages;
	}

	/**
	 * Executes all available updates
	 *
	 * This method executes all available updates. If a query provided by an
	 * update fail, the succeeded queries from this update will not executed
	 * again.
	 *
	 * @return boolean TRUE on success, FALSE otherwise
	 * @todo Should be more generic (Only the database variable should be
	 * updated here. Other stuff should be implemented by the concrete class
	 */
	public function executeUpdates() {

		$sql = EasySCP_Registry::get('Pdo');
		$dbConfig = EasySCP_Registry::get('Db_Config');

		$engine_run_request = false;

		while ($this->checkUpdateExists()) {

			// Get the next database update Version
			$newVersion = $this->_getNextVersion();

			// Get the needed function name
			$functionName = $this->_returnFunctionName($newVersion);

			// Pull the query from the update function using a variable function
			$queryArray = $this->$functionName($engine_run_request);

			// First, switch to exception mode for errors management
			$sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// Checks if a database updated previously failed
			if(isset($dbConfig->FAILED_UPDATE)) {
				list($failedUpdate, $queryNb) = $dbConfig->FAILED_UPDATE;
			} else {
				$failedUpdate = 'inexistent';
				$queryNb = -1;
			}

			// We execute all SQL statements
			foreach($queryArray as $index => $query) {

				// Query was already applied with success ?
				if ($functionName == $failedUpdate && $index < $queryNb) {
					continue;
				}

				try {
					$sql->query($query);
					unset($dbConfig->FAILED_UPDATE);

					// Update revision
					$dbConfig->set($this->_databaseVariableName, $newVersion);

				} catch (PDOException $e) {

					// Store the query number and function name that wraps it
					$dbConfig->FAILED_UPDATE = "$functionName;$index";

					// Prepare error message
					$errorMessage =  sprintf($this->_errorMessage, $newVersion);

					// Extended error message
					if(PHP_SAPI != 'cli') {
						$errorMessage .= ':<br /><br />' . $e->getMessage() .
							'<br /><br />Query: ' . trim($query);
					} else {
						$errorMessage .= ":\n\n" . $e->getMessage() .
							"\nQuery: " . trim($query);
					}

					$this->_addErrorMessage($errorMessage);

					// An error occurred, we stop here !
					return false;
				}
			}

			$this->_currentVersion = $newVersion;

		} // End while

		// We should never run the backend scripts from the CLI update script
		if(PHP_SAPI != 'cli' && $engine_run_request) {
			$this->_sendEngineRequest();
		}

		return true;
	}
}
