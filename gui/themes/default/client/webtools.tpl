{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_MENU_OVERVIEW}{/block}

{block name=BREADCRUMP}
<li><a href="/client/webtools.php">{$TR_MENU_WEBTOOLS}</a></li>
<li><a>{$TR_MENU_OVERVIEW}</a></li>
{/block}

{block name=BODY}
<h2 class="tools"><span>{$TR_MENU_WEBTOOLS}</span></h2>
<table>
	<tr>
		<td style="width: 50px;"><span class="icon_big i_htaccessicon">&nbsp;</span></td>
		<td><a href="/client/protected_areas.php">{$TR_HTACCESS}</a><br />{$TR_HTACCESS_TEXT}</td>
	</tr>
</table>
<br />
<table>
	<tr>
		<td style="width: 50px;"><span class="icon_big i_usersicon">&nbsp;</span></td>
		<td><a href="/client/protected_user_manage.php">{$TR_HTACCESS_USER}</a><br />{$TR_HTACCESS_USER}</td>
	</tr>
</table>
<br />
<table>
	<tr>
		<td style="width: 50px;"><span class="icon_big i_errordocsicon">&nbsp;</span></td>
		<td><a href="/client/error_pages.php">{$TR_ERROR_PAGES}</a><br />{$TR_ERROR_PAGES_TEXT}</td>
	</tr>
</table>
{if isset($ISACTIVE_BACKUP)}
<br />
<table>
	<tr>
		<td style="width: 50px;"><span class="icon_big i_backupicon">&nbsp;</span></td>
		<td><a href="/client/backup.php">{$TR_BACKUP}</a><br />{$TR_BACKUP_TEXT}</td>
	</tr>
</table>
{/if}
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
{/block}