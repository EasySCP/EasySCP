{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_MENU_OVERVIEW}{/block}

{block name=BREADCRUMP}
<li><a href="/reseller/index.php">{$TR_MENU_GENERAL_INFORMATION}</a></li>
<li><a>{$TR_MENU_OVERVIEW}</a></li>
{/block}

{block name=BODY}
{if isset($TR_NEW_MSGS)}
<div class="{$NEW_MSG_TYPE}">{$TR_NEW_MSGS}</div>
{/if}
<h2 class="general"><span>{$GENERAL_INFO}</span></h2>
<table>
	<tr>
		<td>{$ACCOUNT_NAME}</td>
		<td>{$RESELLER_NAME}</td>
	</tr>
	<tr>
		<td>{$DOMAINS}</td>
		<td>{$DMN_MSG}</td>
	</tr>
	<tr>
		<td>{$SUBDOMAINS}</td>
		<td>{$SUB_MSG}</td>
	</tr>
	<tr>
		<td>{$ALIASES}</td>
		<td>{$ALS_MSG}</td>
	</tr>
	<tr>
		<td>{$MAIL_ACCOUNTS}</td>
		<td>{$MAIL_MSG}</td>
	</tr>
	<tr>
		<td>{$TR_FTP_ACCOUNTS}</td>
		<td>{$FTP_MSG}</td>
	</tr>
	<tr>
		<td>{$SQL_DATABASES}</td>
		<td>{$SQL_DB_MSG}</td>
	</tr>
	<tr>
		<td>{$SQL_USERS}</td>
		<td>{$SQL_USER_MSG}</td>
	</tr>
	<tr>
		<td>{$TRAFFIC}</td>
		<td>{$TRAFF_MSG}</td>
	</tr>
	<tr>
		<td>{$DISK}</td>
		<td>{$DISK_MSG}</td>
	</tr>
</table>
{if isset($TR_TRAFFIC_WARNING)}
<div class="warning">{$TR_TRAFFIC_WARNING}</div>
{/if}
<h2 class="traffic"><span>{$TR_TRAFFIC_USAGE}</span></h2>
{$TRAFFIC_USAGE_DATA}
<div class="graph"><span style="width:{$TRAFFIC_PERCENT}%">&nbsp;</span></div>
{if isset($TR_DISK_WARNING)}
<div class="warning">{$TR_DISK_WARNING}</div>
{/if}
<h2 class="diskusage"><span>{$TR_DISK_USAGE}</span></h2>
{$DISK_USAGE_DATA}
<div class="graph"><span style="width:{$DISK_PERCENT}%">&nbsp;</span></div>
{/block}