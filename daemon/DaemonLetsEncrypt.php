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

/**
 * EasySCP Daemon LetsEncrypt functions
 */

 
class DaemonLetsEncrypt {
	/**
	 * @param $Input
	 * @return bool
	 */

	public static function writeLetsEncrypt ($domains = array(), $webroot = '', $contact = array()) {

		// Configuration:
		$certlocation = EasyConfig_PATH . "/letsencrypt";

		// Lescript laden
		require_once dirname(__FILE__). '/extlibs/Lescript/Lescript.php';

		// Always use UTC
		date_default_timezone_set("UTC");

		// Make sure our cert location exists
		if (!is_dir($certlocation)) {
			// Make sure nothing is already there.
			if (file_exists($certlocation)) {
				unlink($certlocation);
			}
			mkdir ($certlocation);
		}

		// Do we need to create or upgrade our cert? Assume no to start with.
		$needsgen = false;

		// Do we HAVE a certificate for all our domains?
		foreach ($domains as $d) {
			$certfile = "$certlocation/$d/cert.pem";

			if (!file_exists($certfile)) {
				// We don't have a cert, so we need to request one.
				$needsgen = true;
			} else {
				// We DO have a certificate.
				$certdata = openssl_x509_parse(file_get_contents($certfile));

				// If it expires in less than a month, we want to renew it.
				$renewafter = $certdata['validTo_time_t']-(86400*30);
				if (time() > $renewafter) {
					// Less than a month left, we need to renew.
					$needsgen = true;
				}
			}
		}

		// Do we need to generate a certificate?
		if ($needsgen) {
			try {
				$le = new Analogic\ACME\Lescript($certlocation, $webroot);
				$le->contact = $contact;  //optional
				$le->initAccount();
				$le->signDomains($domains);

			} catch (\Exception $e) {
				// Exit with an error code, something went wrong.
                return $e->getMessage();
			}

			// Copy Certs to each domain (subdomain)
			$d = $domains[0];
			$cert = file_get_contents("$certlocation/$d/cert.pem");
			$chain = file_get_contents("$certlocation/$d/chain.pem");
			$fullchain = file_get_contents("$certlocation/$d/fullchain.pem");
			$last = file_get_contents("$certlocation/$d/last.csr");
			$private = file_get_contents("$certlocation/$d/private.pem");
			$public = file_get_contents("$certlocation/$d/public.pem");

			foreach ($domains as $d) {
				if (!file_exists("$certlocation/$d")) {
					mkdir("$certlocation/$d");
				}
				file_put_contents("$certlocation/$d/cert.pem", $cert);
				file_put_contents("$certlocation/$d/chain.pem", $chain);
				file_put_contents("$certlocation/$d/fullchain.pem", $fullchain);
				file_put_contents("$certlocation/$d/last.csr", $last);
				file_put_contents("$certlocation/$d/private.pem", $private);
				file_put_contents("$certlocation/$d/public.pem", $public);
			}
			
			// Create a complete .pem file for use with haproxy or apache 2.4,
			// and save it as domain.name.pem for easy reference. It doesn't
			// matter that this is updated each time, as it'll be exactly
			// the same. 
			foreach ($domains as $d) {
				$pem = file_get_contents("$certlocation/$d/fullchain.pem")."\n".file_get_contents("$certlocation/" . $domains[0] . "/private.pem");
				file_put_contents("$certlocation/$d.pem", $pem);
			}

			return true;

			} else {
            return false;
        }
	}
}