{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_MENU_LOSTPW_EMAIL}{/block}

{block name=BREADCRUMP}
<li><a href="/reseller/users.php">{$TR_MENU_MANAGE_USERS}</a></li>
<li><a>{$TR_MENU_LOSTPW_EMAIL}</a></li>
{/block}

{block name=BODY}
<h2 class="email"><span>{$TR_LOSTPW_EMAIL}</span></h2>
<form action="/reseller/settings_lostpassword.php" method="post" id="reseller_settings_lostpassword">
	<fieldset>
		<legend>{$TR_MESSAGE_TEMPLATE_INFO}</legend>
		<table>
			<thead>
				<tr>
					<th colspan="2">{$TR_ACTIVATION_EMAIL}</th>
					<th colspan="2">{$TR_PASSWORD_EMAIL}</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{$TR_USER_LOGIN_NAME}</td>
					<td>{literal}{USERNAME}{/literal}</td>
					<td>{$TR_USER_LOGIN_NAME}</td>
					<td>{literal}{USERNAME}{/literal}</td>
				</tr>
				<tr>
					<td>{$TR_LOSTPW_LINK}</td>
					<td>{literal}{LINK}{/literal}</td>
					<td>{$TR_USER_PASSWORD}</td>
					<td>{literal}{PASSWORD}{/literal}</td>
				</tr>
				<tr>
					<td>{$TR_USER_REAL_NAME}</td>
					<td>{literal}{NAME}{/literal}</td>
					<td>{$TR_USER_REAL_NAME}</td>
					<td>{literal}{NAME}{/literal}</td>
				</tr>
				<tr>
					<td>{$TR_BASE_SERVER_VHOST}</td>
					<td>{literal}{BASE_SERVER_VHOST}{/literal}</td>
					<td>{$TR_BASE_SERVER_VHOST}</td>
					<td>{literal}{BASE_SERVER_VHOST}{/literal}</td>
				</tr>
				<tr>
					<td>{$TR_BASE_SERVER_VHOST_PREFIX}</td>
					<td>{literal}{BASE_SERVER_VHOST_PREFIX}{/literal}</td>
					<td>{$TR_BASE_SERVER_VHOST_PREFIX}</td>
					<td>{literal}{BASE_SERVER_VHOST_PREFIX}{/literal}</td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<fieldset>
		<legend>{$TR_MESSAGE_TEMPLATE}</legend>
		<table>
			<tr>
				<td>{$TR_SENDER_EMAIL}</td>
				<td colspan="2">{$SENDER_EMAIL_VALUE}</td>
			</tr>
			<tr>
				<td>{$TR_SENDER_NAME}</td>
				<td colspan="2">{$SENDER_NAME_VALUE}</td>
			</tr>
			<tr>
				<td>{$TR_SUBJECT}</td>
				<td><input type="text" name="subject1" id="subject1" value="{$SUBJECT_VALUE1}" /></td>
				<td><input type="text" name="subject2" value="{$SUBJECT_VALUE2}" /></td>
			</tr>
			<tr>
				<td>{$TR_MESSAGE}</td>
				<td><textarea name="message1" cols="40" rows="20" id="message1">{$MESSAGE_VALUE1}</textarea></td>
				<td><textarea name="message2" cols="40" rows="20" id="message2">{$MESSAGE_VALUE2}</textarea></td>
			</tr>
		</table>
	</fieldset>
	<div class="buttons">
		<input type="hidden" name="sender_email" value="{$SENDER_EMAIL_VALUE}" />
		<input type="hidden" name="sender_name" value="{$SENDER_NAME_VALUE}" />
		<input type="hidden" name="uaction" value="apply" />
				<input type="submit" name="Submit" value="{$TR_APPLY_CHANGES}" />
	</div>
</form>
{/block}