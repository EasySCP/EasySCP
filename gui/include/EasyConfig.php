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
 * EasySCP Config functions
 */

class EasyConfig {
	static $cfg;

	public static function Reload(){
		self::$cfg = simplexml_load_file(EasyConfig_PATH . '/EasySCP_Config.xml');
	}

	/**
	 * Speichern der Konfiguration
	 *
	 * @param $data array Daten die gespeichert werden sollen
	 * @return bool Gibt bei erfolg true, ansonsten false zurück
	 */
	public static function Save($data){
		return send_request('100 CORE SaveConfig '.json_encode($data));
	}
}

EasyConfig::$cfg = simplexml_load_file(EasyConfig_PATH . '/EasySCP_Config.xml');
?>