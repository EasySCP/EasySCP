{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_ADMIN_LOG}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/index.php">{$TR_MENU_GENERAL_INFORMATION}</a></li>
<li><a>{$TR_ADMIN_LOG}</a></li>
{/block}

{block name=BODY}
<h2 class="adminlog"><span>{$TR_ADMIN_LOG}</span></h2>
<table>
	<tr>
		<th>{$TR_DATE}</th>
		<th>{$TR_MESSAGE}</th>
	</tr>
	{section name=i loop=$ADM_MESSAGE}
	<tr>
		<td>{$DATE[i]}</td>
		<td>{$ADM_MESSAGE[i]}</td>
	</tr>
	{/section}
</table>
<div class="paginator">
	{if !isset($SCROLL_NEXT_GRAY)}
	<span class="icon i_next_gray">&nbsp;</span>
	{/if}
	{if !isset($SCROLL_NEXT)}
	<a href="/admin/admin_log.php?psi={$NEXT_PSI}" title="next" class="icon i_next">next</a>
	{/if}
	{if !isset($SCROLL_PREV_GRAY)}
	<span class="icon i_prev_gray">&nbsp;</span>
	{/if}
	{if !isset($SCROLL_PREV)}
	<a href="/admin/admin_log.php?psi={$PREV_PSI}" title="previous" class="icon i_prev">previous</a>
	{/if}
</div>
<form action="/admin/admin_log.php" method="post" id="admin_log" style="margin-top: 40px;">
	<table>
		<tr>
			<td><label for="uaction_clear">{$TR_CLEAR_LOG_MESSAGE}</label></td>
			<td>
				<select name="uaction_clear" id="uaction_clear">
					<option value="0" selected="selected">{$TR_CLEAR_LOG_EVERYTHING}</option>
					<option value="2">{$TR_CLEAR_LOG_LAST2}</option>
					<option value="4">{$TR_CLEAR_LOG_LAST4}</option>
					<option value="12">{$TR_CLEAR_LOG_LAST12}</option>
					<option value="26">{$TR_CLEAR_LOG_LAST26}</option>
					<option value="52">{$TR_CLEAR_LOG_LAST52}</option>
				</select>
			</td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="uaction" value="clear_log" />
		<input type="submit" name="Submit" value="{$TR_CLEAR_LOG}" />
	</div>
</form>
{/block}