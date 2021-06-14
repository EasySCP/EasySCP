<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Single signon for phpMyAdmin
 *
 * This is just example how to use session based single signon with
 * phpMyAdmin, it is not intended to be perfect code and look, only
 * shows how you can integrate this functionality in your application.
 *
 * @package    PhpMyAdmin
 * @subpackage Example
 */

/* Need to have cookie visible from parent directory */
session_set_cookie_params(0, '/', '', true, true);

/* Create signon session */
$session_name = 'EasySCP';
session_name($session_name);

// Uncomment and change the following line to match your $cfg['SessionSavePath']
//session_save_path('/foobar');
@session_start();

/* Delete there credentials */
$_SESSION['PMA_single_signon_user'] = '';
$_SESSION['PMA_single_signon_password'] = '';
$id = session_id();

/* Close that session */
@session_write_close();

/* Redirect to PhpMyAdmin (should use absolute URL here!) */
header('Location: ../index.php?server=1');
?>
