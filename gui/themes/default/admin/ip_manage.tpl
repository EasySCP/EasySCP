{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function action_delete(url, subject) {
		if (confirm(sprintf("{$TR_MESSAGE_DELETE}", subject)))
		{
			document.location.href = url;
		} else {
			return false;
		}
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$MANAGE_IPS}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/settings.php">{$TR_MENU_SETTINGS}</a></li>
<li><a>{$MANAGE_IPS}</a></li>
{/block}

{block name=BODY}
<h2 class="ip"><span>{$MANAGE_IPS}</span></h2>
<fieldset>
	<legend>{$TR_AVAILABLE_IPS}</legend>
	<table>
		<thead>
			<tr>
				<th>{$TR_IP}</th>
				<th>{$TR_IPv6}</th>
				<th>{$TR_DOMAIN}</th>
				<th>{$TR_ALIAS}</th>
				<th>{$TR_NETWORK_CARD}</th>
				<th>{$TR_ACTION}</th>
			</tr>
		</thead>
		<tbody>
			{section name=i loop=$IP}
			<tr>
				<td>{$IP[i]}</td>
				<td>{$IPv6[i]}</td>
				<td>{$DOMAIN[i]}</td>
				<td>{$ALIAS[i]}</td>
				<td>{$NETWORK_CARD[i]}</td>
				<td>
					{if isset($IP_ACTION[i])}
						{if $IP_ACTION[i] == false}
							N/A
						{else}
							<a href="#" onclick="action_delete('{$IP_ACTION_SCRIPT[i]}', '{$IP[i]}')"  title="{$IP_ACTION[i]}" class="icon i_delete"></a>
						{/if}
					{/if}
				</td>
			</tr>
			{/section}
		</tbody>
	</table>
</fieldset>
<form action="/admin/ip_manage.php" method="post" id="add_new_ip_frm">
	<fieldset>
		<legend>{$TR_ADD_NEW_IP}</legend>
		<table>
			<tr>
				<td><label for="ip_number_1">{$TR_IP}</label></td>
				<td><input type="text" name="ip_number_1" id="ip_number_1" value="{$VALUE_IP1}" maxlength="3" />.</td>
				<td><input type="text" name="ip_number_2" id="ip_number_2" value="{$VALUE_IP2}" maxlength="3" />.</td>
				<td><input type="text" name="ip_number_3" id="ip_number_3" value="{$VALUE_IP3}" maxlength="3" />.</td>
				<td><input type="text" name="ip_number_4" id="ip_number_4" value="{$VALUE_IP4}" maxlength="3" /></td>
			</tr>
			<tr>
				<td><label for="ipv6">{$TR_IPv6}</label></td>
				<td colspan="3"><input type="text" name="ipv6" id="ipv6" value="{$VALUE_IPv6}" /></td>
			</tr>
			<tr>
				<td><label for="domain">{$TR_DOMAIN}</label></td>
				<td colspan="3"><input type="text" name="domain" id="domain" value="{$VALUE_DOMAIN}" /></td>
			</tr>
			<tr>
				<td><label for="alias">{$TR_ALIAS}</label></td>
				<td colspan="3"><input type="text" name="alias" id="alias" value="{$VALUE_ALIAS}" />
				</td>
			</tr>
			<tr>
				<td>{$TR_NETWORK_CARD}</td>
				<td colspan="3">
					<select name="ip_card">
						{section name=i loop=$NETWORK_CARDS}
						<option>{$NETWORK_CARDS[i]}</option>
						{/section}
					</select>
				</td>
			</tr>
		</table>
		<input type="hidden" name="uaction" value="add_ip" />
		<input type="submit" name="Submit" value="  {$TR_ADD}  " />
	</fieldset>
</form>
{/block}