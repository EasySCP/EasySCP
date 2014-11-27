{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function action_delete(url, subject) {
		if (!confirm(sprintf("{$TR_MESSAGE_DELETE}", '"' + subject + '"')))
		{
			return false;
		} else {
			location = url;
			return true;
		}
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_MENU_OVERVIEW}{/block}

{block name=BREADCRUMP}
<li><a href="/client/mail_accounts.php">{$TR_MENU_EMAIL_ACCOUNTS}</a></li>
<li><a>{$TR_MENU_OVERVIEW}</a></li>
{/block}

{block name=BODY}
{if isset($MAIL_MSG)}
<div class="{$MAIL_MSG_TYPE}">{$MAIL_MSG}</div>
{/if}
{if isset($MAIL_ACC)}
<h2 class="email"><span>{$TR_MAIL_USERS}</span></h2>
<table class="tablesorter">
	<thead>
		<tr>
			<th>{$TR_MAIL}</th>
			<th>{$TR_TYPE}</th>
			<th>{$TR_STATUS}</th>
			<th>{$TR_ACTION}</th>
		</tr>
	</thead>
	{if isset($TOTAL_MAIL_ACCOUNTS)}
	<tfoot>
		<tr>
			<td colspan="4">{$TR_TOTAL_MAIL_ACCOUNTS}: {$TOTAL_MAIL_ACCOUNTS}/{$ALLOWED_MAIL_ACCOUNTS}</td>
		</tr>
	</tfoot>
	{/if}
	<tbody>
		{section name=i loop=$MAIL_ACC}
		<tr>
			<td>
				<span class="icon i_mail_icon">{$MAIL_ACC[i]}</span>
				<!--
				{if isset($AUTO_RESPOND_DISABLE[i])}
				<div style="display: {$AUTO_RESPOND_VIS[i]};font-size: smaller;">
					<br />
					{$TR_AUTORESPOND}: [ <a href="{$AUTO_RESPOND_DISABLE_SCRIPT[i]}">{$AUTO_RESPOND_DISABLE[i]}</a> <a href="{$AUTO_RESPOND_EDIT_SCRIPT[i]}">{$AUTO_RESPOND_EDIT[i]}</a> ]
				  </div>
			  {/if}
			  -->
			</td>
			<td>{$MAIL_TYPE[i]}</td>
			<td>{$MAIL_STATUS[i]}</td>
			<td>
				<a href="{$MAIL_EDIT_URL[i]}" title="{$TR_EDIT}" class="icon i_edit"></a>
				<a href="#" onclick="action_delete('{$MAIL_DELETE_URL[i]}', '{$MAIL_ACC[i]}')" title="{$TR_DELETE}" class="icon i_delete"></a>
			</td>
		</tr>
		{/section}
	</tbody>
</table>
{/if}
{/block}