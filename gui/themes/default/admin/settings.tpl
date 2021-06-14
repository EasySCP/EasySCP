{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function(){
		// Tooltips - begin
		$('#tld_help').EasySCPtooltips({ msg:"{$TR_TLD_STRICT_VALIDATION_HELP}"});
		$('#sld_help').EasySCPtooltips({ msg:"{$TR_SLD_STRICT_VALIDATION_HELP}"});
		// Tooltips - end
	});
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_GENERAL_SETTINGS}{/block}

{block name=BREADCRUMP}
<li><a href="/admin/settings.php">{$TR_MENU_SETTINGS}</a></li>
<li><a>{$TR_GENERAL_SETTINGS}</a></li>
{/block}

{block name=BODY}
<h2 class="settings"><span>{$TR_SETTINGS}</span></h2>
<form action="/admin/settings.php" method="post" id="frmsettings">
	<fieldset>
		<legend>{$TR_CHECK_FOR_UPDATES}</legend>
		<table>
			<tr>
				<td style="width: 300px;"><label for="checkforupdate">{$TR_CHECK_FOR_UPDATES}</label></td>
				<td>
					<select name="checkforupdate" id="checkforupdate">
						<option value="0" {$CHECK_FOR_UPDATES_SELECTED_OFF}>{$TR_DISABLED}</option>
						<option value="1" {$CHECK_FOR_UPDATES_SELECTED_ON}>{$TR_ENABLED}</option>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>{$TR_LOSTPASSWORD}</legend>
		<table>
			<tr>
				<td style="width: 300px;"><label for="lostpassword">{$TR_LOSTPASSWORD}</label></td>
				<td>
					<select name="lostpassword" id="lostpassword">
						<option value="0" {$LOSTPASSWORD_SELECTED_OFF}>{$TR_DISABLED}</option>
						<option value="1" {$LOSTPASSWORD_SELECTED_ON}>{$TR_ENABLED}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="lostpassword_timeout">{$TR_LOSTPASSWORD_TIMEOUT}</label></td>
				<td><input type="text" name="lostpassword_timeout" id="lostpassword_timeout" value="{$LOSTPASSWORD_TIMEOUT_VALUE}"/></td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>{$TR_PASSWORD_SETTINGS}</legend>
		<table>
			<tr>
				<td style="width: 300px;"><label for="passwd_strong">{$TR_PASSWD_STRONG}</label></td>
				<td>
					<select name="passwd_strong" id="passwd_strong">
						<option value="0" {$PASSWD_STRONG_OFF}>{$TR_DISABLED}</option>
						<option value="1" {$PASSWD_STRONG_ON}>{$TR_ENABLED}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="passwd_chars">{$TR_PASSWD_CHARS}</label></td>
				<td><input type="text" name="passwd_chars" id="passwd_chars" value="{$PASSWD_CHARS}" maxlength="2" /></td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>{$TR_BRUTEFORCE}</legend>
		<table>
			<tr>
				<td style="width: 300px;"><label for="bruteforce">{$TR_BRUTEFORCE}</label></td>
				<td>
					<select name="bruteforce" id="bruteforce">
						<option value="0" {$BRUTEFORCE_SELECTED_OFF}>{$TR_DISABLED}</option>
						<option value="1" {$BRUTEFORCE_SELECTED_ON}>{$TR_ENABLED}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="bruteforce_between">{$TR_BRUTEFORCE_BETWEEN}</label></td>
				<td>
					<select name="bruteforce_between" id="bruteforce_between">
						<option value="0" {$BRUTEFORCE_BETWEEN_SELECTED_OFF}>{$TR_DISABLED}</option>
						<option value="1" {$BRUTEFORCE_BETWEEN_SELECTED_ON}>{$TR_ENABLED}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="bruteforce_max_login">{$TR_BRUTEFORCE_MAX_LOGIN}</label></td>
				<td><input type="text" name="bruteforce_max_login" id="bruteforce_max_login" value="{$BRUTEFORCE_MAX_LOGIN_VALUE}" maxlength="3" /></td>
			</tr>
			<tr>
				<td><label for="bruteforce_block_time">{$TR_BRUTEFORCE_BLOCK_TIME}</label></td>
				<td><input name="bruteforce_block_time" type="text" id="bruteforce_block_time" value="{$BRUTEFORCE_BLOCK_TIME_VALUE}" maxlength="3" /></td>
			</tr>
			<tr>
				<td><label for="bruteforce_between_time">{$TR_BRUTEFORCE_BETWEEN_TIME}</label></td>
				<td><input name="bruteforce_between_time" type="text" id="bruteforce_between_time" value="{$BRUTEFORCE_BETWEEN_TIME_VALUE}" maxlength="3" /></td>
			</tr>
			<tr>
				<td><label for="bruteforce_max_capcha">{$TR_BRUTEFORCE_MAX_CAPTCHA}</label></td>
				<td><input name="bruteforce_max_capcha" type="text" id="bruteforce_max_capcha" value="{$BRUTEFORCE_MAX_CAPTCHA}" maxlength="3" /></td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>{$TR_DNAMES_VALIDATION_SETTINGS}</legend>
		<table>
			<tr>
				<td style="width: 300px;"><label for="tld_strict_validation">{$TR_TLD_STRICT_VALIDATION} <span id="tld_help" class="icon i_help">&nbsp;</span></label></td>
				<td>
					<select name="tld_strict_validation" id="tld_strict_validation">
						<option value="0" {$TLD_STRICT_VALIDATION_OFF}>{$TR_DISABLED}</option>
						<option value="1" {$TLD_STRICT_VALIDATION_ON}>{$TR_ENABLED}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="sld_strict_validation">{$TR_SLD_STRICT_VALIDATION} <span id="sld_help" class="icon i_help">&nbsp;</span></label></td>
				<td>
					<select name="sld_strict_validation" id="sld_strict_validation">
						<option value="0" {$SLD_STRICT_VALIDATION_OFF}>{$TR_DISABLED}</option>
						<option value="1" {$SLD_STRICT_VALIDATION_ON}>{$TR_ENABLED}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="max_dnames_labels">{$TR_MAX_DNAMES_LABELS}</label></td>
				<td><input name="max_dnames_labels" type="text" id="max_dnames_labels" value="{$MAX_DNAMES_LABELS_VALUE}" maxlength="2" /></td>
			</tr>
			<tr>
				<td><label for="max_subdnames_labels">{$TR_MAX_SUBDNAMES_LABELS}</label></td>
				<td><input name="max_subdnames_labels" type="text" id="max_subdnames_labels" value="{$MAX_SUBDNAMES_LABELS_VALUE}" maxlength="2" /></td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>{$TR_MAIL_SETTINGS}</legend>
		<table>
			<tr>
				<td style="width: 300px;"><label for="create_default_email_addresses">{$TR_CREATE_DEFAULT_EMAIL_ADDRESSES}</label></td>
				<td>
					<select name="create_default_email_addresses" id="create_default_email_addresses">
						<option value="0" {$CREATE_DEFAULT_EMAIL_ADDRESSES_OFF}>{$TR_DISABLED}</option>
						<option value="1" {$CREATE_DEFAULT_EMAIL_ADDRESSES_ON}>{$TR_ENABLED}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="hard_mail_suspension">{$TR_HARD_MAIL_SUSPENSION}</label></td>
				<td>
					<select name="hard_mail_suspension" id="hard_mail_suspension">
						<option value="0" {$HARD_MAIL_SUSPENSION_OFF}>{$TR_DISABLED}</option>
						<option value="1" {$HARD_MAIL_SUSPENSION_ON}>{$TR_ENABLED}</option>
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>{$TR_OTHER_SETTINGS}</legend>
		<table>
			<tr>
				<td style="width: 300px;"><label for="def_language">{$TR_USER_INITIAL_LANG}</label></td>
				<td>
					<select name="def_language" id="def_language">
						{section name=i loop=$LANG_NAME}
						<option value="{$LANG_VALUE[i]}" {$LANG_SELECTED[i]}>{$LANG_NAME[i]}</option>
						{/section}
					</select>
				</td>
			</tr>
			<tr>
				<td style="width: 300px;"><label for="def_theme">{$TR_USER_INITIAL_THEME}</label></td>
				<td>
					<select name="def_theme" id="def_theme">
						{section name=i loop=$THEME_NAME}
						<option value="{$THEME_VALUE[i]}" {$THEME_SELECTED[i]}>{$THEME_NAME[i]}</option>
						{/section}
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="support_system">{$TR_SUPPORT_SYSTEM}</label></td>
				<td>
					<select name="support_system" id="support_system">
						<option value="0" {$SUPPORT_SYSTEM_SELECTED_OFF}>{$TR_DISABLED}</option>
						<option value="1" {$SUPPORT_SYSTEM_SELECTED_ON}>{$TR_ENABLED}</option>
					</select>
				</td>
			</tr>
			<!--
			<tr>
				<td><label for="hosting_plan_level">{$TR_HOSTING_PLANS_LEVEL}</label></td>
				<td>
					<select name="hosting_plan_level" id="hosting_plan_level">
						<option value="admin" {$HOSTING_PLANS_LEVEL_ADMIN}>{$TR_ADMIN}</option>
						<option value="reseller" {$HOSTING_PLANS_LEVEL_RESELLER}>{$TR_RESELLER}</option>
					</select>
				</td>
			</tr>
			-->
			<tr>
				<td><label for="domain_rows_per_page">{$TR_DOMAIN_ROWS_PER_PAGE}</label></td>
				<td><input type="text" name="domain_rows_per_page" id="domain_rows_per_page" value="{$DOMAIN_ROWS_PER_PAGE}" maxlength="3" /></td>
			</tr>
			<tr>
				<td><label for="log_level">{$TR_LOG_LEVEL}</label></td>
				<td>
					<select name="log_level" id="log_level">
						<option value="E_USER_OFF" {$LOG_LEVEL_SELECTED_OFF}>{$TR_E_USER_OFF}</option>
						<option value="E_USER_ERROR" {$LOG_LEVEL_SELECTED_ERROR}>{$TR_E_USER_ERROR}</option>
						<option value="E_USER_WARNING" {$LOG_LEVEL_SELECTED_WARNING}>{$TR_E_USER_WARNING}</option>
						<option value="E_USER_NOTICE" {$LOG_LEVEL_SELECTED_NOTICE}>{$TR_E_USER_NOTICE}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td style="width: 300px;"><label for="php_version">{$TR_USER_PHP_VERSION}</label></td>
				<td>
					<select name="php_version" id="php_version">
						{section name=i loop=$PHP_VERSION_NAME}
						<option value="{$PHP_VERSION_VALUE[i]}" {$PHP_VERSION_SELECTED[i]}>{$PHP_VERSION_NAME[i]}</option>
						{/section}
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	<div class="buttons">
		<input type="hidden" name="uaction" value="apply" />
		<input type="submit" name="Submit" value="{$TR_APPLY_CHANGES}" />
	</div>
</form>
{/block}