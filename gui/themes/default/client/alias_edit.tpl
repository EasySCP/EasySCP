{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function setForwardReadonly(obj){
		if(obj.value == 1) {
			document.getElementById('client_alias_edit').elements['forward'].readOnly = false;
			document.getElementById('client_alias_edit').elements['forward_prefix'].disabled = false;
		} else {
			document.getElementById('client_alias_edit').elements['forward'].readOnly = true;
			document.getElementById('client_alias_edit').elements['forward'].value = '';
			document.getElementById('client_alias_edit').elements['forward_prefix'].disabled = true;
		}
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_EDIT_ALIAS}{/block}

{block name=BREADCRUMP}
<li><a href="/client/domains_manage.php">{$TR_MENU_MANAGE_DOMAINS}</a></li>
<li><a>{$TR_EDIT_ALIAS}</a></li>
{/block}

{block name=BODY}
<h2 class="domains"><span>{$TR_EDIT_ALIAS}</span></h2>
<form action="/client/alias_edit.php?edit_id={$ID}" method="post" id="client_alias_edit">
	<table>
		<tr>
			<td>{$TR_DOMAIN_IP}</td>
			<td>{$ALIAS_NAME}</td>
		</tr>
		<tr>
			<td>{$TR_DOMAIN_IP}</td>
			<td>{$DOMAIN_IP}</td>
		</tr>
		<tr>
			<td>{$TR_ENABLE_FWD}</td>
			<td>
				<input type="radio" name="status" id="status_EN" value="1" onchange="setForwardReadonly(this);" {$CHECK_EN} /> &nbsp; {$TR_ENABLE}<br />
				<input type="radio" name="status" id="status_DIS" value="0" onchange="setForwardReadonly(this);" {$CHECK_DIS} /> &nbsp;{$TR_DISABLE}
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
				<input type="text" name="forward" id="forward" value="{$FORWARD}" {$READONLY_FORWARD} />
			</td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="uaction" value="modify" />
		<input type="submit" name="Submit" value="{$TR_MODIFY}" />
	</div>
</form>
{/block}