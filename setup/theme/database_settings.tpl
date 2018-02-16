{include file='header.tpl'}
<body>
	<div class="header">
		<a class="link" href="http://www.easyscp.net" title="Easy Server Control Panel">
			<img alt="EasySCP logo" src="{$THEME_COLOR_PATH}/images/easyscp_logo.png" />
		</a>
	</div>
	<div class="location">&nbsp;</div>
	<div class="main">
		{if isset($MESSAGE)}
			<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<div class="content">
			<h2 class="general"><span>Welcome to EasySCP WebGUI Setup</span></h2>
			This program will set up the EasySCP system on your server.<br />
			<br />
			First we need the SQL settings.<br />
			<br />
			<form action="" method="post" id="setup">
				<fieldset>
					<legend>Database settings</legend>
					<table>
						<tr>
							<td><label for="DB_HOST">Please enter SQL server hostname</label></td>
							<td><input type="text" name="DB_HOST" id="DB_HOST" value="{$DB_HOST}"/></td>
						</tr>
						<tr>
							<td><label for="DB_DATABASE">Please enter EasySCP SQL database name</label></td>
							<td><input type="text" name="DB_DATABASE" id="DB_DATABASE" value="{$DB_DATABASE}"/></td>
						</tr>
						<tr>
							<td><label for="DB_USER">Please enter EasySCP SQL user name</label></td>
							<td><input type="text" name="DB_USER" id="DB_USER" value="{$DB_USER}"/></td>
						</tr>
						<tr>
							<td><label for="DB_PASSWORD">Please enter EasySCP SQL password</label></td>
							<td><input type="password" name="DB_PASSWORD" id="DB_PASSWORD" value="{$DB_PASSWORD}"/></td>
						</tr>
						<tr>
							<td><label for="DB_PASSWORD2">Please repeat EasySCP SQL password:</label></td>
							<td><input type="password" name="DB_PASSWORD2" id="DB_PASSWORD2" value="{$DB_PASSWORD2}"/></td>
						</tr>
					</table>
				</fieldset>
				<div class="buttons">
					<input type="hidden" name="uaction" value="database_settings" />
					<input type="submit" name="Submit" value="Next" />
				</div>
			</form>
			<div class="login"><p>Powered by <a class="link" href="http://www.easyscp.net" title="Easy Server Control Panel">Easy Server Control Panel</a></p></div>
		</div>
	</div>
{include file='footer.tpl'}