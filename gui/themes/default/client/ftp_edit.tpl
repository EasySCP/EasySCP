{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_EDIT_FTP_USER}{/block}

{block name=BREADCRUMP}
<li><a href="/client/ftp_accounts.php">{$TR_MENU_FTP_ACCOUNTS}</a></li>
<li><a>{$TR_EDIT_FTP_USER}</a></li>
{/block}

{block name=BODY}
<h2 class="ftp"><span>{$TR_EDIT_FTP_USER}</span></h2>
<form action="/client/ftp_edit.php" method="post" id="client_ftp_edit">
	<table>
		<tr>
			<td>{$TR_FTP_ACCOUNT}</td>
			<td><input type="text" name="username" id="ftp_account" value="{$FTP_ACCOUNT}"  readonly="readonly"/></td>
		</tr>
		<tr>
			<td>{$TR_PASSWORD}</td>
			<td><input type="password" name="pass" id="pass" value="" /></td>
		</tr>
		<tr>
			<td>{$TR_PASSWORD_REPEAT}</td>
			<td><input type="password" name="pass_rep" id="pass_rep" value="" /></td>
		</tr>
		<tr>
			<td><input type="checkbox" name="use_other_dir" id="use_other_dir" {$USE_OTHER_DIR_CHECKED} />&nbsp;{$TR_USE_OTHER_DIR}</td>
			<td><input type="text" name="other_dir" id="other_dir" value="{$OTHER_DIR}" /><a href="#" onclick="showFileTree();" class="icon i_bc_folder">{$CHOOSE_DIR}</a></td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="id" value="{$ID}" />
		<input type="hidden" name="uaction" value="edit_user" />
		<input type="submit" name="Submit" value="{$TR_EDIT}" />
	</div>
</form>
{/block}