{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_MAINTENANCEMODE}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/system_info.php">{$TR_MENU_SYSTEM_TOOLS}</a></li>
<li><a>{$TR_MAINTENANCEMODE}</a></li>
{/block}

{block name=BODY}
<h2 class="maintenancemode"><span>{$TR_MAINTENANCEMODE}</span></h2>
<div class="{$TR_MESSAGE_TYPE}">{$TR_MESSAGE_TEMPLATE_INFO}</div>
<form action="/admin/settings_maintenance_mode.php" method="post" id="maintenancemode_frm">
	<table>
		<tr>
			<td><label for="maintenancemode_message">{$TR_MESSAGE}</label></td>
			<td><textarea name="maintenancemode_message" id="maintenancemode_message" cols="80" rows="15">{$MESSAGE_VALUE}</textarea></td>
		</tr>
		<tr>
			<td><label for="maintenancemode">{$TR_MAINTENANCEMODE}</label></td>
			<td>
				<select name="maintenancemode" id="maintenancemode">
					<option value="0" {$SELECTED_OFF}>{$TR_DISABLED}</option>
					<option value="1" {$SELECTED_ON}>{$TR_ENABLED}</option>
				</select>
			</td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="uaction" value="apply" />
		<input type="submit" name="Submit" value="{$TR_APPLY_CHANGES}" />
	</div>
</form>
{/block}