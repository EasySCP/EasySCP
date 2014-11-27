<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

require 'easyscp-setup-lib.php';

# Es soll unsere eigene Fehlerbehandlung genutzt werden
set_error_handler("errorHandler");

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'index.tpl';

$xml = simplexml_load_file("config.xml");

if (isset($_POST['uaction']) && $_POST['uaction'] != '') {
	switch ($_POST['uaction']) {
		case 'step1':
			if (checkMySQL($xml)){
				$template = 'index2.tpl';
			}
			break;
		case 'step2':
			if (checkData($xml)){
				$template = 'index_finisch.tpl';
			} else {
				$template = 'index2.tpl';
			}
			break;
		default:
			$template = 'index.tpl';
	}
}

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE' => 'EasySCP - Setup',
	)
);

if ($template == 'index.tpl'){
	$tpl->assign(
		array(
			'DB_HOST'		=> (isset($_POST['DB_HOST'])) ? trim($_POST['DB_HOST']) : $xml->DB_HOST,
			'DB_DATABASE'	=> (isset($_POST['DB_DATABASE'])) ? trim($_POST['DB_DATABASE']) : $xml->DB_DATABASE,
			'DB_USER'		=> (isset($_POST['DB_USER'])) ? trim($_POST['DB_USER']) : $xml->DB_USER,
			'DB_PASSWORD'	=> (isset($_POST['DB_PASSWORD'])) ? trim($_POST['DB_PASSWORD']) : $xml->DB_PASSWORD,
			'DB_PASSWORD2'	=> (isset($_POST['DB_PASSWORD2'])) ? trim($_POST['DB_PASSWORD2']) :$xml->DB_PASSWORD2
		)
	);
}

if ($template == 'index2.tpl'){
	$tpl->assign(
		array(
			'HOST_OS'		=> getOS($xml),
			// 'HOST_FQHN'		=> (isset($_POST['HOST_FQHN'])) ? trim($_POST['HOST_FQHN']) : $_SERVER['SERVER_NAME'],
			'HOST_FQHN'		=> (isset($_POST['HOST_FQHN'])) ? trim($_POST['HOST_FQHN']) : gethostbyaddr(gethostbyname($_SERVER['SERVER_NAME'])),
			'HOST_IP'		=> (isset($_POST['HOST_IP'])) ? trim($_POST['HOST_IP']) : gethostbyname($_SERVER['SERVER_NAME']),
			'HOST_IPv6'		=> (isset($_POST['HOST_IPv6'])) ? trim($_POST['HOST_IPv6']) : $xml->HOST_IPv6,
			// 'HOST_NAME'		=> (isset($_POST['HOST_NAME'])) ? trim($_POST['HOST_NAME']) : 'admin.'.$_SERVER['SERVER_NAME'],
			'HOST_NAME'		=> (isset($_POST['HOST_NAME'])) ? trim($_POST['HOST_NAME']) : 'admin.'.gethostbyaddr(gethostbyname($_SERVER['SERVER_NAME'])),

			'PANEL_ADMIN'	=> (isset($_POST['PANEL_ADMIN'])) ? trim($_POST['PANEL_ADMIN']) : $xml->PANEL_ADMIN,
			'PANEL_PASS'	=> (isset($_POST['PANEL_PASS'])) ? trim($_POST['PANEL_PASS']) : $xml->PANEL_PASS,
			'PANEL_PASS2'	=> (isset($_POST['PANEL_PASS2'])) ? trim($_POST['PANEL_PASS2']) : $xml->PANEL_PASS2,
			'PANEL_MAIL'	=> (isset($_POST['PANEL_MAIL'])) ? trim($_POST['PANEL_MAIL']) : $xml->PANEL_MAIL,

			'Secondary_DNS'	=> (isset($_POST['Secondary_DNS'])) ? trim($_POST['Secondary_DNS']) : $xml->Secondary_DNS,
			'Timezone'		=> (isset($_POST['Timezone'])) ? trim($_POST['Timezone']) : date_default_timezone_get(),

		)
	);
	if(isset($_POST['LocalNS'])){
		$tpl->assign(
			array(
				'LocalNS_yes'	=> ($_POST['LocalNS'] == '_yes_' ) ? 'checked="checked"' : '',
				'LocalNS_no'	=> ($_POST['LocalNS'] == '_no_' ) ? 'checked="checked"' : ''
			)
		);
	} else {
		$tpl->assign(
			array(
				'LocalNS_yes'	=> 'checked="checked"',
				'LocalNS_no'	=> ''
			)
		);
	}
	if(isset($_POST['MySQL_Prefix'])){
		$tpl->assign(
			array(
				'MySQL_infront'	=> ($_POST['MySQL_Prefix'] == 'infront' ) ? 'checked="checked"' : '',
				'MySQL_behind'	=> ($_POST['MySQL_Prefix'] == 'behind' ) ? 'checked="checked"' : '',
				'MySQL_none'	=> ($_POST['MySQL_Prefix'] == 'none' ) ? 'checked="checked"' : ''
			)
		);
	} else {
		$tpl->assign(
			array(
				'MySQL_infront'	=> '',
				'MySQL_behind'	=> '',
				'MySQL_none'	=> 'checked="checked"'
			)
		);
	}
	if(isset($_POST['AWStats'])){
		$tpl->assign(
			array(
				'AWStats_yes'	=> ($_POST['AWStats'] == '_yes_' ) ? 'checked="checked"' : '',
				'AWStats_no'	=> ($_POST['AWStats'] == '_no_' ) ? 'checked="checked"' : ''
			)
		);
	} else {
		$tpl->assign(
			array(
				'AWStats_yes'	=> '',
				'AWStats_no'	=> 'checked="checked"'
			)
		);
	}
}

gen_page_message($tpl);

$tpl->display($template);

function checkMySQL($xml){
	if(!isset($_POST['DB_HOST']) || $_POST['DB_HOST'] == ''){
		set_page_message('Please enter SQL server hostname!', 'error');
		return false;
	}
	if(!isset($_POST['DB_DATABASE']) || $_POST['DB_DATABASE'] == ''){
		set_page_message('Please enter EasySCP SQL database name!', 'error');
		return false;
	}
	if(!isset($_POST['DB_USER']) || $_POST['DB_USER'] == ''){
		set_page_message('Please enter EasySCP SQL user name!', 'error');
		return false;
	}
	if(!isset($_POST['DB_PASSWORD']) || $_POST['DB_PASSWORD'] == ''){
		set_page_message('Please enter EasySCP SQL password!', 'error');
		return false;
	}
	if(!isset($_POST['DB_PASSWORD2']) || $_POST['DB_PASSWORD2'] == ''){
		set_page_message('Please repeat EasySCP SQL password!', 'error');
		return false;
	}
	if($_POST['DB_PASSWORD'] != $_POST['DB_PASSWORD2']){
		set_page_message('The entered SQL passwords do not match. Please check!', 'error');
		return false;
	}

	if(
		isset($_POST['DB_HOST']) && $_POST['DB_HOST'] != '' &&
		isset($_POST['DB_DATABASE']) && $_POST['DB_DATABASE'] != '' &&
		isset($_POST['DB_USER']) && $_POST['DB_USER'] != '' &&
		isset($_POST['DB_PASSWORD']) && $_POST['DB_PASSWORD'] != '' &&
		isset($_POST['DB_PASSWORD2']) && $_POST['DB_PASSWORD2'] != '' &&
		$_POST['DB_PASSWORD'] == $_POST['DB_PASSWORD2']
	){
		$connectid = '';
		try {
			$connectid = new PDO(
				'mysql:host='.trim($_POST['DB_HOST']).';port=3306',
				trim($_POST['DB_USER']),
				trim($_POST['DB_PASSWORD']),
				array(
					PDO::ATTR_PERSISTENT => true,
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
				)
			);
		}
		catch(PDOException $e){
			set_page_message('Can´t connect to database. Please check your data and make sure that database is running!', 'error');
		}

		// TODO This user has insufficient privileges

		if ($connectid != ''){
			$xml->DB_HOST = trim($_POST['DB_HOST']);
			$xml->DB_DATABASE = trim($_POST['DB_DATABASE']);
			$xml->DB_USER = trim($_POST['DB_USER']);
			$xml->DB_PASSWORD = trim($_POST['DB_PASSWORD']);
			$xml->DB_PASSWORD2 = trim($_POST['DB_PASSWORD2']);

			$handle = fopen("config.xml", "wb");
			fwrite($handle, $xml->asXML());
			fclose($handle);

			return true;
		} else {
			return false;
		}
	}
	return false;
}

function checkData($xml){
	if(!isset($_POST['HOST_FQHN']) || $_POST['HOST_FQHN'] == ''){
		set_page_message('Please enter a fully qualified hostname!', 'error');
		return false;
	} else {
		$checkFQDN = explode('.', trim($_POST['HOST_FQHN']));
		if (count($checkFQDN) < 3){
			set_page_message('Please enter a fully qualified hostname!', 'error');
			return false;
		}
	}
	if(!isset($_POST['HOST_IP']) || $_POST['HOST_IP'] == ''){
		set_page_message('Please enter the system network address!', 'error');
		return false;
	}
	if(filter_var($_POST['HOST_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) == false){
		set_page_message('Please enter a valid ipv4 network address!', 'error');
		return false;
	}
	if(isset($_POST['HOST_IPv6']) && $_POST['HOST_IPv6'] != '' && filter_var($_POST['HOST_IPv6'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) == false){
		set_page_message('Please enter a valid ipv6 network address!', 'error');
		return false;
	}
	if(!isset($_POST['HOST_NAME']) || $_POST['HOST_NAME'] == ''){
		set_page_message('Please enter the domain name where EasySCP will be reachable on!', 'error');
		return false;
	} else {
		$checkHOST = explode('.', trim($_POST['HOST_NAME']));
		if (count($checkHOST) < 3 || $_POST['HOST_NAME'] == $_POST['HOST_FQHN']){
			set_page_message('Please enter the domain name where EasySCP will be reachable on!', 'error');
			return false;
		}
	}

	if(!isset($_POST['PANEL_ADMIN']) || $_POST['PANEL_ADMIN'] == ''){
		set_page_message('Please enter administrator login name!', 'error');
		return false;
	}
	if(!isset($_POST['PANEL_PASS']) || $_POST['PANEL_PASS'] == ''){
		set_page_message('Please enter administrator password!', 'error');
		return false;
	}
	if(!isset($_POST['PANEL_PASS2']) || $_POST['PANEL_PASS2'] == ''){
		set_page_message('Please repeat administrator password!', 'error');
		return false;
	}
	if($_POST['PANEL_PASS'] != $_POST['PANEL_PASS2']){
		set_page_message('The entered administrator passwords do not match. Please check!', 'error');
		return false;
	}
	if(!isset($_POST['PANEL_MAIL']) || $_POST['PANEL_MAIL'] == ''){
		set_page_message('Please enter administrator e-mail address!', 'error');
		return false;
	}

	if(!isset($_POST['Timezone']) || $_POST['Timezone'] == ''){
		set_page_message('Please enter Server\'s Timezone!', 'error');
		return false;
	}

	$HOST_OS = json_decode(base64_decode(trim($_POST['HOST_OS'])));
	$xml->DistName = $HOST_OS->DistName;
	$xml->DistVersion = $HOST_OS->DistVersion;

	$xml->HOST_FQHN = trim($_POST['HOST_FQHN']);
	$xml->HOST_IP = trim($_POST['HOST_IP']);
	$xml->HOST_IPv6 = trim($_POST['HOST_IPv6']);
	$xml->HOST_NAME = trim($_POST['HOST_NAME']);

	if ($xml->FTP_PASSWORD == 'AUTO'){
		$xml->FTP_PASSWORD = generatePassword(18);
	}

	if ($xml->PMA_PASSWORD == 'AUTO'){
		$xml->PMA_PASSWORD = generatePassword(18);
	}
	if ($xml->PMA_BLOWFISH == 'AUTO'){
		$xml->PMA_BLOWFISH = generatePassword(31);
	}

	$xml->PANEL_ADMIN = trim($_POST['PANEL_ADMIN']);
	$xml->PANEL_PASS = trim($_POST['PANEL_PASS']);
	$xml->PANEL_PASS2 = trim($_POST['PANEL_PASS2']);
	$xml->PANEL_MAIL = trim($_POST['PANEL_MAIL']);

	$xml->Secondary_DNS = trim($_POST['Secondary_DNS']);
	$xml->LocalNS = trim($_POST['LocalNS']);
	$xml->MySQL_Prefix = trim($_POST['MySQL_Prefix']);
	$xml->Timezone = trim($_POST['Timezone']);
	$xml->AWStats = trim($_POST['AWStats']);

	$handle = fopen("config.xml", "wb");
	fwrite($handle, $xml->asXML());
	fclose($handle);

	return true;
}

/**
 *
 * @return \Smarty 
 */
function getTemplate(){
	require_once('../easyscp/gui/include/Smarty/Smarty.class.php');
	$tpl = new Smarty();
	$tpl->caching = false;
	$tpl->setTemplateDir(
			array(
				'EasySCP' => '/etc/easyscp/'
			)
	);
	$tpl->setCompileDir(INCLUDEPATH . '/theme/templates_c');

	return $tpl;
}

function errorHandler($errorCode, $errorMessage, $file, $line) {
	set_page_message($errorMessage, 'error');
	return true;
}

function generatePassword($pwlen = 8){
	mt_srand();
	$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$pw = '';
	for($i=0;$i<$pwlen;$i++)
	{
		$pw .= $salt[mt_rand(0, strlen($salt)-1)];
	}
	return $pw;
}

function getOS($xml){
	$os = simplexml_load_file('/etc/easyscp/EasySCP_OS.xml');
	if (isset($_POST['HOST_OS'])){
		$select = trim($_POST['HOST_OS']);
	} elseif ($xml->DistName != '' && $xml->DistVersion != ''){
		$select = base64_encode('{"DistName":"'.$xml->DistName.'","DistVersion":"'.$xml->DistVersion.'"}');
	} else {
		if(file_exists('/etc/centos-release')){
			// CentOS 6
			$select = base64_encode('{"DistName":"CentOS","DistVersion":"6"}');
		} elseif(file_exists('/etc/os-release')){
			$fp = @fopen('/etc/os-release', "r");
			$temp = fread($fp, filesize('/etc/os-release'));
			fclose($fp);
			if(strpos($temp, 'VERSION_ID="7"') !== false){
				// Debian 7
				$select = base64_encode('{"DistName":"Debian","DistVersion":"7"}');
			} elseif(strpos($temp, 'VERSION_ID="12.04"')  !== false) {
				// Ubuntu 12.04
				$select = base64_encode('{"DistName":"Ubuntu","DistVersion":"12.04"}');
			} elseif(strpos($temp, 'VERSION_ID="14.04"')  !== false) {
				// Ubuntu 14.04
				$select = base64_encode('{"DistName":"Ubuntu","DistVersion":"14.04"}');
			} else {
				// Unbekannt
				$select = '';
			}
		} else {
			$select = '';
		}
	}
	$temp = '';

	for( $i = 0; $i < count($os->Dist); $i++) {
		for( $x = 0; $x < count($os->Dist[$i]->OS); $x++) {
			$value = base64_encode('{"DistName":"'.$os->Dist[$i]->OS[$x]->DistName.'","DistVersion":"'.$os->Dist[$i]->OS[$x]->DistVersion.'"}');
			if ($select == $value){
				$temp .= '<option value="'.$value.'" selected="selected">'.$os->Dist[$i]->OS[$x]->Name.'</option>';
			} else {
				$temp .= '<option value="'.$value.'">'.$os->Dist[$i]->OS[$x]->Name.'</option>';
			}
		}
	}

	return $temp;
}
?>