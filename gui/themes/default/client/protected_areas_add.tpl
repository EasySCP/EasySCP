{include file='client/header.tpl'}
<body>
	<script type="text/javascript">
	/* <![CDATA[ */
		$(document).ready(function() { 
			$('#protected_areas_delete').click(function() { 
				document.location.href = 'protected_areas_delete.php?id={$CDIR}';
			});
			$('#protected_user_manage').click(function() { 
				document.location.href = 'protected_user_manage.php';
			});
			$('#protected_areas').click(function() { 
				document.location.href = 'protected_areas.php';
			});

			document.forms[0].elements["users[]"].disabled = {$USER_FORM_ELEMENS};
			document.forms[0].elements["groups[]"].disabled = {$GROUP_FORM_ELEMENS};
		});

		function changeType(wath) {
			document.forms[0].elements["users[]"].disabled = wath != "user";
			document.forms[0].elements["groups[]"].disabled = wath == "user";
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
			<li><a href="protected_areas.php">{$TR_HTACCESS}</a></li>
			<li><a>{$TR_PROTECT_DIR}</a></li>
		</ul>
	</div>
	<div class="left_menu">{include file="$MENU"}</div>
	<div class="main">
		{if isset($MESSAGE)}
		<div class="{$MSG_TYPE}">{$MESSAGE}</div>
		{/if}
		<h2 class="htaccess"><span>{$TR_PROTECT_DIR}</span></h2>
		<form action="protected_areas_add.php" method="post" id="client_protected_areas_add">
			<table>
				<tr>
					<td>{$TR_PATH}</td>
					<td>
						<input type="text" name="other_dir"  id="other_dir"  value="{$PATH}" />
						<a href="#" onclick="showFileTree();" class="icon i_bc_folder">{$CHOOSE_DIR}</a>
					</td>
				</tr>
				<tr>
					<td>{$TR_AREA_NAME}</td>
					<td><input type="text" name="paname" id="paname" value="{$AREA_NAME}" /></td>
				</tr>
			</table>
			<div>&nbsp;</div>
			<table>
				<thead>
					<tr>
						<th>{$TR_USER}</th>
						<th>{$TR_GROUPS}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><input type="radio" name="ptype" id="ptype_1" value="user" onfocus="changeType('user');" {$USER_CHECKED} />&nbsp;{$TR_USER_AUTH}</td>
						<td><input type="radio" name="ptype" id="ptype_2" value="group" onfocus="changeType('group');" {$GROUP_CHECKED} />&nbsp;{$TR_GROUP_AUTH}</td>
					</tr>
					<tr>
						<td>
							<select name="users[]" id="users" multiple="multiple" size="5">
								{section name=i loop=$USER_LABEL}
							 	<option value="{$USER_VALUE[i]}" {$USER_SELECTED[i]}>{$USER_LABEL[i]}</option>
								{/section}
							</select>
						</td>
						<td>
							<select name="groups[]" id="groups" multiple="multiple" size="5">
								{section name=i loop=$GROUP_LABEL}
								<option value="{$GROUP_VALUE[i]}" {$GROUP_SELECTED[i]}>{$GROUP_LABEL[i]}</option>
								{/section}
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="buttons">
				<input type="hidden" name="cdir" value="{$CDIR}" />
				<input type="hidden" name="sub" value="YES" />
				<input type="hidden" name="use_other_dir" />
				<input type="hidden" name="uaction" value="" />
				<input type="button" name="Button" value="{$TR_PROTECT_IT}" onclick="return sbmt(document.forms[0],'protect_it');" />
				{if isset($UNPROTECT_IT)}
				<input type="button" name="protected_areas_delete" id="protected_areas_delete" value="{$TR_UNPROTECT_IT}" />
				{/if}
				<input type="button" name="protected_user_manage" id="protected_user_manage" value="{$TR_MANAGE_USRES}" />
				<input type="button" name="protected_areas" id="protected_areas" value="{$TR_CANCEL}" />
			</div>
		</form>
	</div>
{include file='client/footer.tpl'}