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

echo('Starting "CronDomainLetsEncrypt".');
echo("\n");
echo date('Y-m-d, H:i:s');
echo("\n\n");

// Check Master
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

if ($sslData['ssl_status'] == 3 | $sslData['ssl_status'] == 4
	|| $sslData['ssl_status'] == 13 | $sslData['ssl_status'] == 14) {
	$domains = array(DaemonConfig::$cfg->BASE_SERVER_VHOST->__toString());
	$email = array(DaemonConfig::$cfg->DEFAULT_ADMIN_ADDRESS->__toString());

	if (count($domains) > 0) {
		echo("Check Domain: \n");
		print_r($domains);
		echo ("\n");
		$retVal = DaemonLetsEncrypt::writeLetsEncrypt($domains, DaemonConfig::$cfg->GUI_ROOT_DIR, $email);
		// exec changes every time if no error (debug)
		if ($retVal === true || $retVal === false) {
			// Execute Master Changes
			send_request('110 DOMAIN master');
		} elseif (is_string($retVal)) {
			echo("\n");
			echo('Error: ' . $retVal);
			echo("\n\n");
		}
	}
}

// Check all domain
$sql_query = "
	SELECT
		*
	FROM
		domain
	ORDER BY
		domain_id;
";

foreach (DB::query($sql_query) as $domain) {

	$domains = array ();

	$domainData = queryDomainDataByDomainID($domain['domain_id']);
	if ($domainData['ssl_status'] == 3 || $domainData['ssl_status'] == 4
		|| $domainData['ssl_status'] == 13 || $domainData['ssl_status'] == 14) {
		array_push($domains, $domainData['domain_name']);
		array_push($domains, 'www.' . $domainData['domain_name']);
	}
	if ($subDomainData = querySubDomainDataByDomainID($domainData['domain_id'])) {
		foreach ($subDomainData->fetchAll() as $row) {
			if ($row['ssl_status'] == 3 || $row['ssl_status'] == 4
				|| $row['ssl_status'] == 13 || $row['ssl_status'] == 14) {
				array_push($domains, $row['subdomain_name'] . '.' . $row['domain_name']);
				array_push($domains, 'www.' . $row['subdomain_name'] . '.' . $row['domain_name']);
			}
		}
	}
	$email = array($domainData['email']);
	if (count($domains) > 0) {
		echo("Check Domain: \n");
		print_r($domains);
		echo ("\n");
		$retVal = DaemonLetsEncrypt::writeLetsEncrypt($domains, DaemonConfig::$distro->APACHE_WWW_DIR . '/' . $domainData['domain_name'], $email);
		// New Cert?
		// exec changes every time if no error (debug)
		if ($retVal === true || $retVal === false) {
			// Set Domain to CHANGE
			dbSetDomainStatus('change', $domain['domain_id']) ;
			// Set SubDomains to CHANGE
			if ($subDomainData = querySubDomainDataByDomainID($domainData['domain_id'])) {
				foreach ($subDomainData->fetchAll() as $row) {
				dbSetSubDomainStatus('change', $row['subdomain_id']) ;
				}
			}
			// Execute Domain Changes
			send_request('110 DOMAIN domain ' . $domain['domain_id']);
		} elseif (is_string($retVal)) {
			echo("\n");
			echo('Error: ' . $retVal);
			echo("\n\n");
		}
	}
}

function queryDomainDataByDomainID($domainID){
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

function querySubDomainDataByDomainID($domainID) {
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

function dbSetDomainStatus($status, $domainID) {
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
	if ($row = DB::execute($sql_param)->closeCursor()) {
		return true;
	} else {
		return false;
	}
}

function dbSetSubDomainStatus($status, $subDomainID) {
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
		return true;
	} else {
		return false;
	}
}

/**
 * Send request to the daemon
 *
 * @param string $execute
 * @return string Daemon answer
 * @todo Remove error operator
 */
function send_request($execute) {

	// @$socket = socket_create (AF_INET, SOCK_STREAM, 0);
	@$socket = socket_create (AF_UNIX, SOCK_STREAM, 0);
	if ($socket < 0) {
		$errno = "socket_create() failed.\n";
		return $errno;
	}

	// @$result = socket_connect ($socket, '127.0.0.1', 9876);
	@$result = socket_connect($socket, EasyConfig::$cfg->SOCK_EASYSCPD);
	if ($result == false) {
		$errno = "socket_connect() failed.\n";
		return $errno;
	}

	// read one line with welcome string
	socket_read($socket, 1024, PHP_NORMAL_READ);
	// $out = read_line($socket);

	// check query
	if ($execute == '') {
		$errno = "socket_write() empty query.\n";
		return $errno;
	}

	// send reg check query
	// $query = $execute . "\r\n";
	$query = trim($execute) . "\n";
	socket_write($socket, $query, strlen ($query));

	// read answer from the daemon
	$out = socket_read($socket, 10240, PHP_NORMAL_READ);

	socket_shutdown($socket, 2);
	socket_close($socket);

	// sleep(1);
	// todo: prüfen ob das noch benötigt wird. Wenn keine Fehler mehr auftreten kann es entfernt werden
	// usleep(250);

	return trim($out);
}

echo('Finished "CronDomainLetsEncrypt".');
echo("\n");

?>