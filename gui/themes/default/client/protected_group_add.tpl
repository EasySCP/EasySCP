{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_ADD_GROUP}{/block}

{block name=BREADCRUMP}
<li><a href="/client/webtools.php">{$TR_MENU_WEBTOOLS}</a></li>
<li><a href="/client/protected_user_manage.php">{$TR_HTACCESS_USER}</a></li>
<li><a>{$TR_ADD_GROUP}</a></li>
{/block}

{block name=BODY}
<h2 class="users"><span>{$TR_ADD_GROUP}</span></h2>
<form action="/client/protected_group_add.php" method="post" id="client_protected_group_add">
	<table>
		<tr>
			<td>{$TR_GROUPNAME}</td>
			<td><input type="text" name="groupname" id="groupname" value="" /></td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="uaction" value="add_group" />
		<input type="submit" name="Submit" value="{$TR_ADD_GROUP}" />
	</div>
</form>
{/block}