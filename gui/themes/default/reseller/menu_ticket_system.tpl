<ul id="menuLeft" class="menuLeft">
	<li>
		{include file='reseller/part_general_information.tpl'}
	</li>
	<li>
		{include file='reseller/part_user_manage.tpl'}
	</li>
	{if isset($HOSTING_PLANS)}
	<li>
		{include file='reseller/part_hosting_plan.tpl'}
	</li>
	{/if}
	<!--
	<li>
		<a href="orders.php" title="{$TR_MENU_ORDERS}" class="purchasing icon_link">{$TR_MENU_ORDERS}</a>
		<ul>
			<li><a href="orders.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="order_settings.php">{$TR_MENU_ORDER_SETTINGS}</a></li>
			<li><a href="order_email.php">{$TR_MENU_ORDER_EMAIL}</a></li>
		</ul>
	</li>
	-->
	<li>
		{include file='reseller/part_statistics.tpl'}
	</li>
	{if isset($SUPPORT_SYSTEM)}
	<li class="menuLeft_expand">
		{include file='reseller/part_ticket_system.tpl'}
	</li>
	{/if}
</ul>