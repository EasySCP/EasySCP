<ul id="menuLeft" class="menuLeft">
	<li>
		<a href="index.php" title="{$TR_MENU_GENERAL_INFORMATION}" class="general icon_link">{$TR_MENU_GENERAL_INFORMATION}</a>
		<ul>
			<li><a href="index.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="password_change.php">{$TR_MENU_CHANGE_PASSWORD}</a></li>
			<li><a href="personal_change.php">{$TR_MENU_CHANGE_PERSONAL_DATA}</a></li>
			<li><a href="language.php">{$TR_MENU_LANGUAGE}</a></li>
			<li><a href="server_status.php">{$TR_MENU_SERVER_STATUS}</a></li>
			<li><a href="admin_log.php">{$TR_MENU_ADMIN_LOG}</a></li>
		</ul>
	</li>
	<li class="menuLeft_expand">
		<a href="manage_users.php" title="{$TR_MENU_MANAGE_USERS}" class="manage_users_active icon_link">{$TR_MENU_MANAGE_USERS}</a>
		<ul>
			<li><a href="manage_users.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="admin_add.php">{$TR_MENU_ADD_ADMIN}</a></li>
			<li><a href="reseller_add.php">{$TR_MENU_ADD_RESELLER}</a></li>
			<li><a href="manage_reseller_owners.php">{$TR_MENU_RESELLER_ASIGNMENT}</a></li>
			<li><a href="manage_reseller_users.php">{$TR_MENU_USER_ASIGNMENT}</a></li>
			<li><a href="circular.php">{$TR_MENU_CIRCULAR}</a></li>
			<li><a href="sessions_manage.php">{$TR_MENU_MANAGE_SESSIONS}</a></li>
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
	<li>
		<a href="system_info.php" title="{$TR_MENU_SYSTEM_TOOLS}" class="webtools icon_link">{$TR_MENU_SYSTEM_TOOLS}</a>
		<ul>
			<li><a href="system_info.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="settings_maintenance_mode.php">{$TR_MAINTENANCEMODE}</a></li>
			<li><a href="tools_config_ssl.php">{$TR_MANAGE_SSL}</a></li>
			<li><a href="easyscp_updates.php">{$TR_MENU_EasySCP_UPDATE}</a></li>
			<li><a href="easyscp_debugger.php">{$TR_MENU_EasySCP_DEBUGGER}</a></li>
			<li><a href="rootkit_log.php">{$TR_MENU_ROOTKIT_LOG}</a></li>
			<li><a href="cronjob_overview.php">{$TR_MENU_CRONJOB_OVERVIEW}</a></li>
			<li><a href="cronjob_manage.php">{$TR_MENU_CRONJOB_ADD}</a></li>
		</ul>
	</li>
	<li>
		<a href="server_statistic.php" title="{$TR_MENU_STATISTICS}" class="statistics icon_link">{$TR_MENU_STATISTICS}</a>
		<ul>
			<li><a href="server_statistic.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a href="reseller_statistics.php">{$TR_MENU_RESELLER_STATISTICS}</a></li>
			<li><a href="ip_usage.php">{$TR_MENU_IP_USAGE}</a></li>
		</ul>
	</li>
	{if isset($SUPPORT_SYSTEM)}
	<li>
		<a href="ticket_system.php" title="{$TR_MENU_SUPPORT_SYSTEM}" class="support icon_link">{$TR_MENU_SUPPORT_SYSTEM}</a>
		<ul>
			<li><a href="ticket_system.php">{$TR_OPEN_TICKETS}</a></li>
			<li><a href="ticket_closed.php">{$TR_CLOSED_TICKETS}</a></li>
		</ul>
	</li>
	{/if}
	<li>
		<a href="settings.php" title="{$TR_MENU_SETTINGS}" class="settings icon_link">{$TR_MENU_SETTINGS}</a>
		<ul>
			<li><a href="settings.php">{$TR_GENERAL_SETTINGS}</a></li>
			<li><a href="ip_manage.php">{$TR_MENU_MANAGE_IPS}</a></li>
			<li><a href="settings_server_traffic.php">{$TR_MENU_SERVER_TRAFFIC_SETTINGS}</a></li>
			<li><a href="settings_welcome_mail.php">{$TR_MENU_EMAIL_SETUP}</a></li>
			<li><a href="settings_lostpassword.php">{$TR_MENU_LOSTPW_EMAIL}</a></li>
			<li><a href="settings_ports.php">{$TR_SERVERPORTS}</a></li>
		</ul>
	</li>
</ul>
