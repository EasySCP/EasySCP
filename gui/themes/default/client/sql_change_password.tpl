{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_CHANGE_SQL_USER_PASSWORD}{/block}

{block name=BREADCRUMP}
<li><a href="/client/sql_manage.php">{$TR_MENU_MANAGE_SQL}</a></li>
<li><a>{$TR_CHANGE_SQL_USER_PASSWORD}</a></li>
{/block}

{block name=BODY}
<h2 class="password"><span>{$TR_CHANGE_SQL_USER_PASSWORD}</span></h2>
<form action="/client/sql_change_password.php" method="post" id="client_sql_change_password">
	<table>
		<tr>
			<td>{$TR_USER_NAME}</td>
			<td><input id="user_name" type="text" name="user_name" value="{$USER_NAME}" readonly="readonly" /></td>
		</tr>
		<tr>
			<td>{$TR_PASS}</td>
			<td><input id="pass" type="password" name="pass" value="" /></td>
		</tr>
		<tr>
			<td>{$TR_PASS_REP}</td>
			<td><input id="pass_rep" type="password" name="pass_rep" value="" /></td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="id" value="{$ID}" />
		<input type="hidden" name="uaction" value="change_pass" />
		<input type="submit" name="Submit" value="{$TR_CHANGE}" />
	</div>
</form>
{/block}