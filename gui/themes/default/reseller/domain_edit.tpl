{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function(){
		// Datepicker - begin
		$('#dmn_exp_never').change(function() {
			var dmn_exp_date = $('#dmn_exp_date');
			if ($(this).is(':checked')) {
				dmn_exp_date.attr('disabled', 'disabled');
			} else {
				dmn_exp_date.removeAttr('disabled');
			}
		});
		// Datepicker - end

		// jQuery UI Datepicker
		$('#dmn_exp_date').datepicker({
			dateFormat: '{$VL_DATE_FORMAT}',
			dayNamesMin: ['{$TR_SU}', '{$TR_MO}', '{$TR_TU}', '{$TR_WE}', '{$TR_TH}', '{$TR_FR}', '{$TR_SA}'],
			monthNames: ['{$TR_JANUARY}', '{$TR_FEBRUARY}', '{$TR_MARCH}', '{$TR_APRIL}', '{$TR_MAY}', '{$TR_JUNE}', '{$TR_JULY}', '{$TR_AUGUST}', '{$TR_SEPTEMBER}', '{$TR_OCTOBER}', '{$TR_NOVEMBER}', '{$TR_DECEMBER}'],
			isRTL: false,
			showOtherMonths: true,
			defaultDate: '+1y',
			minDate: new Date()
		});
		jQuery('#ui-datepicker-div').hide();
	});
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_EDIT_DOMAIN}{/block}

{block name=BREADCRUMP}
<li><a href="/reseller/users.php">{$TR_MENU_MANAGE_USERS}</a></li>
<li><a href="/reseller/users.php">{$TR_MENU_OVERVIEW}</a></li>
<li><a>{$TR_EDIT_DOMAIN}</a></li>
{/block}

{block name=BODY}
<h2 class="domains"><span>{$TR_EDIT_DOMAIN}</span></h2>
<form action="/reseller/domain_edit.php" method="post" id="admin_domain_edit">
	<fieldset>
		<legend>{$TR_DOMAIN_PROPERTIES}</legend>
		<table>
			<tr>
				<td>{$TR_DOMAIN_NAME}</td>
				<td>{$VL_DOMAIN_NAME}</td>
			</tr>
			<tr>
				<td>{$TR_DOMAIN_EXPIRE}</td>
				<td>
					<input type="text" name="dmn_expire_date" id="dmn_exp_date" value="{$VL_DOMAIN_EXPIRE}" {$VL_EXPIRE_DATE_DISABLED} />
					&nbsp;{$TR_EXPIRE_CHECKBOX} <input type="checkbox" name="dmn_expire_never" id="dmn_exp_never" {$VL_EXPIRE_NEVER_SELECTED} />
				</td>
			</tr>
			<tr>
				<td>{$TR_DOMAIN_IP}</td>
				<td>{$VL_DOMAIN_IP}</td>
					<!--
					<select name="domain_ip">
						<option value="{$IP_VALUE}" {$IP_SELECTED}>{$IP_NUM}&nbsp;({$IP_NAME})</option>
					</select>
					-->
			</tr>
			<tr>
				<td>{$TR_PHP_SUPP}</td>
				<td>
					<select name="domain_php" id="domain_php">
						<option value="_yes_" {$PHP_YES}>{$TR_YES}</option>
						<option value="_no_" {$PHP_NO}>{$TR_NO}</option>
					</select>
				</td>
			</tr>
			<tr style="display:none;">
				<td>{$TR_PHP_EDIT}</td>
				<td>
					<select name="domain_php_edit" id="domain_php_edit">
						<option value="_yes_" {$PHP_EDIT_YES}>{$TR_YES}</option>
						<option value="_no_" {$PHP_EDIT_NO}>{$TR_NO}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{$TR_CGI_SUPP}</td>
				<td>
					<select name="domain_cgi" id="domain_cgi">
						<option value="_yes_" {$CGI_YES}>{$TR_YES}</option>
						<option value="_no_" {$CGI_NO}>{$TR_NO}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{$TR_SSL_SUPP}</td>
				<td>
					<select name="domain_ssl" id="domain_ssl">
						<option value="_yes_" {$SSL_YES}>{$TR_YES}</option>
						<option value="_no_" {$SSL_NO}>{$TR_NO}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{$TR_DNS_SUPP}</td>
				<td>
					<select name="domain_dns" id="domain_dns">
						<option value="_yes_" {$DNS_YES}>{$TR_YES}</option>
						<option value="_no_" {$DNS_NO}>{$TR_NO}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{$TR_BACKUP}</td>
				<td>
					<select name="backup" id="backup">
						<option value="_dmn_" {$BACKUP_DOMAIN}>{$TR_BACKUP_DOMAIN}</option>
						<option value="_sql_" {$BACKUP_SQL}>{$TR_BACKUP_SQL}</option>
						<option value="_full_" {$BACKUP_FULL}>{$TR_BACKUP_FULL}</option>
						<option value="_no_" {$BACKUP_NO}>{$TR_BACKUP_NO}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{$TR_BACKUP_COUNT}</td>
				<td>
					<input type="radio" name="countbackup" id="countbackup_yes" value="_yes_" {$BACKUPCOUNT_YES} />&nbsp;{$TR_YES}
					<input type="radio" name="countbackup" id="countbackup_no" value="_no_" {$BACKUPCOUNT_NO} />&nbsp;{$TR_NO}
				</td>
			</tr>
			<tr>
				<td>{$TR_SUBDOMAINS}</td>
				<td><input type="text" name="dom_sub" id="dom_sub" value="{$VL_DOM_SUB}"/></td>
			</tr>
			<tr>
				<td>{$TR_ALIAS}</td>
				<td><input type="text" name="dom_alias" id="dom_alias" value="{$VL_DOM_ALIAS}"/></td>
			</tr>
			<tr>
				<td>{$TR_MAIL_ACCOUNT}</td>
				<td><input type="text" name="dom_mail_acCount" id="dom_mail_acCount" value="{$VL_DOM_MAIL_ACCOUNT}"/></td>
			</tr>
			<tr>
				<td>{$TR_FTP_ACCOUNTS}</td>
				<td><input type="text" name="dom_ftp_acCounts" id="dom_ftp_acCounts" value="{$VL_FTP_ACCOUNTS}"/></td>
			</tr>
			<tr>
				<td>{$TR_SQL_DB}</td>
				<td><input type="text" name="dom_sqldb" id="dom_sqldb" value="{$VL_SQL_DB}"/></td>
			</tr>
			<tr>
				<td>{$TR_SQL_USERS}</td>
				<td><input type="text" name="dom_sql_users" id="dom_sql_users" value="{$VL_SQL_USERS}"/></td>
			</tr>
			<tr>
				<td>{$TR_TRAFFIC}</td>
				<td><input type="text" name="dom_traffic" id="dom_traffic" value="{$VL_TRAFFIC}"/></td>
			</tr>
			<tr>
				<td>{$TR_DISK}</td>
				<td><input type="text" name="dom_disk" id="dom_disk" value="{$VL_DOM_DISK}"/></td>
			</tr>
			<tr>
				<td>{$TR_USER_NAME}</td>
				<td>{$VL_USER_NAME}</td>
			</tr>
		</table>
	</fieldset>
	<div class="buttons">
		<input type="hidden" name="uaction" value="sub_data" />
		<input type="submit" name="Submit" value="{$TR_UPDATE_DATA}" />
	</div>
</form>
{/block}