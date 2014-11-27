{extends file="common/layout.tpl"}

{block name=TR_PAGE_TITLE}{$TR_PAGE_TITLE}{/block}

{block name=CUSTOM_JS}{/block}

{block name=CONTENT_HEADER}{$TR_ERROR_EDIT_PAGE}{/block}

{block name=BREADCRUMP}
<li><a href="/client/webtools.php">{$TR_MENU_WEBTOOLS}</a></li>
<li><a href="/client/error_pages.php">{$TR_MENU_ERROR_PAGES}</a></li>
<li><a>{$TR_ERROR_EDIT_PAGE}</a></li>
{/block}

{block name=BODY}
<h2 class="errors"><span>{$TR_ERROR_EDIT_PAGE} {$EID}</span></h2>
<form action="/client/error_pages.php" method="post" id="client_error_pages">
	<fieldset>
		<textarea name="error" cols="120" rows="25" id="error">{$ERROR}</textarea>
	</fieldset>
	<div class="buttons">
		<input type="hidden" name="eid" value="{$EID}" />
		<input type="hidden" name="uaction" value="updt_error" />
		<input type="submit" name="Submit" value="{$TR_SAVE}" />
	</div>
</form>
{/block}