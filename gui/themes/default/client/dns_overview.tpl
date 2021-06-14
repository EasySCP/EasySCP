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

{block name=CONTENT_HEADER}{$TR_DNS}{/block}

{block name=BREADCRUMP}
	<li><a href="/client/domains_manage.php">{$TR_MENU_MANAGE_DOMAINS}</a></li>
	<li><a>{$TR_DNS}</a></li>
{/block}

{block name=BODY}
<h2 class="domains">{$TR_DNS}</h2>
{if isset($ALS_MSG)}
<div class="{$ALS_MSG_TYPE}">{$ALS_MSG}</div>
{/if}
<form method="post">
	<input type="hidden" name="select_domain" value="true" />
	<table>
		<tr>
			<td><b>Domain:</b></td>
			<td>
				<select name="domain_id">
					{foreach item=d from=$D_USER_DOMAINS}
						<option value="{$d.alias}-{$d.domain_id}"{if $D_USER_DOMAIN_SELECTED==$d.alias-$d.domain_id} selected="selected"{/if}>{$d.domain_name}</option>
					{/foreach}
				</select>
			</td>
			<td>
				<input type="submit" value="{$TR_SELECT}" />
			</td>
		</tr>
	</table>
</form>
<p><a href="/client/dns_add.php">{$TR_DNS_ADD}</a></p>
<table class="tablesorter">
	<thead>
		<tr>
			<th>{$TR_DOMAIN_NAME}</th>
			<th>{$TR_DNS_NAME}</th>
			<th>{$TR_DNS_TYPE}</th>
			<th style="max-width:500px;word-break:break-all">{$TR_DNS_DATA}</th>
			<th style="width:200px">{$TR_DNS_ACTION}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$D_DNS_ZONE item=r}
		<tr>
			<td><span class="icon i_domain_icon">{$r.DNS_DOMAIN}</span></td>
			<td>{$r.DNS_NAME}</td>
			<td>{$r.DNS_TYPE}</td>
			<td style="max-width:500px;word-break:break-all">{$r.DNS_DATA}</td>
			<td>
				{if $r.DNS_RW==true}
				<a href="{$r.DNS_ACTION_SCRIPT_EDIT}" title="{$r.DNS_ACTION_EDIT}" class="icon i_edit"></a>
				<a href="#" onclick="action_delete('{$r.DNS_ACTION_SCRIPT_DELETE}', '{$r.DNS_TYPE_RECORD}')" title="{$r.DNS_ACTION_DELETE}" class="icon i_delete"></a>
				{/if}
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>
{/block}