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

class DaemonCoreCommon {
	protected static function checkAllDataAlias(){
		$sql_query = "
			SELECT
				alias_id, status
			FROM
				domain_aliasses
			WHERE
				status
			IN
				('add', 'change', 'delete', 'disable', 'enable')
			ORDER BY
				domain_id;
		";
		foreach (DB::query($sql_query) as $row) {
			$retVal = DaemonDomain::Start('alias '.$row['alias_id'], false);
			if($retVal !== true){
				return $retVal;
			}
		}
		return true;
	}

	protected static function checkAllDataDomain(){
		$sql_query = "
			SELECT
				domain_id, status
			FROM
				domain
			WHERE
				status
			IN
				('add', 'change', 'delete', 'disable', 'enable')
			ORDER BY
				domain_id;
		";
		foreach (DB::query($sql_query) as $row) {
			$retVal = DaemonDomain::Start('domain '.$row['domain_id'], false);
			if($retVal !== true){
				return $retVal;
			}
		}
		return true;
	}

	protected static function checkAllDataHTAccess(){
		/*
		$sql_query = "
			SELECT
				d.domain_id,
				h. *
			FROM
				domain d,
				htaccess_groups h
			WHERE
				d.domain_id = h.dmn_id
			AND
				h.status
			IN
				('add', 'change', 'delete')
			ORDER BY
				dmn_id;
		";
		*/
		$sql_query = "
			SELECT
				dmn_id
			FROM
				htaccess h
			WHERE
				h.status <> 'ok'
			UNION
			SELECT
				dmn_id
			FROM
				htaccess_groups hg
			WHERE
				hg.status <> 'ok'
			UNION
			SELECT
				dmn_id
			FROM
				htaccess_users hu
			WHERE
				hu.status <> 'ok'
			ORDER BY
				dmn_id;
		";
		foreach (DB::query($sql_query) as $row) {
			$retVal = DaemonDomain::Start('htaccess '.$row['dmn_id'], false);
			if($retVal !== true){
				return $retVal;
			}
		}
		return true;
	}

	protected static function checkAllDataMail(){
		$sql_query = "
			SELECT
				domain_id, status
			FROM
				mail_users
			WHERE
				status
			IN
				('add', 'change', 'delete', 'disable', 'enable')
			ORDER BY
				domain_id;
		";
		foreach (DB::query($sql_query) as $row) {
			$retVal = DaemonMail::Start($row['domain_id']);
			if($retVal !== true){
				return $retVal;
			}
		}
		return true;
	}
}
?>