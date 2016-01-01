<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * EasySCP Daemon Common functions
 */

/**
 * Standard EasyConfig path
 */
define('EasyConfig_PATH', '/etc/easyscp');

/**
 * Autoloader für Klassen
 */
class AutoLoader {

	/**
	 * Lädt eine benötigte Klasse automatisch, falls diese vorhanden ist.
	 *
	 * @param string $className Name der Klasse die geladen werden soll
	 */
	public static function loadClass($className) {
		System_Daemon::debug('Starting AutoLoader subprocess.');
		if(file_exists(dirname(__FILE__).'/'.$className.'.php')) {
			System_Daemon::debug('Loading '.$className.'.php');
			require_once(dirname(__FILE__).'/'.$className.'.php');
		} elseif(file_exists(dirname(__FILE__).'/../gui/include/'.$className.'.php')) {
				System_Daemon::debug('Loading '.$className.'.php');
			require_once(dirname(__FILE__) . '/../gui/include/'.$className.'.php');
		} elseif(file_exists(dirname(__FILE__) . '/../gui/include/Smarty/sysplugins/'.strtolower($className).'.php')){
			System_Daemon::debug('Loading '.strtolower($className).'.php');
			require_once(dirname(__FILE__) . '/../gui/include/Smarty/sysplugins/'.strtolower($className).'.php');
		} else {
			System_Daemon::debug('Not found '.$className.'.php');
		}
		System_Daemon::debug('Finished AutoLoader subprocess.');
	}
}

/**
 * Registriert den AutoLoader für benötigte Klassen (gibt es seit PHP 5 >= 5.1.2).
 */
spl_autoload_register('AutoLoader::loadClass');

class DaemonCommon {

	/**
	 * Stell eine Verbindung zum EasySCP Controller her
	 *
	 * @param string $execute Befehl der an den Controller gesendet wird.
	 *
	 * @return mixed
	 */
	public static function ControlConnect($execute){
		if (file_exists(DaemonConfig::$cfg->{'SOCK_EASYSCPC'})){
			$socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
			if ($socket < 0) {return 'socket_create() failed.<br />';}

			$result = socket_connect($socket, DaemonConfig::$cfg->{'SOCK_EASYSCPC'});
			if ($result == false) {return 'socket_connect() failed.<br />';	}

			socket_read($socket, 1024, PHP_NORMAL_READ);

			socket_write($socket, $execute . "\n", strlen($execute . "\n"));

			socket_shutdown($socket, 2);
			socket_close($socket);
		} else {
			return 'EasySCP Controller not running.';
		}

		return true;
	}

	/**
	 * Passwort zufällig erzeugen
	 *
	 * Mit dieser Funktion kann man ein zufälliges Passwort erzeugen. Als Parameter kann man die gewünschte Passwort länge übergeben.
	 *
	 * @param int $pwlen Die gewünschte länge des Passwortes. Default ist aktuell 8 zeichen wenn nichts übergeben wurde.
	 * @return string Gibt das erzeugte Passwort zurück.
	 */
	public static function generatePassword($pwlen = 8){
		mt_srand();
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$pw = '';
		for($i=0;$i<$pwlen;$i++)
		{
			$pw .= $salt[mt_rand(0, strlen($salt)-1)];
		}
		return $pw;
	}

	/**
	 *
	 * @param array $tpl_param
	 * @return Smarty
	 */
	public static function getTemplate($tpl_param) {
		require_once('../gui/include/Smarty/Smarty.class.php');
		$tpl = new Smarty();
		$tpl->caching = false;
		$tpl->setTemplateDir(
				array(
						'EasySCP' => '/etc/easyscp/'
				)
		);
		$tpl->setCompileDir('/tmp/templates_c/');

		$tpl->assign($tpl_param);

		return $tpl;
	}

	/**
	 * Split an given array in single lines for GUI display
	 * @static
	 * @param $array
	 * @return string
	 */
	public static function listArrayforGUI($array){
		$temp = '';
		foreach($array AS $line){
			$temp .= $line.'<br />';
		}
		return $temp;
	}

	/**
	 * Split an given array in single lines for LOG display
	 * @static
	 * @param $array
	 * @return string
	 */
	public static function listArrayforLOG($array){
		$temp = '';
		foreach($array AS $line){
			$temp .= $line.'\n';
		}
		return $temp;
	}

	/**
	 *
	 * @param String $directory directory path
	 * @param String $user system user
	 * @param String $group system group
	 * @param int $perm directory permission
	 * @return boolean
	 */
	public static function systemCreateDirectory($directory, $user, $group, $perm = 0775) {
		if (!file_exists($directory)) {
			if (mkdir("$directory", $perm, true)) {
				System_Daemon::debug("Created $directory with permission $perm");
			} else {
				System_Daemon::warning("Failed to create $directory with permission $perm");
				return false;
			}
		} else {
			if (chmod("$directory", $perm)){
				System_Daemon::debug("Change permission of $directory to $perm");
			} else {
				System_Daemon::warning("Failed to change permission of $directory to $perm");
				return false;
			}
		}
		if (chown("$directory", "$user")) {
			System_Daemon::debug("Changed ownership of $directory to $user");
		} else {
			System_Daemon::warning("Failed to change ownership of $directory to $user");
			return false;
		}
		if (chgrp("$directory", "$group")) {
			System_Daemon::debug("Changed group ownership of $directory to $group");
		} else {
			System_Daemon::warning("Failed to change group ownership of $directory to $group");
		}
		return true;
	}

	/**
	 * Rechte für Datei setzen
	 *
	 * Mit dieser Funktion kann man die Rechte einzelner Dateien neu setzen lassen.
	 *
	 * @param string $fileName Name und Pfad der Datei.
	 * @param string $user Benutzer welchem die Datei zugewiesen werden soll.
	 * @param string $group Gruppe welcher die Datei zugewiesen werden soll.
	 * @param mixed $perm Die Rechte welche der Datei zugewiesen werden sollen.
	 * @return boolean.
	 */
	public static function systemSetFilePermissions($fileName, $user, $group, $perm){
		if (!chown("$fileName", "$user")){
			System_Daemon::warning("Failed to change ownership of $fileName to $user");
			return false;
		}
		if (!chgrp("$fileName", "$group")){
			System_Daemon::warning("Failed to change group ownership of $fileName to $group");
			return false;
		}
		if (!chmod("$fileName", $perm)){
			System_Daemon::warning("Failed to change permission of $fileName to $perm");
			return false;
		}

		return true;
	}

	/**
	 * Rechte für Ordner setzen
	 *
	 * Mit dieser Funktion kann man die Rechte einzelner Ordner neu setzen lassen.
	 *
	 * @param string $folderName Name und Pfad der Datei.
	 * @param string $user Benutzer welchem die Datei zugewiesen werden soll.
	 * @param string $group Gruppe welcher die Datei zugewiesen werden soll.
	 * @param mixed $perm Die Rechte welche der Datei zugewiesen werden sollen.
	 * @return boolean.
	 * @return void.
	 */
	public static function systemSetFolderPermissions($folderName, $user, $group, $perm){
		if (!chown($folderName, "$user")){
			System_Daemon::warning("Failed to change ownership of $folderName to $user");
			return false;
		}
		if (!chgrp($folderName, "$group")){
			System_Daemon::warning("Failed to change group ownership of $folderName to $group");
			return false;
		}
		if (!chmod($folderName, $perm)){
			System_Daemon::warning("Failed to change permission of $folderName to $perm");
			return false;
		}

		return true;
	}

	/**
	 * Rechte für den GUI Ordner setzen
	 *
	 * Mit dieser Funktion kann man die Rechte des GUI Ordner neu setzen lassen.
	 * Nützlich falls da mal was durcheinander gekommen sein sollte
	 *
	 * @return boolean.
	 */
	public static function systemSetGUIPermissions(){
		self::systemSetPermissionsRecursive(DaemonConfig::$cfg->{'GUI_ROOT_DIR'}.'/', DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, '0755', '0644');

		self::systemSetPermissionsRecursive(DaemonConfig::$cfg->{'GUI_ROOT_DIR'}.'/phptmp', DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, '0770', '0640');
		self::systemSetPermissionsRecursive(DaemonConfig::$cfg->{'GUI_ROOT_DIR'}.'/themes/**/templates_c', DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, '0750', '0640');
		self::systemSetPermissionsRecursive(DaemonConfig::$cfg->{'GUI_ROOT_DIR'}.'/tools/filemanager/temp', DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, '0750', '0640');
		self::systemSetPermissionsRecursive(DaemonConfig::$cfg->{'GUI_ROOT_DIR'}.'/tools/webmail/logs', DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, '0750', '0640');
		self::systemSetPermissionsRecursive(DaemonConfig::$cfg->{'GUI_ROOT_DIR'}.'/tools/webmail/temp', DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, '0750', '0640');
		self::systemSetPermissionsRecursive(DaemonConfig::$cfg->{'GUI_ROOT_DIR'}.'/update', DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_UID'}, DaemonConfig::$cfg->{'APACHE_SUEXEC_USER_PREF'}.DaemonConfig::$cfg->{'APACHE_SUEXEC_MIN_GID'}, '0750', '0640');

		// Main virtual webhosts directory must be owned by root and readable by all the domain-specific users.
		self::systemSetFolderPermissions(DaemonConfig::$cfg->{'APACHE_WWW_DIR'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0555);

		// Main fcgid directory must be world-readable, because all the domain-specific users must be able to access its contents.
		self::systemSetFolderPermissions(DaemonConfig::$cfg->{'PHP_STARTER_DIR'}, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0555);

		return true;
	}

	/**
	 * Rechte für Ordner setzen
	 *
	 * Mit dieser Funktion kann man die Rechte einzelner Ordner neu setzen lassen.
	 *
	 * @param string $pathName Pfad des Ordner wo die Rechne neu gesetzt werden sollen.
	 * @param string $user Benutzer welchem die Dateien und Ordner zugewiesen werden sollen.
	 * @param string $group Gruppe welcher die Dateien und Ordner zugewiesen werden sollen.
	 * @param string $dirPerm Die Zugriffsrechte die den Ordnern zugewiesen werden sollen.
	 * @param string $filePerm Die Zugriffsrechte die den Dateien zugewiesen werden sollen.
	 * @return boolean.
	 */
	public static function systemSetPermissionsRecursive($pathName, $user, $group, $dirPerm, $filePerm){
		exec('find '.$pathName.' -type d -print0 | xargs -r -0 '.DaemonConfig::$cmd->{'CMD_CHMOD'}.' '.$dirPerm, $result, $error);
		exec('find '.$pathName.' -type f -print0 | xargs -r -0 '.DaemonConfig::$cmd->{'CMD_CHMOD'}.' '.$filePerm, $result, $error);
		exec('find '.$pathName.' -print0 | xargs -r -0 '.DaemonConfig::$cmd->{'CMD_CHOWN'}.' '.$user.':'.$group, $result, $error);

		return true;
	}

	/**
	 * Rechte für das System setzen
	 *
	 * Mit dieser Funktion kann man die Rechte des Systems neu setzen lassen (z.b. Konfiguration, Daemon etc.).
	 * Nützlich falls da mal was durcheinander gekommen sein sollte
	 *
	 * @return boolean.
	 */
	public static function systemSetSystemPermissions(){

		// TODO: Remove them when GUI Config has changed to XML
		// Außerdem wird die Datei beim Schreiben der OldConfig bereits mit den richtigen Rechten versehen, das hier wäre nur für den Notfall falls die Rechte durcheinander geraten sind
		// easyscp.conf must be world readable because user "vmail" needs to access it.
		self::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/easyscp.conf', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
		self::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/EasySCP_CMD.xml', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
		self::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/EasySCP_Config.xml', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
		self::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/EasySCP_Config_DB.php', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
		self::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/EasySCP_OS.xml', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
		self::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/easyscp-keys.conf', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
		self::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/Iana_TLD.xml', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);

		self::systemSetFilePermissions('/etc/logrotate.d/easyscp', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);

		self::systemSetPermissionsRecursive(DaemonConfig::$cfg->DAEMON_ROOT_DIR, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, '0700', '0700');

		self::systemSetFilePermissions(DaemonConfig::$cfg->DAEMON_ROOT_DIR.'CronDomainTraffic', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);

		self::systemSetPermissionsRecursive(DaemonConfig::$cfg->SRV_TRAFF_LOG_DIR, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, '0700', '0700');

		if (file_exists('/lib/systemd/system/easyscp_control.service')){
			self::systemSetFilePermissions('/lib/systemd/system/easyscp_control.service', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
		}

		if (file_exists('/lib/systemd/system/easyscp_daemon.service')){
			self::systemSetFilePermissions('/lib/systemd/system/easyscp_daemon.service', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
		}

		return true;
	}

	/**
	 *
	 * @param string $fileName
	 * @param mixed $content
	 * @param mixed $user
	 * @param mixed $group
	 * @param mixed $perm
	 * @param bool $append
	 * @return boolean
	 */
	public static function systemWriteContentToFile($fileName, $content, $user, $group, $perm, $append = false){
		$flags = ($append == true) ? FILE_APPEND : 0;
		if (file_put_contents($fileName, $content, $flags)){
			return DaemonCommon::systemSetFilePermissions($fileName, $user, $group, $perm);
		} else {
			System_Daemon::warning("Failed to write content to $fileName");
			return false;
		}
	}
}
?>