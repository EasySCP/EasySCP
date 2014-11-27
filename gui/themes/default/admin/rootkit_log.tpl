{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_MENU_ROOTKIT_LOG}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/system_info.php">{$TR_MENU_SYSTEM_TOOLS}</a></li>
<li><a>{$TR_MENU_ROOTKIT_LOG}</a></li>
{/block}

{block name=BODY}
<h2 class="serverstatus"><span>{$TR_ROOTKIT_LOG}</span></h2>
{section name=i loop=$FILENAME}
<table>
	<tr>
		<th>{$FILENAME[i]}:</th>
	</tr>
	<tr>
		<td>{$LOG[i]}</td>
	</tr>
</table>
{/section}
{/block}