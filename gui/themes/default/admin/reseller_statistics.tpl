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
			<li><a>{$TR_RESELLER_STATISTICS}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="user"><span>{$TR_RESELLER_STATISTICS}</span></h2>
		{if isset($RESELLER_NAME)}
		<form action="reseller_statistics.php?psi={$POST_PREV_PSI}" method="post" id="admin_reseller_statistics">
			<p>
				{$TR_MONTH}
				<select name="month" id="month">
					{section name=i loop=$MONTH_VALUE}
					<option{$MONTH_SELECTED[i]}>{$MONTH_VALUE[i]}</option>
					{/section}
				</select>
				{$TR_YEAR}
				<select name="year" id="year">
					{section name=i loop=$YEAR_VALUE}
					<option{$YEAR_SELECTED[i]}>{$YEAR_VALUE[i]}</option>
					{/section}
				</select>
				<input type="hidden" name="uaction" value="show" />
				<input type="submit" name="Submit" value="  {$TR_SHOW}  " />
			</p>
		</form>
		<table>
			<thead>
				<tr>
					<th>{$TR_RESELLER_NAME}</th>
					<th>{$TR_TRAFF}</th>
					<th>{$TR_DISK}</th>
					<th>{$TR_DOMAIN}</th>
					<th>{$TR_SUBDOMAIN}</th>
					<th>{$TR_ALIAS}</th>
					<th>{$TR_MAIL}</th>
					<th>{$TR_FTP}</th>
					<th>{$TR_SQL_DB}</th>
					<th>{$TR_SQL_USER}</th>
				</tr>
			</thead>
			<tbody>
				{section name=i loop=$RESELLER_NAME}
				<tr>
					<td><a href="reseller_user_statistics.php?rid={$RESELLER_ID[i]}&amp;name={$RESELLER_NAME[i]}&amp;month={$MONTH[i]}&amp;year={$YEAR[i]}" title="{$RESELLER_NAME[i]}" class="icon i_domain">{$RESELLER_NAME[i]}</a></td>
					<td><div class="graph"><span style="width: {$TRAFF_PERCENT[i]}%">&nbsp;</span><strong>{$TRAFF_SHOW_PERCENT[i]}&nbsp;%</strong></div>{$TRAFF_MSG[i]}</td>
					<td><div class="graph"><span style="width: {$DISK_PERCENT[i]}%">&nbsp;</span><strong>{$DISK_SHOW_PERCENT[i]}&nbsp;%</strong></div>{$DISK_MSG[i]}</td>
					<td>{$DMN_MSG[i]}</td>
					<td>{$SUB_MSG[i]}</td>
					<td>{$ALS_MSG[i]}</td>
					<td>{$MAIL_MSG[i]}</td>
					<td>{$FTP_MSG[i]}</td>
					<td>{$SQL_DB_MSG[i]}</td>
					<td>{$SQL_USER_MSG[i]}</td>
				</tr>
				{/section}
			</tbody>
		</table>
		<div class="paginator">
			{if !isset($SCROLL_NEXT_GRAY)}
			<span class="icon i_next_gray">&nbsp;</span>
			{/if}
			{if !isset($SCROLL_NEXT)}
			<a href="reseller_statistics.php?psi={$NEXT_PSI}&amp;month={$MONTH}&amp;year={$YEAR}" title="next" class="icon i_next">next</a>
			{/if}
			{if !isset($SCROLL_PREV_GRAY)}
			<span class="icon i_prev_gray">&nbsp;</span>
			{/if}
			{if !isset($SCROLL_PREV)}
			<a href="reseller_statistics.php?psi={$PREV_PSI}&amp;month={$MONTH}&amp;year={$YEAR}" title="previous" class="icon i_prev">previous</a>
			{/if}
		</div>
		{/if}
	</div>
{include file='admin/footer.tpl'}