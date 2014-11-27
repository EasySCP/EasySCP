{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function action_delete(url, subject) {
		if (!confirm(sprintf("{$TR_MESSAGE_DELETE}", subject)))
			return false;
		location = url;
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_MENU_OVERVIEW}{/block}

{block name=BREADCRUMP}
<li><a href="/client/domains_manage.php">{$TR_MENU_MANAGE_DOMAINS}</a></li>
<li><a>{$TR_MENU_OVERVIEW}</a></li>
{/block}

{block name=BODY}
{if isset($ALS_MSG)}
	<div class="{$ALS_MSG_TYPE}">{$ALS_MSG}</div>
{/if}
{if isset($ALS_NAME)}
<h2 class="domains"><span>{$TR_DOMAIN_ALIASES}</span></h2>
<table class="tablesorter">
	<thead>
		<tr>
			<th>{$TR_ALS_NAME}</th>
			<th style="width:200px">{$TR_ALS_MOUNT}</th>
			<th style="width:200px">{$TR_ALS_FORWARD}</th>
			<th style="width:100px">{$TR_ALS_STATUS}</th>
			<th style="width:200px">{$TR_ALS_ACTION}</th>
		</tr>
	</thead>
	<tbody>
		{section name=i loop=$ALS_NAME}
		<tr>
			<td><a href="http://{$ALS_NAME[i]}/" class="icon i_domain" title="{$ALS_NAME[i]}">{$ALS_NAME[i]}</a></td>
			<td>{$ALS_MOUNT[i]}</td>
			<td>{$ALS_FORWARD[i]}</td>
			<td>{$ALS_STATUS[i]}</td>
			<td>
				<a href="{$ALS_EDIT_LINK[i]}" title="{$ALS_EDIT[i]}" class="icon i_edit"></a>
				<a href="#" onclick="action_delete('{$ALS_ACTION_SCRIPT[i]}', '{$ALS_NAME[i]}')" title="{$ALS_ACTION[i]}" class="icon i_delete"></a>
			</td>
		</tr>
		{/section}
	</tbody>
</table>
{/if}
{if isset($SUB_MSG)}
<div class="{$SUB_MSG_TYPE}">{$SUB_MSG}</div>
{/if}
{if isset($SUB_NAME)}
<h2 class="domains"><span>{$TR_SUBDOMAINS}</span></h2>
<table class="tablesorter">
	<thead>
		<tr>
			<th>{$TR_SUB_NAME}</th>
			<th style="width:200px">{$TR_SUB_MOUNT}</th>
			<th style="width:200px">{$TR_SUB_FORWARD}</th>
			<th style="width:100px">{$TR_SUB_STATUS}</th>
			<th style="width:200px">{$TR_SUB_ACTION}</th>
		</tr>
	</thead>
	<tbody>
		{section name=i loop=$SUB_NAME}
		<tr>
			<td><a href="http://{$SUB_NAME[i]}.{$SUB_ALIAS_NAME[i]}/" title="{$SUB_NAME[i]}.{$SUB_ALIAS_NAME[i]}" class="icon i_domain">{$SUB_NAME[i]}.{$SUB_ALIAS_NAME[i]}</a></td>
			<td>{$SUB_MOUNT[i]}</td>
			<td>{$SUB_FORWARD[i]}</td>
			<td>{$SUB_STATUS[i]}</td>
			<td>
				<a href="{$SUB_EDIT_LINK[i]}" title="{$SUB_EDIT[i]}" class="icon i_edit"></a>
				<a href="#" onclick="action_delete('{$SUB_ACTION_SCRIPT[i]}', '{$SUB_NAME[i]}')" title="{$SUB_ACTION[i]}" class="icon i_delete"></a>
			</td>
		</tr>
		{/section}
	</tbody>
</table>
{/if}
{if isset($DNS_MSG)}
<div class="{$DNS_MSG_TYPE}">{$DNS_MSG}</div>
{/if}
{/block}