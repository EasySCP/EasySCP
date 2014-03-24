{include file='client/header.tpl'}
<body>
	<script type="text/javascript">
	/* <![CDATA[ */
		{literal}
		function in_array(needle, haystack) {
			var n = haystack.length;
			for (var i = 0; i < n; i++) {
				if (haystack[i] == needle) return true;
			}
			return false;
		}

		function dns_show_rows(arr_show) {
			var arr_possible = new Array('name', 'ip_address', 'ip_address_v6',
				'srv_name', 'srv_protocol', 'srv_ttl', 'srv_prio',
				'srv_weight', 'srv_host', 'srv_port', 'cname', 'ns');
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
			if (value == 'A') {
				dns_show_rows(new Array('name', 'ip_address'));
			} else if (value == 'AAAA') {
				dns_show_rows(new Array('name', 'ip_address_v6'));
			} else if (value == 'SRV') {
				dns_show_rows(new Array('srv_name', 'srv_protocol', 'srv_ttl',
					'srv_prio', 'srv_weight', 'srv_host', 'srv_port'));
			} else if (value == 'CNAME') {
				dns_show_rows(new Array('name', 'cname'));
			} else if (value == 'MX') {
				dns_show_rows(new Array('srv_prio', 'srv_host'));
			} else if (value == 'NS') {
				dns_show_rows(new Array('ns'));
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
	<div class="header">
		{include file="$MAIN_MENU"}
		<div class="logo">
			<img src="{$THEME_COLOR_PATH}/images/easyscp_logo.png" alt="EasySCP logo" />
			<img src="{$THEME_COLOR_PATH}/images/easyscp_webhosting.png" alt="EasySCP - Easy Server Control Panel" />
		</div>
	</div>
	<div class="location">
		<ul class="location-menu">
			{if isset($YOU_ARE_LOGGED_AS)}
			<li><a href="change_user_interface.php?action=go_back" class="backadmin">{$YOU_ARE_LOGGED_AS}</a></li>
			{/if}
			<li><a href="../index.php?logout" class="logout">{$TR_MENU_LOGOUT}</a></li>
		</ul>
		<ul class="path">
			<li><a href="domains_manage.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a>{$TR_EDIT_DNS}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
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
	</div>
	<script type="text/javascript">
	/* <![CDATA[ */
		{literal}
		dns_type_changed(document.getElementById('dns_type').value);
		{/literal}
	/* ]]> */
	</script>
{include file='client/footer.tpl'}