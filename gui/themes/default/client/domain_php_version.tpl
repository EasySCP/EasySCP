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
<form action="/client/domain_php_version.php" method="post" id="client_php_version">
	<fieldset>
		<h2 class="domains"><span>{$TR_DOMAIN}</span></h2>
		{if isset($DMN_MSG)}
			<div class="{$DMN_MSG_TYPE}">{$DMN_MSG}</div>
		{/if}
		{if isset($DMN_NAME)}
		<table class="tablesorter">
			<thead>
				<tr>
					<th>{$TR_DMN_NAME}</th>
					<th style="width:200px">{$TR_DMN_PHP_VERSION}</th>
					<th style="width:200px">{$TR_DMN_STATUS}</th>
					<th style="width:200px">{$TR_DMN_ACTION}</th>
				</tr>
			</thead>
			<tbody>
				{section name=i loop=$DMN_NAME}
				<tr>
					<td><a href="http://{$DMN_NAME[i]}/" class="icon i_domain" title="{$DMN_NAME[i]}">{$DMN_NAME[i]}</a></td>
					<td>
						<select name="dmn_php_version[]" id="dmn_php_version{$DMN_PHP_NUM[i]}">
							{section name=n loop=$DMN_PHP_VERSION{$DMN_PHP_NUM[i]}}
							<option value="{$DMN_PHP_VERSION{$DMN_PHP_NUM[i]}[n]['PHP_VERSION_VALUE']}" {$DMN_PHP_VERSION{$DMN_PHP_NUM[i]}[n]['PHP_VERSION_SELECTED']}>{$DMN_PHP_VERSION{$DMN_PHP_NUM[i]}[n]['PHP_VERSION_NAME']}</option>
							{/section}
						</select> 
					</td>
					<td>{$DMN_STATUS[i]}</td>
					<td><input type="hidden" name="dmn_php_num[]" id="dmn_php_num{$DMN_PHP_NUM[i]}" value="{$DMN_PHP_NUM[i]}" /></td>
				</tr>
				{/section}
			</tbody>
		</table>
		{/if}
		<h2 class="domains"><span>{$TR_SUBDOMAINS}</span></h2>
		{if isset($SUB_MSG)}
		<div class="{$SUB_MSG_TYPE}">{$SUB_MSG}</div>
		{/if}
		{if isset($SUB_NAME)}
		<table class="tablesorter">
			<thead>
				<tr>
					<th>{$TR_SUB_NAME}</th>
					<th style="width:200px">{$TR_SUB_PHP_VERSION}</th>
					<th style="width:200px">{$TR_SUB_STATUS}</th>
					<th style="width:200px">{$TR_SUB_ACTION}</th>
				</tr>
			</thead>
			<tbody>
				{section name=i loop=$SUB_NAME}
				<tr>
					<td><a href="http://{$SUB_NAME[i]}.{$SUB_ALIAS_NAME[i]}/" title="{$SUB_NAME[i]}.{$SUB_ALIAS_NAME[i]}" class="icon i_domain">{$SUB_NAME[i]}.{$SUB_ALIAS_NAME[i]}</a></td>
					<td>
						<select name="sub_php_version[]" id="sub_php_version{$SUB_PHP_NUM[i]}">
							{section name=n loop=$SUB_PHP_VERSION{$SUB_PHP_NUM[i]}}
							<option value="{$SUB_PHP_VERSION{$SUB_PHP_NUM[i]}[n]['PHP_VERSION_VALUE']}" {$SUB_PHP_VERSION{$SUB_PHP_NUM[i]}[n]['PHP_VERSION_SELECTED']}>{$SUB_PHP_VERSION{$SUB_PHP_NUM[i]}[n]['PHP_VERSION_NAME']}</option>
							{/section}
						</select> 
					</td>
					<td>{$SUB_STATUS[i]}</td>
					<td><input type="hidden" name="sub_php_num[]" id="sub_php_num{$SUB_PHP_NUM[i]}" value="{$SUB_PHP_NUM[i]}" /></td>
				</tr>
				{/section}
			</tbody>
		</table>
		{/if}
		<div class="buttons">
			<input type="hidden" name="uaction" value="update" />
			<input type="submit" name="Submit" value="{$VAL_FOR_SUBMIT_ON_UPDATE}" />
			<input type="reset" name="Reset" value="{$VAL_FOR_SUBMIT_ON_RESET}" />
		</div>
	</fieldset>
</form>
{if isset($DNS_MSG)}
<div class="{$DNS_MSG_TYPE}">{$DNS_MSG}</div>
{/if}
{/block}