# EasySCP a Virtual Hosting Control Panel
# Copyright (C) 2010-2017 by Easy Server Control Panel - http://www.easyscp.net
#
# This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
# To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
#
# @link 		http://www.easyscp.net
# @author 		EasySCP Team

#
# Master Begin
#
{if isset($REDIRECT) && $REDIRECT == true }
<VirtualHost {$BASE_SERVER_IP}:80>
	ServerAdmin		{$DEFAULT_ADMIN_ADDRESS}
	DocumentRoot	{$GUI_ROOT_DIR}/htdocs

	ServerName		{$BASE_SERVER_VHOST}

	# Redirect to SSL Page
	RewriteEngine On
	{literal}
	RewriteCond %{HTTPS} !on
	RewriteRule ^/(.*) https://%{SERVER_NAME}%{REQUEST_URI} [R]
	{/literal}
</VirtualHost>
{else}
<VirtualHost {$BASE_SERVER_IP}:{$BASE_PORT}>

	ServerAdmin     {$DEFAULT_ADMIN_ADDRESS}
	DocumentRoot    {$GUI_ROOT_DIR}/htdocs

	ServerName      {$BASE_SERVER_VHOST}

{if isset($SSL_CERT_DIR) && isset($SSL_KEY_DIR)}
	SSLEngine       On
	SSLCertificateFile {$SSL_CERT_DIR}/easyscp_master-cert.pem
	SSLCertificateKeyFile {$SSL_KEY_DIR}/easyscp_master-key.pem
{if isset($SSL_CACERT) && $SSL_CACERT == true}
	SSLCACertificateFile {$SSL_CERT_DIR}/easyscp_master-cacert.pem
{/if}
{/if}

	ErrorLog {$APACHE_LOG_DIR}/error.log

	CustomLog {$APACHE_LOG_DIR}/access.log combined

	Alias /errors   {$GUI_ROOT_DIR}/errordocs/

	ErrorDocument 401 /errors/401.html
	ErrorDocument 403 /errors/403.html
	ErrorDocument 404 /errors/404.html
	ErrorDocument 500 /errors/500.html
	ErrorDocument 503 /errors/503.html

	Alias /pma      {$GUI_ROOT_DIR}/tools/pma/
	Alias /webmail  {$GUI_ROOT_DIR}/tools/webmail/
	Alias /ftp      {$GUI_ROOT_DIR}/tools/filemanager/

	DirectoryIndex index.php index.htm index.html

	<IfModule suexec_module>
	SuexecUserGroup {$SUEXEC_UID} {$SUEXEC_GID}
	</IfModule>

	<Directory {$GUI_ROOT_DIR}>
		Options -Indexes +Includes +FollowSymLinks +MultiViews
		AllowOverride None
		# Apache 2.2
		<IfModule !mod_authz_core.c>
			Order allow,deny
			Allow from all
		</IfModule>
		# Apache 2.4
		<IfModule mod_authz_core.c>
			Require all granted
		</IfModule>
	</Directory>

	<IfModule mod_fcgid.c>
		<Directory {$GUI_ROOT_DIR}>
			FCGIWrapper {$PHP_STARTER_DIR}/master/php-fcgi-starter .php
			Options +ExecCGI
		</Directory>
		<Directory "{$PHP_STARTER_DIR}/master">
			AllowOverride None
			Options +ExecCGI +MultiViews -Indexes
			# Apache 2.2
			<IfModule !mod_authz_core.c>
				Order allow,deny
				Allow from all
			</IfModule>
			# Apache 2.4
			<IfModule mod_authz_core.c>
				Require all granted
			</IfModule>
		</Directory>
	</IfModule>

</VirtualHost>
{/if}

{if isset($BASE_SERVER_IPv6)}
{if isset($REDIRECT) && $REDIRECT == true }
<VirtualHost [{$BASE_SERVER_IPv6}]:80>
	ServerAdmin		{$DEFAULT_ADMIN_ADDRESS}
	DocumentRoot	{$GUI_ROOT_DIR}/htdocs

	ServerName		{$BASE_SERVER_VHOST}

	# Redirect to SSL Page
	RewriteEngine On
	{literal}
	RewriteCond %{HTTPS} !on
	RewriteRule ^/(.*) https://%{SERVER_NAME}%{REQUEST_URI} [R]
	{/literal}
</VirtualHost>
{else}
<VirtualHost [{$BASE_SERVER_IPv6}]:{$BASE_PORT}>

	ServerAdmin     {$DEFAULT_ADMIN_ADDRESS}
	DocumentRoot    {$GUI_ROOT_DIR}/htdocs

	ServerName      {$BASE_SERVER_VHOST}

{if isset($SSL_CERT_DIR) && isset($SSL_KEY_DIR)}
	SSLEngine       On
	SSLCertificateFile {$SSL_CERT_DIR}/easyscp_master-cert.pem
	SSLCertificateKeyFile {$SSL_KEY_DIR}/easyscp_master-key.pem
{if isset($SSL_CACERT) && $SSL_CACERT == true}
	SSLCACertificateFile {$SSL_CERT_DIR}/easyscp_master-cacert.pem
{/if}
{/if}

	ErrorLog {$APACHE_LOG_DIR}/error.log

	CustomLog {$APACHE_LOG_DIR}/access.log combined

	Alias /errors   {$GUI_ROOT_DIR}/errordocs/

	ErrorDocument 401 /errors/401.html
	ErrorDocument 403 /errors/403.html
	ErrorDocument 404 /errors/404.html
	ErrorDocument 500 /errors/500.html
	ErrorDocument 503 /errors/503.html

	Alias /pma      {$GUI_ROOT_DIR}/tools/pma/
	Alias /webmail  {$GUI_ROOT_DIR}/tools/webmail/
	Alias /ftp      {$GUI_ROOT_DIR}/tools/filemanager/

	DirectoryIndex index.php index.htm index.html

	<IfModule suexec_module>
	SuexecUserGroup {$SUEXEC_UID} {$SUEXEC_GID}
	</IfModule>

	<Directory {$GUI_ROOT_DIR}>
		Options -Indexes +Includes +FollowSymLinks +MultiViews
		AllowOverride None
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

	<IfModule mod_fcgid.c>
		<Directory {$GUI_ROOT_DIR}>
			FCGIWrapper {$PHP_STARTER_DIR}/master/php-fcgi-starter .php
			Options +ExecCGI
		</Directory>
		<Directory "{$PHP_STARTER_DIR}/master">
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

</VirtualHost>
{/if}
{/if}
#
# Master End
#
