{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_SERVER_STATUS}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/index.php">{$TR_MENU_GENERAL_INFORMATION}</a></li>
<li><a>{$TR_SERVER_STATUS}</a></li>
{/block}

{block name=BODY}
<h2 class="serverstatus"><span>{$TR_SERVER_STATUS}</span></h2>
<table>
	<tr>
		<th>{$TR_HOST}</th>
		<th>{$TR_SERVICE}</th>
		<th>{$TR_STATUS}</th>
	</tr>
	{section name=i loop=$HOST}
	<tr>
		<td class="{$CLASS[i]}">{$HOST[i]} (Port {$PORT[i]})</td>
		<td class="{$CLASS[i]}">{$SERVICE[i]}</td>
		<td class="{$CLASS[i]}">{$STATUS[i]}</td>
	</tr>
	{/section}
</table>
{/block}