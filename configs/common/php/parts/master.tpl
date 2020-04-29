[master]
user = {$DOMAIN_UID}
group = {$DOMAIN_GID}

listen = /run/php-fpm.master.sock

listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = ondemand
pm.max_children = 3
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
pm.process_idle_timeout = 10s;
pm.max_requests = 500

request_terminate_timeout = 3600s

php_admin_value[open_basedir] = "{$WWW_DIR}/{$DOMAIN_NAME}/:{$CONF_DIR}/:/proc/:/bin/df:/bin/mount:{$RKHUNTER_LOG}:{$CHKROOTKIT_LOG}:{$PEAR_DIR}:{$OTHER_ROOTKIT_LOG}:{$EASYSCPC_DIR}:{$EASYSCPD_DIR}"
php_admin_value[disable_functions] = "pcntl_alarm,pcntl_fork,pcntl_waitpid,pcntl_wait,pcntl_wifexited,pcntl_wifstopped,pcntl_wifsignaled,pcntl_wifcontinued,pcntl_wexitstatus,pcntl_wtermsig,pcntl_wstopsig,pcntl_signal,pcntl_signal_dispatch,pcntl_get_last_error,pcntl_strerror,pcntl_sigprocmask,pcntl_sigwaitinfo,pcntl_sigtimedwait,pcntl_exec,pcntl_getpriority,pcntl_setpriority, show_source, system, shell_exec, passthru, exec, phpinfo, shell, symlink"
php_admin_value[max_execution_time] = "30"
php_admin_value[memory_limit] = "128M"
php_admin_flag[log_errors] = On
php_admin_flag[html_errors] = Off
php_admin_value[post_max_size] = "32M"
php_admin_value[upload_tmp_dir] = "{$WWW_DIR}/{$DOMAIN_NAME}/phptmp/"
php_admin_value[upload_max_filesize] = "8M"
php_admin_value[date.timezone] = "{$PHP_TIMEZONE}"
php_admin_value[sendmail_path] = "/usr/sbin/sendmail -t -i -f webmaster@{$MAIL_DMN}"
php_admin_value[session.save_path] = "{$WWW_DIR}/{$DOMAIN_NAME}/phptmp"
php_admin_value[soap.wsdl_cache_dir] = "{$WWW_DIR}/{$DOMAIN_NAME}/phptmp"
