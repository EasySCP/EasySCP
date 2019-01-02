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
$template = 'client/dns_overview.tpl';

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

$dmn_default_id = get_user_domain_id($_SESSION['user_id']);
$dmn_alias = 0;
$dmn_id = $dmn_default_id;

$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP - Client/Manage DNS'),
		'TR_DNS'			=> tr("DNS zone's records"),
		'TR_DNS_NAME'		=> tr('Name'),
		'TR_DNS_CLASS'		=> tr('Class'),
		'TR_DNS_TYPE'		=> tr('Type'),
		'TR_DNS_ACTION'		=> tr('Actions'),
		'TR_DNS_DATA'		=> tr('Record data'),
		'TR_DNS_STATUS'		=> tr('Status'),
		'TR_DOMAIN_NAME'	=> tr('Domain'),
		'TR_SELECT'			=> tr('Select'),
		'TR_DNS_ADD'		=> tr('Add DNS record'),
		'D_USER_DOMAINS'	=> get_user_domains($_SESSION['user_id']),
		'TR_MESSAGE_DELETE'	=> tr('Are you sure you want to delete %s?')
	)
);

if (isset($_GET['select_domain']) && ($_GET['select_domain'])) {
	$dmn_data = explode('-', $_GET['domain_id']);
	$dmn_alias = $dmn_data[0];
	$dmn_id = $dmn_data[1];
}

$dmn_zone_data = get_dns_zone($dmn_alias, $dmn_id);
$tpl->assign(
	array(
		'D_USER_DOMAIN_SELECTED'	=> 	$dmn_alias.'-'.$dmn_id,
		'D_DNS_ZONE'	=>	$dmn_zone_data,
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
?>