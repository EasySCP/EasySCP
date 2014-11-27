{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function(){
		changeType('{$DEFAULT}');
	});
	function changeType(type) {
		if (type == "normal") {
			document.forms[0].mail_id.disabled = false;
			document.forms[0].forward_list.disabled = true;
		} else {
			document.forms[0].mail_id.disabled = true;
			document.forms[0].forward_list.disabled = false;
		}
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_CREATE_CATCHALL_MAIL_ACCOUNT}{/block}

{block name=BREADCRUMP}
<li><a href="/client/mail_accounts.php">{$TR_MENU_EMAIL_ACCOUNTS}</a></li>
<li><a href="/client/mail_catchall.php">{$TR_MENU_CATCH_ALL_MAIL}</a></li>
<li><a>{$TR_CREATE_CATCHALL_MAIL_ACCOUNT}</a></li>
{/block}

{block name=BODY}
<h2 class="email"><span>{$TR_CREATE_CATCHALL_MAIL_ACCOUNT}</span></h2>
<form action="/client/mail_catchall_add.php" method="post" id="client_mail_catchall_add">
	<table>
		<tr>
			<td><input type="radio" name="mail_type" id="mail_type1" value="normal" onclick="changeType('normal');" {$NORMAL_MAIL} />&nbsp;{$TR_MAIL_LIST}</td>
			<td>
				<select name="mail_id">
					{section name=i loop=$MAIL_ACCOUNT}
					<option value="{$MAIL_ID[i]};{$MAIL_ACCOUNT_PUNNY[i]};">{$MAIL_ACCOUNT[i]}</option>
					{/section}
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="radio" name="mail_type" id="mail_type2" value="forward" onclick="changeType('forward');" {$FORWARD_MAIL} />&nbsp;{$TR_FORWARD_MAIL}</td>
		</tr>
		<tr>
			<td>{$TR_FORWARD_TO}</td>
			<td><textarea name="forward_list" id="forward_list" cols="35" rows="5"></textarea></td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="id" value="{$ID}" />
		<input type="hidden" name="uaction" value="create_catchall" />
		<input type="submit" name="Submit" value="{$TR_CREATE_CATCHALL}" />
	</div>
</form>
{/block}