<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2015 by Easy Server Control Panel - http://www.easyscp.net
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

$tpl = EasySCP_TemplateEngine::getInstance();
$template = 'reseller/user_add3.tpl';

if (isset($_SESSION['user_add3_added']) && $_SESSION['user_add3_added'] == "_yes_") {
	user_goto('users.php?psi=last');
}

if (isset($_SESSION['user_add3_add_alias']) && $_SESSION['user_add3_add_alias'] == "_yes_") {
	unset($_SESSION["user_add3_add_alias"]);
	user_goto('user_add4.php?accout=' . $_SESSION['dmn_id']);
}

// static page messages
gen_logged_from($tpl);

$tpl->assign(
	array(
		'TR_PAGE_TITLE'			=> tr('EasySCP - User/Add user'),
		'TR_ADD_USER'			=> tr('Add user'),
		'TR_CORE_DATA'			=> tr('Core data'),
		'TR_USERNAME'			=> tr('Username'),
		'TR_PASSWORD'			=> tr('Password'),
		'TR_REP_PASSWORD'		=> tr('Repeat password'),
		'TR_DMN_IP'				=> tr('Domain IP'),
		'TR_USREMAIL'			=> tr('Email'),
		'TR_ADDITIONAL_DATA'	=> tr('Additional data'),
		'TR_CUSTOMER_ID'		=> tr('Customer ID'),
		'TR_FIRSTNAME'			=> tr('First name'),
		'TR_LASTNAME'			=> tr('Last name'),
		'TR_GENDER'				=> tr('Gender'),
		'TR_MALE'				=> tr('Male'),
		'TR_FEMALE'				=> tr('Female'),
		'TR_UNKNOWN'			=> tr('Unknown'),
		'TR_COMPANY'			=> tr('Company'),
		'TR_POST_CODE'			=> tr('Zip/Postal code'),
		'TR_CITY'				=> tr('City'),
		'TR_STATE_PROVINCE'		=> tr('State/Province'),
		'TR_COUNTRY'			=> tr('Country'),
		'TR_STREET1'			=> tr('Street 1'),
		'TR_STREET2'			=> tr('Street 2'),
		'TR_PHONE'				=> tr('Phone'),
		'TR_FAX'				=> tr('Fax'),
		'TR_BTN_ADD_USER'		=> tr('Add user'),
		'TR_ADD_ALIASES'		=> tr('Add other domains to this account'),
		'VL_USR_PASS'			=> passgen()
	)
);

gen_reseller_mainmenu($tpl, 'reseller/main_menu_users_manage.tpl');
gen_reseller_menu($tpl, 'reseller/menu_users_manage.tpl');

if (!init_in_values()) {
	set_page_message(
		tr("Domain data has been altered. Please enter again."),
		'warning'
	);
	unset_messages();
	user_goto('user_add1.php');
}


// Process the action ...
if (isset($_POST['uaction'])
	&& ("user_add3_nxt" === $_POST['uaction'])
	&& !isset($_SESSION['step_two_data'])) {
	if (check_ruser_data($tpl, '_no_')) {
		add_user_data($_SESSION['user_id']);
	}
	set_page_message($_SESSION['Message']);
	unset($_SESSION['Message']);
} else {
	unset($_SESSION['step_two_data']);
	gen_empty_data();
}

gen_user_add3_page($tpl);
gen_page_message($tpl);

if (!check_reseller_permissions($_SESSION['user_id'], 'alias')) {
	$tpl->assign('ALIAS_ADD', '');
}

if ($cfg->DUMP_GUI_DEBUG) {
	dump_gui_debug($tpl);
}

$tpl->display($template);

// FUNCTION declaration

/**
 * Get data from previous page
 */
function init_in_values() {
	global $dmn_name, $dmn_expire, $dmn_user_name, $hpid;

	if (isset($_SESSION['dmn_expire'])) {
		$dmn_expire = strtotime($_SESSION['dmn_expire']);
	} else {
		$dmn_expire = 0;
	}

	if (isset($_SESSION['step_one'])) {
		$step_two = $_SESSION['dmn_name'] . ";" . $_SESSION['dmn_tpl'];
		$hpid = $_SESSION['dmn_tpl'];
		unset($_SESSION['dmn_name']);
		unset($_SESSION['dmn_tpl']);
		unset($_SESSION['chtpl']);
		unset($_SESSION['step_one']);
	} else if (isset($_SESSION['step_two_data'])) {
		$step_two = $_SESSION['step_two_data'];
		unset($_SESSION['step_two_data']);
	} else if (isset($_SESSION['local_data'])) {
		$step_two = $_SESSION['local_data'];
		unset($_SESSION['local_data']);
	} else {
		$step_two = "'';0";
	}

	list($dmn_name, $hpid) = explode(";", $step_two);

	$dmn_user_name = $dmn_name;
	if (!validates_dname(decode_idna($dmn_name)) || ($hpid == '')) {
		return false;
	}
	return true;

} // End of init_in_values()

/**
 * generate page add user 3
 * @param EasySCP_TemplateEngine $tpl
 */
function gen_user_add3_page($tpl) {
	global $dmn_name, $hpid, $dmn_user_name, $user_email, $customer_id,
		$first_name, $last_name, $gender, $firm, $zip, $city, $state, $country,
		$street_one, $street_two, $phone, $fax;

	$cfg = EasySCP_Registry::get('Config');

	$dmn_user_name = decode_idna($dmn_user_name);
	// Fill in the fields
	$tpl->assign(
		array(
			'VL_USERNAME'		=> tohtml($dmn_user_name),
			'VL_USR_PASS'		=> passgen(),
			'VL_MAIL'			=> tohtml($user_email),
			'VL_USR_ID'			=> $customer_id,
			'VL_USR_NAME'		=> tohtml($first_name),
			'VL_LAST_USRNAME'	=> tohtml($last_name),
			'VL_USR_FIRM'		=> tohtml($firm),
			'VL_USR_POSTCODE'	=> tohtml($zip),
			'VL_USRCITY'		=> tohtml($city),
			'VL_USRSTATE'		=> tohtml($state),
			'VL_MALE'			=> ($gender == 'M') ? $cfg->HTML_SELECTED : '',
			'VL_FEMALE'			=> ($gender == 'F') ? $cfg->HTML_SELECTED : '',
			'VL_UNKNOWN'		=> ($gender == 'U') ? $cfg->HTML_SELECTED : '',
			'VL_COUNTRY'		=> tohtml($country),
			'VL_STREET1'		=> tohtml($street_one),
			'VL_STREET2'		=> tohtml($street_two),
			'VL_PHONE'			=> tohtml($phone),
			'VL_FAX'			=> tohtml($fax)
		)
	);

	generate_ip_list($tpl, $_SESSION['user_id']);
	$_SESSION['local_data'] = "$dmn_name;$hpid";

} // End of gen_user_add3_page()

/**
 * Init global value with empty values
 */
function gen_empty_data() {

	global $user_email, $customer_id, $first_name, $last_name, $gender, $firm,
		$zip, $city, $state, $country, $street_one, $street_two, $mail, $phone,
		$fax, $domain_ip;

	$user_email		= '';
	$customer_id	= '';
	$first_name		= '';
	$last_name		= '';
	$gender			= 'U';
	$firm			= '';
	$zip			= '';
	$city			= '';
	$state			= '';
	$country		= '';
	$street_one		= '';
	$street_two		= '';
	$phone			= '';
	$fax			= '';
	$domain_ip		= '';

} // End of gen_empty_data()

/**
 * Save data for new user in db
 */
function add_user_data($reseller_id) {
	global $hpid, $dmn_name, $dmn_expire, $dmn_user_name, $admin_login, 
		$user_email, $customer_id, $first_name, $last_name, $gender, $firm,
		$zip, $city, $state, $country, $street_one, $street_two, $phone,
		$fax, $inpass, $domain_ip, $dns, $backup,$countbackup;

	$sql = EasySCP_Registry::get('Db');
	$cfg = EasySCP_Registry::get('Config');

	// Let's get Desired Hosting Plan Data;
	$err_msg = '';

	if (!empty($err_msg)) {
		set_page_message($err_msg, 'error');
		return false;
	}

	if (isset($_SESSION["ch_hpprops"])) {
		$props = $_SESSION["ch_hpprops"];
		unset($_SESSION["ch_hpprops"]);
	} else {

		if (isset($cfg->HOSTING_PLANS_LEVEL)
			&& $cfg->HOSTING_PLANS_LEVEL === 'admin') {
			$query = 'SELECT `props` FROM `hosting_plans` WHERE `id` = ?';
			$res = exec_query($sql, $query, $hpid);
		} else {
			$query = "SELECT `props` FROM `hosting_plans` WHERE `reseller_id` = ? AND `id` = ?";
			$res = exec_query($sql, $query, array($reseller_id, $hpid));
		}

		$data = $res->fetchRow();
		$props = unserialize($data['props']);
	}

	$php = $props['allow_php'];
	$phpe = $props['allow_phpeditor'];
	$cgi = $props['allow_cgi'];
	$sub = $props['subdomain_cnt'];
	$als = $props['alias_cnt'];
	$mail = $props['mail_cnt'];
	$ftp = $props['ftp_cnt'];
	$sql_db = $props['db_cnt'];
	$sql_user = $props['sqluser_cnt'];
	$traff = $props['traffic'];
	$disk = $props['disk'];
	$backup = $props['allow_backup'];
	$countbackup = $props['disk_countbackup'];
	$dns = $props['allow_dns'];
	$ssl = $props['allow_ssl'];

	$php			= preg_replace("/\_/", "", $php);
	$phpe			= preg_replace("/\_/", "", $phpe);
	$cgi			= preg_replace("/\_/", "", $cgi);
	$ssl			= preg_replace("/\_/", "", $ssl);
	$backup			= preg_replace("/\_/", "", $backup);
	$countbackup	= preg_replace("/\_/", "", $countbackup);
	$dns			= preg_replace("/\_/", "", $dns);
	$pure_user_pass = $inpass;
	$inpass			= crypt_user_pass($inpass);
	$first_name		= clean_input($first_name);
	$last_name		= clean_input($last_name);
	$firm			= clean_input($firm);
	$zip			= clean_input($zip);
	$city			= clean_input($city);
	$state			= clean_input($state);
	$country		= clean_input($country);
	$phone			= clean_input($phone);
	$fax			= clean_input($fax);
	$street_one		= clean_input($street_one);
	$street_two		= clean_input($street_two);
	$customer_id	= clean_input($customer_id);

	if (!validates_dname(decode_idna($dmn_user_name))) {
		return;
	}

	$query = "
		INSERT INTO `admin` (
			`admin_name`, `admin_pass`, `admin_type`, `domain_created`,
			`created_by`, `fname`, `lname`,
			`firm`, `zip`, `city`, `state`,
			`country`, `email`, `phone`,
			`fax`, `street1`, `street2`,
			`customer_id`, `gender`
		)
		VALUES (
			?, ?, 'user', unix_timestamp(),
			?, ?, ?,
			?, ?, ?, ?,
			?, ?, ?,
			?, ?, ?,
			?, ?
		)
	";

	exec_query(
		$sql,
		$query,
		array(
			$dmn_user_name, $inpass,
			$reseller_id, $first_name, $last_name,
			$firm, $zip, $city, $state,
			$country, $user_email, $phone,
			$fax, $street_one, $street_two,
			$customer_id, $gender
		)
	);

	print $sql->errorMsg();

	$record_id = $sql->insertId();
	
	$query = "
		INSERT INTO `domain` (
			`domain_name`, `domain_admin_id`,
			`domain_created_id`, `domain_created`, `domain_expires`,
			`domain_mailacc_limit`, `domain_ftpacc_limit`,
			`domain_traffic_limit`, `domain_sqld_limit`,
			`domain_sqlu_limit`, `status`,
			`domain_subd_limit`, `domain_alias_limit`,
			`domain_ip_id`, `domain_disk_limit`,
			`domain_disk_usage`, `domain_php`, `domain_php_edit`, `domain_cgi`,
			`allowbackup`, `domain_dns`, `domain_ssl`, `domain_disk_countbackup`
		)
		VALUES (
			:domain_name, :domain_admin_id,
			:domain_created_id, unix_timestamp(), :domain_expires,
			:domain_mailacc_limit, :domain_ftpacc_limit,
			:domain_traffic_limit, :domain_sqld_limit,
			:domain_sqlu_limit, :status,
			:domain_subd_limit, :domain_alias_limit,
			:domain_ip_id, :domain_disk_limit,
			'0', :domain_php, :domain_php_edit, :domain_cgi,
			:allowbackup, :domain_dns, :domain_ssl, :domain_disk_countbackup
		)
	";
	$param = array(
		':domain_name' => $dmn_name,
		':domain_admin_id' => $record_id, 
		':domain_created_id' => $reseller_id,
		':domain_expires' => $dmn_expire,
		':domain_mailacc_limit' => $mail,
		':domain_ftpacc_limit' => $ftp,
		':domain_traffic_limit' => $traff,
		':domain_sqld_limit' => $sql_db,
		':domain_sqlu_limit' => $sql_user,
		':status' => $cfg->ITEM_ADD_STATUS,
		':domain_subd_limit' => $sub,
		':domain_alias_limit' => $als,
		':domain_ip_id' => $domain_ip,
		':domain_disk_limit' => $disk,
		':domain_php' => $php,
		':domain_php_edit' => $phpe,
		':domain_cgi' => $cgi,
		':allowbackup' => $backup,
		':domain_dns' => $dns,
		':domain_ssl' => $ssl,
		':domain_disk_countbackup' => $countbackup,
	);

	DB::prepare($query);
	DB::execute($param);
	$dmn_id = DB::getInstance()->lastInsertId();
	
	// AddDefaultDNSEntries($dmn_id, 0, $dmn_name, $domain_ip);

	// TODO: Check if max user and group id is reached
	// update domain and gid
	$domain_gid=$cfg->APACHE_SUEXEC_MIN_GID+$dmn_id;
	$domain_uid=$cfg->APACHE_SUEXEC_MIN_UID+$dmn_id;
	
	$query="
		UPDATE `domain`
		SET `domain_gid`=?,
			`domain_uid`=?
		WHERE `domain_id`=?
	";
	
	exec_query(
			$sql, 
			$query,
			array(
				$domain_gid,
				$domain_uid, 
				$dmn_id)
	);
	
	// Add statistics group
	$query = "
		INSERT INTO `htaccess_users`
			(`dmn_id`, `uname`, `upass`, `status`)
		VALUES
			(?, ?, ?, ?)
	";

	exec_query($sql, $query,
			array(
				$dmn_id, $dmn_name,
				crypt_user_pass_with_salt($pure_user_pass), $cfg->ITEM_ADD_STATUS
			)
	);

	$user_id = $sql->insertId();

	$query = "
		INSERT INTO `htaccess_groups`
			(`dmn_id`, `ugroup`, `members`, `status`)
		VALUES
			(?, ?, ?, ?)
	";

	exec_query(
		$sql,
		$query,
		array(
			$dmn_id, $cfg->AWSTATS_GROUP_AUTH, $user_id, $cfg->ITEM_ADD_STATUS
		)
	);

	// Create the 3 default addresses if wanted
	if ($cfg->CREATE_DEFAULT_EMAIL_ADDRESSES) {
		client_mail_add_default_accounts($dmn_id, $user_email, $dmn_name); // 'domain', 0
	}

	// let's send mail to user
	send_add_user_auto_msg (
		$reseller_id,
		$dmn_user_name,
		$pure_user_pass,
		$user_email,
		$first_name,
		$last_name,
		tr('Domain account')
	);

	// $user_def_lang = $cfg->USER_INITIAL_LANG;
	$user_def_lang = '';
	// $user_theme_color = $cfg->USER_INITIAL_THEME;
	$user_theme_color = '';

	$query = "
		INSERT INTO `user_gui_props`
			(`user_id`, `lang`, `layout`)
		VALUES
			(?, ?, ?)
	";

	exec_query($sql, $query, array($record_id,
			$user_def_lang,
			$user_theme_color));
	// send request to daemon
	// TODO Prüfen, da es hier zu einem Fehler kommt ("Domain data has been altered. Please enter again.")
	send_request('110 DOMAIN domain '.$dmn_id);
	send_request('130 MAIL '.$dmn_id);

	$admin_login = $_SESSION['user_logged'];
	write_log("$admin_login: add user: $dmn_user_name (for domain $dmn_name)");
	write_log("$admin_login: add domain: $dmn_name");

	update_reseller_c_props($reseller_id);

	if (isset($_POST['add_alias']) && $_POST['add_alias'] === 'on') {
		// we have to add some aliases for this looser
		$_SESSION['dmn_id'] = $dmn_id;
		$_SESSION['dmn_ip'] = $domain_ip;
		$_SESSION['user_add3_add_alias'] = "_yes_";
		user_goto('user_add4.php?accout=' . $dmn_id);
	} else {
		// we have not to add alias
		$_SESSION['user_add3_added'] = "_yes_";
		user_goto('users.php?psi=last');
	}
} // End of add_user_data()
?>