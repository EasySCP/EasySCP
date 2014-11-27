{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_EDIT_USER}{/block}

{block name=BREADCRUMP}
<li><a href="/reseller/users.php">{$TR_MENU_MANAGE_USERS}</a></li>
<li><a href="/reseller/users.php">{$TR_MENU_OVERVIEW}</a></li>
<li><a>{$TR_EDIT_USER}</a></li>
{/block}

{block name=BODY}
<h2 class="general"><span>{$TR_MANAGE_USERS}</span></h2>
<form action="/reseller/user_edit.php" method="post" id="reseller_user_edit">
	<fieldset>
		<legend>{$TR_CORE_DATA}</legend>
		<table>
			<tr>
				<td>{$TR_USERNAME}</td>
				<td>{$VL_USERNAME}</td>
			</tr>
			<tr>
				<td>{$TR_PASSWORD}</td>
				<td><input type="password" name="userpassword" id="userpassword" value="{$VAL_PASSWORD}"/></td>
			</tr>
			<tr>
				<td>{$TR_REP_PASSWORD}</td>
				<td><input type="password" name="userpassword_repeat" id="userpassword_repeat" value="{$VAL_PASSWORD}"/></td>
			</tr>
			<tr>
				<td>{$TR_USREMAIL}</td>
				<td><input type="text" name="useremail" id="useremail" value="{$VL_MAIL}"/></td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>{$TR_ADDITIONAL_DATA}</legend>
		<table>
			<tr>
				<td>{$TR_CUSTOMER_ID}</td>
				<td><input type="text" name="useruid" id="useruid" value="{$VL_USR_ID}"/></td>
			</tr>
			<tr>
				<td>{$TR_FIRSTNAME}</td>
				<td><input type="text" name="userfname" id="userfname" value="{$VL_USR_NAME}"/></td>
			</tr>
			<tr>
				<td>{$TR_LASTNAME}</td>
				<td><input type="text" name="userlname" id="userlname" value="{$VL_LAST_USRNAME}"/></td>
			</tr>
			<tr>
				<td>{$TR_GENDER}</td>
				<td>
					<select name="gender" id="gender">
						<option value="M" {$VL_MALE}>{$TR_MALE}</option>
						<option value="F" {$VL_FEMALE}>{$TR_FEMALE}</option>
						<option value="U" {$VL_UNKNOWN}>{$TR_UNKNOWN}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{$TR_COMPANY}</td>
				<td><input type="text" name="userfirm" id="userfirm" value="{$VL_USR_FIRM}" /></td>
			</tr>
			<tr>
				<td>{$TR_STREET1}</td>
				<td><input type="text" name="userstreet1" id="userstreet1" value="{$VL_STREET1}" /></td>
			</tr>
			<tr>
				<td>{$TR_STREET2}</td>
				<td><input type="text" name="userstreet2" id="userstreet2" value="{$VL_STREET2}" /></td>
			</tr>
			<tr>
				<td>{$TR_POST_CODE}</td>
				<td><input type="text" name="userzip" id="userzip" value="{$VL_USR_POSTCODE}" /></td>
			</tr>
			<tr>
				<td>{$TR_CITY}</td>
				<td><input type="text" name="usercity" id="usercity" value="{$VL_USRCITY}" /></td>
			</tr>
			<tr>
				<td>{$TR_STATE}</td>
				<td><input type="text" name="userstate" id="userstate" value="{$VL_USRSTATE}" /></td>
			</tr>
			<tr>
				<td>{$TR_COUNTRY}</td>
				<td><input type="text" name="usercountry" id="usercountry" value="{$VL_COUNTRY}" /></td>
			</tr>
			<tr>
				<td>{$TR_PHONE}</td>
				<td><input type="text" name="userphone" id="userphone" value="{$VL_PHONE}" /></td>
			</tr>
			<tr>
				<td>{$TR_FAX}</td>
				<td><input type="text" name="userfax" id="userfax" value="{$VL_FAX}" /></td>
			</tr>
		</table>
		<div class="buttons">
			<input type="hidden" name="edit_id" value="{$EDIT_ID}" />
			<input type="hidden" name="uaction" value="save_changes" />
			<input type="checkbox" id="send_data" name="send_data" checked="checked" />&nbsp;{$TR_SEND_DATA}
			<input type="submit"  name="Submit" value="{$TR_BTN_ADD_USER}"/>
		</div>
	</fieldset>
</form>
{/block}