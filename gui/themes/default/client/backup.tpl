{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function action_delete(url, subject) {
		return confirm(sprintf("{$TR_MESSAGE_DELETE}", subject));
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_MENU_DAILY_BACKUP}{/block}

{block name=BREADCRUMP}
<li><a href="/client/webtools.php">{$TR_MENU_WEBTOOLS}</a></li>
<li><a>{$TR_MENU_DAILY_BACKUP}</a></li>
{/block}

{block name=BODY}
<h2 class="hdd"><span>{$TR_BACKUP}</span></h2>
<h2>{$TR_DOWNLOAD_DIRECTION}</h2>
<ol>
	<li>{$TR_FTP_LOG_ON}</li>
	<li>{$TR_SWITCH_TO_BACKUP}</li>
	<li>{$TR_DOWNLOAD_FILE}<br />{$TR_USUALY_NAMED}</li>
</ol>
<h2>{$TR_RESTORE_BACKUP}</h2>
<p>{$TR_RESTORE_DIRECTIONS}</p>
<form action="backup.php" method="post" id="client_backup" onsubmit="return confirm('{$TR_CONFIRM_MESSAGE}');">
	<div class="buttons">
		<input type="hidden" name="uaction" value="bk_restore" />
		<input type="submit" name="Submit" value="{$TR_RESTORE}" />
	</div>
</form>
{/block}