{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function action_delete(url, subject) {
		if (!confirm(sprintf("{$TR_MESSAGE_DELETE}", '"' + subject + '"')))
		{
			return false;
		} else {
			location = url;
			return true;
		}
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_MENU_OVERVIEW}{/block}

{block name=BREADCRUMP}
<li><a href="/client/sql_manage.php">{$TR_MENU_MANAGE_SQL}</a></li>
<li><a>{$TR_MENU_OVERVIEW}</a></li>
{/block}

{block name=BODY}
{if isset($DB_NAME)}
<h2 class="sql"><span>{$TR_MANAGE_SQL}</span></h2>
<table>
	<tr>
		<th>{$TR_DATABASE}</th>
		<th>{$TR_ACTION}</th>
	</tr>
	{section name=i loop=$DB_NAME}
	<tr>
		<td><span class="icon i_database_small">&nbsp;</span> {$DB_NAME[i]}</td>
		<td>
			<a href="/client/sql_user_add.php?id={$DB_ID[i]}" title="{$TR_ADD_USER}" class="icon i_add_user"></a>
			<a href="#" onclick="action_delete('sql_database_delete.php?id={$DB_ID[i]}', '{$DB_NAME[i]}')" title="{$TR_DELETE}" class="icon i_delete"></a>
		</td>
	</tr>
	{if $DB_MSG[i]}
	<tr>
		<td colspan="2">{$DB_MSG[i]}</td>
	</tr>
	{/if}
	{section name=user loop=$DB_USERLIST[i]}
	<tr>
		<td><span class="icon i_users">&nbsp;</span> {$DB_USERLIST[i][user].DB_USER}</td>
		<td>
			<a href="/client/pma_auth.php?id={$DB_USERLIST[i][user].USER_ID}" id="phpMyAdmin" title="{$TR_PHP_MYADMIN}" class="icon i_pma external"></a>
			<a href="/client/sql_change_password.php?id={$DB_USERLIST[i][user].USER_ID}" title="{$TR_CHANGE_PASSWORD}" class="icon i_change_password"></a>
			<a href="#" onclick="action_delete('sql_delete_user.php?id={$DB_USERLIST[i][user].USER_ID}', '{$DB_USERLIST[i][user].DB_USER}')" title="{$TR_DELETE}" class="icon i_delete"></a>
		</td>
	</tr>
	{/section}
	{/section}
</table>
{/if}
{/block}