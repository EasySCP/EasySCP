<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2017 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'admin/system_info.tpl';

$sysinfo = new EasySCP_SystemInfo();

$tpl->assign(
	array(
		'CPU_MODEL'				=> tohtml($sysinfo->cpu['model']),
		'CPU_COUNT'				=> tohtml($sysinfo->cpu['cpus']),
		'CPU_MHZ'				=> tohtml($sysinfo->cpu['cpuspeed']),
		'CPU_CACHE'				=> tohtml($sysinfo->cpu['cache']),
		'CPU_BOGOMIPS'			=> tohtml($sysinfo->cpu['bogomips']),
		'UPTIME'				=> tohtml($sysinfo->uptime),
		'KERNEL'				=> tohtml($sysinfo->kernel),
		'LOAD'					=> $sysinfo->load[0] .' '.
									$sysinfo->load[1] .' '.
									$sysinfo->load[2],
		'RAM_TOTAL'				=> sizeit($sysinfo->ram['total'], 'KB'),
		'RAM_USED'				=> sizeit($sysinfo->ram['used'], 'KB'),
		'RAM_FREE'				=> sizeit($sysinfo->ram['free'], 'KB'),
		'SWAP_TOTAL'			=> sizeit($sysinfo->swap['total'], 'KB'),
		'SWAP_USED'				=> sizeit($sysinfo->swap['used'], 'KB'),
		'SWAP_FREE'				=> sizeit($sysinfo->swap['free'], 'KB'),
	)
);

$mount_points = $sysinfo->filesystem;

foreach ($mount_points as $mountpoint) {
		$tpl->append(
			array(
				'MOUNT'		=> tohtml($mountpoint['mount']),
				'TYPE'		=> tohtml($mountpoint['fstype']),
				'PARTITION'	=> tohtml($mountpoint['disk']),
				'PERCENT'	=> $mountpoint['percent'],
				'FREE'		=> sizeit($mountpoint['free'], 'KB'),
				'USED'		=> sizeit($mountpoint['used'], 'KB'),
				'SIZE'		=> sizeit($mountpoint['size'], 'KB'),
			)
		);

}


// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Virtual Hosting Control System'),
		'TR_CPU_BOGOMIPS'		=> tr('CPU bogomips'),
		'TR_CPU_CACHE'			=> tr('CPU cache'),
		'TR_CPU_COUNT'			=> tr('Number of CPU Cores'),
		'TR_CPU_MHZ'			=> tr('CPU MHz'),
		'TR_CPU_MODEL'			=> tr('CPU model'),
		'TR_CPU_SYSTEM_INFO'	=> tr('CPU system Info'),
		'TR_FILE_SYSTEM_INFO'	=> tr('Filesystem system Info'),
		'TR_FREE'				=> tr('Free'),
		'TR_KERNEL'				=> tr('Kernel Version'),
		'TR_LOAD'				=> tr('Load (1 Min, 5 Min, 15 Min)'),
		'TR_MEMRY_SYSTEM_INFO'	=> tr('Memory system info'),
		'TR_MOUNT'				=> tr('Mount'),
		'TR_RAM'				=> tr('RAM'),
		'TR_PARTITION'			=> tr('Partition'),
		'TR_PERCENT'			=> tr('Percent'),
		'TR_SIZE'				=> tr('Size'),
		'TR_SWAP'				=> tr('Swap'),
		'TR_SYSTEM_INFO_TITLE'	=> tr('System info'),
		'TR_SYSTEM_INFO'		=> tr('Vital system info'),
		'TR_TOTAL'				=> tr('Total'),
		'TR_TYPE'				=> tr('Type'),
		'TR_UPTIME'				=> tr('Up time'),
		'TR_USED'				=> tr('Used'),
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
