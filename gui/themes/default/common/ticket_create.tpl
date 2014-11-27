{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_NEW_TICKET}{/block}

{block name=BREADCRUMP}
<li><a href="ticket_system.php">{$TR_MENU_SUPPORT_SYSTEM}</a></li>
<li><a>{$TR_NEW_TICKET}</a></li>
{/block}

{block name=BODY}
<h2 class="support"><span>{$TR_NEW_TICKET}</span></h2>
<form action="ticket_create.php" method="post" id="reseller_ticket_create">
	<table>
		<tbody>
			<tr>
				<td><label for="urgency">{$TR_URGENCY}</label></td>
				<td>
					<select name="urgency" id="urgency">
						<option value="1" {$OPT_URGENCY_1}>{$TR_LOW}</option>
						<option value="2" {$OPT_URGENCY_2}>{$TR_MEDIUM}</option>
						<option value="3" {$OPT_URGENCY_3}>{$TR_HIGH}</option>
						<option value="4" {$OPT_URGENCY_4}>{$TR_VERI_HIGH}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="subj">{$TR_SUBJECT}</label></td>
				<td><input type="text" name="subj" id="subj" value="{$SUBJECT}" /></td>
			</tr>
			<tr>
				<td><label for="user_message">{$TR_YOUR_MESSAGE}</label></td>
				<td><textarea name="user_message" id="user_message" cols="80" rows="12">{$USER_MESSAGE}</textarea></td>
			</tr>
		</tbody>
	</table>
	<div class="buttons">
		<input type="hidden" name="uaction" value="send_msg" />
		<input type="submit" name="Submit" value="{$TR_SEND_MESSAGE}" />
	</div>
</form>
{/block}