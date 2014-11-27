<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
		"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" xml:lang="en">
<head>
	<title>{$TR_PAGE_TITLE}</title>
	<meta http-equiv='Content-Script-Type' content='text/javascript' />
	<meta http-equiv='Content-Style-Type' content='text/css' />
	<meta http-equiv='Content-Type' content='text/html; charset={$THEME_CHARSET}' />
	<meta name='copyright' content='EasySCP' />
	<meta name='owner' content='EasySCP' />
	<meta name='publisher' content='EasySCP' />
	<meta name='robots' content='nofollow, noindex' />
	<meta name='title' content='{$TR_PAGE_TITLE}' />
	<link href="{$THEME_COLOR_PATH}/css/easyscp.css" rel="stylesheet" type="text/css" />
	<link href="{$THEME_COLOR_PATH}/css/jquery.ui.css" rel="stylesheet" type="text/css" />
	<link href="{$THEME_COLOR_PATH}/css/jquery.easyscp.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="{$THEME_SCRIPT_PATH}/jquery.js"></script>
	<script type="text/javascript" src="{$THEME_SCRIPT_PATH}/jquery.ui.js"></script>
	<script type="text/javascript" src="{$THEME_SCRIPT_PATH}/jquery.easyscp.js"></script>
	<script type="text/javascript" src="{$THEME_SCRIPT_PATH}/easyscp.js"></script>
</head>
<body style="background:none">
	<script type="text/javascript">
	/* <![CDATA[ */
		function CopyText(inputname) {
			window.opener.document.forms[0].other_dir.value = document.forms[0].elements[inputname].value;
			window.opener.document.forms[0].use_other_dir.checked = true;
			self.close();
		}
	/* ]]> */
	</script>
	{if isset($MESSAGE)}
	<div class="{$MSG_TYPE}">{$MESSAGE}</div>
	{/if}
	<h2><span>{$TR_DIRECTORY_TREE}</span></h2>
	<form action="">
		<table style="empty-cells:show">
			<thead>
				<tr>
					<th>{$TR_DIRS}</th>
					<th>{$TR__ACTION}</th>
				</tr>
			</thead>
			<tbody>
				{section name=i loop=$DIR_NAME}
				<tr>
					<td><a href="{$LINK[i]}" class="icon i_bc_{$ICON[i]}">{$DIR_NAME[i]}</a></td>
					<td>
					{if $ACTION_LINK[i] != 'no'}
					<a href="javascript:CopyText('{$CHOOSE_IT[i]}');" class="icon i_choose">{$CHOOSE}</a><input type="hidden" name="{$CHOOSE_IT[i]}" value="{$CHOOSE_IT[i]}" />
					{/if}
					</td>
				</tr>
				{/section}
			</tbody>
		</table>
	</form>
</body>
</html>