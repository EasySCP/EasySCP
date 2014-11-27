{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_UPDATES_TITLE}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/system_info.php">{$TR_MENU_SYSTEM_TOOLS}</a></li>
<li><a>{$TR_UPDATES_TITLE}</a></li>
{/block}

{block name=BODY}
<h2 class="update"><span>{$TR_UPDATES_TITLE}</span></h2>
{if isset($UPDATE_MESSAGE)}
<div class="{$UPDATE_MSG_TYPE}">{$UPDATE_MESSAGE}</div>
{/if}
{if isset($UPDATE)}
<table>
	<thead>
		<tr>
			<th colspan="2">{$TR_AVAILABLE_UPDATES}</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><strong>{$TR_UPDATE}</strong></td>
			<td>{$UPDATE}</td>
		</tr>
		<tr>
			<td><strong>{$TR_INFOS}</strong></td>
			<td>{$INFOS}</td>
		</tr>
	</tbody>
</table>
{/if}
<form action="/admin/easyscp_updates.php" enctype="multipart/form-data" method="post" id="easyscp_update">
	<table>
		<tr>
			<td><label for="update_file">Update File upload</label></td>
			<td><input type="file" name="update_file" id="update_file" /></td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="execute" id='execute' value="update_file" />
		<input type="submit" name="Submit" value="{$TR_EXECUTE_UPDATE}" />
	</div>
</form>
<br />
<h2 class="update"><span>{$TR_DB_UPDATES_TITLE}</span></h2>
{if isset($DB_UPDATE_MESSAGE)}
<div class="{$DB_UPDATE_MSG_TYPE}">{$DB_UPDATE_MESSAGE}</div>
{/if}
{if isset($DB_UPDATE)}
<form action="/admin/easyscp_updates.php" method="post" id="database_update">
	<table>
		<thead>
			<tr>
				<th colspan="2">{$TR_DB_AVAILABLE_UPDATES}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><strong>{$TR_UPDATE}</strong></td>
				<td>{$DB_UPDATE}</td>
			</tr>
			<tr>
				<td><strong>{$TR_INFOS}</strong></td>
				<td>{$DB_INFOS}</td>
			</tr>
		</tbody>
	</table>
	<div class="buttons">
		<input type="hidden" name="execute" id='execute' value="update" />
		<input type="submit" name="Submit" value="{$TR_EXECUTE_UPDATE}" />
	</div>
</form>
{/if}
{if isset($MIGRATION_ENABLED)}
<br />
<h2 class="update"><span>{$TR_MIGRATION_TITLE}</span></h2>
	{if isset($MIGRATION_MESSAGE)}
		<div class="{$MIGRATION_MSG_TYPE}">{$MIGRATION_MESSAGE}</div>
	{/if}
	{if isset($MIGRATION_AVAILABLE)}
	<form action="/admin/easyscp_updates.php" method="post" id="database_migration">
		<table>
			<thead>
				<tr>
					<th colspan="2">{$TR_MIGRATION_AVAILABLE}</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td><strong>{$TR_MIGRATION_INFOS}</strong></td>
					<td>{$MIGRATION_INFOS}</td>
				</tr>
				</tbody>
			</table>
			<div class="buttons">
				<input type="hidden" name="execute_migration" id='execute_migration' value="migrate" />
				<input type="hidden" name="migration_version" id='migration_version' value="{$MIGRATION_VERSION}" />
				<input type="submit" name="Submit" value="{$TR_EXECUTE_MIGRATION}" />
			</div>
		</form>
	{/if}
{/if}
{/block}