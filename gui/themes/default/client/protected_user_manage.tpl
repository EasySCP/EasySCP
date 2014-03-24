{include file='client/header.tpl'}
<body>
	<script type="text/javascript">
	/* <![CDATA[ */
		$(document).ready(function() {
			$('#protected_user_add').click(function() {
				document.location.href = 'protected_user_add.php';
			});
			$('#protected_group_add').click(function() {
				document.location.href = 'protected_group_add.php';
			});
		}); 

		function action_delete(url, subject) {
			if (!confirm(sprintf("{$TR_MESSAGE_DELETE}", subject))){
				return false;
			}
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
			<li><a href="webtools.php">{$TR_MENU_OVERVIEW}</a></li>
			<li><a>{$TR_HTACCESS_USER}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="users"><span>{$TR_USER_MANAGE}</span></h2>
		{if isset($UNAME)}
		<table>
			<thead>
				<tr>
					<th>{$TR_USERNAME}</th>
					<th>{$TR_STATUS}</th>
					<th>{$TR_ACTION}</th>
				</tr>
			</thead>
			<tbody>
				{section name=i loop=$UNAME}
				<tr>
					<td>{$UNAME[i]}</td>
					<td>{$USTATUS[i]}</td>
					<td>
						<a href="protected_user_assign.php?uname={$USER_ID[i]}" title="{$TR_GROUP}" class="icon i_users"></a>
						<a href="{$USER_EDIT_SCRIPT[i]}" title="{$USER_EDIT[i]}" class="icon i_edit"></a>
						<a href="#" onclick="{$USER_DELETE_SCRIPT[i]}" title="{$USER_DELETE[i]}" class="icon i_delete"></a>
					</td>
				</tr>
				{/section}
			</tbody>
		</table>
		{else}
		<div class="info">{$USER_MESSAGE}</div>
		{/if}
		<div class="buttons">
			<input type="button" name="protected_user_add" id="protected_user_add" value="{$TR_ADD_USER}" />
		</div>
		<h2 class="groups"><span>{$TR_GROUPS}</span></h2>
		{if isset($GNAME)}
		<table>
			<thead>
				<tr>
					<th>{$TR_GROUPNAME}</th>
					<th>{$TR_GROUP_MEMBERS}</th>
					<th>{$TR_STATUS}</th>
					<th>{$TR_ACTION}</th>
				</tr>
			</thead>
			<tbody>
				{section name=i loop=$GNAME}
				<tr>
					<td>{$GNAME[i]}</td>
					<td>{$MEMBER[i]}</td>
					<td>{$GSTATUS[i]}</td>
					<td>
						<a href="#" onclick="{$GROUP_DELETE_SCRIPT[i]}" title="{$GROUP_DELETE[i]}" class="icon i_delete"></a>
					</td>
				</tr>
				{/section}
			</tbody>
		</table>
		{else}
		<div class="info">{$GROUP_MESSAGE}</div>
		{/if}
		<div class="buttons">
			<input type="button" name="protected_group_add" id="protected_group_add" value="{$TR_ADD_GROUP}" />
		</div>
	</div>
{include file='client/footer.tpl'}