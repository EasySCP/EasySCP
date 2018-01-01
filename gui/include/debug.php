<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2018 by Easy Server Control Panel - http://www.easyscp.net
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

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function dump_gui_debug($tpl) {
	$gui_debug_data = '<div id="dump_gui_debug">';
	$gui_debug_data .= '<span style="color:#00f;text-decoration:underline;">Content of <strong>$_SESSION</strong>:<br /></span>';
	$gui_debug_data .= '<pre>';
	$gui_debug_data .= htmlentities(print_r($_SESSION, true));
	$gui_debug_data .= '</pre>';
	$gui_debug_data .= '<span style="color:#00f;text-decoration:underline;">Content of <strong>$_POST</strong>:<br /></span>';
	$gui_debug_data .= '<pre>';
	$gui_debug_data .= htmlentities(print_r($_POST, true));
	$gui_debug_data .= '</pre>';
	$gui_debug_data .= '<span style="color:#00f;text-decoration:underline;">Content of <strong>$_GET</strong>:<br /></span>';
	$gui_debug_data .= '<pre>';
	$gui_debug_data .= htmlentities(print_r($_GET, true));
	$gui_debug_data .= '</pre>';
	$gui_debug_data .= '<span style="color:#00f;text-decoration:underline;">Content of <strong>$_COOKIE</strong>:<br /></span>';
	$gui_debug_data .= '<pre>';
	$gui_debug_data .= htmlentities(print_r($_COOKIE, true));
	$gui_debug_data .= '</pre>';
	$gui_debug_data .= '<span style="color:#00f;text-decoration:underline;">Content of <strong>$_FILES</strong>:<br /></span>';
	$gui_debug_data .= '<pre>';
	$gui_debug_data .= htmlentities(print_r($_FILES, true));
	$gui_debug_data .= '</pre>';

	/* Activate debug code if needed
	$gui_debug_data .= '<span style="color:#00f;text-decoration:underline;">Content of <strong>$_SERVER</strong>:<br /></span>';
	$gui_debug_data .= '<pre>';
	$gui_debug_data .= htmlentities(print_r($_SERVER, true));
	$gui_debug_data .= '</pre>';
	*/

	$gui_debug_data .= '</div>';

	$tpl->assign('GUI_DEBUG', $gui_debug_data);
}
