{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_CHANGE_PASSWORD}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/index.php">{$TR_MENU_GENERAL_INFORMATION}</a></li>
<li><a>{$TR_CHANGE_PASSWORD}</a></li>
{/block}

{block name=BODY}
<h2 class="password"><span>{$TR_CHANGE_PASSWORD}</span></h2>
<form action="password_change.php" method="post" id="admin_password_change">
	<table>
		<tr>
			<td><label for="curr_pass">{$TR_CURR_PASSWORD}</label></td>
			<td><input type="password" name="curr_pass" id="curr_pass" value=""/></td>
		</tr>
		<tr>
			<td><label for="pass">{$TR_PASSWORD}</label></td>
			<td><input type="password" name="pass" id="pass" value="" /></td>
		</tr>
		<tr>
			<td><label for="pass_rep">{$TR_PASSWORD_REPEAT}</label></td>
			<td><input type="password" name="pass_rep" id="pass_rep" value="" /></td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="uaction" value="updt_pass" />
		<input name="Submit" type="submit" value="{$TR_UPDATE_PASSWORD}" />
	</div>
</form>
{/block}