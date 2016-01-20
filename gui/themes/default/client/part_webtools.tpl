<a href="webtools.php" title="{$TR_MENU_WEBTOOLS}" class="webtools icon_link">{$TR_MENU_WEBTOOLS}</a>
<ul>
	<li><a href="webtools.php">{$TR_MENU_OVERVIEW}</a></li>
	<li><a href="protected_areas.php">{$TR_HTACCESS}</a></li>
	<li><a href="protected_user_manage.php">{$TR_HTACCESS_USER}</a></li>
	<li><a href="error_pages.php">{$TR_MENU_ERROR_PAGES}</a></li>
	{if isset($ISACTIVE_BACKUP)}
	<li><a href="backup.php">{$TR_MENU_DAILY_BACKUP}</a></li>
	{/if}
	{if isset($ISACTIVE_EMAIL)}
	<li><a href="{$WEBMAIL_PATH}" class="external">{$TR_WEBMAIL}</a></li>
	{/if}
	<li><a href="{$FILEMANAGER_PATH}" class="external">{$TR_FILEMANAGER}</a></li>
	{if isset($AWSTATS_PATH)}
	<li><a href="{$AWSTATS_PATH}" class="external">{$TR_AWSTATS}</a></li>
	{/if}
	<li><a href="cronjob_overview.php">{$TR_MENU_CRONJOB_OVERVIEW}</a></li>
	<li><a href="cronjob_manage.php">{$TR_MENU_CRONJOB_ADD}</a></li>
</ul>
