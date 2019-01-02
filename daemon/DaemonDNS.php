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
 * EasySCP Daemon DNS functions
 */

class DaemonDNS {
	/**
	 * @param $Input
	 * @return bool
	 */
	public static function Start($Input) {
		System_Daemon::debug('Starting "DaemonDNS::Start" subprocess.');

		System_Daemon::debug('Finished "DaemonDNS::Start" subprocess.');
		return true;
	}

	/**
	 * Get UID and IP address for given domain_id
	 * @param $dmn_id
	 * @return mixed
	 * @throws Exception
	 */
	private static function getUidAndIP($dmn_id){
		$sql_param = array(
			"domain_id" => $dmn_id,
		);

		$sql_query = "
			SELECT
				d.domain_uid,
				i.ip_number
			FROM
				domain d,
				server_ips i
			WHERE
				d.domain_id = :domain_id
			AND
				d.domain_ip_id = i.ip_id;
		";

		DB::prepare($sql_query);
		$row = DB::execute($sql_param, true);

		return $row;
	}
	/**
	 * Add server Alias (vuXXXX.myserver.tld) to DNS
	 * @return bool
	 */
	public static function AddServerAlias($dmn_id){

		System_Daemon::debug('Started "DaemonDNS::AddServerAlias" subprocess.');

		$row = self::getUidAndIP($dmn_id);

		$dmn_uid = $row['domain_uid'];
		$dmn_ip = $row['ip_number'];
		$dmn_name = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $dmn_uid . "." . DaemonConfig::$cfg->BASE_SERVER_VHOST;

		$sql_param[] = array(
			'domain_id'		=> 1,
			'domain_name'	=> $dmn_name,
			'domain_type'	=> 'A',
			'domain_content'=> $dmn_ip,
			'domain_ttl'	=> '7200',
			'domain_prio'	=> NULL
		);

		$sql_query = "
			INSERT INTO
				powerdns.records (domain_id, name, type, content, ttl, prio)
			VALUES
				(:domain_id, :domain_name, :domain_type, :domain_content, :domain_ttl, :domain_prio)
			ON DUPLICATE KEY UPDATE
			 	name = :domain_name;
		";

		$stmt = DB::prepare($sql_query);

		foreach ($sql_param as $data) {
			$stmt->execute($data);
		}

		$stmt = Null;

		System_Daemon::debug('Finished "DaemonDNS::AddServerAlias" subprocess.');

		return true;
	}
	/**
	 * Adds default DNS entries when adding a domain
	 *
	 * @param int $dmn_id Domain ID
	 * @param boolean $dmn_alias Domain is an Alias
	 * @return boolean
	 */
	public static function AddDefaultDNSEntries($dmn_id, $dmn_alias = false) {
		System_Daemon::debug('Starting "DaemonDNS::AddDefaultDNSEntries" subprocess.');

		$sql_param = array(
			"domain_id" => $dmn_id,
		);

		if (!$dmn_alias){
			$easyscp_domain_id_string = "easyscp_domain_id";
			$sql_query = "
				SELECT
					d.domain_id, d.domain_name,
					i.ip_number, i.ip_number_v6 
				FROM
					domain d,
					server_ips i
				WHERE
					d.domain_id = :domain_id
				AND
					d.domain_ip_id = i.ip_id;
			";
		}  else {
			$easyscp_domain_id_string = "easyscp_domain_alias_id";
			$sql_query = "
				SELECT
					d.alias_id AS domain_id, d.alias_name AS domain_name,
					i.ip_number, i.ip_number_v6
				FROM
					domain_aliasses d,
					server_ips i
				WHERE
					d.domain_id = :domain_id
				AND
					d.alias_ip_id = i.ip_id;
			";
		}

		DB::prepare($sql_query);
		$row = DB::execute($sql_param, true);

		$dmn_id = $row['domain_id'];
		$dmn_name = $row['domain_name'];
		$dmn_ip = $row['ip_number'];
		$dmn_ipv6 = $row['ip_number_v6'];

		// Add some default DNS entries

		$sql_param = array(
			':domain_name'		=> $dmn_name,
			':easyscp_domain_id'=> $dmn_id
		);

		$sql_query = "
			INSERT INTO
				powerdns.domains ($easyscp_domain_id_string, name, type)
			VALUES
				(:easyscp_domain_id, :domain_name, 'MASTER')
			 ON DUPLICATE KEY UPDATE
			 	name = :domain_name;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		$sql_param = array(':name' => $dmn_name);
		$sql_query = "SELECT id FROM powerdns.domains WHERE name = :name;";

		DB::prepare($sql_query);
		$row = DB::execute($sql_param, true);

		$dmn_dns_id = $row['id'];

		$sql_param = array();

		$sql_param[] = array(
			'domain_id'		=> $dmn_dns_id,
			'domain_name'	=> $dmn_name,
			'domain_type'	=> 'SOA',
			'domain_content'=> 'ns1.' . $dmn_name . '. ' . DaemonConfig::$cfg->{'DEFAULT_ADMIN_ADDRESS'} . ' ' . time() . ' 12000 1800 604800 86400',
			'domain_ttl'	=> '3600',
			'domain_prio'	=> Null
		);

		$sql_param[] = array(
			'domain_id'		=> $dmn_dns_id,
			'domain_name'	=> 'ns1.' . $dmn_name,
			'domain_type'	=> 'A',
			'domain_content'=> $dmn_ip,
			'domain_ttl'	=> '7200',
			'domain_prio'	=> NULL
		);

		$sql_param[] = array(
			'domain_id'		=> $dmn_dns_id,
			'domain_name'	=> $dmn_name,
			'domain_type'	=> 'NS',
			'domain_content'=> 'ns1.' . $dmn_name,
			'domain_ttl'	=> '28800',
			'domain_prio'	=> NULL
		);

		$sql_param[] = array(
			'domain_id'		=> $dmn_dns_id,
			'domain_name'	=> 'ns.' . $dmn_name,
			'domain_type'	=> 'CNAME',
			'domain_content'=> 'ns1.' . $dmn_name,
			'domain_ttl'	=> '7200',
			'domain_prio'	=> NULL
		);

		$sql_param[] = array(
			'domain_id'		=> $dmn_dns_id,
			'domain_name'	=> 'mail.' . $dmn_name,
			'domain_type'	=> 'A',
			'domain_content'=> $dmn_ip,
			'domain_ttl'	=> '7200',
			'domain_prio'	=> NULL
		);

		$sql_param[] = array(
			'domain_id'		=> $dmn_dns_id,
			'domain_name'	=> $dmn_name,
			'domain_type'	=> 'MX',
			'domain_content'=> 'mail.' . $dmn_name,
			'domain_ttl'	=> '7200',
			'domain_prio'	=> '10'
		);

		$sql_param[] = array(
			'domain_id'		=> $dmn_dns_id,
			'domain_name'	=> $dmn_name,
			'domain_type'	=> 'A',
			'domain_content'=> $dmn_ip,
			'domain_ttl'	=> '7200',
			'domain_prio'	=> NULL
		);

		$sql_param[] = array(
			'domain_id'		=> $dmn_dns_id,
			'domain_name'	=> 'www.' . $dmn_name,
			'domain_type'	=> 'A',
			'domain_content'=> $dmn_ip,
			'domain_ttl'	=> '7200',
			'domain_prio'	=> NULL
		);

		$sql_param[] = array(
			'domain_id'		=> $dmn_dns_id,
			'domain_name'	=> 'ftp.' . $dmn_name,
			'domain_type'	=> 'CNAME',
			'domain_content'=> 'www.' . $dmn_name,
			'domain_ttl'	=> '7200',
			'domain_prio'	=> NULL
		);

		$sql_param[] = array(
			'domain_id'		=> $dmn_dns_id,
			'domain_name'	=> 'webmail.' . $dmn_name,
			'domain_type'	=> 'CNAME',
			'domain_content'=> 'www.' . $dmn_name,
			'domain_ttl'	=> '7200',
			'domain_prio'	=> NULL
		);

		if ($dmn_ipv6 != ''){
			$sql_param[] = array(
					'domain_id'		=> $dmn_dns_id,
					'domain_name'	=> 'ns1.' . $dmn_name,
					'domain_type'	=> 'AAAA',
					'domain_content'=> $dmn_ipv6,
					'domain_ttl'	=> '7200',
					'domain_prio'	=> NULL
			);

			$sql_param[] = array(
					'domain_id'		=> $dmn_dns_id,
					'domain_name'	=> 'mail.' . $dmn_name,
					'domain_type'	=> 'AAAA',
					'domain_content'=> $dmn_ipv6,
					'domain_ttl'	=> '7200',
					'domain_prio'	=> NULL
			);

			$sql_param[] = array(
					'domain_id'		=> $dmn_dns_id,
					'domain_name'	=> $dmn_name,
					'domain_type'	=> 'AAAA',
					'domain_content'=> $dmn_ipv6,
					'domain_ttl'	=> '7200',
					'domain_prio'	=> NULL
			);

			$sql_param[] = array(
					'domain_id'		=> $dmn_dns_id,
					'domain_name'	=> 'www.' . $dmn_name,
					'domain_type'	=> 'AAAA',
					'domain_content'=> $dmn_ipv6,
					'domain_ttl'	=> '7200',
					'domain_prio'	=> NULL
			);
		}


		$sql_query = "
			INSERT INTO
				powerdns.records (domain_id, name, type, content, ttl, prio)
			VALUES
				(:domain_id, :domain_name, :domain_type, :domain_content, :domain_ttl, :domain_prio)
			ON DUPLICATE KEY UPDATE
			 	name = :domain_name;
		";

		$stmt = DB::prepare($sql_query);

		foreach ($sql_param as $data) {
			$stmt->execute($data);
		}

		$stmt = Null;
		unset($stmt);

		if (!$dmn_alias) {
			self::AddServerAlias($dmn_id);
		}

		System_Daemon::debug('Finished "DaemonDNS::AddDefaultDNSEntries" subprocess.');

		return true;
	}
	/**
	 * Updates the DNS serial and notified_serial for a secondary DNS server
	 */
	public static function UpdateNotifiedSerial($domainData) {
		System_Daemon::debug('Starting "DaemonDNS::UpdateNotifiedSerial" subprocess.');

		$sql_param = array(
			':name' => $domainData['domain_name']
		);

		$sql_query = "
			SELECT
				id
			FROM
				powerdns.domains
			WHERE
				name = :name;
		";

		DB::prepare($sql_query);
		$row = DB::execute($sql_param, true);

		$dmn_dns_id = $row['id'];

		$sql_param = array(
            'domain_content'	=> 'ns1.' . $domainData['domain_name'] . '. ' . DaemonConfig::$cfg->{'DEFAULT_ADMIN_ADDRESS'} . ' ' . time() . ' 12000 1800 604800 86400',
			'domain_id'			=> $dmn_dns_id,
			'domain_name'		=> $domainData['domain_name'],
            'domain_type'		=> 'SOA'
		);
		
		$sql_query = "
			UPDATE
				powerdns.records
			SET
				content = :domain_content
			WHERE
				domain_id = :domain_id
			AND
				name = :domain_name
			AND
				type = :domain_type;
			";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		System_Daemon::debug('Finished "DaemonDNS::UpdateNotifiedSerial" subprocess.');

		return true;
	}

	/**
	 * Adds DNS entry when adding a suddomain
	 *
	 * @param array $domainData
	 * @return boolean
	 */
	public static function AddDNSEntry($domainData) {
		System_Daemon::debug('Starting "DaemonDNS::AddDNSEntry" subprocess.');

		$sql_param = array(
			':name' => $domainData['domain_name']
		);

		$sql_query = "
			SELECT
				id
			FROM
				powerdns.domains
			WHERE
				name = :name;
		";

		DB::prepare($sql_query);
		$row = DB::execute($sql_param, true);

		$dmn_dns_id = $row['id'];

		$sql_param = array();

		$sql_param[] = array(
			'domain_id'		=> $dmn_dns_id,
			'domain_name'	=> $domainData['subdomain_name'] . '.' . $domainData['domain_name'],
			'domain_type'	=> 'A',
			'domain_content'=> $domainData['ip_number'],
			'domain_ttl'	=> '7200',
			'domain_prio'	=> NULL
		);

		if (isset($domainData['ip_number_v6']) && $domainData['ip_number_v6'] != ''){
			$sql_param[] = array(
					'domain_id'		=> $dmn_dns_id,
					'domain_name'	=> $domainData['subdomain_name'] . '.' . $domainData['domain_name'],
					'domain_type'	=> 'AAAA',
					'domain_content'=> $domainData['ip_number_v6'],
					'domain_ttl'	=> '7200',
					'domain_prio'	=> NULL
			);
		}

		$sql_query = "
			INSERT INTO
				powerdns.records (domain_id, name, type, content, ttl, prio)
			VALUES
				(:domain_id, :domain_name, :domain_type, :domain_content, :domain_ttl, :domain_prio)
			ON DUPLICATE KEY UPDATE
			 	name = :domain_name;
			";

		$stmt = DB::prepare($sql_query);

		foreach ($sql_param as $data) {
			$stmt->execute($data);
		}

		$stmt = Null;
		unset($stmt);

		// Update the DNS serial
		self::UpdateNotifiedSerial($domainData);
		
		System_Daemon::debug('Finished "DaemonDNS::AddDNSEntry" subprocess.');

		return true;
	}

	/**
	 * Delete all DNS entries when removing a domain/alias
	 *
	 * @param int $dmn_id
	 * @param boolean $dmn_alias Domain is an Alias
	 * @return boolean
	 */
	public static function DeleteAllDomainDNSEntries($dmn_id, $dmn_alias = false) {
		System_Daemon::debug('Starting "DaemonDNS::DeleteDomainDNSEntries" subprocess.');

		$sql_param = array(
			"domain_id"	=>	$dmn_id,
		);

		$sql_query = self::getDeleteQuery($dmn_alias);

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		if (!$dmn_alias) {
			self::DeleteServerAlias($dmn_id);
		}

		System_Daemon::debug('Finished "DaemonDNS::DeleteDomainDNSEntries" subprocess.');

		return true;
	}

	/**
	 * Generating query to delete all domains/aliases from DNS.
	 * @param $dmn_alias
	 * @return string
	 */
	private static function getDeleteQuery($dmn_alias){
		if (!$dmn_alias){
			$id_string = "easyscp_domain_id";
		}  else {
			$id_string = "easyscp_domain_alias_id";
		}
		switch(DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'}){
			case "CentOS_6":
				// This query generates error on Debian 8
				$sql_query = "
					DELETE
						powerdns.domains.*,
						powerdns.records.*
					FROM
						powerdns.domains AS domains
					LEFT JOIN
						powerdns.records AS records ON domains.id = records.domain_id
					WHERE
						domains.$id_string = :domain_id;
				";
				break;
			default:
				// This query does not work on CentOS 6
				$sql_query = "
					DELETE
						domains.*,
						records.*
					FROM
						powerdns.domains AS domains
					LEFT JOIN
						powerdns.records AS records ON domains.id = records.domain_id
					WHERE
						domains.$id_string = :domain_id;
				";
		}
		return $sql_query;
	}

	/**
	 * Delete DNS entry when removing a suddomain
	 *
	 * @param array $domainData
	 * @return boolean
	 */
	public static function DeleteDNSEntry($domainData) {
		System_Daemon::debug('Starting "DaemonDNS::DeleteDNSEntry" subprocess.');

		$sql_param = array(
			'domain_name'	=> $domainData['subdomain_name'] . '.' . $domainData['domain_name']
		);

		$sql_query = "
			DELETE FROM
				powerdns.records
			WHERE
				name = :domain_name;

			";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		// Update the DNS serial
		self::UpdateNotifiedSerial($domainData);
		
		System_Daemon::debug('Finished "DaemonDNS::DeleteDNSEntry" subprocess.');

		return true;
	}

	/**
	 * Delete server alias (vuXXXX.myserver.tld) from DNS
	 * @param $dmn_id
	 * @return bool
	 */
	public static function DeleteServerAlias($dmn_id){

		$row = self::getUidAndIP($dmn_id);

		System_Daemon::debug("Domain ID: ".$dmn_id);
		$domainData = array(
			'subdomain_name'	=> DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $row['domain_uid'],
			'domain_name'		=> DaemonConfig::$cfg->BASE_SERVER_VHOST
		);
		System_Daemon::debug('Trying to delete name:' .	$domainData['subdomain_name'] . '.' . $domainData['domain_name']);

		return self::DeleteDNSEntry($domainData);
	}
}
?>
