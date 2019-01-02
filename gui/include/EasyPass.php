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
 * EasySCP Password functions
 */

// TODO Auf neue XML Config und DB Klasse umstellen

class EasyPass {

	public static function check_udata($id, $pass){
		$sql = EasySCP_Registry::get('Db');

		$query = "
			SELECT
				`admin_id`, `admin_pass`
			FROM
				`admin`
			WHERE
				`admin_id` = ?
			AND
				`admin_pass` = ?
		";

		$rs = exec_query($sql, $query, array($id, md5($pass)));

		return (($rs->recordCount()) != 1) ? false : true;
	}
}
?>