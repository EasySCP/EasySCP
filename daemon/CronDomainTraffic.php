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

$daten = array();
$dateien = array();
$LOG_DIR = DaemonConfig::$distro->{'APACHE_TRAFFIC_LOG_DIR'}.'/';

exec(DaemonConfig::$cmd->CMD_LOGROTATE . ' --force ' . dirname(__FILE__) . '/CronDomainTraffic >> /dev/null 2>&1', $result, $error);

foreach(glob($LOG_DIR.'*.1') as $datei) {
	$domain = substr(str_replace($LOG_DIR, '', $datei), 0, -2);

	$dateien[] = $datei;

	if(!isset($daten[$domain])){
		$daten[$domain] = array();
	}

	$handle = fopen ($datei, 'r');
	while (!feof($handle)) {
		$buffer = fgets($handle);
		if ($buffer != ''){
			$temp = datumIO($buffer);

			if(isset($daten[$domain][$temp[0]])){
				$daten[$domain][$temp[0]]['IN'] += (int)$temp[1];
				$daten[$domain][$temp[0]]['OUT'] += (int)$temp[2];
			} else {
				$daten[$domain][$temp[0]] = array(
					'IN'	=> (int)$temp[1],
					'OUT'	=> (int)$temp[2]
				);
			}
		}
	}
	fclose ($handle);
}

foreach ($daten as $domain => $id){
	$domain_id = explode(".", $domain, 3);

	if (!isset($domain_id[0]) || !ctype_digit($domain_id[0])){continue;}

	$sql_query = "
		INSERT INTO
			domain_traffic (domain_id, dtraff_time, dtraff_web_in, dtraff_web_out)
		VALUES
			('".$domain_id[0]."', :dtraff_time, :dtraff_web_in, :dtraff_web_out)
		ON DUPLICATE KEY UPDATE
			dtraff_web_in = dtraff_web_in + :dtraff_web_in, dtraff_web_out = dtraff_web_out + :dtraff_web_out;
	";

	$stmt = DB::prepare($sql_query);

	foreach ($daten[$domain] as $zeit => $id){
		$sql_param = array(
			'dtraff_time'	=> $zeit,
			'dtraff_web_in'	=> (is_int($daten[$domain][$zeit]['IN'])) ? $daten[$domain][$zeit]['IN'] : 0,
			'dtraff_web_out'=> (is_int($daten[$domain][$zeit]['OUT'])) ? $daten[$domain][$zeit]['OUT'] : 0,
		);

		$stmt->execute($sql_param);
	}

	$stmt = Null;
	unset($stmt);
}

unset($datei);

foreach ($dateien as $datei){
	exec(DaemonConfig::$cmd->{'CMD_RM'} . ' -rf '.$datei);
}

function datumIO($zeile){
	$daten = explode(' ', $zeile);
	$daten[0] = str_replace('[', '', $daten[0]);
	$daten[0] = str_replace(']', '', $daten[0]);
	$daten[1] = str_replace('IN:', '', $daten[1]);
	$daten[2] = str_replace('OUT:', '', $daten[2]);
	$zeit = explode('_', $daten[0]);
	$daten[0] = mktime($zeit[3], $zeit[4], $zeit[5], $zeit[1], $zeit[2], $zeit[0]);

	// Zeitstempel auf 1 Tag abrunden
	$daten[0] = $daten[0] - ($daten[0] % 86400) - date('Z');

	return $daten;
}
?>