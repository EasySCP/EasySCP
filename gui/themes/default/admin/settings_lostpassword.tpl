{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_LOSTPW_EMAIL}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/settings.php">{$TR_MENU_SETTINGS}</a></li>
<li><a>{$TR_LOSTPW_EMAIL}</a></li>
{/block}

{block name=BODY}
<h2 class="email"><span>{$TR_LOSTPW_EMAIL}</span></h2>
<form action="/admin/settings_lostpassword.php" method="post" id="admin_settings_lostpassword">
	<fieldset>
		<legend>{$TR_MESSAGE_TEMPLATE_INFO}</legend>
		<table>
			<tr>
				<td colspan="2"><b>{$TR_ACTIVATION_EMAIL}</b></td>
				<td colspan="2"><b>{$TR_PASSWORD_EMAIL}</b></td>
			</tr>
			<tr>
				<td><b>{$TR_USER_LOGIN_NAME}</b></td>
				<td>{literal}{USERNAME}{/literal}</td>
				<td><b>{$TR_USER_LOGIN_NAME}</b></td>
				<td>{literal}{USERNAME}{/literal}</td>
			</tr>
			<tr>
				<td><b>{$TR_LOSTPW_LINK}</b></td>
				<td>{literal}{LINK}{/literal}</td>
				<td><b>{$TR_USER_PASSWORD}</b></td>
				<td>{literal}{PASSWORD}{/literal}</td>
			</tr>
			<tr>
				<td><b>{$TR_USER_REAL_NAME}</b></td>
				<td>{literal}{NAME}{/literal}</td>
				<td><b>{$TR_USER_REAL_NAME}</b></td>
				<td>{literal}{NAME}{/literal}</td>
			</tr>
			<tr>
				<td><b>{$TR_BASE_SERVER_VHOST}</b></td>
				<td>{literal}{BASE_SERVER_VHOST}{/literal}</td>
				<td><b>{$TR_BASE_SERVER_VHOST}</b></td>
				<td>{literal}{BASE_SERVER_VHOST}{/literal}</td>
			</tr>
			<tr>
				<td><b>{$TR_BASE_SERVER_VHOST_PREFIX}</b></td>
				<td>{literal}{BASE_SERVER_VHOST_PREFIX}{/literal}</td>
				<td><b>{$TR_BASE_SERVER_VHOST_PREFIX}</b></td>
				<td>{literal}{BASE_SERVER_VHOST_PREFIX}{/literal}</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>{$TR_MESSAGE_TEMPLATE}</legend>
		<table>
			<tr>
				<td>{$TR_SUBJECT}</td>
				<td><input type="text" name="subject1" id="subject1" value="{$SUBJECT_VALUE1}" /></td>
				<td><input type="text" name="subject2" id="subject2" value="{$SUBJECT_VALUE2}" /></td>
			</tr>
			<tr>
				<td>{$TR_MESSAGE}</td>
				<td><textarea name="message1" id="message1" cols="80" rows="20" style="width: 400px;">{$MESSAGE_VALUE1}</textarea></td>
				<td><textarea name="message2" id="message2" cols="80" rows="20" style="width: 400px;">{$MESSAGE_VALUE2}</textarea></td>
			</tr>
			<tr>
				<td>{$TR_SENDER_EMAIL}</td>
				<td colspan="2">{$SENDER_EMAIL_VALUE}</td>
			</tr>
			<tr>
				<td>{$TR_SENDER_NAME}</td>
				<td colspan="2">{$SENDER_NAME_VALUE}</td>
			</tr>
		</table>
	</fieldset>
	<div class="buttons">
		<input type="hidden" name="uaction" value="apply" />
				<input type="submit" name="Submit" value="{$TR_APPLY_CHANGES}" />
	</div>
</form>
{/block}