{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_RESELLER_ASSIGNMENT}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/manage_users.php">{$TR_MENU_MANAGE_USERS}</a></li>
<li><a>{$TR_RESELLER_ASSIGNMENT}</a></li>
{/block}

{block name=BODY}
<h2 class="users2"><span>{$TR_RESELLER_ASSIGNMENT}</span></h2>
{if isset($NUMBER)}
<form action="/admin/manage_reseller_owners.php" method="post" id="admin_reseller_assignment">
	<table>
		<tr>
			<th>{$TR_NUMBER}</th>
			<th>{$TR_MARK}</th>
			<th>{$TR_RESELLER_NAME}</th>
			<th>{$TR_OWNER}</th>
		</tr>
		{section name=i loop=$NUMBER}
		<tr>
			<td>{$NUMBER[i]}</td>
			<td><input id="{$CKB_NAME[i]}" type="checkbox" name="{$CKB_NAME[i]}" /></td>
			<td><label for="{$CKB_NAME[i]}">{$RESELLER_NAME[i]}</label></td>
			<td>{$OWNER[i]}</td>
		</tr>
		{/section}
	</table>
	{if isset($VALUE)}
	<div class="buttons">
		{$TR_TO_ADMIN}
		<select name="dest_admin">
			{section name=i loop=$VALUE}
			<option {$SELECTED[i]} value="{$VALUE[i]}">{$OPTION[i]}</option>
			{/section}
		</select>
		<input type="hidden" name="uaction" value="reseller_owner" />
		<input type="submit" name="Submit" value="{$TR_MOVE}" />
	</div>
	{/if}
</form>
{/if}
{/block}