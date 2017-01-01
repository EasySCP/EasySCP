<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2017 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link        http://www.easyscp.net
 * @author        EasySCP Team
 */

/**
 * SSL
 */
class EasySSL
{
	/**
	 * Verify if SSL key and certificate match
	 * @param $key
	 * @param $cert
	 * @return bool
	 */
	public static function checkSSLKey($key, $cert){
		if (openssl_x509_check_private_key(clean_input($cert), clean_input($key))) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Query for domain names from domain, subdomain, alias and subdomain alias tables.
	 * @param $domain_id
	 * @return mixed
	 * @throws Exception
	 */
	public static function getDomainNames($domain_id){
		$sql_param = array(
			"domain_id"	=> $domain_id
		);
		$sql_query = "
			SELECT
				CONCAT_WS(';',
					'd',
					domain_id,
					domain_id)
				AS id, domain_name
			FROM
				domain
			WHERE
				domain_id = :domain_id
			AND
				domain_ssl = 'yes'
			UNION SELECT
				CONCAT_WS(';',
					'a',
					da.alias_id,
					da.alias_id),
				alias_name AS domain_name
			FROM
				domain as d,
				domain_aliasses as da
			WHERE
				d.domain_id=da.domain_id
			AND
				d.domain_id = :domain_id
			AND
				d.domain_ssl = 'yes'
			UNION SELECT
				CONCAT_WS(';','sd',
					subdomain_id,
					d.domain_id),
				CONCAT(`subdomain_name`, '.', `domain_name`)
			FROM
				domain AS d,
				subdomain AS s
			WHERE
				d.domain_id = s.domain_id
			AND
				d.domain_id = :domain_id
			AND
				d.domain_ssl = 'yes'
			UNION SELECT
				CONCAT_WS(';','sa',
					subdomain_alias_id,
					da.alias_id),
				CONCAT(`subdomain_alias_name`,
					'.',
					`alias_name`)
			FROM
				domain AS d,
				domain_aliasses AS da,
				subdomain_alias AS sa
			WHERE
				da.alias_id = sa.alias_id
			AND
				d.domain_id = da.domain_id
			AND
				da.domain_id = :domain_id
			AND
				d.domain_ssl = 'yes';
		";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param);
		return $rs;
	}

	/**
	 * Get SSL data for domain, subdomain, alias or subdomain alias
	 * @param $sslDomain
	 * @return mixed
	 * @throws Exception
	 */
	public static function getSSLData($sslDomain){
		$domain = explode(';',$sslDomain);
		$tableData = self::getTableData($domain);
		$table = $tableData['table'];
		$column = $tableData['id_column'];
		$sql_param = array(
			"domain_id"	=> $domain[1]
		);
		$sql_query = "
			SELECT
				ssl_status,
				ssl_key,
				ssl_cert,
				ssl_cacert
			FROM
				$table
			WHERE
				$column = :domain_id;
		";
		DB::prepare($sql_query);
		$rs = DB::execute($sql_param,true);
		return $rs;
	}

	/**
	 * Get Table and column names
	 * @param $domain
	 * @return array
	 */
	private static function getTableData($domain){
		switch ($domain[0]){
			case 'd':
				$tableData = array(
					'table'		=> 'domain',
					'id_column'	=> 'domain_id',
					'type'		=> 'domain'
				);
				break;
			case 'sd':
				$tableData = array(
					'table'		=> 'subdomain',
					'id_column'	=> 'subdomain_id',
					'type'		=> 'domain'
				);
				break;
			case 'a':
				$tableData = array(
					'table'		=> 'domain_aliasses',
					'id_column'	=> 'alias_id',
					'type'		=> 'alias'
				);
				break;
			case 'sa':
				$tableData = array(
					'table'		=> 'subdomain_alias',
					'id_column'	=> 'subdomain_alias_id',
					'type'		=> 'alias'
				);
				break;
			default:
				$tableData = array(
					'table'		=> '',
					'id_column'	=> '',
					'type'		=> ''
				);
		}
		return $tableData;
	}

	/**
	 * @param $sslDomain
	 * @param $status
	 * @param $key
	 * @param $cert
	 * @param $cacert
	 * @return bool
	 * @throws Exception
	 */
	public static function storeSSLData($sslDomain, $status, $key, $cert, $cacert){
		if (self::checkSSLKey($key, $cert)===true){
			$domain = explode(';',$sslDomain);
			$tableData = self::getTableData($domain);
			$table = $tableData['table'];
			$idColumn = $tableData['id_column'];
			$sql_param = array(
				"ssl_cert"	=> $cert,
				"ssl_cacert"=> $cacert,
				"ssl_key"	=> $key,
				"ssl_status"=> $status,
				"domain_id"	=> $domain[1]
			);
			$sql_query = "
				UPDATE
					$table
				SET
					ssl_cert	= :ssl_cert,
					ssl_cacert	= :ssl_cacert,
					ssl_key		= :ssl_key,
					ssl_status	= :ssl_status,
					status		= 'change'
				WHERE
					(ssl_cert <> :ssl_cert OR ssl_cacert <> :ssl_cacert OR ssl_key <> :ssl_key OR ssl_status <> :ssl_status)
				AND
					$idColumn	= :domain_id;
			";
			DB::prepare($sql_query);
			$rs = DB::execute($sql_param);

			if ($rs->rowCount() > 0) {
				send_request('110 DOMAIN ' .$tableData['type'] . ' ' . $domain[2]);
			}
			return $rs;
		} else {
			return false;
		}

	}
}