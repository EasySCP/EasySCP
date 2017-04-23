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

class DaemonDomain extends DaemonDomainCommon {
	/**
	 * Handles DaemonDomain requests to create, modify or delete domains.
	 * Includes the creation of virtual users for EasySCP.
	 *
	 * @param string $Input
	 * @param bool $reload
	 * @return mixed
	 */
	public static function Start($Input, $reload = true) {
		System_Daemon::debug('Starting "DaemonDomain::Start" subprocess.');

		$data = explode(" ", $Input);
		switch ($data[0]) {
			case 'domain':
				System_Daemon::debug('Starting domain.');
				$retVal = false;
				if ($domainData = self::queryDomainDataByDomainID($data[1])) {
					$retVal = self::handleDomain($domainData);
					if ($retVal !== true){
						$msg = 'Handling of domain '.$domainData['domain_name'].' failed!';
						System_Daemon::debug($msg);
						return $msg . '<br />' . $retVal;
					}
					if ($subDomainData = self::querySubDomainDataByDomainID($domainData['domain_id'])) {
						foreach ($subDomainData->fetchAll() as $row){
							$retVal = self::handleSubDomain($row);
							if ($retVal !== true){
								$msg = 'Handling of subdomain '.$domainData['subdomain_name'].'.'.$domainData['domain_name'].' failed!';
								System_Daemon::debug($msg);
								return $msg . '<br />' . $retVal;
							}
						}
					}
				} else {
					System_Daemon::debug('Failed to get "queryDomainDataByDomainID": ' . $data[1]);
				}
				if ($retVal === true){
					System_Daemon::debug("Set Domain status");
					if($domainData['status']=='disable'){
						$retVal = self::dbSetDomainStatus('disabled', $domainData['domain_id']);
					} else {
						$retVal = self::dbSetDomainStatus('ok', $domainData['domain_id']);
					}
					if ($retVal !== true) {
						$msg = 'Setting Domain status failed';
						System_Daemon::debug($msg);
						return $msg . '<br />' . $retVal;
					}
				}
				break;
			case 'alias':
				System_Daemon::debug("Starting alias");
				if ($aliasData = self::queryAliasDataByAliasID($data[1])) {
					$retVal = self::handleAlias($aliasData);
					if ($retVal !== true){
						$msg = 'Handling of alias '.$aliasData['alias_name'].' failed!';
						System_Daemon::debug($msg);
						return $msg . '<br />' . $retVal;
					}
					if ($subDomainAliasData = self::queryAliasSubDomainDataByAliasID($aliasData['alias_id'])) {
						while ($row = $subDomainAliasData->fetch()) {
							$retVal = self::handleSubDomainAlias($row);
							if ($retVal !== true){
								$msg = 'Handling of subdomain '.$row['subdomain_alias_name'].'.'.$row['alias_name'].' failed!';
								System_Daemon::debug($msg);
								return $msg . '<br />' . $retVal;
							}
						}
					}
				}
				break;
			case 'htaccess':
				if ($domainData = self::queryHTAccessData($data[1])) {
					$retVal = self::handleHTAccess($domainData);
					if ($retVal !== true){
						$msg = 'Handling of htaccess for '.$domainData['domain_name'].' failed!';
						System_Daemon::debug($msg);
						return $msg . '<br />' . $retVal;
					}
				}
				break;
			case 'reload':
				break;
			case 'master':
				$retVal = self::writeMasterConfig();
				if ($retVal !== true){
					$msg = 'Handling of master configuration failed!';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			default:
				System_Daemon::warning("Don't know what to do with " . $data[0]);
		}

		if($reload === true){
			$retVal = self::apacheReloadConfig();
			if($retVal !== true){
				$msg = 'Reload apache config failed';
				System_Daemon::debug($msg);
				return $msg . '<br />' . ((DaemonConfig::$cfg->{'DEBUG'} == '1') ? DaemonCommon::listArrayforGUI($retVal) : '');
			}
		}

		System_Daemon::debug('Finished "DaemonDomain::Start" subprocess.');

		return true;
	}

	protected static function handleAlias($aliasData) {
		System_Daemon::debug('Starting "DaemonDomain::handleAlias = ' . $aliasData['alias_name'] . '" subprocess.');

		switch ($aliasData['alias_status']) {
			case 'add':
				$retVal = self::apacheWriteDomainConfig($aliasData);
				if ($retVal !== true) {
					$msg = 'Writing alias configuration failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = DaemonDNS::AddDefaultDNSEntries($aliasData['domain_id'], true);
				if ($retVal !== true) {
					$msg = 'Creating of default domain dns entries failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = self::apacheEnableSite($aliasData['alias_name']);
				if ($retVal !== true) {
					$msg = 'Failed to enable alias!';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				break;
			case 'change':
				$retVal = self::apacheWriteDomainConfig($aliasData);
				if ($retVal !== true) {
					$msg = 'Writing alias configuration failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			case 'delete':
				$retVal = self::apacheDisableSite($aliasData['alias_name']);
				if ($retVal !== true) {
					$msg = 'Failed to disable alias!';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				$retVal = self::deleteAlias($aliasData);
				if ($retVal !== true) {
					$msg = 'Failed to delete alias!';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				$retVal = DaemonDNS::DeleteAllDomainDNSEntries($aliasData['alias_id'], true);
				if ($retVal !== true) {
					$msg = 'Deleting alias DNS entries failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			case 'disable':
				$retVal = self::apacheDisableSite($aliasData['alias_name']);
				if ($retVal !== true) {
					$msg = 'Disabling alias failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			case 'enable':
				$retVal = self::apacheEnableSite($aliasData['alias_name']);
				if ($retVal !== true) {
					$msg = 'Enabling alias failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			case 'ok':
				// Configuration has to be rewritten every time to ensure that
				// all domains and subdomains are completely configured
				$retVal = self::apacheWriteDomainConfig($aliasData);
				if ($retVal !== true) {
					$msg = 'Writing alias configuration failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			default:
				$msg = 'Don\'t know what to do with ' . $aliasData['status'] . ' (handleAlias)';
				System_Daemon::warning($msg);
				return $msg . '<br />';
		}

		if ($retVal === true) {
			if($aliasData['alias_status']=='disable'){
				$retVal = self::dbSetAliasStatus('disabled', $aliasData['alias_id']);
			} else {
				$retVal = self::dbSetAliasStatus('ok', $aliasData['alias_id']);
			}
			if ($retVal !== true) {
				$msg = 'Setting Alias status failed';
				System_Daemon::debug($msg);
				return $msg . '<br />' . $retVal;
			}
		}

		System_Daemon::debug('Finished "DaemonDomain::handleAlias = ' . $aliasData['alias_name'] . '" subprocess.');

		return true;
	}

	/**
	 * Handles DaemonDomain requests to create, modify or delete domains.
	 * Includes the creation of virtual users for EasySCP.
	 *
	 * @param array $domainData
	 * @return mixed
	 */
	protected static function handleDomain($domainData) {
		System_Daemon::debug('Starting "DaemonDomain::handleDomain = ' . $domainData['domain_name'] . '" subprocess.');

		switch ($domainData['status']) {
			case 'add':
				$retVal = self::createDomain($domainData);
				if ($retVal !== true) {
					$msg = 'Creation of domain failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = self::apacheWriteDomainConfig($domainData);
				if ($retVal !== true) {
					$msg = 'Writing domain configuration failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = DaemonDNS::AddDefaultDNSEntries($domainData['domain_id']);
				if ($retVal !== true) {
					$msg = 'Creating of default domain dns entries failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = self::handleHTAccess($domainData);
				if ($retVal !== true) {
					$msg = 'Creating of htaccess data failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = self::apacheEnableSite($domainData['domain_name']);
				if ($retVal !== true) {
					$msg = 'Enabling domain failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				break;
			case 'change':
				$retVal = self::apacheWriteDomainConfig($domainData);
				if ($retVal !== true) {
					$msg = 'Writing domain configuration failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = self::handleHTAccess($domainData);
				if ($retVal !== true) {
					$msg = 'Creating of htaccess data failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				break;
			case 'delete':
				$retVal = self::apacheDisableSite($domainData['domain_name']);
				if ($retVal !== true) {
					$msg = 'Disabling domain failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				// Needs to be before deletedomain because DNS entry for vuXXXX must be deleted as well
				$retVal = DaemonDNS::DeleteAllDomainDNSEntries($domainData['domain_id']);
				if ($retVal !== true) {
					$msg = 'Deleting of domain dns entries failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = self::deleteDomain($domainData);
				if ($retVal !== true) {
					$msg = 'Deleting domain failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			case 'disable':
				/**
				$retVal = self::apacheWriteDisabledSiteConfig($domainData);
				if ($retVal !== true) {
				$msg = 'Writing domain configuration failed';
				System_Daemon::debug($msg);
				return $msg . '<br />' . $retVal;
				}
				 */
				$retVal = self::apacheDisableSite($domainData['domain_name']);
				if ($retVal !== true) {
					$msg = 'Disabling domain failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				$retVal = self::apacheEnableDisabledSite($domainData['domain_name']);
				if ($retVal !== true) {
					$msg = 'Enabling disabled site of domain failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			case 'enable':
				$retVal = self::apacheEnableSite($domainData['domain_name']);
				if ($retVal !== true) {
					$msg = 'Enabling domain failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				$retVal = self::apacheDisableDisabledSite($domainData['domain_name']);
				if ($retVal !== true) {
					$msg = 'Disabling disabled site of domain failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			case 'ok':
				// Configuration has to be rewritten every time to ensure that
				// all domains and subdomains are completely configured
				$retVal = self::apacheWriteDomainConfig($domainData);
				if ($retVal !== true) {
					$msg = 'Writing domain configuration failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			default:
				$msg = 'Don\'t know what to do with ' . $domainData['status'] . ' (handleDomain)';
				System_Daemon::warning($msg);
				return $msg . '<br />';
		}

		System_Daemon::debug('Finished "DaemonDomain::handleDomain = ' . $domainData['domain_name'] . '" subprocess.');

		return true;
	}

	protected static function handleHTAccess($domainData) {
		System_Daemon::debug('Starting "DaemonDomain::handleHTAccess = ' . $domainData['domain_name'] . '" subprocess.');

		$retVal = self::writeHTAccessUser($domainData);
		if ($retVal !== true) {
			$msg = 'Writing htaccess users failed';
			System_Daemon::debug($msg);
			return $msg . '<br />' . $retVal;
		}

		$retVal = self::writeHTAccessGroup($domainData);
		if ($retVal !== true) {
			$msg = 'Writing htaccess groups failed';
			System_Daemon::debug($msg);
			return $msg . '<br />' . $retVal;
		}

		$sql_param = array(
			':domain_id' => $domainData['domain_id']
		);

		$sql_query = "
			SELECT
				*
			FROM
				htaccess
			WHERE
				dmn_id = :domain_id
		";

		DB::prepare($sql_query);
		foreach (DB::execute($sql_param) as $row) {
			switch ($row['status']) {
				case 'add':
					$retVal = self::writeHTAccessFile($domainData, $row);
					if ($retVal !== true) {
						$msg = 'Adding htaccess file failed';
						System_Daemon::debug($msg);
						return $msg . '<br />' . $retVal;
					}
					break;
				case 'change':
					$retVal = self::writeHTAccessFile($domainData, $row);
					if ($retVal !== true) {
						$msg = 'Change htaccess data failed';
						System_Daemon::debug($msg);
						return $msg . '<br />' . $retVal;
					}
					break;
				case 'delete':
					$retVal = self::deleteHTAccessFile($domainData, $row);
					if ($retVal !== true) {
						$msg = 'Deleting htaccess file failed';
						System_Daemon::debug($msg);
						return $msg . '<br />' . $retVal;
					}
					break;
				default:
					System_Daemon::warning("Don't know what to do with " . $row['status']);
			}
		}

		System_Daemon::debug('Finished "DaemonDomain::handleHTAccess = ' . $domainData['domain_name'] . '" subprocess.');

		return true;
	}

	protected static function handleSubDomain($subDomainData) {
		System_Daemon::debug('Starting "DaemonDomain::handleSubDomain = ' . $subDomainData['subdomain_name'] . '" subprocess.');

		System_Daemon::debug("HandleSubDomain ");
		switch ($subDomainData['subdomain_status']) {
			case 'add':
				$retVal = self::createDomain($subDomainData);
				if ($retVal !== true) {
					$msg = 'Creation of subdomain failed!';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = self::apacheWriteDomainConfig($subDomainData);
				if ($retVal !== true) {
					$msg = 'Writing subdomain configuration failed!';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = DaemonDNS::AddDNSEntry($subDomainData);
				if ($retVal !== true) {
					$msg = 'Creating of subdomain dns entry failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				break;
			case 'change':
				$retVal = self::apacheWriteDomainConfig($subDomainData);
				if ($retVal !== true) {
					$msg = 'Writing subdomain configuration failed!';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				//			$retVal = domainChange($domainData);
				break;
			case 'delete':
				System_Daemon::debug("Delete subdomain");
				$retVal = self::deleteSubDomain($subDomainData);
				if ($retVal !== true) {
					$msg = 'Deleting subdomain failed!';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = DaemonDNS::DeleteDNSEntry($subDomainData);
				if ($retVal !== true) {
					$msg = 'Deleting of subdomain dns entry failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			case 'ok':
				// Configuration has to be rewritten every time to ensure that
				// all domains and subdomains are completely configured
				$retVal = self::apacheWriteDomainConfig($subDomainData);
				if ($retVal !== true) {
					$msg = 'Writing subdomain configuration failed!';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			default:
				$msg = "Don't know what to do with " . $subDomainData['status'] . ' (handlesubdomain)';
				System_Daemon::warning($msg);
				return $msg . '<br />';
				break;
		}
		if ($retVal === true) {
			System_Daemon::debug("Set SubDomain status");
			$retVal = self::dbSetSubDomainStatus('ok', $subDomainData['subdomain_id']);
			if ($retVal !== true) {
				$msg = 'Setting status to ok for subdomain with ID: '. $subDomainData['subdomain_id'] .' failed!';
				System_Daemon::debug($msg);
				return $msg . '<br />' . $retVal;
			}
		}
		return true;
	}

	protected static function handleSubDomainAlias($subDomainAliasData) {
		System_Daemon::debug('Starting "DaemonDomain::handleSubDomainAlias = ' . $subDomainAliasData['subdomain_name'].'.'.$subDomainAliasData['alias_name'] . '" subprocess.');

		switch ($subDomainAliasData['alias_status']) {
			case 'add':
				$retVal = self::apacheWriteDomainConfig($subDomainAliasData);
				if ($retVal !== true) {
					$msg = 'Writing subdomain-alias configuration failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = DaemonDNS::AddDNSEntry($subDomainAliasData);
				if ($retVal !== true) {
					$msg = 'Creating of subdomain-alias dns entry failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			case 'change':
				$retVal = self::apacheWriteDomainConfig($subDomainAliasData);
				if ($retVal !== true) {
					$msg = 'Writing subdomain-alias configuration failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			case 'delete':
				$retVal = self::deleteAliasSubDomain($subDomainAliasData);
				if ($retVal !== true) {
					$msg = 'Deleting of subdomain-alias failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				$retVal = DaemonDNS::DeleteDNSEntry($subDomainAliasData);
				if ($retVal !== true) {
					$msg = 'Deleting of subdomain-alias dns entry failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}

				break;
			case 'ok':
				// Configuration has to be rewritten every time to ensure that
				// all domains and subdomains are completely configured
				$retVal = self::apacheWriteDomainConfig($subDomainAliasData);
				if ($retVal !== true) {
					$msg = 'Writing subdomain-alias configuration failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
				break;
			default:
				$msg = "Don't know what to do with " . $subDomainAliasData['status'] . " (handlesubdomainalias)";
				System_Daemon::warning($msg);
				return $msg . '<br />';
				break;
		}
		if ($retVal === true) {
			$retVal = self::dbSetAliasSubDomainStatus('ok', $subDomainAliasData['subdomain_alias_id']);
			if ($retVal !== true) {
				$msg = 'Setting Aliassubdomain status failed';
				System_Daemon::debug($msg);
				return $msg . '<br />' . $retVal;
			}
		}
		return true;
	}
}
?>