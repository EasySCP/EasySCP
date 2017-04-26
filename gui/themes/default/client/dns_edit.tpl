{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function(){
		dns_type_changed(document.getElementById('dns_type').value);
	});

	{literal}
	function in_array(needle, haystack) {
		var n = haystack.length;
		for (var i = 0; i < n; i++) {
			if (haystack[i] == needle) return true;
		}
		return false;
	}

	function dns_show_rows(arr_show) {
		var arr_possible = ['cname', 'ip_address', 'ip_address_v6', 'name', 'ns', 'plain', 'srv_host', 'srv_name', 'srv_prio', 'srv_port', 'srv_protocol', 'srv_ttl', 'srv_weight'];
		var n = arr_possible.length;
		var trname;
		for (var i = 0; i < n; i++) {
			trname = 'tr_dns_'+arr_possible[i];
			o = document.getElementById(trname);
			if (o) {
				if (in_array(arr_possible[i], arr_show)) {
					o.style.display = 'table-row';
				} else {
					o.style.display = 'none';
				}
			} else {
				alert('Not found: '+trname);
			}
		}
	}

	function dns_type_changed(value) {
		switch (value){
			case 'A':
				dns_show_rows(['name', 'ip_address']);
				break;
			case 'AAAA':
				dns_show_rows(['name', 'ip_address_v6']);
				break;
			case 'CNAME':
				dns_show_rows(['name', 'cname']);
				break;
			case 'MX':
				dns_show_rows(['srv_prio', 'srv_host']);
				break;
			case 'NS':
				dns_show_rows(['ns']);
				break;
			case 'SRV':
				dns_show_rows(['srv_name', 'srv_protocol', 'srv_ttl', 'srv_prio', 'srv_weight', 'srv_host', 'srv_port']);
				break;
			case 'TXT':
				dns_show_rows(['plain']);
				break;
		}
	}

	var IPADDRESS = "[0-9\.]";
	var IPv6ADDRESS = "[0-9a-f:A-F]";
	var NUMBERS = "[0-9]";

	function filterChars(e, allowed){
		var keynum;
		if (window.event){
			keynum = window.event.keyCode;
			e = window.event;
		} else if (e) {
			keynum = e.which;
		} else {
			return true;
		}

		if ((keynum == 8) || (keynum == 0)) {
			return true;
		}
		var keychar = String.fromCharCode(keynum);

		if (e.ctrlKey && ((keychar=="C") || (keychar=="c") || (keychar=="V") || (keychar=="v"))) {
			return true;
		}
		var re = new RegExp(allowed);
		return re.test(keychar);
	}
	{/literal}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_EDIT_DNS}{/block}

{block name=BREADCRUMP}
	<li><a href="/client/domains_manage.php">{$TR_MENU_MANAGE_DOMAINS}</a></li>
	<li><a>{$TR_EDIT_DNS}</a></li>
{/block}

{block name=BODY}
<h2 class="domains"><span>{$TR_EDIT_DNS}</span></h2>
<form action="{$ACTION_MODE}" method="post" id="client_dns_edit">
	<table>
		{if isset($ADD_RECORD)}
		<tr>
			<td>{$TR_DOMAIN}</td>
			<td><select name="alias_id">{$SELECT_ALIAS}</select></td>
		</tr>
		{/if}
		<tr>
			<td>{$TR_DNS_TYPE}</td>
			<td><select name="type" id="dns_type" onchange="dns_type_changed(this.value)">{$SELECT_DNS_TYPE}</select></td>
		</tr>
		<tr id="tr_dns_name">
			<td>{$TR_DNS_NAME}</td>
			<td><input type="text" name="dns_name" value="{$DNS_NAME}" /></td>
		</tr>
		<tr id="tr_dns_srv_name">
			<td>{$TR_DNS_SRV_NAME}</td>
			<td><input type="text" name="dns_srv_name" value="{$DNS_SRV_NAME}" /></td>
		</tr>
		<tr id="tr_dns_ip_address">
			<td>{$TR_DNS_IP_ADDRESS}</td>
			<td><input type="text" onkeypress="return filterChars(event, IPADDRESS);" name="dns_A_address" value="{$DNS_ADDRESS}" /></td>
		</tr>
		<tr id="tr_dns_ip_address_v6">
			<td>{$TR_DNS_IP_ADDRESS_V6}</td>
			<td><input type="text" onkeypress="return filterChars(event, IPv6ADDRESS);" name="dns_AAAA_address" value="{$DNS_ADDRESS_V6}" /></td>
		</tr>
		<tr id="tr_dns_srv_protocol">
			<td>{$TR_DNS_SRV_PROTOCOL}</td>
			<td><select name="srv_proto" id="srv_protocol">{$SELECT_DNS_SRV_PROTOCOL}</select></td>
		</tr>
		<tr id="tr_dns_srv_ttl">
			<td>{$TR_DNS_SRV_TTL}</td>
			<td><input type="text" onkeypress="return filterChars(event, NUMBERS);" name="dns_srv_ttl" value="{$DNS_SRV_TTL}" /></td>
		</tr>
		<tr id="tr_dns_srv_prio">
			<td>{$TR_DNS_SRV_PRIO}</td>
			<td><input type="text" onkeypress="return filterChars(event, NUMBERS);" name="dns_srv_prio" value="{$DNS_SRV_PRIO}" /></td>
		</tr>
		<tr id="tr_dns_srv_weight">
			<td>{$TR_DNS_SRV_WEIGHT}</td>
			<td><input type="text" onkeypress="return filterChars(event, NUMBERS);"name="dns_srv_weight" value="{$DNS_SRV_WEIGHT}" /></td>
		</tr>
		<tr id="tr_dns_srv_host">
			<td>{$TR_DNS_SRV_HOST}</td>
			<td><input type="text" name="dns_srv_host" value="{$DNS_SRV_HOST}" /></td>
		</tr>
		<tr id="tr_dns_srv_port">
			<td>{$TR_DNS_SRV_PORT}</td>
			<td><input type="text" onkeypress="return filterChars(event, NUMBERS);" name="dns_srv_port" value="{$DNS_SRV_PORT}" /></td>
		</tr>
		<tr id="tr_dns_cname">
			<td>{$TR_DNS_CNAME}</td>
			<td><input type="text" name="dns_cname" value="{$DNS_CNAME}" />.</td>
		</tr>
		<tr id="tr_dns_ns">
			<td>{$TR_DNS_NS}</td>
			<td><input type="text" name="dns_ns" value="{$DNS_NS_HOSTNAME}" />.</td>
		</tr>
		<tr id="tr_dns_plain">
			<td>{$TR_DNS_PLAIN}</td>
			<td><input type="text" name="dns_plain_data" value="{$DNS_PLAIN}" size="60"/></td>
		</tr>
	</table>
	<div class="buttons">
		{if isset($FORM_EDIT_MODE)}
		<input type="hidden" name="uaction" value="modify" />
		<input type="submit" name="Submit" value="{$TR_MODIFY}" />
		{/if}
		{if isset($FORM_ADD_MODE)}
		<input type="hidden" name="uaction" value="add" />
		<input type="submit" name="Submit" value="{$TR_ADD}" />
		{/if}
	</div>
</form>
{/block}