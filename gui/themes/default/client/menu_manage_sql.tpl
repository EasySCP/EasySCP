<ul id="menuLeft" class="menuLeft">
	<li>
		<a href="index.php" title="{$TR_MENU_GENERAL_INFORMATION}" class="general icon_link">{$TR_MENU_GENERAL_INFORMATION}</a>
		<ul>
			<li><a href="index.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="password_change.php">{$TR_MENU_CHANGE_PASSWORD}</a></li>
			<li><a href="personal_change.php">{$TR_MENU_CHANGE_PERSONAL_DATA}</a></li>
			<li><a href="language.php">{$TR_MENU_LANGUAGE}</a></li>
			{if isset($ISACTIVE_UPDATE_HP)}
			<li><a href="hosting_plan_update.php">{$TR_MENU_UPDATE_HP}</a></li>
			{/if}
		</ul>
	</li>
{if isset($ISACTIVE_DOMAIN)}
	<li>
		<a href="domains_manage.php" title="{$TR_MENU_MANAGE_DOMAINS}" class="domains icon_link">{$TR_MENU_MANAGE_DOMAINS}</a>
		<ul>
			<li><a href="domains_manage.php">{$TR_MENU_OVERVIEW}</a></li>
			{if isset($ISACTIVE_SUBDOMAIN_MENU)}
			<li><a href="subdomain_add.php">{$TR_MENU_ADD_SUBDOMAIN}</a></li>
			{/if}
			{if isset($ISACTIVE_ALIAS_MENU)}
			<li><a href="alias_add.php">{$TR_MENU_ADD_ALIAS}</a></li>
			{/if}
			{if isset($ISACTIVE_DNS_MENU)}
			<li><a href="dns_overview.php">{$TR_MENU_MANAGE_DNS}</a></li>
			{/if}
			{if isset($ISACTIVE_SSL_MENU)}
			<li><a href="domain_manage_ssl.php">{$TR_MENU_MANAGE_SSL}</a></li>
			{/if}
			{if isset($ISACTIVE_PHP_EDITOR)}
				<li><a href="domain_php.php">{$TR_MENU_PHP_EDITOR}</a></li>
			{/if}
		</ul>
	</li>
{/if}
{if isset($ISACTIVE_EMAIL)}
	<li>
		<a href="mail_accounts.php" title="{$TR_MENU_EMAIL_ACCOUNTS}" class="email icon_link">{$TR_MENU_EMAIL_ACCOUNTS}</a>
		<ul>
			<li><a href="mail_accounts.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="mail_add.php">{$TR_MENU_ADD_MAIL_USER}</a></li>
			<li><a href="mail_catchall.php">{$TR_MENU_CATCH_ALL_MAIL}</a></li>
			<li><a href="{$WEBMAIL_PATH}" class="external">{$TR_WEBMAIL}</a></li>
		</ul>
	</li>
{/if}
{if isset($ISACTIVE_FTP)}
	<li>
		<a href="ftp_accounts.php" title="{$TR_MENU_FTP_ACCOUNTS}" class="ftp icon_link">{$TR_MENU_FTP_ACCOUNTS}</a>
		<ul>
			<li><a href="ftp_accounts.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="ftp_add.php">{$TR_MENU_ADD_FTP_USER}</a></li>
			<li><a href="{$FILEMANAGER_PATH}" class="external">{$TR_FILEMANAGER}</a></li>
		</ul>
	</li>
{/if}
{if isset($ISACTIVE_SQL)}
	<li class="menuLeft_expand">
		<a href="sql_manage.php" title="{$TR_MENU_MANAGE_SQL}" class="database_active icon_link">{$TR_MENU_MANAGE_SQL}</a>
		<ul>
			<li><a href="sql_manage.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="sql_database_add.php">{$TR_MENU_ADD_SQL_DATABASE}</a></li>
			<li><a href="{$PMA_PATH}" class="external">{$TR_PHPMYADMIN}</a></li>
		</ul>
	</li>
{/if}
	<li>
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
		</ul>
	</li>
	<li>
		<a href="domain_statistics.php" title="{$TR_MENU_DOMAIN_STATISTICS}" class="statistics icon_link">{$TR_MENU_DOMAIN_STATISTICS}</a>
		<ul>
			<li><a href="domain_statistics.php">{$TR_MENU_OVERVIEW}</a></li>
			{if isset($AWSTATS_PATH)}
			<li><a href="{$AWSTATS_PATH}" class="external">{$TR_AWSTATS}</a></li>
			{/if}
		</ul>
	</li>
{if isset($SUPPORT_SYSTEM)}
	<li>
		<a href="ticket_system.php" title="{$TR_MENU_SUPPORT_SYSTEM}" class="support icon_link">{$TR_MENU_SUPPORT_SYSTEM}</a>
		<ul>
			<li><a href="ticket_system.php">{$TR_OPEN_TICKETS}</a></li>
			<li><a href="ticket_closed.php">{$TR_CLOSED_TICKETS}</a></li>
			<li><a href="ticket_create.php">{$TR_MENU_NEW_TICKET}</a></li>
		</ul>
	</li>
{/if}
</ul>
