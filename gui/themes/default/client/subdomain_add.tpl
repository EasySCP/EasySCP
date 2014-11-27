{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function(){
		load();

		// Tooltips - begin
		$('#fwd_help').EasySCPtooltips({ msg:"{$TR_DMN_HELP}"});
		$('#mnt_help').EasySCPtooltips({ msg:"{$TR_MNT_POINT_HELP}"});
		$('#subdmn_help').EasySCPtooltips({ msg:"{$TR_SUBDMN_ASSIGN_HELP}"});
		// Tooltips - end
		//disable subdomain selection by default
		document.getElementById('client_subdomain_add').elements['subdmn_id'].readOnly = true;
		document.getElementById('client_subdomain_add').elements['subdmn_id'].disabled = true;
	});

	function makeUser() {
		var subname  = document.getElementById('client_subdomain_add').elements['subdomain_name'].value;
		subname = subname.toLowerCase();
		document.getElementById('client_subdomain_add').elements['subdomain_mnt_pt'].value = "/" + subname;
	}

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

	function load(){
		var element = document.getElementById('dmn_type');
		toggleSubDomainMount(element);
	}

	function toggleSubDomainMount(obj){
		if(obj.value == 'dmn') {
			var subname  = document.getElementById('client_subdomain_add').elements['subdomain_name'].value;
			subname = subname.toLowerCase();
			document.getElementById('client_subdomain_add').elements['subdmn_id'].readOnly = true;
			document.getElementById('client_subdomain_add').elements['subdmn_id'].disabled = true;
			document.getElementById('client_subdomain_add').elements['subdomain_mnt_pt'].readOnly = false;
			document.getElementById('client_subdomain_add').elements['subdomain_mnt_pt'].disabled = false;
			document.getElementById('client_subdomain_add').elements['subdomain_mnt_pt'].value = "/" + subname;
			document.getElementById('domain_mode').style.display='table-row';
			document.getElementById('subdmn_id').style.display='none';
			document.getElementById('subdomain_name').style.display='table-row';
		} else {
			document.getElementById('client_subdomain_add').elements['subdmn_id'].readOnly = false;
			document.getElementById('client_subdomain_add').elements['subdmn_id'].disabled = false;
			document.getElementById('client_subdomain_add').elements['subdomain_mnt_pt'].readOnly = true;
			document.getElementById('client_subdomain_add').elements['subdomain_mnt_pt'].disabled = true;
			document.getElementById('client_subdomain_add').elements['subdomain_mnt_pt'].value = '';
			document.getElementById('domain_mode').style.display='none';
			document.getElementById('subdmn_id').style.display='table-row';
			document.getElementById('subdomain_name').style.display='none';
		}
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_MENU_ADD_SUBDOMAIN}{/block}

{block name=BREADCRUMP}
<li><a href="/client/domains_manage.php">{$TR_MENU_MANAGE_DOMAINS}</a></li>
<li><a>{$TR_MENU_ADD_SUBDOMAIN}</a></li>
{/block}

{block name=BODY}
<h2 class="domains"><span>{$TR_ADD_SUBDOMAIN}</span></h2>
<form action="/client/subdomain_add.php" method="post" id="client_subdomain_add">
	<table>
		<tr>
			<td>{$TR_SUBDOMAIN_NAME} <span id="fwd_help" class="icon i_help">&nbsp;</span></td>
			<td>
				<input type="text" name="subdomain_name" id="subdomain_name" value="{$SUBDOMAIN_NAME}" onblur="makeUser();" />&nbsp;
				{if isset($SUBDMN_NAME)}
				<select name="subdmn_id" id="subdmn_id">
					{section name=i loop=$SUBDMN_NAME}
					<option value="{$SUBDMN_ID[i]}" {$SUBDMN_SELECTED[i]}>.{$SUBDMN_NAME[i]}</option>
					{/section}
				</select>
				{/if}
				<input type="radio" name="dmn_type" id="dmn_type" value="dmn" onchange='toggleSubDomainMount(this);' {$SUB_DMN_CHECKED} />{$DOMAIN_NAME}&nbsp;
				{if isset($ALS_NAME)}
				<input type="radio" name="dmn_type" value="als" onchange='toggleSubDomainMount(this);' {$SUB_ALS_CHECKED} />
				<select name="als_id">
					{section name=i loop=$ALS_NAME}
					<option value="{$ALS_ID[i]}" {$ALS_SELECTED[i]}>.{$ALS_NAME[i]}</option>
					{/section}
				</select>
				{/if}
			</td>
		</tr>
		<tr id="domain_mode">
			<td>{$TR_DIR_TREE_SUBDOMAIN_MOUNT_POINT} <span id="mnt_help" class="icon i_help">&nbsp;</span></td>
			<td><input type="text" name="subdomain_mnt_pt" id="subdomain_mnt_pt" value="{$SUBDOMAIN_MOUNT_POINT}" /></td>
		</tr>
		<tr>
			<td>{$TR_ENABLE_FWD}</td>
			<td>
				<input type="radio" name="status" id="status_EN" value="1" onchange='setForwardReadonly(this);' {$CHECK_EN} />&nbsp;{$TR_ENABLE}<br />
				<input type="radio" name="status" id="status_DIS" value="0" onchange='setForwardReadonly(this);' {$CHECK_DIS} />&nbsp;{$TR_DISABLE}
			</td>
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
	<div class="buttons">
		<input type="hidden" name="uaction" value="add_subd" />
		<input type="submit" name="Submit" value="{$TR_ADD}" />
	</div>
</form>
{/block}