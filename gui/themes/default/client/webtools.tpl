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
		<h2 class="tools"><span>{$TR_MENU_WEBTOOLS}</span></h2>
		<table>
			<tr>
				<td style="width: 50px;"><span class="icon_big i_htaccessicon">&nbsp;</span></td>
				<td><a href="protected_areas.php">{$TR_HTACCESS}</a><br />{$TR_HTACCESS_TEXT}</td>
			</tr>
		</table>
		<br />
		<table>
			<tr>
				<td style="width: 50px;"><span class="icon_big i_usersicon">&nbsp;</span></td>
				<td><a href="protected_user_manage.php">{$TR_HTACCESS_USER}</a><br />{$TR_HTACCESS_USER}</td>
			</tr>
		</table>
		<br />
		<table>
			<tr>
				<td style="width: 50px;"><span class="icon_big i_errordocsicon">&nbsp;</span></td>
				<td><a href="error_pages.php">{$TR_ERROR_PAGES}</a><br />{$TR_ERROR_PAGES_TEXT}</td>
			</tr>
		</table>
		<br />
		<table>
			<tr>
				<td style="width: 50px;"><span class="icon_big i_backupicon">&nbsp;</span></td>
				<td><a href="backup.php">{$TR_BACKUP}</a><br />{$TR_BACKUP_TEXT}</td>
			</tr>
		</table>
		{if isset($WEBMAIL_PATH)}
		<br />
		<table>
			<tr>
				<td style="width: 50px;"><span class="icon_big i_webmailicon">&nbsp;</span></td>
				<td><a href="{$WEBMAIL_PATH}" class="external">{$TR_WEBMAIL}</a><br />{$TR_WEBMAIL_TEXT}</td>
			</tr>
		</table>
		{/if}
		<br />
		<table>
			<tr>
				<td style="width: 50px;"><span class="icon_big i_filemanagericon">&nbsp;</span></td>
				<td><a href="{$FILEMANAGER_PATH}" class="external">{$TR_FILEMANAGER}</a><br />{$TR_FILEMANAGER_TEXT}</td>
			</tr>
		</table>
		{if isset($AWSTATS_PATH)}
		<br />
		<table>
			<tr>
				<td style="width: 50px;"><span class="icon_big i_awstatsicon">&nbsp;</span></td>
				<td><a href="{$AWSTATS_PATH}" class="external">{$TR_AWSTATS}</a><br />{$TR_AWSTATS_TEXT}</td>
			</tr>
		</table>
		{/if}
	</div>
{include file='client/footer.tpl'}