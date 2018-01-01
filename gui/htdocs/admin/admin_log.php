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
$template = 'admin/admin_log.tpl';

// static page messages
clear_log();

generate_page ($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'				=> tr('EasySCP - Admin/Admin Log'),
		'TR_ADMIN_LOG'				=> tr('Admin Log'),
		'TR_CLEAR_LOG'				=> tr('Clear log'),
		'TR_DATE'					=> tr('Date'),
		'TR_MESSAGE'				=> tr('Message'),
		'TR_CLEAR_LOG_MESSAGE'		=> tr('Delete from log:'),
		'TR_CLEAR_LOG_EVERYTHING'	=> tr('everything'),
		'TR_CLEAR_LOG_LAST2'		=> tr('older than 2 weeks'),
		'TR_CLEAR_LOG_LAST4'		=> tr('older than 1 month'),
		'TR_CLEAR_LOG_LAST12'		=> tr('older than 3 months'),
		'TR_CLEAR_LOG_LAST26'		=> tr('older than 6 months'),
		'TR_CLEAR_LOG_LAST52'		=> tr('older than 12 months'),
	)
);

gen_admin_mainmenu($tpl, 'admin/main_menu_general_information.tpl');
gen_admin_menu($tpl, 'admin/menu_general_information.tpl');

gen_page_message($tpl);

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

unset_messages();

/**
 * @param EasySCP_TemplateEngine $tpl
 */
function generate_page($tpl) {

	$cfg = EasySCP_Registry::get('Config');
	$sql = EasySCP_Registry::get('Db');

	$start_index = 0;
	$rows_per_page = 12;

	if (isset($_GET['psi']) && is_numeric($_GET['psi']))
		$start_index = intval($_GET['psi']);

	$count_query = "
		SELECT
			COUNT(`log_id`) AS cnt
		FROM
			`log`;
	";

	$query = "
		SELECT
			DATE_FORMAT(`log_time`, '%Y-%m-%d %H:%i') AS dat, `log_message`
		FROM
			`log`
		ORDER BY
			`log_time` DESC
		LIMIT
			$start_index, $rows_per_page;
	";

	$rs = exec_query($sql, $count_query);

	$records_count = $rs->fields['cnt'];

	$rs = exec_query($sql, $query);

	if ($rs->rowCount() == 0) {
		// set_page_message(tr('Log is empty!'), 'info');
		$tpl->assign(
			array(
				'PAG_MESSAGE'	=> tr('Log is empty!')
			)
		);
	} else {
		$prev_si = $start_index - $rows_per_page;

		if ($start_index == 0) {
			$tpl->assign('SCROLL_PREV', '');
		} else {
			$tpl->assign(
				array(
					'SCROLL_PREV_GRAY'	=> '',
					'PREV_PSI'			=> $prev_si
				)
			);
		}

		$next_si = $start_index + $rows_per_page;

		if ($next_si + 1 > $records_count) {
			$tpl->assign('SCROLL_NEXT', '');
		} else {
			$tpl->assign(
				array(
					'SCROLL_NEXT_GRAY'	=> '',
					'NEXT_PSI'			=> $next_si
				)
			);
		}

		$tpl->assign(
			array(
				'PAGE_MESSAGE' => ''
			)
		);


		while (!$rs->EOF) {
			$log_message = $rs->fields['log_message'];

			$replaces = array(
				'/[^a-zA-Z](delete[sd]?)[^a-zA-Z]/i'	=> ' <strong style="color:#f00">\\1</strong> ',
				'/[^a-zA-Z](remove[sd]?)[^a-zA-Z]/i'	=> ' <strong style="color:#f00">\\1</strong> ',
				'/[^a-zA-Z](add(s|ed)?)[^a-zA-Z]/i'		=> ' <strong style="color:#3c6">\\1</strong> ',
				'/[^a-zA-Z](change[sd]?)[^a-zA-Z]/i'	=> ' <strong style="color:#30f">\\1</strong> ',
				'/[^a-zA-Z](update[sd]?)[^a-zA-Z]/i'	=> ' <strong style="color:#30f">\\1</strong> ',
				'/[^a-zA-Z](edit(s|ed)?)[^a-zA-Z]/i'	=> ' <strong style="color:#3c6">\\1</strong> ',
				'/[^a-zA-Z](unknown)[^a-zA-Z]/i'		=> ' <strong style="color:#c0f">\\1</strong> ',
				'/[^a-zA-Z](logged)[^a-zA-Z]/i'			=> ' <strong style="color:#360">\\1</strong> ',
				'/[^a-zA-Z]((session )?manipulation)[^a-zA-Z]/i'	=> ' <strong style="color:#f00">\\1</strong> ',
				'/[^a-zA-Z]*(Warning[\!]?)[^a-zA-Z]/i'	=> ' <strong style="color:#f00">\\1</strong> ',
				'/(bad password login data)/i'			=> ' <strong style="color:#f00">\\1</strong> '
			);

			foreach ($replaces as $pattern => $replacement) {
				$log_message = preg_replace($pattern, $replacement, $log_message);
			}

			$date_formt = $cfg->DATE_FORMAT . ' H:i';
			$tpl->append(
				array(
					'ADM_MESSAGE'	=> $log_message,
					'DATE'		=> date($date_formt, strtotime($rs->fields['dat'])),
				)
			);


			$rs->moveNext();
		} // end while
	}
}

function clear_log() {
	$sql = EasySCP_Registry::get('Db');

	if (isset($_POST['uaction']) && $_POST['uaction'] === 'clear_log') {

		switch ($_POST['uaction_clear']) {
			case 0:
				$query = "DELETE FROM `log`";
				$msg = tr('%s deleted the full admin log!', $_SESSION['user_logged']);
				break;

			case 2:
				// 2 Weeks
				$query = "
					DELETE FROM
						`log`
					WHERE
						DATE_SUB(CURDATE(), INTERVAL 14 DAY) >= `log_time`;
				";
				$msg = tr('%s deleted the admin log older than two weeks!', $_SESSION['user_logged']);

				break;

			case 4:
				$query = "
					DELETE FROM
						`log`
					WHERE
						DATE_SUB(CURDATE(), INTERVAL 1 MONTH) >= `log_time`;
				";
				$msg = tr('%s deleted the admin log older than one month!', $_SESSION['user_logged']);

				break;

			case 12:
				$query = "
					DELETE FROM
						`log`
					WHERE
						DATE_SUB(CURDATE(), INTERVAL 3 MONTH) >= `log_time`;
				";
				$msg = tr('%s deleted the admin log older than three months!', $_SESSION['user_logged']);
				break;

			case 26:
				$query = "
					DELETE FROM
						`log`
					WHERE
						DATE_SUB(CURDATE(), INTERVAL 6 MONTH) >= `log_time`;
				";
				$msg = tr('%s deleted the admin log older than six months!', $_SESSION['user_logged']);
				break;

			case 52;
				$query = "
					DELETE FROM
						`log`
					WHERE
						DATE_SUB(CURDATE(), INTERVAL 1 YEAR) >= `log_time`;
				";
				$msg = tr('%s deleted the admin log older than one year!', $_SESSION['user_logged']);

				break;
			default:
				throw new EasySCP_Exception(tr('Invalid time period!'));
		}

		execute_query($sql, $query);
		write_log($msg);
	}
}
?>