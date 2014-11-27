{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$IP_USAGE}{/block}

{block name=BREADCRUMP}
<li><a href="/reseller/user_statistics.php">{$TR_MENU_DOMAIN_STATISTICS}</a></li>
<li><a>{$IP_USAGE}</a></li>
{/block}

{block name=BODY}
<h2 class="ip"><span>{$IP_USAGE}</span></h2>
{section name=i loop=$IP}
<table>
	<tr>
		<th>{$IP[i]}</th>
	</tr>
	<tr>
		<td><b>{$RECORD_COUNT[i]}</b></td>
	</tr>
</table>
<br />
{if isset($DOMAIN_NAME[i])}
<table>
	<tr>
		<th>{$TR_DOMAIN_NAME}</th>
	</tr>
	{section name=domain loop=$DOMAIN_NAME[i]}
	<tr>
		<td>{$DOMAIN_NAME[i][domain]}</td>
	</tr>
	{/section}
</table>
<br /><br />
{/if}
{/section}
{/block}