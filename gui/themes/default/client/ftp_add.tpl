{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_ADD_FTP_USER}{/block}

{block name=BREADCRUMP}
<li><a href="/client/ftp_accounts.php">{$TR_MENU_FTP_ACCOUNTS}</a></li>
<li><a>{$TR_ADD_FTP_USER}</a></li>
{/block}

{block name=BODY}
<h2 class="ftp"><span>{$TR_ADD_FTP_USER}</span></h2>
<form action="/client/ftp_add.php" method="post" id="client_ftp_add">
	<table>
		<tr>
			<td>{$TR_USERNAME}</td>
			<td><input type="text" name="username" id="username" value="{$USERNAME}" /></td>
		</tr>
		<tr>
			<td><input type="radio" name="dmn_type" id="dmn_type" value="dmn" {$DMN_TYPE_CHECKED} />{$TR_TO_MAIN_DOMAIN}</td>
			<td>{$FTP_SEPARATOR}{$DOMAIN_NAME}</td>
		</tr>
		{if isset($ALS_NAME)}
		<tr>
			<td><input type="radio" name="dmn_type" id="als_type" value="als" {$ALS_TYPE_CHECKED} />{$TR_TO_DOMAIN_ALIAS}</td>
			<td>
				<select name="als_id" id="als_id">
					{section name=i loop=$ALS_NAME}
					<option value="{$ALS_ID[i]}" {$ALS_SELECTED[i]}>{$FTP_SEPARATOR}{$ALS_NAME[i]}</option>
					{/section}

				</select>
			</td>
		</tr>
		{/if}
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
		<input type="hidden" name="uaction" value="add_user" />
		<input type="submit" name="Submit" value="{$TR_ADD}" />
	</div>
</form>
{/block}