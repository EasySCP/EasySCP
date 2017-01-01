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
 * @since 1.2.0
 */
require '../../include/easyscp-lib.php';

check_login(__FILE__);

// Get a reference to the Config object
$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'admin/tools_config_ssl.tpl';

$html_selected = $cfg->HTML_SELECTED;

$uaction = filter_input(INPUT_POST, 'uaction');
if (isset($uaction) && $uaction == 'apply') {
    update_ssl_data();
}

switch ($cfg->SSL_STATUS) {
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
		'TR_PAGE_TITLE'				=> tr('EasySCP - Virtual Hosting Control System'),
		'TR_SSL_TITLE'				=> tr('Manage SSL certificate'),
		'TR_SSL_ENABLED'			=> tr('SSL enabled'),
		'TR_SSL_CERTIFICATE'		=> tr('SSL certificate'),
		'TR_SSL_KEY'				=> tr('SSL key'),
		'TR_SSL_CACERT'				=> tr('Certificate of Certification Authorities (CA) (optional, if needed)'),
		'TR_APPLY_CHANGES'			=> tr('Apply changes'),
		'TR_SSL_STATUS_DISABLED'	=> tr('SSL disabled'),
		'TR_SSL_STATUS_SSLONLY'		=> tr('SSL enabled'),
		'TR_SSL_STATUS_BOTH'		=> tr('both'),
		'TR_MESSAGE'				=> tr('Message'),
		'SSL_STATUS'				=> $cfg->SSL_STATUS,
		'SSL_CERTIFICATE'			=> $cfg->SSL_CERT,
		'SSL_KEY'					=> $cfg->SSL_KEY,
		'SSL_CACERT'				=> $cfg->SSL_CACERT
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_system_tools.tpl');
gen_admin_menu($tpl, 'admin/menu_system_tools.tpl');

gen_page_message($tpl);

if (EasyConfig::$cfg->DUMP_GUI_DEBUG) {
    dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

function update_ssl_data() {
	// Get a reference to the Config object
	$cfg = EasySCP_Registry::get('Config');

    // Gets a reference to the EasySCP_ConfigHandler_Db instance
    $db_cfg = EasySCP_Registry::get('Db_Config');
    $db_cfg->resetQueriesCounter('update');

    $sslkey=clean_input(filter_input(INPUT_POST, 'ssl_key'));
    $sslcert=clean_input(filter_input(INPUT_POST, 'ssl_cert'));
    $sslcacert=clean_input(filter_input(INPUT_POST, 'ssl_cacert'));
    $sslstatus=clean_input(filter_input(INPUT_POST, 'ssl_status'));

	if(openssl_x509_check_private_key($sslcert, $sslkey)){
		// update the ssl related values
		$db_cfg->SSL_KEY = $sslkey;
		$db_cfg->SSL_CERT = $sslcert;
		$db_cfg->SSL_CACERT = $sslcacert;
		$db_cfg->SSL_STATUS = $sslstatus;

		$cfg->replaceWith($db_cfg);

		/*
		$data = array (
			'SSL_KEY'	=> $sslkey,
			'SSL_CERT'	=> $sslcert,
			'SSL_STATUS'=> $sslstatus
		);
		*/

		$data = array (
			'SSL_STATUS'=> $sslstatus
		);

		EasyConfig::Save($data);

		write_log(
				get_session('user_logged') . ": Updated SSL configuration!"
		);

		// get number of updates 
		$update_count = $db_cfg->countQueries('update');

		if ($update_count == 0) {
			set_page_message(tr("SSL configuration unchanged"), 'info');
		} elseif ($update_count > 0) {
			set_page_message(tr('SSL configuration updated!'), 'success');
		}
	} else {
		set_page_message(tr("SSL key/cert don't match"), 'Warning');

		write_log(
            get_session('user_logged') . ": Update of SSL configuration failed!"
	    );
	}

	send_request('110 DOMAIN master');
	
	user_goto('tools_config_ssl.php');
}

?>