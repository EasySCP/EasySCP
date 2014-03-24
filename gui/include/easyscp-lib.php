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
 * This is the primary file that should be included in all the EasySCP's user
 * levels scripts such as all scripts that live under gui/htdocs/{admin,reseller,client}
 */

// Set default error reporting level
error_reporting(E_ALL|E_STRICT);

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
spl_autoload_register('AutoLoader::loadClass');

/**
 * Exception Handler for uncaught exceptions
 *
 * Sets the exception handler for uncaught exceptions and register it in the
 * registry.
 */
EasySCP_Registry::setAlias(
	'exceptionHandler',
	EasySCP_Exception_Handler::getInstance()->setHandler()
);

/**
 * Attach the primary writer to write uncaught exceptions messages to
 * the client browser.
 *
 * The writer writes all exception messages to the client browser. In production,
 * all messages are replaced by a specific message to avoid revealing important
 * information about the EasySCP application environment if the user is not an
 * administrator.
 *
 * Another optional writers will be attached to this object during
 * initialization process.
 */
EasySCP_Registry::get('exceptionHandler')->attach(
	new EasySCP_Exception_Writer_Browser()
);

/**
 * Encryption data
 */
require_once INCLUDEPATH . '/easyscp-load-db-keys.php';

if($easyscp_db_pass_key != '{KEY}' && $easyscp_db_pass_iv != '{IV}') {
	EasySCP_Registry::set('MCRYPT_KEY', $easyscp_db_pass_key);
	EasySCP_Registry::set('MCRYPT_IV', $easyscp_db_pass_iv);
	unset($easyscp_db_pass_key, $easyscp_db_pass_iv);
} else {
	throw new EasySCP_Exception(
		'Error: Database key and/or initialization vector was not generated!'
	);
}

/**
 * Include EasySCP common functions
 */
require_once 'Net/IDNA2.php';
require_once INCLUDEPATH . '/easyscp-functions.php';

/**
 * Bootstrap the EasySCP environment, and default configuration
 *
 * @see {@link EasySCP_Bootstrap} class
 * @see {@link EasySCP_Initializer} class
 */
require_once INCLUDEPATH . '/environment.php';

/**
 * Internationalization functions
 */
require_once 'i18n.php';

/**
 * System message functions
 *
 * @deprecated Deprecated since 1.0.6 - Will be replaced by EasySCP_Exception
 */
require_once 'system-message.php';

/**
 * SQL convenience functions
 */
require_once 'sql.php';

/**
 * Authentication functions
 */
require_once 'login-functions.php';

/**
 * User level functions
 *
 * @todo: Must be refactored to be able to load only files that are needed
 */
require_once 'admin-functions.php';
require_once 'reseller-functions.php';
require_once 'client-functions.php';

/**
 * Some others shared libraries
 */
require_once 'calc-functions.php';
require_once 'date-functions.php';
require_once 'debug.php';
require_once 'emailtpl-functions.php';
require_once 'functions.order_system.php';
require_once 'functions.ticket_system.php';
require_once 'input-checks.php';
require_once 'layout-functions.php';
require_once 'lostpassword-functions.php';
?>