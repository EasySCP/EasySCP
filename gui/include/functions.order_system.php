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

/**
 * @todo use template(s) instead of hardcoded XHTML
 * @param object $tpl	EasySCP_TemplateEngine instance
 * @param object $sql	EasySCP_Database instance
 * @param int $user_id
 * @param bool encode
 */
function gen_purchase_haf($tpl, $sql, $user_id, $encode = false) {

	$cfg = EasySCP_Registry::get('Config');

	$query = "
		SELECT
			`header`, `footer`
		FROM
			`orders_settings`
		WHERE
			`user_id` = ?
		;
	";

	$rs = exec_query($sql, $query, $user_id);

	if($rs->recordCount() == 0) {
		$THEME_CHARSET = tr('encoding');
		$title = tr("EasySCP - Order Panel");
		$THEME_COLOR_PATH = $cfg->LOGIN_TEMPLATE_PATH;

		$header = <<<RIC
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en">
<head>
	<title>{$title}</title>
	<meta http-equiv='Content-Script-Type' content='text/javascript' />
	<meta http-equiv='Content-Style-Type' content='text/css' />
	<meta http-equiv='Content-Type' content='text/html; charset={$THEME_CHARSET}' />
	<meta name='copyright' content='EasySCP' />
	<meta name='owner' content='EasySCP' />
	<meta name='publisher' content='EasySCP' />
	<meta name='robots' content='nofollow, noindex' />
	<meta name='title' content='{$title}' />
	<link href="/{$THEME_COLOR_PATH}/css/easyscp.orderpanel.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<div style="margin: 20px auto;">
		<table style="height: 95%;width: 100%;">
			<tr>
				<td align="center">
RIC;

		$footer = <<<RIC
				</td>
			</tr>
		</table>
	</div>
</body>
</html>
RIC;

	} else {
		$header = $rs->fields['header'];
		$footer = $rs->fields['footer'];
		$header = str_replace('\\', '', $header);
		$footer = str_replace('\\', '', $footer);
	}

	if($encode) {
		$header = htmlentities($header, ENT_COMPAT, 'UTF-8');
		$footer = htmlentities($footer, ENT_COMPAT, 'UTF-8');
	}

	$tpl->assign(
	array(
		'PURCHASE_HEADER' => $header,
		'PURCHASE_FOOTER' => $footer
		)
	);
}
?>