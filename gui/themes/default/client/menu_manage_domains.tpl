<ul id="menuLeft" class="menuLeft">
	<li>
		{include file='client/part_general_information.tpl'}
	</li>
{if isset($ISACTIVE_DOMAIN)}
	<li class="menuLeft_expand">
		{include file='client/part_manage_domains.tpl'}
	</li>
{/if}
{if isset($ISACTIVE_EMAIL)}
	<li>
		{include file='client/part_email_accounts.tpl'}
	</li>
{/if}
{if isset($ISACTIVE_FTP)}
	<li>
		{include file='client/part_ftp_accounts.tpl'}
	</li>
{/if}
{if isset($ISACTIVE_SQL)}
	<li>
		{include file='client/part_manage_sql.tpl'}
	</li>
{/if}
	<li>
		{include file='client/part_webtools.tpl'}
	</li>
	<li>
		{include file='client/part_statistics.tpl'}
	</li>
{if isset($SUPPORT_SYSTEM)}
	<li>
		{include file='client/part_ticket_system.tpl'}
	</li>
{/if}
</ul>
