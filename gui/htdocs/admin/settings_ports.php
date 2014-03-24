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

require '../../include/easyscp-lib.php';

check_login(__FILE__);

/*******************************************************************************
 * Main program
 */

/**
 * Dispatches the request
 */

// Adds a service port or updates one or more services ports
if (isset($_POST['uaction']) && $_POST['uaction'] != 'reset') {

	add_update_services(($_POST['uaction']) == 'add' ? true : false);
	user_goto('settings_ports.php');
}

// Deletes a service port
if(isset($_GET['delete'])) {

	delete_service(clean_input($_GET['delete']));
	user_goto('settings_ports.php');

}

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'admin/settings_ports.tpl';

show_services($tpl);

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'					=> tr('EasySCP - Admin/Settings'),
		'TR_ACTION'						=> tr('Action'),
		'TR_UDP'						=> tr('udp'),
		'TR_TCP'						=> tr('tcp'),
		'TR_ENABLED'					=> tr('Yes'),
		'TR_DISABLED'					=> tr('No'),
		'TR_APPLY_CHANGES'				=> tr('Apply changes'),
		'TR_SERVERPORTS'				=> tr('Server ports'),
		'TR_SERVICE'					=> tr('Service'),
		'TR_IP'							=> tr('IP'),
		'TR_PORT'						=> tr('Port'),
		'TR_PROTOCOL'					=> tr('Protocol'),
		'TR_SHOW'						=> tr('Show'),
		'TR_ACTION'						=> tr('Action'),
		'TR_DELETE'						=> tr('Delete'),
		'TR_MESSAGE_DELETE'				=> tr('Are you sure you want to delete %s service port ?', true, '%s'),
		'TR_SHOW_UPDATE_SERVICE_PORT'	=> tr('View / Update service(s) port'),
		'TR_ADD_NEW_SERVICE_PORT'		=> tr('Add new service port'),
		'VAL_FOR_SUBMIT_ON_UPDATE'		=> tr('Update'),
		'VAL_FOR_SUBMIT_ON_ADD'			=> tr('Add'),
		'VAL_FOR_SUBMIT_ON_RESET'		=> tr('Reset')
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_settings.tpl');
gen_admin_menu($tpl, 'admin/menu_settings.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/*******************************************************************************
 * Functions
 */

/**
 * Prepare and put data in session on error(s)
 *
 * @since 1.0.7
 * @param boolean TRUE on add, FALSE otherwise
 * @return void
 */
function to_session($mode) {

	// Get a reference to the array that contain all error fields ids
	$error_fields_ids = &EasySCP_Registry::get('Error_Fields_Ids');

	// Create a json object that will be used by client browser for fields
	// highlighting
	$_SESSION['error_fields_ids'] = json_encode($error_fields_ids);

	// Data for error on add
	if($mode) {
		$values = array(
			'name_new' => $_POST['name_new'],
			'ip_new' => $_POST['ip_new'],
			'port_new' => $_POST['port_new'],
			'port_type_new' => $_POST['port_type_new'],
			'show_val_new' => $_POST['show_val_new']
		);

		$_SESSION['error_on_add'] = $values;

	// Data for error on update
	} else {

		foreach($_POST['var_name'] as $index => $service) {

			$port = $_POST['port'][$index];
			$proto = $_POST['port_type'][$index];
			$name = $_POST['name'][$index];
			$show = $_POST['show_val'][$index];
			$custom = $_POST['custom'][$index];
			$ip = $_POST['ip'][$index];

			$values[$service] = "$port;$proto;$name;$show;$custom;$ip";

			$_SESSION['error_on_updt'] = $values;
		}
	}
} // end to_session()

/**
 * Validates a service port and sets an appropriate message on error
 *
 * @since 1.0.7
 * @param string $name Service port name
 * @param string $ip Ip address
 * @param int $port Service port
 * @param string $proto Service port protocol
 * @param int $show
 * @param int $index Item index on uppdate, empty value otherwise
 * @return TRUE if valid, FALSE otherwise
 */
function validates_service($name, $ip, $port, $proto, $show, $index = '') {

	// Get a reference to the EasySCP_ConfigHandler_Db instance
	$db_cfg = EasySCP_Registry::get('Db_Config');

	// Get a reference to the array that contain all errors messages
	$messages = &EasySCP_Registry::get('Page_Messages');

	// Get a reference to the array that contain all error fields ids
	$error_fields_ids = &EasySCP_Registry::get('Error_Fields_Ids');

	// Accounting for errors messages
	static $msg_cnt = 0;

	$db_sname = "PORT_$name";
	$ip = ($ip == 'localhost') ? '127.0.0.1' : $ip;

	if (!is_basicString($name)) {
		$messages[] = tr('Only letters, numbers, dash and underscore are allowed for services names!');
		$error_fields_ids[] = "name$index";
	}

	if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false) {
		$messages[] = tr('Wrong IP number!');
		$error_fields_ids[] = "ip$index";
	}

	if(!is_number($port) || $port <= 0) {
		$messages[] = tr('Only positive numbers are allowed for services ports!');
		$error_fields_ids[] = "port$index";
	}

	if(!is_int($index) && isset($db_cfg->$db_sname)) {
		$messages[] = tr('Service port with same name already exists!');
		$error_fields_ids[] = "name$index";
	}

	if($proto != 'tcp' && $proto != 'udp') {
		$messages[] = tr('Unallowed protocol!');
		$error_fields_ids[] = "port_type$index";
	}

	if($show != '0' && $show != '1') {
		$messages[] = tr('Incorrect value for show entry!');
		$error_fields_ids[] = "show_val$index";
	}

	return ($msg_cnt = count($messages) != $msg_cnt) ? false : true;

} // end validates_service()

/**
 * Adds or updates a services ports
 *
 * @since 1.0.7
 * @param boolean $mode TRUE on add, FALSE on update
 * @return void
 */
function add_update_services($mode) {

	// Gets a reference to the EasySCP_ConfigHandler_Db instance
	$db_cfg = EasySCP_Registry::get('Db_Config');

	// Create a pool for messages on error and gets a reference to him
	$messages = &EasySCP_Registry::set('Page_Messages', array());

	// Create a pool for error fields ids and gets a reference to him
	$error_fields_ids = &EasySCP_Registry::set('Error_Fields_Ids', array());

	// Adds a service port
	if($mode) {
		$port = $_POST['port_new'];
		$proto = $_POST['port_type_new'];
		$name = strtoupper($_POST['name_new']);
		$show = $_POST['show_val_new'];
		$ip = $_POST['ip_new'];

		if(validates_service($name, $ip, $port, $proto, $show)) {
			$db_sname = "PORT_$name";

			// Add the service port in the database
			// See EasySCP_ConfigHandler_Db adapter class to learn how it work
			$db_cfg->$db_sname = "$port;$proto;$name;$show;1;$ip";

			write_log(
					get_session('user_logged') .
						": Added service port $name ($port)!"
			);
		}

	// Updates one or more services ports
	} else {
		// Reset counter of update queries
		$db_cfg->resetQueriesCounter('update');

		foreach($_POST['name'] as $index => $name) {

			$port = $_POST['port'][$index];
			$proto = $_POST['port_type'][$index];
			$name = strtoupper($name);
			$show = $_POST['show_val'][$index];
			$custom = $_POST['custom'][$index];
			$ip = $_POST['ip'][$index];

			if(validates_service($name, $ip, $port, $proto, $show, $index)) {
				$db_sname = $_POST['var_name'][$index];

				// Update the service port in the database
				// See EasySCP_ConfigHandler_Db adapter class to learn how it work
				$db_cfg->$db_sname = "$port;$proto;$name;$show;$custom;$ip";
			}
		}
	}

	// Prepare data and messages for error page
	if(!empty($error_fields_ids)) {
		to_session($mode);
		set_page_message(implode('<br />', array_unique($messages)), 'error');
	// Prepares message for page on add
	} elseif($mode) {
		set_page_message(tr('Service port was added!'), 'success');
	// Prepares message for page on update
	} else {
		// gets the number of queries that were been executed
		$updt_count = $db_cfg->countQueries('update');

		// An Update was been made in the database ?
		if($updt_count == 1) {
			set_page_message(
				tr('%d Service port was updated!', $updt_count),
				'success'
			);
		} elseif($updt_count > 1) {
			set_page_message(
				tr('%d Services port were updated!', $updt_count),
				'success'
			);
		} else {
			set_page_message(tr("Nothing's been changed!"), 'info');
		}
	}
} // end add_update_services()

/**
 * Gets and prepares the template part for services ports
 *
 * This function is used for generation of both pages (show page and error page)
 *
 * @since 1.0.7
 * @param EasySCP_TemplateEngine $tpl Reference to a template instance
 * @return void;
 */
function show_services($tpl) {

	// Gets reference to the EasySCP_ConfigHandler_File object
	$cfg = EasySCP_Registry::get('Config');

	// Gets the needed data

	if(isset($_SESSION['error_on_updt'])) {
		$values = new EasySCP_Config_Handler($_SESSION['error_on_updt']);
		unset($_SESSION['error_on_updt']);
		$services = array_keys($values->toArray());
	} else {
		$values = EasySCP_Registry::get('Db_Config');

		// Filter function to get only the services ports names
		$filter = create_function(
			'$value', 'if(substr($value, 0, 5) == \'PORT_\') return $value;'
		);

		// Gets list of services port names
		$services = array_filter(array_keys($values->toArray()), $filter);

		if(isset($_SESSION['error_on_add'])) {
			$error_on_add = new EasySCP_Config_Handler($_SESSION['error_on_add']);
			unset($_SESSION['error_on_add']);
		}
	}

	// Prepares tpl

	if(empty($services)) {
		set_page_message(tr('You have no custom service ports defined.'), 'info');
	} else {
		sort($services);

		foreach($services as $index => $service) {

			$v = (count(explode(';', $values->$service)) < 6)
				? $values->$service . ';' : $values->$service;

			list($port, $proto, $name, $status, $custom, $ip) = explode(';', $v);

			$selected_udp = $proto == 'udp' ? $cfg->HTML_SELECTED : '';
			$selected_tcp = $proto == 'udp' ? '' : $cfg->HTML_SELECTED;

			$selected_on = $status == '1' ? $cfg->HTML_SELECTED : '';
			$selected_off = $status == '1' ? '' : $cfg->HTML_SELECTED;

			if ($custom == 0) {
				$tpl->append(
					array(
						'SERVICE' => tohtml($name) .
							'<input name="name[]" type="hidden" id="name' .
							$index . '" value="' . tohtml($name) . '" />',

						'NAME' => false,
						'PORT_READONLY' => $cfg->HTML_READONLY,
						'PROTOCOL_READONLY' => $cfg->HTML_DISABLED,
						'URL_DELETE' => false,
						'NUM' => $index
					)
				);

			} else {

				$tpl->append(
					array(
						'SERVICE' =>
							'<input name="name[]" type="text" id="name' .
								$index . '" value="' . tohtml($name) .
								'" class="textinput" maxlength="25" />',

						'NAME' => tohtml($name),
						'PORT_READONLY' => '',
						'PROTOCOL_READONLY' => '',
						'URL_DELETE' => "?delete=$service",
						'NUM' => $index
					)
				);

			}

			$tpl->append(
				array(
					'CUSTOM' => tohtml($custom),
					'VAR_NAME' => tohtml($service),
					'IP' => (($ip == '127.0.0.1')
						? 'localhost'
						: (empty($ip) ? $cfg->BASE_SERVER_IP : tohtml($ip))),
					'PORT' => $port,
					'SELECTED_UDP' => $selected_udp,
					'SELECTED_TCP' => $selected_tcp,
					'SELECTED_ON' => $selected_on,
					'SELECTED_OFF' => $selected_off
				)
			);

		}

		// Add fields
		$tpl->assign( isset($error_on_add)
			? array(
				'VAL_FOR_NAME_NEW' =>  $error_on_add['name_new'],
				'VAL_FOR_IP_NEW' => $error_on_add['ip_new'],
				'VAL_FOR_PORT_NEW' => $error_on_add['port_new']
			) : array(
				'VAL_FOR_NAME_NEW' => '',
				'VAL_FOR_IP_NEW' => '',
				'VAL_FOR_PORT_NEW' => ''
			)
		);

		// Error fields ids
		$tpl->assign(
			array(
			'ERROR_FIELDS_IDS' => isset($_SESSION['error_fields_ids'])
				? $_SESSION['error_fields_ids'] : "[]"
			)
		);

		unset($_SESSION['error_fields_ids']);
	}
} // end show_services()

/**
 * Remove a service port from the database
 *
 * @param string $port_name service name
 * return void
 */
function delete_service($port_name) {

	$db_cfg = EasySCP_Registry::get('Db_Config');

	if (!isset($db_cfg->$port_name)) {
		set_page_message(tr('Unknown service port name!'), 'error');

		return;
	}

	$values = (count(explode(';', $db_cfg->$port_name)) < 6)
		? $db_cfg->$port_name . ';' : $db_cfg->$port_name;

	list(,,,,$custom) = explode(';', $values);

	if($custom == 1) {
		// Remove the service port from the database
		// see EasySCP_ConfigHandler_Db adapter class to learn how it work
		unset($db_cfg->$port_name);

		write_log(
			get_session('user_logged') . ": Removed service port $port_name!"
		);

		set_page_message(tr('Service port was removed!'), 'info');
	} else {
		set_page_message(
			tr('You are not allowed to remove this port entry!'), 'error'
		);
	}
}
?>