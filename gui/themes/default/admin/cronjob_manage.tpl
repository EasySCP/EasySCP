{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}
<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function(){
		document.forms[0].elements['minute'].disabled = false;
		document.forms[0].elements['hour'].disabled = false;
		document.forms[0].elements['day_of_month'].disabled = false;
		document.forms[0].elements['month'].disabled = false;
		document.forms[0].elements['day_of_week'].disabled = false;
		document.forms[0].elements['minute_expert'].disabled = true;
		document.forms[0].elements['hour_expert'].disabled = true;
		document.forms[0].elements['day_of_month_expert'].disabled = true;
		document.forms[0].elements['month_expert'].disabled = true;
		document.forms[0].elements['day_of_week_expert'].disabled = true;
		document.getElementById('normal_mode').style.display='block';
		document.getElementById('expert_mode').style.display='none';
	});

	function getSelected (obj) {
		var txt = "";
		var selects=0;
		for (var i = 0; i < obj.options.length; i++) {
			var isSelected = obj.options[i].selected;
			if(isSelected){
				if(selects >0){
					txt += ',';
				}
				txt += obj.options[i].value;
				selects ++;
			}
		}
		alert (txt);
	}

	function deselectAllOptions(option){
		var i;
		for(i=1;i<option.options.length;i++) {
			option.options[i].selected=false;
		}
		option.options[0].selected=true;
	}

	function switchExpertMode(obj){
		if(obj.value == 1) {
			if (!confirm("{$TR_MESSAGE_EXPERT_MODE}")){
				return false;
			}
			document.forms[0].elements['minute'].disabled = true;
			document.forms[0].elements['hour'].disabled = true;
			document.forms[0].elements['day_of_month'].disabled = true;
			document.forms[0].elements['month'].disabled = true;
			document.forms[0].elements['day_of_week'].disabled = true;
			document.forms[0].elements['minute_expert'].disabled = false;
			document.forms[0].elements['hour_expert'].disabled = false;
			document.forms[0].elements['day_of_month_expert'].disabled = false;
			document.forms[0].elements['month_expert'].disabled = false;
			document.forms[0].elements['day_of_week_expert'].disabled = false;
			document.getElementById('normal_mode').style.display='none';
			document.getElementById('expert_mode').style.display='block';
		} else {
			if (!confirm("{$TR_MESSAGE_NORMAL_MODE}")){
				return false;
			}
			deselectAllOptions(document.getElementById('minute'));
			deselectAllOptions(document.getElementById('hour'));
			deselectAllOptions(document.getElementById('month'));
			deselectAllOptions(document.getElementById('day_of_month'));
			deselectAllOptions(document.getElementById('day_of_week'));
			document.forms[0].elements['minute'].disabled = false;
			document.forms[0].elements['hour'].disabled = false;
			document.forms[0].elements['day_of_month'].disabled = false;
			document.forms[0].elements['month'].disabled = false;
			document.forms[0].elements['day_of_week'].disabled = false;
			document.forms[0].elements['minute_expert'].value = '*';
			document.forms[0].elements['hour_expert'].value = '*';
			document.forms[0].elements['day_of_month_expert'].value = '*';
			document.forms[0].elements['month_expert'].value = '*';
			document.forms[0].elements['day_of_week_expert'].value = '*';

			document.forms[0].elements['minute_expert'].disabled = true;
			document.forms[0].elements['hour_expert'].disabled = true;
			document.forms[0].elements['day_of_month_expert'].disabled = true;
			document.forms[0].elements['month_expert'].disabled = true;
			document.forms[0].elements['day_of_week_expert'].disabled = true;
			document.getElementById('normal_mode').style.display='block';
			document.getElementById('expert_mode').style.display='none';
		}
	}
	/* ]]> */
</script>
{/block}

{block name=CONTENT_HEADER}{$TR_SSL_TITLE}{/block}

{block name=BREADCRUMP}
	<li><a href="/admin/system_info.php">{$TR_MENU_SYSTEM_TOOLS}</a></li>
	<li><a>{$TR_MENU_CRONJOB_ADD}</a></li>
{/block}

{block name=BODY}
<h2 class="serverstatus"><span>{$TR_ADD_CRONJOB}</span></h2>
<form action="cronjob_manage.php" method="post" id="admin_add_cronjob">
	<fieldset>
		<table>
			<tr>
				<td>{$TR_NAME}:</td>
				<td><input name="name" type="text" class="textinput" id="name" value="{$NAME}" /></td>
			</tr>
			<tr>
				<td>{$TR_DESCRIPTION}:</td>
				<td><input name="description" type="text" class="textinput" id="description" value="{$DESCRIPTION}" /></td>
			</tr>
			<tr>
				<td>{$TR_ACTIVE}:</td>
				<td>
					<select name="active">
						<option value="yes" {$ACTIVE_YES_SELECTED}>{$TR_YES}</option>
						<option value="no"  {$ACTIVE_NO_SELECTED}>{$TR_NO}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>{$TR_COMMAND}</td>
				<td colspan="4"><input name="cron_cmd" type="text" class="textinput" id="cron_cmd" value="{$CRON_CMD}"/></td>
			</tr>
			<tr>
				<td>{$TR_USER}:</td>
				<td colspan="4"><input name="user_name" type="text" class="textinput" id="user_name" value="{$USER_NAME}" /></td>
			</tr>
			<tr>
				<td>{$TR_EXPERT_MODE}:</td>
				<td>
					<input type="radio" checked="checked" name="expert_mode" id="expert_mode_no" value="0" onchange="switchExpertMode(this);" /><label for="expert_mode_no"> {$TR_NO}</label>
					<input type="radio" name="expert_mode" id="expert_mode_yes" value="1" onchange="switchExpertMode(this);" /><label for="expert_mode_yes"> {$TR_YES}</label>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset id='normal_mode'>
		<legend>{$TR_CRON_SCHEDULE}</legend>
		<table>
			<tr>
				<td>{$TR_MIN}
					<select name="minute[]" size="6" multiple="multiple" id="minute">
						{section name=i loop=$MINUTE_TEXT}
						<option value="{$MINUTE_VALUE[i]}" {$MINUTE_SELECTED[i]}>{$MINUTE_TEXT[i]}</option>
						{/section}
					</select>
				</td>
				<td>{$TR_HOUR}
					<select name="hour[]" size="6" multiple="multiple" id="hour">
						{section name=i loop=$HOUR_TEXT}
						<option value="{$HOUR_VALUE[i]}" {$HOUR_SELECTED[i]}>{$HOUR_TEXT[i]}</option>
						{/section}
					</select>
				</td>
				<td>{$TR_DAY}
					<select name="day_of_month[]" size="6" multiple="multiple" id="day_of_month">
						{section name=i loop=$DOM_TEXT}
						<option value="{$DOM_VALUE[i]}" {$DOM_SELECTED[i]}>{$DOM_TEXT[i]}</option>
						{/section}
					</select>
				</td>
				<td>{$TR_MONTHS}
					<select name="month[]" size="6" multiple="multiple" id="month">
						{section name=i loop=$MONTH_TEXT}
						<option value="{$MONTH_VALUE[i]}" {$MONTH_SELECTED[i]}>{$MONTH_TEXT[i]}</option>
						{/section}
					</select>
				</td>
				<td>{$TR_WEEKDAYS}
					<select name="day_of_week[]" size="6" multiple="multiple" id="day_of_week">
						{section name=i loop=$DOW_TEXT}
						<option value="{$DOW_VALUE[i]}" {$DOW_SELECTED[i]}>{$DOW_TEXT[i]}</option>
						{/section}
					</select>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset id='expert_mode'>
		<legend>{$TR_CRON_SCHEDULE} {$TR_EXPERT_MODE}</legend>
		<table>
			<tr>
				<td>{$TR_MIN}</td>
				<td><input name="minute[]" type="text" class="textinput" id="minute_expert" value="{$MINUTE_EXPERT}" /></td>
				<td>{$TR_HOUR}</td>
				<td><input name="hour[]" type="text" class="textinput" id="hour_expert" value="{$HOUR_EXPERT}" /></td>
				<td>{$TR_DAY}</td>
				<td><input name="day_of_month[]" type="text" class="textinput" id="day_of_month_expert" value="{$DOM_EXPERT}" /></td>
				<td>{$TR_MONTHS}</td>
				<td><input name="month[]" type="text" class="textinput" id="month_expert" value="{$MONTH_EXPERT}" /></td>
				<td>{$TR_WEEKDAYS}</td>
				<td><input name="day_of_week[]" type="text" class="textinput" id="day_of_week_expert" value="{$DOW_EXPERT}" /></td>
			</tr>
		</table>

	</fieldset>
	<div class="buttons">
		<input type="hidden" name="uaction" value="add_cronjob" />
		<input type="hidden" name="cron_id" value="{$CRON_ID}" />
		<input type="reset" name="Reset" value="{$TR_RESET}" />
		<input type="submit" name="Submit" value="{$TR_ADD}" />
	</div>
</form>
{/block}