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
			<li><a href="system_info.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a>{$TR_DEBUGGER_TITLE}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="debugger"><span>{$TR_DEBUGGER_TITLE}</span></h2>
		<table>
			<thead>
				<tr>
					<th>{$TR_DOMAIN}</th>
				</tr>
			</thead>
			<tbody>
			{if isset($TR_DOMAIN_MESSAGE)}
				<tr>
					<td>{$TR_DOMAIN_MESSAGE}</td>
				</tr>

			{/if}
			{if isset($TR_DOMAIN_NAME)}
				{section name=i loop=$TR_DOMAIN_NAME}
				<tr>
					<td>
						{$TR_DOMAIN_NAME[i]} - <span style="color:red;">{$TR_DOMAIN_ERROR[i]}</span>
					</td>
				</tr>
				{/section}
			{/if}
			</tbody>
		</table>
		<br />
		<table>
			<thead>
				<tr>
					<th>{$TR_ALIAS}</th>
				</tr>
			</thead>
			<tbody>
			{if isset($TR_ALIAS_MESSAGE)}
				<tr>
					<td>{$TR_ALIAS_MESSAGE}</td>
				</tr>
			{/if}
			{if isset($TR_ALIAS_NAME)}
				{section name=i loop=$TR_ALIAS_NAME}
				<tr>
					<td>
						{$TR_ALIAS_NAME[i]} - <span style="color:red;">{$TR_ALIAS_ERROR[i]}</span>
					</td>
				</tr>
				{/section}
			{/if}
			</tbody>
		</table>
		<br />
		<table>
			<thead>
				<tr>
					<th>{$TR_SUBDOMAIN}</th>
				</tr>
			</thead>
			<tbody>
			{if isset($TR_SUBDOMAIN_MESSAGE)}
				<tr>
					<td>{$TR_SUBDOMAIN_MESSAGE}</td>
				</tr>
			{/if}
			{if isset($TR_SUBDOMAIN_NAME)}
				{section name=i loop=$TR_SUBDOMAIN_NAME}
				<tr>
					<td>
						{$TR_SUBDOMAIN_NAME[i]} - <span style="color:red;">{$TR_SUBDOMAIN_ERROR[i]}</span>
					</td>
				</tr>
				{/section}
			{/if}
			</tbody>
		</table>
		<br />
		<table>
			<thead>
				<tr>
					<th>{$TR_SUBDOMAIN_ALIAS}</th>
				</tr>
			</thead>
			<tbody>
			{if isset($TR_SUBDOMAIN_ALIAS_MESSAGE)}
				<tr>
					<td>{$TR_SUBDOMAIN_ALIAS_MESSAGE}</td>
				</tr>
			{/if}
			{if isset($TR_SUBDOMAIN_ALIAS_NAME)}
				{section name=i loop=$TR_SUBDOMAIN_ALIAS_NAME}
				<tr>
					<td>
						{$TR_SUBDOMAIN_ALIAS_NAME[i]} - <span style="color:red;">{$TR_SUBDOMAIN_ALIAS_ERROR[i]}</span>
					</td>
				</tr>
				{/section}
			{/if}
			</tbody>
		</table>
		<br />
		<table>
			<thead>
				<tr>
					<th>{$TR_MAIL}</th>
				</tr>
			</thead>
			<tbody>
			{if isset($TR_MAIL_MESSAGE)}
				<tr>
					<td>{$TR_MAIL_MESSAGE}</td>
				</tr>
			{/if}
			{if isset($TR_MAIL_NAME)}
				{section name=i loop=$TR_MAIL_NAME}
				<tr>
					<td>
						{$TR_MAIL_NAME[i]} - <span style="color:red;">{$TR_MAIL_ERROR[i]}</span>
					</td>
				</tr>
				{/section}
			{/if}
			</tbody>
		</table>
		<br />
		<table>
			<thead>
				<tr>
					<th>{$TR_HTACCESS}</th>
				</tr>
			</thead>
			<tbody>
			{if isset($TR_HTACCESS_MESSAGE)}
				<tr>
					<td>{$TR_HTACCESS_MESSAGE}</td>
				</tr>
			{/if}
			{if isset($TR_HTACCESS_NAME)}
				{section name=i loop=$TR_HTACCESS_NAME}
				<tr>
					<td>
						{$TR_HTACCESS_TYPE[i]} - {$TR_HTACCESS_NAME[i]} - <span style="color:red;">{$TR_HTACCESS_ERROR[i]}</span>
					</td>
				</tr>
				{/section}
			{/if}
			</tbody>
		</table>
		<br />
		<table>
			<tbody>
				<tr>
					<th>{$TR_DAEMON_TOOLS}</th>
				</tr>
			</tbody>
			<tbody>
				<tr>
					<td>
						{if $EXEC_COUNT eq "0"}
							{$EXEC_COUNT} {$TR_EXEC_REQUESTS}
						{else}
							<a href="easyscp_debugger.php?action=run_engine" class="link">{$EXEC_COUNT} {$TR_EXEC_REQUESTS}</a>
						{/if}
					</td>
				</tr>
				<tr>
					<td>{$TR_ERRORS}</td>
				</tr>
			</tbody>
		</table>
	</div>
{include file='admin/footer.tpl'}