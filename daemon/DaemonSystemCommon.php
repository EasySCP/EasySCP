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
	 * Handle creation of cron file for all users
	 * 
	 * @return boolean
	 */
	protected static function handleCronjobsForAllUsers(){
		System_Daemon::debug('Starting "handleCronjobs" subprocess.');

		$sql_query = "
			SELECT
				admin_id, admin_name
			FROM
				admin
		";

		$admins = DB::query($sql_query);
		
		if ($admins->rowCount() > 0) {
			foreach ($admins as $row){
				$retVal = self::handleCronjobForUser($row['admin_id'], $row['admin_name']);
				if ($retVal !== true) {
					$msg = 'Handling of cronjobs for user ' . $row['admin_name'] . ' failed!';
					System_Daemon::debug($msg);
					return $msg . '<br />' . $retVal;
				}
			}
		}
		System_Daemon::debug('Finished "handleCronjobs" subprocess.');
		return true;
	}

	/**
	 * Create cron file for one user 
	 * 
	 * @param int $userID
	 * @param string $userName
	 * @return boolean
	 */
	protected static function handleCronjobForUser($userID, $userName){
		System_Daemon::debug('Starting "handleCronjobForUser" subprocess.');

		$confFile = DaemonConfig::$cfg->{'CRON_DIR'} . '/EasySCP_' . $userName;
		
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
		
		if ($cronData->rowCount()==0){
			unlink($confFile);
		} else {
			$tpl_param = array('ADMIN'=>$userName);
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
			System_Daemon::debug($confFile);
			$retVal = DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644, false);

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
		}

		System_Daemon::debug('Finished "handleCronjobForUser" subprocess.');

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
			Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net

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