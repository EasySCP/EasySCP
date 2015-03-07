{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_MENU_UPDATE_HP}{/block}

{block name=BREADCRUMP}
	<li><a href="/client/index.php">{$TR_MENU_GENERAL_INFORMATION}</a></li>
	<li><a>{$TR_MENU_UPDATE_HP}</a></li>
{/block}

{block name=BODY}
<h2 class="purchasing"><span>{$TR_MENU_UPDATE_HP}</span></h2>
{if isset($HP_NAME)}
<table>
	{section name=i loop=$HP_NAME}
	<tr>
	  <td>
		<strong>{$HP_NAME[i]}</strong><br />
		{$HP_DESCRIPTION[i]}<br />
		<br />
		{$HP_DETAILS[i]}<br />
		<br />
		<strong>{$HP_COSTS[i]}</strong></td>
	</tr>
	<tr>
	  <td><a href="/client/hosting_plan_update.php?{$LINK[i]}={$ID[i]}" class="icon i_details">{$TR_PURCHASE}</a></td>
	</tr>
	{/section}
</table>
{/if}
{/block}