{include file='header.tpl'}
<body>
	<div class="header">
		<a class="link" href="http://www.easyscp.net" title="Easy Server Control Panel">
			<img alt="EasySCP logo" src="{$THEME_COLOR_PATH}/images/easyscp_logo.png" />
		</a>
	</div>
	<div class="location">&nbsp;</div>
	<div class="main">
		<div id="EasySCP_Setup_Error_MSG" class="error" style="display: none;"></div>
		<div id="EasySCP_Setup_Finish_MSG" class="success" style="display: none;"></div>
		<div id="EasySCP_Setup" class="content">
			<h2 class="general"><span>Welcome to EasySCP 2.0.0 Setup</span></h2>
			<br />
			<button type="button" onclick="EasySCP_Setup();">Start Setup</button>
		</div>
		<div class="login"><p>Powered by <a class="link" href="http://www.easyscp.net" title="Easy Server Control Panel">Easy Server Control Panel</a></p></div>
	</div>
{include file='footer.tpl'}