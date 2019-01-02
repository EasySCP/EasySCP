<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2019 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * Autoloader für Klassen
 */
class AutoLoader {

	/**
	 * Lädt eine benötigte Klasse automatisch, falls diese vorhanden ist.
	 *
	 * @param string $className Name der Klasse die geladen werden soll
	 */
	public static function loadClass($className) {

		$path = str_replace('_', '/', $className);

		if(file_exists(INCLUDEPATH . '/' . $path . '.php')) {
			require_once INCLUDEPATH . '/' . $path . '.php';
		}
	}
}

/**
 * Registriert den AutoLoader für benötigte Klassen (gibt es seit PHP 5 >= 5.1.2).
 */
spl_autoload_register('AutoLoader::loadClass');
?>