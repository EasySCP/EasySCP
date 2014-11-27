{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_DOMAIN_STATISTICS}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/server_statistic.php">{$TR_MENU_STATISTICS}</a></li>
<li><a href="/admin/reseller_statistics.php">{$TR_RESELLER_STATISTICS}</a></li>
<li><a href="/admin/reseller_user_statistics.php?rid={$RESELLER_ID}&amp;name={$RESELLER_NAME}&amp;month={$MONTH}&amp;year={$YEAR}">{$TR_RESELLER_USER_STATISTICS}</a></li>
<li><a>{$TR_DOMAIN_STATISTICS}</a></li>
{/block}

{block name=BODY}
<h2 class="stats"><span>{$TR_DOMAIN_STATISTICS}</span></h2>
<form action="/admin/domain_statistics.php" method="post" id="admin_domain_statistics">
	<fieldset>
		<label for="month">{$TR_MONTH}</label>
		<select name="month" id="month">
			{section name=i loop=$MONTH_VALUE}
			<option {$MONTH_SELECTED[i]}>{$MONTH_VALUE[i]}</option>
			{/section}
		</select>
		<label for="month">{$TR_YEAR}</label>
		<select name="year" id="year">
			{section name=i loop=$YEAR_VALUE}
			<option {$YEAR_SELECTED[i]}>{$YEAR_VALUE[i]}</option>
			{/section}
		</select>
		<input type="hidden" name="uaction" value="show_traff" />
		<input type="submit" name="Submit"  value="{$TR_SHOW}" />
	</fieldset>
</form>
<table>
	<thead>
		<tr>
			<th>{$TR_DAY}</th>
			<th>{$TR_WEB_TRAFFIC}</th>
			<th>{$TR_FTP_TRAFFIC}</th>
			<th>{$TR_SMTP_TRAFFIC}</th>
			<th>{$TR_POP3_TRAFFIC}</th>
			<th>{$TR_ALL_TRAFFIC}</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td>{$TR_ALL}</td>
			<td>{$ALL_WEB_TRAFFIC}</td>
			<td>{$ALL_FTP_TRAFFIC}</td>
			<td>{$ALL_SMTP_TRAFFIC}</td>
			<td>{$ALL_POP3_TRAFFIC}</td>
			<td>{$ALL_ALL_TRAFFIC}</td>
		</tr>
	</tfoot>
	<tbody>
		{section name=i loop=$DATE}
		<tr>
			<td>{$DATE[i]}</td>
			<td>{$WEB_TRAFFIC[i]}</td>
			<td>{$FTP_TRAFFIC[i]}</td>
			<td>{$SMTP_TRAFFIC[i]}</td>
			<td>{$POP3_TRAFFIC[i]}</td>
			<td>{$ALL_TRAFFIC[i]}</td>
		</tr>
		{/section}
	</tbody>
</table>
{/block}