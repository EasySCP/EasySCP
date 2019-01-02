<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 *
 * @copyright 	2001-2006 by moleSoftware GmbH
 * @copyright 	2006-2010 by ispCP | http://isp-control.net
 * @copyright 	2010-2019 by Easy Server Control Panel - http://www.easyscp.net
 * @version 	SVN: $Id$
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 *
 * @license
 * The contents of this file are subject to the Mozilla Public License
 * Version 1.1 (the "License"); you may not use this file except in
 * compliance with the License. You may obtain a copy of the License at
 * http://www.mozilla.org/MPL/
 *
 * Software distributed under the License is distributed on an "AS IS"
 * basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
 * License for the specific language governing rights and limitations
 * under the License.
 *
 * The Original Code is "VHCS - Virtual Hosting Control System".
 *
 * The Initial Developer of the Original Code is moleSoftware GmbH.
 * Portions created by Initial Developer are Copyright (C) 2001-2006
 * by moleSoftware GmbH. All Rights Reserved.
 *
 * Portions created by the ispCP Team are Copyright (C) 2006-2010 by
 * isp Control Panel. All Rights Reserved.
 *
 * Portions created by the EasySCP Team are Copyright (C) 2010-2019 by
 * Easy Server Control Panel. All Rights Reserved.
 */

/**
 * check_input checks HTML fields of type <input> for content that could be
 * related to cross site scripting. The function will call EasySCP_Exception() if
 * any of the defined commands is entered in a protected <input> field. An
 * additional information is displayed to the user.
 *
 * @input String $value The submitted string in the <input> field (value)
 */
function check_input($value = '') {
	// possible commands for XSS are stored in $CHECK_VARS
	$CHECK_VARS = array();
	$CHECK_VARS[] = "/wget /i";
	$CHECK_VARS[] = "/chmod /i";
	$CHECK_VARS[] = "/chown /i";
	$CHECK_VARS[] = "/lnyx /i";
	$CHECK_VARS[] = "/curl /i";
	$CHECK_VARS[] = "/fopen /i";
	$CHECK_VARS[] = "/mkdir /i";
	$CHECK_VARS[] = "/passwd /i";
	$CHECK_VARS[] = "/http:/i";
	$CHECK_VARS[] = "/ftp:/i";

	$CHECK_VARS[] = "/content-type:/i";
	$CHECK_VARS[] = "/content-transfer-encoding:/i";
	$CHECK_VARS[] = "/mime-version:/i";
	$CHECK_VARS[] = "/subject:/i";
	$CHECK_VARS[] = "/to:/i";
	$CHECK_VARS[] = "/cc:/i";
	$CHECK_VARS[] = "/bcc:/i";
	$CHECK_VARS[] = "/\r/";
	$CHECK_VARS[] = "/\n/";
	$CHECK_VARS[] = "/%0a/";
	$CHECK_VARS[] = "/%0d/";

	if (!empty($value)) {
		$value = strtolower($value);

		foreach ($CHECK_VARS as $VAR) {
			if (preg_match($VAR, $value) > 0) {
				$message = "Possible hacking attempt. Script terminated.";
				write_log($message);
				throw new EasySCP_Exception(tr($message));
			}
		}
	}
}

/**
 * clean_html replaces up defined inputs.
 *
 * @input String $text text string to be cleaned
 * @return String cleared text string
 */
function clean_html($text) {
	$search = array(
		'@<script[^>]*?>.*?</script[\s]*>@si',	// remove JavaScript
		'@<[\/\!]*?[^<>]*?>@si',				// remove HTML tags
		'@([\r\n])[\s]+@', 						// remove spaces
		'@&(quot|#34|#034);@i',					// change HTML entities
		'@&(apos|#39|#039);@i',					// change HTML entities
		'@&(amp|#38);@i',
		'@&(lt|#60);@i',
		'@&(gt|#62);@i',
		'@&(nbsp|#160);@i',
		'@&(iexcl|#161);@i',
		'@&(cent|#162);@i',
		'@&(pound|#163);@i',
		'@&(copy|#169);@i'/*,
		'@&#(\d+);@e'*/
	); // handle as php

	$replace = array(
		'',
		'',
		'\1',
		'"',
		"'",
		'&',
		'<',
		'>',
		' ',
		chr(161),
		chr(162),
		chr(163),
		chr(169),
		//'chr(\1)'
	);

	$text = preg_replace($search, $replace, $text);
	// and second one...
	$text = strip_tags($text);

	return $text;
}

/**
 * clean_input
 *
 * @param String $input input data (eg. post-var) to be cleaned
 * @param boolean $htmlencode should return value be html encoded (& -> &amp;)
 * @return String {|} trimmed, stripslashed, eventually htmlencoded input string
 */
function clean_input($input, $htmlencode = false) {
	if ((strpos($input, "{") == 0)
		&& (strpos($input, "}") == strlen($input)-1)) {
		$input = trim($input, "{..}");
	}

	if (get_magic_quotes_gpc()) {
		$input = stripslashes($input);
	}
	if ($htmlencode) {
		return htmlentities($input, ENT_QUOTES, "UTF-8");
	} else {
		return $input;
	}
}

/**
 * Convert any text to HTML
 * @param string $text
 * @return string HTML entitied text
 */
function tohtml($text) {
	return htmlentities($text, ENT_QUOTES, "UTF-8");
}

/**
 * Convert any text to JavaScript text
 * @param  $text
 * @return string JavaScript text
 */
function tojs($text) {
	$result = htmlentities($text, ENT_COMPAT, "UTF-8");
	$result = strtr($result, array('\\'=>'\\\\',"'"=>"\\'",'"'=>'\\"',"\r"=>'\\r',"\n"=>'\\n','</'=>'<\/'));

	return $result;
}

/**
 * Passwort check
 *
 * Check if a password is valid
 *
 * @author		EasySCP Team
 * @version		1.01
 *
 * @access	public
 * @param	string	$data		username to be checked
 * @param	int		$num		number of max. chars
 * @param	string	$permitted	RegExp of permitted chars
 * @return	boolean				valid password or not
 */
function chk_password($password, $num = 50, $permitted = "") {

	$cfg = EasySCP_Registry::get('Config');

	if ($num > 255) {
		$num = 255;
	} else if ($num < 6) {
		$num = 6;
	}

	$len = strlen($password);
	if ($len < $cfg->PASSWD_CHARS || $len > $num) {
		return false;
	}

	if (!empty($permitted) && (bool)preg_match($permitted, $password)) {
		return false;
	}

	if ($cfg->PASSWD_STRONG) {
		return (bool)(preg_match("/[0-9]/", $password)
			&& preg_match("/[a-zA-Z]/", $password));
	} else {
		return true;
	}
}

/**
 * Validates a username
 *
 * This function validates syntax of usernames.
 * The characters allowed are all alphanumeric in
 * upper or lower case, the hyphen , the low dash
 * and  the dot, the three latter  being banned at
 * the beginning and end of string.
 *
 * Successive instances of a dot or underscore are prohibited
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0
 * @param string $username the username to be checked
 * @param int $min_char number of min. chars
 * @param int $max_char number min. chars
 * @return boolean True if the username is valid, FALSE otherwise
 */
function validates_username($username, $min_char = 2, $max_char = 30) {

	$pattern = '@^[[:alnum:]](:?(?<![-_])(:?-*|[_.])?(?![-_])[[:alnum:]]*)*?(?<![-_.])$@';

	return (bool) (preg_match($pattern, $username) &&
		strlen($username) >= $min_char &&
		strlen($username) <= $max_char );
}

/**
 * Validates an e-mail address
 *
 * This function should validate an e-mail address according to RFC 2822.
 * {@see _validates_tld} will be used to validate the top level domain of the 
 * address.
 *
 * @version 1.1
 * @param String $email the e-mail address to verify
 * @param int $num maximum lenght of the e-mail address (Default: 255)
 * @return boolean whether the address is a valid e-mail address
 */
function chk_email($email, $num = 255) {
	if (strlen($email) > $num) {
		return false;
	}

	/*
	 * get the domain part (part after the last @) and re-combine the rest to
	 * the user part.
	 */
	$labels = explode('@', $email);
	$domainPart = array_pop($labels);
	$userPart = implode('@', $labels);

	/**
	 * Last part contains the Domain name, we validate with
	 * {@see validates_dname}, the user part is validated with
	 * {@see easyscp_check_local_part}
	 */
	if (validates_dname($domainPart, true) &&
		easyscp_check_local_part($userPart, strlen($userPart)))
		return true;

	return false;
}

/**
 * Validates the user part of an e-mail address
 *
 * This function validates an e-mail address' user part according to RFC 2822.
 *
 * @param string $userPart the user part of the e-mail address
 * @param int $num maximum lenght of the e-mail address (Default: 150)
 * @return boolean whether the user part is valid
 */
function easyscp_check_local_part($userPart, $num = 150) {
	if (strlen($userPart) > $num) {
		return false;
	}
	// RegEx begin
	$nonascii = "\x80-\xff"; // non ASCII chars are not allowed

	$nqtext = "[^\\\\$nonascii\015\012\"]"; // all not quoteable chars
	$qchar = "\\\\[^$nonascii]";			// matched quoted chars

	$normuser = '[a-zA-Z0-9][a-zA-Z0-9_.-]*';
	$quotedstring = "\"(?:$nqtext|$qchar)+\"";
	$regex = "(?:$normuser|$quotedstring)";
	// RegEx end

	return (bool) preg_match("/^$regex$/", $userPart);
}

/**
 * Functions to validate the syntax of domain names - begin
 */

/**
 * Validates a domain name
 *
 * This function validates a domain name. Here domains are
 * limited to {label[s]}.{sld.tld|tld} parts.
 *
 * For new domain names validations, the maximum number of labels
 * is determined by the 'MAX_DNAMES_LABELS' parameter value .
 * This parameter can be overridden by admin in the frontend. If this
 * function is called for the subdomain validation, the maximum number
 * of labels is set differently to avoid problems if admin has changed the
 * parameter value.
 *
 * The string representing the domain name must not exceed 255 characters
 * according RFC 1123.
 *
 * Labels syntax: See {@link _validates_dname_label()}
 *
 * SLDs syntax: See {@link _validates_sld()}
 *
 * TLDs syntax: See {@link _validates_tld()}
 *
 * @param string $dname domain name to be check
 * @param boolean $subdname_process Process up to 99 sub domains instead of MAX_DNAMES_LABELS
 * @return boolean TRUE if successful, FALSE otherwise
 */
function validates_dname($dname, $subdname_process = false) {

	global $validation_err_msg;
	$cfg = EasySCP_Registry::get('Config');
	$validation_err_msg = tr('Wrong domain name syntax or number of labels');

	$max_labels = ($subdname_process) ? 99 : $cfg->MAX_DNAMES_LABELS;

	if (!$subdname_process) {

		// Check lenght according RFC 1123 (Max of 255 chars)
		if (strlen($dname) > 255) {
			$validation_err_msg = tr('Wrong domain name lenght!');
			return false;
		}
	}

	$pattern = "@^((?:[^.]+\.){0,{$max_labels}})(?:([^.]+)\.)([^.]+)$@i";

	$matches = array();

	if ( ($ret = preg_match($pattern, $dname, $matches)) ) {

		$labels = preg_split('/\./', $matches[1], -1, PREG_SPLIT_NO_EMPTY);

		// Validates label(s)
		foreach ($labels as $label) {
			if (!_validates_dname_label($label)) {
				$ret = false;
				break;
			}
		}

		if ($ret && _validates_sld($matches[2] . '.' . $matches[3]) &&
			_validates_tld($matches[3])) {
			return true;
		}
	}

	return false;
} // end validates_dname()

/**
 * Validates a subdomain name
 *
 * This function validates a subdomain. Here, a subdomain is
 * limited to {label[s]}.{sld.tld|tld} parts.
 *
 * The maximum number of labels is determined by
 * the 'MAX_SUBDNAMES_LABELS' parameter value.
 * This parameter can be overridden by admin in the frontend.
 *
 * Labels 'www|ftp|mail|ns[1-2]?' cannot be used as the first part
 * of a subdomain.
 *
 * The string representing the subdomain name must not exceed
 * 255 characters according RFC 1123.
 *
 * See {@link validates_dname()} for more information about
 * the allowed syntax for labels, SLDs and TLDs.
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.1
 * @since r2228
 * @param string $subdname labels of subdomain name to be check
 * @param string $dname full domain name to be check
 * @return boolean TRUE if successful, FALSE otherwize
 */
function validates_subdname($subdname, $dname) {

	$cfg = EasySCP_Registry::get('Config');
	global $validation_err_msg;
	$validation_err_msg = tr('Wrong subdomain syntax or number of labels!');

	// Check lenght according RFC 1123 (Max of 255 chars)
	if (strlen($subdname . '.' . $dname) > 255){
		$validation_err_msg = tr('Wrong subdomain lenght!');
		return false;
	}

	// Counts the number of labels (Only in the domain name part)
	$dname_nb_labels = count(explode('.', $dname)) -1;

	// Retrieves the maximum number of labels for the subdomain
	$subdname_nb_labels = $cfg->MAX_SUBDNAMES_LABELS;

	$matches = array();

	// Check number of labels and get the data
	// TRUE if the subdomain syntax or number of labels is correct
	// TRUE with $matches[1] set and no empty if the first label of the subdomain is reserved
	// FALSE if the subdomain syntax is wrong
	$pattern = "@^
		(?:(www|ftp|mail|ns[1-2]?)\.)?
		((?:[^.]+\.){0,{$subdname_nb_labels}})
		((?:[^.]+\.){{$dname_nb_labels}})
		([^.]+)
	$@x";

	if (($ret = preg_match($pattern, $subdname . '.' . $dname , $matches)) && !empty($matches[1])) {
		$validation_err_msg = tr('Label not allowed: <strong>%s</strong>', $matches[1]);
		$ret = false;
	}

	if ($ret && $sub_labels = preg_split('/\./', $matches[2], -1, PREG_SPLIT_NO_EMPTY)) {

		// Validates subdomains label[s]
		foreach ($sub_labels as $label) {
			if (!_validates_dname_label($label) && !$ret = false) {
				break;
			}
		}
	}

	return (bool) ($ret && validates_dname($matches[3] . $matches[4], true));
}

/**
 * Validates a domain name label located below the SLD
 *
 * Here, a label is represented by a token domain name that
 * is located before the Second Level Domain name. TLDs and
 * SLDs have their own validation functions.
 *
 * Accepted formats for labels are:
 *
 * - ASCII format according RFCs 1123, 1035
 * - Internationalized labels in ToUnicode format according RFC 3490
 *
 *  For example, the ACE label 'xn--bcher-kva' should be submited as 'bücher'.
 *
 * A label must not exceed 63 characters according RFCs 1123, 1035.
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.1
 * @since r2228
 * @access private
 * @param string $label label to be validates
 * @return boolean TRUE if successful, FALSE otherwise
 */
function _validates_dname_label($label) {

	global $validation_err_msg;
	$validation_err_msg = tr('Wrong label syntax: <strong>%s</strong>', $label);

	mb_internal_encoding('UTF-8');

	if (!isACE($label) &&
		mb_strpos($label, '-') !== 0 &&
		(mb_strrpos($label, '-') !== (mb_strlen($label)-1)) &&
		mb_substr($label, 2, 2, 'utf-8') !== '--') {

		$label = encode_idna($label);

		$matches = array();

		// TRUE if the label syntax and lenght is correct
		// TRUE with $matches[1] set if the label lenght is wrong
		// FALSE if the label syntax is wrong
		$pattern = '@^(?:[a-z0-9][-a-z0-9]{0,61}[a-z0-9]?(?<!-)|([-a-z0-9]{64,}))$@i';

		if (($ret = preg_match($pattern, $label, $matches)) && array_key_exists(1, $matches) ) {
			$validation_err_msg = tr('Wrong label lenght: <strong>%s</strong>', $label);
			$ret = false;
		}

	} else {
		$ret = false;
	}

	return (bool) $ret;
}

/**
 * Validates a Top Level Domain
 *
 * The validation can be strict or not. If strict, the Top Level
 * Domain's must be listed in Iana  root database. Otherwise, the
 * syntax must respect the realistic usage of TLD's.
 *
 * In both case, the used rule for the permitted characters is based
 * on the realistic usage of Top level Domains.
 *
 * See {@link http://www.iana.org/domains/root/db/# Iana Root Zone Database}
 * for more information about the Iana TLD's list.
 *
 * @param string $tld
 * @return boolean TRUE if successfull, FALSE otherwise
 */
function _validates_tld($tld) {

	$cfg = EasySCP_Registry::get('Config');
	global $validation_err_msg;
	$validation_err_msg = tr('Wrong Top Level Domain syntax: <strong>%s</strong>', $tld);

	$matches = array();

	if ($cfg->TLD_STRICT_VALIDATION) {
		$ianaTLDXML = simplexml_load_file(EasyConfig_PATH . '/Iana_TLD.xml');
		
		$idnTld = idn_to_ascii($tld);
		// $idnTld = idn_to_ascii($tld,IDNA_NONTRANSITIONAL_TO_ASCII,INTL_IDNA_VARIANT_UTS46);
		$search = "//domain[tld='".$idnTld."']";
		if ($ianaTLDXML->xpath($search)) {
			return true;
		} else {
			return false;
		}
	} else {

		// This pattern matches only realistic TLDs (i.e. those with 2 to 6 letters) - Not strict.
		// TRUE if the TLD syntax and lenght is correct
		// TRUE with $matches[1] set if the TLD lenght is wrong
		// FALSE if the TLD syntax is wrong
		$pattern = '@^(?:[a-z]{2,6}|([a-z]|[a-z]{7,}))$@';
	}

	if (($ret = preg_match($pattern, $tld, $matches)) && array_key_exists(1, $matches)) {
		$validation_err_msg = tr('Wrong Top Level Domain lenght: <strong>%s</strong>', $tld);
		$ret = false;
	}

	return (bool) $ret;
} // end _validates_tld()

/**
 * Validates an Second Level Domain
 *
 * This function validates an SLD (SC-LD, ccSLD...)
 *
 * S-C SLD (or S-LSLD):
 * Single-character second-level domains are domain names
 * in which the second-level domain consists of only one
 * letter, such as 'i.net', 'x.com'.
 *
 * ccSLD:
 * A country code second-level domain (ccSLD) is a
 * second-level domain to a country code top-level domain
 * such as {com, net and org}, e.g. .com.sg.
 *
 * The SLDs 'example.com', 'example.net' and 'example.org' are
 * reserved (See RFC 2606, Section 3)
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.1
 * @since r2228
 * @access private
 * @param string $sld sld to be validates
 * @return boolean TRUE if the syntax is valid, FALSE otherwise
 */
function _validates_sld($sld) {

	global $validation_err_msg;
	$cfg = EasySCP_Registry::get('Config');

	if ($cfg->SLD_STRICT_VALIDATION) {

		// Single-Character (SC) SLD
		// Note: All another SC SLD are presently reserved in
		// all gTLD registry agreements except for
		//  * Island,
		//  * Germany since 2009/10/23
		$scSLD =
			'i\.net|'.
			'q\.(?:com|net)|'.
			'x\.org|'.
			'[xz]\.com|'.
			'[a-z0-9]\.de|'.
			'[a-z0-9]\.is';

		// Reserved SLD according RFC 2606
		$reserved_SLD = 'example\.(?:com|net|org)';

		// TRUE if the SLD syntax and lenght is correct
		// TRUE with $matches[1] set if the SLD is reserved
		// TRUE with $matches[2] set if the SLD lenght is wrong
		// FALSE if the SLD syntax is wrong
		$pattern = "@^
			(?:($reserved_SLD)|
			$scSLD|
			(?:(?:[a-z0-9](?:[a-z0-9]|-+(?!\.))(?:[-a-z0-9](?!\.)){0,60}[a-z0-9]?)|([-a-z0-9]{64,}))\.)
		@x";

		$matches = array();

		if (!isACE($sld)) {

			mb_internal_encoding('UTF-8');

			if (mb_strpos($only_sld_part = mb_substr($sld, 0, mb_strpos($sld, '.')), '-') !== 0 &&
				(mb_strrpos($only_sld_part, '-') !== (mb_strlen($only_sld_part)-1)) &&
				mb_substr($sld, 2, 2, 'utf-8') !== '--' &&
				preg_match($pattern, encode_idna($sld), $matches)) {

				if (array_key_exists(2, $matches)) {
					$validation_err_msg = tr('Wrong Second Level Domain lenght: <strong>%s</strong>', $only_sld_part);
				} elseif (array_key_exists(1, $matches)) {
					$validation_err_msg = tr('Wrong domain name: <strong>%s</strong> is reserved!', $sld);
				}

				$ret = true;
			} else {

				$validation_err_msg = tr('Wrong Second Level Domain syntax: <strong>%s</strong>', $only_sld_part);
				$ret = false;
			}

		} else {
			$ret = false;
		}
		return (bool) ($ret && count($matches) <= 1);

	} else {
		return _validates_dname_label(substr($sld, 0, strpos($sld, '.')));
	}

} // end _validates_sld()

/**
 * Check if a domain name label is an ACE label
 *
 * According Rfc 3490, an ACE label is an internationalised
 * label in ASCII format with the ACE prefix 'xn--'.
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version 1.0
 * @since r2266
 * @param string $label label to be validates
 * @return boolean TRUE if the string is an ACE lable, FALSE otherwise
 * @todo Check ASCII range
 */
function isACE($label) {

	global $validation_err_msg;

	// Check if the input is an ACE label
	if (strpos($label, 'xn--') === 0) {

		$validation_err_msg = tr(
			"ERROR: ACE labels are not allowed. Please use the ToUnicode equivalent.<br />".
			"<small>Example: for ACE label <strong>xn--bcher-kva</strong> use <strong>bücher</strong> instead</small>."
		);

		return true;
	}

	return false;
}

/**
 * Functions to validate the syntax of domain names- end
 */

/**
 * Function for checking EasySCP limits.
 *
 * @param string $data EasySCP 'limit' field data (by default valids are numbers greater equal 0)
 * @param mixed $extra single extra permitted value or array of permitted values
 * @return boolean whether syntax is correct
 * @example easyscp_limit_check($_POST['domains_limit'], null)
 * @example easyscp_limit_check($_POST['ftp_accounts_limit'])
 *
 * @todo foreach and "=" inner this loop is unusual
 */
function easyscp_limit_check($data, $extra = -1) {
	if (!is_null($extra) && !is_bool($extra)) {
		if (is_array($extra)) {
			$nextra = '';
			$max = count($extra);

			foreach ($extra as $n => $element) {
				$nextra = $element . ($n < $max) ? '|' : '';
			}

			$extra = $nextra;
		} else {
			$extra .= '|';
		}
	} else {
		$extra = '';
	}

	return (bool)preg_match("/^(${extra}0|[1-9][0-9]*)$/D", $data);
}

/**
 * Validates a mount point
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version	1.0
 * @since r2228
 * @param string $token mount point to validate
 * @param int|null $max_token_char number of max. chars by token.
 *	Set as null for no limit
 * @return boolean True if the mount point is valid, FALSE otherwise
 */
function validates_mpoint($mpoint, $max_token_char = null) {

	$pattern = '@^((:?|(:?[[:alnum:]]|/|/(?:htdocs|backups|cgi-bin|errors|logs|phptmp)[/]?))|.+/|.*//.*)$@';

	if (preg_match($pattern, $mpoint)) {
		return false;
	}

	$tokens = preg_split('@/@', $mpoint, -1, PREG_SPLIT_NO_EMPTY);

	foreach ($tokens as $token) {
		if (!_validates_mpoint_token($token, $max_token_char)) {
			return false;
		}
	}

	return true;
}

/**
 * Validates a mount point token
 *
 * This function checks syntax of mount point token.
 * The characters allowed are all alphanumeric in
 * upper or lower case, the hyphen , the low dash
 * and  the dot, the three latter  being banned at
 * the beginning and end of token.
 *
 * Successive instances of a dot or underscore are prohibited
 *
 * @author Laurent Declercq <l.declercq@nuxwin.com>
 * @version	1.0
 * @since r2228
 * @access private
 * @param string $token token of mount point to validate
 * @param int|null $max_char number of max. chars.
 *	Set as NULL for no limit
 * return boolean True if the token is valid, FALSE otherwise
 */
function _validates_mpoint_token($token, $max_char = null) {

	$pattern = '@^[[:alnum:]](:?(?<![-_])(:?-*|[_.])?(?![-_])[[:alnum:]]*)*?(?<![-_.])$@';

	return (bool) (preg_match($pattern, $token) && (is_null($max_char) || strlen($token) <= $max_char));
}

/**
 * @todo document this function
 */
function get_session($value) {
	if (array_key_exists($value, $_SESSION)) {
		return $_SESSION[$value];
	} else {
		return null;
	}
}

/**
 * All in one function to check who owns what.
 *
 * @param mixed $id FTP/mail/domain/alias/subdomain/etc id to check
 * @param string $type What kind of id $id is
 * @param boolean $forcefinal Ignore the resolver's is_final value (force as yes)
 * @return numeric The id of the admin who owns the id $id of $type type
 */
function who_owns_this($id, $type = 'dmn', $forcefinal = false) {
	$sql = EasySCP_Registry::get('Db');

	$who = null;
	// Fix $type according to type or by alias
	switch ($type) {
		case 'dmn_id':
			$type = 'domain_id';
			break;
		case 'sub_id':
			$type = 'subdomain_id';
			break;
		case 'als_id':
			$type = 'alias_id';
			break;
		case 'user':
			$type = 'client';
			break;
		case 'domain_uid':
			$type = 'uid';
			break;
		case 'ticket':
			$type = 'ticket_id';
			break;
		case 'domain_gid':
			$type = 'gid';
			break;
		case 'sqlu_id':
		case 'sqluser_id':
			$type = 'sql_user_id';
			break;
		case 'sqld_id':
		case 'sqldatabase_id':
			$type = 'sql_database_id';
			break;
		case 'ftpuser':
		case 'ftpuserid':
		case 'ftp_userid':
			$type = 'ftp_user';
			break;
		case 'sqluser':
		case 'sqlu':
		case 'sqlu_name':
			// Can't guess by type
			$type = 'sql_user';
			break;
		case 'sqldatabase':
		case 'sqld':
		case 'sqld_name':
			// Can't guess by type
			$type = 'sql_database';
			break;
		case 'dmn':
		case 'normal':
		case 'domain':
			if (!is_numeric($id)) {
				$type = 'domain';
			} else {
				$type = 'domain_id';
			}
			break;
		case 'als':
		case 'alias':
		case 'domain_alias':
			if (!is_numeric($id)) {
				$type = 'alias';
			} else {
				$type = 'alias_id';
			}
			break;
		case 'sub':
		case 'subdom':
		case 'subdomain':
			if (!is_numeric($id)) {
				$type = 'subdomain';
			} else {
				$type = 'subdomain_id';
			}
			break;
		case 'alssub':
			if (!is_numeric($id)) {
				$type = 'subdomain_alias';
			} else {
				$type = 'subdomain_alias_id';
			}
			break;
	}

	/**
	 * $resolvers is a multi-dimensional array.
	 * Its elements keys are the value that will be matched by $type.
	 * Each element is an array, containing at least two elements:
	 * 'query' and 'is_final'
	 * The former is the SQL query that should only SELECT one item; or false in case a query isn't used.
	 * The latter is a boolean which specifies whether the result of that 'resolver' is an admin id or not
	 *
	 * Other elements might be:
	 * 'next', 'separator', 'pos'
	 *
	 * 'next' is the $type value for the next call to who_owns_this (only used when 'is_final' is false)
	 * 'separator' is the separator to be used when exploding the $id (only used when 'query' is false)
	 * 'post' is the position in the array/result of exploding $id (only used when 'query' is false)
	 *
	 * NOTE: 'query' MUST be formated like: 'SELECT something FROM...' in order to correctly detect the field being selected
	 */
	$resolvers = array();

	$resolvers['domain_id'] = array();
	$resolvers['domain_id']['query'] = 'SELECT `domain_admin_id` FROM `domain` WHERE `domain_id` = ? LIMIT 1;';
	$resolvers['domain_id']['is_final'] = true;

	$resolvers['alias_id'] = array();
	$resolvers['alias_id']['query'] = 'SELECT `domain_id` FROM `domain_aliasses` WHERE `alias_id` = ? LIMIT 1;';
	$resolvers['alias_id']['is_final'] = false;
	$resolvers['alias_id']['next'] = 'dmn';

	$resolvers['alias'] = array();
	$resolvers['alias']['query'] = 'SELECT `domain_id` FROM `domain_aliasses` WHERE `alias_name` = ? LIMIT 1;';
	$resolvers['alias']['is_final'] = false;
	$resolvers['alias']['next'] = 'dmn';

	$resolvers['subdomain_id'] = array();
	$resolvers['subdomain_id']['query'] = 'SELECT `domain_id` FROM `subdomain` WHERE `subdomain_id` = ? LIMIT 1;';
	$resolvers['subdomain_id']['is_final'] = false;
	$resolvers['subdomain_id']['next'] = 'dmn';

	$resolvers['subdomain'] = array();
	$resolvers['subdomain']['query'] = false;
	$resolvers['subdomain']['separator'] = '.';
	$resolvers['subdomain']['pos'] = 1;
	$resolvers['subdomain']['is_final'] = false;
	$resolvers['subdomain']['next'] = 'dmn';

	$resolvers['subdomain_alias_id'] = array();
	$resolvers['subdomain_alias_id']['query'] = 'SELECT `alias_id` FROM `subdomain_alias` WHERE `subdomain_alias_id` = ? LIMIT 1;';
	$resolvers['subdomain_alias_id']['is_final'] = false;
	$resolvers['subdomain_alias_id']['next'] = 'alias';

	$resolvers['subdomain_alias'] = array();
	$resolvers['subdomain_alias']['query'] = false;
	$resolvers['subdomain_alias']['separator'] = '.';
	$resolvers['subdomain_alias']['pos'] = 1;
	$resolvers['subdomain_alias']['is_final'] = false;
	$resolvers['subdomain_alias']['next'] = 'alias';

	$resolvers['client'] = array();
	$resolvers['client']['query'] = 'SELECT `created_by` FROM `admin` WHERE `admin_id` = ? LIMIT 1;';
	$resolvers['client']['is_final'] = true;

	$resolvers['reseller'] = $resolvers['admin'] = $resolvers['client'];

	$resolvers['domain'] = array();
	$resolvers['domain']['query'] = 'SELECT `domain_admin_id` FROM `domain` WHERE `domain` = ? LIMIT 1;';
	$resolvers['domain']['is_final'] = true;

	$resolvers['ticket_id'] = array();
	$resolvers['ticket_id']['query'] = 'SELECT `ticket_from` FROM `ticket` WHERE `ticket_id` = ? LIMIT 1;';
	$resolvers['ticket_id']['is_final'] = true;

	$resolvers['uid'] = array();
	$resolvers['uid']['query'] = 'SELECT `domain_admin_id` FROM `domain` WHERE `domain_uid` = ? LIMIT 1;';
	$resolvers['uid']['is_final'] = true;

	$resolvers['gid'] = array();
	$resolvers['gid']['query'] = 'SELECT `domain_admin_id` FROM `domain` WHERE `domain_gid` = ? LIMIT 1;';
	$resolvers['gid']['is_final'] = true;

	$resolvers['gid'] = array();
	$resolvers['gid']['query'] = 'SELECT `domain_admin_id` FROM `domain` WHERE `domain_gid` = ? LIMIT 1;';
	$resolvers['gid']['is_final'] = true;

	$resolvers['ftp_user'] = array();
	$resolvers['ftp_user']['query'] = 'SELECT `uid` FROM `ftp_users` WHERE `userid` = ? LIMIT 1;';
	$resolvers['ftp_user']['is_final'] = false;
	$resolvers['ftp_user']['next'] = 'uid';

	$resolvers['sql_user_id'] = array();
	$resolvers['sql_user_id']['query'] = 'SELECT `sqld_id` FROM `sql_user` WHERE `sqlu_id` = ? LIMIT 1;';
	$resolvers['sql_user_id']['is_final'] = false;
	$resolvers['sql_user_id']['next'] = 'sqld_id';

	$resolvers['sql_database_id'] = array();
	$resolvers['sql_database_id']['query'] = 'SELECT `domain_id` FROM `sql_database` WHERE `sqld_id` = ? LIMIT 1;';
	$resolvers['sql_database_id']['is_final'] = false;
	$resolvers['sql_database_id']['next'] = 'dmn';

	$resolvers['sql_user'] = array();
	$resolvers['sql_user']['query'] = 'SELECT `sqld_id` FROM `sql_user` WHERE `sqlu_name` = ? LIMIT 1;';
	$resolvers['sql_user']['is_final'] = false;
	$resolvers['sql_user']['next'] = 'sqld_id';

	$resolvers['sql_database'] = array();
	$resolvers['sql_database']['query'] = 'SELECT `domain_id` FROM `sql_database` WHERE `sqld_name` = ? LIMIT 1;';
	$resolvers['sql_database']['is_final'] = false;
	$resolvers['sql_database']['next'] = 'dmn';

	$resolvers['mail_id'] = array();
	$resolvers['mail_id']['query'] = 'SELECT `domain_id` FROM `mail_users` WHERE `mail_id` = ? LIMIT 1;';
	$resolvers['mail_id']['is_final'] = false;
	$resolvers['mail_id']['next'] = 'dmn';

	$resolvers['mail'] = array();
	$resolvers['mail']['query'] = false;
	$resolvers['mail']['separator'] = '@';
	$resolvers['mail']['post'] = 1;
	$resolvers['mail']['is_final'] = false;
	$resolvers['mail']['next'] = 'dmn';

	$resolvers['htaccess_id'] = array();
	$resolvers['htaccess_id']['query'] = 'SELECT `dmn_id` FROM `htaccess` WHERE `id` = ? LIMIT 1;';
	$resolvers['htaccess_id']['is_final'] = false;
	$resolvers['htaccess_id']['next'] = 'dmn';

	$resolvers['htaccess_group_id'] = array();
	$resolvers['htaccess_group_id']['query'] = 'SELECT `dmn_id` FROM `htaccess_groups` WHERE `id` = ? LIMIT 1;';
	$resolvers['htaccess_group_id']['is_final'] = false;
	$resolvers['htaccess_group_id']['next'] = 'dmn';

	$resolvers['htaccess_user_id'] = array();
	$resolvers['htaccess_user_id']['query'] = 'SELECT `dmn_id` FROM `htaccess_users` WHERE `id` = ? LIMIT 1;';
	$resolvers['htaccess_user_id']['is_final'] = false;
	$resolvers['htaccess_user_id']['next'] = 'dmn';

	$resolvers['hosting_plan_id'] = array();
	$resolvers['hosting_plan_id']['query'] = 'SELECT `reseller_id` FROM `hosting_plans` WHERE `id` = ? LIMIT 1;';
	$resolvers['hosting_plan_id']['is_final'] = true;

	if (isset($resolvers[$type])) {
		$r = $resolvers[$type];
		if ($r['query']) {
			$matches = array();
			if (!preg_match('/SELECT[ \t]+`([\w]+)`[ \t]+FROM/i', $r['query'], $matches)) {
				throw new EasySCP_Exception(tr('Unknown Error'));
			}
			$select = $matches[1];
			$rs = exec_query($sql, $r['query'], $id);
			if ($rs->recordCount() != 0) {
				if ($r['is_final'] || $forcefinal) {
					$who = $rs->fields[$select];
				} else {
					$who = who_owns_this($rs->fields[$select], $r['next']);
				}
			}
		} else {
			$ex = explode($r['separator'], $id);
			if (!$r['is_final'] && !$forcefinal) {
				$who = who_owns_this($r['pos'], $r['next']);
			} else {
				$who = $ex[$r['pos']];
			}
		}
	}

	if ($type != 'admin' && (empty($who) || $who <= 0)) {
		$who = null;
	}

	return $who;
}
?>
