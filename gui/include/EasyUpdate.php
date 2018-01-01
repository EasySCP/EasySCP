<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2018 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * EasySCP Update functions
 */

class EasyUpdate {
	/**
	 * Prüfen ob neue Version vorhanden ist
	 * @param $check bool
	 * @return bool Liefert bei erfolg bzw. vorhandenem Update true, ansonsten false zurück
	 */
	public static function checkUpdate($check = false){
		$last_update = 'http://www.easyscp.net/checkUpdate/'.EasyConfig::$cfg->Version.'/'.EasyConfig::$cfg->BuildDate.'/';
		ini_set('user_agent', 'EasySCP/'.EasyConfig::$cfg->Version);

		$last_update_result = file_get_contents($last_update);

		if($last_update_result > EasyConfig::$cfg->BuildDate){
			$check = true;
		}

		return $check;
	}

	/**
	 * Prüfen ob Update gültig ist
	 * @param $check bool
	 * @return bool Liefert bei erfolg bzw. gültigem Update true, ansonsten false zurück
	 */
	public static function verifyUpdate($check = false){

		return $check;
	}
}
?>