{include file='client/header.tpl'}
<body>
	<script type="text/javascript">
	/* <![CDATA[ */
		function action_delete(url, subject) {
			if (!confirm(sprintf("{$TR_MESSAGE_DELETE}", subject)))
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
			<li><a href="domains_manage.php">{$TR_MENU_MANAGE_DOMAINS}</a></li>
			<li><a href="domains_manage.php">{$TR_MENU_OVERVIEW}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		<h2 class="domains">{$TR_DNS}</h2>
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		{if isset($ALS_MSG)}
		<div class="{$ALS_MSG_TYPE}">{$ALS_MSG}</div>
		{/if}
		<form method="post">
			<input type="hidden" name="select_domain" value="true" />
			<table>
				<tr>
					<td><b>Domain:</b></td>
					<td>
						<select name="domain_id">
							{foreach item=d from=$D_USER_DOMAINS}
								<option value="{$d.alias}-{$d.domain_id}"{if $D_USER_DOMAIN_SELECTED==$d.alias-$d.domain_id} selected="selected"{/if}>{$d.domain_name}</option>
							{/foreach}
						</select>
					</td>
					<td>
						<input type="submit" value="{$TR_SELECT}" />
					</td>
				</tr>
			</table>
		</form>
		<p><a href="dns_add.php">{$TR_DNS_ADD}</a></p>
		<table class="tablesorter">
			<thead>
				<tr>
					<th>{$TR_DOMAIN_NAME}</th>
					<th>{$TR_DNS_NAME}</th>
					<th>{$TR_DNS_TYPE}</th>
					<th>{$TR_DNS_DATA}</th>
					<th style="width:200px">{$TR_DNS_ACTION}</th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$D_DNS_ZONE item=r}
				<tr>
					<td><span class="icon i_domain_icon">{$r.DNS_DOMAIN}</span></td>
					<td>{$r.DNS_NAME}</td>
					<td>{$r.DNS_TYPE}</td>
					<td>{$r.DNS_DATA}</td>
					<td>
						<a href="{$r.DNS_ACTION_SCRIPT_EDIT}" title="{$r.DNS_ACTION_EDIT}" class="icon i_edit"></a>
						<a href="#" onclick="action_delete('{$r.DNS_ACTION_SCRIPT_DELETE}', '{$r.DNS_TYPE_RECORD}')" title="{$r.DNS_ACTION_DELETE}" class="icon i_delete"></a>
					</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{include file='client/footer.tpl'}