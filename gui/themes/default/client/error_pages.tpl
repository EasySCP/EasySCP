{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_MENU_ERROR_PAGES}{/block}

{block name=BREADCRUMP}
<li><a href="/client/webtools.php">{$TR_MENU_WEBTOOLS}</a></li>
<li><a>{$TR_MENU_ERROR_PAGES}</a></li>
{/block}

{block name=BODY}
<h2 class="errors"><span>{$TR_ERROR_PAGES}</span></h2>
<table>
	<tr>
		<td><span class="icon_big i_error401">{$TR_ERROR_401}</span></td>
		<td><a href="/client/error_edit.php?eid=401" title="{$TR_EDIT}" class="icon i_edit"></a></td>
		<td><a href="{$DOMAIN}/errors/401.html" class="icon i_preview">{$TR_VIEW}</a></td>
	</tr>
	<tr>
		<td><span class="icon_big i_error403">{$TR_ERROR_403}</span></td>
		<td><a href="/client/error_edit.php?eid=403" title="{$TR_EDIT}" class="icon i_edit"></a></td>
		<td><a href="{$DOMAIN}/errors/403.html" class="icon i_preview">{$TR_VIEW}</a></td>
	</tr>
	<tr>
		<td><span class="icon_big i_error404">{$TR_ERROR_404}</span></td>
		<td><a href="/client/error_edit.php?eid=404" title="{$TR_EDIT}" class="icon i_edit"></a></td>
		<td><a href="{$DOMAIN}/errors/404.html" class="icon i_preview">{$TR_VIEW}</a></td>
	</tr>
	<tr>
		<td><span class="icon_big i_error500">{$TR_ERROR_500}</span></td>
		<td><a href="/client/error_edit.php?eid=500" title="{$TR_EDIT}" class="icon i_edit"></a></td>
		<td><a href="{$DOMAIN}/errors/500.html" class="icon i_preview">{$TR_VIEW}</a></td>
	</tr>
	<tr>
		<td><span class="icon_big i_error503">{$TR_ERROR_503}</span></td>
		<td><a href="/client/error_edit.php?eid=503" title="{$TR_EDIT}" class="icon i_edit"></a></td>
		<td><a href="{$DOMAIN}/errors/503.html" class="icon i_preview">{$TR_VIEW}</a></td>
	</tr>
</table>
{/block}