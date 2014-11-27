{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function(){
		// Tooltips - begin
		$('#dmn_help').EasySCPtooltips({ msg:"{$TR_DMN_HELP}"});
		// Tooltips - end
	});

	function makeUser() {
		var dname = document.getElementById('reseller_alias_add').elements['ndomain_name'].value;
		dname = dname.toLowerCase();
		document.getElementById('reseller_alias_add').elements['ndomain_mpoint'].value = "/" + dname;
	}

	function setForwardReadonly(obj){
		if(obj.value == 1) {
			document.forms[0].elements['forward'].readOnly = false;
			document.forms[0].elements['forward_prefix'].disabled = false;
		} else {
			document.forms[0].elements['forward'].readOnly = true;
			document.forms[0].elements['forward'].value = '';
			document.forms[0].elements['forward_prefix'].disabled = true;
		}
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_ADD_ALIAS}{/block}

{block name=BREADCRUMP}
<li><a href="/reseller/users.php">{$TR_MENU_OVERVIEW}</a></li>
<li><a href="/reseller/alias.php">{$TR_MENU_DOMAIN_ALIAS}</a></li>
<li><a>{$TR_ADD_ALIAS}</a></li>
{/block}

{block name=BODY}
<h2 class="domains"><span>{$TR_MANAGE_DOMAIN_ALIAS}</span></h2>
<form action="/reseller/alias_add.php" method="post" id="reseller_alias_add">
	<fieldset>
		<legend>{$TR_ADD_ALIAS}</legend>
		<table>
			<tr>
				<td>{$TR_DOMAIN_ACCOUNT}</td>
				<td colspan="2">
					<select name="usraccounts" id="usraccounts" size="5">
						{section name=i loop=$USER}
						<option value="{$USER[i]}" {$SELECTED[i]}>{$USER_DOMAIN_ACCOUNT[i]}</option>
						{/section}
					</select>
				</td>
			</tr>
			<tr>
				<td>{$TR_DOMAIN_NAME} <span id="dmn_help" class="icon i_help">&nbsp;</span></td>
				<td><input type="text" name="ndomain_name" id="ndomain_name" value="{$DOMAIN}" onblur="makeUser();" /></td>
			</tr>
<!--				<tr>
				<td>{$TR_MOUNT_POINT}</td>
				<td><input type="text" name="ndomain_mpoint" id="ndomain_mpoint" value='{$MP}' /></td>
		</tr>-->
			<tr>
				<td>{$TR_ENABLE_FWD}</td>
				<td>
					<input type="radio" name="status" id="status_EN" value="1" onchange="setForwardReadonly(this);" {$CHECK_EN} /> {$TR_ENABLE}<br />
					<input type="radio" name="status" id="status_DIS" value="0" onchange="setForwardReadonly(this);" {$CHECK_DIS} /> {$TR_DISABLE}</td>
			</tr>
			<tr>
				<td>{$TR_FORWARD}</td>
				<td>
					<select name="forward_prefix" style="vertical-align:middle" {$DISABLE_FORWARD}>
						<option value="{$TR_PREFIX_HTTP}" {$HTTP_YES}>{$TR_PREFIX_HTTP}</option>
						<option value="{$TR_PREFIX_HTTPS}" {$HTTPS_YES}>{$TR_PREFIX_HTTPS}</option>
						<option value="{$TR_PREFIX_FTP}" {$FTP_YES}>{$TR_PREFIX_FTP}</option>
					</select>
					<input type="text" name="forward" id="forward" value="{$FORWARD}" {$READONLY_FORWARD} />
				</td>
			</tr>
		</table>
	</fieldset>
	<div class="buttons">
		<input type="hidden" name="uaction" value="add_alias" />
		<input type="submit" name="Submit" value="{$TR_ADD}" />
	</div>
</form>
{/block}