<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2020 by Easy Server Control Panel - http://www.easyscp.net
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

/*******************************************************************************
 * Main program
 */

/**
 * Dispatches the request
 */

// Updates one or more PHP versions
if (isset($_POST['uaction']) && $_POST['uaction'] == 'update') {

	update_php_versions($cfg);

	set_page_message(tr("PHP versions successfully updated!"), 'success');

	user_goto('domain_php_version.php');
}

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'client/domain_php_version.tpl';

if (EasyConfig::$php !== null) {
	// Has EasyConfig::$php valid PHP_FPM config?
	$php_entry_cnt = 0;
	$php_entry_valid = 0;
	foreach (EasyConfig::$php->PHP_Entry as $PHP_Entry) {
		if (isset($PHP_Entry->SRV_PHP) && isset($PHP_Entry->PHP_NAME)
				&& isset($PHP_Entry->PHP_FPM_POOL_DIR)
				&& $PHP_Entry->SRV_PHP != '' && $PHP_Entry->PHP_NAME != ''
				&& $PHP_Entry->PHP_FPM_POOL_DIR != ''
				&& isset($PHP_Entry->ENABLE)
				&& isset($PHP_Entry->ID)) {
			$php_entry_valid = $php_entry_valid + 1;
		}
	$php_entry_cnt = $php_entry_cnt + 1;
	}

	// dynamic page data.
	gen_user_dmn_list($tpl, $cfg, $_SESSION['user_id']);
	if ($php_entry_cnt == $php_entry_valid) {
		gen_user_sub_list($tpl, $cfg, $_SESSION['user_id']);
	} else {
		$tpl->assign(array(
			'SUB_MSG'		=> tr('Subdomains only by using PHP-FPM available!'),
			'SUB_MSG_TYPE'	=> 'info',
			'SUB_LIST'		=> '')
		);
	}
} else {
	set_page_message(tr("Missing EasySCP_PHP.xml"), 'error');
}

// static page messages.
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Client/Manage Domain PHP Version'),
		'TR_MANAGE_DOMAINS'			=> tr('Manage domain PHP version'),
		'TR_DOMAIN'					=> tr('Domain'),
		'TR_DMN_NAME'				=> tr('Name'),
		'TR_DMN_PHP_VERSION'		=> tr('PHP version'),
		'TR_DMN_STATUS'				=> tr('Status'),
		'TR_DMN_ACTION'				=> tr('Actions'),
		'TR_SUBDOMAINS'				=> tr('Subdomains'),
		'TR_SUB_NAME'				=> tr('Name'),
		'TR_SUB_PHP_VERSION'		=> tr('PHP version'),
		'TR_SUB_STATUS'				=> tr('Status'),
		'TR_SUB_ACTION'				=> tr('Actions'),
		'VAL_FOR_SUBMIT_ON_UPDATE'	=> tr('Update'),
		'VAL_FOR_SUBMIT_ON_RESET'	=> tr('Reset')
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

// page functions.

function update_php_versions($cfg) {

	$update_status = $cfg->ITEM_CHANGE_STATUS;

	foreach($_POST['dmn_php_version'] as $index => $dmn_php_version) {

		// update the domain
		$sql_param = array(
			':domain_php_version'		=> $dmn_php_version,
			':domain_status'			=> $update_status,
			':domain_id'				=> $_POST['dmn_php_num'][$index]
		);

		$sql_query = "
			UPDATE
				domain
			SET
				domain_php_version 	= :domain_php_version,
				status 				= :domain_status
			WHERE
				domain_id = :domain_id;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param);
	}

	foreach($_POST['sub_php_version'] as $index => $sub_php_version) {
		// update the subdomains
		$sql_param = array(
			':subdomain_php_version'	=> $sub_php_version,
			':subdomain_status'			=> $update_status,
			':subdomain_id'				=> $_POST['sub_php_num'][$index]
		);

		$sql_query = "
			UPDATE
				subdomain
			SET
				subdomain_php_version 	= :subdomain_php_version,
				status 					= :subdomain_status
			WHERE
				subdomain_id = :subdomain_id;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param);
	}
	
	// Send request to the EasySCP daemon
	send_request('100 CORE checkAll');
}

function gen_user_sub_php_list($tpl, $cfg, $php_version, $id) {
	$entry = 0;
	$html_selected = $cfg->HTML_SELECTED;
	foreach (EasyConfig::$php->PHP_Entry as $PHP_Entry) {
		if (isset($PHP_Entry->PHP_NAME) && $PHP_Entry->PHP_NAME != '') {
			$php_selected = ($php_version == $entry) ? $html_selected : '';
			if (isset($PHP_Entry->ENABLE) && $PHP_Entry->ENABLE == '1') {
				$tpl->append(
					array(
						'SUB_PHP_VERSION' . $id	=> 	array(
														'PHP_VERSION_VALUE'		=> $entry,
														'PHP_VERSION_SELECTED'	=> $php_selected,
														'PHP_VERSION_NAME'		=> $PHP_Entry->PHP_NAME->__toString())
					)
				);
			} elseif ($php_version == $entry) {
				$tpl->append(
					array(
						'SUB_PHP_VERSION' . $id	=> 	array(
														'PHP_VERSION_VALUE'		=> $entry,
														'PHP_VERSION_SELECTED'	=> $php_selected,
														'PHP_VERSION_NAME'		=> $PHP_Entry->PHP_NAME->__toString() . ' (disabled)')
					)
				);
			}
		}
		$entry = $entry + 1;
	}
	
	return true;
}

function gen_user_dmn_php_list($tpl, $cfg, $php_version, $id) {
	$entry = 0;
	$html_selected = $cfg->HTML_SELECTED;
	foreach (EasyConfig::$php->PHP_Entry as $PHP_Entry) {
		if (isset($PHP_Entry->PHP_NAME) && $PHP_Entry->PHP_NAME != '') {
			$php_selected = ($php_version == $entry) ? $html_selected : '';
			if (isset($PHP_Entry->ENABLE) && $PHP_Entry->ENABLE == '1') {
				$tpl->append(
					array(
						'DMN_PHP_VERSION' . $id	=> 	array(
														'PHP_VERSION_VALUE'		=> $entry,
														'PHP_VERSION_SELECTED'	=> $php_selected,
														'PHP_VERSION_NAME'		=> $PHP_Entry->PHP_NAME->__toString())
					)
				);
			} elseif ($php_version == $entry) {
				$tpl->append(
					array(
						'DMN_PHP_VERSION' . $id	=> 	array(
														'PHP_VERSION_VALUE'		=> $entry,
														'PHP_VERSION_SELECTED'	=> $php_selected,
														'PHP_VERSION_NAME'		=> $PHP_Entry->PHP_NAME->__toString() . ' (disabled)')
					)
				);
			}
		}
		$entry = $entry + 1;
	}
	
	return true;
}

function gen_user_sub_action($sub_id, $sub_status) {

	$cfg = EasySCP_Registry::get('Config');

	if ($sub_status === $cfg->ITEM_OK_STATUS) {
		return array(tr('Delete'), "subdomain_delete.php?id=$sub_id");
	} else {
		return array(tr('N/A'), '#');
	}
}

function gen_user_dmn_action($sub_id, $sub_status) {

	$cfg = EasySCP_Registry::get('Config');

	if ($sub_status === $cfg->ITEM_OK_STATUS) {
		return array(tr('Delete'), "alssub_delete.php?id=$sub_id");
	} else {
		return array(tr('N/A'), '#');
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $user_id
 */
function gen_user_sub_list($tpl, $cfg, $user_id) {

	$sql = EasySCP_Registry::get('Db');

	$domain_id = get_user_domain_id($user_id);

	$query = "
		SELECT
			s.subdomain_id,
			s.subdomain_name,
			s.subdomain_mount,
            s.subdomain_php_version,
			s.status,
			s.subdomain_url_forward,
			d.domain_name
		FROM
			subdomain s,
			domain d
		WHERE
			s.domain_id = d.domain_id
		AND
			s.domain_id = ?
		ORDER BY
			s.subdomain_name;
	";

	$rs = exec_query($sql, $query, $domain_id);

	if ($rs->recordCount() == 0) {
		$tpl->assign(array(
			'SUB_MSG'		=> tr('Subdomain list is empty!'),
			'SUB_MSG_TYPE'	=> 'info',
			'SUB_LIST'		=> '')
		);
	} else {
		while (!$rs->EOF) {

			$sbd_name = decode_idna($rs->fields['subdomain_name']);
			$dmn_name = decode_idna($rs->fields['domain_name']);
			$tpl->append(
				array(
					'SUB_PHP_NUM'		=> $rs->fields['subdomain_id'],
					'SUB_NAME'			=> tohtml($sbd_name),
					'SUB_ALIAS_NAME'	=> tohtml($dmn_name),
					'SUB_STATUS'		=> translate_dmn_status($rs->fields['status'])
				)
			);
			gen_user_sub_php_list($tpl, $cfg, $rs->fields['subdomain_php_version'], $rs->fields['subdomain_id']);
			$rs->moveNext();
		}

		$tpl->assign('SUB_MESSAGE', '');
	}
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param int $user_id
 */
function gen_user_dmn_list($tpl, $cfg, $user_id) {

	$sql = EasySCP_Registry::get('Db');

	$domain_id = get_user_domain_id($user_id);

	$query = "
		SELECT
			`domain_id`,
			`domain_name`,
			`status`,
			`domain_php_version`
		FROM
			`domain`
		WHERE
			`domain_id` = ?
		ORDER BY
			`domain_name`
	;";

	$rs = exec_query($sql, $query, $domain_id);

	if ($rs->recordCount() == 0) {
		$tpl->assign(array(
			'DMN_MSG'		=> tr('Domain list is empty!'),
			'DMN_MSG_TYPE'	=> 'info',
			'DMN_LIST'		=> '')
		);
	} else {
		while (!$rs->EOF) {

			$domain_name = decode_idna($rs->fields['domain_name']);
			$tpl->append(
				array(
					'DMN_PHP_NUM'		=> $rs->fields['domain_id'],
					'DMN_NAME'			=> tohtml($domain_name),
					'DMN_STATUS'		=> translate_dmn_status($rs->fields['status'])
				)
			);
			gen_user_dmn_php_list($tpl, $cfg, $rs->fields['domain_php_version'], $rs->fields['domain_id']);
			$rs->moveNext();
		}

		$tpl->assign('DMN_MESSAGE', '');
	}
}
?>