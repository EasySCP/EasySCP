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
 * DatabaseResult class -  Wrap the PDOStatement class
 *
 * @property mixed EOF
 * @property mixed fields
 *
 * @category	EasySCP
 * @package     EasySCP_Database
 * @subpackage  ResultSet
 * @copyright 	2010-2019 by EasySCP | http://www.easyscp.net
 * @author 		EasySCP Team
 */
class EasySCP_Database_ResultSet {

	/**
	 * PDOStatement object
	 *
	 * @var PDOStatement
	 */
	protected $_stmt = null;

	/**
     * Default fetch mode
	 *
	 * Controls how the next row will be returned to the caller. This value must
	 * be one of the PDO::FETCH_* constants
     *
     * @var integer
     */
    protected $_fetchMode = PDO::FETCH_ASSOC;

	/**
	 * A row from the result set associated with the referenced PDOStatement
	 * object
	 *
	 * @see fields()
	 * @see _get()
	 * @var array
	 */
	protected $_fields = null;

	/**
	 * Create a new DatabaseResult object
	 *
	 * @throws EasySCP_Exception_Database
	 * @param PDOStatement $stmt A PDOStatement instance
	 * @return void
	 */
	public function __construct($stmt) {

		if(!($stmt instanceof PDOStatement)) {
			throw new EasySCP_Exception_Database(
				'Argument passed to ' . __METHOD__ . '() must be a ' .
					'PDOStatement object!'
			);
		}

		$this->_stmt = $stmt;
	}

	/**
	 * PHP overloading
	 *
	 * PHP overloading method that allows to fetch the first row in the result
	 * set or check if one row exist in the result set
	 *
	 * @throws EasySCP_Exception_Database
	 * @param  string $param
	 * @return mixed Depending of the $param value, this method can returns the
	 * first row of a result set or a boolean that indicate if any rows exists
	 * in the result set
	 */
	public function __get($param) {

		if ($param == 'fields') {
			if (is_null($this->_fields)) {
				$this->_fields = $this->fetchRow();
			}

			return $this->_fields;
		}

		if ($param == 'EOF') {
			if ($this->_stmt->rowCount() == 0) {
				return true;
			}

			return !is_null($this->_fields) && !is_array($this->_fields);
		}

		throw new EasySCP_Exception_Database("Unknown parameter: `$param`");
	}

	/**
	 * Gets column field value from the current row
	 *
	 * @see get()
	 * @param string $param Colum field name
	 * @return mixed Column value
	 */
	public function fields($param) {

		return $this->fields[$param];
	}

	/**
	 * Returns the number of rows affected by the last SQL statement
	 *
	 * This method returns the number of rows affected by the last DELETE,
	 * INSERT, or UPDATE SQL statement
	 *
	 * If the last SQL statement executed by the associated PDOStatement was a
	 * SELECT statement, some RDBMS (like Mysql) may return the number of rows
	 * returned by that statement. However, this behaviour is not guaranteed for
	 * all RDBMS and should not be relied on for portable applications.
	 *
	 * @return int Number of rows affected by the last SQL statement
	 */
	public function rowCount() {

		return $this->_stmt->rowCount();
	}

	/**
	 * Alias of the rowCount() method
	 *
	 * @see rowCount()
	 * @return int Number of rows affected by the last SQL statement
	 */
	public function recordCount() {

		return $this->_stmt->rowCount();
	}

	/**
	 * Set fetch style globally
	 *
	 * This methods allows to set fetch style globally for all rows
	 *
	 * Note: Currently, all fetch style are not implemented
	 *
	 * @author Laurent Declercq <laurent.declercq@ispcp.net>
	 * @since 1.0.7
	 * @param int $fetchStyle Controls how the next row will be returned to the
	 * caller. This value must be one of the PDO::FETCH_* constants
	 * @return void
	 * @todo Finish fetch style implementation
	 */
	public function setFetchStyle($fetchStyle) {

		$this->_fetchMode = $fetchStyle;
	}

	/**
	 * Fetches the next row from the current result set
	 *
	 * Fetches a row from the result set. The fetch_style parameter determines
	 * how the row is returned.
	 *
	 * @param int $fetchStyle Controls how the next row will be returned to the
	 * caller. This value must be one of the PDO::FETCH_* constants
	 * @return mixed The return value of this function on success depends on the
	 * fetch style. In all cases, FALSE is returned on failure.
	 * @todo Finish fetch style implementation
	 */
	public function fetchRow($fetchStyle = null) {

		$fetchStyle = is_null($fetchStyle) ? $this->_fetchMode : $fetchStyle;

		return $this->_stmt->fetch($fetchStyle);
	}

	/**
	 * Fetches all rows from the current result set
	 *
	 * Fetches all rows from the result set. The fetch_style parameter
	 * determines how the rows are returned.
	 *
	 * @param int $fetchStyle Controls how the next row will be returned to the
	 * caller. This value must be one of the PDO::FETCH_* constants
	 * @return mixed The return value of this function on success depends on the
	 * fetch style. In all cases, FALSE is returned on failure.
	 * @todo Finish fetch style implementation
	 */
	public function fetchAll($fetchStyle = null) {
		
		$fetchStyle = is_null($fetchStyle) ? $this->_fetchMode : $fetchStyle;

		return $this->_stmt->fetchAll($fetchStyle);
	}

	/**
	 * Fetches the next row from the current result set
	 *
	 * @return void
	 */
	public function moveNext() {

		$this->_fields = $this->fetchRow();
	}

	/**
	 * Error information associated with the last operation on the statement
	 * handle
	 *
	 * @author Laurent Declercq <laurent.declercq@ispcp.net>
	 * @since 1.0.7
	 * @return array Error information
	 */
	public function errorInfo() {

		return $this->_stmt->errorInfo();
	}

	/**
	 * Stringified error information
	 *
	 * This method returns a stringified version of the error information
	 * associated with the last statement operation.
	 *
	 * @author Laurent Declercq <laurent.declercq@ispcp.net>
	 * @since 1.0.7
	 * @return string Error information
	 */
	public function errorInfoToString() {

		return implode(' - ', $this->_stmt->errorInfo());
	}
}
