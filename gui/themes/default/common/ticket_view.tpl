{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function(){
		$('#SubmitAction').click(function() {
			form = document.getElementById('ticket_view');
			form.uaction.value = '{$ACTION}';
			form.submit();
		});
	});
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_VIEW_SUPPORT_TICKET}{/block}

{block name=BREADCRUMP}
<li><a href="ticket_system.php">{$TR_MENU_SUPPORT_SYSTEM}</a></li>
<li><a>{$TR_VIEW_SUPPORT_TICKET}</a></li>
{/block}

{block name=BODY}
<h2 class="support"><span>{$TR_VIEW_SUPPORT_TICKET}</span></h2>
{if isset($SUBJECT)}
<table>
	<tr>
		<td>{$TR_TICKET_URGENCY}:</td>
		<td>{$URGENCY}</td>
	</tr>
	<tr>
		<td>{$TR_TICKET_SUBJECT}:</td>
		<td>{$SUBJECT}</td>
	</tr>
	{section name=i loop=$FROM}
	<tr>
		<td>{$TR_TICKET_FROM}:</td>
		<td>{$FROM[i]}</td>
	</tr>
	<tr>
		<td>{$TR_TICKET_DATE}:</td>
		<td>{$DATE[i]}</td>
	</tr>
	<tr>
		<td colspan="2">{$TICKET_CONTENT[i]}</td>
	</tr>
	{/section}
</table>
{/if}
<h2 class="doc">{$TR_NEW_TICKET_REPLY}</h2>
<form action="ticket_view.php?ticket_id={$ID}" method="post" id="ticket_view">
	<table>
		<tbody>
			<tr>
				<td><textarea name="user_message" cols="80" rows="12"></textarea></td>
			</tr>
		</tbody>
	</table>
	<div class="buttons">
		<input type="hidden" name="screenwidth" value="{$SCREENWIDTH}" />
		<input type="hidden" name="subject" value="{$SUBJECT}" />
		<input type="hidden" name="urgency" value="{$URGENCY_ID}" />
		<input type="hidden" name="uaction" value="send_msg" />
		<input type="submit" name="Submit" value="{$TR_REPLY}" />
		<input type="button" id="SubmitAction" value="{$TR_ACTION}" />
	</div>
</form>
{/block}