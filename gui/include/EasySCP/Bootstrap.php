<?php
/**
 * ispCP ω (OMEGA) a Virtual Hosting Control System
 *
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
 * The Original Code is "ispCP - ISP Control Panel".
 *
 * The Initial Developer of the Original Code is ispCP Team.
 * Portions created by Initial Developer are Copyright (C) 2006-2011 by
 * isp Control Panel. All Rights Reserved.
 *
 * @category	EasySCP
 * @package		EasySCP_Bootstrap
 * @copyright	2006-2011 by ispCP | http://isp-control.net
 * @author		Laurent Declercq <laurent.declercq@ispcp.net>
 * @version		SVN: $Id: Bootstrap.php 3762 2011-01-14 08:43:43Z benedikt $
 * @link		http://isp-control.net ispCP Home Site
 * @license		http://www.mozilla.org/MPL/ MPL 1.1
 */

/**
 * Defines include directory path if needed
 */
defined('INCLUDEPATH') or define('INCLUDEPATH', dirname(dirname(__FILE__)));

/**
 * Bootstrap class for EasySCP
 *
 * This class provide a very small program to boot EasySCP
 *
 * <b>Note:</b> Will be improved later
 *
 * @package		EasySCP_Bootstrap
 * @author		Laurent Declercq <laurent.declercq@ispcp.net>
 * @since		1.0.7
 * @version		1.0.4
 */
class EasySCP_Bootstrap {

	/**
	 * Boot EasySCP environment and, configuration
	 *
	 * @throws EasySCP_Exception
	 * @return void
	 */
	public static function boot() {

		if(!self::_isBooted()) {
			$boot = new self;
			$boot->_run();
		} else {
			throw new EasySCP_Exception('Error: EasySCP is already booted!');
		}
	}

	/**
	 * This class implements the Singleton Design Pattern
	 *
	 * @return void
	 */
	private function __construct() {}

	/**
	 * This class implements the Singleton Design Pattern
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Check if EasySCP is already booted
	 *
	 * @return boolean TRUE if booted, FALSE otherwise
	 */
	protected static function _isBooted() {

		return class_exists('EasySCP_Initializer', false);
	}

	/**
	 * Load the initializer and set the include_path
	 *
	 * @return void
	 */
	protected function _run() {

		$this->_loadInitializer();
		EasySCP_Initializer::run('_setIncludePath');
	}

	/**
	 * Load the initializer
	 *
	 * @return void
	 */
	protected function _loadInitializer() {

      require INCLUDEPATH . '/EasySCP/Initializer.php';
	}
}
