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

//   -------------------------------------------------------------------------------
//  | For credits, see the credits.txt file                                         |
//   -------------------------------------------------------------------------------
//  |                                                                               |
//  |                              INSTRUCTIONS                                     |
//  |                                                                               |
//  |  The messages to translate are listed below.                                  |
//  |  The structure of each line is like this:                                     |
//  |     $message["Hello world!"] = "Hello world!";                                |
//  |                                                                               |
//  |  Keep the text between square brackets [] as it is.                           |
//  |  Translate the 2nd part, keeping the same punctuation and HTML tags.          |
//  |                                                                               |
//  |  The English message, for example                                             |
//  |     $message["net2ftp is written in PHP!"] = "net2ftp is written in PHP!";    |
//  |  should become after translation:                                             |
//  |     $message["net2ftp is written in PHP!"] = "net2ftp est ecrit en PHP!";     |
//  |     $message["net2ftp is written in PHP!"] = "net2ftp is geschreven in PHP!"; |
//  |                                                                               |
//  |  Note that the variable starts with a dollar sign $, that the value is        |
//  |  enclosed in double quotes " and that the line ends with a semi-colon ;       |
//  |  Be careful when editing this file, do not erase those special characters.    |
//  |                                                                               |
//  |  Some messages also contain one or more variables which start with a percent  |
//  |  sign, for example %1\$s or %2\$s. The English message, for example           |
//  |     $messages[...] = ["The file %1\$s was copied to %2\$s "]                  |
//  |  should becomes after translation:                                            |
//  |     $messages[...] = ["Le fichier %1\$s a �t� copi� vers %2\$s "]             |
//  |                                                                               |
//  |  When a real percent sign % is needed in the text it is entered as %%         |
//  |  otherwise it is interpreted as a variable. So no, it's not a mistake.        |
//  |                                                                               |
//  |  Between the messages to translate there is additional PHP code, for example: |
//  |      if ($net2ftp_globals["state2"] == "rename") {           // <-- PHP code  |
//  |          $net2ftp_messages["Rename file"] = "Rename file";   // <-- message   |
//  |      }                                                       // <-- PHP code  |
//  |  This code is needed to load the messages only when they are actually needed. |
//  |  There is no need to change or delete any of that PHP code; translate only    |
//  |  the message.                                                                 |
//  |                                                                               |
//  |  Thanks in advance to all the translators!                                    |
//  |  David.                                                                       |
//  |                                                                               |
//   -------------------------------------------------------------------------------


// -------------------------------------------------------------------------
// Language settings
// -------------------------------------------------------------------------

// HTML lang attribute
$net2ftp_messages["en"] = "de";

// HTML dir attribute: left-to-right (LTR) or right-to-left (RTL)
$net2ftp_messages["ltr"] = "ltr";

// CSS style: align left or right (use in combination with LTR or RTL)
$net2ftp_messages["left"] = "left";
$net2ftp_messages["right"] = "right";

// Encoding
$net2ftp_messages["iso-8859-1"] = "UTF-8";


// -------------------------------------------------------------------------
// Status messages
// -------------------------------------------------------------------------

// When translating these messages, keep in mind that the text should not be too long
// It should fit in the status textbox

$net2ftp_messages["Connecting to the FTP server"] = "Verbindung zum FTP-Server wird hergestellt";
$net2ftp_messages["Logging into the FTP server"] = "Einloggen beim FTP-Server";
$net2ftp_messages["Setting the passive mode"] = "Wechsle in den Passiven Modus";
$net2ftp_messages["Getting the FTP system type"] = "Erfrage den FTP-Systemtyp";
$net2ftp_messages["Changing the directory"] = "Wechsle das Verzeichnis";
$net2ftp_messages["Getting the current directory"] = "Aktuelles Verzeichnis wird geladen";
$net2ftp_messages["Getting the list of directories and files"] = "Ordner- und Dateiliste wird empfangen";
$net2ftp_messages["Parsing the list of directories and files"] = "Gliedere die Liste der Verzeichnisse und Dateien";
$net2ftp_messages["Logging out of the FTP server"] = "Verbindung wird getrennt";
$net2ftp_messages["Getting the list of directories and files"] = "Ordner- und Dateiliste wird empfangen";
$net2ftp_messages["Printing the list of directories and files"] = "Ordner- und Dateiliste wird erstellt";
$net2ftp_messages["Processing the entries"] = "Verarbeiten der Eintr&auml;ge";
$net2ftp_messages["Processing entry %1\$s"] = "Verarbeite Eintrag %1\$s";
$net2ftp_messages["Checking files"] = "Verarbeiten der Dateien";
$net2ftp_messages["Transferring files to the FTP server"] = "Dateien werden zum FTP-Server geschickt";
$net2ftp_messages["Decompressing archives and transferring files"] = "Archive werden entpackt und die Dateien transferiert";
$net2ftp_messages["Searching the files..."] = "Dateien werden gesucht...";
$net2ftp_messages["Uploading new file"] = "Upload der neuen Datei";
$net2ftp_messages["Reading the file"] = "Lesen der Datei";
$net2ftp_messages["Parsing the file"] = "Gliederung der Datei";
$net2ftp_messages["Reading the new file"] = "Lesen der neuen Datei";
$net2ftp_messages["Reading the old file"] = "Lesen der alten Datei";
$net2ftp_messages["Comparing the 2 files"] = "Vergleich der 2 Dateien";
$net2ftp_messages["Printing the comparison"] = "Ausgabe des Vergleichs";
$net2ftp_messages["Sending FTP command %1\$s of %2\$s"] = "Sende FTP-Beefehl %1\$s von %2\$s";
$net2ftp_messages["Getting archive %1\$s of %2\$s from the FTP server"] = "&Uuml;bertrage Archiv %1\$s von %2\$s vom FTP-Server";
$net2ftp_messages["Creating a temporary directory on the FTP server"] = "Erzeuge ein tempor&auml;res Verzeichnis auf dem FTP-Server";
$net2ftp_messages["Setting the permissions of the temporary directory"] = "Setze die Berechtigungen des tempor&auml;ren Verzeichnisses ";
$net2ftp_messages["Copying the net2ftp installer script to the FTP server"] = "Kopiere das net2ftp-Installationsskript zum FTP-Server";
$net2ftp_messages["Script finished in %1\$s seconds"] = "Skript beendet in %1\$s Sekunden";
$net2ftp_messages["Script halted"] = "Script angehalten";

// Used on various screens
$net2ftp_messages["Please wait..."] = "Bitte warten...";


// -------------------------------------------------------------------------
// index.php
// -------------------------------------------------------------------------
$net2ftp_messages["Unexpected state string: %1\$s. Exiting."] = "Unerwartete Zustandszeichenkette: %1\$s. Beende.";
$net2ftp_messages["This beta function is not activated on this server."] = "Dies Beta-Funktion ist auf Ihrem Server nicht aktiviert.";
$net2ftp_messages["This function has been disabled by the Administrator of this website."] = "Diese Funktion wurde vom Administrator der Webseite deaktiviert.";


// -------------------------------------------------------------------------
// /includes/browse.inc.php
// -------------------------------------------------------------------------
$net2ftp_messages["The directory <b>%1\$s</b> does not exist or could not be selected, so the directory <b>%2\$s</b> is shown instead."] = "Das Verzeichnis <b>%1\$s</b> existiert nicht oder kann nicht ausgew&auml;hlt werden, deshalb wird das Verzeichnis <b>%2\$s</b> stattdessen angezeigt.";
$net2ftp_messages["Your root directory <b>%1\$s</b> does not exist or could not be selected."] = "Ihr Root-Verzeichnis <b>%1\$s</b> existiert nicht oder kann nicht ausgew&auml;hlt werden.";
$net2ftp_messages["The directory <b>%1\$s</b> could not be selected - you may not have sufficient rights to view this directory, or it may not exist."] = "Das Verzeichnis <b>%1\$s</b> kann nicht ausgew&auml;hlt werden - entweder Sie haben nicht die entsprechenden Rechte, um das Verzeichnis anzuzeigen, oder es existiert nicht.";
$net2ftp_messages["Entries which contain banned keywords can't be managed using net2ftp. This is to avoid Paypal or Ebay scams from being uploaded through net2ftp."] = "Eintr&auml;ge mit verbotenen Schl&uuml;sselw&ouml;rtern k&ouml;nnen nicht mit net2ftp verwaltet werden. Dies dient dazu, um zu verhindern, dass Paypal or Ebay scams mit net2ftp hochgeladen werden.";
$net2ftp_messages["Files which are too big can't be downloaded, uploaded, copied, moved, searched, zipped, unzipped, viewed or edited; they can only be renamed, chmodded or deleted."] = "Dateien, die zu gro&szlig; sind, k&ouml;nnen nicht herunterladen, hochgeladen, kopiert, verschoben, gesucht, gepackt, entpackt, betrachtet oder bearbeitet werden; sie k&ouml;nnen nur umbenannt, gel&ouml;scht oder die Zugriffsrechte ge&auml;ndert werden.";
$net2ftp_messages["Execute %1\$s in a new window"] = "In einem neuen Fenster %1\$s ausf&uuml;hren";


// -------------------------------------------------------------------------
// /includes/main.inc.php
// -------------------------------------------------------------------------
$net2ftp_messages["Please select at least one directory or file!"] = "Bitte mindestens eine Datei oder ein Verzeichniss ausw&auml;hlen!";


// -------------------------------------------------------------------------
// /includes/authorizations.inc.php
// -------------------------------------------------------------------------

// checkAuthorization()
$net2ftp_messages["The FTP server <b>%1\$s</b> is not in the list of allowed FTP servers."] = "Der FTP Server <b>%1\$s</b> ist nicht in der Liste der erlaubten FTP Server.";
$net2ftp_messages["The FTP server <b>%1\$s</b> is in the list of banned FTP servers."] = "Der FTP Server <b>%1\$s</b> ist in der Liste der verbotenen FTP Server.";
$net2ftp_messages["The FTP server port %1\$s may not be used."] = "Der FTP Server Port %1\$s darf nicht genutzt werden.";
$net2ftp_messages["Your IP address (%1\$s) is not in the list of allowed IP addresses."] = "Ihre IP-Adresse (%1\$s) is nicht in der Liste der erlaubten IP-Adressen";
$net2ftp_messages["Your IP address (%1\$s) is in the list of banned IP addresses."] = "Ihre IP-Adresse (%1\$s) ist in der Liste der verbotenen IP Addressen.";

// isAuthorizedDirectory()
$net2ftp_messages["Table net2ftp_users contains duplicate rows."] = "Die Tabelle net2ftp_users enth&auml;lt doppelte Zeilen.";

// checkAdminUsernamePassword()
$net2ftp_messages["You did not enter your Administrator username or password."] = "Sie haben ihren Administrator-Benutzername oder das Passwort nicht eingegeben";
$net2ftp_messages["Wrong username or password. Please try again."] = "Falscher Benutzername oder Passwort. Bitte versuchen Sie es erneut.";

// -------------------------------------------------------------------------
// /includes/consumption.inc.php
// -------------------------------------------------------------------------
$net2ftp_messages["Unable to determine your IP address."] = "Kann Ihre IP-Adresse nicht aufl&ouml;sen.";
$net2ftp_messages["Table net2ftp_log_consumption_ipaddress contains duplicate rows."] = "Tabelle net2ftp_log_consumption_ipaddress enth&auml;lt doppelte Eintr&auml;ge.";
$net2ftp_messages["Table net2ftp_log_consumption_ftpserver contains duplicate rows."] = "Tabelle net2ftp_log_consumption_ftpserver enth&auml;lt doppelte Eintr&auml;ge.";
$net2ftp_messages["The variable <b>consumption_ipaddress_datatransfer</b> is not numeric."] = "Die Variable <b>consumption_ipaddress_datatransfer</b> ist nicht numerisch.";
$net2ftp_messages["Table net2ftp_log_consumption_ipaddress could not be updated."] = "Tabelle net2ftp_log_consumption_ipaddress konnte nicht aktualisiert werden.";
$net2ftp_messages["Table net2ftp_log_consumption_ipaddress contains duplicate entries."] = "Tabelle net2ftp_log_consumption_ipaddress enth&auml;lt doppelte Eintr&auml;ge.";
$net2ftp_messages["Table net2ftp_log_consumption_ftpserver could not be updated."] = "Tabelle net2ftp_log_consumption_ftpserver konnte nicht aktualisiert werden.";
$net2ftp_messages["Table net2ftp_log_consumption_ftpserver contains duplicate entries."] = "Tabelle net2ftp_log_consumption_ftpserver enth&auml;lt doppelte Eintr&auml;ge.";
$net2ftp_messages["Table net2ftp_log_access could not be updated."] = "Tabelle net2ftp_log_access konnte nicht aktualisiert werden.";
$net2ftp_messages["Table net2ftp_log_access contains duplicate entries."] = "Tabelle net2ftp_log_access enth&auml;lt doppelte Eintr&auml;ge.";


// -------------------------------------------------------------------------
// /includes/database.inc.php
// -------------------------------------------------------------------------
$net2ftp_messages["Unable to connect to the MySQL database. Please check your MySQL database settings in net2ftp's configuration file settings.inc.php."] = "Die Verbindung zur MySQL Datenbank konnte nicht hergestellt werden. Bitte pr&uuml;fen Sie die MySQL Datenbankeinstellungen in net2ftp's Konfigurationsdatei settings.inc.php.";
$net2ftp_messages["Unable to select the MySQL database. Please check your MySQL database settings in net2ftp's configuration file settings.inc.php."] = "Die MySQL Datenbank konnte nicht ausgew&auml;hlt werden. Bitte pr&uuml;fen Sie die MySQL Datenbankeinstellungen in net2ftp's Konfigurationsdatei settings.inc.php.";


// -------------------------------------------------------------------------
// /includes/errorhandling.inc.php
// -------------------------------------------------------------------------
$net2ftp_messages["An error has occured"] = "Ein Fehler ist aufgetreten";
$net2ftp_messages["Go back"] = "Zur&uuml;ck";
$net2ftp_messages["Go to the login page"] = "Zur&uuml;ck zur Anmeldeseite";


// -------------------------------------------------------------------------
// /includes/filesystem.inc.php
// -------------------------------------------------------------------------

// ftp_openconnection()
$net2ftp_messages["The <a href=\"http://www.php.net/manual/en/ref.ftp.php\" target=\"_blank\">FTP module of PHP</a> is not installed.<br /><br /> The administrator of this website should install this module. Installation instructions are given on <a href=\"http://www.php.net/manual/en/ftp.installation.php\" target=\"_blank\">php.net</a><br />"] = "The <a href=\"http://www.php.net/manual/en/ref.ftp.php\" target=\"_blank\">FTP module of PHP</a> is not installed.<br /><br /> The administrator of this website should install this module. Installation instructions are given on <a href=\"http://www.php.net/manual/en/ftp.installation.php\" target=\"_blank\">php.net</a><br />";
$net2ftp_messages["The <a href=\"http://www.php.net/manual/en/function.ftp-ssl-connect.php\" target=\"_blank\">FTP and/or OpenSSL modules of PHP</a> is not installed.<br /><br /> The administrator of this website should install these modules. Installation instructions are given on php.net: <a href=\"http://www.php.net/manual/en/ftp.installation.php\" target=\"_blank\">here for FTP</a>, and <a href=\"http://www.php.net/manual/en/openssl.installation.php\" target=\"_blank\">here for OpenSSL</a><br />"] = "The <a href=\"http://www.php.net/manual/en/function.ftp-ssl-connect.php\" target=\"_blank\">FTP and/or OpenSSL modules of PHP</a> is not installed.<br /><br /> The administrator of this website should install these modules. Installation instructions are given on php.net: <a href=\"http://www.php.net/manual/en/ftp.installation.php\" target=\"_blank\">here for FTP</a>, and <a href=\"http://www.php.net/manual/en/openssl.installation.php\" target=\"_blank\">here for OpenSSL</a><br />";
$net2ftp_messages["The <a href=\"http://www.php.net/manual/en/function.ssh2-sftp.php\" target=\"_blank\">SSH2 module of PHP</a> is not installed.<br /><br /> The administrator of this website should install this module. Installation instructions are given on <a href=\"http://www.php.net/manual/en/ssh2.installation.php\" target=\"_blank\">php.net</a><br />"] = "The <a href=\"http://www.php.net/manual/en/function.ssh2-sftp.php\" target=\"_blank\">SSH2 module of PHP</a> is not installed.<br /><br /> The administrator of this website should install this module. Installation instructions are given on <a href=\"http://www.php.net/manual/en/ssh2.installation.php\" target=\"_blank\">php.net</a><br />";
$net2ftp_messages["Unable to connect to FTP server <b>%1\$s</b> on port <b>%2\$s</b>.<br /><br />Are you sure this is the address of the FTP server? This is often different from that of the HTTP (web) server. Please contact your ISP helpdesk or system administrator for help.<br />"] = "Konnte keine Verbindung zum FTP Server <b>%1\$s</b> auf Port <b>%2\$s</b> herstellen.<br /><br />Bitte pr&uuml;fen Sie die Adresse des FTP-Servers - diese unterscheidet sich oft von der Adresse des HTTP (Web) Servers. Bitte kontaktieren Sie die Hotline Ihres Providers oder Ihren Systemadministrator.<br />";
$net2ftp_messages["Unable to login to FTP server <b>%1\$s</b> with username <b>%2\$s</b>.<br /><br />Are you sure your username and password are correct? Please contact your ISP helpdesk or system administrator for help.<br />"] = "Anmeldung am FTP Server  <b>%1\$s</b> mit Benutzername <b>%2\$s</b> fehlgeschlagen.<br /><br />Bitte pr&uuml;fen Sie Ihren Benutzernamen und das Kennwort. Kontaktieren Sie die Hotline Ihres Providers oder Fragen Sie Ihren Systemadministrator.<br />";
$net2ftp_messages["Unable to switch to the passive mode on FTP server <b>%1\$s</b>."] = "Konnte nicht in den passiven Modus auf dem FTP-Server <b>%1\$s</b> wechseln.";

// ftp_openconnection2()
$net2ftp_messages["Unable to connect to the second (target) FTP server <b>%1\$s</b> on port <b>%2\$s</b>.<br /><br />Are you sure this is the address of the second (target) FTP server? This is often different from that of the HTTP (web) server. Please contact your ISP helpdesk or system administrator for help.<br />"] = "Konnte nicht zum zweiten (Ziel-) FTP-Server <b>%1\$s</b> auf Port <b>%2\$s</b> verbinden.<br /><br />Bitte pr&uuml;fen Sie die Adresse des FTP-Servers - diese unterscheidet sich oft von der Adresse des HTTP (Web) Servers. Bitte kontaktieren Sie die Hotline Ihres Providers oder Ihren Systemadministrator.<br />";
$net2ftp_messages["Unable to login to the second (target) FTP server <b>%1\$s</b> with username <b>%2\$s</b>.<br /><br />Are you sure your username and password are correct? Please contact your ISP helpdesk or system administrator for help.<br />"] = "Anmeldung am zweiten (Ziel-) FTP Server <b>%1\$s</b> mit Benutzername <b>%2\$s</b> fehlgeschlagen.<br /><br />Bitte pr&uuml;fen Sie Ihren Benutzernamen und das Kennwort. Kontaktieren Sie die Hotline Ihres Providers oder Fragen Sie Ihren Systemadministrator.<br />";
$net2ftp_messages["Unable to switch to the passive mode on the second (target) FTP server <b>%1\$s</b>."] = "Konnte nicht in den passiven Modus auf dem zweiten (Ziel-) FTP-Server <b>%1\$s</b> wechseln.";

// ftp_myrename()
$net2ftp_messages["Unable to rename directory or file <b>%1\$s</b> into <b>%2\$s</b>"] = "Umbenennen der Datei oder des Verzeichnisses <b>%1\$s</b> in <b>%2\$s</b> fehlgeschlagen";

// ftp_mychmod()
$net2ftp_messages["Unable to execute site command <b>%1\$s</b>. Note that the CHMOD command is only available on Unix FTP servers, not on Windows FTP servers."] = "Ausf&uuml;hrung des SITE-Kommandos <b>%1\$s</b> fehlgeschlagen. Hinweis: Das CHMOD Kommando ist nur auf Unix-FTP-Servern verf&uuml;gbar, nicht auf Windows-FTP-Servern.";
$net2ftp_messages["Directory <b>%1\$s</b> successfully chmodded to <b>%2\$s</b>"] = "Zugriffsrechte des Verzeichnisses <b>%1\$s</b> erfolgreich in <b>%2\$s</b> ge&auml;ndert";
$net2ftp_messages["Processing entries within directory <b>%1\$s</b>:"] = "Verabeiten der Eintr&auml;ge im Verzeichnis <b>%1\$s</b>:";
$net2ftp_messages["File <b>%1\$s</b> was successfully chmodded to <b>%2\$s</b>"] = "Zugriffsrechte der Datei <b>%1\$s</b> erfolgreich in <b>%2\$s</b> ge&auml;ndert";
$net2ftp_messages["All the selected directories and files have been processed."] = "Alle ausgew&auml;hlten Verzeichnisse und Dateien wurden verarbeitet.";

// ftp_rmdir2()
$net2ftp_messages["Unable to delete the directory <b>%1\$s</b>"] = "L&ouml;schen des Verzeichnisses <b>%1\$s</b> fehlgeschlagen";

// ftp_delete2()
$net2ftp_messages["Unable to delete the file <b>%1\$s</b>"] = "L&ouml;schen der Datei <b>%1\$s</b> fehlgeschlagen";

// ftp_newdirectory()
$net2ftp_messages["Unable to create the directory <b>%1\$s</b>"] = "Der neue Ordner <b>%1\$s</b> kann nicht angelegt werden";

// ftp_readfile()
$net2ftp_messages["Unable to create the temporary file"] = "Die tempor&auml;re Datei kann nicht erstellt werden";
$net2ftp_messages["Unable to get the file <b>%1\$s</b> from the FTP server and to save it as temporary file <b>%2\$s</b>.<br />Check the permissions of the %3\$s directory.<br />"] = "Laden der Datei <b>%1\$s</b> vom FTP Server und Zwischenspeichern als <b>%2\$s</b> fehlgeschlagen.<br />Bitte pr&uuml;fen Sie die Zugriffsrechte des Ordners %3\$s.<br />";
$net2ftp_messages["Unable to open the temporary file. Check the permissions of the %1\$s directory."] = "&Ouml;ffnen der zwischengespeicherten Datei fehlgeschlagen. Bitte pr&uuml;fen Sie die Zugriffsrechte des Ordners %1\$s.";
$net2ftp_messages["Unable to read the temporary file"] = "Lesen der tempor&auml;ren Datei fehlgeschlagen.";
$net2ftp_messages["Unable to close the handle of the temporary file"] = "Die Verarbeitung der tempor&auml;ren Datei konnte nicht beendet werden";
$net2ftp_messages["Unable to delete the temporary file"] = "Die tempor&auml;re Datei kann nicht gel&ouml;scht werden";

// ftp_writefile()
$net2ftp_messages["Unable to create the temporary file. Check the permissions of the %1\$s directory."] = "Die tempor&auml;re Datei kann nicht erstellt werden. Bitte Berechtigung des Verzeichnisses %1\$s &uuml;berpr&uuml;fen.";
$net2ftp_messages["Unable to open the temporary file. Check the permissions of the %1\$s directory."] = "&Ouml;ffnen der zwischengespeicherten Datei fehlgeschlagen. Bitte pr&uuml;fen Sie die Zugriffsrechte des Ordners %1\$s.";
$net2ftp_messages["Unable to write the string to the temporary file <b>%1\$s</b>.<br />Check the permissions of the %2\$s directory."] = "Speichern der Zeichenkette in die tempor&auml;re Datei <b>%1\$s</b> fehlgeschlagen.<br />Bitte pr&uuml;fen Sie die Zugriffsrechte des Ordners %2\$s.";
$net2ftp_messages["Unable to close the handle of the temporary file"] = "Die Verarbeitung der tempor&auml;ren Datei konnte nicht beendet werden";
$net2ftp_messages["Unable to put the file <b>%1\$s</b> on the FTP server.<br />You may not have write permissions on the directory."] = "Konnte Datei <b>%1\$s</b> nicht auf dem FTP Server ablegen.<br />Bitte pr&uuml;fen Sie Ihre Schreibrechte in diesem Verzeichnis.";
$net2ftp_messages["Unable to delete the temporary file"] = "Die tempor&auml;re Datei kann nicht gel&ouml;scht werden";

// ftp_copymovedelete()
$net2ftp_messages["Processing directory <b>%1\$s</b>"] = "Verarbeiten des Ordners <b>%1\$s</b>";
$net2ftp_messages["The target directory <b>%1\$s</b> is the same as or a subdirectory of the source directory <b>%2\$s</b>, so this directory will be skipped"] = "Das Ziel-Verzeichnis <b>%1\$s</b> ist das Gleiche als der Quellordner <b>%2\$s</b>, oder ein Unterordner davon, Ordner wird &uuml;bersprungen";
$net2ftp_messages["The directory <b>%1\$s</b> contains a banned keyword, so this directory will be skipped"] = "Das Verzeichnis <b>%1\$s</b> enth&auml;lt ein verbotenes Schl&uuml;sselwort, dieses Verzeichnis wird &uuml;bersprungen";
$net2ftp_messages["The directory <b>%1\$s</b> contains a banned keyword, aborting the move"] = "Das Verzeichnis <b>%1\$s</b> enth&auml;lt ein verbotenes Schl&uuml;sselwort, Abbruch des Verschiebens";
$net2ftp_messages["Unable to create the subdirectory <b>%1\$s</b>. It may already exist. Continuing the copy/move process..."] = "Unm&ouml;glich das Unterverzeichnis <b>%1\$s</b> zu erstellen. Evtl. besteht es bereits. Setze das Kopieren/Verschieben fort...";
$net2ftp_messages["Created target subdirectory <b>%1\$s</b>"] = "Zielunterverzeichnis <b>%1\$s</b> erstellt";
$net2ftp_messages["The directory <b>%1\$s</b> could not be selected, so this directory will be skipped"] = "Das Verzeichnis <b>%1\$s</b> kann nicht ausgew&auml;lt werden, dieses Verzeichnis wird &uuml;bersprungen";
$net2ftp_messages["Unable to delete the subdirectory <b>%1\$s</b> - it may not be empty"] = "Das Unterverzeichniss <b>%1\$s</b> konnte nicht gel&ouml;scht werden - es ist nicht leer";
$net2ftp_messages["Deleted subdirectory <b>%1\$s</b>"] = "Verzeichniss <b>%1\$s</b> gel&ouml;scht";
$net2ftp_messages["Deleted subdirectory <b>%1\$s</b>"] = "Verzeichniss <b>%1\$s</b> gel&ouml;scht";
$net2ftp_messages["Unable to move the directory <b>%1\$s</b>"] = "Das Verzeichnis <b>%1\$s</b> konnte nicht verschoben werden.";
$net2ftp_messages["Moved directory <b>%1\$s</b>"] = "Verzeichnis <b>%1\$s</b> wurde verschoben";
$net2ftp_messages["Processing of directory <b>%1\$s</b> completed"] = "Verarbeitung des Verzeichnisses <b>%1\$s</b> beendet";
$net2ftp_messages["The target for file <b>%1\$s</b> is the same as the source, so this file will be skipped"] = "Das Ziel f&uuml;r die Datei <b>%1\$s</b> ist die Selbe wie die Quelle, diese Datei wird &uuml;bersprungen";
$net2ftp_messages["The file <b>%1\$s</b> contains a banned keyword, so this file will be skipped"] = "Die Datei <b>%1\$s</b> enth&auml;lt ein verbotenes Schl&uuml;sselwort, diese Datei wird &uuml;bersprungen";
$net2ftp_messages["The file <b>%1\$s</b> contains a banned keyword, aborting the move"] = "Die Datei <b>%1\$s</b> enth&auml;lt ein verbotenes Schl&uuml;sselwort, Abbruch des Verschiebens";
$net2ftp_messages["The file <b>%1\$s</b> is too big to be copied, so this file will be skipped"] = "Die Datei <b>%1\$s</b> ist zum Kopieren zu gro&szlig;, diese Datei wird &uuml;bersprungen";
$net2ftp_messages["The file <b>%1\$s</b> is too big to be moved, aborting the move"] = "Die Datei <b>%1\$s</b> ist zum Verschieben zu gro&szlig;, Abbruch des Verschiebens";
$net2ftp_messages["Unable to copy the file <b>%1\$s</b>"] = "Die Datei <b>%1\$s</b> kann nicht kopiert werden";
$net2ftp_messages["Copied file <b>%1\$s</b>"] = "Datei <b>%1\$s</b> kopiert";
$net2ftp_messages["Unable to move the file <b>%1\$s</b>, aborting the move"] = "Verschieben der Datei <b>%1\$s</b> unm&ouml;glich, verschieben wird abgebrochen";
$net2ftp_messages["Unable to move the file <b>%1\$s</b>"] = "Unable to move the file <b>%1\$s</b>";
$net2ftp_messages["Moved file <b>%1\$s</b>"] = "Datei <b>%1\$s</b> verschoben";
$net2ftp_messages["Unable to delete the file <b>%1\$s</b>"] = "L&ouml;schen der Datei <b>%1\$s</b> fehlgeschlagen";
$net2ftp_messages["Deleted file <b>%1\$s</b>"] = "Gel&ouml;schte Datei <b>%1\$s</b>";
$net2ftp_messages["All the selected directories and files have been processed."] = "Alle ausgew&auml;hlten Verzeichnisse und Dateien wurden verarbeitet.";

// ftp_processfiles()

// ftp_getfile()
$net2ftp_messages["Unable to copy the remote file <b>%1\$s</b> to the local file using FTP mode <b>%2\$s</b>"] = "Die entfernte Datei <b>%1\$s</b> konnte nicht lokal per FTP Modus <b>%2\$s</b> kopiert werden";
$net2ftp_messages["Unable to delete file <b>%1\$s</b>"] = "Die Datei <b>%1\$s</b> kann nicht gel&ouml;scht werden";

// ftp_putfile()
$net2ftp_messages["The file is too big to be transferred"] = "Die Datei ist zu gro&szlig;, um &uuml;bertragen zu werden";
$net2ftp_messages["Daily limit reached: the file <b>%1\$s</b> will not be transferred"] = "Tages-Beschr&auml;nkung erreicht: die Datei <b>%1\$s</b> wird nicht transferiert";
$net2ftp_messages["Unable to copy the local file to the remote file <b>%1\$s</b> using FTP mode <b>%2\$s</b>"] = "Unm&ouml;glich die lokale Datei zur entfernten Datei <b>%1\$s</b> unter Verwendung des FTP-Modus <b>%2\$s</b> zu kopieren";
$net2ftp_messages["Unable to delete the local file"] = "Lokale Datei kann nicht gel&ouml;scht werden";

// ftp_downloadfile()
$net2ftp_messages["Unable to delete the temporary file"] = "Die tempor&auml;re Datei kann nicht gel&ouml;scht werden";
$net2ftp_messages["Unable to send the file to the browser"] = "Die Datei konnte nicht an den Browser gesendet werden";

// ftp_zip()
$net2ftp_messages["Unable to create the temporary file"] = "Die tempor&auml;re Datei kann nicht erstellt werden";
$net2ftp_messages["The zip file has been saved on the FTP server as <b>%1\$s</b>"] = "Das ZIP-Archiv wurde auf dem FTP-Server als <b>%1\$s</b> gespeichert";
$net2ftp_messages["Requested files"] = "Angeforderte Dateien";

$net2ftp_messages["Dear,"] = "Sehr geehrte(r),";
$net2ftp_messages["Someone has requested the files in attachment to be sent to this email account (%1\$s)."] = "Jemand hat veranlasst, dass diese Datei an Ihre E-Mail Adresse (%1\$s) gesendet wird.";
$net2ftp_messages["If you know nothing about this or if you don't trust that person, please delete this email without opening the Zip file in attachment."] = "Wenn Sie nichts davon wissen oder der Person nicht trauen, l&ouml;schen Sie bitte diese E-Mail und den Anhang, ohne Sie zu &ouml;ffnen.";
$net2ftp_messages["Note that if you don't open the Zip file, the files inside cannot harm your computer."] = "Beachten Sie bitte, dass die Dateien im Anhang Ihrem Computer nicht schaden k&ouml;nnen, wenn Sie die Datei nicht &ouml;ffnen.";
$net2ftp_messages["Information about the sender: "] = "Informationen &uuml;ber den Absender: ";
$net2ftp_messages["IP address: "] = "IP Addresse: ";
$net2ftp_messages["Time of sending: "] = "Gesendet: ";
$net2ftp_messages["Sent via the net2ftp application installed on this website: "] = "Versendet durch den net2ftp Dienst der Webseite: ";
$net2ftp_messages["Webmaster's email: "] = "E-Mail Adresse des Webmaster: ";
$net2ftp_messages["Message of the sender: "] = "Nachricht des Absenders: ";
$net2ftp_messages["net2ftp is free software, released under the GNU/GPL license. For more information, go to http://www.net2ftp.com."] = "net2ftp ist freie Software, freigegeben unter der GNU/GPL Lizenz. F&uuml;r weitere Informationen, gehen Sie bitte auf http://www.net2ftp.com.";

$net2ftp_messages["The zip file has been sent to <b>%1\$s</b>."] = "Die Zip Datei wurde versand an <b>%1\$s</b>.";

// acceptFiles()
$net2ftp_messages["File <b>%1\$s</b> is too big. This file will not be uploaded."] = "Datei <b>%1\$s</b> ist zu gro&szlig;. Diese Datei wird nicht hochgeladen.";
$net2ftp_messages["File <b>%1\$s</b> is contains a banned keyword. This file will not be uploaded."] = "Die Datei <b>%1\$s</b> enth&auml;lt ein verbotenes Schl&uuml;sselwort. Die Datei wird nicht hochgeladen.";
$net2ftp_messages["Could not generate a temporary file."] = "Tempor&auml;re Datei kann nicht erstellt werden.";
$net2ftp_messages["File <b>%1\$s</b> could not be moved"] = "Datei <b>%1\$s</b> konnte nicht verschoben werden";
$net2ftp_messages["File <b>%1\$s</b> is OK"] = "Datei <b>%1\$s</b> ist OK";
$net2ftp_messages["Unable to move the uploaded file to the temp directory.<br /><br />The administrator of this website has to <b>chmod 777</b> the /temp directory of net2ftp."] = "Es war nicht m&ouml;glich, die hochgeladene Datei ins tempor&auml;re Verzeichnis zu verschieben.<br /><br />Der Administrator dieser Seite muss die Zugriffsrechte des net2ftp - /tmp-Verzeichnisses auf <b>0777 (chmod)</b> setzen.";
$net2ftp_messages["You did not provide any file to upload."] = "Sie haben keine Datei zum Upload ausgew&auml;hlt.";

// ftp_transferfiles()
$net2ftp_messages["File <b>%1\$s</b> could not be transferred to the FTP server"] = "Datei <b>%1\$s</b> konnte nicht auf den FTP-Server geladen werden";
$net2ftp_messages["File <b>%1\$s</b> has been transferred to the FTP server using FTP mode <b>%2\$s</b>"] = "Datei <b>%1\$s</b> wurde erfolgreich auf den FTP-Server im Modus <b>%2\$s</b> &uuml;bertragen";
$net2ftp_messages["Transferring files to the FTP server"] = "Dateien werden zum FTP-Server geschickt";

// ftp_unziptransferfiles()
$net2ftp_messages["Processing archive nr %1\$s: <b>%2\$s</b>"] = "Verarbeitung von Archiv Nr. %1\$s: <b>%2\$s</b>";
$net2ftp_messages["Archive <b>%1\$s</b> was not processed because its filename extension was not recognized. Only zip, tar, tgz and gz archives are supported at the moment."] = "Archiv <b>%1\$s</b> wurde nicht verarbeitet, das Format ist unbekannt. Zur Zeit unterst&uuml;tzte Archiv-Formate: zip, tar, tgz (tar-gzip), gz (gzip).";
$net2ftp_messages["Unable to extract the files and directories from the archive"] = "Die Dateien und die Verzeichnisse vom Archiv zu extrahieren war nicht m&ouml;glich";
$net2ftp_messages["Archive contains filenames with ../ or ..\\ - aborting the extraction"] = "Das Archiv enth&auml;lt Dateinamen mit ../ oder ..\\ - auspacken abgebrochen";
$net2ftp_messages["Could not unzip entry %1\$s (error code %2\$s)"] = "Der Eintrag %1\$s konnte nicht entpackt werden (Fehlercode: %2\$s)";
$net2ftp_messages["Created directory %1\$s"] = "Verzeichnis %1\$s wurde erstellt";
$net2ftp_messages["Could not create directory %1\$s"] = "Konnte Verzeichnis %1\$s nicht erstellen";
$net2ftp_messages["Copied file %1\$s"] = "Datei %1\$s wurde kopiert";
$net2ftp_messages["Could not copy file %1\$s"] = "Konnte Datei %1\$s nicht kopieren";
$net2ftp_messages["Unable to delete the temporary directory"] = "Das tempor&aauml;re Verzeichnis konnte nicht gel&ouml;scht werden";
$net2ftp_messages["Unable to delete the temporary file %1\$s"] = "Die tempor&auml;re Datei %1\$s konnte nicht gel&ouml;scht werden";

// ftp_mysite()
$net2ftp_messages["Unable to execute site command <b>%1\$s</b>"] = "SITE-Kommando <b>%1\$s</b> fehlgeschlagen";

// shutdown()
$net2ftp_messages["Your task was stopped"] = "Ihr Auftrag wurde angehalten";
$net2ftp_messages["The task you wanted to perform with net2ftp took more time than the allowed %1\$s seconds, and therefor that task was stopped."] = "Ihr Arbeitsauftrag den Sie mit net2ftp ausf&uuml;hren wollten, hat mehr Zeit als die erlaubten  %1\$s Sekunden in Anspruch genommen, und wurde deswegen abgebrochen.";
$net2ftp_messages["This time limit guarantees the fair use of the web server for everyone."] = "Diese Zeitbeschr&auml;nkung gew&auml;hrleistet den Betrieb des Webservers f&uuml;r andere Nutzer.";
$net2ftp_messages["Try to split your task in smaller tasks: restrict your selection of files, and omit the biggest files."] = "Versuchen Sie, Ihren Auftrag in kleinere Schritte aufzutrennen: schr&auml;nken Sie die Auswahl an Dateien ein, und/oder &uuml;berspringen sie die gr&ouml;&szlig;ten Dateien.";
$net2ftp_messages["If you really need net2ftp to be able to handle big tasks which take a long time, consider installing net2ftp on your own server."] = "Sollten Sie net2ftp ben&ouml;tigen, um gr&ouml;&szlig;ere Arbeitsauftr&auml;ge auszuf&uuml;hren, k&ouml;nnen Sie net2ftp auf Ihrem eigenen Webserver installieren.";

// SendMail()
$net2ftp_messages["You did not provide any text to send by email!"] = "Sie haben keinen Text f&uuml;r den EMail-Versand angegeben!";
$net2ftp_messages["You did not supply a From address."] = "Sie haben keine Absenderadresse eingegeben.";
$net2ftp_messages["You did not supply a To address."] = "Sie haben keine Empf&auml;ngeradresse eingegeben.";
$net2ftp_messages["Due to technical problems the email to <b>%1\$s</b> could not be sent."] = "Aus technischen Gr&uuml;nden konnte die EMail an <b>%1\$s</b> nicht versendet werden.";

// tempdir2()
$net2ftp_messages["Unable to create a temporary directory because (unvalid parent directory)"] = "Es konnte kein tempor&auml;res Verzeichnis angelegt werden (ung&uuml;ltiges Verzeichnis)";
$net2ftp_messages["Unable to create a temporary directory because (parent directory is not writeable)"] = "Es konnte kein tempor&auml;res Verzeichnis angelegt werden (Verzeichnis ist nicht beschreibbar)";
$net2ftp_messages["Unable to create a temporary directory (too many tries)"] = "Es konnte kein tempor&auml;res Verzeichnis angelegt werden (zu viele Versuche)";

// -------------------------------------------------------------------------
// /includes/logging.inc.php
// -------------------------------------------------------------------------
// logAccess(), logLogin(), logError()
$net2ftp_messages["Unable to execute the SQL query."] = "Kann die SQL-Abfrage nicht ausf&uuml;hren.";
$net2ftp_messages["Unable to open the system log."] = "Kann system log nicht &ouml;ffnen.";
$net2ftp_messages["Unable to write a message to the system log."] = "Unm&ouml;glich eine Nachricht ins system.log zu schreiben.";

// getLogStatus(), putLogStatus()
$net2ftp_messages["Table net2ftp_log_status contains duplicate entries."] = "Die Tabelle net2ftp_log_status enth&auml;lt doppelte Eintr&auml;ge.";
$net2ftp_messages["Table net2ftp_log_status could not be updated."] = "Die Tabelle net2ftp_log_status konnte nicht aktualisiert werden.";

// rotateLogs()
$net2ftp_messages["The log tables were renamed successfully."] = "Die Log Tabellen wurden erfolgreich umbenannt.";
$net2ftp_messages["The log tables could not be renamed."] = "Die Log Tabellen konnten nicht umbenannt werden.";
$net2ftp_messages["The log tables were copied successfully."] = "Die Log Tabellen wurden erfolgreich kopiert.";
$net2ftp_messages["The log tables could not be copied."] = "Die Log Tabellen konnten nicht kopiert werden.";
$net2ftp_messages["The oldest log table was dropped successfully."] = "Die &auml;lteste Log Tabelle wurde erfolgreich gel&ouml;scht.";
$net2ftp_messages["The oldest log table could not be dropped."] = "Die &auml;lteste Log Tabelle konnte nicht gel&ouml;scht werden.";


// -------------------------------------------------------------------------
// /includes/registerglobals.inc.php
// -------------------------------------------------------------------------
$net2ftp_messages["Please enter your username and password for FTP server "] = "Bitte geben Sie Ihren Benutzernamen und das Kennwort ein, f&uuml;r den FTP Server ";
$net2ftp_messages["You did not fill in your login information in the popup window.<br />Click on \"Go to the login page\" below."] = "Sie haben keine Zugangsdaten im Popup-Fenster ausgef&uuml:llt.<br />Klicken Sie unten auf \"Go to the login page\".";
$net2ftp_messages["Access to the net2ftp Admin panel is disabled, because no password has been set in the file settings.inc.php. Enter a password in that file, and reload this page."] = "Der Zugang zum net2ftp Administrationsbereich wurde deaktiviert, da kein Kennwort in der Datei settings.inc.php eingetragen wurde. Tragen Sie dort ein Kennwort ein, und laden diese Seite neu.";
$net2ftp_messages["Please enter your Admin username and password"] = "Bitte geben Sie Ihren Administrations-Benutzernamen und das entsprechende Kennwort ein"; 
$net2ftp_messages["You did not fill in your login information in the popup window.<br />Click on \"Go to the login page\" below."] = "Sie haben keine Zugangsdaten im Popup-Fenster ausgef&uuml:llt.<br />Klicken Sie unten auf \"Go to the login page\".";
$net2ftp_messages["Wrong username or password for the net2ftp Admin panel. The username and password can be set in the file settings.inc.php."] = "Falscher Benutzername oder falsches Kennwort f&uuml;r net2ftp Administrationsbereich. Bitte pr&uuml;fen Sie Ihre Eingabe bzw. die Einstellungen in der Datei settings.inc.php.";


// -------------------------------------------------------------------------
// /skins/skins.inc.php
// -------------------------------------------------------------------------
$net2ftp_messages["Blue"] = "Blau";
$net2ftp_messages["Grey"] = "Grau";
$net2ftp_messages["Black"] = "Schwarz";
$net2ftp_messages["Yellow"] = "Gelb";
$net2ftp_messages["Pastel"] = "Pastel";

// getMime()
$net2ftp_messages["Directory"] = "Verzeichnis";
$net2ftp_messages["Symlink"] = "Symlink";
$net2ftp_messages["ASP script"] = "ASP Skript";
$net2ftp_messages["Cascading Style Sheet"] = "Cascading Style Sheet";
$net2ftp_messages["HTML file"] = "HTML Datei";
$net2ftp_messages["Java source file"] = "Java source Datei";
$net2ftp_messages["JavaScript file"] = "JavaScript Datei";
$net2ftp_messages["PHP Source"] = "PHP Source";
$net2ftp_messages["PHP script"] = "PHP Skript";
$net2ftp_messages["Text file"] = "Text Datei";
$net2ftp_messages["Bitmap file"] = "Bitmap Datei";
$net2ftp_messages["GIF file"] = "GIF Datei";
$net2ftp_messages["JPEG file"] = "JPEG Datei";
$net2ftp_messages["PNG file"] = "PNG Datei";
$net2ftp_messages["TIF file"] = "TIF Datei";
$net2ftp_messages["GIMP file"] = "GIMP Datei";
$net2ftp_messages["Executable"] = "Ausf&uuml;hrbare Datei";
$net2ftp_messages["Shell script"] = "Shell script";
$net2ftp_messages["MS Office - Word document"] = "MS Office - Word Dokument";
$net2ftp_messages["MS Office - Excel spreadsheet"] = "MS Office - Excel Tabellendokument";
$net2ftp_messages["MS Office - PowerPoint presentation"] = "MS Office - PowerPoint Pr&auml;sentation";
$net2ftp_messages["MS Office - Access database"] = "MS Office - Access Datenbank";
$net2ftp_messages["MS Office - Visio drawing"] = "MS Office - Visio Zeichnung";
$net2ftp_messages["MS Office - Project file"] = "MS Office - Project Datei";
$net2ftp_messages["OpenOffice - Writer 6.0 document"] = "OpenOffice - Writer 6.0 Dokument";
$net2ftp_messages["OpenOffice - Writer 6.0 template"] = "OpenOffice - Writer 6.0 Vorlage";
$net2ftp_messages["OpenOffice - Calc 6.0 spreadsheet"] = "OpenOffice - Calc 6.0 Tabellemndokument";
$net2ftp_messages["OpenOffice - Calc 6.0 template"] = "OpenOffice - Calc 6.0 Vorlage";
$net2ftp_messages["OpenOffice - Draw 6.0 document"] = "OpenOffice - Draw 6.0 Dokument";
$net2ftp_messages["OpenOffice - Draw 6.0 template"] = "OpenOffice - Draw 6.0 Vorlage";
$net2ftp_messages["OpenOffice - Impress 6.0 presentation"] = "OpenOffice - Impress 6.0 Pr&auml;sentation";
$net2ftp_messages["OpenOffice - Impress 6.0 template"] = "OpenOffice - Impress 6.0 Vorlage";
$net2ftp_messages["OpenOffice - Writer 6.0 global document"] = "OpenOffice - Writer 6.0 Globaldokument";
$net2ftp_messages["OpenOffice - Math 6.0 document"] = "OpenOffice - Math 6.0 Dokument";
$net2ftp_messages["StarOffice - StarWriter 5.x document"] = "StarOffice - StarWriter 5.x Dokument";
$net2ftp_messages["StarOffice - StarWriter 5.x global document"] = "StarOffice - StarWriter 5.x global Dokument";
$net2ftp_messages["StarOffice - StarCalc 5.x spreadsheet"] = "StarOffice - StarCalc 5.x Tabellendokument";
$net2ftp_messages["StarOffice - StarDraw 5.x document"] = "StarOffice - StarDraw 5.x Dokument";
$net2ftp_messages["StarOffice - StarImpress 5.x presentation"] = "StarOffice - StarImpress 5.x Pr&auml;sentation";
$net2ftp_messages["StarOffice - StarImpress Packed 5.x file"] = "StarOffice - StarImpress gepackte 5.x Datei";
$net2ftp_messages["StarOffice - StarMath 5.x document"] = "StarOffice - StarMath 5.x Dokument";
$net2ftp_messages["StarOffice - StarChart 5.x document"] = "StarOffice - StarChart 5.x Dokument";
$net2ftp_messages["StarOffice - StarMail 5.x mail file"] = "StarOffice - StarMail 5.x Maildatei";
$net2ftp_messages["Adobe Acrobat document"] = "Adobe Acrobat Dokument";
$net2ftp_messages["ARC archive"] = "ARC Archiv";
$net2ftp_messages["ARJ archive"] = "ARJ Archiv";
$net2ftp_messages["RPM"] = "RPM";
$net2ftp_messages["GZ archive"] = "GZ Archiv";
$net2ftp_messages["TAR archive"] = "TAR Archiv";
$net2ftp_messages["Zip archive"] = "Zip Archiv";
$net2ftp_messages["MOV movie file"] = "MOV Videodatei";
$net2ftp_messages["MPEG movie file"] = "MPEG Videodatei";
$net2ftp_messages["Real movie file"] = "Real Videodatei";
$net2ftp_messages["Quicktime movie file"] = "Quicktime Datei";
$net2ftp_messages["Shockwave flash file"] = "Shockwave Flash Datei";
$net2ftp_messages["Shockwave file"] = "Shockwave Datei";
$net2ftp_messages["WAV sound file"] = "WAV Audiodatei";
$net2ftp_messages["Font file"] = "Font Datei";
$net2ftp_messages["%1\$s File"] = "%1\$s Datei";
$net2ftp_messages["File"] = "Datei";

// getAction()
$net2ftp_messages["Back"] = "Zur&uuml;ck";
$net2ftp_messages["Submit"] = "Senden";
$net2ftp_messages["Refresh"] = "Aktualisieren";
$net2ftp_messages["Details"] = "Details";
$net2ftp_messages["Icons"] = "Icons";
$net2ftp_messages["List"] = "List";
$net2ftp_messages["Logout"] = "Abmelden";
$net2ftp_messages["Help"] = "Hilfe";
$net2ftp_messages["Bookmark"] = "Lesezeichen";
$net2ftp_messages["Save"] = "Speichern";
$net2ftp_messages["Default"] = "Standard";


// -------------------------------------------------------------------------
// /skins/[skin]/header.template.php and footer.template.php
// -------------------------------------------------------------------------
$net2ftp_messages["Help Guide"] = "Hilfe/Anleitung";
$net2ftp_messages["Forums"] = "Foren";
$net2ftp_messages["License"] = "Lizenz";
$net2ftp_messages["Powered by"] = "Powered by";
$net2ftp_messages["You are now taken to the net2ftp forums. These forums are for net2ftp related topics only - not for generic webhosting questions."] = "Sie werden nun zu den net2ftp Foren weitergeleitet. Diese Foren sind nur f&uuml;r Themen, die mit net2ftp im Zusammenhang stehen - nicht f&uuml;r allgemeine Fragen zum Webhosting.";
$net2ftp_messages["Standard"] = "Standard";
$net2ftp_messages["Mobile"] = "Mobile";

// -------------------------------------------------------------------------
// Admin module
if ($net2ftp_globals["state"] == "admin") {
// -------------------------------------------------------------------------

// /modules/admin/admin.inc.php
$net2ftp_messages["Admin functions"] = "Administrationsfunktionen";

// /skins/[skin]/admin1.template.php
$net2ftp_messages["Version information"] = "Versionsinformationen";
$net2ftp_messages["This version of net2ftp is up-to-date."] = "Diese Version von net2ftp ist aktuell.";
$net2ftp_messages["The latest version information could not be retrieved from the net2ftp.com server. Check the security settings of your browser, which may prevent the loading of a small file from the net2ftp.com server."] = "Die aktuelle Versionsinformation konnte vom net2ftp.com Server nicht empfangen werden. &Uuml;berpr&uuml;fen Sie die Sicherheitseinstellungen Ihres Browsers, sie verhindern eventuell das Laden einer kleinen Datei vom net2ftp.com Server.";
$net2ftp_messages["Logging"] = "Logging";
$net2ftp_messages["Date from:"] = "Datum ab:";
$net2ftp_messages["to:"] = "bis:";
$net2ftp_messages["Empty logs"] = "L&ouml;schen";
$net2ftp_messages["View logs"] = "Anzeigen";
$net2ftp_messages["Go"] = "Weiter";
$net2ftp_messages["Setup MySQL tables"] = "MySQL-Tabellen einrichten";
$net2ftp_messages["Create the MySQL database tables"] = "Anlegen der MySQL-Datenbanktabellen";

} // end admin

// -------------------------------------------------------------------------
// Admin_createtables module
if ($net2ftp_globals["state"] == "admin_createtables") {
// -------------------------------------------------------------------------

// /modules/admin_createtables/admin_createtables.inc.php
$net2ftp_messages["Admin functions"] = "Administrationsfunktionen";
$net2ftp_messages["The handle of file %1\$s could not be opened."] = "The handle of file %1\$s could not be opened.";
$net2ftp_messages["The file %1\$s could not be opened."] = "Die Datei %1\$s konnte nicht ge&ouml;ffnet werden.";
$net2ftp_messages["The handle of file %1\$s could not be closed."] = "The handle of file %1\$s could not be closed.";
$net2ftp_messages["The connection to the server <b>%1\$s</b> could not be set up. Please check the database settings you've entered."] = "Die Verbindung zum Server <b>%1\$s</b> konnte nicht hergestellt werden. Bitte &uuml;berpr&uuml;fen Sie die Datenbankeinstellungen.";
$net2ftp_messages["Unable to select the database <b>%1\$s</b>."] = "Die Datenbank konnte nicht ausgew&auml;hlt werden <b>%1\$s</b>.";
$net2ftp_messages["The SQL query nr <b>%1\$s</b> could not be executed."] = "Die SQL-Abfrage Nr. <b>%1\$s</b> konnte nicht ausgef&uuml;hrt werden..";
$net2ftp_messages["The SQL query nr <b>%1\$s</b> was executed successfully."] = "Die SQL-Abfrage Nr. <b>%1\$s</b> wurde erfolgreich ausgef&uuml;hrt.";

// /skins/[skin]/admin_createtables1.template.php
$net2ftp_messages["Please enter your MySQL settings:"] = "Bitte geben Sie Ihre MySQL-Einstellungen ein:";
$net2ftp_messages["MySQL username"] = "MySQL Benutzername";
$net2ftp_messages["MySQL password"] = "MySQL Kennwort";
$net2ftp_messages["MySQL database"] = "MySQL Datenbankname";
$net2ftp_messages["MySQL server"] = "MySQL Server";
$net2ftp_messages["This SQL query is going to be executed:"] = "Folgende SQL-Anfrage wird ausgef&uuml;hrt:";
$net2ftp_messages["Execute"] = "Ausf&uuml;hren";

// /skins/[skin]/admin_createtables2.template.php
$net2ftp_messages["Settings used:"] = "Verwendete Einstellungen:";
$net2ftp_messages["MySQL password length"] = "MySQL Kennwortl&auml;nge";
$net2ftp_messages["Results:"] = "Ergebnisse:";

} // end admin_createtables


// -------------------------------------------------------------------------
// Admin_viewlogs module
if ($net2ftp_globals["state"] == "admin_viewlogs") {
// -------------------------------------------------------------------------

// /modules/admin_createtables/admin_viewlogs.inc.php
$net2ftp_messages["Admin functions"] = "Administrationsfunktionen";
$net2ftp_messages["Unable to execute the SQL query <b>%1\$s</b>."] = "Die SQL-Abfrage <b>%1\$s</b> konnte nicht ausgef&uuml;hrt werden.";
$net2ftp_messages["No data"] = "Keine Daten";

} // end admin_viewlogs


// -------------------------------------------------------------------------
// Admin_emptylogs module
if ($net2ftp_globals["state"] == "admin_emptylogs") {
// -------------------------------------------------------------------------

// /modules/admin_createtables/admin_emptylogs.inc.php
$net2ftp_messages["Admin functions"] = "Administrationsfunktionen";
$net2ftp_messages["The table <b>%1\$s</b> was emptied successfully."] = "Die Tabelle <b>%1\$s</b> wurde erfolgreich geleert.";
$net2ftp_messages["The table <b>%1\$s</b> could not be emptied."] = "Die Tabelle <b>%1\$s</b> konnte nicht geleert werden.";
$net2ftp_messages["The table <b>%1\$s</b> was optimized successfully."] = "Die Tabelle <b>%1\$s</b> wurde erfolgreich optimiert.";
$net2ftp_messages["The table <b>%1\$s</b> could not be optimized."] = "Die Tabelle <b>%1\$s</b> konnte nicht optimiert werden.";

} // end admin_emptylogs


// -------------------------------------------------------------------------
// Advanced module
if ($net2ftp_globals["state"] == "advanced") {
// -------------------------------------------------------------------------

// /modules/advanced/advanced.inc.php
$net2ftp_messages["Advanced functions"] = "Erweiterte Funktionen";

// /skins/[skin]/advanced1.template.php
$net2ftp_messages["Go"] = "Weiter";
$net2ftp_messages["Disabled"] = "Disabled";
$net2ftp_messages["Advanced FTP functions"] = "Erweiterte FTP Funktionen";
$net2ftp_messages["Send arbitrary FTP commands to the FTP server"] = "Sende benutzerdefinierte FTP-Kommandos zum FTP-Server";
$net2ftp_messages["This function is available on PHP 5 only"] = "Diese Funktion ist nur mit PHP5 verf&uuml;gbar";
$net2ftp_messages["Troubleshooting functions"] = "Fehlersuchfunktionen";
$net2ftp_messages["Troubleshoot net2ftp on this webserver"] = "Fehlersuche bei net2ftp auf diesem Webserver";
$net2ftp_messages["Troubleshoot an FTP server"] = "Fehlersuche bei einem FTP Server";
$net2ftp_messages["Test the net2ftp list parsing rules"] = "Teste die net2ftp Listensatzgliederungsregeln";
$net2ftp_messages["Translation functions"] = "&Uuml;bersetzungsfunktionen";
$net2ftp_messages["Introduction to the translation functions"] = "Einf&uuml;hrung in die &Uuml;bersetzungsfunktionen";
$net2ftp_messages["Extract messages to translate from code files"] = "Extrahiere zu &uuml;bersetzende Zeichenketten aus dem Quelltext";
$net2ftp_messages["Check if there are new or obsolete messages"] = "Suche nach neuen oder veralteten Zeichenketten";

$net2ftp_messages["Beta functions"] = "Betafunktionen";
$net2ftp_messages["Send a site command to the FTP server"] = "Ein SITE-Kommando auf dem FTP Server absetzen";
$net2ftp_messages["Apache: password-protect a directory, create custom error pages"] = "Apache: Verzeichnis passwortsch&uuml;tzen, eine eigene Error-Seite anlegen";
$net2ftp_messages["MySQL: execute an SQL query"] = "MySQL: eine SQL-Anfrage ausf&uuml;hren";


// advanced()
$net2ftp_messages["The site command functions are not available on this webserver."] = "Die Navigations Funktionen sind auf diesem Webserver nicht verf&uuml;gbar.";
$net2ftp_messages["The Apache functions are not available on this webserver."] = "Die Apache Funktionen sind auf diesem Webserver nicht verf&uuml;gbar.";
$net2ftp_messages["The MySQL functions are not available on this webserver."] = "Die MySQL Funktionen sind auf diesem Webserver nicht verf&uuml;gbar.";
$net2ftp_messages["Unexpected state2 string. Exiting."] = "Unerwartete state2-Zeichenkette. Beende.";

} // end advanced


// -------------------------------------------------------------------------
// Advanced_ftpserver module
if ($net2ftp_globals["state"] == "advanced_ftpserver") {
// -------------------------------------------------------------------------

// /modules/advanced_ftpserver/advanced_ftpserver.inc.php
$net2ftp_messages["Troubleshoot an FTP server"] = "Fehlersuche bei einem FTP Server";

// /skins/[skin]/advanced_ftpserver1.template.php
$net2ftp_messages["Connection settings:"] = "Verbindungseigenschaften:";
$net2ftp_messages["FTP server"] = "FTP Server";
$net2ftp_messages["FTP server port"] = "FTP Server Port";
$net2ftp_messages["Username"] = "Benutzername";
$net2ftp_messages["Password"] = "Passwort";
$net2ftp_messages["Password length"] = "Passwortl&auml;nge";
$net2ftp_messages["Passive mode"] = "Passiver Modus";
$net2ftp_messages["Directory"] = "Verzeichnis";
$net2ftp_messages["Printing the result"] = "Drucke das Ergebnis";

// /skins/[skin]/advanced_ftpserver2.template.php
$net2ftp_messages["Connecting to the FTP server: "] = "Verbinden mit dem FTP Server: ";
$net2ftp_messages["Logging into the FTP server: "] = "Anmelden am FTP Server: ";
$net2ftp_messages["Setting the passive mode: "] = "Setzen des passiven Modus:";
$net2ftp_messages["Getting the FTP server system type: "] = "Pr&uuml;fe Systemtyp des FTP-Servers: ";
$net2ftp_messages["Changing to the directory %1\$s: "] = "Wechseln in das Verzeichniss %1\$s: ";
$net2ftp_messages["The directory from the FTP server is: %1\$s "] = "Das Verzeichniss des FTP Server ist: %1\$s ";
$net2ftp_messages["Getting the raw list of directories and files: "] = "Empfang einer Rohliste der Dateien und Ordnern: ";
$net2ftp_messages["Trying a second time to get the raw list of directories and files: "] = "Erneuter Empfangsversuch einer Rohliste der Dateien und Ordnern: ";
$net2ftp_messages["Closing the connection: "] = "Verbindung wird geschlossen: ";
$net2ftp_messages["Raw list of directories and files:"] = "Rohliste der Dateien und Ordner:";
$net2ftp_messages["Parsed list of directories and files:"] = "Gegliederte Liste der Dateien und Ordner:";

$net2ftp_messages["OK"] = "OK";
$net2ftp_messages["not OK"] = "not OK";

} // end advanced_ftpserver


// -------------------------------------------------------------------------
// Advanced_parsing module
if ($net2ftp_globals["state"] == "advanced_parsing") {
// -------------------------------------------------------------------------

$net2ftp_messages["Test the net2ftp list parsing rules"] = "Teste die net2ftp Listensatzgliederungsregeln";
$net2ftp_messages["Sample input"] = "Sample input";
$net2ftp_messages["Parsed output"] = "Parsed output";

} // end advanced_parsing


// -------------------------------------------------------------------------
// Advanced_webserver module
if ($net2ftp_globals["state"] == "advanced_webserver") {
// -------------------------------------------------------------------------

$net2ftp_messages["Troubleshoot your net2ftp installation"] = "Fehlersuche bei der net2ftp Installation";
$net2ftp_messages["Printing the result"] = "Drucke das Ergebnis";

$net2ftp_messages["Checking if the FTP module of PHP is installed: "] = "&Uuml;berpr&uuml;fen ob das FTP Modul von PHP installiert ist";
$net2ftp_messages["yes"] = "Ja";
$net2ftp_messages["no - please install it!"] = "Nein - bitte installieren!";

$net2ftp_messages["Checking the permissions of the directory on the web server: a small file will be written to the /temp folder and then deleted."] = "&Uuml;berpr&uuml;fung der Berechtigungen des Verzeichnisses auf dem Webserver: eine kleine Datei wird in den /temp Ordner geschrieben und anschlie&szlig;end gel&ouml;scht.";
$net2ftp_messages["Creating filename: "] = "Dateiname wird erstellt: ";
$net2ftp_messages["OK. Filename: %1\$s"] = "OK. Dateiname: %tempfilename";
$net2ftp_messages["not OK"] = "not OK";
$net2ftp_messages["OK"] = "OK";
$net2ftp_messages["not OK. Check the permissions of the %1\$s directory"] = "nicht OK. Bitte die Berechtigung des Ordners %1\$s &uuml;berpr&uuml;fen";
$net2ftp_messages["Opening the file in write mode: "] = "&Ouml;ffnen der Datei im Schreib-Modus: ";
$net2ftp_messages["Writing some text to the file: "] = "Schreiben von Text in die Datei: ";
$net2ftp_messages["Closing the file: "] = "Schlie&szlig;en der Datei: ";
$net2ftp_messages["Deleting the file: "] = "L&ouml;schen der Datei: ";

$net2ftp_messages["Testing the FTP functions"] = "Testing the FTP functions";
$net2ftp_messages["Connecting to a test FTP server: "] = "Connecting to a test FTP server: ";
$net2ftp_messages["Connecting to the FTP server: "] = "Verbinden mit dem FTP Server: ";
$net2ftp_messages["Logging into the FTP server: "] = "Anmelden am FTP Server: ";
$net2ftp_messages["Setting the passive mode: "] = "Setzen des passiven Modus:";
$net2ftp_messages["Getting the FTP server system type: "] = "Pr&uuml;fe Systemtyp des FTP-Servers: ";
$net2ftp_messages["Changing to the directory %1\$s: "] = "Wechseln in das Verzeichniss %1\$s: ";
$net2ftp_messages["The directory from the FTP server is: %1\$s "] = "Das Verzeichniss des FTP Server ist: %1\$s ";
$net2ftp_messages["Getting the raw list of directories and files: "] = "Empfang einer Rohliste der Dateien und Ordnern: ";
$net2ftp_messages["Trying a second time to get the raw list of directories and files: "] = "Erneuter Empfangsversuch einer Rohliste der Dateien und Ordnern: ";
$net2ftp_messages["Closing the connection: "] = "Verbindung wird geschlossen: ";
$net2ftp_messages["Raw list of directories and files:"] = "Rohliste der Dateien und Ordner:";
$net2ftp_messages["Parsed list of directories and files:"] = "Gegliederte Liste der Dateien und Ordner:";
$net2ftp_messages["OK"] = "OK";
$net2ftp_messages["not OK"] = "not OK";

} // end advanced_webserver


// -------------------------------------------------------------------------
// Bookmark module
if ($net2ftp_globals["state"] == "bookmark") {
// -------------------------------------------------------------------------

$net2ftp_messages["Drag and drop one of the links below to the bookmarks bar"] = "Klicken und ziehen Sie einen der Links in Ihre Lesezeichen- oder Favoritenleiste";
$net2ftp_messages["Right-click on one of the links below and choose \"Add to Favorites...\""] = "Rechtsklick auf einen der Links und \"Zu Favoriten hinzuf&uuml;gen ...\" ausw&auml;hlen";
$net2ftp_messages["Right-click on one the links below and choose \"Add Link to Bookmarks...\""] = "Rechtsklick auf einen der Links und \"Add Link to Bookmarks...\" ausw&auml;hlen";
$net2ftp_messages["Right-click on one of the links below and choose \"Bookmark link...\""] = "Rechtsklick auf einen der Links und \"Bookmark link...\" ausw&auml;hlen";
$net2ftp_messages["Right-click on one of the links below and choose \"Bookmark This Link...\""] = "Rechtsklick auf einen der Links und \"Lesezeichen f&uuml;r diesen Link hinzuf&uuml;gen...\" ausw&auml;hlen";
$net2ftp_messages["One click access (net2ftp won't ask for a password - less safe)"] = "1-Klick Zugang (net2ftp fragt nicht nach Ihrem Passwort - unsicher)";
$net2ftp_messages["Two click access (net2ftp will ask for a password - safer)"] = "2-Klick Zugang (net2ftp wird nach Ihrem Passwort fragen - sicher)";
$net2ftp_messages["Note: when you will use this bookmark, a popup window will ask you for your username and password."] = "Achtung: Wenn Sie dieses Lesezeichen benutzen, werden Sie in einem Popup Fenster nach dem Usernamen und Passwort gefragt.";

} // end bookmark


// -------------------------------------------------------------------------
// Browse module
if ($net2ftp_globals["state"] == "browse") {
// -------------------------------------------------------------------------

// /modules/browse/browse.inc.php
$net2ftp_messages["Choose a directory"] = "Verzeichniss ausw&auml;hlen";
$net2ftp_messages["Please wait..."] = "Bitte warten...";

// browse()
$net2ftp_messages["Directories with names containing \' cannot be displayed correctly. They can only be deleted. Please go back and select another subdirectory."] = "Verzeichnisse, die  \' enthalten, k&ouml;nnen nicht korrekt dargestellt werden. Diese k&ouml;nnen nur gel&ouml;scht werden. Bitte gehen Sie zur&uuml;ck und w&auml;hlen Sie ein anderes Verzeichniss.";

$net2ftp_messages["Daily limit reached: you will not be able to transfer data"] = "Tages-Beschr&auml;nkung erreicht: Sie k&ouml;nnen keine Daten mehr transferieren.";
$net2ftp_messages["In order to guarantee the fair use of the web server for everyone, the data transfer volume and script execution time are limited per user, and per day. Once this limit is reached, you can still browse the FTP server but not transfer data to/from it."] = "Um die faire Nutzung des Webservers f&uuml;r alle Nutzer zu gew&aumlhrleisten, ist das Transfervolumen und die Laufzeit von Skripten pro Nutzer und Tag beschr&auml;nkt. Wird die Beschr&auml;nkung erreicht, k&ouml;nnen Sie immernoch den FTP Server durchsuchen, allerdings k&ouml;nnen keine Daten mehr hoch- oder runtergeladen werden.";
$net2ftp_messages["If you need unlimited usage, please install net2ftp on your own web server."] = "Wenn Sie unbeschr&auml;nkten Zugang ben&ouml;tigen, installieren Sie net2ftp bitte auf Ihrem eigenen Webserver.";

// printdirfilelist()
// Keep this short, it must fit in a small button!
$net2ftp_messages["New dir"] = "Neuer Ordner";
$net2ftp_messages["New file"] = "Neue Text-Datei";
$net2ftp_messages["HTML templates"] = "HTML templates";
$net2ftp_messages["Upload"] = "Upload";
$net2ftp_messages["Java Upload"] = "Java Upload";
$net2ftp_messages["Flash Upload"] = "Flash Upload";
$net2ftp_messages["Install"] = "Install";
$net2ftp_messages["Advanced"] = "Erweitert";
$net2ftp_messages["Copy"] = "Kopieren";
$net2ftp_messages["Move"] = "Verschieben";
$net2ftp_messages["Delete"] = "L&ouml;schen";
$net2ftp_messages["Rename"] = "Umbenennen";
$net2ftp_messages["Chmod"] = "Zugriffsrechte";
$net2ftp_messages["Download"] = "Download";
$net2ftp_messages["Unzip"] = "Unzip";
$net2ftp_messages["Zip"] = "Zip";
$net2ftp_messages["Size"] = "Gr&ouml;&szlig;e";
$net2ftp_messages["Search"] = "Suchen";
$net2ftp_messages["Go to the parent directory"] = "&Uuml;bergeordneter Ordner";
$net2ftp_messages["Go"] = "Weiter";
$net2ftp_messages["Transform selected entries: "] = "Ausgew&auml;hlte Eintr&auml;ge: ";
$net2ftp_messages["Transform selected entry: "] = "Transform selected entry: ";
$net2ftp_messages["Make a new subdirectory in directory %1\$s"] = "Erstellen eines neuen Unterverzeichnisses im Ordner %1\$s";
$net2ftp_messages["Create a new file in directory %1\$s"] = "Erstellen einer neuen Datein im Ordner %1\$s";
$net2ftp_messages["Create a website easily using ready-made templates"] = "Erstellen einer neuen Webseite mir vorgefertigeten Templates";
$net2ftp_messages["Upload new files in directory %1\$s"] = "Upload neuer Dateien in Verzeichniss %1\$s";
$net2ftp_messages["Upload directories and files using a Java applet"] = "Upload von Ordnern und Dateien mit einem Java Applet";
$net2ftp_messages["Upload files using a Flash applet"] = "Upload Dateien mit einem Flash Applet";
$net2ftp_messages["Install software packages (requires PHP on web server)"] = "Installieren von Software-Paketen (ben&ouml;tigt PHP auf dem Webserver)";
$net2ftp_messages["Go to the advanced functions"] = "Wechseln zu erweiterten Funktionen";
$net2ftp_messages["Copy the selected entries"] = "Kopieren der ausgew&auml;hlten Eintr&auml;ge";
$net2ftp_messages["Move the selected entries"] = "Verschieben der ausgew&auml;hlten Eintr&auml;ge";
$net2ftp_messages["Delete the selected entries"] = "L&ouml;schen der ausgew&auml;hlten Eintr&auml;ge";
$net2ftp_messages["Rename the selected entries"] = "Umbenennen der ausgew&auml;hlten Eintr&auml;ge";
$net2ftp_messages["Chmod the selected entries (only works on Unix/Linux/BSD servers)"] = "Zugriffsrechte der ausgew&auml;hlten Eintr&auml;ge &auml;ndern (funktioniert nur auf Unix/Linux/BSD Servern)";
$net2ftp_messages["Download a zip file containing all selected entries"] = "Download eine ZIP Datei mit allen ausgew&auml;hlten Elementen";
$net2ftp_messages["Unzip the selected archives on the FTP server"] = "Entpacke das ausgew&auml;hlte Archiv auf dem FTP-Server";
$net2ftp_messages["Zip the selected entries to save or email them"] = "Zippen der ausgew&auml;lten Elemente zum speichern oder versenden per Mail";
$net2ftp_messages["Calculate the size of the selected entries"] = "Kalkulieren der Gr&ouml;&szlig;e ausgew&auml;hlter Eintr&auml;ge";
$net2ftp_messages["Find files which contain a particular word"] = "Suchen nach Dateien mit einem bestimmten Wort im Text";
$net2ftp_messages["Click to sort by %1\$s in descending order"] = "Absteigend nach %1\$s sortieren";
$net2ftp_messages["Click to sort by %1\$s in ascending order"] = "Aufsteigend nach %1\$s sortieren";
$net2ftp_messages["Ascending order"] = "Aufsteigend";
$net2ftp_messages["Descending order"] = "Absteigend";
$net2ftp_messages["Upload files"] = "Upload Dateien";
$net2ftp_messages["Up"] = "Aufw&auml;rts";
$net2ftp_messages["Click to check or uncheck all rows"] = "Alle Zeilen an- bzw. abw&auml;hlen";
$net2ftp_messages["All"] = "Alle";
$net2ftp_messages["Name"] = "Name";
$net2ftp_messages["Type"] = "Typ";
//$net2ftp_messages["Size"] = "Size";
$net2ftp_messages["Owner"] = "Besitzer";
$net2ftp_messages["Group"] = "Gruppe";
$net2ftp_messages["Perms"] = "Berechtigungen";
$net2ftp_messages["Mod Time"] = "&Auml;nderungs-Datum/Zeit";
$net2ftp_messages["Actions"] = "Aktionen";
$net2ftp_messages["Select the directory %1\$s"] = "W&auml;hle Verzeichnis %1\$s";
$net2ftp_messages["Select the file %1\$s"] = "W&auml;hle Datei %1\$s";
$net2ftp_messages["Select the symlink %1\$s"] = "W&auml;hle das Alias/Symlink %1\$s";
$net2ftp_messages["Go to the subdirectory %1\$s"] = "Gehe zum Unterverzeichnis %1\$s";
$net2ftp_messages["Download the file %1\$s"] = "Datei %1\$s herunterladen";
$net2ftp_messages["Follow symlink %1\$s"] = "Folge Alias/Symlink %1\$s";
$net2ftp_messages["View"] = "Anzeigen";
$net2ftp_messages["Edit"] = "Bearbeiten";
$net2ftp_messages["Update"] = "Aktualisieren";
$net2ftp_messages["Open"] = "&Ouml;ffnen";
$net2ftp_messages["View the highlighted source code of file %1\$s"] = "Den Quellcode der Datei %1\$s ansehen";
$net2ftp_messages["Edit the source code of file %1\$s"] = "Den Quellcode der Datei %1\$s bearbeiten";
$net2ftp_messages["Upload a new version of the file %1\$s and merge the changes"] = "Hochladen einer neuen Version der Datei %1\$s und zusammenf&uuml;gen der &Auml;nderungen";
$net2ftp_messages["View image %1\$s"] = "View image %1\$s";
$net2ftp_messages["View the file %1\$s from your HTTP web server"] = "Die Datei %1\$s von Ihrem Webserver ansehen";
$net2ftp_messages["(Note: This link may not work if you don't have your own domain name.)"] = "(Achtung: Dieser Link wird nicht funktionieren, wenn Sie keinen eigene Dom&auml;ne haben.)";
$net2ftp_messages["This folder is empty"] = "Dieser Ordner ist leer";

// printSeparatorRow()
$net2ftp_messages["Directories"] = "Ordner";
$net2ftp_messages["Files"] = "Dateien";
$net2ftp_messages["Symlinks"] = "Symlinks";
$net2ftp_messages["Unrecognized FTP output"] = "Unerkannter FTP Output";
$net2ftp_messages["Number"] = "Nummer";
$net2ftp_messages["Size"] = "Gr&ouml;&szlig;e";
$net2ftp_messages["Skipped"] = "Ausgelassen";
$net2ftp_messages["Data transferred from this IP address today"] = "Heute &uuml;bertragene Daten von IP-Adresse";
$net2ftp_messages["Data transferred to this FTP server today"] = "Heute &uuml;bertragene Daten vom FTP-Server";

// printLocationActions()
$net2ftp_messages["Language:"] = "Sprache:";
$net2ftp_messages["Skin:"] = "Skin:";
$net2ftp_messages["View mode:"] = "Ansichts-Modus:";
$net2ftp_messages["Directory Tree"] = "Verzeichnissbaum";

// ftp2http()
$net2ftp_messages["Execute %1\$s in a new window"] = "In einem neuen Fenster %1\$s ausf&uuml;hren";
$net2ftp_messages["This file is not accessible from the web"] = "Auf diese Datei kann aus dem Web nicht zugegriffen werden";

// printDirectorySelect()
$net2ftp_messages["Double-click to go to a subdirectory:"] = "Doppleklick um in ein Unterverzeichniss zu wechseln:";
$net2ftp_messages["Choose"] = "Auswahl";
$net2ftp_messages["Up"] = "Aufw&auml;rts";

} // end browse


// -------------------------------------------------------------------------
// Calculate size module
if ($net2ftp_globals["state"] == "calculatesize") {
// -------------------------------------------------------------------------
$net2ftp_messages["Size of selected directories and files"] = "Gr&ouml;&szlig;e der ausgew&auml;hlten Ordner und Dateien";
$net2ftp_messages["The total size taken by the selected directories and files is:"] = "Die verbrauchte Gesamtgr&ouml;&szlig;e der ausgew&auml;hlten Ordner und Dateien ist:";
$net2ftp_messages["The number of files which were skipped is:"] = "Anzahl der Dateien, die ausgelassen wurden:";

} // end calculatesize


// -------------------------------------------------------------------------
// Chmod module
if ($net2ftp_globals["state"] == "chmod") {
// -------------------------------------------------------------------------
$net2ftp_messages["Chmod directories and files"] = "Berechtigungen von Ordnern und Dateien &auml;ndern";
$net2ftp_messages["Set all permissions"] = "Setzen aller Berechtigungen";
$net2ftp_messages["Read"] = "Lesen";
$net2ftp_messages["Write"] = "Schreiben";
$net2ftp_messages["Execute"] = "Ausf&uuml;hren";
$net2ftp_messages["Owner"] = "Besitzer";
$net2ftp_messages["Group"] = "Gruppe";
$net2ftp_messages["Everyone"] = "Jeder";
$net2ftp_messages["To set all permissions to the same values, enter those permissions and click on the button \"Set all permissions\""] = "To set all permissions to the same values, enter those permissions and click on the button \"Set all permissions\"";
$net2ftp_messages["Set the permissions of directory <b>%1\$s</b> to: "] = "Setze Zugriffsrechte des Ordners <b>%1\$s</b> auf: ";
$net2ftp_messages["Set the permissions of file <b>%1\$s</b> to: "] = "Setze Zugriffsrechte der Datei<b>%1\$s</b> auf: ";
$net2ftp_messages["Set the permissions of symlink <b>%1\$s</b> to: "] = "Setze Zugriffsrechte des Alias/Symlinks <b>%1\$s</b> auf: ";
$net2ftp_messages["Chmod value"] = "Zugriffsrecht";
$net2ftp_messages["Chmod also the subdirectories within this directory"] = "Zugriffsrechte auch in Unterordnern dieses Ordners setzen";
$net2ftp_messages["Chmod also the files within this directory"] = "Zugriffsrechte auch f&uuml;r Dateien in diesem Ordner setzen";
$net2ftp_messages["The chmod nr <b>%1\$s</b> is out of the range 000-777. Please try again."] = "Das Zugriffsrecht <b>%1\$s</b> ist nicht innerhalb des erlaubten Bereichs 000-777. Bitte versuchen Sie es erneut.";

} // end chmod


// -------------------------------------------------------------------------
// Clear cookies module
// -------------------------------------------------------------------------
// No messages


// -------------------------------------------------------------------------
// Copy/Move/Delete module
if ($net2ftp_globals["state"] == "copymovedelete") {
// -------------------------------------------------------------------------
$net2ftp_messages["Choose a directory"] = "Verzeichniss ausw&auml;hlen";
$net2ftp_messages["Copy directories and files"] = "Dateien und Verzeichnisse kopieren";
$net2ftp_messages["Move directories and files"] = "Dateien und Verzeichnisse verschieben";
$net2ftp_messages["Delete directories and files"] = "Dateien und Verzeichnisse l&ouml;schen";
$net2ftp_messages["Are you sure you want to delete these directories and files?"] = "Sind Sie sicher, dass Sie diese Dateien und Verzeichnisse l&ouml;schen wollen?";
$net2ftp_messages["All the subdirectories and files of the selected directories will also be deleted!"] = "Alle Unterordner und Dateien der ausgew&auml;hlten Verzeichnisse werden ebenfalls gel&ouml;scht!";
$net2ftp_messages["Set all targetdirectories"] = "Setzen als Zielverzeichniss f&uuml;r alle";
$net2ftp_messages["To set a common target directory, enter that target directory in the textbox above and click on the button \"Set all targetdirectories\"."] = "Um einen gemeinsamen Zielordner anzugeben, tragen Sie das Zielverzeichnis in das obere Eingabefeld ein, und klicken auf \"Set all targetdirectories\" bzw \"Alle Zielordner setzen\".";
$net2ftp_messages["Note: the target directory must already exist before anything can be copied into it."] = "Hinweis: der Zielordner muss bereits existieren, bevor Dateien hineinkopiert werden k&ouml;nnen.";
$net2ftp_messages["Different target FTP server:"] = "Anderer Ziel FTP Server:";
$net2ftp_messages["Username"] = "Benutzername";
$net2ftp_messages["Password"] = "Passwort";
$net2ftp_messages["Leave empty if you want to copy the files to the same FTP server."] = "Leer lassen, um Dateien auf den gleichen FTP Server zu &uuml;bertragen";
$net2ftp_messages["If you want to copy the files to another FTP server, enter your login data."] = "Um Dateien auf einen anderen FTP-Server zu &uuml;bertragen, geben Sie Ihre Login-Daten ein.";
$net2ftp_messages["Leave empty if you want to move the files to the same FTP server."] = "Leer lassen, um Dateien auf dem gleichen FTP-Server zu verschieben.";
$net2ftp_messages["If you want to move the files to another FTP server, enter your login data."] = "Um Dateien auf einen anderen FTP-Server zu verschieben, geben Sie Ihre Login-Daten ein.";
$net2ftp_messages["Copy directory <b>%1\$s</b> to:"] = "Kopiere Verzeichniss <b>%1\$s</b> nach:";
$net2ftp_messages["Move directory <b>%1\$s</b> to:"] = "Verschiebe Verzeichniss <b>%1\$s</b> nach:";
$net2ftp_messages["Directory <b>%1\$s</b>"] = "Verzeichniss <b>%1\$s</b>";
$net2ftp_messages["Copy file <b>%1\$s</b> to:"] = "Kopiere Datei <b>%1\$s</b> nach:";
$net2ftp_messages["Move file <b>%1\$s</b> to:"] = "Verschiebe Datei <b>%1\$s</b> nach:";
$net2ftp_messages["File <b>%1\$s</b>"] = "File <b>%1\$s</b>";
$net2ftp_messages["Copy symlink <b>%1\$s</b> to:"] = "Kopiere Symlink <b>%1\$s</b> nach:";
$net2ftp_messages["Move symlink <b>%1\$s</b> to:"] = "Verschiebe Symlink <b>%1\$s</b> nach:";
$net2ftp_messages["Symlink <b>%1\$s</b>"] = "Symlink <b>%1\$s</b>";
$net2ftp_messages["Target directory:"] = "Ziel Verzeichniss:";
$net2ftp_messages["Target name:"] = "Ziel Name:";
$net2ftp_messages["Processing the entries:"] = "Verarbeiten der Eintr&auml;ge:";

} // end copymovedelete


// -------------------------------------------------------------------------
// Download file module
// -------------------------------------------------------------------------
// No messages


// -------------------------------------------------------------------------
// EasyWebsite module
if ($net2ftp_globals["state"] == "easyWebsite") {
// -------------------------------------------------------------------------
$net2ftp_messages["Create a website in 4 easy steps"] = "Erstellen einer Webseite i 4 einfachen Schritten";
$net2ftp_messages["Template overview"] = "Template &Uml;bersicht";
$net2ftp_messages["Template details"] = "Template Details";
$net2ftp_messages["Files are copied"] = "Dateien wurden kopiert";
$net2ftp_messages["Edit your pages"] = "Bearbeiten Sie Ihre Seiten";

// Screen 1 - printTemplateOverview
$net2ftp_messages["Click on the image to view the details of a template."] = "Klicken Sie auf das Bild, um die Details eines Templates zu sehen.";
$net2ftp_messages["Back to the Browse screen"] = "Zur&uuml;ck zum Browse-Fenster";
$net2ftp_messages["Template"] = "Template";
$net2ftp_messages["Copyright"] = "Copyright";
$net2ftp_messages["Click on the image to view the details of this template"] = "Klicken Sie auf das Bild, um die Details des Templates zu sehen.";

// Screen 2 - printTemplateDetails
$net2ftp_messages["The template files will be copied to your FTP server. Existing files with the same filename will be overwritten. Do you want to continue?"] = "Die Template-Datei wird auf den FTP-Server kopiert. Existierende Dateien mit gleichen Dateinamen werden &uuml;berschrieben! M&ouml;chten Sie fortfahren?";
$net2ftp_messages["Install template to directory: "] = "Installiere Template in Verzeichnis: ";
$net2ftp_messages["Install"] = "Install";
$net2ftp_messages["Size"] = "Gr&ouml;&szlig;e";
$net2ftp_messages["Preview page"] = "Seitenvorschau";
$net2ftp_messages["opens in a new window"] = "&ouml;ffnen in einem neuen Fenster";

// Screen 3
$net2ftp_messages["Please wait while the template files are being transferred to your server: "] = "Bitte warten Sie einen Moment, die Template-Dateien werden auf Ihren FTP-Server &uuml;bertragen: ";
$net2ftp_messages["Done."] = "Fertig!";
$net2ftp_messages["Continue"] = "Fortsetzen";

// Screen 4 - printEasyAdminPanel
$net2ftp_messages["Edit page"] = "Seite bearbeiten";
$net2ftp_messages["Browse the FTP server"] = "Diesen FTP-Server durchsuchen";
$net2ftp_messages["Add this link to your favorites to return to this page later on!"] = "F&uuml;gen Sie diesen Link zu Ihren Favoriten hinzu, um die Seite erneut zu besuchen!";
$net2ftp_messages["Edit website at %1\$s"] = "Bearbeite Webseite auf %1\$s";
$net2ftp_messages["Internet Explorer: right-click on the link and choose \"Add to Favorites...\""] = "Internet Explorer: Rechtsklick auf den Link und \"Zu Favoriten hinzuf�gen ...\" ausw�hlen";
$net2ftp_messages["Netscape, Mozilla, Firefox: right-click on the link and choose \"Bookmark This Link...\""] = "Netscape, Mozilla, Firefox: Rechtsklick auf den Link und \"Bookmark This Link...\" ausw�hlen";

// ftp_copy_local2ftp
$net2ftp_messages["WARNING: Unable to create the subdirectory <b>%1\$s</b>. It may already exist. Continuing..."] = "WARNUNG: Unm&ouml;glich das Unterverzeichnis <b>%1\$s</b> zu erstellen. Vielleicht existiert es bereits. Setze fort...";
$net2ftp_messages["Created target subdirectory <b>%1\$s</b>"] = "Zielunterverzeichnis <b>%1\$s</b> erstellt";
$net2ftp_messages["WARNING: Unable to copy the file <b>%1\$s</b>. Continuing..."] = "WARNUNG:  Konnte Datei <b>%1\$s</b> nicht kopieren. Setze fort...";
$net2ftp_messages["Copied file <b>%1\$s</b>"] = "Datei <b>%1\$s</b> kopiert";
}


// -------------------------------------------------------------------------
// Edit module
if ($net2ftp_globals["state"] == "edit") {
// -------------------------------------------------------------------------

// /modules/edit/edit.inc.php
$net2ftp_messages["Unable to open the template file"] = "Die Vorlage kann nicht ge&ouml;ffnet werden";
$net2ftp_messages["Unable to read the template file"] = "Die Vorlage kann nicht gelesen werden";
$net2ftp_messages["Please specify a filename"] = "Bitte geben Sie einen Dateinamen an";
$net2ftp_messages["Status: This file has not yet been saved"] = "Status: Diese Datei wurde noch nicht gespeichert";
$net2ftp_messages["Status: Saved on <b>%1\$s</b> using mode %2\$s"] = "Status: Speichern auf <b>%1\$s</b> im Modus %2\$s";
$net2ftp_messages["Status: <b>This file could not be saved</b>"] = "Status: <b>Die Datei konnte nicht gespeichert werden</b>";
$net2ftp_messages["Not yet saved"] = "Not yet saved";
$net2ftp_messages["Could not be saved"] = "Could not be saved";
$net2ftp_messages["Saved at %1\$s"] = "Saved at %1\$s";

// /skins/[skin]/edit.template.php
$net2ftp_messages["Directory: "] = "Verzeichniss: ";
$net2ftp_messages["File: "] = "Datei: ";
$net2ftp_messages["New file name: "] = "Dateiname: ";
$net2ftp_messages["Character encoding: "] = "Character encoding: ";
$net2ftp_messages["Note: changing the textarea type will save the changes"] = "Hinweis: &Auml;ndern des Textarea-Typs speichert die &Auml;nderungen";
$net2ftp_messages["Copy up"] = "Copy up";
$net2ftp_messages["Copy down"] = "Copy down";

} // end if edit


// -------------------------------------------------------------------------
// Find string module
if ($net2ftp_globals["state"] == "findstring") {
// -------------------------------------------------------------------------

// /modules/findstring/findstring.inc.php 
$net2ftp_messages["Search directories and files"] = "Suche Ordner und Dateien";
$net2ftp_messages["Search again"] = "Erneute Suche";
$net2ftp_messages["Search results"] = "Suchergebnisse";
$net2ftp_messages["Please enter a valid search word or phrase."] = "Bitte geben Sie ein g&uuml;ltiges Suchwort oder Satzteil ein.";
$net2ftp_messages["Please enter a valid filename."] = "Bitte geben Sie einen g&uuml;ltigen Dateinamen an.";
$net2ftp_messages["Please enter a valid file size in the \"from\" textbox, for example 0."] = "Bitte geben Sie eine g&uuml;ltige Dateigr&ouml;&szlig;e im \"von\" Textfeld ein, zum Beispiel 0.";
$net2ftp_messages["Please enter a valid file size in the \"to\" textbox, for example 500000."] = "Bitte geben Sie eine g&uuml;ltige Dateigr&ouml;&szlig;e im \"bis\" Textfeld ein, zum Beispiel 500000.";
$net2ftp_messages["Please enter a valid date in Y-m-d format in the \"from\" textbox."] = "Bitte geben Sie ein g&uuml;ltiges Datum in der Form J-m-t in das \"von\" Textfeld ein.";
$net2ftp_messages["Please enter a valid date in Y-m-d format in the \"to\" textbox."] = "Bitte geben Sie ein g&uuml;ltiges Datum in der Form J-m-t in das \"bis\" Textfeld ein.";
$net2ftp_messages["The word <b>%1\$s</b> was not found in the selected directories and files."] = "Das Suchwort <b>%1\$s</b> konnte in den ausgew&auml;hlten Dateien und Ordnern nicht gefunden werden.";
$net2ftp_messages["The word <b>%1\$s</b> was found in the following files:"] = "Das Suchwort <b>%1\$s</b> wurde in folgenden Dateien gefunden:";

// /skins/[skin]/findstring1.template.php
$net2ftp_messages["Search for a word or phrase"] = "Suche nach einem Wort oder Satzteil";
$net2ftp_messages["Case sensitive search"] = "Gro&szlig;- und Kleinschreibung bei Suche beachten";
$net2ftp_messages["Restrict the search to:"] = "Einschr&auml;nken der Suche nach:";
$net2ftp_messages["files with a filename like"] = "Dateien mit einem Namen wie";
$net2ftp_messages["(wildcard character is *)"] = "(wildcard character is *)";
$net2ftp_messages["files with a size"] = "Dateien mit einer Gr&ouml;&szlig;e";
$net2ftp_messages["files which were last modified"] = "Dateien die zuletzt ge&auml;ndert wurden am";
$net2ftp_messages["from"] = "von";
$net2ftp_messages["to"] = "bis";

$net2ftp_messages["Directory"] = "Verzeichnis";
$net2ftp_messages["File"] = "Datei";
$net2ftp_messages["Line"] = "Line";
$net2ftp_messages["Action"] = "Aktion";
$net2ftp_messages["View"] = "Anzeigen";
$net2ftp_messages["Edit"] = "Bearbeiten";
$net2ftp_messages["View the highlighted source code of file %1\$s"] = "Den Quellcode der Datei %1\$s ansehen";
$net2ftp_messages["Edit the source code of file %1\$s"] = "Den Quellcode der Datei %1\$s bearbeiten";

} // end findstring


// -------------------------------------------------------------------------
// Help module
// -------------------------------------------------------------------------
// No messages yet


// -------------------------------------------------------------------------
// Install size module
if ($net2ftp_globals["state"] == "install") {
// -------------------------------------------------------------------------

// /modules/install/install.inc.php
$net2ftp_messages["Install software packages"] = "Installiere Software Pakete";
$net2ftp_messages["Unable to open the template file"] = "Die Vorlage kann nicht ge&ouml;ffnet werden";
$net2ftp_messages["Unable to read the template file"] = "Die Vorlage kann nicht gelesen werden";
$net2ftp_messages["Unable to get the list of packages"] = "Die Paketliste konnte nicht geholt werden";

// /skins/blue/install1.template.php
$net2ftp_messages["The net2ftp installer script has been copied to the FTP server."] = "Das net2ftp Installationsskript wurde auf den FTP-Server kopiert.";
$net2ftp_messages["This script runs on your web server and requires PHP to be installed."] = "Dieses Skript l&auml;uft auf Ihrem Webserver und ben&ouml;tigt PHP.";
$net2ftp_messages["In order to run it, click on the link below."] = "Um es laufen zu lassen, klicken Sie auf den Link unten.";
$net2ftp_messages["net2ftp has tried to determine the directory mapping between the FTP server and the web server."] = "net2ftp hat versucht, die Verzeichnis-Abbildung zwischen FTP- und Webserver zu bestimmen.";
$net2ftp_messages["Should this link not be correct, enter the URL manually in your web browser."] = "Sollte der Link nicht korrekt sein, geben Sie URL manuell in Ihren Webbrowser ein.";

} // end install


// -------------------------------------------------------------------------
// Java upload module
if ($net2ftp_globals["state"] == "jupload") {
// -------------------------------------------------------------------------
$net2ftp_messages["Upload directories and files using a Java applet"] = "Upload von Ordnern und Dateien mit einem Java Applet";
$net2ftp_messages["Your browser does not support applets, or you have disabled applets in your browser settings."] = "Ihr Browser unterst&uuml;tzt keine Java-Anwendungen oder Sie haben diese explizit in den Einstellungen deaktiviert.";
$net2ftp_messages["To use this applet, please install the newest version of Sun's java. You can get it from <a href=\"http://www.java.com/\">java.com</a>. Click on Get It Now."] = "Um diese Funktion nutzen zu k&ouml;nnen ben&ouml;tigen Sie die neueste Java Version. Bitte gehen Sie auf <a href=\"http://www.java.com/\" target=\"_blank\">java.com</a> und installieren oder aktivieren Sie die neueste Version f&uuml;r Ihren Browser.";
$net2ftp_messages["The online installation is about 1-2 MB and the offline installation is about 13 MB. This 'end-user' java is called JRE (Java Runtime Environment)."] = "Sie ben&ouml;tigen mindestens die JRE (Java Runtime Environment) Variante. Wenden Sie sich an Ihren Systemadministrator wenn Sie Probleme haben.";
$net2ftp_messages["Alternatively, use net2ftp's normal upload or upload-and-unzip functionality."] = "Alternativ k&ouml;nnen Sie auch die normale Upload oder Flash-Upload (erfordert das Flash-Plugin) Funktion nutzen.";

} // end jupload



// -------------------------------------------------------------------------
// Login module
if ($net2ftp_globals["state"] == "login") {
// -------------------------------------------------------------------------
$net2ftp_messages["Login!"] = "Login!";
$net2ftp_messages["Once you are logged in, you will be able to:"] = "Sobald Sie sich angemeldet haben, k&ouml;nnen Sie:";
$net2ftp_messages["Navigate the FTP server"] = "Steuerung des FTP-Servers";
$net2ftp_messages["Once you have logged in, you can browse from directory to directory and see all the subdirectories and files."] = "Durchsuchen der Verzeichnisse, dabei sehen Sie alle Dateien und Unterverzeichnisse.";
$net2ftp_messages["Upload files"] = "Upload Dateien";
$net2ftp_messages["There are 3 different ways to upload files: the standard upload form, the upload-and-unzip functionality, and the Java Applet."] = "Es gibt 3 verschiedene M&ouml;glichkeiten, um Dateien hochzuladen: das Standardformular, die hochladen und auspacken Funktion und das Java-Applet.";
$net2ftp_messages["Download files"] = "Dateien herunterladen";
$net2ftp_messages["Click on a filename to quickly download one file.<br />Select multiple files and click on Download; the selected files will be downloaded in a zip archive."] = "Klicken Sie auf einen Dateinamen, um eine Datei schnell herunterzuladen.<br /> W&auml;hlen Sie mehrere Dateien aus und klicken Sie auf Herunterladen; die ausgew&auml;hlten Dateien werden als ZIP-Archiv herunter geladen.";
$net2ftp_messages["Zip files"] = "Erstellung von Zip-Archiven";
$net2ftp_messages["... and save the zip archive on the FTP server, or email it to someone."] = "... sichern Sie das ZIP-Archiv auf dem FTP-Server oder versenden Sie es einfach per E-Mail.";
$net2ftp_messages["Unzip files"] = "Auspacken von kompimierten Dateien";
$net2ftp_messages["Different formats are supported: .zip, .tar, .tgz and .gz."] = "Diese Formate werden unterst&uuml;tzt: .zip, .tar, .tgz and .gz.";
$net2ftp_messages["Install software"] = "Software installieren";
$net2ftp_messages["Choose from a list of popular applications (PHP required)."] = "W&auml;hlen Sie aus einer Liste beliebter Anwendungen (PHP wird ben&ouml;tigt).";
$net2ftp_messages["Copy, move and delete"] = "Kopieren, verschieben und l&ouml;schen";
$net2ftp_messages["Directories are handled recursively, meaning that their content (subdirectories and files) will also be copied, moved or deleted."] = "Verzeichnisse werde rekursiv behandelt, das heisst, dass ihr Inhalt (Unterverzeichnisse und Dateien) auch mit kopiert, verschoben oder gel&ouml;scht werden.";
$net2ftp_messages["Copy or move to a 2nd FTP server"] = "Kopieren oder Verschieben zu einem 2ten FTP-Server";
$net2ftp_messages["Handy to import files to your FTP server, or to export files from your FTP server to another FTP server."] = "N&uuml;tzlich um Dateien auf Ihrem FTP-Server zu importieren oder um Dateien zu einem anderen FTP-Server zu exportieren.";
$net2ftp_messages["Rename and chmod"] = "Umbenennen and &auml;ndern der Berechtigungen (chmod)";
$net2ftp_messages["Chmod handles directories recursively."] = "Chmod behandelt Verzeichnisse rekursiv.";
$net2ftp_messages["View code with syntax highlighting"] = "Betrachten sie den Quellcode mit hervorgehobener Syntax.";
$net2ftp_messages["PHP functions are linked to the documentation on php.net."] = "PHP-Funktionen sind direkt zu php.net verlinkt.";
$net2ftp_messages["Plain text editor"] = "Plain-Text-Editor";
$net2ftp_messages["Edit text right from your browser; every time you save the changes the new file is transferred to the FTP server."] = "Bearbeiten Sie Text in Ihrem Browser; immer wenn Sie &Auml;nderungen abspeichern, wird die Datei automatisch auf den FTP-Server &uuml;bertragen.";
$net2ftp_messages["HTML editors"] = "HTML editors";
$net2ftp_messages["Edit HTML a What-You-See-Is-What-You-Get (WYSIWYG) form; there are 2 different editors to choose from."] = "Bearbeiten Sie HTML mit einem What-You-See-Is-What-You-Get (WYSIWYG) Editor; sie k&ouml;nnen zwischen 2 Editoren w&auml;hlen.";
$net2ftp_messages["Code editor"] = "Code-Editor";
$net2ftp_messages["Edit HTML and PHP in an editor with syntax highlighting."] = "Bearbeiten Sie HTML and PHP in einem Editor mit Syntaxhighlighting (Hervorhebung der Syntax).";
$net2ftp_messages["Search for words or phrases"] = "Suche nach Worten und Phrasen";
$net2ftp_messages["Filter out files based on the filename, last modification time and filesize."] = "Filtern Sie Dateien nach Namen, dem Datum der letzter Bearbeitung und der Dateigr&ouml;&szlig;e.";
$net2ftp_messages["Calculate size"] = "Berechnung der Gr&ouml;&szlig;e";
$net2ftp_messages["Calculate the size of directories and files."] = "Berechnen Sie die Gr&ouml;&szlig;e von Verzeichnissen und Dateien.";

$net2ftp_messages["FTP server"] = "FTP Server";
$net2ftp_messages["Example"] = "Beispiel";
$net2ftp_messages["Port"] = "Port";
$net2ftp_messages["Protocol"] = "Protocol";
$net2ftp_messages["Username"] = "Benutzername";
$net2ftp_messages["Password"] = "Passwort";
$net2ftp_messages["Anonymous"] = "Anonym";
$net2ftp_messages["Passive mode"] = "Passiver Modus";
$net2ftp_messages["Initial directory"] = "Anfangsverzeichniss";
$net2ftp_messages["Language"] = "Sprache";
$net2ftp_messages["Skin"] = "Skin";
$net2ftp_messages["FTP mode"] = "FTP-Modus";
$net2ftp_messages["Automatic"] = "Automatisch";
$net2ftp_messages["Login"] = "Anmeldung";
$net2ftp_messages["Clear cookies"] = "Cookies l&ouml;schen";
$net2ftp_messages["Admin"] = "Admin";
$net2ftp_messages["Please enter an FTP server."] = "Bitte geben Sie einen FTP-Server ein.";
$net2ftp_messages["Please enter a username."] = "Bitte tragen Sie einen Benutzernamen ein.";
$net2ftp_messages["Please enter a password."] = "Bitte geben Sie das Passwort ein.";

} // end login


// -------------------------------------------------------------------------
// Login module
if ($net2ftp_globals["state"] == "login_small") {
// -------------------------------------------------------------------------

$net2ftp_messages["Please enter your Administrator username and password."] = "Bitte geben Sie den Administrator-Benutzernamen und Passwort ein.";
$net2ftp_messages["Please enter your username and password for FTP server <b>%1\$s</b>."] = "Bitte geben Sie den Benutzernamen und das Passwort f&uuml;r den FTP-Server <b>%1\$s</b> ein.";
$net2ftp_messages["Username"] = "Benutzername";
$net2ftp_messages["Your session has expired; please enter your password for FTP server <b>%1\$s</b> to continue."] = "Ihre Sitzung ist abgelaufen, bitte geben Sie zum Fortsetzen das Passwort f&uuml;r Ihren FTP-Server <b>%1\$s</b> ein.";
$net2ftp_messages["Your IP address has changed; please enter your password for FTP server <b>%1\$s</b> to continue."] = "Ihre IP-Adresse hat sich ge&auml;ndert, bitte geben Sie zum Fortsetzen das Passwort f&uuml;r Ihren FTP-Server <b>%1\$s</b> ein.";
$net2ftp_messages["Password"] = "Passwort";
$net2ftp_messages["Login"] = "Anmeldung";
$net2ftp_messages["Continue"] = "Fortsetzen";

} // end login_small


// -------------------------------------------------------------------------
// Logout module
if ($net2ftp_globals["state"] == "logout") {
// -------------------------------------------------------------------------

// logout.inc.php
$net2ftp_messages["Login page"] = "Startseite";

// logout.template.php
$net2ftp_messages["You have logged out from the FTP server. To log back in, <a href=\"%1\$s\" title=\"Login page (accesskey l)\" accesskey=\"l\">follow this link</a>."] = "Sie haben sie sich abgemeldet. Um sich wieder anzumelden, <a href=\"%1\$s\" title=\"Login page (accesskey l)\" accesskey=\"l\">folgen Sie diesem Link</a>.";
$net2ftp_messages["Note: other users of this computer could click on the browser's Back button and access the FTP server."] = "Achtung: Andere Benutzer dieses Computers k&ouml;nnen &uuml;ber den Zur&uuml;ck-Button Ihres Browsers auf Ihren FTP-Server zugreifen!";
$net2ftp_messages["To prevent this, you must close all browser windows."] = "Um das zu verhindern, m&uuml;ssen Sie alle Browserfenster schlie&szlig;en.";
$net2ftp_messages["Close"] = "Schlie&szlig;en";
$net2ftp_messages["Click here to close this window"] = "Klicken Sie hier, um dieses Fenster zu schlie&szlig;en";

} // end logout


// -------------------------------------------------------------------------
// New directory module
if ($net2ftp_globals["state"] == "newdir") {
// -------------------------------------------------------------------------
$net2ftp_messages["Create new directories"] = "Erstellen neuer Verzeichnisse";
$net2ftp_messages["The new directories will be created in <b>%1\$s</b>."] = "Die neuen Verzeichnisse werden erstellt in <b>%1\$s</b>.";
$net2ftp_messages["New directory name:"] = "Neuer Verzeichnisname:";
$net2ftp_messages["Directory <b>%1\$s</b> was successfully created."] = "Verzeichnis <b>%1\$s</b> wurde erfolgreich angelegt.";
$net2ftp_messages["Directory <b>%1\$s</b> could not be created."] = "Verzeichnis <b>%1\$s</b> kann nicht erstellt werden.";

} // end newdir


// -------------------------------------------------------------------------
// Raw module
if ($net2ftp_globals["state"] == "raw") {
// -------------------------------------------------------------------------

// /modules/raw/raw.inc.php
$net2ftp_messages["Send arbitrary FTP commands"] = "Senden von benutzerdefinierten FTP-Befehlen";


// /skins/[skin]/raw1.template.php
$net2ftp_messages["List of commands:"] = "Liste der Befehle:";
$net2ftp_messages["FTP server response:"] = "Antwort vom FTP-Server:";

} // end raw


// -------------------------------------------------------------------------
// Rename module
if ($net2ftp_globals["state"] == "rename") {
// -------------------------------------------------------------------------
$net2ftp_messages["Rename directories and files"] = "Umbenennen von Ordnern und Dateien";
$net2ftp_messages["Old name: "] = "Alter Name: ";
$net2ftp_messages["New name: "] = "Neuer Name: ";
$net2ftp_messages["The new name may not contain any dots. This entry was not renamed to <b>%1\$s</b>"] = "Der neue Name darf kein Punkte beinhalten. Dieser Eintrag wurde nicht umbenannt in <b>%1\$s</b>";
$net2ftp_messages["The new name may not contain any banned keywords. This entry was not renamed to <b>%1\$s</b>"] = "Der neue Name darf keine verbotenen Schl&uuml;sselworte enthalten. Dieser Eintrag wurde nicht umbenannt in <b>%1\$s</b>";
$net2ftp_messages["<b>%1\$s</b> was successfully renamed to <b>%2\$s</b>"] = "<b>%1\$s</b> wurde erfolgreich umbenannt in <b>%2\$s</b>";
$net2ftp_messages["<b>%1\$s</b> could not be renamed to <b>%2\$s</b>"] = "<b>%1\$s</b> konnte nicht in <b>%2\$s</b> umbenannt werden";

} // end rename


// -------------------------------------------------------------------------
// Unzip module
if ($net2ftp_globals["state"] == "unzip") {
// -------------------------------------------------------------------------

// /modules/unzip/unzip.inc.php
$net2ftp_messages["Unzip archives"] = "Entpacke Archive";
$net2ftp_messages["Getting archive %1\$s of %2\$s from the FTP server"] = "&Uuml;bertrage Archiv %1\$s von %2\$s vom FTP-Server";
$net2ftp_messages["Unable to get the archive <b>%1\$s</b> from the FTP server"] = "Archiv <b>%1\$s</b> konnte nicht vom FTP-Server geholt werden";

// /skins/[skin]/unzip1.template.php
$net2ftp_messages["Set all targetdirectories"] = "Setzen als Zielverzeichniss f&uuml;r alle";
$net2ftp_messages["To set a common target directory, enter that target directory in the textbox above and click on the button \"Set all targetdirectories\"."] = "Um einen gemeinsamen Zielordner anzugeben, tragen Sie das Zielverzeichnis in das obere Eingabefeld ein, und klicken auf \"Set all targetdirectories\" bzw \"Alle Zielordner setzen\".";
$net2ftp_messages["Note: the target directory must already exist before anything can be copied into it."] = "Hinweis: der Zielordner muss bereits existieren, bevor Dateien hineinkopiert werden k&ouml;nnen.";
$net2ftp_messages["Unzip archive <b>%1\$s</b> to:"] = "Unzip archive <b>%1\$s</b> to:";
$net2ftp_messages["Target directory:"] = "Ziel Verzeichniss:";
$net2ftp_messages["Use folder names (creates subdirectories automatically)"] = "Benutze Ordner Namen (Erstellt Unterordner automatisch)";

} // end unzip


// -------------------------------------------------------------------------
// Upload module
if ($net2ftp_globals["state"] == "upload") {
// -------------------------------------------------------------------------
$net2ftp_messages["Upload to directory:"] = "In Ordner hochladen:";
$net2ftp_messages["Files"] = "Dateien";
$net2ftp_messages["Archives"] = "Archives";
$net2ftp_messages["Files entered here will be transferred to the FTP server."] = "Hier angegebene Dateien werden zum FTP Server &uuml;bertragen.";
$net2ftp_messages["Archives entered here will be decompressed, and the files inside will be transferred to the FTP server."] = "Hier angegebene Archive werden dekomprimiert und die Dateien werden an den FTP Server &uuml;bermittelt.";
$net2ftp_messages["Add another"] = "Weitere hinzuf&uuml;gen";
$net2ftp_messages["Use folder names (creates subdirectories automatically)"] = "Benutze Ordner Namen (Erstellt Unterordner automatisch)";

$net2ftp_messages["Choose a directory"] = "Verzeichniss ausw&auml;hlen";
$net2ftp_messages["Please wait..."] = "Bitte warten...";
$net2ftp_messages["Uploading... please wait..."] = "Upload... Bitte warten...";
$net2ftp_messages["If the upload takes more than the allowed <b>%1\$s seconds<\/b>, you will have to try again with less/smaller files."] = "Wenn der Upload als die erlaubten <b>%1\$s<\/b> dauert, sollten Sie es noch mal mit weniger/kleineren Dateien probieren.";
$net2ftp_messages["This window will close automatically in a few seconds."] = "Dieses Fenster schlie&szlig;t sich in wenigen Sekunden automatisch.";
$net2ftp_messages["Close window now"] = "Alle Fenster schlie&szlig;en";

$net2ftp_messages["Upload files and archives"] = "Dateien und Archive hochladen";
$net2ftp_messages["Upload results"] = "Ergebnisse des Hochladens";
$net2ftp_messages["Checking files:"] = "&Uuml;berpr&uuml;fe Dateien:";
$net2ftp_messages["Transferring files to the FTP server:"] = "&Uuml;bertragen der Dateien an den FTP-Server:";
$net2ftp_messages["Decompressing archives and transferring files to the FTP server:"] = "Entpacke Archiv und &uuml;bertrage Dateien auf den FTP-Server:";
$net2ftp_messages["Upload more files and archives"] = "Weitere Dateien und Ordner hochladen";

} // end upload


// -------------------------------------------------------------------------
// Messages which are shared by upload and jupload
if ($net2ftp_globals["state"] == "upload" || $net2ftp_globals["state"] == "jupload") {
// -------------------------------------------------------------------------
$net2ftp_messages["Restrictions:"] = "Einschr&auml;nkungen:";
$net2ftp_messages["The maximum size of one file is restricted by net2ftp to <b>%1\$s</b> and by PHP to <b>%2\$s</b>"] = "Die maximale Gr&ouml;&szlig;e einer Datei ist von net2ftp auf <b>%1\$s</b> und von PHP auf <b>%2\$s</b> begrenzt";
$net2ftp_messages["The maximum execution time is <b>%1\$s seconds</b>"] = "Die maximale Zeit zum ausf&uuml;hren ist <b>%1\$s Sekunden</b>";
$net2ftp_messages["The FTP transfer mode (ASCII or BINARY) will be automatically determined, based on the filename extension"] = "Der FTP Transfer Modus (ASCII oder BINARY) wird automatisch gew&auml;hlt, basierend auf der Dateierweiterung";
$net2ftp_messages["If the destination file already exists, it will be overwritten"] = "Wenn die Zieldatei bereits existiert wird sie &uuml;berschrieben";

} // end upload or jupload


// -------------------------------------------------------------------------
// View module
if ($net2ftp_globals["state"] == "view") {
// -------------------------------------------------------------------------

// /modules/view/view.inc.php
$net2ftp_messages["View file %1\$s"] = "Datei %1\$s anzeigen";
$net2ftp_messages["View image %1\$s"] = "View image %1\$s";
$net2ftp_messages["View Macromedia ShockWave Flash movie %1\$s"] = "Macromedia ShockWave Flash Film %1\$s betrachten";
$net2ftp_messages["Image"] = "Bild";

// /skins/[skin]/view1.template.php
$net2ftp_messages["Syntax highlighting powered by <a href=\"http://luminous.asgaard.co.uk\">Luminous</a>"] = "Syntax-Hervorhebung powered by <a href=\"http://luminous.asgaard.co.uk\">Luminous</a>";
$net2ftp_messages["To save the image, right-click on it and choose 'Save picture as...'"] = "Um Bilder abzuspeichern, klicken Sie mit der rechten Maustaste darauf und w&auml;hlen 'Bild speichern unter ...' im Kontextmen&uuml;";

} // end view


// -------------------------------------------------------------------------
// Zip module
if ($net2ftp_globals["state"] == "zip") {
// -------------------------------------------------------------------------

// /modules/zip/zip.inc.php
$net2ftp_messages["Zip entries"] = "Zip Eintr&auml;ge";

// /skins/[skin]/zip1.template.php
$net2ftp_messages["Save the zip file on the FTP server as:"] = "Datei als ZIP-Datei auf dem FTP-Server speichern als:";
$net2ftp_messages["Email the zip file in attachment to:"] = "ZIP-Archiv im E-Mail-Anhang versenden an:";
$net2ftp_messages["Note that sending files is not anonymous: your IP address as well as the time of the sending will be added to the email."] = "Hinweis: Das Versenden von Dateien ist nicht anonym: Ihre IP-Addresse und die aktuelle Zeit werden an die E-Mail angeh&auml;ngt.";
$net2ftp_messages["Some additional comments to add in the email:"] = "Weitere Kommentare an die EMail anh&auml;ngen::";

$net2ftp_messages["You did not enter a filename for the zipfile. Go back and enter a filename."] = "Sie haben keinen Dateinamen f&uuml;r das ZIP-Archiv spezifiziert. Gehen Sie zur&uuml;ck und geben Sie einen Dateinamen an.";
$net2ftp_messages["The email address you have entered (%1\$s) does not seem to be valid.<br />Please enter an address in the format <b>username@domain.com</b>"] = "Die von Ihnen eingegebene EMail-Adresse (%1\$s) scheint ung&uuml;ltig zu sein.<br />Bitte geben Sie die Adresse in der Form <b>benutzername@domain.tld (.de,.com usw.)</b> ein";

} // end zip

?>