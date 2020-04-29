[{$SERVER_NAME}]
user = {$DOMAIN_UID}
group = {$DOMAIN_GID}

listen = /run/php-fpm.{$SERVER_NAME}.sock

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

;;;;;;;;;;;;;;;;;;;;;;
; User Configuration ;
;;;;;;;;;;;;;;;;;;;;;;

include = /etc/easyscp/php/user/{$SERVER_NAME}.conf

;;;;;;;;;;;;;;;;;;;;;;;;;
; Default Configuration ;
;;;;;;;;;;;;;;;;;;;;;;;;;

php_admin_value[open_basedir] = "{$WWW_DIR}/{$MASTER_DOMAIN}/:{$PEAR_DIR}/:/dev/urandom:/proc/meminfo:/var/www/php_open/"
php_admin_value[disable_functions] = "pcntl_alarm,pcntl_fork,pcntl_waitpid,pcntl_wait,pcntl_wifexited,pcntl_wifstopped,pcntl_wifsignaled,pcntl_wifcontinued,pcntl_wexitstatus,pcntl_wtermsig,pcntl_wstopsig,pcntl_signal,pcntl_signal_dispatch,pcntl_get_last_error,pcntl_strerror,pcntl_sigprocmask,pcntl_sigwaitinfo,pcntl_sigtimedwait,pcntl_exec,pcntl_getpriority,pcntl_setpriority, show_source, system, passthru, shell"
php_value[max_execution_time] = "360"
php_value[memory_limit] = "128M"
php_flag[log_errors] = On
php_flag[html_errors] = Off
php_value[post_max_size] = "64M"
php_admin_value[upload_tmp_dir] = "{$WWW_DIR}/{$MASTER_DOMAIN}/phptmp/"
php_value[upload_max_filesize] = "64M"
php_value[date.timezone] = "{$PHP_TIMEZONE}"
php_admin_value[sendmail_path] = "/usr/sbin/sendmail -t -i -f webmaster@{$MASTER_DOMAIN}"
php_admin_value[session.save_path] = "{$WWW_DIR}/{$MASTER_DOMAIN}/phptmp"
php_admin_value[soap.wsdl_cache_dir] = "{$WWW_DIR}/{$MASTER_DOMAIN}/phptmp"
