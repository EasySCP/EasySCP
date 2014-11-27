{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_CIRCULAR}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/manage_users.php">{$TR_MENU_MANAGE_USERS}</a></li>
<li><a>{$TR_CIRCULAR}</a></li>
{/block}

{block name=BODY}
<h2 class="email"><span>{$TR_CIRCULAR}</span></h2>
<form action="/admin/circular.php" method="post" id="admin_circular">
	<fieldset>
		<legend>{$TR_CORE_DATA}</legend>
		<table>
			<tr>
				<td><label for="rcpt_to">{$TR_SEND_TO}</label></td>
				<td>
					<select id="rcpt_to" name="rcpt_to">
						<option value="usrs">{$TR_ALL_USERS}</option>
						<option value="rslrs">{$TR_ALL_RESELLERS}</option>
						<option value="usrs_rslrs">{$TR_ALL_USERS_AND_RESELLERS}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="msg_subject">{$TR_MESSAGE_SUBJECT}</label></td>
				<td><input type="text" name="msg_subject" id="msg_subject" value="{$MESSAGE_SUBJECT}"/></td>
			</tr>
			<tr>
				<td><label for="msg_text">{$TR_MESSAGE_TEXT}</label></td>
				<td><textarea name="msg_text" id="msg_text" cols="80" rows="20">{$MESSAGE_TEXT}</textarea></td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>{$TR_ADDITIONAL_DATA}</legend>
		<table>
			<tr>
				<td><label for="sender_email">{$TR_SENDER_EMAIL}</label></td>
				<td><input type="text" name="sender_email" id="sender_email" value="{$SENDER_EMAIL}"/></td>
			</tr>
			<tr>
				<td><label for="sender_name">{$TR_SENDER_NAME}</label></td>
				<td><input type="text" name="sender_name" id="sender_name" value="{$SENDER_NAME}"/></td>
			</tr>
		</table>
	</fieldset>
	<div class="buttons">
		<input type="hidden" name="uaction" value="send_circular" />
		<input type="submit" name="Submit" value="{$TR_SEND_MESSAGE}" />
	</div>
</form>
{/block}