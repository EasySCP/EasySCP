<ul id="menuLeft" class="menuLeft">
	<li>
		{include file='admin/part_general_information.tpl'}
	</li>
	<li>
		{include file='admin/part_users_manage.tpl'}
	</li>
	<li>
		{include file='admin/part_system_tools.tpl'}
	</li>
	<li>
		{include file='admin/part_statistics.tpl'}
	</li>
	{if isset($SUPPORT_SYSTEM)}
	<li class="menuLeft_expand">
		{include file='admin/part_ticket_system.tpl'}
	</li>
	{/if}
	<li>
		{include file='admin/part_settings.tpl'}
	</li>
</ul>
