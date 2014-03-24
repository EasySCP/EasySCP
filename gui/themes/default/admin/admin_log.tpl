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
			<li><a href="index.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a>{$TR_ADMIN_LOG}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="adminlog"><span>{$TR_ADMIN_LOG}</span></h2>
		<table>
			<tr>
				<th>{$TR_DATE}</th>
				<th>{$TR_MESSAGE}</th>
			</tr>
			{section name=i loop=$ADM_MESSAGE}
			<tr>
				<td>{$DATE[i]}</td>
				<td>{$ADM_MESSAGE[i]}</td>
			</tr>
			{/section}
		</table>
		<div class="paginator">
			{if !isset($SCROLL_NEXT_GRAY)}
			<span class="icon i_next_gray">&nbsp;</span>
			{/if}
			{if !isset($SCROLL_NEXT)}
			<a href="admin_log.php?psi={$NEXT_PSI}" title="next" class="icon i_next">next</a>
			{/if}
			{if !isset($SCROLL_PREV_GRAY)}
			<span class="icon i_prev_gray">&nbsp;</span>
			{/if}
			{if !isset($SCROLL_PREV)}
			<a href="admin_log.php?psi={$PREV_PSI}" title="previous" class="icon i_prev">previous</a>
			{/if}
		</div>
		<form action="admin_log.php" method="post" id="admin_log" style="margin-top: 40px;">
			<table>
				<tr>
					<td><label for="uaction_clear">{$TR_CLEAR_LOG_MESSAGE}</label></td>
					<td>
						<select name="uaction_clear" id="uaction_clear">
							<option value="0" selected="selected">{$TR_CLEAR_LOG_EVERYTHING}</option>
							<option value="2">{$TR_CLEAR_LOG_LAST2}</option>
							<option value="4">{$TR_CLEAR_LOG_LAST4}</option>
							<option value="12">{$TR_CLEAR_LOG_LAST12}</option>
							<option value="26">{$TR_CLEAR_LOG_LAST26}</option>
							<option value="52">{$TR_CLEAR_LOG_LAST52}</option>
						</select>
					</td>
				</tr>
			</table>
			<div class="buttons">
				<input type="hidden" name="uaction" value="clear_log" />
				<input type="submit" name="Submit" value="{$TR_CLEAR_LOG}" />
			</div>
		</form>
	</div>
{include file='admin/footer.tpl'}