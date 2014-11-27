{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_SYSTEM_INFO}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/system_info.php">{$TR_MENU_SYSTEM_TOOLS}</a></li>
<li><a>{$TR_MENU_OVERVIEW}</a></li>
{/block}

{block name=BODY}
<h2 class="system_vital"><span>{$TR_SYSTEM_INFO}</span></h2>
<table class="description">
	<tr>
		<th style="width: 250px;">{$TR_KERNEL}</th>
		<td>{$KERNEL}</td>
	</tr>
	<tr>
		<th>{$TR_UPTIME}</th>
		<td>{$UPTIME}</td>
	</tr>
	<tr>
		<th>{$TR_LOAD}</th>
		<td>{$LOAD}</td>
	</tr>
</table>
<h2 class="system_cpu"><span>{$TR_CPU_SYSTEM_INFO}</span></h2>
<table class="description">
	<tr>
		<th style="width: 250px;">{$TR_CPU_MODEL}</th>
		<td>{$CPU_MODEL}</td>
	</tr>
	<tr>
		<th>{$TR_CPU_COUNT}</th>
		<td>{$CPU_COUNT}</td>
	</tr>
	<tr>
		<th>{$TR_CPU_MHZ}</th>
		<td>{$CPU_MHZ}</td>
	</tr>
	<tr>
		<th>{$TR_CPU_CACHE}</th>
		<td>{$CPU_CACHE}</td>
	</tr>
	<tr>
		<th>{$TR_CPU_BOGOMIPS}</th>
		<td>{$CPU_BOGOMIPS}</td>
	</tr>
</table>
<h2 class="system_memory"><span>{$TR_MEMRY_SYSTEM_INFO}</span></h2>
<table>
	<tr>
		<th>{$TR_RAM}</th>
		<th>{$TR_TOTAL}</th>
		<th>{$TR_USED}</th>
		<th>{$TR_FREE}</th>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>{$RAM_TOTAL}</td>
		<td>{$RAM_USED}</td>
		<td>{$RAM_FREE}</td>
	</tr>
	<tr>
		<th>{$TR_SWAP}</th>
		<th>{$TR_TOTAL}</th>
		<th>{$TR_USED}</th>
		<th>{$TR_FREE}</th>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>{$SWAP_TOTAL}</td>
		<td>{$SWAP_USED}</td>
		<td>{$SWAP_FREE}</td>
	</tr>
</table>
<h2 class="system_filesystem"><span>{$TR_FILE_SYSTEM_INFO}</span></h2>
{if isset($MOUNT)}
<table>
	<thead>
		<tr>
			<th>{$TR_MOUNT}</th>
			<th>{$TR_TYPE}</th>
			<th>{$TR_PARTITION}</th>
			<th>{$TR_PERCENT}</th>
			<th>{$TR_FREE}</th>
			<th>{$TR_USED}</th>
			<th>{$TR_SIZE}</th>
		</tr>
	</thead>
	<tbody>
		{section name=i loop=$MOUNT}
		<tr>
			<td>{$MOUNT[i]}</td>
			<td>{$TYPE[i]}</td>
			<td>{$PARTITION[i]}</td>
			<td>{$PERCENT[i]}</td>
			<td>{$FREE[i]}</td>
			<td>{$USED[i]}</td>
			<td>{$SIZE[i]}</td>
		</tr>
		{/section}
	</tbody>
</table>
{/if}
{/block}