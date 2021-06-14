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
	{if isset($ISACTIVE_PHP_VERSION)}
		<li><a href="domain_php_version.php">{$TR_MENU_PHP_VERSION}</a></li>
	{/if}
	{if isset($ISACTIVE_PHP_EDITOR)}
		<li><a href="domain_php.php">{$TR_MENU_PHP_EDITOR}</a></li>
	{/if}
</ul>
