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
 * EasySCP Daemon DNS functions
 */

class DaemonDNS {
	/**
	 * Handles DaemonDNS requests to update DNS NotifiedSerial
	 *
	 * @param string $Input
	 * @param bool $reload
	 * @return mixed
	 */
	public static function Start($Input, $reload = true) {
		System_Daemon::debug('Starting "DaemonDNS::Start" subprocess.');

		$data = explode(" ", $Input);
		switch ($data[0]) {
			case 'domain':
				System_Daemon::debug('Starting domain update.');
				$retVal = false;
				if ($domainData = self::queryDomainDataByDomainID($data[1])) {
					$retVal = self::UpdateNotifiedSerial($domainData);
				} else {
					System_Daemon::debug('Failed to get "queryDomainDataByDomainID": ' . $data[1]);
				}
				break;
			case 'alias':
				System_Daemon::debug("Starting alias update");
				if ($aliasData = self::queryAliasDataByAliasID($data[1])) {
					$retVal = self::UpdateNotifiedSerial($aliasData);
				} else {
					System_Daemon::debug('Failed to get "queryAliasDataByDomainID": ' . $data[1]);
				}
				break;
			default:
				System_Daemon::warning("Don't know what to do with " . $data[0]);
		}

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
	public static function AddDefaultDNSEntries($domainData, $dmn_alias = false) {
		System_Daemon::debug('Starting "DaemonDNS::AddDefaultDNSEntries" subprocess.');

		$sql_param = array(
			"domain_id" => $domainData['domain_id'],
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

		$tpl_param = array();

		//Get Default DNS Entries 
		$tpl_param = array(
			'DMN_DNS_ID'			=> $dmn_dns_id,
			'DMN_NAME'				=> $dmn_name,
			'DMN_IP'				=> $dmn_ip,
			'DMN_IPV6'				=> $dmn_ipv6,
			'DEFAULT_ADMIN_ADDRESS'	=> DaemonConfig::$cfg->{'DEFAULT_ADMIN_ADDRESS'},
			'BASE_SERVER_IP'		=> DaemonConfig::$cfg->{'BASE_SERVER_IP'},
			'TIME'					=> time(),
			'SUBDMN_NAME'			=> ''
		);

		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch(EasyConfig_PATH . '/EasySCP_DNS.xml');
		$tpl = NULL;
		unset($tpl);
		$xml_data = DaemonCommon::xml_load_string($config);
		
		if ($xml_data !== null) {
			
			$sql_param = array();
			
			foreach($xml_data->DefaultDNSEntries_Domain->children() as $DNS_Entry) {
				$sql_param[] = array(
					'domain_id'		=> ($DNS_Entry->domain_id->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_id->__toString(),
					'domain_name'	=> ($DNS_Entry->domain_name->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_name->__toString(),
					'domain_type'	=> ($DNS_Entry->domain_type->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_type->__toString(),
					'domain_content'=> ($DNS_Entry->domain_content->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_content->__toString(),
					'domain_ttl'	=> ($DNS_Entry->domain_ttl->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_ttl->__toString(),
					'domain_prio'	=> ($DNS_Entry->domain_prio->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_prio->__toString()
				);
			} 

			if ($dmn_ipv6 != ''){
				foreach($xml_data->DefaultDNSEntries_Domain_IPv6->children() as $DNS_Entry) {
					$sql_param[] = array(
						'domain_id'		=> ($DNS_Entry->domain_id->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_id->__toString(),
						'domain_name'	=> ($DNS_Entry->domain_name->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_name->__toString(),
						'domain_type'	=> ($DNS_Entry->domain_type->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_type->__toString(),
						'domain_content'=> ($DNS_Entry->domain_content->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_content->__toString(),
						'domain_ttl'	=> ($DNS_Entry->domain_ttl->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_ttl->__toString(),
						'domain_prio'	=> ($DNS_Entry->domain_prio->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_prio->__toString()
					);
				} 
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
		} else {
			System_Daemon::debug('Failed "DaemonDNS::AddDefaultDNSEntries" subprocess.');
		}
			
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

		$tpl_param = array();

		//Get Default DNS Entries 
		$tpl_param = array(
			'DMN_DNS_ID'			=> $dmn_dns_id,
			'DMN_NAME'				=> $domainData['domain_name'],
			'DMN_IP'				=> $domainData['ip_number'],
			'DMN_IPV6'				=> $domainData['ip_number_v6'],
			'DEFAULT_ADMIN_ADDRESS'	=> DaemonConfig::$cfg->{'DEFAULT_ADMIN_ADDRESS'},
			'BASE_SERVER_IP'		=> DaemonConfig::$cfg->{'BASE_SERVER_IP'},
			'TIME'					=> time(),
			'SUBDMN_NAME'			=> $domainData['subdomain_name']
		);

		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch(EasyConfig_PATH . '/EasySCP_DNS.xml');
		$tpl = NULL;
		unset($tpl);
		$xml_data = DaemonCommon::xml_load_string($config);
		
		if ($xml_data !== null) {
		
			$sql_param = array();
			
			foreach($xml_data->DefaultDNSEntries_SubDomain->children() as $DNS_Entry) {
				$sql_param[] = array(
					'domain_id'		=> ($DNS_Entry->domain_id->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_id,
					'domain_name'	=> ($DNS_Entry->domain_name->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_name,
					'domain_type'	=> ($DNS_Entry->domain_type->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_type,
					'domain_content'=> ($DNS_Entry->domain_content->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_content,
					'domain_ttl'	=> ($DNS_Entry->domain_ttl->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_ttl,
					'domain_prio'	=> ($DNS_Entry->domain_prio->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_prio
				);
			} 

			if (isset($domainData['ip_number_v6']) && $domainData['ip_number_v6'] != ''){
				foreach($xml_data->DefaultDNSEntries_SubDomain_IPv6->children() as $DNS_Entry) {
					$sql_param[] = array(
						'domain_id'		=> ($DNS_Entry->domain_id->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_id->__toString(),
						'domain_name'	=> ($DNS_Entry->domain_name->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_name->__toString(),
						'domain_type'	=> ($DNS_Entry->domain_type->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_type->__toString(),
						'domain_content'=> ($DNS_Entry->domain_content->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_content->__toString(),
						'domain_ttl'	=> ($DNS_Entry->domain_ttl->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_ttl->__toString(),
						'domain_prio'	=> ($DNS_Entry->domain_prio->__toString() == 'NULL') ? NULL : $DNS_Entry->domain_prio->__toString()
					);
				} 
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

			System_Daemon::debug('Finished "DaemonDNS::AddDNSEntry" subprocess.');
		} else {
			System_Daemon::debug('Failed "DaemonDNS::AddDNSEntry" subprocess.');
		}
			
		return true;
	}

	/**
	 * Delete all DNS entries when removing a domain/alias
	 *
	 * @param int $dmn_id
	 * @param boolean $dmn_alias Domain is an Alias
	 * @return boolean
	 */
	public static function DeleteAllDomainDNSEntries($domainData, $dmn_alias = false) {
		System_Daemon::debug('Starting "DaemonDNS::DeleteDomainDNSEntries" subprocess.');

		$sql_param = array(
			"domain_id"	=>	$domainData['domain_id'],
		);

		$sql_query = self::getDeleteQuery($dmn_alias);

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		if (!$dmn_alias) {
			self::DeleteServerAlias($domainData['domain_id']);
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
			'domain_name'	=> '%' . $domainData['subdomain_name'] . '.' . $domainData['domain_name']
		);

		$sql_query = "
			DELETE FROM
				powerdns.records
			WHERE
				name LIKE :domain_name;

			";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

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
	
	/**
	 * Updates the DNS serial and notified_serial for a secondary DNS server
	 *
	 * @param array $domainData
	 * @return boolean
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

		$tpl_param = array();

		//Get Default DNS Entries 
		$tpl_param = array(
			'DMN_DNS_ID'			=> $dmn_dns_id,
			'DMN_NAME'				=> $domainData['domain_name'],
			'DEFAULT_ADMIN_ADDRESS'	=> DaemonConfig::$cfg->{'DEFAULT_ADMIN_ADDRESS'},
			'BASE_SERVER_IP'		=> DaemonConfig::$cfg->{'BASE_SERVER_IP'},
			'TIME'					=> time(),
			'DMN_IP'				=> '',
			'DMN_IPV6'				=> '',
			'SUBDMN_NAME'			=> ''
			);
			
			
		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch(EasyConfig_PATH . '/EasySCP_DNS.xml');
		$tpl = NULL;
		unset($tpl);
		$xml_data = DaemonCommon::xml_load_string($config);

		if ($xml_data !== null) {
		
			foreach($xml_data->DefaultDNSEntries_Domain->children() as $DNS_Entry) {
				if ($DNS_Entry->domain_type == 'SOA') {
					$sql_param = array(
						'domain_id'		=> ($DNS_Entry->domain_id == 'NULL') ? NULL : $DNS_Entry->domain_id,
						'domain_name'	=> ($DNS_Entry->domain_name == 'NULL') ? NULL : $DNS_Entry->domain_name,
						'domain_type'	=> ($DNS_Entry->domain_type == 'NULL') ? NULL : $DNS_Entry->domain_type,
						'domain_content'=> ($DNS_Entry->domain_content == 'NULL') ? NULL : $DNS_Entry->domain_content,
					);
					break;
				}
			} 
			
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
		} else {
			System_Daemon::debug('Failed "DaemonDNS::UpdateNotifiedSerial" subprocess.');
		}
			
		return true;
	}

	/**
	 * Returns DomainData by DomainID
	 *
	 * @param array $domainID
	 * @return $domainData
	 */
	public static function queryDomainDataByDomainID($domainID){
		$sql_param = array(
			':domain_id' => $domainID
		);

		$sql_query = "
			SELECT
				a.email,
				d.*,
				s.ip_number,
				s.ip_number_v6
			FROM
				admin a,
				domain d,
				server_ips s
			WHERE
				a.admin_id = d.domain_admin_id
			AND
				d.domain_ip_id = s.ip_id
			AND
				d.domain_id  = :domain_id;
		";
		DB::prepare($sql_query);
		$domainData = DB::execute($sql_param, true);

		return $domainData;
	}

	/**
	 * Returns aliasData by alias_id
	 *
	 * @param array $alias_id
	 * @return $aliasData
	 */
	public static function queryAliasDataByAliasID($alias_id) {
		$sql_param = array(
			':alias_id' => $alias_id
		);
		$sql_query = "
			SELECT
				   da.alias_name as domain_name,
				   da.alias_name,
				   da.status as alias_status,
				   da.alias_id,
				   da.url_forward as subdomain_url_forward,
				   da.status as domain_status,
				   a.email,
				   d.domain_name as master_domain,
				   d.domain_cgi,
				   d.domain_php,
				   d.domain_gid,
				   d.domain_uid,
				   d.domain_id,
				   d.domain_mailacc_limit,
				   d.domain_ssl,
				   da.ssl_key,
				   da.ssl_cert,
				   da.ssl_status,
				   s.ip_number,
				   s.ip_number_v6
			FROM
				domain AS d,
				server_ips AS s,
				domain_aliasses da,
				admin a
			WHERE
				da.alias_ip_id = s.ip_id
			AND
				a.admin_id = d.domain_admin_id
			AND
				da.alias_id = :alias_id
			AND
				da.domain_id = d.domain_id;
		";

		DB::prepare($sql_query);
		$aliasData = DB::execute($sql_param, true);

		return $aliasData;
	}

}
?>
