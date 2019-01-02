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
$template = 'admin/rootkit_log.tpl';

// Check Log Files
$config_entries = array('RKHUNTER_LOG', 'CHKROOTKIT_LOG', 'OTHER_ROOTKIT_LOG');

foreach ($config_entries as $config_entry) {
	if (empty($config_entry) || !$cfg->exists($config_entry) || !$cfg->$config_entry) {
		continue;
	}

	$filename = $cfg->$config_entry;
	$contents = '';

	if (@file_exists($filename) && is_readable($filename) && filesize($filename)>0) {
		$handle = fopen($filename, 'r');

		$log = fread($handle, filesize($filename));

		fclose($handle);

		$contents = nl2br(tohtml($log));

		$contents = '<div>' . $contents . '</div>';

		$search = array();
		$replace = array();
		// rkhunter-like log colouring
		$search [] = '/[^\-]WARNING/i';
		$replace[] = '<strong style="color:orange">$0</strong>';
		$search [] = '/([^a-z])(OK)([^a-z])/i';
		$replace[] = '$1<span style="color:green">$2</span>$3';
		$search [] = '/[ \t]+clean[ \t]+/i';
		$replace[] = '<span style="color:green">$0</span>';
		$search [] = '/Not found/i';
		$replace[] = '<span style="color:blue">$0</span>';
		$search [] = '/Skipped/i';
		$replace[] = '<span style="color:blue">$0</span>';
		$search [] = '/unknown[^)]/i';
		$replace[] = '<strong style="color:#bf55bf">$0</strong>';
		$search [] = '/Unsafe/i';
		$replace[] = '<strong style="color:#cfcf00">$0</strong>';
		$search [] = '/[1-9][0-9]*[ \t]+vulnerable/i';
		$replace[] = '<strong style="color:red">$0</strong>';
		$search [] = '/0[ \t]+vulnerable/i';
		$replace[] = '<span style="color:green">$0</span>';
		$search [] = '#(\[[0-9]{2}:[[0-9]{2}:[[0-9]{2}\][ \t]+-{20,35}[ \t]+)([a-zA-Z0-9 ]+)([ \t]+-{20,35})<br />#e';
		$replace[] = '"</div><a href=\"#\" onclick=\"showHideBlocks(\'rkhuntb" . $blocksCount . "\');return false;\">$1<strong>$2</strong>$3</a><br /><div id=\"rkhuntb" . $blocksCount++ . "\">"';
		// chkrootkit-like log colouring
		$search [] = '/([^a-z][ \t]+)(INFECTED)/i';
		$replace[] = '$1<strong style="color:red">$2</strong>';
		$search [] = '/Nothing found/i';
		$replace[] = '<span style="color:green">$0</span>';
		$search [] = '/Nothing detected/i';
		$replace[] = '<span style="color:green">$0</span>';
		$search [] = '/Not infected/i';
		$replace[] = '<span style="color:green">$0</span>';
		$search [] = '/no packet sniffer/i';
		$replace[] = '<span style="color:green">$0</span>';
		$search [] = '/(: )(PACKET SNIFFER)/i';
		$replace[] = '$1<span style="color:orange">$2</span>';
		$search [] = '/not promisc/i';
		$replace[] = '<span style="color:green">$0</span>';
		$search [] = '/no suspect file(s|)/i';
		$replace[] = '<span style="color:green">$0</span>';
		$search [] = '/([0-9]+) process(|es) hidden/i';
		$replace[] = '<span style="color:#cfcf00">$0</span>';

		$contents = preg_replace($search, $replace, $contents);
	} else {
		$contents = '<strong style="color:red">' . tr("%s doesn't exist or is empty", $filename) . '</strong>';
	}

	$tpl->append(
		array(
			'LOG'		=> $contents,
			'FILENAME'	=> tohtml($filename)
		)
	);
}

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP Admin / System Tools / Anti-Rootkits Tools Log Checker'),
		'TR_ROOTKIT_LOG'	=> tr('Anti-Rootkits Tools Log Checker')
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_system_tools.tpl');
gen_admin_menu($tpl, 'admin/menu_system_tools.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();
?>