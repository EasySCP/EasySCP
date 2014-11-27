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
<li><a href="/client/ftp_accounts.php">{$TR_MENU_FTP_ACCOUNTS}</a></li>
<li><a>{$TR_MENU_OVERVIEW}</a></li>
{/block}

{block name=BODY}
{if isset($FTP_MSG)}
<div class="{$FTP_MSG_TYPE}">{$FTP_MSG}</div>
{/if}
{if isset($FTP_ACCOUNT)}
<h2 class="ftp"><span>{$TR_FTP_USERS}</span></h2>
<table class="tablesorter">
	<thead>
		<tr>
			<th>{$TR_FTP_ACCOUNT}</th>
			<th>{$TR_FTP_ACTION}</th>
		</tr>
	</thead>
	{if isset($TOTAL_FTP_ACCOUNTS)}
	<tfoot>
		<tr>
			<td colspan="2">{$TR_TOTAL_FTP_ACCOUNTS}&nbsp;{$TOTAL_FTP_ACCOUNTS}</td>
		</tr>
	</tfoot>
	{/if}
	<tbody>
		{section name=i loop=$FTP_ACCOUNT}
		<tr>
			<td>{$FTP_ACCOUNT[i]}</td>
			<td>
				<a href="/client/ftp_edit.php?id={$UID[i]}" title="{$TR_EDIT}" class="icon i_edit"></a>
				<a href="#" onclick="action_delete('ftp_delete.php?id={$UID[i]}', '{$FTP_ACCOUNT[i]}')" title="{$TR_DELETE}" class="icon i_delete"></a>
				{if $FTP_LOGIN_AVAILABLE[i]}
				<a href="/client/ftp_auth.php?id={$UID[i]}" title="{$TR_LOGINAS}" class="icon i_identity external"></a>
				{/if}
			</td>
		</tr>
		{/section}
	</tbody>
</table>
{/if}
{/block}