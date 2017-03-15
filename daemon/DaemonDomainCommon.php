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

class DaemonDomainCommon {

	protected static function apacheDisableDisabledSite($siteName) {
		return self::apacheDisableSite($siteName."-disabled");
	}

	/**
	 * Disable a site in Apache web-server
	 *
	 * @param string $siteName
	 * @return mixed
	 */
	protected static function apacheDisableSite($siteName) {
		System_Daemon::debug('Disabling '.$siteName);
		$confFile = DaemonConfig::$distro->APACHE_SITES_DIR . '/' . $siteName . '.conf';

		if (file_exists($confFile)) {
			switch(DaemonConfig::$cfg->DistName){
				case 'CentOS':
					$command = DaemonConfig::$cmd->CMD_MV.' -f ' . $confFile .' ' . $confFile . '.disabled';
					break;
				default:
					$command = DaemonConfig::$cmd->CMD_HTTPD_A2DISSITE . ' ' . $siteName . '.conf 1>&1 2>&1';
			}

			System_Daemon::debug('Doing: ' . $command);
			exec($command, $result, $error);

			if ($error == 0){
				System_Daemon::debug('Disabled '.$siteName);
			} else {
				$msg = 'Failed to disable '.$siteName;
				System_Daemon::debug($msg);
				System_Daemon::debug(DaemonCommon::listArrayforLOG($result));
				return $msg . '<br />' . ((DaemonConfig::$cfg->DEBUG == '1') ? DaemonCommon::listArrayforGUI($result) : '');
			}
		}

		return true;
	}

	protected static function apacheEnableDisabledSite($siteName) {
		return self::apacheEnableSite($siteName."-disabled");
	}

	/**
	 * Enable a site in Apache web-server
	 *
	 * @param string $siteName
	 * @return mixed
	 */
	protected static function apacheEnableSite($siteName) {
		System_Daemon::debug('Enabling '.$siteName);
		$confFile = DaemonConfig::$distro->APACHE_SITES_DIR . '/' . $siteName . '.conf';

		switch(DaemonConfig::$cfg->DistName){
			case 'CentOS':
				if (file_exists($confFile . '.disabled')) {
					$command = DaemonConfig::$cmd->CMD_MV.' -f ' . $confFile . '.disabled ' . $confFile;
				}
				break;
			default:
				if (file_exists($confFile)) {
					$command = DaemonConfig::$cmd->CMD_HTTPD_A2ENSITE . ' ' . $siteName . '.conf 1>&1 2>&1';
				}
		}

		if (isset($command) && $command != ''){
			System_Daemon::debug('Doing: ' . $command);
			exec($command, $result, $error);
		} else {
			$result = '';
			$error = 0;
		}

		if ($error == 0){
			System_Daemon::debug('Enabled '.$siteName);
		} else {
			$msg = 'Failed to enable '.$siteName;
			System_Daemon::debug($msg);
			System_Daemon::debug(DaemonCommon::listArrayforLOG($result));
			return $msg . '<br />' . ((DaemonConfig::$cfg->DEBUG == '1') ? DaemonCommon::listArrayforGUI($result) : '');
		}

		return true;
	}

	/**
	 * Reload Apache web-server
	 *
	 * @static
	 * @return bool
	 */
	protected static function apacheReloadConfig() {
		System_Daemon::debug('Starting "apacheReloadConfig" subprocess.');

		switch(DaemonConfig::$cfg->DistName){
			case 'CentOS':
				exec(DaemonConfig::$cmd->CMD_HTTPD_CTL . ' configtest 1>&1 2>&1', $result, $error);
				break;
			default:
				exec(DaemonConfig::$cmd->CMD_HTTPD_CTL . ' configtest 1>&1 2>&1', $result, $error);
		}

		if($error == 0){
			if (file_exists(DaemonConfig::$cfg->SOCK_EASYSCPC)){
				$socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
				if ($socket < 0) {return 'socket_create() failed.<br />';}

				$result = socket_connect($socket, DaemonConfig::$cfg->SOCK_EASYSCPC);
				if ($result == false) {return 'socket_connect() failed.<br />';	}

				socket_read($socket, 1024, PHP_NORMAL_READ);

				socket_write($socket, 'ApacheRestart' . "\n", strlen('ApacheRestart' . "\n"));

				socket_shutdown($socket, 2);
				socket_close($socket);
			} else {
				return 'EasySCP Controller not running. System is unable to reload Apache config.<br />';
			}
		}

		System_Daemon::debug('Finished "apacheReloadConfig" subprocess.');

		return ($error == 0) ? true : $result;
	}

	/**
	 * @param array $domainData
	 * @return boolean
	 */
	protected static function apacheWriteDisabledSiteConfig($domainData) {
		$tpl_param = array(
			'DOMAIN_IP'			=> $domainData['ip_number'],
			'DOMAIN_NAME'		=> $domainData['domain_name'],
			'DOMAIN_GID'		=> DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_gid'],
			'DOMAIN_UID'		=> DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_uid'],
			'AWSTATS'			=> (DaemonConfig::$cfg->AWSTATS_ACTIVE == 'yes') ? true : false,
			'DOMAIN_CGI'		=> ($domainData['domain_cgi'] == 'yes') ? true : false,
			'DOMAIN_PHP'		=> ($domainData['domain_php'] == 'yes') ? true : false,
			'BASE_SERVER_VHOST'	=> DaemonConfig::$cfg->BASE_SERVER_VHOST,
			'WWW_DIR'			=> DaemonConfig::$distro->APACHE_WWW_DIR
		);

		$tpl = DaemonCommon::getTemplate($tpl_param);

		$config = $tpl->fetch('apache/parts/vhost_disabled.tpl');
		$tpl = NULL;
		unset($tpl);
		$confFile = DaemonConfig::$distro->APACHE_SITES_DIR . '/' . $domainData['domain_name'] . '-disabled.conf';
		if (DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Writes Apache configuration for default domain and enables configuration.
	 *
	 * @param array $domainData
	 * @return boolean
	 */
	protected static function apacheWriteDomainConfig($domainData) {
		if ($domainData['domain_mailacc_limit'] == '-1'){
			DaemonMail::DaemonMailDeleteDomain($domainData['domain_name']);
		} else {
			DaemonMail::DaemonMailAddDomain($domainData['domain_name']);
		}

		$append = false;
		$sysGroup = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_gid'];
		$sysUser = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_uid'];
		$serverName = $domainData['domain_name'];

		$tpl_param = array(
			'DOMAIN_IP'					=> $domainData['ip_number'],
			'DOMAIN_GID'				=> $sysUser,
			'DOMAIN_UID'				=> $sysGroup,
			'SERVER_ADMIN'				=> $domainData['email'],
			'DOC_ROOT'					=> $domainData['domain_name'],
			'SERVER_NAME'				=> $serverName,
			'SERVER_ALIAS'				=> 'www.'.$domainData['domain_name'].' '.$sysGroup.'.'.DaemonConfig::$cfg->BASE_SERVER_VHOST,
			'MASTER_DOMAIN'				=> $domainData['domain_name'],
			'AWSTATS'					=> (DaemonConfig::$cfg->AWSTATS_ACTIVE == 'yes') ? true : false,
			'DOMAIN_CGI'				=> ($domainData['domain_cgi'] == 'yes') ? true : false,
			'DOMAIN_PHP'				=> ($domainData['domain_php'] == 'yes') ? true : false,
			'BASE_SERVER_VHOST'			=> DaemonConfig::$cfg->BASE_SERVER_VHOST,
			'BASE_SERVER_VHOST_PREFIX'	=> DaemonConfig::$cfg->BASE_SERVER_VHOST_PREFIX,
			'WWW_DIR'					=> DaemonConfig::$distro->APACHE_WWW_DIR,
			'CUSTOM_SITES_CONFIG_DIR'	=> DaemonConfig::$distro->APACHE_CUSTOM_SITES_CONFIG_DIR,
			'HTACCESS_USERS_FILE_NAME'	=> DaemonConfig::$cfg->HTACCESS_USERS_FILE_NAME,
			'HTACCESS_GROUPS_FILE_NAME' => DaemonConfig::$cfg->HTACCESS_GROUPS_FILE_NAME,
			'AWSTATS_GROUP_AUTH'		=> DaemonConfig::$cfg->AWSTATS_GROUP_AUTH,
			'PHP_STARTER_DIR'			=> DaemonConfig::$distro->PHP_STARTER_DIR,
			'APACHE_LOG_DIR'			=> DaemonConfig::$distro->APACHE_LOG_DIR,
			'APACHE_TRAFFIC_LOG_DIR'	=> DaemonConfig::$distro->APACHE_TRAFFIC_LOG_DIR,
			'SELF'						=> $domainData['domain_name']
		);

		if ($tpl_param['SERVER_ADMIN'] == null || $tpl_param['SERVER_ADMIN'] == ''){
			$msg = 'Mail Address for ServerAdmin must be set!';
			System_Daemon::warning($msg);
			return $msg.'<br />'.false;
		}

		if($domainData['ssl_status'] == 1){
			$tpl_param['DOMAIN_PORT'] = 80;
			$tpl_param['REDIRECT'] = true;
		} else {
			$tpl_param['DOMAIN_PORT'] = 80;
		}

		if(isset($domainData['subdomain_url_forward'])){
			$tpl_param['FORWARD_URL'] = $domainData['subdomain_url_forward'];
		}
		if(isset($domainData['subdomain_name'])){
			$append = true;
			$serverName = $domainData['subdomain_name'].'.'.$domainData['domain_name'];

			$tpl_param['DOC_ROOT']			= $domainData['domain_name'].$domainData['subdomain_mount'];
			$tpl_param['SERVER_NAME']		= $serverName;
			$tpl_param['SELF']				= $domainData['subdomain_name'].'.'.$domainData['domain_name'];
			$tpl_param['SERVER_ALIAS']		= 'www.'.$domainData['subdomain_name'].'.'.$domainData['domain_name'];
			$tpl_param['TRAFFIC_PREFIX']	= $domainData['domain_id'].'.'.$domainData['subdomain_id'].'.';
		}
		else {
			$tpl_param['TRAFFIC_PREFIX']	= $domainData['domain_id'].'.0.';
		}

		if(isset($domainData['master_domain'])){
			$tpl_param['MASTER_DOMAIN'] = $domainData['master_domain'];
			$tpl_param['DOC_ROOT']		= $domainData['master_domain'];
			if (isset($domainData['subdomain_mount'])){
				$tpl_param['DOC_ROOT'] .= $domainData['subdomain_mount'];
			}
		}

		$tpl = DaemonCommon::getTemplate($tpl_param);
		// write Apache config
		$config = $tpl->fetch('apache/parts/vhost.tpl');
		$tpl = NULL;
		unset($tpl);
		$confFile = DaemonConfig::$distro->APACHE_SITES_DIR . '/' . $domainData['domain_name'] . '.conf';

		$retVal = DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644, $append);
		if ($retVal !== true) {
			$msg = 'Failed to write'. $confFile;
			System_Daemon::warning($msg);
			return $msg.'<br />'.$retVal;
		}

		if (isset($domainData['ip_number_v6']) && $domainData['ip_number_v6'] != ''){
			$append = true;
			$tpl_param['DOMAIN_IP']	= '['.$domainData['ip_number_v6'].']';

			$tpl = DaemonCommon::getTemplate($tpl_param);
			// write Apache config
			$config = $tpl->fetch('apache/parts/vhost.tpl');
			$tpl = NULL;
			unset($tpl);
			$confFile = DaemonConfig::$distro->APACHE_SITES_DIR . '/' . $domainData['domain_name'] . '.conf';

			$retVal = DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644, $append);
			if ($retVal !== true) {
				$msg = 'Failed to write'. $confFile;
				System_Daemon::warning($msg);
				return $msg.'<br />'.$retVal;
			}

			$tpl_param['DOMAIN_IP']	= $domainData['ip_number'];
		}

		if ($domainData['ssl_status']>0){
			$append = true;
			$retVal = self::writeSSLKeys($domainData);
			if($retVal !== true){
				$msg = 'Writing SSL keys failed';
				System_Daemon::debug($msg);
				return $msg.'<br />'.$retVal;
			}
			$tpl_param['DOMAIN_PORT']	= 443;
			$tpl_param['SSL_CERT_DIR']	= DaemonConfig::$distro->SSL_CERT_DIR;
			$tpl_param['SSL_KEY_DIR']	= DaemonConfig::$distro->SSL_KEY_DIR;
			$tpl_param['REDIRECT']		= false;

			if (isset($domainData['ssl_cacert']) && $domainData['ssl_cacert'] != ''){
				$tpl_param['SSL_CACERT'] = true;
			}

			$tpl = DaemonCommon::getTemplate($tpl_param);
			// write Apache config
			$config = $tpl->fetch('apache/parts/vhost.tpl');
			$tpl = NULL;
			unset($tpl);
			$confFile = DaemonConfig::$distro->APACHE_SITES_DIR . '/' . $domainData['domain_name'] . '.conf';

			$retVal = DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644, $append);
			if ($retVal !== true) {
				$msg = 'Failed to write'. $confFile;
				System_Daemon::warning($msg);
				return $msg.'<br />'.$retVal;
			}

			if (isset($domainData['ip_number_v6']) && $domainData['ip_number_v6'] != ''){
				$append = true;
				$tpl_param['DOMAIN_IP']	= '['.$domainData['ip_number_v6'].']';

				$tpl = DaemonCommon::getTemplate($tpl_param);
				// write Apache config
				$config = $tpl->fetch('apache/parts/vhost.tpl');
				$tpl = NULL;
				unset($tpl);
				$confFile = DaemonConfig::$distro->APACHE_SITES_DIR . '/' . $domainData['domain_name'] . '.conf';

				$retVal = DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644, $append);
				if ($retVal !== true) {
					$msg = 'Failed to write'. $confFile;
					System_Daemon::warning($msg);
					return $msg.'<br />'.$retVal;
				}
			}
		}

		$tpl = DaemonCommon::getTemplate($tpl_param);
		// write Apache config
		$config = $tpl->fetch('apache/parts/custom.conf.tpl');
		$tpl = NULL;
		unset($tpl);

		$confFile = DaemonConfig::$distro->APACHE_CUSTOM_SITES_CONFIG_DIR . '/' . $serverName . '.custom';
		if (!file_exists($confFile)){
			$retVal = DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644,$append);
			if ($retVal !== true) {
				$msg = 'Failed to write'. $confFile;
				System_Daemon::warning($msg);
				return $msg.'<br />'.$retVal;
			}
		}
		return true;
	}

	/**
	 * Create new Domain
	 *
	 * @param array $domainData
	 * @return boolean
	 */
	protected static function createDomain($domainData) {
		$retVal = self::systemCreateUserGroup($domainData);
		if($retVal !== true){
			$msg = 'Failed to create user and group!';
			System_Daemon::debug($msg);
			return $msg.'<br />'.$retVal;
		}

		$retVal = self::directoriesCreateHtdocsStructure($domainData);
		if($retVal !== true){
			$msg = 'Failed to create htdocs!';
			System_Daemon::debug($msg);
			return $msg.'<br />'.$retVal;
		}

		$retVal = self::directoriesCreateFCGI($domainData);
		if($retVal !== true){
			$msg = 'Failed to create fcgi!';
			System_Daemon::debug($msg);
			return $msg.'<br />'.$retVal;
		}

		System_Daemon::debug('Successfully created domain ' . $domainData['domain_name']);

		return true;
	}

	protected static function dbSetAliasStatus($status, $aliasID) {
		$sql_param = array(
			':alias_id' => $aliasID,
			':status' => $status
		);

		$sql_query = '
			UPDATE
				domain_aliasses
			SET
				status=:status
			WHERE
				alias_id = :alias_id;
		';

		DB::prepare($sql_query);
		if ($row = DB::execute($sql_param)->closeCursor()) {
			System_Daemon::debug('Status set to '.$status.' for alias with ID: '.$aliasID);
			return true;
		} else {
			System_Daemon::warning('Setting status to '.$status.' for alias '.$aliasID.' failed!');
			return false;
		}
	}

	protected static function dbSetAliasSubDomainStatus($status, $aliasSubDomainID) {
		$sql_param = array(
			':subdomain_alias_id' => $aliasSubDomainID,
			':status' => $status
		);

		$sql_query = '
			UPDATE
				subdomain_alias
			SET
				status=:status
			WHERE
				subdomain_alias_id = :subdomain_alias_id;
		';

		DB::prepare($sql_query);
		if ($row = DB::execute($sql_param)->closeCursor()) {
			System_Daemon::debug('Status set to '.$status.' for alias subdomain with ID: '.$aliasSubDomainID);
			return true;
		} else {
			System_Daemon::warning('Setting status to '.$status.' for alias subdomain with ID: '.$aliasSubDomainID.' failed!');
			return false;
		}
	}

	/**
	 * Update domain status
	 *
	 * @param string $status
	 * @param int $domainID
	 * @return mixed
	 */
	protected static function dbSetDomainStatus($status, $domainID) {
		$sql_param = array(
			':domain_id'=> $domainID,
			':status'	=> $status
		);

		$sql_query = '
			UPDATE
				domain
			SET
				status = :status
			WHERE
				domain_id = :domain_id;
		';

		DB::prepare($sql_query);
		if (DB::execute($sql_param)->closeCursor()) {
			System_Daemon::debug('Status set to '.$status.' for domain '.$domainID);
		} else {
			$msg = 'Setting status to '.$status.' for domain '.$domainID.' failed!';
			System_Daemon::debug($msg);
			return $msg.'<br />';
		}
		return true;
	}

	protected static function dbSetSubDomainStatus($status, $subDomainID) {
		$sql_param = array(
			':subdomain_id' => $subDomainID,
			':status' => $status
		);

		$sql_query = '
			UPDATE
				subdomain
			SET
				status = :status
			WHERE
				subdomain_id = :subdomain_id;
		';

		DB::prepare($sql_query);
		if ($row = DB::execute($sql_param)->closeCursor()) {
			System_Daemon::debug('Status set to '.$status.' for alias with ID: '.$subDomainID);
			return true;
		} else {
			System_Daemon::warning('Setting status to '.$status.' for subdomain with ID: '.$subDomainID.' failed!');
			return false;
		}
	}

	protected static function deleteAlias($aliasData) {
		if ($subDomainAliasData = self::queryAliasSubDomainDataByAliasID($aliasData['alias_id'])) {
			while ($row = $subDomainAliasData->fetch()) {
				$retVal = self::deleteAliasSubDomain($row);
				if($retVal !== true){
					$fqdn = $row['subdomain_alias_name'].'.'.$row['alias_name'];
					$msg = 'Deleting of alias subdomain '. $fqdn .'failed';
					System_Daemon::debug($msg);
					return $msg . '<br />' . ((DaemonConfig::$cfg->DEBUG == '1') ? DaemonCommon::listArrayforGUI($retVal) : '');
				}
			}
		}

		$fqdn = $aliasData['domain_name'];

		$confFile = DaemonConfig::$distro->APACHE_SITES_DIR . '/' . $fqdn . '.conf';
		$retVal = self::deleteFile($confFile);
		if ($retVal!==true){
			return $retVal;
		}

		$confFile = DaemonConfig::$distro->APACHE_CUSTOM_SITES_CONFIG_DIR . '/' . $fqdn . '.custom';
		$retVal = self::deleteFile($confFile);
		if ($retVal!==true){
			return $retVal;
		}
		self::deleteSSLKeys($aliasData['domain_name']);
		$sql_param = array(
			':alias_id' => $aliasData['alias_id']
		);
		$sql_query = '
			DELETE FROM
				domain_aliasses
			WHERE
				alias_id=:alias_id;
		';
		DB::prepare($sql_query);
		if ($row = DB::execute($sql_param)->closeCursor()) {

		}

		// Delete Mail Domain from db
		DaemonMail::DaemonMailDeleteDomain($aliasData['domain_name']);

		return true;
	}

	protected static function deleteAliasSubDomain($aliasSubDomainData) {

		$fqdn = $aliasSubDomainData['subdomain_name'] . '.' . $aliasSubDomainData['alias_name'];
		$confFile = DaemonConfig::$distro->APACHE_CUSTOM_SITES_CONFIG_DIR . '/' . $fqdn . '.custom';
		$retVal = self::deleteFile($confFile);
		if ($retVal !== true ) {
			return $retVal;
		}
		self::deleteSSLKeys($fqdn);
		$sql_param = array(
			':subdomain_alias_id' => $aliasSubDomainData['subdomain_alias_id']
		);
		$sql_query = '
			DELETE FROM
				subdomain_alias
			WHERE
				subdomain_alias_id=:subdomain_alias_id;
		';
		DB::prepare($sql_query);
		if ($row = DB::execute($sql_param)->closeCursor()) {

		}
		return true;
	}

	/**
	 *
	 * @param array $domainData
	 * @return mixed
	 */
	protected static function deleteDomain($domainData) {

		//delete user and group
		$sysGroup = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_gid'];
		$sysUser = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_uid'];
		$cmdGroup = DaemonConfig::$cmd->CMD_GROUPDEL . ' ' . $sysGroup;
		$cmdUser = DaemonConfig::$cmd->CMD_USERDEL . ' ' . $sysUser;
		exec($cmdUser);
		exec($cmdGroup);

		//delete directories
		$homeDir = DaemonConfig::$distro->APACHE_WWW_DIR . '/' . $domainData['domain_name'];
		$fcgiDir = $fcgiDir = DaemonConfig::$distro->PHP_STARTER_DIR . '/' . $domainData['domain_name'];
		$cmd = DaemonConfig::$cmd->CMD_RM . ' -rf ' . $homeDir;
		exec($cmd);
		System_Daemon::debug('Deleted ' . $homeDir);

		$confFile = DaemonConfig::$distro->APACHE_SITES_DIR . '/' . $domainData['domain_name'] . '.conf';
		$retVal = self::deleteFile($confFile);
		if ($retVal!==true){
			return $retVal;
		}

		$confFile = DaemonConfig::$distro->APACHE_SITES_DIR . '/' . $domainData['domain_name'] . '-disabled.conf';
		$retVal = self::deleteFile($confFile);
		if ($retVal!==true){
			return $retVal;
		}

		$cmd = DaemonConfig::$cmd->CMD_RM . ' -rf ' . $fcgiDir;
		exec($cmd);
		System_Daemon::debug('Deleted '.$fcgiDir);

		$rs = self::getDomainNames($domainData['domain_id']);
		while ($row = $rs->fetch()) {
			self::deleteSSLKeys($row['domain_name']);
		}
		//delete domain from db
		$sql_param = array(
			':domain_id' => $domainData['domain_id']
		);

		$sql_query = '
			DELETE FROM
				domain
			WHERE
				domain_id = :domain_id;
		';

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		// Delete Mail Domain from db
		DaemonMail::DaemonMailDeleteDomain($domainData['domain_name']);

		return true;
	}

	/**
	 * Delete HTAccess File
	 *
	 * @param array $domainData
	 * @param array $row
	 * @return bool
	 */
	protected static function deleteHTAccessFile($domainData, $row) {
		$confFile = DaemonConfig::$distro->APACHE_WWW_DIR . '/' . $domainData['domain_name'] . $row['path'] . '/.htaccess';

		$retVal = self::deleteFile($confFile);
		if ($retVal!==true){
			return $retVal;
		}

		$sql_param = array(
			':id' => $row['id']
		);

		$sql_query = '
			DELETE FROM
				htaccess
			WHERE
				id = :id;
	';

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		return true;
	}

	protected static function deleteSubDomain($subDomainData) {
		//delete directories
		$homeDir = DaemonConfig::$distro->APACHE_WWW_DIR . "/" . $subDomainData['domain_name'] . $subDomainData['mount'];
		$cmd = DaemonConfig::$cmd->CMD_RM . " -rf $homeDir";
		System_Daemon::debug($cmd);
		exec($cmd);
		System_Daemon::warning("Deleted $homeDir");

		$fqdn = $subDomainData['subdomain_name'] . "." . $subDomainData['domain_name'];
		$confFile = DaemonConfig::$distro->APACHE_CUSTOM_SITES_CONFIG_DIR . "/$fqdn.custom";
		$retVal = self::deleteFile($confFile);
		if ($retVal!==true){
			return $retVal;
		}
		self::deleteSSLKeys($fqdn);
		$sql_param = array(
			':subdomain_id' => $subDomainData['subdomain_id']
		);
		$sql_query = "
			DELETE FROM
				subdomain
			WHERE
				subdomain_id = :subdomain_id;
		";
		DB::prepare($sql_query);
		if ($row = DB::execute($sql_param)->closeCursor()) {

		}

		return true;
	}

	/**
	 *
	 * @param array $domainData
	 * @return boolean
	 */
	protected static function directoriesCreateDisabled($domainData) {
		$disabledDir = DaemonConfig::$distro->APACHE_WWW_DIR . '/' . $domainData['domain_name'];
		if (isset($domainData['mount'])) {
			$disabledDir .= '/' . $domainData['mount'];
		}
		$disabledDir .= '/disabled';

		$sysGroup = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_gid'];
		$sysUser = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_uid'];

		if (!DaemonCommon::systemCreateDirectory($disabledDir, $sysUser, $sysGroup) ||
				!DaemonCommon::systemCreateDirectory($disabledDir . '/images', $sysUser, $sysGroup)){
			return "Cannot create Directory " . $disabledDir . " or " . $disabledDir . '/images';
		} else {
			System_Daemon::debug('Created ' . $disabledDir);
		}

		$tpl_param = array(
			'THEME_CHARSET' => 'UTF8',
			'BASE_SERVER_VHOST_PREFIX' => DaemonConfig::$cfg->BASE_SERVER_VHOST_PREFIX,
			'DOMAIN_NAME' => $domainData['domain_name']
		);
		$tpl = DaemonCommon::getTemplate($tpl_param);

		// write Apache config
		$config = $tpl->fetch("apache/parts/default_index_disabled.tpl");
		$tpl = NULL;
		unset($tpl);
		$htmlFile = $disabledDir . '/index.html';
		if (!DaemonCommon::systemWriteContentToFile($htmlFile, $config, $sysUser, DaemonConfig::$distro->APACHE_GROUP, 0644)||
				!copy(DaemonConfig::$cfg->ROOT_DIR . '/gui/domain_disable_page/easyscp.css', $disabledDir . '/easyscp.css')||
				!DaemonCommon::systemSetFolderPermissions($disabledDir . '/easyscp.css', $sysUser, $sysGroup, 0755)){
			return "Cannot write disabled content in " . $disabledDir;
		}

		//copy images
		$sourceDir = dir(DaemonConfig::$cfg->ROOT_DIR . '/gui/domain_disable_page/images');
		while (false !== $entry = $sourceDir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			if ($disabledDir . '/images/' . $entry !== DaemonConfig::$cfg->ROOT_DIR . '/gui/domain_disable_page/images/' . $entry) {
				if(!copy(DaemonConfig::$cfg->ROOT_DIR . '/gui/domain_disable_page/images/' . $entry, $disabledDir . '/images/' . $entry)){
					System_Daemon::debug(DaemonConfig::$cfg->ROOT_DIR . '/gui/domain_disable_page/images/' . $entry . ' to ' . $disabledDir . '/images/' . $entry);
					return "Cannot write images content in " . $disabledDir . '/images/';
				}
			}
		}
		return true;
	}

	/**
	 * @param array $domainData
	 * @return boolean
	 */
	protected static function directoriesCreateError($domainData) {
		$errorDir = DaemonConfig::$distro->APACHE_WWW_DIR . '/' . $domainData['domain_name'];
		if (isset($domainData['mount'])) {
			$errorDir .= '/' . $domainData['mount'];
		}
		$errorDir .= '/errors';

		$sysGroup = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_gid'];
		$sysUser = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_uid'];

		if(!DaemonCommon::systemCreateDirectory($errorDir, $sysUser, $sysGroup, 0775)){
			System_Daemon::warning('Creating directory ' . $errorDir . 'failed!');
			return "Cannot create " . $errorDir;
		}
		if(!DaemonCommon::systemCreateDirectory($errorDir . '/inc', $sysUser, $sysGroup, 0775)){
			System_Daemon::warning('Creating directory ' . $errorDir . '/inc failed!');
			return "Cannot create " . $errorDir . '/inc';
		}

		// copy errorfiles
		$errorFiles = array(401, 403, 404, 500, 503, 'bw_exceeded');
		foreach ($errorFiles as $file) {
			System_Daemon::debug($errorDir . '/' . $file . '.html');
			if (!file_exists($errorDir . '/' . $file . '.html')) {
				System_Daemon::debug('Copying file ' . $errorDir . '/' . $file . '.html');
				if (!copy(DaemonConfig::$cfg->ROOT_DIR . '/gui/errordocs/' . $file . '.html', $errorDir . '/' . $file . '.html')){
					System_Daemon::warning('Copying file ' . $file . '.html failed!');
					return "Cannot write to " . $errorDir;
				}
			}
		}

		// copy inc
		$sourceDir = dir(DaemonConfig::$cfg->ROOT_DIR . '/gui/errordocs/inc');
		while (false !== $entry = $sourceDir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			if (DaemonConfig::$cfg->ROOT_DIR . '/gui/errordocs/inc/' . $entry !== $errorDir . '/inc/' . $entry) {
				if (!copy(DaemonConfig::$cfg->ROOT_DIR . '/gui/errordocs/inc/' . $entry, $errorDir . '/inc/' . $entry)){
					System_Daemon::warning('Copying file ' . $entry . ' failed!');
					return "Cannot write to " . $errorDir . '/inc/';
				}
			}
		}
		return true;
	}

	/**
	 * Writes Apache configuration for default domain and enables configuration.
	 *
	 * @param array $domainData
	 * @return boolean
	 */
	protected static function directoriesCreateFCGI($domainData) {
		System_Daemon::debug('Starting "directoriesCreateFCGI" subprocess.');

		$fcgiDir = DaemonConfig::$distro->PHP_STARTER_DIR . "/" . $domainData['domain_name'];
		$sysGroup = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_gid'];
		$sysUser = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_uid'];

		System_Daemon::debug("Creating $fcgiDir");
		if (!DaemonCommon::systemCreateDirectory($fcgiDir, $sysUser, $sysGroup, 0555) ||
				!DaemonCommon::systemCreateDirectory("$fcgiDir/php", $sysUser, $sysGroup, 0555)) {
			return false;
		}

		//file
		if (!file_exists($fcgiDir . "/php-fcgi-starter")) {
			$tpl_param = array(
				'DOMAIN_NAME'		=> $domainData['domain_name'],
				'WWW_DIR'			=> DaemonConfig::$distro->APACHE_WWW_DIR,
				'PHP_STARTER_DIR'	=> DaemonConfig::$distro->PHP_STARTER_DIR,
				'PHP_FASTCGI_BIN'	=> DaemonConfig::$distro->PHP_FASTCGI_BIN
			);

			$tpl = DaemonCommon::getTemplate($tpl_param);

			$config = $tpl->fetch("php/parts/php-fcgi-starter.tpl");
			$tpl = NULL;
			unset($tpl);
			if (!DaemonCommon::systemWriteContentToFile($fcgiDir . '/php-fcgi-starter', $config, $sysUser, $sysGroup, 0550)) {
				return false;
			}
		}

		if (!file_exists($fcgiDir . "/php/php.ini")) {
			$tpl_param = array(
				'DOMAIN_NAME'		=> $domainData['domain_name'],
				'WWW_DIR'			=> DaemonConfig::$distro->APACHE_WWW_DIR,
				'PHP_STARTER_DIR'	=> DaemonConfig::$distro->PHP_STARTER_DIR,
				'PEAR_DIR'			=> DaemonConfig::$distro->PEAR_DIR,
				'PHP_TIMEZONE'		=> DaemonConfig::$cfg->PHP_TIMEZONE
			);
			$tpl = DaemonCommon::getTemplate($tpl_param);

			$config = $tpl->fetch('php/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'} . '/php.ini');
			$tpl = NULL;
			unset($tpl);
			if (!DaemonCommon::systemWriteContentToFile($fcgiDir . '/php/php.ini', $config, $sysUser, $sysGroup, 0440)) {
				return false;
			}
		}

		System_Daemon::debug('Finished "directoriesCreateFCGI" subprocess.');

		return true;
	}

	/**
	 *
	 * @param array $domainData
	 * @return boolean
	 */
	protected static function directoriesCreateHtdocs($domainData) {
		$htdocsDir = DaemonConfig::$distro->APACHE_WWW_DIR . "/" . $domainData['domain_name'];
		if (isset($domainData['mount'])) {
			$htdocsDir .= "/" . $domainData['mount'];
		}
		$htdocsDir .= "/htdocs";

		$sysGroup = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_gid'];
		$sysUser = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_uid'];

		if	(!DaemonCommon::systemCreateDirectory($htdocsDir, $sysUser, $sysGroup)||
				!DaemonCommon::systemCreateDirectory($htdocsDir . "/images", $sysUser, $sysGroup)){
			return "Cannot create " . $htdocsDir . ' or ' . $htdocsDir . "/images";
		}

		$tpl_param = array(
			"THEME_CHARSET" => "UTF-8",
			"DOMAIN_NAME" => $domainData['domain_name'],
			"BASE_SERVER_VHOST_PREFIX" => DaemonConfig::$cfg->BASE_SERVER_VHOST_PREFIX,
			"BASE_SERVER_VHOST" => DaemonConfig::$cfg->BASE_SERVER_VHOST
		);
		$tpl = DaemonCommon::getTemplate($tpl_param);

		$config = $tpl->fetch("apache/parts/default_index.tpl");
		$tpl = NULL;
		unset($tpl);
		$htmlFile = $htdocsDir . "/index.html";

		if (!DaemonCommon::systemWriteContentToFile($htmlFile,$config,$sysUser,$sysGroup,0644)||
				!copy(DaemonConfig::$cfg->ROOT_DIR . "/gui/domain_default_page/easyscp.css", $htdocsDir . "/easyscp.css")||
				!DaemonCommon::systemSetFolderPermissions($htdocsDir . "/easyscp.css", $sysUser, $sysGroup, 0775)){
			return "Cannot write to " . $htdocsDir;
		}

		//copy images
		$sourceDir = dir(DaemonConfig::$cfg->ROOT_DIR . "/gui/domain_default_page/images");
		while (false !== $entry = $sourceDir->read()) {
			// Skip pointers
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			if ("$htdocsDir/images/$entry" !== DaemonConfig::$cfg->ROOT_DIR . "/gui/domain_default_page/images/$entry") {
				if (!copy(DaemonConfig::$cfg->ROOT_DIR . "/gui/domain_default_page/images/$entry", "$htdocsDir/images/$entry")){
					return "Cannot copy to " . $htdocsDir . '/images/';
				}
			}
		}
		return true;
	}

	/**
	 * @param array $domainData
	 * @return boolean
	 */
	protected static function directoriesCreateHtdocsStructure($domainData) {
		$homeDir	= DaemonConfig::$distro->APACHE_WWW_DIR . "/" . $domainData['domain_name'];
		if (isset($domainData['mount'])) {
			$homeDir .= "/" . $domainData['mount'];
		}
		$sysUser	= DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_uid'];
		$sysGroup	= DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_gid'];

		$retVal = DaemonCommon::systemCreateDirectory($homeDir, $sysUser, $sysGroup, 0755);
		if ($retVal!==true){
			$msg = 'Failed to create '.$homeDir.'!';
			System_Daemon::warning($msg);
			return $msg . '<br />' . $retVal;
		}

		if (!isset($domainData['mount'])){
			$retVal = DaemonCommon::systemCreateDirectory($homeDir . '/backups', $sysUser, $sysGroup, 0755);
			if ($retVal!==true){
				$msg = 'Failed to create '.$homeDir.'/backups!';
				System_Daemon::warning($msg);
				return $msg . '<br />' . $retVal;
			}

			$retVal = self::directoriesCreateDisabled($domainData);
			if ($retVal!==true){
				$msg = 'Failed to create disabled directory!';
				System_Daemon::warning($msg);
				return $msg . '<br />' . $retVal;
			}

			$retVal = DaemonCommon::systemCreateDirectory($homeDir . '/logs', $sysUser, $sysGroup, 0700);
			if ($retVal!==true){
				$msg = 'Failed to create '.$homeDir.'/logs!';
				System_Daemon::warning($msg);
				return $msg . '<br />' . $retVal;
			}

			$retVal = DaemonCommon::systemCreateDirectory($homeDir . '/phptmp', $sysUser, $sysGroup, 0770);
			if ($retVal!==true){
				$msg = 'Failed to create '.$homeDir.'/phptmp!';
				System_Daemon::warning($msg);
				return $msg . '<br />' . $retVal;
			}
		}

		$retVal = DaemonCommon::systemCreateDirectory($homeDir . '/cgi-bin', $sysUser, $sysGroup, 0755);
		if ($retVal!==true){
			$msg = 'Failed to create '.$homeDir.'/cgi-bin!';
			System_Daemon::warning($msg);
			return $msg . '<br />' . $retVal;
		}

		$retVal = self::directoriesCreateError($domainData);
		if ($retVal!==true){
			$msg = 'Failed to create error directory!';
			System_Daemon::warning($msg);
			return $msg . '<br />' . $retVal;
		}

		$retVal = self::directoriesCreateHtdocs($domainData);
		if ($retVal!==true){
			$msg = 'Failed to create htdocs directory!';
			System_Daemon::warning($msg);
			return $msg . '<br />' . $retVal;
		}

		$retVal = DaemonCommon::systemCreateDirectory($homeDir . '/statistics', $sysUser, $sysGroup, 0755);
		if ($retVal!==true){
			$msg = 'Failed to create '.$homeDir.'/statistics!';
			System_Daemon::warning($msg);
			return $msg . '<br />' . $retVal;
		}

		return true;
	}

	/**
	 *
	 * @param array $domainData
	 * @return boolean
	 */
	protected static function getFQDN($domainData) {
		$fqdn = "";
		if (isset($domainData['alias_name'])) {
			if (isset($domainData['subdomain_alias_name'])) {
				$fqdn = $domainData['subdomain_alias_name'];
			}
			$fqdn .= $domainData['alias_name'];
			return $fqdn;
		}
		if (isset($domainData['domain_name'])) {
			if (isset($domainData['subdomain_name'])) {
				$fqdn = $domainData['subdomain_name'];
			}
			$fqdn .= $domainData['domain_name'];
			return $fqdn;
		}
		return false;
	}

	protected static function queryAliasDataByAliasID($alias_id) {
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

	protected static function queryAliasSubDomainDataByAliasID($aliasID) {
		$sql_param = array(
			':alias_id' => $aliasID
		);

		$sql_query = "
			SELECT
				   sa.subdomain_alias_name as subdomain_name,
				   sa.status as alias_status,
				   sa.status,
				   sa.subdomain_alias_id,
				   sa.subdomain_alias_url_forward as subdomain_url_forward,
				   sd.subdomain_mount,
				   sa.subdomain_id,
				   da.alias_name as domain_name,
				   da.alias_name,
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
				   sa.ssl_key,
				   sa.ssl_cert,
				   sa.ssl_status,
				   s.ip_number,
				   s.ip_number_v6
				FROM
					subdomain_alias sa,
					domain AS d,
					server_ips AS s,
					domain_aliasses da,
					subdomain sd,
					admin a
				WHERE
					da.alias_ip_id = s.ip_id
				AND
					a.admin_id = d.domain_admin_id
				AND
					da.alias_id = :alias_id
				AND
					da.domain_id = d.domain_id
				AND
					sa.alias_id = da.alias_id
				AND
					sd.subdomain_id = sa.subdomain_id;
		";

		DB::prepare($sql_query);
		$aliasSubDomainData = DB::execute($sql_param);

		return $aliasSubDomainData;
	}

	protected static function queryDomainDataByDomainID($domainID){
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

	protected static function querySubDomainDataByDomainID($domainID) {
		$sql_param = array(
			':domain_id' => $domainID
		);
		$sql_query = "
			SELECT
				a.email,
				d.domain_cgi,
				d.domain_php,
				d.domain_gid,
				d.domain_uid,
				d.domain_id,
				d.domain_name,
				d.domain_mailacc_limit,
				d.domain_ssl,
				s.ip_number,
				s.ip_number_v6,
				sd.subdomain_name,
				sd.status as subdomain_status,
				sd.subdomain_mount as mount,
				sd.subdomain_mount,
				sd.ssl_key,
				sd.ssl_cert,
				sd.ssl_cacert,
				sd.ssl_status,
	  		    sd.subdomain_id,
				sd.subdomain_url_forward,
				sd.subdomain_id
			FROM
				admin a,
				domain d,
				server_ips s,
				subdomain sd
			WHERE
				a.admin_id = d.domain_admin_id
			AND
				d.domain_ip_id = s.ip_id
			AND
				sd.domain_id = d.domain_id
			AND
				d.domain_id = :domain_id;
		";
		DB::prepare($sql_query);
		$subDomainData = DB::execute($sql_param);

		return $subDomainData;
	}

	protected static function querySubDomainDataBySubDomainID($subDomainID) {
		$sql_param = array(
			':subdomain_id' => $subDomainID
		);
		$sql_query = '
			SELECT
				a.email,
				d.domain_cgi,
				d.domain_php,
				d.domain_gid,
				d.domain_uid,
				d.domain_id,
				d.domain_name,
				d.domain_mailacc_limit,
				d.domain_ssl,
				s.ip_number,
				s.ip_number_v6,
				sd.subdomain_name,
				sd.status as subdomain_status,
				sd.subdomain_mount as mount,
				sd.subdomain_mount,
				sd.ssl_key,
				sd.ssl_cert,
				sd.ssl_cacert,
				sd.ssl_status,
	  		    sd.subdomain_id,
				sd.subdomain_url_forward,
				sd.subdomain_id
			FROM
				domain AS d,
				server_ips AS s,
				subdomain AS sd,
				admin AS a
			WHERE
				d.domain_ip_id = s.ip_id
			AND
				sd.domain_id = d.domain_id
			AND
				d.domain_admin_id = a.admin_id
			AND
				sd.subdomain_id = :subdomain_id;
		';
		DB::prepare($sql_query);
		$subDomainData = DB::execute($sql_param);

		return $subDomainData;
	}

	protected static function queryHTAccessData($domainID){
		$sql_param = array(
			':domain_id' => $domainID
		);

		$sql_query = "
			SELECT
				domain_id, domain_name, domain_gid, domain_uid
			FROM
				domain
			WHERE
				domain_id = :domain_id;
		";
		DB::prepare($sql_query);
		$domainData = DB::execute($sql_param, true);

		return $domainData;
	}

	/**
	 * Create system user and group
	 * @param array $domainData
	 * @return boolean
	 */
	protected static function systemCreateUserGroup($domainData) {
		$retVal = true;
		$sysGroup = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_gid'];
		$sysUser = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . $domainData['domain_uid'];
		$sysGID = $domainData['domain_gid'];
		$sysUID = $domainData['domain_uid'];
		$homeDir = DaemonConfig::$distro->APACHE_WWW_DIR . "/" . $domainData['domain_name'];

		// add group and user for BSD has a different format
		if (strcmp(DaemonConfig::$cfg->ROOT_GROUP, 'wheel') == 0) {
			$cmdGroup = DaemonConfig::$cmd->CMD_GROUPADD . " $sysGroup -g $sysGID";
			$cmdUser = DaemonConfig::$cmd->CMD_USERADD . " $sysUser -c virtual-user -d $homeDir -g " .
					"$sysGroup -s /bin/false -u $sysUID";
		} else {
			$cmdGroup = DaemonConfig::$cmd->CMD_GROUPADD . " -g $sysGID $sysGroup";
			$cmdUser = DaemonConfig::$cmd->CMD_USERADD . " -c virtual-user -d $homeDir -g " .
					"$sysGroup -s /bin/false -u $sysUID $sysUser";
		}

		exec('getent group '.$sysGroup.' 2>&1', $result, $error);
		if ($result == Null){
			System_Daemon::debug("Group $sysGroup ($sysGID) does not exist");
			exec($cmdGroup.' >> /dev/null 2>&1', $result, $error);
			System_Daemon::debug($cmdGroup);
			System_Daemon::debug($result . $error);
			unset($result);
		}

		exec('getent passwd '.$sysUser.' 2>&1', $result, $error);
		if ($result == Null){
			System_Daemon::debug("User $sysUser ($sysUID) does not exist");
			exec($cmdUser.' >> /dev/null 2>&1', $result, $error);
			System_Daemon::debug($cmdUser);
			System_Daemon::debug($result . $error);
			unset($result);
		}

		return $retVal;
	}

	/**
	 * @static
	 * @param $domainData
 	 * @param $row
	 * @return bool
	 */
	protected static function writeHTAccessFile($domainData, $row){
		$content = '';
		$content .= 'AuthType ' . $row['auth_type'] ."\n";
		$content .= 'AuthName "' . $row['auth_name'] . '"' ."\n";
		if ($row['group_id'] == 0) {
			$content .= 'AuthUserFile ' . DaemonConfig::$distro->APACHE_WWW_DIR . '/' . $domainData['domain_name'] . '/.htpasswd' ."\n";
			$users = '';
			if(strpos($row['user_id'], ",") === false){
				$user = DB::query('select uname from htaccess_users where id = '.$row['user_id'], true);
				$users .= ' ' .$user['uname'];
			} else {
				$temp = explode(',', $row['user_id']);
				foreach ($temp as $user_id){
					$user = DB::query('select uname from htaccess_users where id = '.$user_id, true);
					$users .= ' ' .$user['uname'];
				}
			}
			$content .= 'Require user' . $users ."\n";
		} else {
			$content .= 'AuthUserFile ' . DaemonConfig::$distro->APACHE_WWW_DIR . '/' . $domainData['domain_name'] . '/.htpasswd' ."\n";
			$content .= 'AuthGroupFile ' . DaemonConfig::$distro->APACHE_WWW_DIR . '/' . $domainData['domain_name'] . '/.htgroup' ."\n";
			$groups = '';
			if(strpos($row['group_id'], ",") === false){
				$group = DB::query('select ugroup from htaccess_groups where id = '.$row['group_id'], true);
				$groups .= ' ' .$group['ugroup'];
			} else {
				$temp = explode(',', $row['group_id']);
				foreach ($temp as $group_id){
					$group = DB::query('select ugroup from htaccess_groups where id = '.$group_id, true);
					$groups .= ' ' .$group['ugroup'];
				}
			}
			$content .= 'Require group ' . $groups ."\n";
		}

		$fileName = DaemonConfig::$distro->APACHE_WWW_DIR . '/' . $domainData['domain_name'] . $row['path'] . '/.htaccess';

		if (!DaemonCommon::systemWriteContentToFile($fileName, $content, 'vu' . $domainData['domain_uid'], 'vu' . $domainData['domain_gid'], 0644)){
			$msg = 'Failed to write htaccess file for ' . $domainData['domain_name'];
			System_Daemon::warning($msg);
			return $msg.'<br />';
		}

		$sql_param = array(
				':domain_id' => $domainData['domain_id']
		);

		$sql_query = "
			UPDATE
				htaccess
			SET
				status = 'ok'
			WHERE
				dmn_id = :domain_id;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		return true;
	}

	/**
	 * @static
	 * @param $domainData
	 * @return bool
	 */
	protected static function writeHTAccessGroup($domainData){
		System_Daemon::debug('Starting "DaemonDomainCommon::writeHTAccessGroup = ' . $domainData['domain_name'] . '" subprocess.');

		//delete groups marked as 'delete'
		$sql_param = array(
			':domain_id' 	=> $domainData['domain_id'],
			':status'		=> 'delete'
		);

		$sql_query = "
			DELETE FROM
				htaccess_groups
			WHERE
				dmn_id = :domain_id
			AND
				status = :status;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		$content = '';
		$fileName = DaemonConfig::$distro->APACHE_WWW_DIR . '/' . $domainData['domain_name'] . '/.htgroup';

		$sql_param = array(
			':domain_id' => $domainData['domain_id']
		);

		$sql_query = "
			SELECT
				ugroup, members
			FROM
				htaccess_groups
			WHERE
				dmn_id = :domain_id;
		";

		DB::prepare($sql_query);
		foreach (DB::execute($sql_param) as $row) {
			$users = '';
			if (!is_null($row['members'])) {
				$temp = explode(',', $row['members']);
				foreach($temp as $id){
					$user = DB::query('select uname from htaccess_users where id = '.$id, true);
					$users .= ' ' .$user['uname'];
				}
				$content .= $row['ugroup'].':'.$users."\n";
			}
		}

		if (!DaemonCommon::systemWriteContentToFile($fileName, $content, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644)){
			$msg = 'Failed to write htaccess_groups for ' . $domainData['domain_name'];
			System_Daemon::warning($msg);
			return $msg.'<br />';
		}

		$sql_query = "
			UPDATE
				htaccess_groups
			SET
				status = 'ok'
			WHERE
				dmn_id = :domain_id;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		System_Daemon::debug('Finished "DaemonDomainCommon::writeHTAccessGroup = ' . $domainData['domain_name'] . '" subprocess.');

		return true;
	}

	/**
	 * @static
	 * @param $domainData
	 * @return bool
	 */
	protected static function writeHTAccessUser($domainData){
		System_Daemon::debug('Starting "DaemonDomainCommon::writeHTAccessUser = ' . $domainData['domain_name'] . '" subprocess.');

		//delete users marked as 'delete'
		$sql_param = array(
			':domain_id' 	=> $domainData['domain_id'],
			':status'		=> 'delete'
		);

		$sql_query = "
			DELETE FROM
				htaccess_users
			WHERE
				dmn_id = :domain_id
			AND
				status = :status;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();
		
		$content = '';
		$fileName = DaemonConfig::$distro->APACHE_WWW_DIR . '/' . $domainData['domain_name'] . '/.htpasswd';

		$sql_param = array(
			':domain_id' => $domainData['domain_id']
		);

		$sql_query = "
			SELECT
				uname, upass
			FROM
				htaccess_users
			WHERE
				dmn_id = :domain_id;
		";

		DB::prepare($sql_query);
		foreach (DB::execute($sql_param) as $row) {
			$content .= $row['uname'].':'.$row['upass']."\n";
		}

		if (!DaemonCommon::systemWriteContentToFile($fileName, $content, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644)){
			$msg = 'Failed to write htaccess_users for ' . $domainData['domain_name'];
			System_Daemon::warning($msg);
			return $msg.'<br />';
		}

		$sql_query = "
			UPDATE
				htaccess_users
			SET
				status = 'ok'
			WHERE
				dmn_id = :domain_id;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		System_Daemon::debug('Finished "DaemonDomainCommon::writeHTAccessUser = ' . $domainData['domain_name'] . '" subprocess.');

		return true;
	}

	protected static function writeMasterConfig(){
		$append = false;
		$sysGroup = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . DaemonConfig::$cfg->APACHE_SUEXEC_MIN_GID;
		$sysUser = DaemonConfig::$cfg->APACHE_SUEXEC_USER_PREF . DaemonConfig::$cfg->APACHE_SUEXEC_MIN_UID;

		$sql_query = "
			SELECT
				*
			FROM
				config
			WHERE
				name IN (
					'SSL_KEY',
					'SSL_CERT',
					'SSL_CACERT',
					'SSL_STATUS')
		";

		$rs = DB::query($sql_query);
		$sslData = array();
		while ($row=$rs->fetch()){
			$sslData[strtolower($row['name'])] = $row['value'];
		}

		$tpl_param = array(
			'BASE_SERVER_IP'			=> DaemonConfig::$cfg->BASE_SERVER_IP,
			'SUEXEC_GID'				=> $sysUser,
			'SUEXEC_UID'				=> $sysGroup,
			'DEFAULT_ADMIN_ADDRESS'		=> DaemonConfig::$cfg->DEFAULT_ADMIN_ADDRESS,
			'GUI_ROOT_DIR'				=> DaemonConfig::$cfg->GUI_ROOT_DIR,
			'BASE_SERVER_VHOST'			=> DaemonConfig::$cfg->BASE_SERVER_VHOST,
			'APACHE_LOG_DIR'			=> DaemonConfig::$distro->APACHE_LOG_DIR,
			'PHP_STARTER_DIR'			=> DaemonConfig::$distro->PHP_STARTER_DIR
		);

		if (isset(DaemonConfig::$cfg->BASE_SERVER_IPv6) && DaemonConfig::$cfg->BASE_SERVER_IPv6 != ''){
			$tpl_param['BASE_SERVER_IPv6'] = DaemonConfig::$cfg->BASE_SERVER_IPv6;
		}

		if($sslData['ssl_status']==1){
			$tpl_param['BASE_PORT']	= 80;
			$tpl_param['REDIRECT']	= true;
		} else {
			$tpl_param['BASE_PORT']	= 80;
		}

		$tpl = DaemonCommon::getTemplate($tpl_param);
		// write Apache config
		$config = $tpl->fetch('apache/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'} . '/00_master.conf.tpl');
		$tpl = NULL;
		unset($tpl);
		$confFile = DaemonConfig::$cfg->CONF_DIR.'/apache/working/00_master.conf';

		$retVal = DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644, $append);
		if ($retVal !== true) {
			$msg = 'Failed to write'. $confFile;
			System_Daemon::warning($msg);
			return $msg.'<br />'.$retVal;
		}

		if ($sslData['ssl_status']>0){
			$append = true;
			$sslData['domain_name'] = 'master';
			$retVal = self::writeSSLKeys($sslData);
			if($retVal !== true){
				$msg = 'Writing SSL keys failed';
				System_Daemon::debug($msg);
				return $msg.'<br />'.$retVal;
			}
			$tpl_param['BASE_PORT']		= 443;
			$tpl_param['SSL_CERT_DIR']	= DaemonConfig::$distro->SSL_CERT_DIR;
			$tpl_param['SSL_KEY_DIR']	= DaemonConfig::$distro->SSL_KEY_DIR;
			$tpl_param['REDIRECT']		= false;

			if (isset($sslData['ssl_cacert']) && $sslData['ssl_cacert'] != ''){
				$tpl_param['SSL_CACERT'] = true;
			}

			$tpl = DaemonCommon::getTemplate($tpl_param);
			// write Apache config
			$config = $tpl->fetch('apache/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'} . '/00_master.conf.tpl');
			$tpl = NULL;
			unset($tpl);
			$confFile = DaemonConfig::$cfg->CONF_DIR.'/apache/working/00_master.conf';

			$retVal = DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644, $append);
			if ($retVal !== true) {
				$msg = 'Failed to write'. $confFile;
				System_Daemon::warning($msg);
				return $msg.'<br />'.$retVal;
			}
		}

		exec(DaemonConfig::$cmd->CMD_CP.' -pf '.DaemonConfig::$cfg->CONF_DIR.'/apache/working/00_master.conf '.DaemonConfig::$distro->APACHE_SITES_DIR.'/00_master.conf', $result, $error);

		return true;
	}

	/**
	 * Verify if SSL-key and certificate corresponds. Write key and certificate
	 * @param array $domainData
	 * @return mixed
	 */
	protected static function writeSSLKeys($domainData){
		System_Daemon::debug(print_r($domainData,true));
		if (isset($domainData['subdomain_name'])) {
			$fqdn = $domainData['subdomain_name'] . '.' . $domainData['domain_name'];
		} else {
			$fqdn =  $domainData['domain_name'];
		}

		$cacertFile = DaemonConfig::$distro->SSL_CERT_DIR . '/easyscp_' . $fqdn . '-cacert.pem';
		$certFile   = DaemonConfig::$distro->SSL_CERT_DIR . '/easyscp_' . $fqdn . '-cert.pem';
		$keyFile    = DaemonConfig::$distro->SSL_KEY_DIR  . '/easyscp_' . $fqdn . '-key.pem';

		$cert = $domainData['ssl_cert'];
		$key = $domainData['ssl_key'];

		if (openssl_x509_check_private_key($cert, $key)) {
			if(!DaemonCommon::systemWriteContentToFile($certFile, $domainData['ssl_cert'], DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644)){
				$msg = 'Failed to write certificate '.$certFile;
				System_Daemon::debug($msg);
				return $msg.'<br />';
			}
			if (!DaemonCommon::systemWriteContentToFile($keyFile, $domainData['ssl_key'], DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0640)) {
				$msg = 'Failed to write key '.$keyFile;
				System_Daemon::debug($msg);
				return $msg.'<br />';
			}
		} else{
			$msg = 'Certificate and key don\'t match';
			System_Daemon::debug($msg);
			return $msg.'<br />';
		}

		if (isset($domainData['ssl_cacert']) && $domainData['ssl_cacert'] != ''){
			if(!DaemonCommon::systemWriteContentToFile($cacertFile, $domainData['ssl_cacert'], DaemonConfig::$cfg->ROOT_USER, DaemonConfig::$cfg->ROOT_GROUP, 0644)){
				$msg = 'Failed to write certificate of certification authorities (CA) '.$cacertFile;
				System_Daemon::debug($msg);
				return $msg.'<br />';
			}
		}

		return true;
	}

	/**
	 * Delete all keys and certificates for domain name
	 * @param $domainName
	 */
	protected static function deleteSSLKeys($domainName){
		$cacertFile = DaemonConfig::$distro->SSL_CERT_DIR . '/easyscp_' . $domainName . '-cacert.pem';
		$certFile = DaemonConfig::$distro->SSL_CERT_DIR . '/easyscp_' . $domainName . '-cert.pem';
		$keyFile = DaemonConfig::$distro->SSL_KEY_DIR . '/easyscp_' . $domainName . '-key.pem';

		if (file_exists($cacertFile)) {
			$cmdCacert = DaemonConfig::$cmd->CMD_RM . ' ' . $cacertFile;
			exec($cmdCacert);
			System_Daemon::debug('Deleted SSL CA cert for ' . $domainName);
		}

		if (file_exists($certFile)) {
			$cmdCert = DaemonConfig::$cmd->CMD_RM . ' ' . $certFile;
			exec($cmdCert);
			System_Daemon::debug('Deleted SSL certificate for ' . $domainName);
		}

		if (file_exists($keyFile)) {
			$cmdKey = DaemonConfig::$cmd->CMD_RM . ' ' . $keyFile;
			exec($cmdKey);
			System_Daemon::debug('Deleted SSL key for ' . $domainName);
		}
	}

	/**
	 * Query for domain names from domain, subdomain, alias and subdomain alias tables.
	 * @param $domain_id
	 * @return mixed
	 * @throws Exception
	 */
	protected static function getDomainNames($domain_id){
		$sql_param = array(
			"domain_id"	=> $domain_id
		);
		$sql_query = "
			SELECT
				domain_name
			FROM
				domain
			WHERE
				domain_id = :domain_id
			UNION SELECT
				alias_name AS domain_name
			FROM
				domain as d,
				domain_aliasses as da
			WHERE
				d.domain_id=da.domain_id
			AND
				d.domain_id = :domain_id
			UNION SELECT
				CONCAT(`subdomain_name`, '.', `domain_name`)
			FROM
				domain AS d,
				subdomain AS s
			WHERE
				d.domain_id = s.domain_id
			AND
				d.domain_id = :domain_id
			UNION SELECT
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
				da.domain_id = :domain_id;
		";

		DB::prepare($sql_query);
		$rs = DB::execute($sql_param);

		return $rs;
	}

	/**
	 * Delete a given fileName
	 *
	 * returns true on success or error message otherwise
	 *
	 * @param $fileName
	 * @return bool|string
	 */
	protected static function deleteFile($fileName){
		if (file_exists($fileName)){
			if(!unlink($fileName)){
				$msg = 'Cannot delete ' .$fileName;
				System_Daemon::debug($msg);
				return $msg . '<br />';
			}
		}

		return true;
	}
}
