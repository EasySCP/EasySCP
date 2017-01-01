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
 * This is the primary file that should be included in all the EasySCP's setup scripts
 */

// Set default error reporting level
error_reporting(E_ALL);

// Sets to TRUE here to ensure displaying of the base core errors
// Will be overwritten during initialization process
// @see EasySCP_Initializer::_setDisplayErrors()
ini_set('display_errors', 1);

// Define path for the EasySCP include directory
define('INCLUDEPATH', dirname(__FILE__));

/**
 * Register the AutoLoader for needed Classes
 */
require_once INCLUDEPATH . '/easyscp-autoloader.php';

/**
 * Bootstrap the EasySCP setup environment
 *
 */
require_once INCLUDEPATH . '/environment.php';

// EasySCP_Registry::set('Config', $config);
// EasySCP_Registry::set('Config', '');

/**
 * Some others shared libraries
 */
 require_once '../easyscp/gui/include/layout-functions.php';
?>