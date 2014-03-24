{include file='client/header.tpl'}
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
			{if isset($YOU_ARE_LOGGED_AS)}
			<li><a href="change_user_interface.php?action=go_back" class="backadmin">{$YOU_ARE_LOGGED_AS}</a></li>
			{/if}
			<li><a href="../index.php?logout" class="logout">{$TR_MENU_LOGOUT}</a></li>
		</ul>
		<ul class="path">
			<li><a href="mail_accounts.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a>{$TR_ENABLE_MAIL_AUTORESPONDER}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="support"><span>{$TR_ENABLE_MAIL_AUTORESPONDER}</span></h2>
		<form action="" method="post" id="client_mail_autoresponder_enable">
			<fieldset>
				<legend>{$TR_ARSP_MESSAGE}</legend>
				<textarea name="arsp_message" cols="50" rows="15"></textarea>
			</fieldset>
			<div class="buttons">
				<input type="hidden" name="id" value="{$ID}" />				
				<input type="hidden" name="uaction" value="enable_arsp" />
				<input type="submit" name="Submit" value="{$TR_ENABLE}" />
			</div>
		</form>
	</div>
{include file='client/footer.tpl'}