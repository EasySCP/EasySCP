{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function action_delete(url, mailacc) {
		if (url.indexOf("delete")==-1) {
			location = url;
		} else {
			if (!confirm(sprintf("{$TR_MESSAGE_DELETE}", mailacc)))
				return false;
			location = url;
		}
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_MENU_CATCH_ALL_MAIL}{/block}

{block name=BREADCRUMP}
<li><a href="/client/mail_accounts.php">{$TR_MENU_EMAIL_ACCOUNTS}</a></li>
<li><a>{$TR_MENU_CATCH_ALL_MAIL}</a></li>
{/block}

{block name=BODY}
<h2 class="email"><span>{$TR_CATCHALL_MAIL_USERS}</span></h2>
{if isset($CATCHALL_MSG)}
<div class="{$MSG_TYPE}">{$CATCHALL_MSG}</div>
{/if}
<table>
	<thead>
		<tr>
			<th>{$TR_DOMAIN}</th>
			<th>{$TR_CATCHALL}</th>
			<th>{$TR_STATUS}</th>
			<th>{$TR_ACTION}</th>
		</tr>
	</thead>
	<tbody>
		{section name=i loop=$CATCHALL_DOMAIN}
		<tr>
			<td>{$CATCHALL_DOMAIN[i]}</td>
			<td>{$CATCHALL_ACC[i]}</td>
			<td>{$CATCHALL_STATUS[i]}</td>
			<td>
				<a href="#" onclick="action_delete('{$CATCHALL_ACTION_SCRIPT[i]}', '{$CATCHALL_ACC[i]}')" title="{$CATCHALL_ACTION[i]}" class="icon i_users"></a>
			</td>
		</tr>
		{/section}
	</tbody>
</table>
{/block}