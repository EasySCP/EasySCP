<?php

//   -------------------------------------------------------------------------------
//  |                  net2ftp: a web based FTP client                              |
//  |              Copyright (c) 2003-2013 by David Gartner                         |
//  |                                                                               |
//  | This program is free software; you can redistribute it and/or                 |
//  | modify it under the terms of the GNU General Public License                   |
//  | as published by the Free Software Foundation; either version 2                |
//  | of the License, or (at your option) any later version.                        |
//  |                                                                               |
//   -------------------------------------------------------------------------------





// **************************************************************************************
// **************************************************************************************
// **                                                                                  **
// **                                                                                  **
function getLanguageArray() {

// --------------
// This function returns an array of languages
// Use the ISO 639 code described here: http://www.w3.org/WAI/ER/IG/ert/iso639.htm
// --------------

	$languageArray["en"]["name"] = "English";
	$languageArray["en"]["file"] = "en.inc.php";
	$languageArray["de"]["name"] = "German";
	$languageArray["de"]["file"] = "de.inc.php";

	return $languageArray;

} // End function getLanguageArray

// **                                                                                  **
// **                                                                                  **
// **************************************************************************************
// **************************************************************************************





// **************************************************************************************
// **************************************************************************************
// **                                                                                  **
// **                                                                                  **
function printLanguageSelect($fieldname, $onchange, $style, $class) {


// --------------
// This function prints a select with the available languages
// Language nr 1 is the default language
// --------------

	global $net2ftp_globals;
	$languageArray = getLanguageArray();

	if ($net2ftp_globals["language"] != "") { $currentlanguage = $net2ftp_globals["language"]; }
	else                                    { $currentlanguage = "en"; }

	if ($onchange == "") { $onchange_full = ""; }
	else                 { $onchange_full = "onchange=\"$onchange\""; }

	if ($style == "")    { $style_full = ""; }
	else                 { $style_full = "style=\"$style\""; }

	if ($class == "")    { $class_full = ""; }
	else                 { $class_full = "class=\"$class\""; }

	echo "<select name=\"$fieldname\" id=\"$fieldname\" $onchange_full $style_full $class_full>\n";

	while (list($key,$value) = each($languageArray)) {
	// $key loops over "en", "fr", "nl", ...
	// $value will be an array like $value["name"] = "English" and $value["file"] = "en.inc.php"
		if ($key == $currentlanguage) { $selected = "selected=\"selected\""; }
		else                          { $selected = ""; }
		echo "<option value=\"" . $key . "\" $selected>" . $value["name"] . "</option>\n";
	} // end while

	echo "</select>\n";

} // End function printLanguageSelect

// **                                                                                  **
// **                                                                                  **
// **************************************************************************************
// **************************************************************************************







// **************************************************************************************
// **************************************************************************************
// **                                                                                  **
// **                                                                                  **

function includeLanguageFile() {

// -------------------------------------------------------------------------
// Global variables
// -------------------------------------------------------------------------
	global $net2ftp_globals, $net2ftp_messages;
	$languageArray = getLanguageArray();

// If language exists, include the language file
	if (array_key_exists($net2ftp_globals["language"], $languageArray) == true) { 
		$languageFile = glueDirectories($net2ftp_globals["application_languagesdir"], $languageArray[$net2ftp_globals["language"]]["file"]);
		require_once($languageFile); 
	}

// If it does not exist, use the default language nr "en" (English)
	else { 
		$net2ftp_globals["language"] = "en";
		$languageFile = glueDirectories($net2ftp_globals["application_languagesdir"], $languageArray[$net2ftp_globals["language"]]["file"]);
		require_once($languageFile);
	}

} // end  function includeLanguageFile

// **                                                                                  **
// **                                                                                  **
// **************************************************************************************
// **************************************************************************************





// **************************************************************************************
// **************************************************************************************
// **                                                                                  **
// **                                                                                  **

function __() {

// --------------
// This function returns a translated message; the core standard function used is sprintf (see manual)
// Input: - from function argument: message name $args[0] and variable parts in the message $args[1], $args[2],... 
//                               (there is a variable nr of variable parts)
//        - from globals: the array of messages $message
// Output: string in the language indicated in $net2ftp_language
// --------------

// -------------------------------------------------------------------------
// Global variables
// -------------------------------------------------------------------------
	global $net2ftp_globals, $net2ftp_messages;


// -------------------------------------------------------------------------
// Get the arguments of this function
// $args[0] contains the messagename
// $args[1], $args[2], ... contain the variables in the message
// -------------------------------------------------------------------------
	$numargs = func_num_args();
	$args = func_get_args();
	$messagename = $args[0];

// -------------------------------------------------------------------------
// Create the argument for the sprintf function
// Aim is to have something like:  sprintf($string_with_percents, $args[1], $args[2], ...);
// As there is a variable nr of arguments in the function __, there is also a variable 
// nr of arguments in sprintf, and this must be constructed with a loop
// -------------------------------------------------------------------------

// Check if the message with that $messagename exists
	if (@array_key_exists($messagename, $net2ftp_messages)) { $string_with_percents = $net2ftp_messages[$messagename]; }
	else { return "MESSAGE NOT FOUND"; }

	$sprintf_argument = "\$translated_string = sprintf(\$string_with_percents";

	for ($i=1; $i<$numargs; $i++) {
		$sprintf_argument .= ",  @htmlentities(\$args[$i], ENT_QUOTES)";
	} // end for

	$sprintf_argument .= ");";

// -------------------------------------------------------------------------
// Run the sprintf function
// -------------------------------------------------------------------------
	eval($sprintf_argument);

	return $translated_string;

} // end function __

// **                                                                                  **
// **                                                                                  **
// **************************************************************************************
// **************************************************************************************

?>