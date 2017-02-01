<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2010 by ispCP | http://isp-control.net
 * @copyright 	2010-2017 by Easy Server Control Panel - http://www.easyscp.net
 * @version 	SVN: $Id$
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 *
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Original Code is "VHCS - Virtual Hosting Control System".
 *
 * The Initial Developer of the Original Code is moleSoftware GmbH.
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 *
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 *
 * Portions created by the EasySCP Team are Copyright (C) 2010-2017 by
 * Easy Server Control Panel. All Rights Reserved.
 */

function check_day($day) {

	if ($day === '') return 0;
	if (preg_match("/^0?[1-9]$/D", $day)) return 1;
	if (preg_match("/^[1-9][0-9]$/D", $day) && (9 < $day) && ($day < 32)) return 1;

	return 0;
}

function check_month($month) {

	if ($month === '') return 0;
	if (preg_match("/^0?[1-9]$/D", $month)) return 1;
	if (preg_match("/^[1-9][0-9]$/D", $month) && (9 < $month) && ($month < 13)) return 1;

	return 0;
}

function check_year($year) {

	$current_year = date("Y", time());
	if ($year === '') return 0;
	if (preg_match("/^[1-9][0-9][0-9][0-9]$/D", $year) && (1899 < $year) && ($year < ($current_year + 1))) return 1;

	return 0;
}

function check_date($date) {

	if ($date === '') return 0;
	$res = preg_match_all("/^([^\.]+)\.([^\.]+)\.([^\n]+)\n/D", "$date\n", $parts, PREG_PATTERN_ORDER);
	if ($res != 1) return 0;
	if (check_day($parts[1][0]) && check_month($parts[2][0]) && check_year($parts[3][0])) return 1;

	return 0;
}

/**
 * Converts a PHP date Format to the jQuery UI Datepicker date format
 *
 * @see http://docs.jquery.com/UI/Datepicker/$.datepicker.formatDate
 * @param String $dateFormat	PHP date format
 * @return String				JavaScript date format
 */
function jQueryDatepickerDateFormat($dateFormat) {

	$search  = array(
		// Day
		'/d/', '/D/', '/j/', '/l/', 
		// Month
		'/F/', '/m/', '/M/', '/n/',
		// Year
		'/o/', '/y/', '/Y/',
		// to delete
		'/N|S|w|z|W|t|L/',
	);
	$replace = array(
		// Day
		'dd', 'D', 'd', 'DD',
		// Month
		'MM', 'mm', 'M', 'm',
		// Year
		'y', 'y', 'yy',
		// delete
		''
	);
	$dateFormat = preg_replace($search, $replace, $dateFormat);

	return $dateFormat;
}

?>