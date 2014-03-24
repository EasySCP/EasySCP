{include file='header.tpl'}
<body>
	<h2 style="margin: 20px;">EasySCP Exception Message</h2>
	{if isset($MESSAGE)} 
	<div class="{$MSG_TYPE}">{$MESSAGE}</div>
	{/if}
</body>
</html>