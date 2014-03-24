{include file='reseller/header.tpl'}
<body>
	<div class="header">
		{include file="$MAIN_MENU"}
		<div class="logo">
			<img src="{$THEME_COLOR_PATH}/images/easyscp_logo.png" alt="EasySCP logo" />
			<img src="{$THEME_COLOR_PATH}/images/easyscp_webhosting.png" alt="EasySCP - Easy Server Control Panel" />
		</div>
	</div>
	<div class="location">
		<ul class="location-menu">
			<!-- <li><a class="help" href="#">Help</a></li> -->
			{if isset($YOU_ARE_LOGGED_AS)}
			<li><a href="change_user_interface.php?action=go_back" class="backadmin">{$YOU_ARE_LOGGED_AS}</a></li>
			{/if}
			<li><a href="../index.php?logout" class="logout">{$TR_MENU_LOGOUT}</a></li>
		</ul>
		<ul class="path">
			<li><a href="user_statistics.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a>{$IP_USAGE}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="ip"><span>{$IP_USAGE}</span></h2>
		{section name=i loop=$IP}
		<table>
			<tr>
				<th>{$IP[i]}</th>
			</tr>
			<tr>
				<td><b>{$RECORD_COUNT[i]}</b></td>
			</tr>
		</table>
		<br />
		{if isset($DOMAIN_NAME[i])}
		<table>
			<tr>
				<th>{$TR_DOMAIN_NAME}</th>
			</tr>
			{section name=domain loop=$DOMAIN_NAME[i]}
			<tr>
				<td>{$DOMAIN_NAME[i][domain]}</td>
			</tr>
			{/section}
		</table>
		<br /><br />
		{/if}
		{/section}
	</div>
{include file='reseller/footer.tpl'}