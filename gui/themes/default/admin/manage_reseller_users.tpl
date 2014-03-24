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
			<li><a>{$TR_USER_ASSIGNMENT}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="users2"><span>{$TR_USER_ASSIGNMENT}</span></h2>
		<form action="manage_reseller_users.php" method="post" id="admin_user_assignment">
			{if isset($SRC_RSL_VALUE)}
			<div class="buttons">
				{$TR_FROM_RESELLER}
				<select name="src_reseller" onchange="return sbmt(document.forms[0],'change_src');">
					{section name=i loop=$SRC_RSL_VALUE}
					<option {$SRC_RSL_SELECTED[i]} value="{$SRC_RSL_VALUE[i]}">{$SRC_RSL_OPTION[i]}</option>
					{/section}
				</select>
			</div>
			{/if}
			{if isset($USER_NAME)}
			<table>
				<thead>
					<tr>
						<th>{$TR_NUMBER}</th>
						<th>{$TR_MARK}</th>
						<th>{$TR_USER_NAME}</th>
					</tr>
				</thead>
				<tbody>
					{section name=i loop=$USER_NAME}
					<tr>
						<td>{$NUMBER[i]}</td>
						<td><input id="{$CKB_NAME[i]}" type="checkbox" name="{$CKB_NAME[i]}" /></td>
						<td><label for="{$CKB_NAME[i]}">{$USER_NAME[i]}</label></td>
					</tr>
					{/section}
				</tbody>
			</table>
			{/if}
			{if isset($DST_RSL_VALUE)}
			<div class="buttons">
				{$TR_TO_RESELLER}
				<select name="dst_reseller">
					{section name=i loop=$DST_RSL_VALUE}
					<option {$DST_RSL_SELECTED[i]} value="{$DST_RSL_VALUE[i]}">{$DST_RSL_OPTION[i]}</option>
					{/section}
				</select>
				<input type="hidden" name="uaction" value="move_user" />
				<input type="submit" name="Submit" value="{$TR_MOVE}" />
			</div>
			{/if}
		</form>
	</div>
{include file='admin/footer.tpl'}