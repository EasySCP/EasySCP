<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2019 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

require '../../include/easyscp-lib.php';

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'admin/server_status.tpl';

getServerStatus();

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP Admin / System Tools / Server Status'),
		'TR_HOST'			=> tr('Host'),
		'TR_SERVICE'		=> tr('Service'),
		'TR_STATUS'			=> tr('Status'),
		'TR_SERVER_STATUS'	=> tr('Server status'),
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_general_information.tpl');
gen_admin_menu($tpl, 'admin/menu_general_information.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/*
 * Site functions
 */

class status {
	var $all = array();

	/**
	 * AddService adds a service to a multi-dimensional array
	 */
	function addService($ip, $port, $service, $type) {
		$small_array = array('ip' => $ip, 'port' => $port, 'service' => $service, 'type' => $type, 'status' => '');
		array_push($this->all, $small_array);
		return $this->all;
	}

	/**
	 * getCount returns the number of services added
	 */
	function getCount() {
		return count($this->all);
	}

	/**
	 * checkStatus checks the status
	 */
	function checkStatus($timeout = 5) {
		for ($i = 0, $x = $this->getCount() - 1; $i <= $x; $i++) {
			$ip = $this->all[$i]['ip'];
			$port = $this->all[$i]['port'];
			$errno = null;
			$errstr = null;

			if ($this->all[$i]['type'] == 'tcp') {
				$fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
			}
			else if ($this->all[$i]['type'] == 'udp') {
				$fp = @fsockopen('udp://' . $ip, $port, $errno, $errstr, $timeout);
			}
			else {
				write_log(sprintf('FIXME: %s:%d' . "\n" . 'Unknown connection type %s',__FILE__, __LINE__, $this->all[$i]['type']));
				throw new EasySCP_Exception('FIXME: ' . __FILE__ . ':' . __LINE__);
			}

			if ($fp) {
				$this->all[$i]['status'] = true;
			}
			else {
				$this->all[$i]['status'] = false;
			}

			if ($fp)
				fclose($fp);
		}
	}

	/**
	 * getStatus a unecessary function to return the status
	 */
	function getStatus() {
		return $this->all;
	}

	/**
	 * getSingleStatus will get the status of single address
	 */
	function getSingleStatus($ip, $port, $type, $timeout = 5) {
		$errno = null;
		$errstr = null;
		if ($type == 'tcp') {
			$fp = @fsockopen($ip, $port, $errno, $errstr, $timeout);
		}
		else if ($type == 'udp') {
			$fp = @fsockopen('udp://' . $ip, $port, $errno, $errstr, $timeout);
		}
		else {
			write_log(sprintf('FIXME: %s:%d' . "\n" . 'Unknown connection type %s',__FILE__, __LINE__, $type));
			throw new EasySCP_Exception('FIXME: ' . __FILE__ . ':' . __LINE__);
		}

		if (!$fp)
			return false;

		fclose($fp);
		return true;
	}
}

/**
 */
function getServerStatus() {

	$cfg = EasySCP_Registry::get('Config');
	$tpl = EasySCP_TemplateEngine::getInstance();

	$easyscp_status = new status;

	$sql_query = "
	    SELECT
			*
		FROM
			config
		WHERE
			name LIKE 'PORT_%'
		ORDER BY
			name ASC
	";

	// Dynamic added Ports
	foreach (DB::query($sql_query) as $row) {
		$value = (count(explode(";", $row['value'])) < 6)
			? $row['value'].';'
			: $row['value'];
		list($port, $protocol, $name, $status, , $ip) = explode(";", $value);
		if ($status) {
			$easyscp_status->addService(($ip == '127.0.0.1' ? 'localhost' : (empty($ip) ? $cfg->BASE_SERVER_IP : $ip)), (int)$port, $name, $protocol);
		}
	}

	$easyscp_status->checkStatus(5);
	$data = $easyscp_status->getStatus();
	$up = tr('UP');
	$down = tr('DOWN');

	// $easyscp_status->addService('localhost', 9875, 'EasySCP Controller', 'tcp');
	if (file_exists(EasyConfig::$cfg->SOCK_EASYSCPC)){
		$img = $up;
		$class = "content up";
	} else {
		$img = $down;
		$class = "content down";
	}

	$tpl->append(
		array(
			'HOST'		=> 'localhost',
			'PORT'		=> '0',
			'SERVICE'	=> 'EasySCP Controller',
			'STATUS'	=> $img,
			'CLASS'		=> $class
		)
	);

	// $easyscp_status->addService('localhost', 9876, 'EasySCP Daemon', 'tcp');
	if (file_exists(EasyConfig::$cfg->SOCK_EASYSCPD)){
		$img = $up;
		$class = "content up";
	} else {
		$img = $down;
		$class = "content down";
	}

	$tpl->append(
		array(
			'HOST'		=> 'localhost',
			'PORT'		=> '0',
			'SERVICE'	=> 'EasySCP Daemon',
			'STATUS'	=> $img,
			'CLASS'		=> $class
		)
	);

	for ($i = 0, $cnt_data = count($data); $i < $cnt_data; $i++) {
		if ($data[$i]['status']) {
			$img = $up;
			$class = "content up";
		} else {
			$img = $down;
			$class = "content down";
		}

		if ($data[$i]['port'] == 23) { // 23 = telnet
			if ($data[$i]['status']) {
				$class = 'content2 down';
				$img = $up;
			} else {
				$class = 'content2 up';
				$img = $down;
			}
		}

		$tpl->append(
			array(
				'HOST'		=> $data[$i]['ip'],
				'PORT'		=> $data[$i]['port'],
				'SERVICE'	=> tohtml($data[$i]['service']),
				'STATUS'	=> $img,
				'CLASS'		=> $class
			)
		);

	}
}
?>