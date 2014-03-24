<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
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

ini_set('display_errors', '1');
error_reporting(E_ALL);
require_once '../../include/easyscp-lib.php';
require_once '../../include/Net/DNS2.php';

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'client/dns_edit.tpl';

$DNS_allowed_types = array('A', 'AAAA', 'CNAME', 'MX', 'SRV', 'NS');

$add_mode = preg_match('~dns_add.php~', $_SERVER['REQUEST_URI']);

$tpl->assign(($add_mode) ? 'FORM_ADD_MODE' : 'FORM_EDIT_MODE', true);

// "Modify" button has been pressed
$editid = null;
if (isset($_POST['uaction']) && ($_POST['uaction'] === 'modify')) {
	if (isset($_GET['edit_id'])) {
		$editid = $_GET['edit_id'];
	} else if (isset($_SESSION['edit_ID'])) {
		$editid = $_SESSION['edit_ID'];
	} else {
		unset($_SESSION['edit_ID']);
		not_allowed();
	}
	// Save data to db
	if (check_fwd_data($editid)) {
		$_SESSION['dnsedit'] = "_yes_";
		user_goto('dns_overview.php');
	}
} elseif (isset($_POST['uaction']) && ($_POST['uaction'] === 'add')) {
	if (check_fwd_data(true)) {
		$_SESSION['dnsedit'] = "_yes_";
		user_goto('dns_overview.php');
	}

} else {
	// Get user id that come for edit
	if (isset($_GET['edit_id'])) {
		$editid = $_GET['edit_id'];
	} else{
		$editid = 0;
	}
	$_SESSION['edit_ID'] = $editid;
}

gen_editdns_page($tpl, $editid);

// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> ($add_mode)
			? tr("EasySCP - Manage Domain Alias/Add DNS zone's record")
			: tr("EasySCP - Manage Domain Alias/Edit DNS zone's record"),
		'ACTION_MODE'			=> ($add_mode) ? 'dns_add.php' : 'dns_edit.php?edit_id='.$editid,
		'TR_MODIFY'				=> tr('Modify'),
		'TR_CANCEL'				=> tr('Cancel'),
		'TR_ADD'				=> tr('Add'),
		'TR_DOMAIN'				=> tr('Domain'),
		'TR_EDIT_DNS'			=> ($add_mode) ? tr("Add DNS zone's record") : tr("Edit DNS zone's record"),
		'TR_DNS'				=> tr("DNS zone's records"),
		'TR_DNS_NAME'			=> tr('Name'),
		'TR_DNS_CLASS'			=> tr('Class'),
		'TR_DNS_TYPE'			=> tr('Type'),
		'TR_DNS_SRV_NAME'		=> tr('Service name'),
		'TR_DNS_IP_ADDRESS'		=> tr('IP address'),
		'TR_DNS_IP_ADDRESS_V6'	=> tr('IPv6 address'),
		'TR_DNS_SRV_PROTOCOL'	=> tr('Service protocol'),
		'TR_DNS_SRV_TTL'		=> tr('TTL'),
		'TR_DNS_SRV_PRIO'		=> tr('Priority'),
		'TR_DNS_SRV_WEIGHT'		=> tr('Relative weight for records with the same priority'),
		'TR_DNS_SRV_HOST'		=> tr('Target host'),
		'TR_DNS_SRV_PORT'		=> tr('Target port'),
		'TR_DNS_TXT'			=> tr('Text'),
		'TR_DNS_CNAME'			=> tr('Canonical name'),
		'TR_DNS_PLAIN'			=> tr('Plain record data'),
		'TR_MANAGE_DOMAIN_DNS'	=> tr("DNS zone's records"),
		'TR_DNS_NS'				=> tr('Hostname of Nameserver')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_manage_domains.tpl');
gen_client_menu($tpl, 'client/menu_manage_domains.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// Begin function block

/**
 * @todo use template loop instead of this hardcoded HTML
 */
function create_options($data, $value = null) {

	$cfg = EasySCP_Registry::get('Config');

	$res = '';
	reset($data);

	foreach ($data as $item) {
		$res .= '<option value="' . $item . '"' .
				(($item == $value) ? $cfg->HTML_SELECTED : '') . '>' . $item .
				'</option>';
	}
	return $res;
}

// Show user data
function not_allowed() {
	$_SESSION['dnsedit'] = '_no_';
	user_goto('dns_overview.php');
}

function decode_zone_data($data) {

	$address = $addressv6 = $srv_name = $srv_proto = $cname = $txt = $name = '';
	$srv_TTL = $srv_prio = $srv_weight = $srv_host = $srv_port = $ns = '';

	if (is_array($data)) {
		//$name = $data['domain_dns'];
		$name = $data['name'];
		switch ($data['type']) {
			case 'A':
				$address = $data['content'];
				break;
			case 'AAAA':
				$addressv6 = $data['content'];
				break;
			case 'CNAME':
				$cname = $data['content'];
				break;
			case 'NS':
				$ns = $data['content'];
				break;
			case 'SRV':
				$name = '';
				if (preg_match('~_([^\.]+)\._([^\s]+)[\s]+([\d]+)~', $data['content'], $srv)) {
					$srv_name = $srv[1];
					$srv_proto = $srv[2];
					$srv_TTL = $data['ttl'];
				}
				if (preg_match('~([\d]+)[\s]+([\d]+)[\s]+([\d]+)[\s]+([^\s]+)+~', $data['content'], $srv)) {
					$srv_prio = $srv[1];
					$srv_weight = $srv[2];
					$srv_port = $srv[3];
					$srv_host = $srv[4];
				}
				break;
			case 'MX':
				$name = '';
				$srv_prio = $data['prio'];
				$srv_host = $data['content'];
				break;
			case 'TXT':
				$name = '';
				// @todo implement
				break;
			default:
				$txt = $data['domain_text'];
		}
	}
	return array(
		$name, $address, $addressv6, $srv_name, $srv_proto, $srv_TTL, $srv_prio,
		$srv_weight, $srv_host, $srv_port, $cname, $txt, $ns, $data['protected']
	);
}

/**
 * @todo use template loop instead of this hardcoded HTML
 * @param EasySCP_TemplateEngine $tpl
 * @param int $edit_id
 */
function gen_editdns_page($tpl, $edit_id) {

	global $sql, $DNS_allowed_types, $add_mode;
	$cfg = EasySCP_Registry::get('Config');

	$dmn_props = get_domain_default_props($_SESSION['user_id']);

	if ($dmn_props['domain_dns'] != 'yes') {
		not_allowed();
	}

	if ($GLOBALS['add_mode']) {
		$data = null;

		$query = "
			SELECT
				'0' AS `alias_id`,
				`domain`.`domain_name` AS `domain_name`
			FROM
				`domain`
			WHERE
				`domain_id` = :domain_id
			UNION
			SELECT
				`domain_aliasses`.`alias_id`,
				`domain_aliasses`.`alias_name`
			FROM
				`domain_aliasses`
			WHERE
				`domain_aliasses`.`domain_id` = :domain_id
			AND `status` <> :state
		";

		$res = exec_query($sql, $query, array('domain_id' => $dmn_props['domain_id'], 'state' => $cfg->ITEM_ORDERED_STATUS));
		$sel = '';
		while ($row = $res->fetchRow()) {
			$sel.= '<option value="' . $row['alias_id'] . '">' .
					decode_idna($row['domain_name']) . '</option>';
		}
		$tpl->assign(
			array(
				'SELECT_ALIAS'	=> $sel,
				'ADD_RECORD'	=> true
			)
		);

	} else {
		$sql_query = "
			SELECT
				d.name AS domain_dns,
				r.*
			FROM
				powerdns.domains d,
				powerdns.records r
			WHERE
				r.id = :record_id
			AND
				d.id = r.domain_id
		";
		
		$sql_param = array(
			'record_id' => $edit_id,
		);
		DB::prepare($sql_query);

		$statement = DB::execute($sql_param,false);
		if ($statement->rowCount() <= 0) {
			return not_allowed();
		}
		
		$data = $statement->fetch();
	}
	
	list(
		$name, $address, $addressv6, $srv_name, $srv_proto, $srv_ttl, $srv_prio,
		$srv_weight, $srv_host, $srv_port, $cname, $plain, $protected, $ns
	) = decode_zone_data($data);

	// Protection against edition (eg. for external mail MX record)
	if($protected == '1') {
		set_page_message(
			tr('You are not allowed to edit this DNS record!'),
			'error'
		);
		not_allowed();
	}

	$dns_type = create_options($DNS_allowed_types, tryPost('type', $data['type']));

	$tpl->assign(
		array(
			'SELECT_DNS_TYPE'			=> $dns_type,
			'DNS_NAME'					=> tohtml($name),
			'DNS_ADDRESS'				=> tohtml(tryPost('dns_A_address', $address)),
			'DNS_ADDRESS_V6'			=> tohtml(tryPost('dns_AAAA_address', $addressv6)),
			'SELECT_DNS_SRV_PROTOCOL'	=> create_options(array('tcp', 'udp'), tryPost('srv_proto', $srv_proto)),
			'DNS_SRV_NAME'				=> tohtml(tryPost('dns_srv_name', $srv_name)),
			'DNS_SRV_TTL'				=> tohtml(tryPost('dns_srv_ttl', $srv_ttl)),
			'DNS_SRV_PRIO'				=> tohtml(tryPost('dns_srv_prio', $srv_prio)),
			'DNS_SRV_WEIGHT'			=> tohtml(tryPost('dns_srv_weight', $srv_weight)),
			'DNS_SRV_HOST'				=> tohtml(tryPost('dns_srv_host', $srv_host)),
			'DNS_SRV_PORT'				=> tohtml(tryPost('dns_srv_port', $srv_port)),
			'DNS_CNAME'					=> tohtml(tryPost('dns_cname', $cname)),
			'DNS_PLAIN'					=> tohtml(tryPost('dns_plain_data', $plain)),
			'DNS_NS_HOSTNAME'			=> tohtml(tryPost('dns_ns', $ns)),
			'ID'						=> $edit_id,
			'ACTION_MODE'				=> ($add_mode) ? 'dns_add.php' : 'dns_edit.php?edit_id='.$edit_id,
		)
	);
}

// Check input data
function tryPost($id, $data) {

	if (array_key_exists($id, $_POST)) {
		return $_POST[$id];
	}
	return $data;
}

function validate_NS($record, &$err = null) {
	if (empty($record['dns_ns'])) {
		$err .= tr('Name must be filled.');
		return false;
	}

	if (preg_match('~([^a-z,^A-Z,^0-9,^\.])~u', $record['dns_ns'], $e)) {
		$err .= sprintf(tr('Use of disallowed char("%s") in NS'), $record['dns_ns']);
		return false;
	}

	return true;
}

function validate_CNAME($record, &$err = null) {

	if (preg_match('~([^a-z,A-Z,0-9\.-])~u', $record['dns_cname'], $e)) {
		$err .= sprintf(tr('Use of disallowed char("%s") in CNAME'), $e[1]);
		return false;
	}
	if (empty($record['dns_name'])) {
		$err .= tr('Name must be filled.');
		return false;
	}
	return true;
}

function validate_A($record, &$err = null) {

	if (filter_var($record['dns_A_address'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
		$err .= sprintf(tr('Wrong IPv4 address ("%s").'), $record['dns_A_address']);
		return false;
	}
	if (empty($record['dns_name'])) {
		$err .= tr('Name must be filled.');
		return false;
	}
	return true;
}

function validate_AAAA($record, &$err = null) {

	if (filter_var($record['dns_AAAA_address'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false) {
		$err .= sprintf(tr('Wrong IPv6 address ("%s").'), $record['dns_AAAA_address']);
		return false;
	}

	if (empty($record['dns_name'])) {
		$err .= tr('Name must be filled.');
		return false;
	}

	return true;
}

function validate_SRV($record, &$err, &$dns, &$text) {

	if (!preg_match('~^([\d]+)$~', $record['dns_srv_port'])) {
		$err .= tr('Port must be a number!');
		return false;
	}

	if (!preg_match('~^([\d]+)$~', $record['dns_srv_ttl'])) {
		$err .= tr('TTL must be a number!');
		return false;
	}

	if (!preg_match('~^([\d]+)$~', $record['dns_srv_prio'])) {
		$err .= tr('Priority must be a number!');
		return false;
	}

	if (!preg_match('~^([\d]+)$~', $record['dns_srv_weight'])) {
		$err .= tr('Relative weight must be a number!');
		return false;
	}

	if (empty($record['dns_srv_name'])) {
		$err .= tr('Service must be filled.');
		return false;
	}

	if (empty($record['dns_srv_host'])) {
		$err .= tr('Host must be filled.');
		return false;
	}

	$dns = sprintf("_%s._%s %d", $record['dns_srv_name'], $record['srv_proto'], $record['dns_srv_ttl']);
	$text = sprintf("%d %d %d %s", $record['dns_srv_prio'], $record['dns_srv_weight'], $record['dns_srv_port'], $record['dns_srv_host']);

	return true;
}

function validate_MX($record, &$err, &$dns_srv_prio, &$dns_srv_host) {

	if (!preg_match('~^([\d]+)$~', $record['dns_srv_prio'])) {
		$err .= tr('Priority must be a number!');
		return false;
	}

	if (empty($record['dns_srv_host'])) {
		$err .= tr('Host must be filled.');
		return false;
	}

	$dns_srv_prio = $record['dns_srv_prio'];
	$dns_srv_host = $record['dns_srv_host'];
	return true;
}

function check_CNAME_conflict($domain, &$err) {

	$resolver = new Net_DNS2_Resolver(
		array(

			'nameservers'   => array('127.0.0.1'),
			'use_tcp'       => true
		)
	);

	try {
		$res = $resolver->query($domain, 'CNAME');
	} catch(Net_DNS2_Exception $e) {
		// array_push($errors, $e->getMessage());
		return true;
	}

	if (isset($res->authority[0])){
		$err .= tr('conflict with CNAME record');
		return false;
	}
	/*
	if ($res === false) {
		return true;
	}

	$err .= tr('conflict with CNAME record');
	// return false;
	*/
	return true;
}

function validate_NAME($domain, &$err) {
	if (preg_match('~([^-a-z,A-Z,0-9.])~u', $domain['name'], $e)) {
		$err .= sprintf(tr('Use of disallowed char("%s") in NAME'), $e[1]);
		return false;
	}
	if (preg_match('/\.$/', $domain['name'])) {
		if (!preg_match('/'.str_replace('.', '\.', $domain['domain']).'\.$/', $domain['name'])) {
			$err .= sprintf(tr('Record "%s" is not part of domain "%s".', $domain['name'], $domain['domain']));
			return false;
		}
	}
	return true;
}

/**
 * @throws EasySCP_Exception_Database
 * @param int $edit_id
 * @return bool
 */
function check_fwd_data($edit_id) {

	global $sql;

	$add_mode = $edit_id === true;

	// unset errors
	$ed_error = '_off_';
	$err = '';

	$_text = '';
	$_type = $_POST['type'];

	$dmn_props = get_domain_default_props($_SESSION['user_id']);
	if ($add_mode) {
		$query = "
			SELECT
				*
			FROM (
				SELECT
					'0' AS `alias_id`,
					`domain`.`domain_name` AS `domain_name`
				FROM
					`domain`
				WHERE
					`domain_id` = ?
				UNION
				SELECT
					`domain_aliasses`.`alias_id`,
					`domain_aliasses`.`alias_name`
				FROM
					`domain_aliasses`
				WHERE
					`domain_aliasses`.`domain_id` = ?
			) AS `tbl`
			WHERE
				IFNULL(`tbl`.`alias_id`, 0) = ?
		";
		$res = exec_query($sql, $query, array($dmn_props['domain_id'], $dmn_props['domain_id'], $_POST['alias_id']));
		if ($res->recordCount() <= 0) {
			not_allowed();
		}
		$alias_id = $res->fetchRow();
		$record_domain = $alias_id['domain_name'];
		// if no alias is selected, ID is 0 else the real alias_id
		$alias_id = $alias_id['alias_id'];
	} else {
		
		$sql_query = "
				SELECT
					d.id,
					d.easyscp_domain_id,
					d.easyscp_domain_alias_id,
					d.name
				FROM
					powerdns.domains d,
					powerdns.records r
				WHERE
					r.id = :record_id
				AND
					r.domain_id = d.id;
		";
		
		$sql_param = array(
			'record_id' => $edit_id,
		);
		
		DB::prepare($sql_query);
		$stmt = DB::execute($sql_param);
		if ($stmt->rowCount() <= 0) {
			not_allowed();
		}
		$data = $stmt->fetch();
		$record_domain = $data['name'];
		$alias_id = $data['easyscp_domain_alias_id'];
		$_dns = $data['name'];
		$domain_id = $data['id'];
	}

	if (!validate_NAME(array('name' => $_POST['dns_name'], 'domain' => $record_domain), $err)) {
		$ed_error = sprintf(tr('Cannot validate %s record. Reason \'%s\'.'), $_POST['type'], $err);
	}
	switch ($_POST['type']) {
		case 'CNAME':
			if (!validate_CNAME($_POST, $err)) {
				$ed_error = sprintf(tr('Cannot validate %s record. Reason \'%s\'.'), $_POST['type'], $err);
			}
			$_text = $_POST['dns_cname'];
			$_dns = $_POST['dns_name'];
			break;
		case 'A':
			if (!validate_A($_POST, $err)) {
				$ed_error = sprintf(tr('Cannot validate %s record. Reason \'%s\'.'), $_POST['type'], $err);
			}
			if (!check_CNAME_conflict($_POST['dns_name'].'.'.$record_domain, $err)){
				$ed_error = sprintf(tr('Cannot validate %s record. Reason \'%s\'.'), $_POST['type'], $err);
			}
			$_text = $_POST['dns_A_address'];
			$_dns = $_POST['dns_name'];
			$_ttl = '7200';
			break;
		case 'AAAA':
			if (!validate_AAAA($_POST, $err)) {
				$ed_error = sprintf(tr('Cannot validate %s record. Reason \'%s\'.'), $_POST['type'], $err);
			}
			if (!check_CNAME_conflict($_POST['dns_name'].'.'.$record_domain, $err)) {
				$ed_error = sprintf(tr('Cannot validate %s record. Reason \'%s\'.'), $_POST['type'], $err);
			}
			$_text = $_POST['dns_AAAA_address'];
			$_dns = $_POST['dns_name'];
			break;
		case 'SRV':
			if (!validate_SRV($_POST, $err, $_dns, $_text)) {
				$ed_error = sprintf(tr('Cannot validate %s record. Reason \'%s\'.'), $_POST['type'], $err);
			}
			break;
		case 'MX':
			$_dns = '';
			if (!validate_MX($_POST, $err, $_dns_srv_prio, $_text)) {
				$ed_error = sprintf(tr('Cannot validate %s record. Reason \'%s\'.'), $_POST['type'], $err);
			} else {
				$_dns = $record_domain . '.';
			}
			break;
		case 'NS':
			$_text = '';
			if (!validate_NS($_POST, $err)) {
				$ed_error = sprintf(tr('Cannot validate %s record. Reason \'%s\'.'), $_POST['type'], $err);
			}
			$_text = $_POST['dns_ns'];
			$_ttl = '28800';
			break;
		case 'SOA':
			$_ttl = '3600';
			break;
		default :
			$ed_error = sprintf(tr('Unknown zone type %s!'), $_POST['type']);
	}

	if ($ed_error === '_off_') {

		if ($add_mode) {

			if ($alias_id > 0) {
				$sql_query = "
					SELECT
						`id`
					FROM
						`powerdns`.`domains`
					WHERE
						`easyscp_domain_alias_id` = :alias_id
				";
				$sql_param = array(
					'alias_id' => $alias_id,
				);
				
				DB::prepare($sql_query);
				$data = DB::execute($sql_param, true);
			} else {
				$sql_query = "
					SELECT
						`id`
					FROM
						`powerdns`.`domains`
					WHERE
						`easyscp_domain_id` = :domain_id
				";
				$sql_param = array(
					'domain_id' => $dmn_props['domain_id'],
				);
				
				DB::prepare($sql_query);
				$data = DB::execute($sql_param, true);
			}
			$sql_query = "
				INSERT INTO
					`powerdns`.`records`
				(`domain_id`, `name`, `type`, `content`, `ttl`, `prio`)
					VALUES
				(:domain_id, :name, :type, :content, :ttl, :prio)
			";


			$sql_param = array(
				'domain_id' => $data['id'],
				'name'	=> $_dns,
				'type' => $_type,
				'content' => $_text,
				'ttl' => $_ttl,
				'prio' => $_dns_srv_prio,
			);
			
			DB::prepare($sql_query);
			DB::execute($sql_param);

		} else {
			
			$sql_query = "
					UPDATE
						`powerdns`.`records`
					SET
						`domain_id` = :domain_id,
						`name`	= :name,
						`type` = :type,
						`content` = :content,
						`ttl` = :ttl,
						`prio` = :prio
					WHERE
						`id` = :record_id
			";
			
			$sql_param = array(
				'domain_id' => $domain_id,
				'name'	=> $_dns,
				'type' => $_type,
				'content' => $_text,
				'ttl' => $_ttl,
				'prio' => $_dns_srv_prio,
				'record_id' => $edit_id,
			);
			
			DB::prepare($sql_query);
			DB::execute($sql_param);
		}

		$admin_login = $_SESSION['user_logged'];
		write_log("$admin_login: " . (($add_mode) ? 'add new' : ' modify') . " dns zone record.");

		unset($_SESSION['edit_ID']);
		return true;
	} else {
		set_page_message($ed_error, 'error');
		return false;
	}
} // End of check_user_data()
?>