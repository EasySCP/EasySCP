{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_CHOOSE_DEFAULT_LANGUAGE}{/block}

{block name=BREADCRUMP}
<li><a href="/client/index.php">{$TR_MENU_GENERAL_INFORMATION}</a></li>
<li><a>{$TR_CHOOSE_DEFAULT_LANGUAGE}</a></li>
{/block}

{block name=BODY}
<h2 class="multilanguage"><span>{$TR_CHOOSE_DEFAULT_LANGUAGE}</span></h2>
<form action="/client/language.php" method="post" id="reseller_language">
	<table>
		<tr>
			<td><label for="def_language">{$TR_CHOOSE_DEFAULT_LANGUAGE}</label></td>
			<td>
				<select name="def_language" id="def_language">
					{section name=i loop=$LANG_NAME}
					<option value="{$LANG_VALUE[i]}" {$LANG_SELECTED[i]}>{$LANG_NAME[i]}</option>
					{/section}
				</select>
			</td>
		</tr>
	</table>
	<div class="buttons">
		<input type="hidden" name="uaction" value="save_lang" />
		<input type="submit" name="Submit" value="{$TR_SAVE}" />
	</div>
</form>
{/block}