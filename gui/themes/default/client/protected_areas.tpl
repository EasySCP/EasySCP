{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function() {
		$('#protected_areas_add').click(function() {
			document.location.href = 'protected_areas_add.php';
		});
		$('#protected_user_manage').click(function() {
			document.location.href = 'protected_user_manage.php';
		});
	});

	function action_delete(url, subject) {
		return confirm(sprintf("{$TR_MESSAGE_DELETE}", subject));
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_HTACCESS}{/block}

{block name=BREADCRUMP}
<li><a href="/client/webtools.php">{$TR_MENU_WEBTOOLS}</a></li>
<li><a>{$TR_HTACCESS}</a></li>
{/block}

{block name=BODY}
<h2 class="htaccess"><span>{$TR_HTACCESS}</span></h2>
{if isset($AREA_PATH)}
<table>
	<thead>
		<tr>
			<th>{$TR_HTACCESS}</th>
			<th>{$TR_STATUS}</th>
			<th>{$TR__ACTION}</th>
		</tr>
	</thead>
	<tbody>
		{section name=i loop=$AREA_PATH}
		<tr>
			<td>{$AREA_NAME[i]}<br /><em>{$AREA_PATH[i]}</em></td>
			<td>{$STATUS[i]}</td>
			<td>
				<a href="/client/protected_areas_add.php?id={$PID[i]}" title="{$TR_EDIT}" class="icon i_edit"></a>
				<a href="/client/protected_areas_delete.php?id={$PID[i]}" onclick="return action_delete('protected_areas_delete.php?id={$PID[i]}', '{$JS_AREA_NAME[i]}')" title="{$TR_DELETE}" class="icon i_delete"></a>
			</td>
		</tr>
		{/section}
	</tbody>
</table>
{/if}
<div class="buttons">
	<input type="button" name="protected_areas_add" id="protected_areas_add" value="{$TR_ADD_AREA}" />
	<input type="button" name="protected_user_manage" id="protected_user_manage" value="{$TR_MANAGE_USRES}" />
</div>
{/block}