{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_USER_ASSIGNMENT}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/manage_users.php">{$TR_MENU_MANAGE_USERS}</a></li>
<li><a>{$TR_USER_ASSIGNMENT}</a></li>
{/block}

{block name=BODY}
<h2 class="users2"><span>{$TR_USER_ASSIGNMENT}</span></h2>
<form action="/admin/manage_reseller_users.php" method="post" id="admin_user_assignment">
	{if isset($SRC_RSL_VALUE)}
	<div class="buttons">
		{$TR_FROM_RESELLER}
		<select name="src_reseller" onchange="return sbmt(document.forms[0],'change_src');">
			{section name=i loop=$SRC_RSL_VALUE}
			<option {$SRC_RSL_SELECTED[i]} value="{$SRC_RSL_VALUE[i]}">{$SRC_RSL_OPTION[i]}</option>
			{/section}
		</select>
	</div>
	{/if}
	{if isset($USER_NAME)}
	<table>
		<thead>
			<tr>
				<th>{$TR_NUMBER}</th>
				<th>{$TR_MARK}</th>
				<th>{$TR_USER_NAME}</th>
			</tr>
		</thead>
		<tbody>
			{section name=i loop=$USER_NAME}
			<tr>
				<td>{$NUMBER[i]}</td>
				<td><input id="{$CKB_NAME[i]}" type="checkbox" name="{$CKB_NAME[i]}" /></td>
				<td><label for="{$CKB_NAME[i]}">{$USER_NAME[i]}</label></td>
			</tr>
			{/section}
		</tbody>
	</table>
	{/if}
	{if isset($DST_RSL_VALUE)}
	<div class="buttons">
		{$TR_TO_RESELLER}
		<select name="dst_reseller">
			{section name=i loop=$DST_RSL_VALUE}
			<option {$DST_RSL_SELECTED[i]} value="{$DST_RSL_VALUE[i]}">{$DST_RSL_OPTION[i]}</option>
			{/section}
		</select>
		<input type="hidden" name="uaction" value="move_user" />
		<input type="submit" name="Submit" value="{$TR_MOVE}" />
	</div>
	{/if}
</form>
{/block}