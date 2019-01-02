#!/usr/bin/php -q

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

require_once(dirname(__FILE__).'/DaemonDummy.php');
require_once(dirname(__FILE__).'/DaemonCommon.php');
require_once(dirname(__FILE__).'/DaemonConfig.php');

System_Daemon::debug('Starting "CronDomainBackup".');

$sql_query = "
	SELECT
		domain_id, domain_name, domain_gid, domain_uid, status, allowbackup
	FROM
		domain
	WHERE
		status = 'ok'
	ORDER BY
		domain_id;
";

foreach (DB::query($sql_query) as $row) {
	// System_Daemon::info(var_dump($row));
	switch($row['allowbackup']){
		case 'dmn':
			System_Daemon::debug('Starting "Domain backup" subprocess.');
			DaemonBackup::DomainData($row);
			System_Daemon::debug('Finished "Domain backup" subprocess.');
			break;
		case 'sql':
			System_Daemon::debug('Starting "SQL backup" subprocess.');
			DaemonBackup::DomainDB($row);
			System_Daemon::debug('Finished "SQL backup" subprocess.');
			break;
		case 'full':
			System_Daemon::debug('Starting "Full backup" subprocess.');
			DaemonBackup::DomainDB($row);
			DaemonBackup::DomainData($row);
			System_Daemon::debug('Finished "Full backup" subprocess.');
			break;
		case 'no':
			System_Daemon::debug("Nichts sichern.");
			break;
		default:
			System_Daemon::debug("Don't know what to do with " . $row['allowbackup']);
	}

	DaemonBackup::CleanUp(DaemonConfig::$distro->{'APACHE_WWW_DIR'} . '/' . $row['domain_name'] . '/backups/', 2);
}

System_Daemon::debug('Finished "CronDomainBackup".');
?>