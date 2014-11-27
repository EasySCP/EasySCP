{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_SERVER_TRAFFIC_SETTINGS}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/settings.php">{$TR_MENU_SETTINGS}</a></li>
<li><a>{$TR_SERVER_TRAFFIC_SETTINGS}</a></li>
{/block}

{block name=BODY}
<h2 class="general"><span>{$TR_SERVER_TRAFFIC_SETTINGS}</span></h2>
<form action="/admin/settings_server_traffic.php" method="post" id="admin_modify_server_traffic_settings">
	<fieldset>
		<legend>{$TR_SET_SERVER_TRAFFIC_SETTINGS}</legend>
		<table>
			<tr>
				<td><label for="max_traffic">{$TR_MAX_TRAFFIC}</label></td>
				<td><input type="text" name="max_traffic" id="max_traffic" value="{$MAX_TRAFFIC}" /></td>
			</tr>
			<tr>
				<td><label for="traffic_warning">{$TR_WARNING}</label></td>
				<td><input type="text" name="traffic_warning" id="traffic_warning" value="{$TRAFFIC_WARNING}" /></td>
			</tr>
		</table>
	</fieldset>
	<div class="buttons">
		<input type="hidden" name="uaction" value="modify" />
		<input type="submit" name="Submit"  value="{$TR_MODIFY}" />
	</div>
</form>
{/block}