#!/usr/bin/php -q

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

require_once(dirname(__FILE__).'/DaemonDummy.php');
require_once(dirname(__FILE__).'/DaemonCommon.php');
require_once(dirname(__FILE__).'/DaemonConfig.php');

$DB_BACKUP_FILE = DaemonConfig::$distro->{'BACKUP_FILE_DIR'}.'/EasySCP_' . date('Ymd') . '.sql';
$ETC_BACKUP_FILE = DaemonConfig::$distro->{'BACKUP_FILE_DIR'}.'/EasySCP_' . date('Ymd') . '.tar';

DB::backupDatabase(DB::$DB_DATABASE, $DB_BACKUP_FILE);
if (file_exists($DB_BACKUP_FILE)){
	DaemonBackup::Compress($DB_BACKUP_FILE);
}
if (file_exists($DB_BACKUP_FILE . '.bz2')){
	DaemonCommon::systemSetFilePermissions($DB_BACKUP_FILE . '.bz2', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640 );
}

exec(DaemonConfig::$cmd->{'CMD_TAR'} . ' --create --directory="'.DaemonConfig::$cfg->{'CONF_DIR'}.'" --file="' . $ETC_BACKUP_FILE .'" . 2>> ' . DaemonConfig::$cfg->{'LOG_DIR'} . '/EasySCP_Backup.log');
if (file_exists($ETC_BACKUP_FILE)){
	DaemonBackup::Compress($ETC_BACKUP_FILE);
}
if (file_exists($ETC_BACKUP_FILE . '.bz2')){
	DaemonCommon::systemSetFilePermissions($ETC_BACKUP_FILE . '.bz2', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0640 );
}

DaemonBackup::CleanUp(DaemonConfig::$distro->{'BACKUP_FILE_DIR'});
?>