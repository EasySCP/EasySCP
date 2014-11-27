{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_MANAGE_USER_SESSIONS}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/manage_users.php">{$TR_MENU_MANAGE_USERS}</a></li>
<li><a>{$TR_MANAGE_USER_SESSIONS}</a></li>
{/block}

{block name=BODY}
<h2 class="users2"><span>{$TR_MANAGE_USER_SESSIONS}</span></h2>
<table>
	<tr>
		<th>{$TR_USERNAME}</th>
		<th>{$TR_LOGIN_ON}</th>
		<th>{$TR_OPTIONS}</th>
	</tr>
	{section name=i loop=$ADMIN_USERNAME}
	<tr>
		<td>{$ADMIN_USERNAME[i]}</td>
		<td>{$LOGIN_TIME[i]}</td>
		<td><a href="{$KILL_LINK[i]}" title="{$TR_DELETE}" class="icon i_delete"></a></td>
	</tr>
	{/section}
</table>
{/block}