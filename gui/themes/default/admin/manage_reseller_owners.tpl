{include file='admin/header.tpl'}
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
			<li><a href="../index.php?logout" class="logout">{$TR_MENU_LOGOUT}</a></li>
		</ul>
		<ul class="path">
			<li><a href="manage_users.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a>{$TR_RESELLER_ASSIGNMENT}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="users2"><span>{$TR_RESELLER_ASSIGNMENT}</span></h2>
		{if isset($NUMBER)}
		<form action="manage_reseller_owners.php" method="post" id="admin_reseller_assignment">
			<table>
				<tr>
					<th>{$TR_NUMBER}</th>
					<th>{$TR_MARK}</th>
					<th>{$TR_RESELLER_NAME}</th>
					<th>{$TR_OWNER}</th>
				</tr>
				{section name=i loop=$NUMBER}
				<tr>
					<td>{$NUMBER[i]}</td>
					<td><input id="{$CKB_NAME[i]}" type="checkbox" name="{$CKB_NAME[i]}" /></td>
					<td><label for="{$CKB_NAME[i]}">{$RESELLER_NAME[i]}</label></td>
					<td>{$OWNER[i]}</td>
				</tr>
				{/section}
			</table>
			{if isset($VALUE)}
			<div class="buttons">
				{$TR_TO_ADMIN}
				<select name="dest_admin">
					{section name=i loop=$VALUE}
					<option {$SELECTED[i]} value="{$VALUE[i]}">{$OPTION[i]}</option>
					{/section}
				</select>
				<input type="hidden" name="uaction" value="reseller_owner" />
				<input type="submit" name="Submit" value="{$TR_MOVE}" />
			</div>
			{/if}
		</form>
		{/if}
	</div>
{include file='admin/footer.tpl'}