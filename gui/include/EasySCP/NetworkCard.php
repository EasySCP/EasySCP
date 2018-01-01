<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2018 by Easy Server Control Panel - http://www.easyscp.net
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
 * Class Network Card
 *
 * @category	EasySCP
 * @package		EasySCP_NetworkCard
 */
class EasySCP_NetworkCard {

	/**
	 * Should be documented
	 *
	 * @var array
	 */
	protected $_interfacesInfo = array();

	/**
	 * Should be documented
	 *
	 * @var array
	 */
	protected $_interfaces = array();

	/**
	 * Should be documented
	 *
	 * array
	 */
	protected $_offlineInterfaces = array();

	/**
	 * Should be documented
	 *
	 * @var array
	 */
	protected $_virtualInterfaces = array();

	/**
	 * Should be documented
	 *
	 * @var array
	 */
	protected $_availableInterfaces = array();

	/**
	 * Should be documented
	 *
	 * @var array
	 */
	protected $_errors = '';

	/**
	 * Should be documented
	 *
	 * @return EasySCP_NetworkCard
	 */
	public function __construct() {

		$this->_getInterface();
		$this->_populateInterfaces();
	}

	/**
	 * Should be documented
	 *
	 * @param  $filename
	 * @return string
	 */
	public function read($filename) {

		if (($result = @file_get_contents($filename)) === false) {
			$this->_errors .= sprintf(tr('File %s does not exists or cannot be reached!'), $filename);
			return '';
		}

		return $result;

	}

	/**
	 * Should be documented
	 *
	 * @return array
	 */
	public function network() {

		$file = $this->read('/proc/net/dev');
		preg_match_all('/(.+):.+/', $file, $dev_name);

		return $dev_name[1];
	}

	/**
	 * Should be documented
	 *
	 * @return void
	 */
	private function _getInterface() {

		foreach ($this->network() as $value) {
			$this->_interfaces[] = trim($value);
		}
	}

	/**
	 * Should be documented
	 *
	 * @param  string $strProgram
	 * @param  string &$strError
	 * @return bool|string
	 */
	protected function executeExternal($strProgram, &$strError) {

		$strBuffer = '';

		$descriptorspec = array(
			0 => array('pipe', 'r'),
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		$pipes = array();
		$process = proc_open($strProgram, $descriptorspec, $pipes);

		if (is_resource($process)) {
			while (!feof($pipes[1])) {
				$strBuffer .= fgets($pipes[1], 1024);
			}

			fclose($pipes[1]);

			while (!feof($pipes[2])) {
				$strError .= fgets($pipes[2], 1024);
			}

			fclose($pipes[2]);
		}

		$return_value = proc_close($process);
		$strError = trim($strError);
		$strBuffer = trim($strBuffer);

		if (!empty($strError) || $return_value != 0) {
			$strError .= "\nReturn value: " . $return_value;
			return false;
		}

		return $strBuffer;
	}

	/**
	 * Should be documented
	 *
	 * @return bool|void
	 */
	private function _populateInterfaces() {

		$cfg = EasySCP_Registry::get('Config');

		$err = '';
		$message = $this->executeExternal($cfg->CMD_IFCONFIG, $err);

		if (!$message) {
			$this->_errors .= tr('Error while trying to obtain list of network cards!') . $err;

			return false;
		}

		preg_match_all("/(?isU)([^ ]{1,}) {1,}.+(?:(?:\n\n)|$)/", $message, $this->_interfacesInfo);

		foreach ($this->_interfacesInfo[0] as $a) {
			if (preg_match("/inet addr\:([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/",$a,$b)) {
				$this->_interfacesInfo[2][] = trim($b[1]);
			} else {
				$this->_interfacesInfo[2][] = '';
			}
		}

		$this->_offlineInterfaces =
			array_diff($this->_interfaces, $this->_interfacesInfo[1]);

		$this->_virtualInterfaces =
			array_diff($this->_interfacesInfo[1], $this->_interfaces);

		switch(EasyConfig::$cfg->{'DistName'} . '_' . EasyConfig::$cfg->{'DistVersion'}){
			case 'Debian_9':
				$this->_availableInterfaces = array_diff(
						$this->_interfaces,
						array('lo')
				);
				break;
			default:
				$this->_availableInterfaces = array_diff(
						$this->_interfaces,
						$this->_offlineInterfaces,
						$this->_virtualInterfaces,
						array('lo')
				);
		}
	}

	/**
	 * Should be documented
	 *
	 * @return array
	 */
	public function getAvailableInterface() {

		return $this->_availableInterfaces;
	}

	/**
	 * Should be documented
	 *
	 * @return string
	 */
	public function getErrors() {

		return nl2br($this->_errors);
	}

	/**
	 * Should be documented
	 *
	 * @param  string $ip
	 * @return array
	 */
	public function ip2NetworkCard($ip) {

		$key = array_search($ip,$this->_interfacesInfo[2]);

		if ($key === false) {
			$this->_errors .= sprintf(
				tr("This IP (%s) is not assigned to any network card!"), $ip
			);
		} else {
			return $this->_interfacesInfo[1][$key];
		}
	}
}
