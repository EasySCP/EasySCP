{include file='client/header.tpl'}
<body>
	{literal}
	<script type="text/javascript">
	/* <![CDATA[ */
		$(document).ready(function() {
			$('#protected_areas_add').click(function() {
				document.location.href = 'protected_areas_add.php';
			});
			$('#protected_user_manage').click(function() {
				document.location.href = 'protected_user_manage.php';
			});
		});

		function action_delete(url, subject) {
			return confirm(sprintf("{$TR_MESSAGE_DELETE}", subject));
		}
	/* ]]> */
	</script>
	{/literal}
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
			<li><a>{$TR_HTACCESS}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="htaccess"><span>{$TR_HTACCESS}</span></h2>
		{if isset($AREA_PATH)}
		<table>
			<thead>
				<tr>
					<th>{$TR_HTACCESS}</th>
					<th>{$TR_STATUS}</th>
					<th>{$TR__ACTION}</th>
				</tr>
			</thead>
			<tbody>
				{section name=i loop=$AREA_PATH}
				<tr>
					<td>{$AREA_NAME[i]}<br /><em>{$AREA_PATH[i]}</em></td>
					<td>{$STATUS[i]}</td>
					<td>
						<a href="protected_areas_add.php?id={$PID[i]}" title="{$TR_EDIT}" class="icon i_edit"></a>
						<a href="protected_areas_delete.php?id={$PID[i]}" onclick="return action_delete('protected_areas_delete.php?id={$PID[i]}', '{$JS_AREA_NAME[i]}')" title="{$TR_DELETE}" class="icon i_delete"></a>
					</td>
				</tr>
				{/section}
			</tbody>
		</table>
		{/if}
		<div class="buttons">
			<input type="button" name="protected_areas_add" id="protected_areas_add" value="{$TR_ADD_AREA}" />
			<input type="button" name="protected_user_manage" id="protected_user_manage" value="{$TR_MANAGE_USRES}" />
		</div>
	</div>
{include file='client/footer.tpl'}