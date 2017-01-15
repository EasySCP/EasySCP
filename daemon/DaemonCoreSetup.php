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
 * Handles DaemonCoreSetup.
 *
 * @param string $Input
 * @return boolean
 */

function Setup($Input){
	System_Daemon::debug('Running Setup ' . $Input . ' subprocess');

    switch($Input){
		case 'StopServices':
			return SetupStopServices();
			break;
		case 'MysqlTest':
			return SetupMysqlTest();
			break;
		case 'EasySCP_Users':
			return SetupEasySCP_Users();
			break;
		case 'EasySCP_Directories':
			return EasySCP_Directories();
			break;
		case 'EasySCP_main_configuration_file':
			return EasySCP_main_configuration_file();
			break;
		case 'EasySCP_database':
			return EasySCP_database();
			break;
		case 'EasySCP_default_SQL_data':
			return EasySCP_default_SQL_data();
			break;
		case 'EasySCP_create_mta_db':
			return EasySCP_create_mta_db();
			break;
		case 'EasySCP_create_pma_db':
			return EasySCP_create_pma_db();
			break;
		case 'EasySCP_create_roundcube_db':
			return EasySCP_create_roundcube_db();
			break;
		case 'EasySCP_create_pdns_db':
			return EasySCP_create_pdns_db();
			break;
		case 'EasySCP_system_resolver':
			return EasySCP_system_resolver();
			break;
		case 'EasySCP_crontab_file':
			return EasySCP_crontab_file();
			break;
		case 'EasySCP_Powerdns_main_configuration_file':
			return EasySCP_Powerdns_main_configuration_file();
			break;
		case 'EasySCP_Apache_php_configuration':
			return EasySCP_Apache_php_configuration();
			break;
		case 'EasySCP_Apache_main_vhost_file':
			return EasySCP_Apache_main_vhost_file();
			break;
		case 'EasySCP_Apache_AWStats_vhost_file':
			return EasySCP_Apache_AWStats_vhost_file();
			break;
		case 'EasySCP_MTA_configuration_files':
			return EasySCP_MTA_configuration_files();
			break;
		case 'EasySCP_ProFTPd_configuration_file':
			return EasySCP_ProFTPd_configuration_file();
			break;
		case 'EasySCP_init_scripts':
			return EasySCP_init_scripts();
			break;
		case 'GUI_PHP':
			return GUI_PHP();
			break;
		case 'GUI_VHOST':
			return GUI_VHOST();
			break;
		case 'GUI_PMA':
			return GUI_PMA();
			break;
		case 'GUI_RoundCube':
			return GUI_RoundCube();
			break;
		case 'Set_daemon_permissions':
			return Set_daemon_permissions();
			break;
		case 'Set_gui_permissions':
			return Set_gui_permissions();
			break;
		case 'Starting_all_services':
			return Starting_all_services();
			break;
		case 'Rkhunter':
			return Rkhunter();
			break;
		case 'System_cleanup':
			return System_cleanup();
			break;
		case 'EasySCP_Finish_Setup':
			Setup_Finishing();
			break;
		default:
			System_Daemon::info('Error: Unknown Setup Process ' . $Input);
			return 'Error: Unknown Setup Process';
    }
}

function SetupStopServices(){
	$services = array('SRV_DNS', 'SRV_FTPD', 'SRV_CLAMD', 'SRV_POSTGREY', 'SRV_POLICYD_WEIGHT', 'SRV_AMAVIS', 'SRV_MTA', 'SRV_DOVECOT');

	foreach($services as $service){
		if ( DaemonConfig::$cmd->$service != 'no' && file_exists(DaemonConfig::$cmd->$service)){
			System_Daemon::debug('Found Service ' . DaemonConfig::$cmd->$service);
			System_Daemon::debug('Stopping Service ' . DaemonConfig::$cmd->$service);
			exec(DaemonConfig::$cmd->$service . ' stop >> /dev/null 2>&1', $result, $error);
		} else {
			if(DaemonConfig::$cmd->$service != 'no'){
				System_Daemon::debug('Service ' . $service . ': ' . DaemonConfig::$cmd->$service . ' does not exist');
			}
		}
	}

    return 'Ok';
}

function SetupMysqlTest(){
	$sql = simplexml_load_file(DaemonConfig::$cfg->{'ROOT_DIR'} . '/../setup/config.xml');

	try {
		$connectid = new PDO(
			'mysql:host='.$sql->{'DB_HOST'}.';port=3306',
			$sql->{'DB_USER'},
			$sql->{'DB_PASSWORD'},
			array(
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			)
		);
	}
	catch(PDOException $e){
		return 'Can´t connect to database. Please check your data and make sure that database is running!';
	}
	if ($connectid != '' && !file_exists(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_DB.php')){
		if ($sql->{'DB_KEY'} == 'AUTO' || strlen($sql->{'DB_KEY'}) != 32 ){
			$sql->{'DB_KEY'} = DaemonCommon::generatePassword(32);
		}
		if ($sql->{'DB_IV'} == 'AUTO' || strlen($sql->{'DB_IV'}) != 8 ){
			$sql->{'DB_IV'} = DaemonCommon::generatePassword(8);
		}
		if (extension_loaded('mcrypt')) {
			$td = @mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');

			// Initialize encryption
			@mcrypt_generic_init($td, $sql->{'DB_KEY'}, $sql->{'DB_IV'});

			// Encrypt string
			$encrypted = @mcrypt_generic($td, $sql->{'DB_PASSWORD'});
			@mcrypt_generic_deinit($td);
			@mcrypt_module_close($td);

			$sql->{'DB_PASSWORDENCRYPT'} = @base64_encode($encrypted);
		} else {
			return 'Error: PHP extension "mcrypt" not loaded!';
		}

		$tpl_param = array(
			'DB_HOST'		=> $sql->{'DB_HOST'},
			'DB_DATABASE'	=> $sql->{'DB_DATABASE'},
			'DB_USER'		=> $sql->{'DB_USER'},
			'DB_PASSWORD'	=> $sql->{'DB_PASSWORDENCRYPT'},
			'DB_KEY'		=> $sql->{'DB_KEY'},
			'DB_IV'			=> $sql->{'DB_IV'}
		);
		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch('tpl/EasySCP_Config_DB.php');
		$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_DB.php';
		$tpl = NULL;
		unset($tpl);

		if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
			return 'Error: Failed to write '.$confFile;
		}

		$tpl_param = array(
			'DB_KEY'		=> $sql->{'DB_KEY'},
			'DB_IV'			=> $sql->{'DB_IV'}
		);
		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch('tpl/easyscp-keys.conf');
		$confFile = DaemonConfig::$cfg->{'CONF_DIR'} . '/easyscp-keys.conf';
		$tpl = NULL;
		unset($tpl);

		if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
			return 'Error: Failed to write '.$confFile;
		}

		$handle = fopen(DaemonConfig::$cfg->{'ROOT_DIR'} . '/../setup/config.xml', "wb");
		fwrite($handle, $sql->asXML());
		fclose($handle);
	}

	return 'Ok';
}

function SetupEasySCP_Users(){

	// Mailbox user
	exec(DaemonConfig::$cmd->{'CMD_ID'}.' -g '.DaemonConfig::$cfg->{'MTA_MAILBOX_GID_NAME'}.' 2>&1', $result, $error);
	if ($error != 0){
		// echo 'MTA Gruppe existiert nicht';
		exec(DaemonConfig::$cmd->{'CMD_GROUPADD'}.' '.DaemonConfig::$cfg->{'MTA_MAILBOX_GID_NAME'}.' >> /dev/null 2>&1', $result, $error);
		unset($result);
		exec(DaemonConfig::$cmd->{'CMD_ID'}.' -g '.DaemonConfig::$cfg->{'MTA_MAILBOX_GID_NAME'}, $result, $error);
	}
	DaemonConfig::$cfg->{'MTA_MAILBOX_GID'} = $result[0];

	unset($result);
	exec(DaemonConfig::$cmd->{'CMD_ID'}.' -u '.DaemonConfig::$cfg->{'MTA_MAILBOX_UID_NAME'}.' 2>&1', $result, $error);
	if ($error != 0){
		// echo 'MTA User existiert nicht';
		exec(DaemonConfig::$cmd->{'CMD_USERADD'}.' -c vmail-user -g '.DaemonConfig::$cfg->{'MTA_MAILBOX_GID_NAME'}.' -s /bin/false -r '.DaemonConfig::$cfg->{'MTA_MAILBOX_UID_NAME'}.' >> /dev/null 2>&1', $result, $error);
		unset($result);
		exec(DaemonConfig::$cmd->{'CMD_ID'}.' -u '.DaemonConfig::$cfg->{'MTA_MAILBOX_UID_NAME'}, $result, $error);
	}
	DaemonConfig::$cfg->{'MTA_MAILBOX_MIN_UID'} = $result[0];
	DaemonConfig::$cfg->{'MTA_MAILBOX_UID'} = $result[0];

	// FCGI Master user
	exec(DaemonConfig::$cmd->{'CMD_ID'}.' -g '.DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}.' 2>&1', $result, $error);
	if ($error != 0){
		// echo 'FCGI Gruppe existiert nicht';
		exec(DaemonConfig::$cmd->{'CMD_GROUPADD'}.' -g '.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}.' '.DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}.' >> /dev/null 2>&1', $result, $error);
	}

	// create user and folder
	exec(DaemonConfig::$cmd->{'CMD_ID'}.' -u '.DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}.' 2>&1', $result, $error);
	if ($error != 0){
		// echo 'FCGI User existiert nicht';
		// exec(DaemonConfig::$cmd->{'CMD_USERADD'}.' -d '.DaemonConfig::$distro->{'PHP_STARTER_DIR'}.'/master -m -c vu-master -g '.DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}.' -s /bin/false -u '.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}.' '.DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}.' >> /dev/null 2>&1', $result, $error);
		exec(DaemonConfig::$cmd->{'CMD_USERADD'} . ' -c vu-master -d ' . DaemonConfig::$cfg->{'ROOT_DIR'} . '/gui -g ' . DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'} . ' -s /bin/false -u ' . DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'} . ' ' . DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'} . ' >> /dev/null 2>&1', $result, $error);
	} else {
		// echo 'FCGI User existiert';
		DaemonCommon::systemCreateDirectory(DaemonConfig::$distro->{'PHP_STARTER_DIR'}.'/master', DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, 0755);
	}

	DaemonConfig::Save();

	return 'Ok';
}

function EasySCP_Directories(){
	if (!DaemonCommon::systemCreateDirectory(DaemonConfig::$distro->{'APACHE_WWW_DIR'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0755)){
		return 'Error: Failed to create "APACHE_WWW_DIR": '.DaemonConfig::$distro->{'APACHE_WWW_DIR'};
	}
	if (!DaemonCommon::systemCreateDirectory(DaemonConfig::$distro->{'APACHE_TRAFFIC_LOG_DIR'}, DaemonConfig::$distro->{'APACHE_USER'}, DaemonConfig::$distro->{'APACHE_GROUP'}, 0755)){
		return 'Error: Failed to create "APACHE_TRAFFIC_LOG_DIR": '.DaemonConfig::$distro->{'APACHE_TRAFFIC_LOG_DIR'};
	}

	if (!DaemonCommon::systemCreateDirectory(DaemonConfig::$distro->{'MTA_VIRTUAL_CONF_DIR'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0755)){
		return 'Error: Failed to create "MTA_VIRTUAL_CONF_DIR": '.DaemonConfig::$distro->{'MTA_VIRTUAL_CONF_DIR'};
	}
	if (!DaemonCommon::systemCreateDirectory(DaemonConfig::$distro->{'MTA_VIRTUAL_MAIL_DIR'}, DaemonConfig::$cfg->{'MTA_MAILBOX_UID_NAME'}, DaemonConfig::$cfg->{'MTA_MAILBOX_GID_NAME'}, 0755)){
		return 'Error: Failed to create "MTA_VIRTUAL_MAIL_DIR": '.DaemonConfig::$distro->{'MTA_VIRTUAL_MAIL_DIR'};
	}

	if (!DaemonCommon::systemCreateDirectory(DaemonConfig::$cfg->{'LOG_DIR'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0755)){
		return 'Error: Failed to create "LOG_DIR": '.DaemonConfig::$cfg->{'LOG_DIR'};
	}

	if (!DaemonCommon::systemCreateDirectory(DaemonConfig::$cfg->{'SRV_TRAFF_LOG_DIR'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0700)){
		return 'Error: Failed to create "SRV_TRAFF_LOG_DIR": '.DaemonConfig::$cfg->{'SRV_TRAFF_LOG_DIR'};
	}

	if (!DaemonCommon::systemCreateDirectory(DaemonConfig::$distro->{'BACKUP_FILE_DIR'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0755)){
		return 'Error: Failed to create "BACKUP_FILE_DIR": '.DaemonConfig::$distro->{'BACKUP_FILE_DIR'};
	}

	if (!DaemonCommon::systemCreateDirectory(DaemonConfig::$distro->{'PHP_STARTER_DIR'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0755)){
		return 'Error: Failed to create "PHP_STARTER_DIR": '.DaemonConfig::$distro->{'PHP_STARTER_DIR'};
	}

	$xml = simplexml_load_file(DaemonConfig::$cfg->{'ROOT_DIR'} . '/../setup/config.xml');
	if ($xml->{'AWStats'} == '_yes_'){
		if (!DaemonCommon::systemCreateDirectory(DaemonConfig::$distro->{'AWSTATS_CACHE_DIR'}, DaemonConfig::$distro->{'APACHE_USER'}, DaemonConfig::$distro->{'APACHE_GROUP'}, 0755)){
			return 'Error: Failed to create "AWSTATS_CACHE_DIR": '.DaemonConfig::$distro->{'AWSTATS_CACHE_DIR'};
		}
		DaemonConfig::$cfg->{'AWSTATS_ACTIVE'} = 'yes';
	} else {
		DaemonConfig::$cfg->{'AWSTATS_ACTIVE'} = 'no';
	}

	unset($xml);

	DaemonConfig::Save();

	return 'Ok';
}

function EasySCP_main_configuration_file(){
	$xml = simplexml_load_file(DaemonConfig::$cfg->{'ROOT_DIR'} . '/../setup/config.xml');

	DaemonConfig::$cfg->{'BuildDate'} = file_get_contents(DaemonConfig::$cfg->{'CONF_DIR'} . '/BUILD');
	DaemonConfig::$cfg->{'Version'} = file_get_contents(DaemonConfig::$cfg->{'CONF_DIR'} . '/VERSION');
	DaemonConfig::$cfg->{'DistName'} = $xml->{'DistName'};
	DaemonConfig::$cfg->{'DistVersion'} = $xml->{'DistVersion'};
	DaemonConfig::$cfg->{'DEFAULT_ADMIN_ADDRESS'} = $xml->{'PANEL_MAIL'};
	DaemonConfig::$cfg->{'SERVER_HOSTNAME'} = $xml->{'HOST_FQHN'};
	DaemonConfig::$cfg->{'BASE_SERVER_IP'} = $xml->{'HOST_IP'};
	DaemonConfig::$cfg->{'BASE_SERVER_IPv6'} = $xml->{'HOST_IPv6'};
	DaemonConfig::$cfg->{'BASE_SERVER_VHOST'} = $xml->{'HOST_NAME'};

	DaemonConfig::$cfg->{'DATABASE_HOST'} = $xml->{'DB_HOST'};
	DaemonConfig::$cfg->{'DATABASE_NAME'} = $xml->{'DB_DATABASE'};
	if (extension_loaded('mcrypt')) {
		$td = @mcrypt_module_open(MCRYPT_BLOWFISH, '', MCRYPT_MODE_CBC, '');

		// Initialize encryption
		@mcrypt_generic_init($td, $xml->{'DB_KEY'}, $xml->{'DB_IV'});

		// Encrypt string
		$encrypted = @mcrypt_generic($td, $xml->{'DB_PASSWORD'});
		@mcrypt_generic_deinit($td);
		@mcrypt_module_close($td);

		DaemonConfig::$cfg->{'DATABASE_PASSWORD'} = @base64_encode($encrypted);
	} else {
		throw new Exception('Error: PHP extension "mcrypt" not loaded!');
	}
	DaemonConfig::$cfg->{'DATABASE_USER'} = $xml->{'DB_USER'};
	DaemonConfig::$cfg->{'PHP_TIMEZONE'} = $xml->{'Timezone'};
	DaemonConfig::$cfg->{'SECONDARY_DNS'} = $xml->{'Secondary_DNS'};
	if ($xml->{'LocalNS'} == '_yes_'){
		DaemonConfig::$cfg->{'LOCAL_DNS_RESOLVER'} = 'yes';
	} else {
		DaemonConfig::$cfg->{'LOCAL_DNS_RESOLVER'} = 'no';
	}
	if ($xml->{'MySQL_Prefix'} != 'none'){
		DaemonConfig::$cfg->{'MYSQL_PREFIX'} = 'yes';
		DaemonConfig::$cfg->{'MYSQL_PREFIX_TYPE'} = $xml->{'MySQL_Prefix'};
	}

	unset($xml);

	switch(DaemonConfig::$cfg->{'DistName'}) {
		case 'Debian':
			DaemonConfig::$cfg->{'MTA_SSL_STATUS'} = 1;
			break;
		default:
			DaemonConfig::$cfg->{'MTA_SSL_STATUS'} = 0;
	}

	DaemonConfig::Save();

	return DaemonConfig::SaveOldConfig();
	// return 'Ok';
}

function EasySCP_database(){
	$connectid = '';
	try {
		$connectid = new PDO(
			'mysql:host='.DaemonConfig::$cfg->{'DATABASE_HOST'}.';port=3306',
			DaemonConfig::$cfg->{'DATABASE_USER'},
			DB::decrypt_data(DaemonConfig::$cfg->{'DATABASE_PASSWORD'}),
			array(
				PDO::ATTR_PERSISTENT => true,
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
			)
		);
	}
	catch(PDOException $e){
		return 'Can´t connect to database. Please check your data and make sure that database is running!';
	}
	if ($connectid != ''){
		System_Daemon::debug('Create EasySCP DB if not exists');

		$query = "
			CREATE DATABASE IF NOT EXISTS ".DaemonConfig::$cfg->{'DATABASE_NAME'}."
  			DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
  		";

		$connectid->query($query)->closeCursor();
	}

	System_Daemon::debug('Import EasySCP DB');

	if(file_exists(DaemonConfig::$cfg->{'CONF_DIR'} . '/database/database.sql')) {
		DB::query(file_get_contents(DaemonConfig::$cfg->{'CONF_DIR'} . '/database/database.sql'))->closeCursor();
		return 'Ok';
	} else {
		return 'Error: Required file ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/database/database.sql not found';
	}
}

function EasySCP_default_SQL_data(){
	$xml = simplexml_load_file(DaemonConfig::$cfg->{'ROOT_DIR'} . '/../setup/config.xml');

	System_Daemon::debug('Inserting primary admin account data...');

	$sql_param = array(
		':admin_name'		=> $xml->{'PANEL_ADMIN'},
		':admin_pass'		=> md5($xml->{'PANEL_PASS'}),
		':domain_created'	=> time(),
		':email'			=> $xml->{'PANEL_MAIL'}
	);
	$sql_query = "
		INSERT INTO
			admin (admin_id, admin_name, admin_pass, admin_type, domain_created, email)
		VALUES
			('1', :admin_name, :admin_pass, 'admin', :domain_created, :email)
		ON DUPLICATE KEY UPDATE
			admin_name = :admin_name, admin_pass = :admin_pass, domain_created = :domain_created, email = :email;
	";

	DB::prepare($sql_query);
	DB::execute($sql_param)->closeCursor();

	DB::query("INSERT INTO user_gui_props (id, user_id) values (1,1) ON DUPLICATE KEY UPDATE user_id = '1';")->closeCursor();

	System_Daemon::debug('Inserting primary Ip data...');

	$sql_param = array(
		':ip_number'	=> $xml->{'HOST_IP'},
		':ip_number_v6'	=> $xml->{'HOST_IPv6'},
		':ip_domain'	=> $xml->{'HOST_FQHN'},
		':ip_alias'		=> $xml->{'HOST_FQHN'}
	);
	$sql_query = "
		INSERT INTO
			server_ips (ip_id, ip_number, ip_number_v6, ip_domain, ip_alias)
		VALUES
			('1', :ip_number, :ip_number_v6, :ip_domain, :ip_alias)
		ON DUPLICATE KEY UPDATE
			ip_number = :ip_number, ip_number_v6 = :ip_number_v6, ip_domain = :ip_domain, ip_alias = :ip_alias;
	";

	DB::prepare($sql_query);
	DB::execute($sql_param)->closeCursor();

	unset($xml);

	return 'Ok';
}

function EasySCP_create_mta_db(){
	System_Daemon::debug('Create MTA Database');

	if(file_exists(DaemonConfig::$cfg->{'CONF_DIR'} . '/database/mail.sql')) {
		DB::query(file_get_contents(DaemonConfig::$cfg->{'CONF_DIR'} . '/database/mail.sql'))->closeCursor();
		DB::setDatabase();
		return 'Ok';
	} else {
		return 'Error: Required file ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/database/mail.sql not found';
	}
}

function EasySCP_create_pma_db(){
	System_Daemon::debug('Create PhpMyAdmin Database');

	if(file_exists(DaemonConfig::$cfg->{'CONF_DIR'} . '/database/phpmyadmin.sql')) {
		DB::query(file_get_contents(DaemonConfig::$cfg->{'CONF_DIR'} . '/database/phpmyadmin.sql'))->closeCursor();
		DB::setDatabase();
		return 'Ok';
	} else {
		return 'Error: Required file ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/database/phpmyadmin.sql not found';
	}
}

function EasySCP_create_roundcube_db(){
	System_Daemon::debug('Create Roundcube Database');

	if(file_exists(DaemonConfig::$cfg->{'CONF_DIR'} . '/database/roundcube.sql')) {
		DB::query(file_get_contents(DaemonConfig::$cfg->{'CONF_DIR'} . '/database/roundcube.sql'))->closeCursor();
		DB::setDatabase();
		return 'Ok';
	} else {
		return 'Error: Required file ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/database/roundcube.sql not found';
	}
}

function EasySCP_create_pdns_db(){
	System_Daemon::debug('Create PowerDNS Database');

	if(file_exists(DaemonConfig::$cfg->{'CONF_DIR'} . '/database/powerdns.sql')) {
		DB::query(file_get_contents(DaemonConfig::$cfg->{'CONF_DIR'} . '/database/powerdns.sql'))->closeCursor();
		DB::setDatabase();
		return 'Ok';
	} else {
		return 'Error: Required file ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/database/powerdns.sql not found';
	}
}

function EasySCP_system_resolver(){
	if(DaemonConfig::$cfg->{'LOCAL_DNS_RESOLVER'} == 'yes'){
		exec("grep '127.0.0.1' /etc/resolv.conf 2>&1", $result, $error);
		if(!isset($result[0])){
			exec("sed -i'*.bak' '0,/nameserver\(.*\)$/s//nameserver 127.0.0.1\\nnameserver\\1/' /etc/resolv.conf", $result, $error);
		}
	}

	return 'Ok';
}

function EasySCP_crontab_file(){
	/**
	$tpl_param = array(
		'ROOT_DIR'		=> DaemonConfig::$cfg->{'ROOT_DIR'},
		'LOG_DIR'		=> DaemonConfig::$cfg->{'LOG_DIR'},
		'RKHUNTER'		=> DaemonConfig::$cmd->{'CMD_RKHUNTER'},
		'RKHUNTER_LOG'	=> DaemonConfig::$distro->{'RKHUNTER_LOG'}
	);

	$tpl = DaemonCommon::getTemplate($tpl_param);
	$config = $tpl->fetch('cron.d/parts/easyscp');
	$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/cron.d/working/easyscp';
	$tpl = NULL;
	unset($tpl);

	if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
		return 'Error: Failed to write '.$confFile;
	}

	exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/cron.d/working/easyscp /etc/cron.d/easyscp', $result, $error);
	 */
	// if (send_request('160 SYSTEM cron '.$_SESSION['user_id'])){
	DaemonSystem::Start('cron 1');

	return 'Ok';
}

function EasySCP_Powerdns_main_configuration_file(){
	$CreatePDNSPass = DaemonConfigDNS::CreatePDNSPass();
	if ($CreatePDNSPass !== true){
		return $CreatePDNSPass;
	}

	$SavePDNSConfig = DaemonConfigDNS::SavePDNSConfig();
	if ($SavePDNSConfig !== true){
		return $SavePDNSConfig;
	}

	// Creating Panel Main DNS config
	$sql_param = array(
		':domain_name'		=> DaemonConfig::$cfg->{'SERVER_HOSTNAME'},
		':easyscp_domain_id'=> '0'
	);

	$sql_query = "
			INSERT INTO
				powerdns.domains (easyscp_domain_id, name, type)
			VALUES
				(:easyscp_domain_id, :domain_name, 'NATIVE')
			 ON DUPLICATE KEY UPDATE
			 	name = :domain_name;
		";

	DB::prepare($sql_query);
	DB::execute($sql_param)->closeCursor();

	$dmn_dns_id	= 1;
	$dmn_name	= DaemonConfig::$cfg->{'SERVER_HOSTNAME'};
	$dmn_ip		= DaemonConfig::$cfg->BASE_SERVER_IP;

	$sql_param = array();

	$sql_param[] = array(
		'domain_id'		=> $dmn_dns_id,
		'domain_name'	=> $dmn_name,
		'domain_type'	=> 'SOA',
		'domain_content'=> 'ns1.'.$dmn_name.'. '.DaemonConfig::$cfg->{'DEFAULT_ADMIN_ADDRESS'}.' '.time().' 12000 1800 604800 86400',
		'domain_ttl'	=> '86400',
		'domain_prio'	=> Null
	);

	$sql_param[] = array(
		'domain_id'		=> $dmn_dns_id,
		'domain_name'	=> 'ns1.'.$dmn_name,
		'domain_type'	=> 'A',
		'domain_content'=> $dmn_ip,
		'domain_ttl'	=> '28800',
		'domain_prio'	=> NULL
	);

	$sql_param[] = array(
		'domain_id'		=> $dmn_dns_id,
		'domain_name'	=> $dmn_name,
		'domain_type'	=> 'NS',
		'domain_content'=> 'ns1.'.$dmn_name,
		'domain_ttl'	=> '28800',
		'domain_prio'	=> NULL
	);

	$sql_param[] = array(
		'domain_id'		=> $dmn_dns_id,
		'domain_name'	=> 'ns.'.$dmn_name,
		'domain_type'	=> 'CNAME',
		'domain_content'=> 'ns1.'.$dmn_name,
		'domain_ttl'	=> '7200',
		'domain_prio'	=> NULL
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
		'domain_name'	=> DaemonConfig::$cfg->{'BASE_SERVER_VHOST'},
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
	unset($stmt);

	return 'Ok';
}

function EasySCP_Apache_php_configuration(){
	// Loading the template from the /etc/easyscp/apache directory, Building the new configuration file
	// and Storing the new file

	// TODO Datei muss nicht mehr bearbeitet werden, kann also direkt kopiert werden. Das noch anpassen
	$tpl_param = array(
		//'PHP_VERSION'	=> DaemonConfig::$cfg->PHP_VERSION
	);

	$tpl = DaemonCommon::getTemplate($tpl_param);
	$config = $tpl->fetch('apache/fcgid_easyscp.conf');
	$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/apache/working/fcgid_easyscp.conf';
	$tpl = NULL;
	unset($tpl);

	if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
		return 'Error: Failed to write '.$confFile;
	}

	// Installing the new file
	exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/apache/working/fcgid_easyscp.conf '.DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/fcgid_easyscp.conf', $result, $error);

	switch(DaemonConfig::$cfg->{'DistName'}){
		case 'CentOS':
			break;
		default:
			exec(DaemonConfig::$cmd->{'CMD_CP'}.' -f '.DaemonConfig::$cfg->{'CONF_DIR'}.'/apache/fcgid_easyscp.load '.DaemonConfig::$cfg->{'CONF_DIR'}.'/apache/working/fcgid_easyscp.load', $result, $error);
			DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/apache/working/fcgid_easyscp.load', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
			exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/apache/working/fcgid_easyscp.load '.DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/fcgid_easyscp.load', $result, $error);
	}

	return 'Ok';
}

function EasySCP_Apache_main_vhost_file(){
	return 'Ok';
}

function EasySCP_Apache_AWStats_vhost_file(){
	return 'Ok';
}

function EasySCP_MTA_configuration_files(){

	if (DaemonConfig::$cfg->{'MTA_SSL_STATUS'} == '1'){
		// Generate SSL Certificate
		switch(DaemonConfig::$cfg->{'DistName'}){
			case 'Debian':
				if (file_exists("/etc/ssl/certs/ssl-cert-snakeoil.pem")){
					exec(DaemonConfig::$cmd->{'CMD_RM'}.' /etc/ssl/certs/ssl-cert-snakeoil.pem', $result, $error);
				}
				if (file_exists("/etc/ssl/private/ssl-cert-snakeoil.key")){
					exec(DaemonConfig::$cmd->{'CMD_RM'}.' /etc/ssl/private/ssl-cert-snakeoil.key', $result, $error);
				}
				exec('make-ssl-cert generate-default-snakeoil', $result, $error);
				break;
			default:
		}
	}

	$SaveMTAConfig = DaemonConfigMail::SaveMTAConfig();
	if ($SaveMTAConfig !== true){
		return $SaveMTAConfig;
	}

	return 'Ok';
}

function EasySCP_ProFTPd_configuration_file(){
	//TODO Übernahme der Daten aus der Setup config.xml (FTP user und Pass) implementieren
	//$xml = simplexml_load_file(DaemonConfig::$cfg->{'ROOT_DIR'} . '/../setup/config.xml');

	$CreateProFTPdPass = DaemonConfigFTP::CreateProFTPdPass();
	if ($CreateProFTPdPass !== true){
		return $CreateProFTPdPass;
	}

	$SaveProFTPdConfig = DaemonConfigFTP::SaveProFTPdConfig();
	if ($SaveProFTPdConfig !== true){
		return $SaveProFTPdConfig;
	}

	return 'Ok';
}

function EasySCP_init_scripts(){

	switch(DaemonConfig::$cfg->{'DistName'}){
		case 'CentOS':
			DaemonCommon::systemSetFilePermissions(DaemonConfig::$cmd->{'SRV_EASYSCPC'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0755);
			DaemonCommon::systemSetFilePermissions(DaemonConfig::$cmd->{'SRV_EASYSCPD'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0755);

			exec('/sbin/chkconfig easyscp_control on', $result, $error);
			exec('/sbin/chkconfig easyscp_daemon on', $result, $error);

			break;
		default:
			DaemonCommon::systemSetFilePermissions(DaemonConfig::$cmd->{'SRV_EASYSCPC'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0755);
			DaemonCommon::systemSetFilePermissions(DaemonConfig::$cmd->{'SRV_EASYSCPD'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0755);

			if (DaemonConfig::$cfg->{'DistName'} == 'Debian' && DaemonConfig::$cfg->{'DistVersion'} == '8' ){
				exec('/bin/systemctl enable easyscp_control', $result, $error);
				exec('/bin/systemctl enable easyscp_daemon', $result, $error);
			} else {
				exec('/usr/sbin/update-rc.d -f easyscp_control remove', $result, $error);
				exec('/usr/sbin/update-rc.d -f easyscp_daemon remove', $result, $error);

				exec('/usr/sbin/update-rc.d easyscp_control defaults 99', $result, $error);
				exec('/usr/sbin/update-rc.d easyscp_daemon defaults 99', $result, $error);
			}
	}

	return 'Ok';
}

function GUI_PHP(){
	// Create the fcgi directories tree for the GUI if it doesn't exists
	DaemonCommon::systemCreateDirectory(DaemonConfig::$distro->{'PHP_STARTER_DIR'}.'/master', DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, 0555);
	DaemonCommon::systemCreateDirectory(DaemonConfig::$distro->{'PHP_STARTER_DIR'}.'/master/php', DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, 0555);

	// PHP5 Starter script
	// Loading the template from /etc/easyscp/php/parts/master, Building the new file
	// and Storing the new file in the working directory

	$tpl_param = array(
		'PHP_STARTER_DIR'	=> DaemonConfig::$distro->{'PHP_STARTER_DIR'},
		'DOMAIN_NAME'		=> 'master',
		'GUI_ROOT_DIR'		=> DaemonConfig::$cfg->{'GUI_ROOT_DIR'},
		'PHP_FASTCGI_BIN'	=> DaemonConfig::$distro->{'PHP_FASTCGI_BIN'}
	);

	$tpl = DaemonCommon::getTemplate($tpl_param);
	$config = $tpl->fetch('php/parts/master.php-fcgi-starter.tpl');
	$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/php/working/master.php-fcgi-starter';
	$tpl = NULL;
	unset($tpl);

	if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, 0550 )){
		return 'Error: Failed to write '.$confFile;
	}

	// Install the new file
	exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/php/working/master.php-fcgi-starter '.DaemonConfig::$distro->{'PHP_STARTER_DIR'}.'/master/php-fcgi-starter', $result, $error);


	// PHP5 php.ini file
	// Loading the template from /etc/easyscp/php/parts/master/php, Building the new file
	// and Store the new file in working directory

	$tpl_param = array(
		'CHKROOTKIT_LOG'		=> DaemonConfig::$distro->{'CHKROOTKIT_LOG'},
		'CONF_DIR'				=> DaemonConfig::$cfg->{'CONF_DIR'},
		'DEFAULT_ADMIN_ADDRESS'	=> DaemonConfig::$cfg->{'DEFAULT_ADMIN_ADDRESS'},
		'DOMAIN_NAME'			=> 'gui',
		'EASYSCPC_DIR'			=> dirname(DaemonConfig::$cfg->{'SOCK_EASYSCPC'}),
		'EASYSCPD_DIR'			=> dirname(DaemonConfig::$cfg->{'SOCK_EASYSCPD'}),
		'MAIL_DMN'				=> idn_to_ascii(DaemonConfig::$cfg->{'BASE_SERVER_VHOST'}),
		'OTHER_ROOTKIT_LOG'		=> (DaemonConfig::$distro->{'OTHER_ROOTKIT_LOG'} != '') ? DaemonConfig::$distro->{'OTHER_ROOTKIT_LOG'} : '',
		'PEAR_DIR'				=> DaemonConfig::$distro->{'PEAR_DIR'},
		'PHP_STARTER_DIR'		=> DaemonConfig::$distro->{'PHP_STARTER_DIR'},
		'PHP_TIMEZONE'			=> DaemonConfig::$cfg->{'PHP_TIMEZONE'},
		'RKHUNTER_LOG'			=> DaemonConfig::$distro->{'RKHUNTER_LOG'},
		'WWW_DIR'				=> DaemonConfig::$cfg->{'ROOT_DIR'}
	);

	$tpl = DaemonCommon::getTemplate($tpl_param);
	$config = $tpl->fetch('php/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'} . '/master.php.ini');
	$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/php/working/master.php.ini';
	$tpl = NULL;
	unset($tpl);

	if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, 0440 )){
		return 'Error: Failed to write '.$confFile;
	}

	// Install the new file
	exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/php/working/master.php.ini '.DaemonConfig::$distro->{'PHP_STARTER_DIR'}.'/master/php/php.ini', $result, $error);

	return 'Ok';
}

function GUI_VHOST(){
	$tpl_param = array(
		'APACHE_LOG_DIR'			=> DaemonConfig::$distro->APACHE_LOG_DIR,
		'BASE_PORT'					=> 80,
		'BASE_SERVER_IP'			=> DaemonConfig::$cfg->BASE_SERVER_IP,
		'BASE_SERVER_VHOST'			=> idn_to_ascii(DaemonConfig::$cfg->{'BASE_SERVER_VHOST'}),
		'DEFAULT_ADMIN_ADDRESS'		=> DaemonConfig::$cfg->DEFAULT_ADMIN_ADDRESS,
		'GUI_ROOT_DIR'				=> DaemonConfig::$cfg->GUI_ROOT_DIR,
		'PHP_STARTER_DIR'			=> DaemonConfig::$distro->{'PHP_STARTER_DIR'},
		'SUEXEC_GID'				=> DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'} . DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'},
		'SUEXEC_UID'				=> DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'} . DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}
	);

	if (isset(DaemonConfig::$cfg->BASE_SERVER_IPv6) && DaemonConfig::$cfg->BASE_SERVER_IPv6 != ''){
		$tpl_param['BASE_SERVER_IPv6'] = DaemonConfig::$cfg->BASE_SERVER_IPv6;
	}

	$tpl = DaemonCommon::getTemplate($tpl_param);
	$config = $tpl->fetch('apache/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'} . '/00_master.conf.tpl');
	$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/apache/working/00_master.conf';
	$tpl = NULL;
	unset($tpl);

	if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
		return 'Error: Failed to write '.$confFile;
	}

	exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/apache/working/00_master.conf '.DaemonConfig::$distro->{'APACHE_SITES_DIR'}.'/00_master.conf', $result, $error);

	return 'Ok';
}

function GUI_PMA(){
	$xml = simplexml_load_file(DaemonConfig::$cfg->{'ROOT_DIR'} . '/../setup/config.xml');
	$pma = simplexml_load_file(DaemonConfig::$cfg->{'CONF_DIR'} . '/tpl/EasySCP_Config_PMA.xml');

	System_Daemon::debug('Building the new pma config file');

	$pma->{'PMA_BLOWFISH'}	= $xml->{'PMA_BLOWFISH'};
	$pma->{'PMA_USER'}		= $xml->{'PMA_USER'};
	$pma->{'PMA_PASSWORD'}	= DB::encrypt_data($xml->{'PMA_PASSWORD'});
	$pma->{'DATABASE_HOST'}	= idn_to_ascii($xml->{'DB_HOST'});
	$pma->{'TMP_DIR'}		= DaemonConfig::$cfg->{'GUI_ROOT_DIR'}.'/phptmp';

	$handle = fopen(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_PMA.xml', "wb");
	fwrite($handle, $pma->asXML());
	fclose($handle);

	DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_PMA.xml', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640);

	DaemonConfigTools::SavePMAConfig();

	System_Daemon::debug('Adding the pma control user');

	$sql_param = array(
		':DATABASE_HOST'=> idn_to_ascii(DaemonConfig::$cfg->{'DATABASE_HOST'}),
		':PMA_USER'		=> $xml->{'PMA_USER'},
		':PMA_PASSWORD'	=> $xml->{'PMA_PASSWORD'}
	);
	$sql_query = "
		GRANT USAGE ON mysql.* TO :PMA_USER@:DATABASE_HOST IDENTIFIED BY :PMA_PASSWORD;
		GRANT SELECT ON mysql.db TO :PMA_USER@:DATABASE_HOST IDENTIFIED BY :PMA_PASSWORD;
		GRANT SELECT (Host, User, Select_priv, Insert_priv, Update_priv, Delete_priv, Create_priv, Drop_priv, Reload_priv, Shutdown_priv, Process_priv, File_priv, Grant_priv, References_priv, Index_priv, Alter_priv, Show_db_priv, Super_priv, Create_tmp_table_priv, Lock_tables_priv, Execute_priv, Repl_slave_priv, Repl_client_priv) ON mysql.user TO :PMA_USER@:DATABASE_HOST IDENTIFIED BY :PMA_PASSWORD;
		GRANT SELECT ON mysql.host TO :PMA_USER@:DATABASE_HOST IDENTIFIED BY :PMA_PASSWORD;
		GRANT SELECT (Host, Db, User, Table_name, Table_priv, Column_priv) ON mysql.tables_priv TO :PMA_USER@:DATABASE_HOST IDENTIFIED BY :PMA_PASSWORD;
		GRANT SELECT, INSERT, DELETE, UPDATE, ALTER ON phpmyadmin.* TO :PMA_USER@:DATABASE_HOST IDENTIFIED BY :PMA_PASSWORD;
		FLUSH PRIVILEGES;
	";

	DB::prepare($sql_query);
	DB::execute($sql_param)->closeCursor();

	return 'Ok';
}

function GUI_RoundCube(){

	$cubeUser = 'cube';
	$cubeUserPwd = DaemonCommon::generatePassword(18);
	$cubeDBHost = idn_to_ascii(DaemonConfig::$cfg->{'DATABASE_HOST'});

	$rc = simplexml_load_file(DaemonConfig::$cfg->{'CONF_DIR'} . '/tpl/EasySCP_Config_RC.xml');

	System_Daemon::debug('Building the new roundcube config file');

	$rc->{'CUBE_USER'} = $cubeUser;
	$rc->{'CUBE_PASS'} = DB::encrypt_data($cubeUserPwd);
	$rc->{'DATABASE_HOST'} = $cubeDBHost;

	$handle = fopen(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_RC.xml', "wb");
	fwrite($handle, $rc->asXML());
	fclose($handle);

	DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'} . '/EasySCP_Config_RC.xml', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640);

	DaemonConfigTools::SaveRCConfig();

	$sql_param = array(
		':CUBE_USER'=> $cubeUser,
		':CUBE_PASS'=> $cubeUserPwd,
		':HOSTNAME'	=> $cubeDBHost
	);
	$sql_query = "
		GRANT SELECT, UPDATE ON mail.users TO :CUBE_USER@:HOSTNAME IDENTIFIED BY :CUBE_PASS;
		GRANT ALL PRIVILEGES ON roundcubemail.* TO :CUBE_USER@:HOSTNAME IDENTIFIED BY :CUBE_PASS;
		FLUSH PRIVILEGES;
	";

	DB::prepare($sql_query);
	DB::execute($sql_param)->closeCursor();

	return 'Ok';
}

function Set_daemon_permissions(){
	DaemonCommon::systemSetSystemPermissions();
	return 'Ok';
}

function Set_gui_permissions(){
	DaemonCommon::systemSetGUIPermissions();

	switch(DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'}){
		case 'CentOS_6':
			exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/iptables/iptables /etc/sysconfig/iptables', $result, $error);

			exec(DaemonConfig::$cmd->SRV_IPTABLES . ' restart >> /dev/null 2>&1', $result, $error);
			break;
		case 'Debian_8':
			exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/iptables/rules.v4 /etc/iptables/rules.v4', $result, $error);
			exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/iptables/rules.v6 /etc/iptables/rules.v6', $result, $error);

			exec('service netfilter-persistent restart >> /dev/null 2>&1', $result, $error);
			break;
		default:
			exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/iptables/rules.v4 /etc/iptables/rules.v4', $result, $error);
			exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/iptables/rules.v6 /etc/iptables/rules.v6', $result, $error);

			exec(DaemonConfig::$cmd->SRV_IPTABLES . ' restart >> /dev/null 2>&1', $result, $error);

	}

	return 'Ok';
}

function Starting_all_services(){
	$services = array('SRV_DNS', 'SRV_FTPD', 'SRV_CLAMD', 'SRV_POSTGREY', 'SRV_POLICYD_WEIGHT', 'SRV_AMAVIS', 'SRV_DOVECOT', 'SRV_MTA');

	foreach($services as $service){
		if ( DaemonConfig::$cmd->$service != 'no' && file_exists(DaemonConfig::$cmd->$service)){
			System_Daemon::debug('Found Service ' . DaemonConfig::$cmd->$service);
			System_Daemon::debug('Starting Service ' . DaemonConfig::$cmd->$service);
			exec(DaemonConfig::$cmd->$service . ' start >> /dev/null 2>&1', $result, $error);
		} else {
			if(DaemonConfig::$cmd->$service != 'no'){
				System_Daemon::debug('Service ' . $service . ': ' . DaemonConfig::$cmd->$service . ' does not exist');
			}
		}
	}

	return 'Ok';
}

function Rkhunter(){
	return 'Ok';
}

function System_cleanup(){

	exec(DaemonConfig::$cmd->{'CMD_RM'}.' -f '.DaemonConfig::$cfg->{'CONF_DIR'}.'/*/*/empty-file', $result, $error);
	exec(DaemonConfig::$cmd->{'CMD_RM'}.' -f '.DaemonConfig::$cfg->{'CONF_DIR'}.'/*/*/*/empty-file', $result, $error);

	switch(DaemonConfig::$cfg->{'DistName'}){
		case 'CentOS':
			// Remove Setup vhost
			unlink(DaemonConfig::$distro->{'APACHE_CUSTOM_SITES_CONFIG_DIR'}.'/easyscp-setup.conf');

			// Disable PHP modul
			if (file_exists(DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/php.conf')){
				// exec('mv '.DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/php.conf '.DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/php.conf.disable >> /dev/null 2>&1');
				exec('cat /dev/null > '.DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/php.conf >> /dev/null 2>&1');
			}

			// TODO Pruefen ob danach SSL noch geht, scheinbar wird das SSL Modul nicht immer geladen
			// Disable SSL modul
			if (file_exists(DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/ssl.conf')){
				// exec('mv '.DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/ssl.conf '.DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/ssl.conf.disable >> /dev/null 2>&1');
				exec('cat /dev/null > '.DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/ssl.conf >> /dev/null 2>&1');
			}

			// Disable Welcome page
			if (file_exists(DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/welcome.conf')){
				// exec('mv '.DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/welcome.conf '.DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/welcome.conf.disable >> /dev/null 2>&1');
				exec('cat /dev/null > '.DaemonConfig::$distro->{'APACHE_MODS_DIR'}.'/welcome.conf >> /dev/null 2>&1');
			}

			break;
		default:
			// Disable Setup vhost
			exec('a2dissite easyscp-setup.conf');

			// Remove Setup vhost
			unlink(DaemonConfig::$distro->{'APACHE_SITES_DIR'}.'/easyscp-setup.conf');

			// Enable GUI vhost (Debian like distributions)
			exec('a2ensite 00_master.conf');

			// Apache Module

			// Dieses Modul ermöglicht die Ausführung von CGI-Skripten in Abhängigkeit von Medientypen und Anfragemethoden.
			//exec('a2enmod actions');

			// Execution of CGI scripts using an external CGI daemon
			//exec('a2enmod cgid');

			// Enabling mod_expires
			// Controls the setting of the Expires HTTP header and the max-age directive of the Cache-Control HTTP header in server responses.
			// The expiration date can set to be relative to either the time the source file was last modified, or to the time of the client access.
			exec('a2enmod expires');

			// Disable default fcgid modules loaders to avoid conflicts with EasySCP loaders
			exec('a2dismod fcgid');

			// Enable EasySCP fcgi loader
			exec('a2enmod fcgid_easyscp');

			// Enabling mod_headers
			// Provides directives to control and modify HTTP request and response headers. Headers can be merged, replaced or removed.
			exec('a2enmod headers');

			// Enabling mod proxy
			// HTTP/1.1 proxy/gateway server
			exec('a2enmod proxy');

			// Enabling mod rewrite
			// Provides a rule-based rewriting engine to rewrite requested URLs on the fly
			exec('a2enmod rewrite');

			// Enabling mod ssl
			// Strong cryptography using the Secure Sockets Layer (SSL) and Transport Layer Security (TLS) protocols
			exec('a2enmod ssl');

			// Enabling mod suexec
			// Allows CGI scripts to run as a specified user and Group
			exec('a2enmod suexec');
	}

	return 'Finished'.DaemonConfig::$cfg->{'BASE_SERVER_VHOST'};
}

function Setup_Finishing(){
	//exec(DaemonConfig::$cmd->{'SRV_HTTPD'} . ' restart');
	$ControlConnect = DaemonCommon::ControlConnect('ApacheRestart');
	if ($ControlConnect !== true){
		return $ControlConnect . ' System is unable to reload Apache config.<br />';
	}

	System_Daemon::info('Restart Daemon.');

	$ControlConnect = DaemonCommon::ControlConnect('DaemonRestart');
	if ($ControlConnect !== true){
		return $ControlConnect . ' System is unable to restart EasySCP Daemon.<br />';
	}

	unlink(DaemonConfig::$cfg->{'ROOT_DIR'}.'/daemon/DaemonCoreSetup.php');
	exec(DaemonConfig::$cmd->{'CMD_RM'}.' -rf '.DaemonConfig::$cfg->{'ROOT_DIR'}.'/../setup >> /dev/null 2>&1');

	// SocketHandler::Close();
	// System_Daemon::restart();
}
?>
