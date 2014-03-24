<ul id="menuLeft" class="menuLeft">
	<li>
		<a href="index.php" title="{$TR_MENU_GENERAL_INFORMATION}" class="general icon_link">{$TR_MENU_GENERAL_INFORMATION}</a>
		<ul>
			<li><a href="index.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="password_change.php">{$TR_MENU_CHANGE_PASSWORD}</a></li>
			<li><a href="personal_change.php">{$TR_MENU_CHANGE_PERSONAL_DATA}</a></li>
			<li><a href="language.php">{$TR_MENU_LANGUAGE}</a></li>
		</ul>
	</li>
	<li>
		<a href="manage_users.php" title="{$TR_MENU_MANAGE_USERS}" class="manage_users icon_link">{$TR_MENU_MANAGE_USERS}</a>
		<ul>
			<li><a href="users.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="user_add1.php">{$TR_MENU_ADD_USER}</a></li>
			<li><a href="alias.php">{$TR_MENU_DOMAIN_ALIAS}</a></li>
			<li><a href="settings_welcome_mail.php">{$TR_MENU_E_MAIL_SETUP}</a></li>
			<li><a href="settings_lostpassword.php">{$TR_MENU_LOSTPW_EMAIL}</a></li>
			<li><a href="circular.php">{$TR_MENU_CIRCULAR}</a></li>
		</ul>
	</li>
	{if isset($HOSTING_PLANS)}
	<li>
		<a href="hosting_plan.php" title="{$TR_MENU_HOSTING_PLANS}" class="hosting_plans icon_link">{$TR_MENU_HOSTING_PLANS}</a>
		<ul>
			<li><a href="hosting_plan.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="hosting_plan_add.php">{$TR_MENU_ADD_HOSTING}</a></li>
		</ul>
	</li>
	{/if}
	<li class="menuLeft_expand">
		<a href="orders.php" title="{$TR_MENU_ORDERS}" class="purchasing_active icon_link">{$TR_MENU_ORDERS}</a>
		<ul>
			<li><a href="orders.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="order_settings.php">{$TR_MENU_ORDER_SETTINGS}</a></li>
			<li><a href="order_email.php">{$TR_MENU_ORDER_EMAIL}</a></li>
		</ul>
	</li>
	<li>
		<a href="user_statistics.php" title="{$TR_MENU_DOMAIN_STATISTICS}" class="statistics icon_link">{$TR_MENU_DOMAIN_STATISTICS}</a>
		<ul>
			<li><a href="user_statistics.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="ip_usage.php">{$TR_MENU_IP_USAGE}</a></li>
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