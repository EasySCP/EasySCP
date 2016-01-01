<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'client/ftp_choose_dir.tpl';

gen_directories($tpl);

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP - Client/Webtools'),
		'CHOOSE'			=> tr('Choose'),
		'TR_DIRECTORY_TREE'	=> tr('Directory tree'),
		'TR_DIRS'			=> tr('Directories'),
		'TR__ACTION'		=> tr('Action')
	)
);

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function gen_directories($tpl) {

	$sql = EasySCP_Registry::get('Db');
	// Initialize variables
	$path = isset($_GET['cur_dir']) ? $_GET['cur_dir'] : '';
	$domain = $_SESSION['user_logged'];
	// Create the virtual file system and open it so it can be used
	$vfs = new EasySCP_VirtualFileSystem($domain, $sql);
	// Get the directory listing
	$list = $vfs->ls($path);
	if (!$list) {
		set_page_message(
			tr('Cannot open directory!<br />Please contact your administrator!'),
			'error'
		);
		return;
	}
	// Show parent directory link
	$parent = explode(DIRECTORY_SEPARATOR, $path);
	array_pop($parent);
	$parent = implode(DIRECTORY_SEPARATOR, $parent);
	$tpl->append(
		array(
			'ACTION'		=> '',
			'ACTION_LINK'	=> 'no',
			'ICON'			=> "parent",
			'DIR_NAME'		=> tr('Parent Directory'),
			'CHOOSE_IT'		=> '',
			'LINK'			=> 'ftp_choose_dir.php?cur_dir=' . $parent
		)
	);
	// Show directories only
	foreach ($list as $entry) {
		// Skip non-directory entries
		if ($entry['type'] != EasySCP_VirtualFileSystem::VFS_TYPE_DIR) {
			continue;
		}
		// Skip '.' and '..'
		if ($entry['file'] == '.' || $entry['file'] == '..') {
			continue;
		}
		// Check for .htaccess existence to display another icon
		$dr = $path . '/' . $entry['file'];
		$tfile = $dr . '/.htaccess';
		if ($vfs->exists($tfile)) {
			$image = "locked";
		} else {
			$image = "folder";
		}

		// Check if folder does not contain a folder that can not be protected
		// @todo: valid directories (e.g. /htdocs/disabled/) are excluded (false positive)
		$forbiddenDirnames = ('/backups|disabled|errors|logs|phptmp/i');
		$forbidden = preg_match($forbiddenDirnames, $entry['file']);
		if ($forbidden === 1) {
			$tpl->append('ACTION_LINK', 'no');
		} else {
			$tpl->append('ACTION_LINK', 'yes');
		}
		// Create the directory link
		$tpl->append(
			array(
				'PROTECT_IT'	=> "protected_areas_add.php?file=".$dr,
				'ICON'			=> $image,
				'DIR_NAME'		=> tohtml($entry['file']),
				'CHOOSE_IT'		=> $dr,
				'LINK'			=> "ftp_choose_dir.php?cur_dir=".$dr
			)
		);
	}
}
?>