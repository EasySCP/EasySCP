{include file='client/header.tpl'}
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
			{if isset($YOU_ARE_LOGGED_AS)}
			<li><a href="change_user_interface.php?action=go_back" class="backadmin">{$YOU_ARE_LOGGED_AS}</a></li>
			{/if}
			<li><a href="../index.php?logout" class="logout">{$TR_MENU_LOGOUT}</a></li>
		</ul>
		<ul class="path">
			<li><a>{$TR_MENU_OVERVIEW}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="stats"><span>{$TR_DOMAIN_STATISTICS}</span></h2>
		<form action="domain_statistics.php" method="post" id="client_domain_statistics">
			<fieldset>
				<label for="month">{$TR_MONTH}</label>
				<select name="month" id="month">
					{section name=i loop=$MONTH}
					<option {$MONTH_SELECTED[i]}>{$MONTH[i]}</option>
					{/section}
				</select>
				<label for="year">{$TR_YEAR}</label>
				<select name="year" id="year">
					{section name=i loop=$YEAR}
					<option {$YEAR_SELECTED[i]}>{$YEAR[i]}</option>
					{/section}
				</select>
				<input type="hidden" name="uaction" value="show_traff" />
				<input type="submit" name="Submit" value="{$TR_SHOW}" />
			</fieldset>
		</form>
		<table>
			<thead>
				<tr>
					<th>{$TR_DATE}</th>
					<th>{$TR_WEB_TRAFF_IN}</th>
					<th>{$TR_WEB_TRAFF_OUT}</th>
					<th>{$TR_FTP_TRAFF}</th>
					<!-- <th>{$TR_SMTP_TRAFF}</th> -->
					<!-- <th>{$TR_POP_TRAFF}</th> -->
					<th>{$TR_SUM}</th>
				</tr>
			</thead>
			{if isset($DATE)}
				<tfoot>
					<tr>
						<td>{$TR_ALL}</td>
						<td>{$WEB_ALL_IN}</td>
						<td>{$WEB_ALL_OUT}</td>
						<td>{$FTP_ALL}</td>
						<!-- <td>{$SMTP_ALL}</td> -->
						<!-- <td>{$POP3_ALL}</td> -->
						<td>{$SUM_ALL}</td>
					</tr>
				</tfoot>
				<tbody>
					{section name=i loop=$DATE}
						<tr>
							<td>{$DATE[i]}</td>
							<td>{$WEB_TRAFFIC_IN[i]}</td>
							<td>{$WEB_TRAFFIC_OUT[i]}</td>
							<td>{$FTP_TRAFFIC[i]}</td>
							<!-- <td>{$SMTP_TRAFFIC[i]}</td> -->
							<!-- <td>{$POP3_TRAFFIC[i]}</td> -->
							<td>{$SUM_TRAFFIC[i]}</td>
						</tr>
					{/section}
				</tbody>
			{/if}
		</table>
	</div>
{include file='client/footer.tpl'}