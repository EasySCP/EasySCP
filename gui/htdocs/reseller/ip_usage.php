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
$template = 'reseller/ip_usage.tpl';

$reseller_id = $_SESSION['user_id'];

gen_logged_from($tpl);

listIPDomains($tpl, $sql);

// static page messages
$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - Reseller/IP Usage'),
		'TR_DOMAIN_STATISTICS'	=> tr('Domain statistics'),
		'IP_USAGE'				=> tr('IP Usage'),
		'TR_DOMAIN_NAME'		=> tr('Domain Name')
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_statistics.tpl');
gen_reseller_menu($tpl, 'reseller/menu_statistics.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/**
 * Page functions
 */

/**
 * Generate List of Domains assigned to IPs
 *
 * @param EasySCP_TemplateEngine $tpl	The TPL object
 * @param EasySCP_Database $sql	The SQL object
 */
function listIPDomains($tpl, $sql) {

	global $reseller_id;

	$query = "
		SELECT
			`reseller_ips`
		FROM
			`reseller_props`
		WHERE
			`reseller_id` = ?;
	";

	$res = exec_query($sql, $query, $reseller_id);

	$data = $res->fetchRow();

	$reseller_ips =  explode(";", substr($data['reseller_ips'], 0, -1));

	$query = "
		SELECT
			`ip_id`, `ip_number`
		FROM
			`server_ips`
		WHERE
			`ip_id`
		IN
			(".implode(',', $reseller_ips).");
	";

	$rs = exec_query($sql, $query);

	while (!$rs->EOF) {

		$no_domains = false;
		$no_alias_domains = false;
		$domains = array();

		$query = "
			SELECT
				`d`.`domain_name`, `a`.`admin_name`
			FROM
				`domain` d
			INNER JOIN
				`admin` a
			ON
				(`a`.`admin_id` = `d`.`domain_created_id`)
			WHERE
				`d`.`domain_ip_id` = ?
			AND
				`d`.`domain_created_id` = ?
			ORDER BY
				`d`.`domain_name`;
		";

		$rs2 = exec_query($sql, $query, array($rs->fields['ip_id'], $reseller_id));
		$domain_count = $rs2->recordCount();

		if ($rs2->recordCount() == 0) {
			$no_domains = true;
		}

		while(!$rs2->EOF) {
			$domains[] = $rs2->fields['domain_name'];

			$rs2->moveNext();
		}

		$query = "
			SELECT
				`da`.`alias_name`, `a`.`admin_name`
			FROM
				`domain_aliasses` da
			INNER JOIN
				`domain` d
			ON
				(`d`.`domain_id` = `da`.`domain_id`)
			INNER JOIN
				`admin` a
			ON
				(`a`.`admin_id` = `d`.`domain_created_id`)
			WHERE
				`da`.`alias_ip_id` = ?
			AND
				`d`.`domain_created_id` = ?
			ORDER BY
				`da`.`alias_name`;
		";

		$rs3 = exec_query($sql, $query, array($rs->fields['ip_id'], $reseller_id));
		$alias_count = $rs3->recordCount();

		if ($rs3->recordCount() == 0) {
			$no_alias_domains = true;
		}

		while(!$rs3->EOF) {
			$domains[] = $rs3->fields['alias_name'];

			$rs3->moveNext();
		}

		if ($no_domains && $no_alias_domains) {
			$tpl->append('DOMAIN_NAME', array(tr("No records found")));
		} else {
			$tpl->append('DOMAIN_NAME', $domains);
		}

		$tpl->append(
			array(
				'IP'			=> $rs->fields['ip_number'],
				'RECORD_COUNT'	=> tr('Total Domains') . " : " .($domain_count+$alias_count)
			)
		);

		$rs->moveNext();
	} // end while
}
?>