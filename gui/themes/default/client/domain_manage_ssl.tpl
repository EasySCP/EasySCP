{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_MENU_OVERVIEW}{/block}

{block name=BREADCRUMP}
<li><a href="/client/domains_manage.php">{$TR_MENU_MANAGE_DOMAINS}</a></li>
<li><a>{$TR_MENU_OVERVIEW}</a></li>
{/block}

{block name=BODY}
<h2 class="domains"><span>{$TR_SSL_CONFIG_TITLE}</span></h2>
<form action="/client/domain_manage_ssl.php" method="post" id="client_settings_ssl">
	<fieldset>
	<table>
		<tr>
			<td>{$TR_SSL_DOMAIN}</td>
			<td>
				<select name="ssl_domain" id="ssldomain" onchange='this.form.submit()'>
					{section name=i loop=$DOMAIN_NAME}
						<option value="{$DOMAIN_VALUE[i]}" {$DOMAIN_SELECTED[i]}>{$DOMAIN_NAME[i]}</option>
					{/section}
				</select>
			</td>
		</tr>
		<tr>
			<td>{$TR_SSL_ENABLED}</td>
			<td>
				<select name="ssl_status" id="sslstatus" onchange='showHideSSL(this)'>
					<option value="10" {$SSL_SELECTED_DISABLED}>{$TR_SSL_STATUS_DISABLED}</option>
					<option value="11" {$SSL_SELECTED_SSLONLY}>{$TR_SSL_STATUS_SSLONLY}</option>
					<option value="12" {$SSL_SELECTED_BOTH}>{$TR_SSL_STATUS_BOTH}</option>
					<option value="13" {$SSL_SELECTED_SSLONLY_LETSENCRYPT}>{$TR_SSL_STATUS_SSLONLY_LETSENCRYPT}</option>
					<option value="14" {$SSL_SELECTED_BOTH_LETSENCRYPT}>{$TR_SSL_STATUS_BOTH_LETSENCRYPT}</option>
				</select>
				</td>
		</tr>
			<tr>
				<td>{$TR_SSL_STATE}</td>
				<td>{$SSL_STATE}</td>
			</tr>
		<tr>
			<td>{$TR_SSL_VALID}</td>
			<td>{$SSL_VALID}</td>
		</tr>
		<tr>
			<td>{$TR_SSL_CERTIFICATE}</td>
			<td><textarea name="ssl_cert" id="sslcertificate" cols="80" rows="15" {$SSL_TEXT_FIELD}>{$SSL_CERTIFICATE}</textarea></td>
		</tr>
		<tr>
			<td>{$TR_SSL_KEY}</td>
			<td><textarea name="ssl_key" id="sslkey" cols="80" rows="15" {$SSL_TEXT_FIELD}>{$SSL_KEY}</textarea></td>
		</tr>
		<tr>
			<td>{$TR_SSL_CACERT}</td>
			<td><textarea name="ssl_cacert" id="ssl_cacert" cols="80" rows="15" {$SSL_TEXT_FIELD}>{$SSL_CACERT}</textarea></td>
		</tr>
	</table>
		<div class="buttons">
			<input type="hidden" name="uaction" value="apply" />
			<input type="submit" name="Submit" value="{$TR_APPLY_CHANGES}" />
		</div>
	</fieldset>
</form>
{/block}