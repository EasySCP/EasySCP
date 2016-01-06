<VirtualHost {$DOMAIN_IP}:80>

	<IfModule suexec_module>
		SuexecUserGroup {$DOMAIN_UID} {$DOMAIN_GID}
	</IfModule>

	ServerAdmin		webmaster@{$DOMAIN_NAME}
	DocumentRoot	{$WWW_DIR}/{$DOMAIN_NAME}/disabled

	ServerName		{$DOMAIN_NAME}
	ServerAlias		www.{$DOMAIN_NAME} {$DOMAIN_NAME} *.{$DOMAIN_NAME} {$DOMAIN_UID}.{$BASE_SERVER_VHOST}

	Alias /errors	{$WWW_DIR}/{$DOMAIN_NAME}/errors/

	ErrorDocument 401 /errors/401.html
	ErrorDocument 403 /errors/403.html
	ErrorDocument 404 /errors/404.html
	ErrorDocument 500 /errors/500.html
	ErrorDocument 503 /errors/503.html

	<IfModule mod_cband.c>
		CBandUser {$DOMAIN_NAME}
	</IfModule>

	DirectoryIndex index.php index.htm index.html

	<Directory {$WWW_DIR}/{$DOMAIN_NAME}/disabled>
		Options -Indexes +Includes +FollowSymLinks +MultiViews
		AllowOverride All
		Order allow,deny
		Allow from all
	</Directory>

</VirtualHost>

<IfModule mod_cband.c>
	<CBandUser {$DOMAIN_NAME}>
		CBandUserLimit 1024Mi
		CBandUserScoreboard /var/www/scoreboards/{$DOMAIN_NAME}
		CBandUserPeriod 4W
		CBandUserPeriodSlice 1W
		CBandUserExceededURL http://www.{$DOMAIN_NAME}/errors/bw_exceeded.html
	</CBandUser>
</IfModule>
