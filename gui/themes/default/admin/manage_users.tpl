{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function action_status(url, dmn_name) {
		if (!confirm(sprintf("{$TR_MESSAGE_CHANGE_STATUS}", dmn_name)))
			return false;
		location = url;
	}
	function action_delete(url, dmn_name) {
		if (!confirm(sprintf("{$TR_MESSAGE_DELETE}", dmn_name)))
			return false;
		location = url;
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_MENU_OVERVIEW}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/manage_users.php">{$TR_MENU_MANAGE_USERS}</a></li>
<li><a>{$TR_MENU_OVERVIEW}</a></li>
{/block}

{block name=BODY}
<h2 class="users"><span>{$TR_ADMINISTRATORS}</span></h2>
{if isset($ADMIN_MESSAGE)}
<div class="{$MSG_TYPE}">{$ADMIN_MESSAGE}</div>
{/if}
<table class="tablesorter">
	<thead>
		<tr>
			<th style="width:50px">&nbsp;</th>
			<th>{$TR_ADMIN_USERNAME}</th>
			<th style="width:100px">{$TR_CREATED_ON}</th>
			<th style="width:200px">{$TR_ADMIN_CREATED_BY}</th>
			<th style="width:200px">{$TR_ADMIN_OPTIONS}</th>
		</tr>
	</thead>
	<tbody>
		{section name=i loop=$ADMIN_USERNAME}
		<tr>
			<td>&nbsp;</td>
			<td>{$ADMIN_USERNAME[i]}</td>
			<td>{$ADMIN_CREATED_ON[i]}</td>
			<td>{$ADMIN_CREATED_BY[i]}</td>
			<td>
				<a href="{$URL_EDIT_ADMIN[i]}" title="{$TR_EDIT}" class="icon i_edit"></a>
				{if $ADMIN_DELETE_SHOW[i]}
				<a href="#" onclick="action_delete('{$URL_DELETE_ADMIN[i]}', '{$ADMIN_USERNAME[i]}')" title="{$TR_DELETE}" class="icon i_delete"></a>
				{/if}
			</td>
		</tr>
		{/section}
	</tbody>
</table>
<h2 class="users"><span>{$TR_RESELLERS}</span></h2>
{if isset($RSL_MESSAGE)}
<div class="info">{$RSL_MESSAGE}</div>
{/if}
{if isset($RSL_USERNAME)}
<table class="tablesorter">
	<thead>
		<tr>
			<th style="width:50px">&nbsp;</th>
			<th>{$TR_RSL_USERNAME}</th>
			<th style="width:100px">{$TR_CREATED_ON}</th>
			<th style="width:200px">{$TR_RSL_CREATED_BY}</th>
			<th style="width:200px">{$TR_RSL_OPTIONS}</th>
		</tr>
	</thead>
	<tbody>
		{section name=i loop=$RSL_USERNAME}
		<tr>
			<td>&nbsp;</td>
			<td>{$RSL_USERNAME[i]}</td>
			<td>{$RESELLER_CREATED_ON[i]}</td>
			<td>{$RSL_CREATED_BY[i]}</td>
			<td>
				<a href="{$URL_CHANGE_INTERFACE_RSL[i]}" title="{$TR_CHANGE_USER_INTERFACE}" class="icon i_details"></a>
				<a href="{$URL_EDIT_RSL[i]}" title="{$TR_EDIT}" class="icon i_edit"></a>
				<a href="#" onclick="action_delete('{$URL_DELETE_RSL[i]}', '{$RSL_USERNAME[i]}')" title="{$TR_DELETE}" class="icon i_delete"></a>
			</td>
		</tr>
		{/section}
	</tbody>
</table>
{/if}
<h2 class="users"><span>{$TR_USERS}</span></h2>
<form action="manage_users.php" method="post" id="admin_manage_users">
	<p>
		<a href="#" onclick="return sbmt_details(document.forms[0],'{$SHOW_DETAILS}');" title="{$TR_VIEW_DETAILS}" class="icon i_show_alias">{$TR_VIEW_DETAILS}</a>
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
	</p>
</form>
{if isset($USR_MESSAGE)}
<div class="info">{$USR_MESSAGE}</div>
{/if}
{if isset($USR_USERNAME)}
<table class="tablesorter">
	<thead>
		<tr>
			<th style="width:50px">{$TR_USER_STATUS}</th>
			<th>{$TR_USR_USERNAME}</th>
			<th style="width:100px">{$TR_CREATED_ON}</th>
			<th style="width:200px">{$TR_USR_CREATED_BY}</th>
			<th style="width:200px">{$TR_USR_OPTIONS}</th>
		</tr>
	</thead>
	<tbody>
		{section name=i loop=$USR_USERNAME}
		<tr>
			<td><a href="#" onclick="action_status('{$URL_CHANGE_STATUS[i]}', '{$USR_USERNAME[i]}')" title="{$STATUS_ICON[i]}" class="icon i_{$STATUS_ICON[i]}"></a></td>
			<td><a href="http://www.{$USR_USERNAME[i]}/" title="{$USR_USERNAME[i]}" class="icon i_goto external">{$USR_USERNAME[i]}</a></td>
			<td>{$USER_CREATED_ON[i]}</td>
			<td>{$USR_CREATED_BY[i]}</td>
			<td>
				<a href="domain_details.php?domain_id={$DOMAIN_ID[i]}" title="{$TR_DETAILS}" class="icon i_identity"></a>
				<a href="{$URL_CHANGE_INTERFACE[i]}" title="{$TR_CHANGE_USER_INTERFACE}" class="icon i_details"></a>
				<a href="{$URL_EDIT_USR[i]}" title="{$TR_EDIT_USR}" class="icon i_edit"></a>
				{if isset($EDIT_OPTION)}
				<a href="domain_edit.php?edit_id={$DOMAIN_ID[i]}" title="{$TR_EDIT_DOMAIN}" class="icon i_domain"></a>
				{/if}
				<a href="#" onclick="action_delete('{$URL_DELETE_USR[i]}', '{$USR_USERNAME[i]}')" title="{$TR_DELETE}" class="icon i_delete"></a>
			</td>
		</tr>
		{if isset($ALIAS_DOMAIN[i])}
		{section name=alias loop=$ALIAS_DOMAIN[i]}
		<tr>
			<td style="width:50px">&nbsp;</td>
			<td colspan="4"><a href="http://www.{$ALIAS_DOMAIN[i][alias]}/" title="{$ALIAS_DOMAIN[i][alias]}" class="icon i_goto">{$ALIAS_DOMAIN[i][alias]}</a></td>
		</tr>
		{/section}
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
	<a href="manage_users.php?psi={$NEXT_PSI}" title="next" class="icon i_next">next</a>
	{/if}
	{if !isset($SCROLL_PREV_GRAY)}
	<span class="icon i_prev_gray">&nbsp;</span>
	{/if}
	{if !isset($SCROLL_PREV)}
	<a href="manage_users.php?psi={$PREV_PSI}" title="previous" class="icon i_prev">previous</a>
	{/if}
</div>
{/block}