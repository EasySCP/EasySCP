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

class DaemonSystemCommon {
	/**
	 * checkUpdate
	 *
	 * @return mixed
	 */
	protected static function checkSystemUpdate() {
		System_Daemon::debug('Starting "checkSystemUpdate" subprocess.');

		System_Daemon::debug('Finished "checkSystemUpdate" subprocess.');

		return true;
	}

	/**
	 * handleCronjob
	 *
	 * @param string $userID
	 * @return mixed
	 */
	protected static function handleCronjob($userID){
		System_Daemon::debug('Starting "handleCronjob" subprocess.');

		$sql_param = array(
			':user_id'	=> $userID,
			':active'	=> 'yes'
		);

		$sql_query = "
			SELECT
				*
			FROM
				cronjobs
			WHERE
				active = :active
			AND
				user_id = :user_id
		";

		DB::prepare($sql_query);
		$cronData = DB::execute($sql_param);

		$sql_param = array(
			':user_id'	=> $userID
		);

		$sql_query = "
			SELECT
				admin_name
			FROM
				admin
			WHERE
				admin_id = :user_id
		";

		DB::prepare($sql_query);
		$adminData = DB::execute($sql_param,true);

		$tpl_param = array('ADMIN'=>$adminData['admin_name']);
		$tpl = DaemonCommon::getTemplate($tpl_param);

		while ($cronJob = $cronData->fetch()){
			$tpl->append(
				array(
					'DESCRIPTION'	=> "# ".$cronJob['description'],
					'SCHEDULE'		=> $cronJob['schedule'],
					'USER'			=> $cronJob['user'],
					'COMMAND'		=> $cronJob['command'],
				)
			);
		}
		// write Cron config
		$config = $tpl->fetch("tpl/cron.tpl");
		$confFile = DaemonConfig::$cfg->{'CRON_DIR'} . '/' . $adminData['admin_name'];
		System_Daemon::debug($confFile);
		$retVal = DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'CRON_GROUP'}, 0600, false);

		if ($retVal !== true) {
			$msg = 'Failed to write'. $confFile;
			System_Daemon::warning($msg);
			return $msg.'<br />'.$retVal;
		} else {
			System_Daemon::debug($confFile.' successfully written!');
		}

		$sql_param = array(
			':user_id'	=> $userID,
			':status'	=> 'ok'
		);

		$sql_query = "
			UPDATE
				cronjobs
			SET
				status = :status
			WHERE
				user_id = :user_id
			AND
				status != :status
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		System_Daemon::debug('Finished "handleCronjob" subprocess.');
		return true;
	}

	/**
	 * handleSystemUpdate
	 *
	 * @return mixed
	 */
	protected static function handleSystemUpdate(){
		System_Daemon::debug('Starting "handleSystemUpdate" subprocess.');

		if (file_exists('phar://' . DaemonConfig::$cfg->{'GUI_ROOT_DIR'} . '/update/update.phar.gz/update.php')){
			include_once 'phar://' . DaemonConfig::$cfg->{'GUI_ROOT_DIR'} . '/update/update.phar.gz/update.php';

			@unlink(DaemonConfig::$cfg->{'GUI_ROOT_DIR'} . '/update/update.phar.gz');
		} else {
			System_Daemon::debug('Datei "update.php" nicht gefunden oder kein gültiges Dateiformat!');

			@unlink(DaemonConfig::$cfg->{'GUI_ROOT_DIR'} . '/update/update.phar.gz');
		}

		System_Daemon::debug('Finished "handleSystemUpdate" subprocess.');
	}

	/**
	 * updateIanaXML
	 * Download list of valid TLDs from IANA and create XML file
	 * @return boolean
	 */
	protected static function updateIanaXML(){
		System_Daemon::debug('Starting "updateIanaXML" subprocess.');

		$ianaUrl = 'http://www.iana.org/domains/root/db';
		$tldTableTag = "tld-table";

		// Create DOMDocument for HTML downloaded from IANA
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTMLFile($ianaUrl);

		$error = libxml_get_last_error();
		if ($error){
			System_Daemon::debug("Error loading HTML from $ianaUrl: " . $error->message);
		}

		$xpath = new DomXpath($dom);

		// Discard white spaces
		$dom->preserveWhiteSpace = false;

		// Create DomDocument for XML file
		$xml = new DOMDocument('1.0', 'UTF-8');
		$xml->formatOutput = true;
		$comment = $xml->createComment("
			EasySCP a Virtual Hosting Control Panel
			Copyright (C) 2010-2012 by Easy Server Control Panel - http://www.easyscp.net

			This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
			To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.

			@link 		http://www.easyscp.net
			@author		EasySCP Team
		");
		$xml->appendChild($comment);
		$rootElement = $xml->createElement("EasySCP_TLD");
		$xml->appendChild($rootElement);
		// Iterate through list of domains and create XML structure
		foreach ($xpath->query('//table[@id="'.$tldTableTag.'"]/tbody/tr') as $row) {
			$domainElement = $xml->createElement("domain");
			$tldElement = $xml->createElement("tld");
			$tldElement->nodeValue = basename($xpath->query(".//span/a", $row)->item(0)->getAttribute('href'), '.html');
			$domainElement->appendChild($tldElement);

			$typeElement = $xml->createElement("type");
			$typeElement->nodeValue = $xpath->query(".//td", $row)->item(1)->nodeValue;
			$domainElement->appendChild($typeElement);

			$sponsorElement = $xml->createElement("sponsor");
			$sponsorElement->nodeValue = $xpath->query(".//td", $row)->item(2)->nodeValue;
			$domainElement->appendChild($sponsorElement);

			$rootElement->appendChild($domainElement);
		}

		$fileSize = $xml->save(EasyConfig_PATH . 'Iana_TLD.xml');
		System_Daemon::debug("Wrote $fileSize to Iana_TLD.xml file");
		System_Daemon::debug('Finished "updateIanaXML" subprocess.');

		if ($fileSize>0){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * verifySystemUpdate
	 *
	 * @return mixed
	 */
	protected static function verifySystemUpdate() {
		System_Daemon::debug('Starting "verifySystemUpdate" subprocess.');

		System_Daemon::debug('Finished "verifySystemUpdate" subprocess.');

		return true;
	}
}
?>