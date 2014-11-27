<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en">
<head>
	<title>{block name=TR_PAGE_TITLE}EasySCP{/block}</title>
	<meta http-equiv='Content-Style-Type' content='text/css' />
	<meta http-equiv='Content-Script-Type' content='text/javascript' />
	<meta http-equiv='Content-Type' content='text/html; charset={$THEME_CHARSET}' />
	<meta name='copyright' content='EasySCP' />
	<meta name='owner' content='EasySCP' />
	<meta name='publisher' content='EasySCP' />
	<meta name='robots' content='nofollow, noindex' />
	<meta name='title' content='{block name=TR_PAGE_TITLE}EasySCP{/block}' />
	<link href="{$THEME_COLOR_PATH}/css/easyscp.css" rel="stylesheet" type="text/css" />
	<link href="{$THEME_COLOR_PATH}/css/jquery.ui.css" rel="stylesheet" type="text/css" />
	<link href="{$THEME_COLOR_PATH}/css/jquery.easyscp.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="{$THEME_SCRIPT_PATH}/jquery.js"></script>
	<script type="text/javascript" src="{$THEME_SCRIPT_PATH}/jquery.ui.js"></script>
	<script type="text/javascript" src="{$THEME_SCRIPT_PATH}/jquery.easyscp.js"></script>
	<script type="text/javascript" src="{$THEME_SCRIPT_PATH}/easyscp.js"></script>
	<!--[if lt IE 7.]>
	<script defer type="text/javascript" src="{$THEME_SCRIPT_PATH}/pngfix.js"></script>
	<![endif]-->
	<script type="text/javascript">
	/* <![CDATA[ */
		$(document).ready(function(){
			initMenu();
			initMain();
		});
	/* ]]> */
	</script>
	{block name=CUSTOM_JS}{/block}
</head>
<body>
	<div class="header">
	{include file="$MAIN_MENU"}
		<div class="logo">
			<img src="{$THEME_COLOR_PATH}/images/easyscp_logo.png" alt="EasySCP logo" />
			<!-- <img src="{$THEME_COLOR_PATH}/images/easyscp_webhosting.png" alt="EasySCP - Easy Server Control Panel" /> -->
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
			{block name=BREADCRUMP}&nbsp;{/block}
		</ul>
	</div>

	<div class="left_menu">{include file="$MENU"}</div>

	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		{block name=BODY}{/block}
	</div>

	<div class="footer">
		EasySCP {$VERSION}<br />
		build: {$BUILDDATE}<br />
	{if isset($DEBUG)}
		Debug Mode: <strong style="color: red;">On</strong>
	{/if}
	</div>
	{if isset($GUI_DEBUG)}
		{$GUI_DEBUG}
	{/if}
</body>
</html>