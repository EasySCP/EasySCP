{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_MENU_ADD_SQL_DATABASE}{/block}

{block name=BREADCRUMP}
<li><a href="/client/sql_manage.php">{$TR_MENU_MANAGE_SQL}</a></li>
<li><a>{$TR_MENU_ADD_SQL_DATABASE}</a></li>
{/block}

{block name=BODY}
<h2 class="sql"><span>{$TR_ADD_DATABASE}</span></h2>
<form action="/client/sql_database_add.php" method="post" id="client_sql_database_add">
	<table>
		<tr>
			<td>{$TR_DB_NAME}</td>
			<td><input type="text" name="db_name" id="db_name" value="{$DB_NAME}" /></td>
		</tr>
		<tr>
			<td>
				{if isset($MYSQL_PREFIX_YES)}
				<input type="checkbox" name="use_dmn_id" {$USE_DMN_ID} />
				{/if}
				{if isset($MYSQL_PREFIX_NO)}
				<input type="hidden" name="use_dmn_id" value="on" />
				{/if}
				{$TR_USE_DMN_ID}
			</td>
			<td>
				{if isset($MYSQL_PREFIX_ALL)}
				<input type="radio" name="id_pos" value="start" {$START_ID_POS_CHECKED} />&nbsp;{$TR_START_ID_POS}<br />
				<input type="radio" name="id_pos" value="end" {$END_ID_POS_CHECKED} />&nbsp;{$TR_END_ID_POS}
				{/if}
				{if isset($MYSQL_PREFIX_INFRONT)}
				<input type="hidden" name="id_pos" value="start" checked="checked" />{$TR_START_ID_POS}
				{/if}
				{if isset($MYSQL_PREFIX_BEHIND)}
				<input type="hidden" name="id_pos" value="end" checked="checked" />{$TR_END_ID_POS}
				{/if}
			</td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="uaction" value="add_db" />
		<input type="submit"  name="Add_New" value="{$TR_ADD}" />
	</div>
</form>
{/block}