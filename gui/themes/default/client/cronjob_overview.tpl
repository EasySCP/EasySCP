{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function action_status(url, dmn_name) {
		if (!confirm(sprintf("{$TR_MESSAGE_CHANGE_STATUS}", dmn_name)))
			return false;
		location = url;
	}
	function action_delete(url, subject) {
		if (!confirm(sprintf("{$TR_MESSAGE_DELETE}", subject)))
			return false;
		location = url;
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_SSL_TITLE}{/block}

{block name=BREADCRUMP}
	<li><a href="/client/webtools.php">{$TR_MENU_WEBTOOLS}</a></li>
	<li><a>{$TR_CRONJOB_OVERVIEW}</a></li>
{/block}

{block name=BODY}
{if isset($CRON_MSG)}
	<div class="{$CRON_MSG_TYPE}">{$CRON_MSG}</div>
{else}
<h2 class="serverstatus"><span>{$TR_CRONJOB_OVERVIEW}</span></h2>
	<table class="tablesorter">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>{$TR_OWNER}</th>
				<th>{$TR_CRONJOB_NAME}</th>
				<th>{$TR_DESCR}</th>
				<th>{$TR_USER}</th>
				<th>{$TR_STATUS}</th>
				<th>{$TR_ADMIN_OPTIONS}</th>
			</tr>
		</thead>
		<tbody>
			{section name=i loop=$CRON_NAME}
			<tr>
				<td><a href="#" onclick="action_status('{$CRON_STATUS_ACTION[i]}', '{$CRON_NAME[i]}')" title="{$STATUS_ICON[i]}" class="icon i_{$STATUS_ICON[i]}"></a></td>
				<td>{$CRON_OWNER[i]}</td>
				<td>{$CRON_NAME[i]}</td>
				<td>{$CRON_DESCR[i]}</td>
				<td>{$CRON_USER[i]}</td>
				<td>{$CRON_STATUS[i]}</td>
				<td>
					<a href="{$CRON_EDIT_ACTION[i]}" title="{$TR_EDIT}" class="icon i_edit"></a>
					<a href="#" onclick="action_delete('{$CRON_DELETE_ACTION[i]}', '{$CRON_NAME[i]}')" title="{$TR_DELETE}" class="icon i_delete"></a>
				</td>
			</tr>
			{/section}
		</tbody>
	</table>
{/if}
{/block}