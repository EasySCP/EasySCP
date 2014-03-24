{include file='reseller/header.tpl'}
<body>
	<script type="text/javascript">
	/* <![CDATA[ */
		function delete_account(url, name) {
				if (!confirm(sprintf("{$TR_MESSAGE_DELETE}", name)))
					return false;
				location = url;
			}
	/* ]]> */
	</script>
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
			<li><a href="users.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a>{$TR_MENU_DOMAIN_ALIAS}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="users"><span>{$TR_MANAGE_ALIAS}</span></h2>
		{if isset($M_DOMAIN_NAME_SELECTED)}
		<form action="alias.php?psi={$PSI}" method="post" id="reseller_alias">
			<fieldset>
				<input type="text" name="search_for" id="search_for" value="{$SEARCH_FOR}" />
				<select name="search_common">
					<option value="alias_name" {$M_DOMAIN_NAME_SELECTED}>{$M_ALIAS_NAME}</option>
					<option value="account_name" {$M_ACCOUN_NAME_SELECTED}>{$M_ACCOUNT_NAME}</option>
				</select>
				<input type="hidden" name="uaction" value="go_search" />
				<input type="submit" name="Submit" value="{$TR_SEARCH}" />
			</fieldset>
		</form>
		{/if}
		{if isset($NAME)}
		<table>
			<thead>
				<tr>
					<th>{$TR_NAME}</th>
					<th>{$TR_REAL_DOMAIN}</th>
					<th>{$TR_FORWARD}</th>
					<th>{$TR_STATUS}</th>
					<th>{$TR_ACTION}</th>
				</tr>
			</thead>
			<tbody>
				{section name=i loop=$NAME}
				<tr>
					<td><a href="http://www.{$NAME[i]}/" class="icon i_domain">{$NAME[i]}</a><br />{$ALIAS_IP[i]}</td>
					<td>{$REAL_DOMAIN[i]}<br />{$REAL_DOMAIN_MOUNT[i]}</td>
					<td>{$FORWARD[i]}</td>
					<td>{$STATUS[i]}</td>
					<td>
						<a href="{$EDIT_LINK[i]}" title="{$EDIT[i]}" class="icon i_edit"></a>
						<a href="#" onclick="delete_account('{$DELETE_LINK[i]}', '{$NAME[i]}')" title="{$DELETE[i]}" class="icon i_delete"></a>
					</td>
				</tr>
				{/section}
			</tbody>
		</table>
		{/if}
		<form action="alias_add.php" method="post" id="admin_alias_add">
			<fieldset>
				<input type="submit" name="Submit"  value="{$TR_ADD_ALIAS}" />
			</fieldset>
		</form>
		<div class="paginator">
			{if !isset($SCROLL_NEXT_GRAY)}
			<span class="icon i_next_gray">&nbsp;</span>
			{/if}
			{if !isset($SCROLL_NEXT)}
			<a href="alias.php?psi={$NEXT_PSI}" title="next" class="icon i_next">next</a>
			{/if}
			{if !isset($SCROLL_PREV_GRAY)}
			<span class="icon i_prev_gray">&nbsp;</span>
			{/if}
			{if !isset($SCROLL_PREV)}
			<a href="alias.php?psi={$PREV_PSI}" title="previous" class="icon i_prev">previous</a>
			{/if}
		</div>
	</div>
{include file='reseller/footer.tpl'}