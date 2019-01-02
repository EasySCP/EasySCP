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

$LOG_DIR = DaemonConfig::$cfg->{'SRV_TRAFF_LOG_DIR'}.'/'.time();
mkdir($LOG_DIR, 0700);
exec('export COLUMNS=120;'.DaemonConfig::$cmd->{'CMD_IPTABLES'}.' -nvxL EASYSCP_INPUT 1>'.$LOG_DIR.'/easyscp-iptables-input.log');
exec(DaemonConfig::$cmd->{'CMD_IPTABLES'}.' -Z EASYSCP_INPUT');

if (isset(DaemonConfig::$cfg->{'BASE_SERVER_IPv6'}) && DaemonConfig::$cfg->{'BASE_SERVER_IPv6'} != ''){
	exec('export COLUMNS=120;'.DaemonConfig::$cmd->{'CMD_IPTABLESv6'}.' -nvxL EASYSCP_INPUT 1>'.$LOG_DIR.'/easyscp-iptables-input-v6.log');
	exec(DaemonConfig::$cmd->{'CMD_IPTABLESv6'}.' -Z EASYSCP_INPUT');
}

exec('export COLUMNS=120;'.DaemonConfig::$cmd->{'CMD_IPTABLES'}.' -nvxL EASYSCP_OUTPUT 1>'.$LOG_DIR.'/easyscp-iptables-output.log');
exec(DaemonConfig::$cmd->{'CMD_IPTABLES'}.' -Z EASYSCP_OUTPUT');

if (isset(DaemonConfig::$cfg->{'BASE_SERVER_IPv6'}) && DaemonConfig::$cfg->{'BASE_SERVER_IPv6'} != ''){
	exec('export COLUMNS=120;'.DaemonConfig::$cmd->{'CMD_IPTABLESv6'}.' -nvxL EASYSCP_OUTPUT 1>'.$LOG_DIR.'/easyscp-iptables-output-v6.log');
	exec(DaemonConfig::$cmd->{'CMD_IPTABLESv6'}.' -Z EASYSCP_OUTPUT');
}

$smtp_in = 0;
$pop3_in = 0;
$imap_in = 0;
$http_in = 0;
$all_in = 0;

$readfile = file($LOG_DIR.'/easyscp-iptables-input.log');
foreach ($readfile as $line){
	$bytes = 0;
	$port = 0;
	if (preg_match('/^ *(\d+) *(\d+) */', $line, $wert)){
		if (is_numeric($wert[2])){
			$bytes += $wert[2];
		}
	}
	if (preg_match('/[d,s]pt\:(\d+) *$/', $line, $wert)){
		if (is_numeric($wert[1])){
			$port = $wert[1];
		}
	}

	if ($port == 25 OR $port == 465){
		$smtp_in += $bytes;
	}
	if ($port == 110 OR $port == 995){
		$pop3_in += $bytes;
	}
	if ($port == 143 OR $port == 993){
		$imap_in += $bytes;
	}
	if ($port == 80 OR $port == 443){
		$http_in += $bytes;
	}
	if ($port > 0){
	} else {
		$all_in += $bytes;
	}
}
$pop3_in += $imap_in;

$smtp_out = 0;
$pop3_out = 0;
$imap_out = 0;
$http_out = 0;
$all_out = 0;

if(file_exists($LOG_DIR.'/easyscp-iptables-input-v6.log')){
	$readfile = file($LOG_DIR.'/easyscp-iptables-input-v6.log');
	foreach ($readfile as $line){
		$bytes = 0;
		$port = 0;
		if (preg_match('/^ *(\d+) *(\d+) */', $line, $wert)){
			if (is_numeric($wert[2])){
				$bytes += $wert[2];
			}
		}
		if (preg_match('/[d,s]pt\:(\d+) *$/', $line, $wert)){
			if (is_numeric($wert[1])){
				$port = $wert[1];
			}
		}

		if ($port == 25 OR $port == 465){
			$smtp_in += $bytes;
		}
		if ($port == 110 OR $port == 995){
			$pop3_in += $bytes;
		}
		if ($port == 143 OR $port == 993){
			$imap_in += $bytes;
		}
		if ($port == 80 OR $port == 443){
			$http_in += $bytes;
		}
		if ($port > 0){
		} else {
			$all_in += $bytes;
		}
	}
	$pop3_in += $imap_in;

	$smtp_out = 0;
	$pop3_out = 0;
	$imap_out = 0;
	$http_out = 0;
	$all_out = 0;
}


$readfile = file($LOG_DIR.'/easyscp-iptables-output.log');
foreach ($readfile as $line){
	$bytes = 0;
	$port = 0;
	if (preg_match('/^ *(\d+) *(\d+) */', $line, $wert)){
		if (is_numeric($wert[2])){
			$bytes += $wert[2];
		}
	}
	if (preg_match('/[d,s]pt\:(\d+) *$/', $line, $wert)){
		if (is_numeric($wert[1])){
			$port = $wert[1];
		}
	}

	if ($port == 25 OR $port == 465){
		$smtp_out += $bytes;
	}
	if ($port == 110 OR $port == 995){
		$pop3_out += $bytes;
	}
	if ($port == 143 OR $port == 993){
		$imap_out += $bytes;
	}
	if ($port == 80 OR $port == 443){
		$http_out += $bytes;
	}
	if ($port > 0){
	} else {
		$all_out += $bytes;
	}
}

$pop3_out += $imap_out;

if(file_exists($LOG_DIR.'/easyscp-iptables-output-v6.log')){
	$readfile = file($LOG_DIR.'/easyscp-iptables-output-v6.log');
	foreach ($readfile as $line){
		$bytes = 0;
		$port = 0;
		if (preg_match('/^ *(\d+) *(\d+) */', $line, $wert)){
			if (is_numeric($wert[2])){
				$bytes += $wert[2];
			}
		}
		if (preg_match('/[d,s]pt\:(\d+) *$/', $line, $wert)){
			if (is_numeric($wert[1])){
				$port = $wert[1];
			}
		}

		if ($port == 25 OR $port == 465){
			$smtp_out += $bytes;
		}
		if ($port == 110 OR $port == 995){
			$pop3_out += $bytes;
		}
		if ($port == 143 OR $port == 993){
			$imap_out += $bytes;
		}
		if ($port == 80 OR $port == 443){
			$http_out += $bytes;
		}
		if ($port > 0){
		} else {
			$all_out += $bytes;
		}
	}

	$pop3_out += $imap_out;
}

// timestamp has floor down for the last half'n our to make timestamps in
// server_traffic and domain_traffic the same
$timestamp = time() - (time() % 1800);

$sql_param = array(
	':traff_time'		=> $timestamp,
	':bytes_in'			=> $all_in,
	':bytes_out'		=> $all_out,
	':bytes_mail_in'	=> $smtp_in,
	':bytes_mail_out'	=> $smtp_out,
	':bytes_pop_in'		=> $pop3_in,
	':bytes_pop_out'	=> $pop3_out,
	':bytes_web_in'		=> $http_in,
	':bytes_web_out'	=> $http_out
);

$sql_query = "
	INSERT INTO
		server_traffic (traff_time, bytes_in, bytes_out, bytes_mail_in, bytes_mail_out, bytes_pop_in, bytes_pop_out, bytes_web_in, bytes_web_out)
	VALUES
		(:traff_time, :bytes_in, :bytes_out, :bytes_mail_in, :bytes_mail_out, :bytes_pop_in, :bytes_pop_out, :bytes_web_in, :bytes_web_out)
	ON DUPLICATE KEY UPDATE
		bytes_in = bytes_in + :bytes_in, bytes_out = bytes_out + :bytes_out, bytes_mail_in = bytes_mail_in + :bytes_mail_in, bytes_mail_out = bytes_mail_out + :bytes_mail_out, bytes_pop_in = bytes_pop_in + :bytes_pop_in, bytes_pop_out = bytes_pop_out + :bytes_pop_out, bytes_web_in = bytes_web_in + :bytes_web_in, bytes_web_out = bytes_web_out + :bytes_web_out;
	";

DB::prepare($sql_query);
DB::execute($sql_param)->closeCursor();

exec(DaemonConfig::$cmd->{'CMD_RM'} . ' -rf '.$LOG_DIR);
?>