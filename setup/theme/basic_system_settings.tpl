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
			Next you are asked to enter a "fully qualified hostname" (FQHN).<br />
			For more infos read <a href="http://en.wikipedia.org/wiki/FQDN">http://en.wikipedia.org/wiki/FQDN</a>.<br />
			<br />
			<form action="" method="post" id="setup">
				<fieldset>
					<legend>Basic system settings</legend>
					<table>
                        <tr>
                            <td><label for="HOST_OS">Please select your OS version</label></td>
                            <td><select name="HOST_OS" id="HOST_OS">{$HOST_OS}</select></td>
                        </tr>
						<tr>
							<td><label for="HOST_FQHN">Please enter a fully qualified hostname</label></td>
							<td><input type="text" name="HOST_FQHN" id="HOST_FQHN" value="{$HOST_FQHN}"/></td>
						</tr>
						<tr>
							<td><label for="HOST_IP">Please enter the system ipv4 network address</label></td>
							<td><input type="text" name="HOST_IP" id="HOST_IP" value="{$HOST_IP}"/></td>
						</tr>
						<tr>
							<td><label for="HOST_IPv6">Please enter the system ipv6 network address (if available)</label></td>
							<td><input type="text" name="HOST_IPv6" id="HOST_IPv6" value="{$HOST_IPv6}"/></td>
						</tr>
						<tr>
							<td><label for="HOST_NAME">Please enter the domain name where EasySCP will be reachable on</label></td>
							<td><input type="text" name="HOST_NAME" id="HOST_NAME" value="{$HOST_NAME}"/></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend>EasySCP admin panel settings</legend>
					<table>
						<tr>
							<td><label for="PANEL_ADMIN">Please enter administrator login name</label></td>
							<td><input type="text" name="PANEL_ADMIN" id="PANEL_ADMIN" value="{$PANEL_ADMIN}"/></td>
						</tr>
						<tr>
							<td><label for="PANEL_PASS">Please enter administrator password</label></td>
							<td><input type="password" name="PANEL_PASS" id="PANEL_PASS" value="{$PANEL_PASS}"/></td>
						</tr>
						<tr>
							<td><label for="PANEL_PASS2">Please repeat administrator password</label></td>
							<td><input type="password" name="PANEL_PASS2" id="PANEL_PASS2" value="{$PANEL_PASS2}"/></td>
						</tr>
						<tr>
							<td><label for="PANEL_MAIL">Please enter administrator e-mail address</label></td>
							<td><input type="text" name="PANEL_MAIL" id="PANEL_MAIL" value="{$PANEL_MAIL}"/></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend>EasySCP other settings</legend>
					<table>
						<tr>
							<td><label for="Secondary_DNS">Secondary DNS server address IP (optional)</label></td>
							<td><input type="text" name="Secondary_DNS" id="Secondary_DNS" value="{$Secondary_DNS}"/></td>
						</tr>
						<tr>
							<td>Do you want allow the system resolver to use the local nameserver?</td>
							<td>
								<input type="radio" name="LocalNS" id="LocalNS_yes" value="_yes_" {$LocalNS_yes} />
								<label for="LocalNS_yes">Yes</label>
								<input type="radio" name="LocalNS" id="LocalNS_no" value="_no_" {$LocalNS_no} />
								<label for="LocalNS_no">No</label>
							</td>
						</tr>
						<tr>
							<td>Use Database Prefix?</td>
							<td>
								<input type="radio" name="MySQL_Prefix" id="MySQL_Prefix_infront" value="infront" {$MySQL_infront} />
								<label for="MySQL_Prefix_infront">infront</label>
								<input type="radio" name="MySQL_Prefix" id="MySQL_Prefix_behind" value="behind" {$MySQL_behind} />
								<label for="MySQL_Prefix_behind">behind</label>
								<input type="radio" name="MySQL_Prefix" id="MySQL_Prefix_none" value="none" {$MySQL_none} />
								<label for="MySQL_Prefix_none">none</label>
							</td>
						</tr>
						<tr>
							<td><label for="Timezone">Please enter Server's Timezone</label></td>
							<td><input type="text" name="Timezone" id="Timezone" value="{$Timezone}"/></td>
						</tr>
						<!--
						<tr>
							<td>Should AWStats be activated?</td>
							<td>
								<input type="radio" name="AWStats" id="AWStats_yes" value="_yes_" {$AWStats_yes} />
								<label for="AWStats_yes">Yes</label>
								<input type="radio" name="AWStats" id="AWStats_no" value="_no_" {$AWStats_no} />
								<label for="AWStats_no">No</label>
							</td>
						</tr>
						-->
					</table>
				</fieldset>
				<div class="buttons">
					<input type="hidden" name="uaction" value="basic_system_settings" />
					<input type="submit" name="Submit" value="Next" />
				</div>
			</form>
			<div class="login"><p>Powered by <a class="link" href="http://www.easyscp.net" title="Easy Server Control Panel">Easy Server Control Panel</a></p></div>
		</div>
	</div>
{include file='footer.tpl'}