{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	function action_delete(url, subject) {
		if (!confirm(sprintf("{$TR_MESSAGE_DELETE}", subject)))
			return false;
		location = url;
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_MENU_OVERVIEW}{/block}

{block name=BREADCRUMP}
<li><a href="/reseller/hosting_plan.php">{$TR_MENU_HOSTING_PLANS}</a></li>
<li><a>{$TR_MENU_OVERVIEW}</a></li>
{/block}

{block name=BODY}
{if isset($PLAN_NAME)}
<h2 class="serverstatus"><span>{$TR_HOSTING_PLANS}</span></h2>
<table>
	<tr>
		<th>{$TR_NOM}</th>
		<th>{$TR_PLAN_NAME}</th>
		<th>{$TR_PURCHASING}</th>
		<th>{$TR_ACTION}</th>
	</tr>
	{section name=i loop=$PLAN_NOM}
	<tr>
		<td>{$PLAN_NOM[i]}</td>
		<td><a href="../orderpanel/package_info.php?coid={$CUSTOM_ORDERPANEL_ID[i]}&amp;user_id={$RESELLER_ID[i]}&amp;id={$HP_ID[i]}" title="{$PLAN_SHOW}" class="external">{$PLAN_NAME[i]}</a></td>
		<td>{$PURCHASING[i]}</td>
		<td>
			<a href="hosting_plan_edit.php?hpid={$HP_ID[i]}" title="{$TR_EDIT}" class="icon i_edit"></a>
			<a href="#" onclick="return action_delete('hosting_plan_delete.php?hpid={$HP_ID[i]}', '{$PLAN_NAME2[i]}')" title="{$TR_DELETE}" class="icon i_delete"></a>
		</td>
	</tr>
	{/section}
</table>
{/if}
{/block}