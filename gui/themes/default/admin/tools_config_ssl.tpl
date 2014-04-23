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
			<li><a>{$TR_SSL_TITLE}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="ssl"><span>{$TR_SSL_TITLE}</span></h2>
		<form action="tools_config_ssl.php" method="post" id="admin_settings_ssl">
			<fieldset>
				<table>
					<tr>
						<td>{$TR_SSL_ENABLED}</td>
						<td>
							<select name="ssl_status" id="ssl_status">
								<option value="0" {$SSL_SELECTED_DISABLED}>{$TR_SSL_STATUS_DISABLED}</option>
								<option value="1" {$SSL_SELECTED_SSLONLY}>{$TR_SSL_STATUS_SSLONLY}</option>
								<option value="2" {$SSL_SELECTED_BOTH}>{$TR_SSL_STATUS_BOTH}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>{$TR_SSL_CERTIFICATE}</td>
						<td><textarea name="ssl_cert" id="ssl_cert" cols="80" rows="15" >{$SSL_CERTIFICATE}</textarea></td>
					</tr>
					<tr>
						<td>{$TR_SSL_KEY}</td>
						<td><textarea name="ssl_key" id="ssl_key" cols="80" rows="15" >{$SSL_KEY}</textarea></td>
					</tr>
					<tr>
						<td>{$TR_SSL_CACERT}</td>
						<td><textarea name="ssl_cacert" id="ssl_cacert" cols="80" rows="15" >{$SSL_CACERT}</textarea></td>
					</tr>
				</table>
				<div class="buttons">
					<input type="hidden" name="uaction" value="apply" />
					<input type="submit" name="Submit" value="{$TR_APPLY_CHANGES}" />
				</div>
			</fieldset>
		</form>
	</div>
{include file='admin/footer.tpl'}