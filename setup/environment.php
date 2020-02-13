<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2020 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * Get a reference to a EasySCP_Configuration instance
 */
$config = EasySCP_Configuration::getInstance();

/**
 * Set some basic configuration parameters
 */

// Standard Theme (if not set)
$config->USER_INITIAL_THEME = 'default';

// Template paths
$config->ROOT_TEMPLATE_PATH = 'theme/';
?>