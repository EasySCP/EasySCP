#!/usr/bin/php -q

<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2015 by Easy Server Control Panel - http://www.easyscp.net
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

function getDiskUsage($path, $excludeBackups = true) {
	$usage = null;
	if (file_exists($path)) {
		$usage = null;
		if ($excludeBackups) {
			exec(DaemonConfig::$cmd->{'CMD_DU'} .' -bcs ' . $path ,$usage);
		} else {
			exec(DaemonConfig::$cmd->{'CMD_DU'} .' -bcs --exclude '. $path .'/backups ' . $path ,$usage);
		}
		$diskUsage = preg_split('/\s/', $usage[0]);
		return $diskUsage[0];
	}
	
	return 0;
}

function getDBUsage($domainID){
	$dbSize = 0;
	// get database information for given domain
	$sql_param = array(
			':domain_id' => $domainID
	);
	$sql_query = "
			SELECT
				sqld_id, sqld_name
			FROM
				sql_database
			WHERE
				domain_id = :domain_id
		";
	DB::prepare($sql_query);
	$sqlData = DB::execute($sql_param);

	// get usage for each database
	$sql_query = '
			SELECT 
				sum(data_length + index_length) size
			FROM 
				information_schema.tables 
			WHERE 
				table_schema = :table_schema
			GROUP BY 
				table_schema
	';
	while ($row = $sqlData->fetch()) {
		$sql_param = array(
			':table_schema' => $row['sqld_name']
		);
		DB::prepare($sql_query);
		$sqlSize = DB::execute($sql_param,true);
		$dbSize += $sqlSize[0];
	}
	return $dbSize;
}
$mailUsage = 0;
$webUsage = 0;
$dbUsage = 0;

$sql_query = "
		SELECT 
			domain_id, domain_name, domain_disk_countbackup 
		FROM 
			domain 
		WHERE 
			status = 'ok'";
$domainData = DB::query($sql_query);

while ($row = $domainData->fetch()) {
	$wwwPath = DaemonConfig::$cfg->{'APACHE_WWW_DIR'} . '/' . $row['domain_name'];
	$mailPath = DaemonConfig::$cfg->{'MTA_VIRTUAL_MAIL_DIR'} . '/' . $row['domain_name'];
	
	if ($row['domain_disk_countbackup']!=='yes'){
		$webUsage = getDiskUsage($wwwPath, true);
	} else {
		$webUsage = getDiskUsage($wwwPath,false);
	}
	$mailUsage = getDiskUsage($mailPath);
	$dbUsage = getDBUsage($row['domain_id']);
	
	$total = $mailUsage+$webUsage+$dbUsage;
	
	$sql_param = array(
		':domain_id' => $row['domain_id'],
		':domain_disk_usage' => $total
	);

	
	$sql_query = "
		UPDATE
			domain
		SET
			domain_disk_usage = :domain_disk_usage
		WHERE
			domain_id = :domain_id
	";

	DB::prepare($sql_query);
	DB::execute($sql_param)->closeCursor();

}
?>