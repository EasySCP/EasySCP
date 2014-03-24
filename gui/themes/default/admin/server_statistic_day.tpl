{include file='admin/header.tpl'}
<body>
	<div class="header">
		{include file="$MAIN_MENU"}
		<div class="logo">
			<img src="{$THEME_COLOR_PATH}/images/easyscp_logo.png" alt="EasySCP logo" />
			<img src="{$THEME_COLOR_PATH}/images/easyscp_webhosting.png" alt="EasySCP - Easy Server Control Panel" />
		</div>
	</div>
	<div class="location">
		<ul class="location-menu">
			<li><a href="../index.php?logout" class="logout">{$TR_MENU_LOGOUT}</a></li>
		</ul>
		<ul class="path">
			<li><a href="server_statistic.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a>{$TR_SERVER_DAY_STATISTICS}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="general"><span>{$TR_SERVER_DAY_STATISTICS}</span></h2>
		<p>{$TR_YEAR} {$YEAR}&nbsp;&nbsp;&nbsp;&nbsp; {$TR_MONTH} {$MONTH}&nbsp;&nbsp;&nbsp;{$TR_DAY} {$DAY}</p>
		{if isset($HOUR)}
		<table>
			<thead>
				<tr>
					<th>{$TR_HOUR}</th>
					<th>{$TR_WEB_IN}</th>
					<th>{$TR_WEB_OUT}</th>
					<th>{$TR_SMTP_IN}</th>
					<th>{$TR_SMTP_OUT}</th>
					<th>{$TR_POP_IN}</th>
					<th>{$TR_POP_OUT}</th>
					<th>{$TR_OTHER_IN}</th>
					<th>{$TR_OTHER_OUT}</th>
					<th>{$TR_ALL_IN}</th>
					<th>{$TR_ALL_OUT}</th>
					<th>{$TR_ALL}</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td>{$TR_ALL}</td>
					<td>{$WEB_IN_ALL}</td>
					<td>{$WEB_OUT_ALL}</td>
					<td>{$SMTP_IN_ALL}</td>
					<td>{$SMTP_OUT_ALL}</td>
					<td>{$POP_IN_ALL}</td>
					<td>{$POP_OUT_ALL}</td>
					<td>{$OTHER_IN_ALL}</td>
					<td>{$OTHER_OUT_ALL}</td>
					<td>{$ALL_IN_ALL}</td>
					<td>{$ALL_OUT_ALL}</td>
					<td>{$ALL_ALL}</td>
				</tr>
			</tfoot>
			<tbody>
				{section name=i loop=$HOUR}
				<tr>
					<td>{$HOUR[i]}</td>
					<td>{$WEB_IN[i]}</td>
					<td>{$WEB_OUT[i]}</td>
					<td>{$SMTP_IN[i]}</td>
					<td>{$SMTP_OUT[i]}</td>
					<td>{$POP_IN[i]}</td>
					<td>{$POP_OUT[i]}</td>
					<td>{$OTHER_IN[i]}</td>
					<td>{$OTHER_OUT[i]}</td>
					<td>{$ALL_IN[i]}</td>
					<td>{$ALL_OUT[i]}</td>
					<td>{$ALL[i]}</td>
				</tr>
				{/section}
			</tbody>
		</table>
		{/if}
	</div>
{include file='admin/footer.tpl'}