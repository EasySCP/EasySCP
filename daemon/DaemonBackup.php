<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2018 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * EasySCP Daemon Backup functions
 */

class DaemonBackup {

	/**
	 * Alte Dateien/Backups entfernen
	 *
	 * Mit dieser Funktion kann man ältere Dateien z.b. Backups entfernen lassen.
	 *
	 * @param string $pfad Der gewünschte Pfad in welchem aufgeräumt werden soll.
	 * @param int $alter Das gewünschte Alter der Dateien. Default sind 14 Tage wenn nichts übergeben wurde.
	 * @return void.
	 */
	public static function CleanUp($pfad, $alter = 14){
		exec(DaemonConfig::$cmd->{'CMD_FIND'}.' '.$pfad.' -maxdepth 1 -type f -mtime +'.$alter.' -print | xargs -r '.DaemonConfig::$cmd->{'CMD_RM'});
	}

	/**
	 * Datei komprimieren
	 *
	 * Mit dieser Funktion kann man Dateien komprimieren lassen. Default ist dabei aktuell das BZIP2 Verfahren.
	 *
	 * @param string $datei Der Name und Pfad der Datei, welche komprimiert werden soll.
	 * @return void.
	 */
	public static function Compress($datei){
		exec(DaemonConfig::$cmd->{'CMD_BZIP'} . ' --force ' . $datei);
	}

	/**
	 * Domain Datenbanken sichern
	 *
	 * Mit dieser Funktion kann man alle Datenbanken einer Domain sichern lassen.
	 *
	 * @param array $domainData Die Daten der Domain welche gesichert werden soll.
	 * @return void.
	 */
	public static function DomainDB($domainData){
		$sql_param = array(
			':domain_id' => $domainData['domain_id']
		);

		$sql_query = "
			SELECT
				domain_id, sqld_name, status
			FROM
				sql_database
			WHERE
				domain_id = :domain_id
			AND
				status = 'ok';
		";

		// Einzelne Schreibweise
		DB::prepare($sql_query);
		foreach (DB::execute($sql_param) as $row) {
			$DB_BACKUP_FILE = DaemonConfig::$distro->{'APACHE_WWW_DIR'} . '/' . $domainData['domain_name'] . '/backups/' . $row['sqld_name'] . '_' . date('Ymd') . '.sql';
			DB::backupDatabase($row['sqld_name'], $DB_BACKUP_FILE);
			if (file_exists($DB_BACKUP_FILE)){
				DaemonCommon::systemSetFilePermissions($DB_BACKUP_FILE, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'} . $domainData['domain_uid'], DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'} . $domainData['domain_gid'], 0644 );
				DaemonBackup::Compress($DB_BACKUP_FILE);
			}
		}
	}

	/**
	 * Domain Daten sichern
	 *
	 * Mit dieser Funktion kann man alle Daten einer Domain sichern lassen.
	 * Nicht gesichert werden die Ordner "backups", "logs" und "phptmp".
	 *
	 * @param array $domainData Die Daten der Domain welche gesichert werden soll.
	 * @return void.
	 */
	public static function DomainData($domainData){
		$DATA_BACKUP_FILE = DaemonConfig::$distro->{'APACHE_WWW_DIR'} . '/' . $domainData['domain_name'] . '/backups/' . $domainData['domain_name'] . '_' . date('Ymd') . '.tar';
		exec(DaemonConfig::$cmd->{'CMD_TAR'} . " -cf '" . $DATA_BACKUP_FILE . "' -C '" . DaemonConfig::$distro->{'APACHE_WWW_DIR'} . "/" . $domainData['domain_name'] . "/' . --exclude=backups --exclude=logs --exclude=phptmp");
		if (file_exists($DATA_BACKUP_FILE)){
			DaemonCommon::systemSetFilePermissions($DATA_BACKUP_FILE, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'} . $domainData['domain_uid'], DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'} . $domainData['domain_gid'], 0644 );
			DaemonBackup::Compress($DATA_BACKUP_FILE);
		}
	}
}
?>