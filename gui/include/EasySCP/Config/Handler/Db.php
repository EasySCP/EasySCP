<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2019 by Easy Server Control Panel - http://www.easyscp.net
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
 * @see EasySCP_Config_Handler
 */
require_once  INCLUDEPATH . '/EasySCP/Config/Handler.php';

/**
 * Class to handle configuration parameters from database
 *
 * EasySCP_Config_Handler adapter class to handle configuration parameters that
 * are stored in database.
 *
 * @package     EasySCP_Config
 * @subpackage  Handler
 */
class EasySCP_Config_Handler_Db extends EasySCP_Config_Handler implements iterator {

	/**
	 * Array that contains all configuration parameters from the database
	 *
	 * @var array
	 */
	protected $_parameters = array();

	/**
	 * PDOStatement to insert a configuration parameter in the database
	 *
	 * <b>Note:</b> For performance reason, the PDOStatement instance is created
	 * only once at the first execution of the {@link _insert()} method.
	 *
	 * @var PDOStatement
	 */
	protected $_insertStmt = null;

	/**
	 * PDOStatement to update a configuration parameter in the database
	 *
	 * <b>Note:</b> For performances reasons, the PDOStatement instance is
	 * created only once at the first execution of the {@link _update()} method.
	 *
	 * @var PDOStatement
	 */
	protected $_updateStmt = null;

	/**
	 * PDOStatement to delete a configuration parameter in the database
	 *
	 * <b>Note:</b> For performances reasons, the PDOStatement instance is
	 * created only once at the first execution of the {@link _delete()} method.
	 *
	 * @var PDOStatement
	 */
	protected $_deleteStmt = null;

	/**
	 * Variable bound to the PDOStatement instances
	 *
	 * This variable is bound to the PDOStatement instances that are used by
	 * {@link _insert()}, {@link _update()} and {@link _delete()} methods.
	 *
	 * @var string Configuration parameter key name
	 */
	protected $_key = null;

	/**
	 * Variable bound to the PDOStatement objects
	 *
	 * This variable is bound to the PDOStatement instances that are used by
	 * both {@link _insert()} and {@link _update()} methods.
	 *
	 * @var mixed Configuration parameter value
	 */
	protected $_value = null;

	/**
	 * Counter for SQL update queries
	 *
	 * @var int
	 */
	protected $_insertQueriesCounter = 0;

	/**
	 * Counter for SQL insert queries
	 *
	 * @var int
	 */
	protected $_updateQueriesCounter = 0;

	/**
	 * Loads all configuration parameters from the database
	 *
	 * @throws EasySCP_Exception
	 *
	 * @return EasySCP_Config_Handler_Db
	 */
	public function __construct() {
		$this->_loadAll();
	}

	/**
	 * Allow access as object properties
	 *
	 * @see set()
	 * @param string $key Configuration parameter key name
	 * @param mixed  $value Configuration parameter value
	 * @return void
	 */
	public function __set($key, $value) {

		$this->set($key, $value);
	}

	/**
	 * Insert or update a configuration parameter in the database
	 *
	 * <b>Note:</b> For performances reasons, queries for updates are only done
	 * if old and new value of a parameter are not the same.
	 *
	 * @param string $key Configuration parameter key name
	 * @param mixed $value Configuration parameter value
	 * @return void
	 */
	public function set($key, $value) {

		$this->_key = $key;
		$this->_value = $value;

		if(!$this->exists($key)) {
			$this->_insert();
		} elseif($this->_parameters[$key] != $value) {
			$this->_update();
		} else {
			return;
		}

		$this->_parameters[$key] = $value;
	}

	/**
	 * Retrieve a configuration parameter value
	 *
	 * @throws EasySCP_Exception
	 * @param string $key Configuration parameter key name
	 * @return mixed Configuration parameter value
	 */
	public function get($key) {

		if (!isset($this->_parameters[$key])) {
			throw new EasySCP_Exception(
				"Error: Configuration variable `$key` is missing!"
			);
		}

		return $this->_parameters[$key];
	}

	/**
	 * Checks if a configuration parameters exists
	 *
	 * @param string $key Configuration parameter key name
	 * @return boolean TRUE if configuration parameter exists, FALSE otherwise
	 */
	public function exists($key) {

		return array_key_exists($key, $this->_parameters);
	}

	/**
	 * PHP isset() overloading on inaccessible members
	 *
	 * This method is triggered by calling isset() or empty() on inaccessible
	 * members.
	 *
	 * <b>Note:</b> This method will return FALSE if the configuration parameter
	 * value is NULL. To test existence of a configuration parameter, you should
	 * use the {@link exists()} method.
	 *
	 * @param string $key Configuration parameter key name
	 * @return boolean TRUE if the parameter exists and its value is not NULL
	 */
	public function __isset($key) {

		return isset($this->_parameters[$key]);
	}

	/**
	 * PHP unset() overloading on inaccessible members
	 *
	 * This method is triggered by calling isset() or empty() on inaccessible
	 * members.
	 *
	 * @param  string $key Configuration parameter key name
	 * @return void
	 */
	public function __unset($key) {

		$this->del($key);
	}

	/**
	 * Force reload of all configuration parameters from the database
	 *
	 * This method will remove all the current loaded parameters and reload it
	 * from the database.
	 *
	 * @return void
	 */
	public function forceReload() {

		$this->_parameters = array();
		$this->_loadAll();
	}

	/**
	 * Returns the count of SQL queries that were executed
	 *
	 * This method returns the count of queries that were executed since the
	 * last call of {@link reset_queries_counter()} method.
	 *
	 * @param $queriesCounterType
	 * @throws EasySCP_Exception
	 * @internal param string $queriesCounter Query counter type (insert|update)
	 *
	 * @return void
	 */
	public function countQueries($queriesCounterType) {

		if($queriesCounterType == 'update') {

			return $this->_updateQueriesCounter;

		} elseif($queriesCounterType == 'insert') {

			return $this->_insertQueriesCounter;

		} else {
			throw new EasySCP_Exception('Error: Unknown queries counter!');
		}
	}

	/**
	 * Reset a counter of queries
	 *
	 * @throws EasySCP_Exception
	 * @param string $queriesCounterType Type of query counter (insert|update)
	 *
	 * @return void
	 */
	public function resetQueriesCounter($queriesCounterType) {

		if($queriesCounterType == 'update') {

			$this->_updateQueriesCounter = 0;

		} elseif($queriesCounterType == 'insert') {

			 $this->_insertQueriesCounter = 0;

		} else {
			throw new EasySCP_Exception('Error: Unknown queries counter!');
		}
	}

	/**
	 * Deletes a configuration parameters from the database
	 *
	 * @param string $key Configuration parameter key name
	 *
	 * @return void
	 */
	public function del($key) {

		$this->_key = $key;
		$this->_delete();

		unset($this->_parameters[$key]);
	}

	/**
	 * Load all configuration parameters from the database
	 *
	 * @throws EasySCP_Exception
	 *
	 * @return void
	 */
	protected function _loadAll() {

		$sql_query = "
			SELECT
				name, value
			FROM
				config
			ORDER BY
				name;
		";
		foreach (DB::query($sql_query) as $row) {
			$this->_parameters[$row['name']] = $row['value'];
		}

	}

	/**
	 * Store a new configuration parameter in the database
	 *
	 * @throws EasySCP_Exception_Database
	 *
	 * @return void
	 */
	protected function _insert() {

		$sql_param = array(
				':index' => $this->_key,
				':value' => $this->_value
		);

		$sql_query = "
			INSERT INTO
					config (name, value)
				VALUES
					(:index, :value)
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		$this->_insertQueriesCounter++;
		/*
		throw new EasySCP_Exception_Database(
			"Error: Unable to insert the configuration parameter `{$this->_key}` in the database"
		);
		*/
	}

	/**
	 * Update a configuration parameter in the database
	 *
	 * @throws EasySCP_Exception_Database
	 *
	 * @return void
	 */
	protected function _update() {

		$sql_param = array(
				':value' => $this->_value,
				':index' => $this->_key
		);

		$sql_query = "
			UPDATE
				config
			SET
				value = :value
			WHERE
				name = :index

		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		$this->_updateQueriesCounter++;

		/*
		throw new EasySCP_Exception_Database(
				"Error: Unable to update the configuration parameter `{$this->_key}` in the database!"
		);
		*/

	}

	/**
	 * Deletes a configuration parameter from the database
	 *
	 * @throws EasySCP_Exception_Database
	 *
	 * @return void
	 */
	protected function _delete() {

		$sql_param = array(
				':index' => $this->_key
		);

		$sql_query = "
			DELETE FROM
				config
			WHERE
				name = :index
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();
		/*
		 throw new EasySCP_Exception_Database(
			'Error: Unable to delete the configuration parameter in the database!'
		);
		 */
	}

	/**
	 * Whether or not an offset exists
	 *
	 * @param mixed $offset An offset to check for existence
	 *
	 * @return boolean TRUE on success or FALSE on failure
	 */
	public function offsetExists($offset) {

		return array_key_exists($this->_parameters, $offset);
	}

	/**
	 * Returns an associative array that contains all configuration parameters
	 *
	 * @return array Array that contains configuration parameters
	 */
	public function toArray() {

		return $this->_parameters;
	}

	/**
	 * Returns the current element
	 *
	 * @return mixed Returns the current element
	 */
	public function current() {

		return current($this->_parameters);
	}

	/**
	 * Returns the key of the current element
	 *
	 * @return scalar Return the key of the current element or NULL on failure
	 */
	public function key() {

		return key($this->_parameters);
	}

	/**
	 * Moves the current position to the next element
	 *
	 * @return void
	 */
	public function next() {

		next($this->_parameters);
	}

	/**
	 * Rewinds back to the first element of the Iterator
	 *
	 * <b>Note:</b> This is the first method called when starting a foreach
	 * loop. It will not be executed after foreach loops.
	 *
	 * @return void
	 */
	public function rewind() {

		reset($this->_parameters);
	}

	/**
	 * Checks if current position is valid
	 *
	 * @return boolean TRUE on success or FALSE on failure
	 */
	public function valid() {

		return array_key_exists(key($this->_parameters), $this->_parameters);
	}
}
