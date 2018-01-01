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

require '../../include/easyscp-lib.php';

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'client/hosting_plan_update.tpl';

if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
	del_order($tpl, $sql, $_GET['delete_id'], $_SESSION['user_id']);
}

if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
	add_new_order($tpl, $sql, $_GET['order_id'], $_SESSION['user_id']);
}

gen_hp($tpl, $sql, $_SESSION['user_id']);

// static page messages
gen_logged_from($tpl);

check_permissions($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'	=> tr('EasySCP - Update hosting plan'),
		'TR_LANGUAGE'	=> tr('Language'),
		'TR_SAVE'		=> tr('Save'),
	)
);

gen_client_mainmenu($tpl, 'client/main_menu_general_information.tpl');
gen_client_menu($tpl, 'client/menu_general_information.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

function check_update_current_value($curr, $new) {

	$result = true;
	if ($curr > 0) {
		if ($new == -1) {
			$result = false;
		} else {
			if ($new != 0 && $curr > $new) {
				$result = false;
			}
		}
	}

	return $result;
}

/**
 * @param EasySCP_TemplateEngine $tpl
 * @param EasySCP_Database $sql
 * @param int $user_id
 */
function gen_hp($tpl, $sql, $user_id) {

	$cfg = EasySCP_Registry::get('Config');

	// get domain id
	$query = "
		SELECT
			`domain_id`
		FROM
			`domain`
		WHERE
			`domain_admin_id` = ?
	";

	$rs = exec_query($sql, $query, $user_id);
	$domain_id = $rs->fields['domain_id'];

	// get current domain settings
	$query = "
		SELECT
			*
		FROM
			`domain`
		WHERE
			`domain_id` = ?
	";

	$rs = exec_query($sql, $query, $domain_id);
	$current = $rs->fetchRow();

	$hp_title = tr('Hosting plans available for update');
	// let's see if we have an order
	$query = "
		SELECT
			*
		FROM
			`orders`
		WHERE
			`customer_id` = ?
		AND
			`status` <> ?
	";

	$rs = exec_query($sql, $query, array($user_id, 'added'));

	if ($rs->recordCount() > 0) {
		$availabe_hp_id = $rs->fields['plan_id'];

		$query = "
			SELECT
				*
			FROM
				`hosting_plans`
			WHERE
				`id` = ?
		";

		$rs = exec_query($sql, $query, $availabe_hp_id);
		$count = 2;
		$purchase_text = tr('Cancel order');
		$purchase_link = 'delete_id';
		$hp_title = tr('Your order');
	} else {
		// generate all hosting plans available for purchasing
		if (isset($cfg->HOSTING_PLANS_LEVEL) && $cfg->HOSTING_PLANS_LEVEL == 'admin') {
			$query = "
				SELECT
					t1.*,
					t2.`admin_id`, t2.`admin_type`
				FROM
					`hosting_plans` AS t1,
					`admin` AS t2
				WHERE
					t2.`admin_type` = ?
				AND
					t1.`reseller_id` = t2.`admin_id`
				AND
					t1.`status` = '1'
				ORDER BY
					t1.`name`
			";

			$rs = exec_query($sql, $query, 'admin');

			$count = $rs->recordCount();
			$count++;
		} else {
			$query = "
				SELECT
					*
				FROM
					`hosting_plans`
				WHERE
					`reseller_id` = ?
				AND
					`status` = '1'
			";

			$count_query = "
				SELECT
					COUNT(`id`) AS cnum
				FROM
					`hosting_plans`
				WHERE
					`reseller_id` = ?
				AND
					`status` = '1'
			";

			$cnt = exec_query($sql, $count_query, $_SESSION['user_created_by']);
			$rs = exec_query($sql, $query, $_SESSION['user_created_by']);
			$count = $cnt->fields['cnum'] + 1;
		}

		$purchase_text = tr('Purchase');
		$purchase_link = 'order_id';
	}

	if ($rs->recordCount() == 0) {
		$tpl->assign(
			array(
				'TR_HOSTING_PLANS'	=> $hp_title,
				'HOSTING_PLANS'		=> '',
				'HP_ORDER'			=> '',
				'COLSPAN'			=> 2
			)
		);

		set_page_message(
			tr('There are no available updates'),
			'info'
		);
		return;
	}

	$tpl->assign('COLSPAN', $count);
	$i = 0;

	while (!$rs->EOF) {

		list(
			$hp_php,
			$hp_cgi,
			$hp_sub,
			$hp_als,
			$hp_mail,
			$hp_ftp,
			$hp_sql_db,
			$hp_sql_user,
			$hp_traff,
			$hp_disk,
			,
			$hp_dns
		) = explode(";", $rs->fields['props']);

		$warning_msgs = $error_msgs = array();

		if ($hp_php === '_yes_') {
			$details = tr('PHP Support: enabled') . "<br />";
			$php = "yes";
		} else {
			$details = tr('PHP Support: disabled') . "<br />";
			$php = "no";

			if ($current['domain_php'] == 'yes') {
				$warning_msgs[] = tr("You have PHP enabled, but the new hosting plan doesn't has this feature.");
			}
		}

		if ($hp_cgi === '_yes_') {
			$cgi = "yes";
			$details .= tr('CGI Support: enabled') . "<br />";
		} else {
			$cgi = "no";
			$details .= tr('CGI Support: disabled') . "<br />";

			if ($current['domain_cgi'] == 'yes') {
				$warning_msgs[] = tr("You have CGI enabled, but the new hosting plan doesn't has this feature.");
			}
		}

		if ($hp_dns === '_yes_') {
			$dns = "yes";
			$details .= tr('DNS Support: enabled') . "<br />";
		} else {
			$dns = "no";
			$details .= tr('DNS Support: disabled') . "<br />";

			if ($current['domain_dns'] == 'yes') {
				$warning_msgs[] = tr("You have DNS enabled, but the new hosting plan doesn't has this feature.");
			}
		}

		$traffic = get_user_traffic($domain_id);

		$curr_value = $traffic[7] / 1048576; // convert disk usage to MB

		if (!check_update_current_value($curr_value, $hp_disk)) {
			$error_msgs[] = tr("You have more disk space in use than the new hosting plan limits.");
		}

		$hdd_usage = tr('Disk limit') . ": " . translate_limit_value($hp_disk, true) . "<br />";

		$curr_value = $traffic[10] / 1048576; // convert max. traffic to MB

		if (!check_update_current_value($curr_value, $hp_traff)) {
			$warning_msgs[] = tr("You did have more traffic than the new hosting plan limits.");
		}

		$traffic_usage = tr('Traffic limit') . ": " . translate_limit_value($hp_traff, true);

		$curr_value = get_domain_running_als_cnt($sql, $domain_id);

		if (!check_update_current_value($curr_value, $hp_als)) {
			$error_msgs[] = tr("You have more aliases in use than the new hosting plan limits.");
		}

		$details .= tr('Aliases') . ": " . translate_limit_value($hp_als) . "<br />";

		$curr_value = get_domain_running_sub_cnt($sql, $domain_id);

		if (!check_update_current_value($curr_value, $hp_sub)) {
			$error_msgs[] = tr("You have more subdomains in use than the new hosting plan limits.");
		}

		$details .= tr('Subdomains') . ": " . translate_limit_value($hp_sub) . "<br />";

		$curr_value = get_domain_running_mail_acc_cnt($sql, $domain_id);

		if (!check_update_current_value($curr_value[0], $hp_mail)) {
			$error_msgs[] = tr("You have more Email addresses in use than the new hosting plan limits.");
		}

		$details .= tr('Emails') . ": " . translate_limit_value($hp_mail) . "<br />";

		$curr_value = get_domain_running_ftp_acc_cnt($sql, $domain_id);

		if (!check_update_current_value($curr_value[0], $hp_ftp)) {
			$error_msgs[] = tr("You have more FTP accounts in use than the new hosting plan limits.");
		}

		$details .= tr('FTPs') . ": " . translate_limit_value($hp_ftp) . "<br />";

		$curr_value = get_domain_running_sqld_acc_cnt($sql, $domain_id);

		if (!check_update_current_value($curr_value, $hp_sql_db)) {
			$error_msgs[] = tr("You have more SQL databases in use than the new hosting plan limits.");
		}

		$details .= tr('SQL Databases') . ": " . translate_limit_value($hp_sql_db) . "<br />";

		$curr_value = get_domain_running_sqlu_acc_cnt($sql, $domain_id);

		if (!check_update_current_value($curr_value, $hp_sql_user)) {
			$error_msgs[] = tr("You have more SQL database users in use than the new hosting plan limits.");
		}

		$details .= tr('SQL Users') . ": " . translate_limit_value($hp_sql_user) . "<br />";

		$details .= $hdd_usage . $traffic_usage;

		$price = $rs->fields['price'];

		if ($price == 0 || $price == '') {
			$price = tr('free of charge');
		} else {
			$price = $price . " " . $rs->fields['value'] . " " . $rs->fields['payment'];
		}

		$check_query = "
			SELECT
				`domain_id`
			FROM
				`domain`
			WHERE
				`domain_admin_id` = ?
			AND
				`domain_mailacc_limit` = ?
			AND
				`domain_ftpacc_limit` = ?
			AND
				`domain_traffic_limit` = ?
			AND
				`domain_sqld_limit` = ?
			AND
				`domain_sqlu_limit` = ?
			AND
				`domain_alias_limit` = ?
			AND
				`domain_subd_limit` = ?
			AND
				`domain_disk_limit` = ?
			AND
				`domain_php` = ?
			AND
				`domain_cgi` = ?
			AND
				`domain_dns` = ?
		";

		$check = exec_query($sql, $check_query,
			array(
				$_SESSION['user_id'],
				$hp_mail, $hp_ftp, $hp_traff,
				$hp_sql_db, $hp_sql_user,
				$hp_als, $hp_sub, $hp_disk,
				$php, $cgi, $dns
			)
		);

		if ($check->recordCount() == 0) {

			$link_purchase = '<a href="hosting_plan_update.php?'
				. $purchase_link.'='.$rs->fields['id']
				. '" class="linkdark">';

			if ($purchase_link == 'order_id' && count($error_msgs) > 0) {
				$link_purchase = tr('You cannot update to this hosting plan, see notices in text.');
				if (count($warning_msgs) > 0) {
					$warning_text = '<br /><br /><strong>'.tr('Warning:').'</strong><br />'.implode('<br />', $warning_msgs);
				} else {
					$warning_text = '';
				}
				$warning_text .= '<br /><br /><strong>'.tr('Caution:').'</strong><br />'.implode('<br />', $error_msgs);
			} elseif ($purchase_link == 'order_id' && count($warning_msgs) > 0) {
				$warning_text = '<br /><br /><strong>'.tr('Warning:').'</strong><br />'.implode('<br />', $warning_msgs);
				$link_purchase .= tr('I understand the warnings - Purchase!');
				$link_purchase .= '</a>';
			} else {
				$warning_text = '';
				$link_purchase .= '{TR_PURCHASE}</a>';
			}

			$tpl->append(
				array(
					'HP_NAME'			=> tohtml($rs->fields['name']),
					'HP_DESCRIPTION'	=> tohtml($rs->fields['description']),
					'HP_DETAILS'		=> $details.$warning_text,
					'HP_COSTS'			=> tohtml($price),
					'ID'				=> $rs->fields['id'],
					'LINK'				=> $purchase_link,
					'ITHEM'				=> ($i % 2 == 0) ? 'content' : 'content2',
					'LINK_PURCHASE'		=> $link_purchase
				)
			);

			$i++;
		}
		$purchase_text = tr('Purchase');
		$purchase_link = 'order_id';
		$rs->moveNext();
	}
	if ($i == 0) {
		$tpl->assign(
			array(
				'HOSTING_PLANS'		=> '',
				'HP_ORDER'			=> '',
				'TR_HOSTING_PLANS'	=> $hp_title,
				'COLSPAN'			=> '2'
			)
		);

		set_page_message(
			tr('There are no available hosting plans for update'),
			'info'
		);
	}

	$tpl->assign(
		array(
			'TR_HOSTING_PLANS'	=> $hp_title,
			'TR_PURCHASE'		=> $purchase_text
		)
	);
}

/**
 * @todo the 2nd query has 2 identical tables in FROM-clause, is this OK?
 */
function add_new_order($tpl, $sql, $order_id, $user_id) {

	$cfg = EasySCP_Registry::get('Config');

	// get domain id
	$query = "
		SELECT
			`domain_id`
		FROM
			`domain`
		WHERE
			`domain_admin_id` = ?
	";

	$rs = exec_query($sql, $query, $user_id);
	$domain_id = $rs->fields['domain_id'];

	$query = "
		SELECT
			*
		FROM
			`hosting_plans`
		WHERE
			`id` = ?
	";

	$error_msgs = array();
	$rs = exec_query($sql, $query, $order_id);
	list(, , $hp_sub, $hp_als, $hp_mail, $hp_ftp, $hp_sql_db, $hp_sql_user, , $hp_disk) = explode(";", $rs->fields['props']);

	$traffic = get_user_traffic($domain_id);

	$curr_value = $traffic[7] / 1048576; // disk usage
	if (!check_update_current_value($curr_value, $hp_disk)) {
		$error_msgs[] = tr("You have more disk space in use than the new hosting plan limits.");
	}

	$curr_value = get_domain_running_als_cnt($sql, $domain_id);
	if (!check_update_current_value($curr_value, $hp_als)) {
		$error_msgs[] = tr("You have more aliases in use than the new hosting plan limits.");
	}

	$curr_value = get_domain_running_sub_cnt($sql, $domain_id);
	if (!check_update_current_value($curr_value, $hp_sub)) {
		$error_msgs[] = tr("You have more subdomains in use than the new hosting plan limits.");
	}

	$curr_value = get_domain_running_mail_acc_cnt($sql, $domain_id);
	if (!check_update_current_value($curr_value[0], $hp_mail)) {
		$error_msgs[] = tr("You have more e-mail addresses in use than the new hosting plan limits.");
	}

	$curr_value = get_domain_running_ftp_acc_cnt($sql, $domain_id);
	if (!check_update_current_value($curr_value[0], $hp_ftp)) {
		$error_msgs[] = tr("You have more FTP accounts in use than the new hosting plan limits.");
	}

	$curr_value = get_domain_running_sqld_acc_cnt($sql, $domain_id);
	if (!check_update_current_value($curr_value, $hp_sql_db)) {
		$error_msgs[] = tr("You have more SQL databases in use than the new hosting plan limits.");
	}

	$curr_value = get_domain_running_sqlu_acc_cnt($sql, $domain_id);
	if (!check_update_current_value($curr_value, $hp_sql_user)) {
		$error_msgs[] = tr("You have more SQL database users in use than the new hosting plan limits.");
	}

	if (count($error_msgs) > 0) {
		set_page_message(implode('<br />', $error_msgs), 'error');
		user_goto('hosting_plan_update.php');
	}

	$date = time();
	$status = "update";
	$query = "
		INSERT INTO `orders`
			(`user_id`,
			`plan_id`,
			`date`,
			`domain_name`,
			`customer_id`,
			`fname`,
			`lname`,
			`firm`,
			`zip`,
			`city`,
			`state`,
			`country`,
			`email`,
			`phone`,
			`fax`,
			`street1`,
			`street2`,
			`status`)
		VALUES
			(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
	";

	exec_query($sql, $query, array(
		$_SESSION['user_created_by'], $order_id, $date, $_SESSION['user_logged'],
		$user_id, '', '', '', '', '', '', '', '', '', '', '', '', $status
	));
	set_page_message(
		tr('Your request for hosting pack update was added successfully'),
		'success'
	);

	$query = "
		SELECT
			t1.`email` AS reseller_mail,
			t2.`email` AS user_mail
		FROM
			`admin` AS t1,
			`admin` AS t2
		WHERE
			t1.`admin_id` = ?
		AND
			t2.`admin_id` = ?
	";

	$rs = exec_query($sql, $query, array($_SESSION['user_created_by'], $_SESSION['user_id']));

	$to = $rs->fields['reseller_mail'];
	$from = $rs->fields['user_mail'];

	$headers = "From: " . $from . "\n";
	$headers .= "MIME-Version: 1.0\nContent-Type: text/plain; charset=utf-8\nContent-Transfer-Encoding: 7bit\n";
	$headers .= "X-Mailer: EasySCP auto mailer";

	$subject = tr("[EasySCP OrderPanel] - You have an update order", true);

	$message = tr("You have an update order for the account %s\n\nPlease login into your EasySCP control panel at %s for more details",
		true,
		$_SESSION['user_logged'],
		$cfg->BASE_SERVER_VHOST_PREFIX . $cfg->BASE_SERVER_VHOST);

	mail($to, $subject, $message, $headers);
}

function del_order($tpl, $sql, $order_id, $user_id) {

	$query = "
		DELETE FROM
			`orders`
		WHERE
			`user_id` = ?
		AND
			`customer_id` = ?
	";

	exec_query($sql, $query, array($_SESSION['user_created_by'], $user_id));
	set_page_message(
		tr('Your request for hosting pack update was removed successfully'),
		'success'
	);
}
?>