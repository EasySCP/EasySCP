{include file='admin/header.tpl'}
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
			<li><a href="../index.php?logout" class="logout">{$TR_MENU_LOGOUT}</a></li>
		</ul>
		<ul class="path">
			<li><a href="system_info.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a>{$TR_MAINTENANCEMODE}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="maintenancemode"><span>{$TR_MAINTENANCEMODE}</span></h2>
		<div class="{$TR_MESSAGE_TYPE}">{$TR_MESSAGE_TEMPLATE_INFO}</div>
		<form action="settings_maintenance_mode.php" method="post" id="maintenancemode_frm">
			<table>
				<tr>
					<td><label for="maintenancemode_message">{$TR_MESSAGE}</label></td>
					<td><textarea name="maintenancemode_message" id="maintenancemode_message" cols="80" rows="15">{$MESSAGE_VALUE}</textarea></td>
				</tr>
				<tr>
					<td><label for="maintenancemode">{$TR_MAINTENANCEMODE}</label></td>
					<td>
						<select name="maintenancemode" id="maintenancemode">
							<option value="0" {$SELECTED_OFF}>{$TR_DISABLED}</option>
							<option value="1" {$SELECTED_ON}>{$TR_ENABLED}</option>
						</select>
					</td>
				</tr>
			</table>
			<div class="buttons">
				<input type="hidden" name="uaction" value="apply" />
				<input type="submit" name="Submit" value="{$TR_APPLY_CHANGES}" />
			</div>
		</form>
	</div>
{include file='admin/footer.tpl'}