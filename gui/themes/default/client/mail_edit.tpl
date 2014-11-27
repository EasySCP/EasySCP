{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function(){
		// Tooltips - begin
		$('#fwd_help').EasySCPtooltips({ msg:"{$TR_FWD_HELP}"});
		// Tooltips - end
	});
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_EDIT_EMAIL_ACCOUNT}{/block}

{block name=BREADCRUMP}
<li><a href="/client/mail_accounts.php">{$TR_MENU_EMAIL_ACCOUNTS}</a></li>
<li><a href="/client/mail_accounts.php">{$TR_MENU_OVERVIEW}</a></li>
<li><a>{$TR_EDIT_EMAIL_ACCOUNT}</a></li>
{/block}

{block name=BODY}
<h2 class="email"><span>{$TR_EDIT_EMAIL_ACCOUNT}</span></h2>
<form action="/client/mail_edit.php?id={$MAIL_ID}" method="post" id="client_mail_edit">
	<fieldset>
		<legend>{$EMAIL_ACCOUNT}</legend>
		{if isset($NORMAL_MAIL)}
		<table>
			<tr>
				<td>{$TR_PASSWORD}</td>
				<td><input type="password" name="pass" id="pass" value="" /></td>
			</tr>
			<tr>
				<td>{$TR_PASSWORD_REPEAT}</td>
				<td><input type="password" name="pass_rep" id="pass_rep" value="" /></td>
			</tr>
		</table>
		{/if}
		{if isset($FORWARD_MAIL)}
		<table>
			<tr>
				<td colspan="2"><input type="checkbox" name="mail_forward" id="mail_forward" value="1" onclick="changeType('forward');" checked="checked" disabled="disabled" />&nbsp;{$TR_FORWARD_MAIL}</td>
			</tr>
			<tr>
				<td>{$TR_FORWARD_TO} <span id="fwd_help" class="icon i_help">&nbsp;</span></td>
				<td><textarea name="forward_list" id="forward_list" cols="35" rows="5">{$FORWARD_LIST}</textarea></td>
			</tr>
		</table>
		{/if}
	</fieldset>
	<div class="buttons">
		<input type="hidden" name="id" value="{$MAIL_ID}" />
		<input type="hidden" name="mail_account" value="{$EMAIL_ACCOUNT}" />
		<input type="hidden" name="mail_type" value="{$MAIL_TYPE}" />
		<input type="hidden" name="uaction" value="{$ACTION}" />
		<input type="submit" name="Submit"  value="{$TR_SAVE}" />
	</div>
</form>
{/block}