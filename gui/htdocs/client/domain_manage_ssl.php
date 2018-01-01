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
 * @since               1.2.0
 */
require '../../include/easyscp-lib.php';

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');
$html_selected = $cfg->HTML_SELECTED;

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'client/domain_manage_ssl.tpl';

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

$dmn_props = get_domain_default_props($_SESSION['user_id']);

if (isset($_SESSION['ssl_configuration_updated']) && $_SESSION['ssl_configuration_updated'] == "_yes_") {
	unset($_POST);
	unset($_SESSION['ssl_configuration_updated']);
}

if (isset($_POST['Submit']) && isset($_POST['uaction']) && ($_POST['uaction'] === 'apply')) {
	$sslkey=clean_input(filter_input(INPUT_POST, 'ssl_key'));
	$sslcert=clean_input(filter_input(INPUT_POST, 'ssl_cert'));
	$sslcacert=clean_input(filter_input(INPUT_POST, 'ssl_cacert'));
	$sslstatus=clean_input(filter_input(INPUT_POST, 'ssl_status'));

	$rs = EasySSL::storeSSLData($_POST['ssl_domain'], $sslstatus, $sslkey, $sslcert, $sslcacert);
	if ($rs ===false){
		set_page_message(tr("SSL Certificate and key don't match!"), 'error');
	} else {
		if ($rs->rowCount() == 0) {
			set_page_message(tr("SSL configuration unchanged"), 'info');
		} else {
			$_SESSION['ssl_configuration_updated'] = "_yes_";
			set_page_message(tr('SSL configuration updated!'), 'success');
		}
		user_goto('domain_manage_ssl.php');
	}
}
if(isset($_POST['ssl_domain'])){
	genDomainSelect($tpl,$dmn_props['domain_id'],$_POST['ssl_domain']);
	$dmn_props = EasySSL::getSSLData($_POST['ssl_domain']);
	$tpl->assign(
		array(
			'SSL_KEY'					=> $dmn_props['ssl_key'],
			'SSL_CERTIFICATE'			=> $dmn_props['ssl_cert'],
			'SSL_CACERT'				=> $dmn_props['ssl_cacert'],
			'SSL_STATUS'				=> $dmn_props['ssl_status'],
		));
} else {
	$tpl->assign(
		array(
			'SSL_KEY'					=> $dmn_props['ssl_key'],
			'SSL_CERTIFICATE'			=> $dmn_props['ssl_cert'],
			'SSL_CACERT'				=> $dmn_props['ssl_cacert'],
			'SSL_STATUS'				=> $dmn_props['ssl_status'],
		));
	genDomainSelect($tpl,$dmn_props['domain_id'],'');
}

switch ($dmn_props['ssl_status']) {
    case 0:
        $tpl->assign('SSL_SELECTED_DISABLED', $html_selected);
        $tpl->assign('SSL_SELECTED_SSLONLY', '');
        $tpl->assign('SSL_SELECTED_BOTH', '');
        break;
    case 1:
        $tpl->assign('SSL_SELECTED_DISABLED', '');
        $tpl->assign('SSL_SELECTED_SSLONLY', $html_selected);
        $tpl->assign('SSL_SELECTED_BOTH', '');
        break;
    default:
        $tpl->assign('SSL_SELECTED_DISABLED', '');
        $tpl->assign('SSL_SELECTED_SSLONLY', '');
        $tpl->assign('SSL_SELECTED_BOTH', $html_selected);
} // end switch

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Manage SSL configuration'),
		'TR_SSL_CONFIG_TITLE'		=> tr('EasySCP SSL config'),
		'TR_SSL_ENABLED'			=> tr('SSL enabled'),
		'TR_SSL_CERTIFICATE'		=> tr('SSL certificate'),
		'TR_SSL_KEY'				=> tr('SSL key'),
		'TR_SSL_CACERT'				=> tr('Certificate of Certification Authorities (CA) (optional, if needed)'),
		'TR_APPLY_CHANGES'			=> tr('Apply changes'),
		'TR_SSL_STATUS_DISABLED'	=> tr('SSL disabled'),
		'TR_SSL_STATUS_SSLONLY'		=> tr('SSL enabled'),
		'TR_SSL_STATUS_BOTH'		=> tr('both'),
		'TR_MESSAGE'				=> tr('Message'),
		'TR_SSL_DOMAIN'				=> tr('Domain'),
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

function genDomainSelect($tpl,$domain_id,$value)
{
	$cfg = EasySCP_Registry::get('Config');
	$rs = EasySSL::getDomainNames($domain_id);
	if ($rs->rowCount() != 0){
		while ($row = $rs->fetch()) {
			$tpl->append(
				array(
					'DOMAIN_VALUE'			=> $row['id'],
					'DOMAIN_NAME'			=> $row['domain_name'],
					'DOMAIN_SELECTED' 		=> $row['id'] === $value ? $cfg->HTML_SELECTED : ''
				)
			);
		}
	}
}
