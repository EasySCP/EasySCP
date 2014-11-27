{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function setForwardReadonly(obj){
		if(obj.value == 1) {
			document.getElementById('client_subdomain_add').elements['forward'].readOnly = false;
			document.getElementById('client_subdomain_add').elements['forward_prefix'].disabled = false;
		} else {
			document.getElementById('client_subdomain_add').elements['forward'].readOnly = true;
			document.getElementById('client_subdomain_add').elements['forward'].value = '';
			document.getElementById('client_subdomain_add').elements['forward_prefix'].disabled = true;
		}
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_EDIT_SUBDOMAIN}{/block}

{block name=BREADCRUMP}
<li><a href="/client/domains_manage.php">{$TR_MENU_MANAGE_DOMAINS}</a></li>
<li><a>{$TR_EDIT_SUBDOMAIN}</a></li>
{/block}

{block name=BODY}
<h2 class="domains"><span>{$TR_EDIT_SUBDOMAIN}</span></h2>
<form action="/client/subdomain_edit.php?edit_id={$ID}" method="post" id="client_subdomain_edit">
	<table>
		<tr>
			<td>{$TR_SUBDOMAIN_NAME}</td>
			<td>{$SUBDOMAIN_NAME}</td>
		</tr>
		<tr>
			<td>{$TR_ENABLE_FWD}</td>
			<td>
				<input type="radio" name="status" id="status1" {$CHECK_EN} value="1" onchange="setForwardReadonly(this);" /> <label for="status1">{$TR_ENABLE}</label><br />
				<input type="radio" name="status" id="status2" {$CHECK_DIS} value="0" onchange="setForwardReadonly(this);" /> <label for="status2">{$TR_DISABLE}</label>
			</td>
		</tr>
		<tr>
			<td>{$TR_FORWARD}</td>
			<td>
				<select name="forward_prefix" style="vertical-align:middle"{$DISABLE_FORWARD}>
					<option value="{$TR_PREFIX_HTTP}" {$HTTP_YES}>{$TR_PREFIX_HTTP}</option>
					<option value="{$TR_PREFIX_HTTPS}" {$HTTPS_YES}>{$TR_PREFIX_HTTPS}</option>
					<option value="{$TR_PREFIX_FTP}" {$FTP_YES}>{$TR_PREFIX_FTP}</option>
				</select>
				<input name="forward" type="text" id="forward" value="{$FORWARD}" />
			</td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="dmn_type" value="{$DMN_TYPE}" />
		<input type="hidden" name="dmn_id" value="{$DMN_ID}" />
		<input type="hidden" name="uaction" value="modify" />
		<input name="Submit" type="submit"  value="{$TR_MODIFY}" />
	</div>
</form>
{/block}