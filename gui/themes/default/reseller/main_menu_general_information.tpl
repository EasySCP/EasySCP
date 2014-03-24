<div class="main_menu">
	{if isset($CUSTOM_BUTTONS)}
	<ul class="icons">
		{section name=i loop=$BUTTON_NAME}
		<li><a href="{$BUTTON_LINK[i]}" {$BUTTON_TARGET[i]} title="{$BUTTON_NAME[i]}"><span class="{$BUTTON_ICON[i]} icon_link">&nbsp;</span></a></li>
		{/section}
	</ul>
	{/if}
</div>
