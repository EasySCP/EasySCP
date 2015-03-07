<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2015 by Easy Server Control Panel - http://www.easyscp.net
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
$template = 'client/mail_accounts.tpl';

// dynamic page data.
if (isset($_SESSION['email_support']) && $_SESSION['email_support'] == 'no') {
	$tpl->assign('NO_MAILS', '');
}

gen_page_lists($tpl, $sql, $_SESSION['user_id']);

// static page messages.
gen_logged_from($tpl);
check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'		=> tr('EasySCP - Client/Manage Users'),
		'TR_MANAGE_USERS'	=> tr('Manage users'),
		'TR_MAIL_USERS'		=> tr('Mail users'),
		'TR_MAIL'			=> tr('Mail'),
		'TR_TYPE'			=> tr('Type'),
		'TR_STATUS'			=> tr('Status'),
		'TR_ACTION'			=> tr('Action'),
		'TR_AUTORESPOND'	=> tr('Auto respond'),
		'TR_DMN_MAILS'		=> tr('Domain mails'),
		'TR_SUB_MAILS'		=> tr('Subdomain mails'),
		'TR_ALS_MAILS'		=> tr('Alias mails'),
		'TR_TOTAL_MAIL_ACCOUNTS' => tr('Mails total'),
		'TR_EDIT'			=> tr('Edit'),
		'TR_DELETE'			=> tr('Delete'),
		'TR_MESSAGE_DELETE' => tr('Are you sure you want to delete %s?', true, '%s')
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_email_accounts.tpl');
gen_client_menu($tpl, 'client/menu_email_accounts.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

// page functions.

/**
 * Must be documented
 *
 * @param EasySCP_TemplateEngine $tpl reference to template object
 * @param EasySCP_Database $sql reference to easyscp_Database object
 * @param int $dmn_id domain name id
 * @param string $dmn_name domain name
 * @return int number of domain mails adresses
 */
function gen_page_dmn_mail_list($tpl, $sql, $dmn_id, $dmn_name) {

	$dmn_query = "
		SELECT
			`mail_id`,
			`mail_acc`,
			`mail_type`,
			`status`,
		CONCAT(
			LEFT(`mail_forward`, 	20),
			IF( LENGTH(`mail_forward`) > 20, '...', '')
		) AS 'mail_forward'
		FROM
			`mail_users`
		WHERE
			`domain_id` = ?
		AND
			`sub_id` = 0
		AND
			(
				`mail_type` LIKE '%".MT_NORMAL_MAIL."%'
			OR
				`mail_type` LIKE '%".MT_NORMAL_FORWARD."%'
			) ";

	$dmn_query .= "
		ORDER BY
			`mail_acc` ASC,
			`mail_type` DESC
	";

	$rs = exec_query($sql, $dmn_query, $dmn_id);

	if ($rs->recordCount() == 0) {
		return 0;
	} else {
		while (!$rs->EOF) {

			$mail_acc = decode_idna($rs->fields['mail_acc']);
			$show_dmn_name = decode_idna($dmn_name);

			$mail_types = explode(',', $rs->fields['mail_type']);
			$mail_type = '';

			foreach ($mail_types as $type) {
				$mail_type .= user_trans_mail_type($type);

				if (strpos($type, '_forward') !== false) {
					$mail_type .= ': ' .
						str_replace(
							array("\r\n", "\n", "\r"), ", ",
							$rs->fields['mail_forward']
						);
				}

				$mail_type .= '<br />';
			}

			$tpl->append(
				array(
					'MAIL_ACC'			=> tohtml($mail_acc . '@' . $show_dmn_name),
					'MAIL_TYPE'			=> $mail_type,
					'MAIL_STATUS'		=> translate_dmn_status($rs->fields['status']),
					'MAIL_EDIT_URL'		=> 'mail_edit.php?id=' . $rs->fields['mail_id'],
					'MAIL_DELETE_URL'	=> 'mail_delete.php?id=' . $rs->fields['mail_id']
				)
			);

			$rs->moveNext();
		}

		return $rs->recordCount();
	}
} // end gen_page_dmn_mail_list()

/**
 * Must be documented
 *
 * @param EasySCP_TemplateEngine $tpl reference to the template object
 * @param EasySCP_Database $sql reference to the easyscp_Database object
 * @param int $dmn_id domain name id
 * @param strinc $dmn_name domain name
 * @return int number of subdomain mails addresses
 */
function gen_page_sub_mail_list($tpl, $sql, $dmn_id, $dmn_name) {

	$sub_query = "
		SELECT
			t1.`subdomain_id` AS sub_id,
			t1.`subdomain_name` AS sub_name,
			t2.`mail_id`,
			t2.`mail_acc`,
			t2.`mail_type`,
			t2.`status`,
		CONCAT(
			LEFT(t2.`mail_forward`, 20),
			IF(LENGTH(t2.`mail_forward`) > 20, '...', '')
		) AS 'mail_forward'
		FROM
			`subdomain` AS t1,
			`mail_users` AS t2
		WHERE
			t1.`domain_id` = ?
		AND
			t2.`domain_id` = ?
		AND
			(
				t2.`mail_type` LIKE '%".MT_SUBDOM_MAIL."%'
			OR
				t2.`mail_type` LIKE '%".MT_SUBDOM_FORWARD."%'
			)
		AND
			t1.`subdomain_id` = t2.`sub_id`
	";

	$sub_query .= "
		ORDER BY
			t2.`mail_acc` ASC,
			t2.`mail_type` DESC
	";

	$rs = exec_query($sql, $sub_query, array($dmn_id, $dmn_id));

	if ($rs->recordCount() == 0) {
		return 0;
	} else {
		while (!$rs->EOF) {
			$mail_acc = decode_idna($rs->fields['mail_acc']);

			$show_sub_name = decode_idna($rs->fields['sub_name']);
			$show_dmn_name = decode_idna($dmn_name);

			$mail_types = explode(',', $rs->fields['mail_type']);
			$mail_type = '';

			foreach ($mail_types as $type) {
				$mail_type .= user_trans_mail_type($type);

				if (strpos($type, '_forward') !== false) {
						$mail_type .= ': ' . str_replace(
							array("\r\n", "\n", "\r"),
							", ",
							$rs->fields['mail_forward']
						);
				}

				$mail_type .= '<br />';
			}

			$tpl->append(
				array(
					'MAIL_ACC'			=> tohtml($mail_acc.'@'.$show_sub_name.'.'.$show_dmn_name),
					'MAIL_TYPE'			=> $mail_type,
					'MAIL_STATUS'		=> translate_dmn_status($rs->fields['status']),
					'MAIL_EDIT_URL'		=> 'mail_edit.php?id=' . $rs->fields['mail_id'],
					'MAIL_DELETE_URL'	=> 'mail_delete.php?id=' . $rs->fields['mail_id']
				)
			);

			$rs->moveNext();
		}

		return $rs->recordCount();
	}
} // end gen_page_sub_mail_list()

/**
 * Must be documented
 *
 * @param EasySCP_TemplateEngine $tpl reference to the template object
 * @param EasySCP_Database $sql reference to the EasySCP_Database object
 * @param int $dmn_id domain name id
 * @param string $dmn_name domain name
 * @return int number of subdomain alias mails addresses
 */
function gen_page_als_sub_mail_list($tpl, $sql, $dmn_id, $dmn_name) {

	$sub_query = "
		SELECT
			t1.`mail_id`,
			t1.`mail_acc`,
			t1.`mail_type`,
			t1.`status`,
		CONCAT(
			LEFT(t1.`mail_forward`, 20),
			IF(LENGTH(t1.`mail_forward`) > 20, '...', '')
		) AS 'mail_forward',
		CONCAT(
			t2.`subdomain_alias_name`, '.', t3.`alias_name`
		) AS 'alssub_name'
		FROM
			`mail_users` AS t1
		LEFT JOIN (
			`subdomain_alias` AS t2
		) ON (
			t1.`sub_id` = t2.`subdomain_alias_id`
		)
		LEFT JOIN (
			`domain_aliasses` AS t3
		) ON (
			t2.`alias_id` = t3.`alias_id`
		)
		WHERE
			t1.`domain_id` = ?
		AND
			(
				t1.`mail_type` LIKE '%".MT_ALSSUB_MAIL."%'
			OR
				t1.`mail_type` LIKE '%".MT_ALSSUB_FORWARD."%'
			)
	";

	$sub_query .= "
		ORDER BY
			t1.`mail_acc` ASC,
			t1.`mail_type` DESC
	";

	$rs = exec_query($sql, $sub_query, $dmn_id);

	if ($rs->recordCount() == 0) {
		return 0;
	} else {
		while (!$rs->EOF) {
			$mail_acc = decode_idna($rs->fields['mail_acc']);
			$show_alssub_name = decode_idna($rs->fields['alssub_name']);
			$mail_types = explode(',', $rs->fields['mail_type']);
			$mail_type = '';

			foreach ($mail_types as $type) {
				$mail_type .= user_trans_mail_type($type);

				if (strpos($type, '_forward') !== false) {
					$mail_type .= ': ' . str_replace(
						array("\r\n", "\n", "\r"), ", ",
						$rs->fields['mail_forward']
					);
				}

				$mail_type .= '<br />';
			}

			$tpl->append(
				array(
					'MAIL_ACC'			=> tohtml($mail_acc . '@' . $show_alssub_name),
					'MAIL_TYPE'			=> $mail_type,
					'MAIL_STATUS'		=> translate_dmn_status($rs->fields['status']),
					'MAIL_EDIT_URL'		=> 'mail_edit.php?id=' . $rs->fields['mail_id'],
					'MAIL_DELETE_URL'	=> 'mail_delete.php?id=' . $rs->fields['mail_id']
				)
			);

			$rs->moveNext();
		}

		return $rs->recordCount();
	}
} // end gen_page_als_sub_mail_list()

/**
 * Must be documented
 *
 * @param EasySCP_TemplateEngine $tpl reference to template object
 * @param EasySCP_Database $sql reference to the EasySCP_Database object
 * @param int $dmn_id domain name id;
 * @param string $dmn_name domain name
 * @return int number of domain alias mails addresses
 */
function gen_page_als_mail_list($tpl, $sql, $dmn_id, $dmn_name) {

	$als_query = "
		SELECT
			t1.`alias_id` AS als_id,
			t1.`alias_name` AS als_name,
			t2.`mail_id`,
			t2.`mail_acc`,
			t2.`mail_type`,
			t2.`status`,
		CONCAT(
			LEFT(t2.`mail_forward`, 20),
			IF( LENGTH(t2.`mail_forward`) > 20, '...', '')
		) AS 'mail_forward'
		FROM
			`domain_aliasses` AS t1,
			`mail_users` AS t2
		WHERE
			t1.`domain_id` = ?
		AND
			t2.`domain_id` = ?
		AND
			t1.`alias_id` = t2.`sub_id`
		AND
			(
				t2.`mail_type` LIKE '%".MT_ALIAS_MAIL."%'
			OR
				t2.`mail_type` LIKE '%".MT_ALIAS_FORWARD."%'
			)
	";

	$als_query .= "
		ORDER BY
			t2.`mail_acc` ASC,
			t2.`mail_type` DESC
	";

	$rs = exec_query($sql, $als_query, array($dmn_id, $dmn_id));

	if ($rs->recordCount() == 0) {
		return 0;
	} else {
		while (!$rs->EOF) {
			$mail_acc = decode_idna($rs->fields['mail_acc']);
			// Unused variable
			// $show_dmn_name = decode_idna($dmn_name);
			$show_als_name = decode_idna($rs->fields['als_name']);
			$mail_types = explode(',', $rs->fields['mail_type']);
			$mail_type = '';

			foreach ($mail_types as $type) {
				$mail_type .= user_trans_mail_type($type);

				if (strpos($type, '_forward') !== false) {
					 $mail_type .= ': ' . str_replace(
					 	array("\r\n", "\n", "\r"),
						", ",
						$rs->fields['mail_forward']
					 );
				}

				$mail_type .= '<br />';
			}

			$tpl->append(
				array(
					'MAIL_ACC'			=> tohtml($mail_acc . '@' . $show_als_name),
					'MAIL_TYPE'			=> $mail_type,
					'MAIL_STATUS'		=> translate_dmn_status($rs->fields['status']),
					'MAIL_EDIT_URL'		=> 'mail_edit.php?id=' . $rs->fields['mail_id'],
					'MAIL_DELETE_URL'	=> 'mail_delete.php?id=' . $rs->fields['mail_id']
				)
			);

			$rs->moveNext();
		}

		return $rs->recordCount();
	}
} // end gen_page_als_mail_list()

/**
 * Must be documented
 *
 * @param EasySCP_TemplateEngine $tpl Reference to the template object
 * @param EasySCP_Database $sql Reference to the EasySCP_Database object
 * @param int $user_id Customer id
 * @return void
 */
function gen_page_lists($tpl, $sql, $user_id) {

	global $dmn_id;

	$dmn_props = get_domain_default_props($user_id);

	$dmn_id = $dmn_props['domain_id'];

	$dmn_mails = gen_page_dmn_mail_list($tpl, $sql, $dmn_id, $dmn_props['domain_name']);
	$sub_mails = gen_page_sub_mail_list($tpl, $sql, $dmn_id, $dmn_props['domain_name']);
	$als_mails = gen_page_als_mail_list($tpl, $sql, $dmn_id, $dmn_props['domain_name']);
	$alssub_mails = gen_page_als_sub_mail_list($tpl, $sql, $dmn_id, $dmn_props['domain_name']);

	$total_mails = $dmn_mails + $sub_mails + $als_mails + $alssub_mails;

	if ($total_mails > 0) {
		$tpl->assign(
			array(
				'MAIL_MESSAGE'			=> '',
				'DMN_TOTAL'				=> $dmn_mails,
				'SUB_TOTAL'				=> $sub_mails,
				'ALSSUB_TOTAL'			=> $sub_mails,
				'ALS_TOTAL'				=> $als_mails,
				'TOTAL_MAIL_ACCOUNTS'	=> $total_mails,
				'ALLOWED_MAIL_ACCOUNTS'	=> ($dmn_props['domain_mailacc_limit'] != 0) ? $dmn_props['domain_mailacc_limit'] : tr('unlimited')
			)
		);
	} else {
		$tpl->assign(
			array(
				'MAIL_MSG'		=> tr('Mail account list is empty!'),
				'MAIL_MSG_TYPE'	=> 'info',
				'MAIL_ITEM'		=> '',
				'MAILS_TOTAL'	=> ''
			)
		);

	}

} // end gen_page_lists()
?>