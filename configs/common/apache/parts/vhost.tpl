{if isset($REDIRECT) && $REDIRECT == true }
<VirtualHost {$DOMAIN_IP}:80>
	ServerAdmin		{$SERVER_ADMIN}
	DocumentRoot	{$WWW_DIR}/{$DOC_ROOT}/htdocs

	ServerName		{$SERVER_NAME}
	ServerAlias		{$SERVER_ALIAS}

	# Redirect to SSL Page
	RewriteEngine On
	{literal}
	RewriteCond %{HTTPS} !on
	RewriteRule ^/(.*) https://%{SERVER_NAME}%{REQUEST_URI} [R=301]
	{/literal}
</VirtualHost>
{else}
<VirtualHost {$DOMAIN_IP}:{$DOMAIN_PORT}>

	<IfModule suexec_module>
		SuexecUserGroup {$DOMAIN_UID} {$DOMAIN_GID}
	</IfModule>

	ServerAdmin		{$SERVER_ADMIN}
	DocumentRoot	{$WWW_DIR}/{$DOC_ROOT}/htdocs

	ServerName		{$SERVER_NAME}
	ServerAlias		{$SERVER_ALIAS}

{if isset($SSL_CERT_DIR) && isset($SSL_KEY_DIR)}
	SSLEngine       On
	SSLCertificateFile {$SSL_CERT_DIR}/easyscp_{$SERVER_NAME}-cert.pem
	SSLCertificateKeyFile {$SSL_KEY_DIR}/easyscp_{$SERVER_NAME}-key.pem
	{if isset($SSL_CACERT) && $SSL_CACERT == true }
	SSLCACertificateFile {$SSL_CERT_DIR}/easyscp_{$SERVER_NAME}-cacert.pem
	{/if}
{/if}

	ErrorLog {$WWW_DIR}/{$MASTER_DOMAIN}/logs/{$SERVER_NAME}-error.log

	CustomLog {$WWW_DIR}/{$MASTER_DOMAIN}/logs/{$SERVER_NAME}-access.log combined
	CustomLog {$APACHE_TRAFFIC_LOG_DIR}/{$TRAFFIC_PREFIX}{$SERVER_NAME} "%{literal}{%Y_%m_%d_%H_%M_%S}{/literal}t %I %O"

	Alias /errors	{$WWW_DIR}/{$DOC_ROOT}/errors/

	RedirectMatch permanent ^/ftp[\/]?$		http://{$BASE_SERVER_VHOST}/ftp/
	RedirectMatch permanent ^/pma[\/]?$		http://{$BASE_SERVER_VHOST}/pma/
	RedirectMatch permanent ^/webmail[\/]?$	http://{$BASE_SERVER_VHOST}/webmail/
	RedirectMatch permanent ^/easyscp[\/]?$	http://{$BASE_SERVER_VHOST}/

	ErrorDocument 401 /errors/401.html
	ErrorDocument 403 /errors/403.html
	ErrorDocument 404 /errors/404.html
	ErrorDocument 500 /errors/500.html
	ErrorDocument 503 /errors/503.html

	<IfModule mod_cband.c>
		CBandUser {$MASTER_DOMAIN}
	</IfModule>

{if isset($FORWARD_URL) && strcmp($FORWARD_URL,'no') != 0 }
	RewriteEngine On
	RewriteRule ^/(.*) {$FORWARD_URL}{literal}%{REQUEST_URI}{/literal} [R]
{else}

{if isset($AWSTATS) && $AWSTATS == true }
	ProxyRequests Off

	<Proxy *>
		# Apache 2.2
		<IfModule !mod_authz_core.c>
			Order deny,allow
			Allow from all
		</IfModule>
		# Apache 2.4
		<IfModule mod_authz_core.c>
			Require all granted
		</IfModule>
	</Proxy>

	ProxyPass			/stats  http://localhost/stats/{$SERVER_NAME}
	ProxyPassReverse	/stats  http://localhost/stats/{$SERVER_NAME}

	<Location /stats>
		<IfModule mod_rewrite.c>
			RewriteEngine on
			RewriteRule ^(.+)\?config=([^\?\&]+)(.*) $1\?config={$SERVER_NAME}&$3 [NC,L]
		</IfModule>
		AuthType Basic
		AuthName "Statistics for domain {$SERVER_NAME}"
		AuthUserFile {$WWW_DIR}/{$MASTER_DOMAIN}/{$HTACCESS_USERS_FILE_NAME}
		AuthGroupFile {$WWW_DIR}/{$MASTER_DOMAIN}/{$HTACCESS_GROUPS_FILE_NAME}
		Require group statistics
	</Location>
{/if}

{if isset($DOMAIN_CGI) && $DOMAIN_CGI == true }
	ScriptAlias /cgi-bin/ {$WWW_DIR}/{$DOC_ROOT}/cgi-bin/
	<Directory {$WWW_DIR}/{$DOC_ROOT}/cgi-bin>
		AllowOverride AuthConfig
		#Options ExecCGI
		# Apache 2.2
		<IfModule !mod_authz_core.c>
			Order deny,allow
			Allow from all
		</IfModule>
		# Apache 2.4
		<IfModule mod_authz_core.c>
			Require all granted
		</IfModule>
	</Directory>
{/if}

	DirectoryIndex index.php index.htm index.html

	<Directory {$WWW_DIR}/{$DOC_ROOT}/htdocs>
		Options -Indexes +Includes +FollowSymLinks +MultiViews
		AllowOverride All
		# Apache 2.2
		<IfModule !mod_authz_core.c>
			Order deny,allow
			Allow from all
		</IfModule>
		# Apache 2.4
		<IfModule mod_authz_core.c>
			Require all granted
		</IfModule>
	</Directory>

{if isset($DOMAIN_PHP) && $DOMAIN_PHP == true}
	<IfModule mod_fcgid.c>
		<Directory {$WWW_DIR}/{$DOC_ROOT}/htdocs>
			FCGIWrapper {$PHP_STARTER_DIR}/{$MASTER_DOMAIN}/php-fcgi-starter .php
			Options +ExecCGI
		</Directory>
		<Directory "{$PHP_STARTER_DIR}/{$MASTER_DOMAIN}">
			AllowOverride None
			Options +ExecCGI +MultiViews -Indexes
			# Apache 2.2
			<IfModule !mod_authz_core.c>
				Order deny,allow
				Allow from all
			</IfModule>
			# Apache 2.4
			<IfModule mod_authz_core.c>
				Require all granted
			</IfModule>
		</Directory>
	</IfModule>
{/if}

	Include {$CUSTOM_SITES_CONFIG_DIR}/{$SERVER_NAME}.custom
{/if}
</VirtualHost>

<IfModule mod_cband.c>
	<CBandUser {$MASTER_DOMAIN}>
		CBandUserLimit 1024Mi
		CBandUserScoreboard /var/www/scoreboards/{$MASTER_DOMAIN}
		CBandUserPeriod 4W
		CBandUserPeriodSlice 1W
		CBandUserExceededURL http://www.{$SERVER_NAME}/errors/bw_exceeded.html
	</CBandUser>
</IfModule>
{/if}