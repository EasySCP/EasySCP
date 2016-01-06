<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * Dummy Funktionen für Standalone Betrieb
 */
class System_Daemon {

	/**
	 * Logging shortcut
	 */
	public static function debug($msg) {
		if (DaemonConfig::$cfg->{'DEBUG'} == '1'){
			echo $msg."\n";
		}
	}

	public static function info($msg) {
		echo $msg."\n";
	}
}
?>