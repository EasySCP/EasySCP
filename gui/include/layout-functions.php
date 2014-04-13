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

if (isset($_SESSION['user_id'])){
	$cfg = EasySCP_Registry::get('Config');

	if( !isset($_SESSION['logged_from']) && !isset($_SESSION['logged_from_id']) ){
		list($user_def_lang, $user_def_layout) = get_user_gui_props($_SESSION['user_id']);


		if ($user_def_lang != '') {
			$cfg->USER_SELECTED_LANG = $user_def_lang;
			$_SESSION['user_def_lang'] = $user_def_lang;
		} else {
			$_SESSION['user_def_lang'] = $cfg->USER_INITIAL_LANG;
		}

		if ($user_def_layout != '') {
			$cfg->USER_INITIAL_THEME = $user_def_layout;
			$_SESSION['user_theme'] = $user_def_layout;
		} else {
			$_SESSION['user_theme'] = $cfg->USER_INITIAL_THEME;
		}
	} else {
		if (isset($_SESSION['logged_from_id']) && $_SESSION['logged_from_id'] != '') {
			list($user_def_lang, $user_def_layout) = get_user_gui_props($_SESSION['logged_from_id']);

			if ($user_def_lang != '') {
				$cfg->USER_SELECTED_LANG = $user_def_lang;
				$_SESSION['user_def_lang'] = $user_def_lang;
			} else {
				$_SESSION['user_def_lang'] = $cfg->USER_INITIAL_LANG;
			}

			if ($user_def_layout != '') {
				$cfg->USER_INITIAL_THEME = $user_def_layout;
				$_SESSION['user_theme'] = $user_def_layout;
			} else {
				$_SESSION['user_theme'] = $cfg->USER_INITIAL_THEME;
			}
		} else {
			if (isset($_SESSION['user_def_lang']) && $_SESSION['user_def_lang'] != '') {
				$cfg->USER_SELECTED_LANG = $_SESSION['user_def_lang'];
			}

			if (isset($_SESSION['user_theme']) && $_SESSION['user_theme'] != '') {
				$cfg->USER_INITIAL_THEME = $_SESSION['user_theme'];
			}
		}
	}
}

// THEME_COLOR management stuff.

function get_user_gui_props($user_id) {

	$cfg = EasySCP_Registry::get('Config');

	$sql_param = array(
		':user_id'	=> $user_id
	);

	$sql_query = "
		SELECT
			lang, layout
		FROM
			user_gui_props
		WHERE
			user_id = :user_id;
	";

	DB::prepare($sql_query);
	$row = DB::execute($sql_param, true);

	if (isset($row['lang']) && !empty($row['lang']) && isset($row['layout']) && !empty($row['layout'])){
		return array($row['lang'], $row['layout']);
	} elseif (isset($row['lang']) && !empty($row['lang'])){
		return array($row['lang'], $cfg->USER_INITIAL_THEME);
	} elseif(isset($row['layout']) && !empty($row['layout'])){
		return array($cfg->USER_INITIAL_LANG, $row['layout']);
	} else {
		return array($cfg->USER_INITIAL_LANG, $cfg->USER_INITIAL_THEME);
	}
}

/**
 * Parses the output of the $_SESSION variable to the template, if exists.
 *
 * @param EasySCP_TemplateEngine $tpl the TPL object
 */
function gen_page_message($tpl) {

	if (isset($_SESSION['user_page_message'])) {
		$tpl->assign('MESSAGE', $_SESSION['user_page_message']);
		$tpl->assign('MSG_TYPE', $_SESSION['user_page_msg_type']);
		unset($_SESSION['user_page_message']);
		unset($_SESSION['user_page_msg_type']);
	}
}

/**
 * Saves a message to the $_SESSION array
 *
 * @param String $message	the formated message text
 * @param String $type		the type of the message: notice, warning, error,
 *								success
 */
function set_page_message($message, $type = 'warning') {

	if (isset($_SESSION['user_page_message'])) {
		$_SESSION['user_page_message'] .= "\n<br />$message";
	} else {
		$_SESSION['user_page_message'] = $message;
	}
	if ($type != 'info' && $type != 'error' && $type != 'success' && $type != 'warning') {
		$type = 'warning';
	}
	$_SESSION['user_page_msg_type'] = $type;
}

/**
 * Converts a Array of Strings to a single <br />-separated String
 *
 * @since r2684
 * @param	String[]	Array of message strings
 * @return	String		a single string with <br /> tags
 */
function format_message($message) {
	$string = "";
	foreach ($message as $msg) {
		$string .= $msg . "<br />\n";
	}
	return $string;
}

/**
 *
 */
function get_menu_vars($menu_link) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$user_id = $_SESSION['user_id'];

	$query = "
		SELECT
			`customer_id`, `fname`, `lname`, `firm`, `zip`, `city`, `state`, `country`, `email`, `phone`, `fax`, `street1`, `street2`
		FROM
			`admin`
		WHERE
			`admin_id` = ?
	";
	$rs = exec_query($sql, $query, $user_id);

	$search = array();
	$replace = array();

	$search [] = '{uid}';
	$replace[] = $_SESSION['user_id'];
	$search [] = '{uname}';
	$replace[] = tohtml($_SESSION['user_logged']);
	$search [] = '{cid}';
	$replace[] = tohtml($rs->fields['customer_id']);
	$search [] = '{fname}';
	$replace[] = tohtml($rs->fields['fname']);
	$search [] = '{lname}';
	$replace[] = tohtml($rs->fields['lname']);
	$search [] = '{company}';
	$replace[] = tohtml($rs->fields['firm']);
	$search [] = '{zip}';
	$replace[] = tohtml($rs->fields['zip']);
	$search [] = '{city}';
	$replace[] = tohtml($rs->fields['city']);
	$search [] = '{state}';
	$replace[] = $rs->fields['state'];
	$search [] = '{country}';
	$replace[] = tohtml($rs->fields['country']);
	$search [] = '{email}';
	$replace[] = tohtml($rs->fields['email']);
	$search [] = '{phone}';
	$replace[] = tohtml($rs->fields['phone']);
	$search [] = '{fax}';
	$replace[] = tohtml($rs->fields['fax']);
	$search [] = '{street1}';
	$replace[] = tohtml($rs->fields['street1']);
	$search [] = '{street2}';
	$replace[] = tohtml($rs->fields['street2']);

	$query = "
		SELECT
			`domain_name`, `domain_admin_id`
		FROM
			`domain`
		WHERE
			`domain_admin_id` = ?
	";

	$rs = exec_query($sql, $query, $user_id);

	$search [] = '{domain_name}';
	$replace[] = $rs->fields['domain_name'];

	$menu_link = str_replace($search, $replace, $menu_link);
	return $menu_link;
}

/**
 * Creates a list of all current installed themes
 *
 */
function gen_def_theme() {

	$cfg = EasySCP_Registry::get('Config');
	$tpl = EasySCP_TemplateEngine::getInstance();

	$dir = '../themes/';
	$excludes = array('scripts');

	$current_theme = $cfg['USER_INITIAL_THEME'];
	$htmlSelected = $cfg->HTML_SELECTED;
	$themes = array();

	$handle = opendir($dir);
	while($file = readdir($handle)) {
		if( $file != '.' && $file != '..' && is_dir($dir.$file) && !in_array($file, $excludes) ) {
			$selected = ($file === $current_theme) ? $htmlSelected : '';
			array_push($themes, array($file, $selected));
		}
	}
	closedir($handle);

	asort($themes[0], SORT_STRING);
	foreach ($themes as $theme) {
		$tpl->append(
			array(
				'THEME_VALUE'	=> $theme[0],
				'THEME_SELECTED'=> $theme[1],
				'THEME_NAME'	=> tohtml($theme[0])
			)
		);
	}
}
?>