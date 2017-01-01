<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2017 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

require '../../include/easyscp-lib.php';

check_login(__FILE__);

$cfg = EasySCP_Registry::get('Config');

if (!isset($_GET['domain_id'])) {
	user_goto('users.php?psi=last');
}

if (!is_numeric($_GET['domain_id'])) {
	user_goto('users.php?psi=last');
}

$sql_param = array(
		':domain_id' => $_GET['domain_id']
);

$sql_query = "
	SELECT
		domain_name, status, domain_created_id
	FROM
		domain
	WHERE
		domain_id = :domain_id
";

DB::prepare($sql_query);
$row = DB::execute($sql_param, true);

// let's check if this reseller has rights to disable/enable this domain
if ($row['domain_created_id'] != $_SESSION['user_id']) {
	user_goto('users.php?psi=last');
}

if ($row['status'] == $cfg->ITEM_OK_STATUS) {
	change_domain_status($_GET['domain_id'], $row['domain_name'], 'disable', 'reseller');
} else if ($row['status'] == $cfg->ITEM_DISABLED_STATUS) {
	change_domain_status($_GET['domain_id'], $row['domain_name'], 'enable', 'reseller');
} else {
	user_goto('users.php?psi=last');
}
?>