{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function delete_account(url, name) {
		if (!confirm(sprintf("{$TR_MESSAGE_DELETE}", name)))
			return false;
		location = url;
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_MENU_DOMAIN_ALIAS}{/block}

{block name=BREADCRUMP}
<li><a href="/reseller/users.php">{$TR_MENU_MANAGE_USERS}</a></li>
<li><a>{$TR_MENU_DOMAIN_ALIAS}</a></li>
{/block}

{block name=BODY}
<h2 class="users"><span>{$TR_MANAGE_ALIAS}</span></h2>
{if isset($M_DOMAIN_NAME_SELECTED)}
<form action="/reseller/alias.php?psi={$PSI}" method="post" id="reseller_alias">
	<fieldset>
		<input type="text" name="search_for" id="search_for" value="{$SEARCH_FOR}" />
		<select name="search_common">
			<option value="alias_name" {$M_DOMAIN_NAME_SELECTED}>{$M_ALIAS_NAME}</option>
			<option value="account_name" {$M_ACCOUN_NAME_SELECTED}>{$M_ACCOUNT_NAME}</option>
		</select>
		<input type="hidden" name="uaction" value="go_search" />
		<input type="submit" name="Submit" value="{$TR_SEARCH}" />
	</fieldset>
</form>
{/if}
{if isset($NAME)}
<table>
	<thead>
		<tr>
			<th>{$TR_NAME}</th>
			<th>{$TR_REAL_DOMAIN}</th>
			<th>{$TR_FORWARD}</th>
			<th>{$TR_STATUS}</th>
			<th>{$TR_ACTION}</th>
		</tr>
	</thead>
	<tbody>
		{section name=i loop=$NAME}
		<tr>
			<td><a href="http://www.{$NAME[i]}/" class="icon i_domain">{$NAME[i]}</a><br />{$ALIAS_IP[i]}</td>
			<td>{$REAL_DOMAIN[i]}<br />{$REAL_DOMAIN_MOUNT[i]}</td>
			<td>{$FORWARD[i]}</td>
			<td>{$STATUS[i]}</td>
			<td>
				<a href="{$EDIT_LINK[i]}" title="{$EDIT[i]}" class="icon i_edit"></a>
				<a href="#" onclick="delete_account('{$DELETE_LINK[i]}', '{$NAME[i]}')" title="{$DELETE[i]}" class="icon i_delete"></a>
			</td>
		</tr>
		{/section}
	</tbody>
</table>
{/if}
<form action="alias_add.php" method="post" id="admin_alias_add">
	<fieldset>
		<input type="submit" name="Submit"  value="{$TR_ADD_ALIAS}" />
	</fieldset>
</form>
<div class="paginator">
	{if !isset($SCROLL_NEXT_GRAY)}
	<span class="icon i_next_gray">&nbsp;</span>
	{/if}
	{if !isset($SCROLL_NEXT)}
	<a href="alias.php?psi={$NEXT_PSI}" title="next" class="icon i_next">next</a>
	{/if}
	{if !isset($SCROLL_PREV_GRAY)}
	<span class="icon i_prev_gray">&nbsp;</span>
	{/if}
	{if !isset($SCROLL_PREV)}
	<a href="alias.php?psi={$PREV_PSI}" title="previous" class="icon i_prev">previous</a>
	{/if}
</div>
{/block}