{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_HOSTING_PLAN_PROPERTIES}{/block}

{block name=BREADCRUMP}
<li><a href="/reseller/users.php">{$TR_MENU_OVERVIEW}</a></li>
<li><a href="/reseller/user_add1.php">{$TR_ADD_USER}</a></li>
<li><a>{$TR_HOSTING_PLAN_PROPERTIES}</a></li>
{/block}

{block name=BODY}
<h2 class="user"><span>{$TR_ADD_USER}</span></h2>
{if !isset($ADD_USER)}
<form action="/reseller/user_add2.php" method="post"  id="reseller_user_add2">
	<fieldset>
		<legend>{$TR_HOSTING_PLAN_PROPERTIES}</legend>
		<table>
			<tr>
				<td>{$TR_TEMPLATE_NAME}</td>
				<td><input type="hidden" name="template" id="template" value="{$VL_TEMPLATE_NAME}" />{$VL_TEMPLATE_NAME}</td>
			</tr>
			<tr>
				<td>{$TR_MAX_SUBDOMAIN}</td>
				<td><input type="text" name="nreseller_max_subdomain_cnt" id="nreseller_max_subdomain_cnt" value="{$MAX_SUBDMN_CNT}" /></td>
			</tr>
			<tr>
				<td>{$TR_MAX_DOMAIN_ALIAS}</td>
				<td><input type="text" name="nreseller_max_alias_cnt" id="nreseller_max_alias_cnt" value="{$MAX_DMN_ALIAS_CNT}" /></td>
			</tr>
			<tr>
				<td>{$TR_MAX_MAIL_COUNT}</td>
				<td><input type="text" name="nreseller_max_mail_cnt" id="nreseller_max_mail_cnt" value="{$MAX_MAIL_CNT}"/></td>
			</tr>
			<tr>
				<td>{$TR_MAX_FTP}</td>
				<td><input type="text" name="nreseller_max_ftp_cnt" id="nreseller_max_ftp_cnt" value="{$MAX_FTP_CNT}" /></td>
			</tr>
			<tr>
				<td>{$TR_MAX_SQL_DB}</td>
				<td><input type="text" name="nreseller_max_sql_db_cnt" id="nreseller_max_sql_db_cnt" value="{$MAX_SQL_CNT}"/></td>
			</tr>
			<tr>
				<td>{$TR_MAX_SQL_USERS}</td>
				<td><input type="text" name="nreseller_max_sql_user_cnt" id="nreseller_max_sql_user_cnt" value="{$VL_MAX_SQL_USERS}"/></td>
			</tr>
			<tr>
				<td>{$TR_MAX_TRAFFIC}</td>
				<td><input type="text" name="nreseller_max_traffic" id="nreseller_max_traffic" value="{$VL_MAX_TRAFFIC}"/></td>
			</tr>
			<tr>
				<td>{$TR_MAX_DISK_USAGE}</td>
				<td><input type="text" name="nreseller_max_disk" id="nreseller_max_disk" value="{$VL_MAX_DISK_USAGE}"/></td>
			</tr>
			<tr>
				<td>{$TR_PHP}</td>
				<td>
					<input type="radio" name="php" id="php_yes" value="_yes_" {$VL_PHPY} />&nbsp;{$TR_YES}
					<input type="radio" name="php" id="php_no" value="_no_" {$VL_PHPN} />&nbsp;{$TR_NO}
				</td>
			</tr>
			<tr>
				<td>{$TR_PHP_EDIT}</td>
				<td>
					<input type="radio" name="php_edit" id="php_edit_yes" value="_yes_" {$VL_PHPEY} />&nbsp;{$TR_YES}
					<input type="radio" name="php_edit" id="php_edit_no" value="_no_" {$VL_PHPEN} />&nbsp;{$TR_NO}
				</td>
			</tr>
			<tr>
				<td>{$TR_CGI}</td>
				<td>
					<input type="radio" name="cgi" id="cgi_yes" value="_yes_" {$VL_CGIY} />&nbsp;{$TR_YES}
					<input type="radio" name="cgi" id="cgi_no" value="_no_" {$VL_CGIN} />&nbsp;{$TR_NO}
				</td>
			</tr>
			<tr>
				<td>{$TR_SSL}</td>
				<td>
					<input type="radio" name="ssl" id="ssl_yes" value="_yes_" {$VL_SSLY} />&nbsp;{$TR_YES}
					<input type="radio" name="ssl" id="ssl_no" value="_no_" {$VL_SSLN} />&nbsp;{$TR_NO}
				</td>
			</tr>
			<tr>
				<td>{$TR_DNS}</td>
				<td>
					<input type="radio" name="dns" id="dns_yes" value="_yes_" {$VL_DNSY} />&nbsp;{$TR_YES}
					<input type="radio" name="dns" id="dns_no" value="_no_" {$VL_DNSN} />&nbsp;{$TR_NO}
				</td>
			</tr>
			<tr>
				<td>{$TR_BACKUP}</td>
				<td>
					<input type="radio" name="backup" id="backup_dmn" value="_dmn_" {$VL_BACKUPD} />&nbsp;{$TR_BACKUP_DOMAIN}
					<input type="radio" name="backup" id="backup_sql" value="_sql_" {$VL_BACKUPS} />&nbsp;{$TR_BACKUP_SQL}
					<input type="radio" name="backup" id="backup_full" value="_full_" {$VL_BACKUPF} />&nbsp;{$TR_BACKUP_FULL}
					<input type="radio" name="backup" id="backup_no" value="_no_" {$VL_BACKUPN} />&nbsp;{$TR_BACKUP_NO}
				</td>
			</tr>
			<tr>
				<td>{$TR_BACKUP_COUNT}</td>
				<td>
					<input type="radio" name="countbackup" id="countbackup_yes" value="_yes_" {$TR_BACKUPCOUNT_YES} />&nbsp;{$TR_YES}
					<input type="radio" name="countbackup" id="countbackup_no" value="_no_" {$TR_BACKUPCOUNT_NO} />&nbsp;{$TR_NO}
				</td>
			</tr>
		</table>
	</fieldset>
	<div class="buttons">
		<input type="hidden" name="uaction" value="user_add2_nxt" />
		<input type="submit" name="Submit" value="{$TR_NEXT_STEP}" />
	</div>
</form>
{/if}
{/block}