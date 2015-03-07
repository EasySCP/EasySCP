{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
{/block}

{block name=CONTENT_HEADER}{$TR_USER_ASSIGN}{/block}

{block name=BREADCRUMP}
	<li><a href="/client/webtools.php">{$TR_MENU_WEBTOOLS}</a></li>
	<li><a href="/client/protected_user_manage.php">{$TR_HTACCESS_USER}</a></li>
	<li><a>{$TR_USER_ASSIGN}</a></li>
{/block}

{block name=BODY}
<h2 class="users"><span>{$TR_USER_ASSIGN}</span></h2>
<table>
	<tr>
		<th colspan="3">{$UNAME}</th>
	</tr>
</table>
{if isset($IN_GROUP)}
<form action="/client/protected_user_assign.php?uname={$UNAME}" method="post" id="client_protected_user_assign_remove">
	<table>
		<tr>
			<td>{$TR_MEMBER_OF_GROUP}</td>
			<td>
				<select name="groups_in">
					{section name=i loop=$GRP_IN}
					<option value="{$GRP_IN_ID[i]}">{$GRP_IN[i]}</option>
					{/section}
				</select>
			</td>
			<td>
				<div class="buttons">
					<input type="hidden" name="nadmin_name" value="{$UID}" />
					<input type="hidden" name="uaction" value="remove" />
					<input type="submit" name="Submit" value="{$TR_REMOVE}" />
				</div>
			</td>
		</tr>
	</table>
</form>
{/if}
{if isset($NOT_IN_GROUP)}
<form action="/client/protected_user_assign.php?uname={$UNAME}" method="post" id="client_protected_user_assign_add">
	<table>
		<tr>
			<td>{$TR_SELECT_GROUP}</td>
			<td>
				<select name="groups">
					{section name=i loop=$GRP_NAME}
					<option value="{$GRP_ID[i]}">{$GRP_NAME[i]}</option>
					{/section}
				</select>
			</td>
			<td>
				<div class="buttons">
					<input type="hidden" name="nadmin_name" value="{$UID}" />
					<input type="hidden" name="uaction" value="add" />
					<input type="submit" name="Submit" value="{$TR_ADD}" />
				</div>
			</td>
		</tr>
	</table>
</form>
{/if}
{/block}