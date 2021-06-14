{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function change_status(dom_id, dmn_name) {
		if (!confirm(sprintf("Are you sure you want to change the status of %s?", dmn_name)))
			return false;
		location = ("domain_status_change.php?domain_id=" + dom_id);
	}

	function action_delete(url, dmn_name) {
		if (!confirm(sprintf("{$TR_MESSAGE_DELETE_ACCOUNT}", dmn_name)))
			return false;
		location = url;
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_MENU_OVERVIEW}{/block}

{block name=BREADCRUMP}
<li><a href="/reseller/users.php">{$TR_MENU_MANAGE_USERS}</a></li>
<li><a>{$TR_MENU_OVERVIEW}</a></li>
{/block}

{block name=BODY}
<h2 class="users"><span>{$TR_MANAGE_USERS}</span></h2>
<form action="users.php" method="post" id="reseller_users">
	<fieldset>
		<a href="#" onclick="return sbmt_details(document.forms[0],'{$SHOW_DETAILS}');" class="icon i_show_alias">{$TR_VIEW_DETAILS}</a>
		<input type="text" name="search_for" id="search_for" value="{$SEARCH_FOR}" />
		<select name="search_common">
			<option value="domain_name" {$M_DOMAIN_NAME_SELECTED}>{$M_DOMAIN_NAME}</option>
			<option value="customer_id" {$M_CUSTOMER_ID_SELECTED}>{$M_CUSTOMER_ID}</option>
			<option value="lname" {$M_LAST_NAME_SELECTED}>{$M_LAST_NAME}</option>
			<option value="firm" {$M_COMPANY_SELECTED}>{$M_COMPANY}</option>
			<option value="city" {$M_CITY_SELECTED}>{$M_CITY}</option>
			<option value="country" {$M_COUNTRY_SELECTED}>{$M_COUNTRY}</option>
		</select>
		<select name="search_status">
			<option value="all" {$M_ALL_SELECTED}>{$M_ALL}</option>
			<option value="ok" {$M_OK_SELECTED}>{$M_OK}</option>
			<option value="disabled" {$M_SUSPENDED_SELECTED}>{$M_SUSPENDED}</option>
		</select>
		<input type="hidden" name="details" value="" />
		<input type="hidden" name="uaction" value="go_search" />
		<input type="submit" name="Submit" value="{$TR_SEARCH}" />
	</fieldset>
</form>
{if isset($NAME)}
<table class="tablesorter">
	<thead>
		<tr>
			<th style="width:50px">{$TR_USER_STATUS}</th>
			<th>{$TR_USERNAME}</th>
			<th style="width:100px">{$TR_CREATION_DATE}</th>
			<th>{$TR_DISK_USAGE}</th>
			<th style="width:200px">{$TR_ACTION}</th>
		</tr>
	</thead>
	<tbody>
		{section name=i loop=$NAME}
		<tr>
			<td><a href="#" onclick="change_status('{$URL_CHANGE_STATUS[i]}', '{$NAME[i]}')" title="{$STATUS_ICON[i]}" class="icon i_{$STATUS_ICON[i]}"></a></td>
			<td><a href="http://{$NAME[i]}/" class="icon i_goto">{$NAME[i]}</a></td>
			<td>{$CREATION_DATE[i]}</td>
			<td>{$DISK_USAGE[i]}</td>
			<td>
				<a href="domain_details.php?domain_id={$DOMAIN_ID[i]}" title="{$TR_DETAILS}" class="icon i_identity"></a>
				{if !isset($EDIT_OPTION)}
				<a href="domain_edit.php?edit_id={$DOMAIN_ID[i]}" title="{$TR_EDIT_DOMAIN}" class="icon i_domain" ></a>
				<a href="user_edit.php?edit_id={$USER_ID[i]}" title="{$TR_EDIT_USER}" class="icon i_edit"></a>
				{/if}
				<a href="domain_statistics.php?month={$VL_MONTH}&amp;year={$VL_YEAR}&amp;domain_id={$DOMAIN_ID[i]}" title="{$TR_STAT}" class="icon i_stats"></a>
				<a href="change_user_interface.php?to_id={$USER_ID[i]}" title="{$TR_CHANGE_USER_INTERFACE}" class="icon i_details"></a>
				<a href="domain_delete.php?domain_id={$DOMAIN_ID[i]}" title="{$TR_DELETE}" class="icon i_delete"></a>
			</td>
		</tr>
		{if isset($ALIAS_DOMAIN)}
		{if isset($ALIAS_DOMAIN[i])}
		{section name=alias loop=$ALIAS_DOMAIN[i]}
		<tr>
			<td style="width:50px">&nbsp;</td>
			<td colspan="4"><a href="http://www.{$ALIAS_DOMAIN[i][alias]}/" title="{$ALIAS_DOMAIN[i][alias]}" class="icon i_goto">{$ALIAS_DOMAIN[i][alias]}</a></td>
		</tr>
		{/section}
		{/if}
		{/if}
		{/section}
	</tbody>
</table>
{/if}
<div class="paginator">
	{if !isset($SCROLL_NEXT_GRAY)}
	<span class="icon i_next_gray">&nbsp;</span>
	{/if}
	{if !isset($SCROLL_NEXT)}
	<a class="icon i_next" href="users.php?psi={$NEXT_PSI}" title="next">next</a>
	{/if}
	{if !isset($SCROLL_PREV_GRAY)}
	<span class="icon i_prev_gray">&nbsp;</span>
	{/if}
	{if !isset($SCROLL_PREV)}
	<a class="icon i_prev" href="users.php?psi={$PREV_PSI}" title="previous">previous</a>
	{/if}
</div>
{/block}