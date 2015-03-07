{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
{/block}

{block name=CONTENT_HEADER}{$TR_ADD_USER}{/block}

{block name=BREADCRUMP}
	<li><a href="/client/webtools.php">{$TR_MENU_WEBTOOLS}</a></li>
	<li><a href="/client/protected_user_manage.php">{$TR_HTACCESS_USER}</a></li>
	<li><a>{$TR_ADD_USER}</a></li>
{/block}

{block name=BODY}
<h2 class="users"><span>{$TR_ADD_USER}</span></h2>
<form action="/client/protected_user_add.php" method="post" id="client_protected_user_add">
	<table>
		<tr>
			<td>{$TR_USERNAME}</td>
			<td><input type="text" name="username" id="username" value="" /></td>
		</tr>
		<tr>
			<td>{$TR_PASSWORD}</td>
			<td><input type="password" id="pass" name="pass" value="" /></td>
		</tr>
		<tr>
			<td>{$TR_PASSWORD_REPEAT}</td>
			<td><input type="password" id="pass_rep" name="pass_rep" value="" /></td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="uaction" value="add_user" />
		<input type="submit" name="Submit" value="{$TR_ADD_USER}" />
	</div>
</form>
{/block}