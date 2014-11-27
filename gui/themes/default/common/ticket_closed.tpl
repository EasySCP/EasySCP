{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function() {
		$('#ticket_delete_all').click(function() {
			if (confirm("{sprintf("{$TR_MESSAGE_DELETE}", {$TR_CLOSED_TICKETS})}"))
			{
				document.location.href = 'ticket_delete.php?delete=closed';
			} else {
				return false;
			}
		});
	});

	function action_delete(url, subject) {
		return confirm(sprintf("{$TR_MESSAGE_DELETE}", subject));
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_CLOSED_TICKETS}{/block}

{block name=BREADCRUMP}
<li><a href="ticket_system.php">{$TR_MENU_SUPPORT_SYSTEM}</a></li>
<li><a>{$TR_CLOSED_TICKETS}</a></li>
{/block}

{block name=BODY}
{if isset($SUBJECT)}
<h2 class="support"><span>{$TR_CLOSED_TICKETS}</span></h2>
<div class="buttons">
	<input type="button" name="ticket_delete_all" id="ticket_delete_all" value="{$TR_DELETE_ALL}" />
</div>
<table>
	<thead>
		<tr>
			<th>{$TR_STATUS}</th>
			{if isset($TR_TICKET_FROM)}
			<th>{$TR_TICKET_FROM}</th>
			{/if}
			<th>{$TR_SUBJECT}</th>
			<th>{$TR_URGENCY}</th>
			<th>{$TR_LAST_DATA}</th>
			<th>{$TR_ACTION}</th>
		</tr>
	</thead>
	<tbody>
		{section name=i loop=$NEW}
		<tr>
			<td>{$NEW[i]}</td>
			{if isset($FROM[i])}
			<td>{$FROM[i]}</td>
			{/if}
			<td><a href="ticket_view.php?ticket_id={$ID[i]}" class="icon i_document">{$SUBJECT[i]}</a></td>
			<td>{$URGENCY[i]}</td>
			<td>{$LAST_DATE[i]}</td>
			<td>
				<a href="ticket_view.php?ticket_id={$ID[i]}" title="{$TR_EDIT}" class="icon i_edit"></a>
				<a href="#" onclick="action_delete('ticket_delete.php?ticket_id={$ID[i]}', '{$SUBJECT2[i]}')" title="{$TR_DELETE}" class="icon i_delete"></a>
			</td>
		</tr>
		{/section}
	</tbody>
</table>
<div class="paginator">
	{if !isset($SCROLL_NEXT_GRAY)}
	<span class="icon i_next_gray">&nbsp;</span>
	{/if}
	{if !isset($SCROLL_NEXT)}
	<a href="ticket_system.php?psi={$NEXT_PSI}" title="next" class="icon i_next">next</a>
	{/if}
	{if !isset($SCROLL_PREV_GRAY)}
	<span class="icon i_prev_gray">&nbsp;</span>
	{/if}
	{if !isset($SCROLL_PREV)}
	<a href="ticket_system.php?psi={$PREV_PSI}" title="previous" class="icon i_prev">previous</a>
	{/if}
</div>
{/if}
{/block}