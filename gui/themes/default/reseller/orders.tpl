{include file='reseller/header.tpl'}
<body>
	<script type="text/javascript">
	/* <![CDATA[ */
		function delete_order(url, domain) {
			if (!confirm(sprintf("{$TR_MESSAGE_DELETE_ACCOUNT}", domain)))
				return false;
			location = url;
		}
	/* ]]> */
	</script>
	<div class="header">
		{include file="$MAIN_MENU"}
		<div class="logo">
			<img src="{$THEME_COLOR_PATH}/images/easyscp_logo.png" alt="EasySCP logo" />
			<img src="{$THEME_COLOR_PATH}/images/easyscp_webhosting.png" alt="EasySCP - Easy Server Control Panel" />
		</div>
	</div>
	<div class="location">
		<ul class="location-menu">
			{if isset($YOU_ARE_LOGGED_AS)}
			<li><a href="change_user_interface.php?action=go_back" class="backadmin">{$YOU_ARE_LOGGED_AS}</a></li>
			{/if}
			<li><a href="../index.php?logout" class="logout">{$TR_MENU_LOGOUT}</a></li>
		</ul>
		<ul class="path">
			<li><a>{$TR_MENU_OVERVIEW}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		{if isset($ID)}
		<h2 class="billing"><span>{$TR_MANAGE_ORDERS}</span></h2>
		<table>
			<tr>
				<th>{$TR_ID}</th>
				<th>{$TR_DOMAIN}</th>
				<th>{$TR_HP}</th>
				<th>{$TR_USER}</th>
				<th>{$TR_STATUS}</th>
				<th>{$TR_ACTION}</th>
			</tr>
			{section name=i loop=$ID} 
			<tr>
				<td>{$ID[i]}</td>
				<td>{$DOMAIN[i]}</td>
				<td>{$HP[i]}</td>
				<td>{$USER[i]}</td>
				<td>{$STATUS[i]}</td>
				<td>
					<a href="{$LINK[i]}" title="{$TR_ADD}" class="icon i_add_user"></a>
					<a href="#" onclick="delete_order('orders_delete.php?order_id={$ID[i]}', '{$DOMAIN[i]}')" title="{$TR_DELETE}" class="icon i_delete"></a>
				</td>
			</tr>
			{/section}
		</table>
		{/if}
		<div class="paginator">
			{if !isset($SCROLL_NEXT_GRAY)}
			<span class="icon i_next_gray">&nbsp;</span>
			{/if}
			{if !isset($SCROLL_NEXT)}
			<a href="orders.php?psi={$NEXT_PSI}" title="next" class="icon i_next">next</a>
			{/if}
			{if !isset($SCROLL_PREV_GRAY)}
			<span class="icon i_prev_gray">&nbsp;</span>
			{/if}
			{if !isset($SCROLL_PREV)}
			<a href="orders.php?psi={$PREV_PSI}" title="previous" class="icon i_prev">previous</a>
			{/if}
		</div>
	</div>
{include file='reseller/footer.tpl'}