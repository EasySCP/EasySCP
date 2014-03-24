<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2014 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * EasySCP Control IPT functions
 */

class ControlIPT {

	public static $logLocation;

	/**
	 * To monitor more ports, edit SERVICES variable add your own ports
	 * (ftp, proxy, http, etc.)
	 *
	 * HTTP(S): 80 443
	 * POP3(S): 110 995
	 * IMAP4(S)): 143 993
	 * MAIL(S): 25 465 587
	 */
	private static $SERVICES_IN = array('80', '443', '110', '995', '143', '993', '25', '465', '587');

	/**
	 * To monitor more outgoing ports, edit SERVICES_OUT variable add your own ports
	 * (mail, etc.)
	 *
	 * MAIL(S): 25 465 587
	 */
	private  static $SERVICES_OUT= array('25', '465', '587');

	/**
	 * IPT einrichten
	 *
	 * Mit dieser Funktion werden die nötigen IPT chains dem System hinzugefügt
	 *
	 */
	public static function Add(){
		// add_rules
		exec(DaemonConfig::$cmd->CMD_IPTABLES.' -N EASYSCP_INPUT  2>> '.self::$logLocation);
		exec(DaemonConfig::$cmd->CMD_IPTABLES.' -N EASYSCP_OUTPUT 2>> '.self::$logLocation);

		// All traffic should jump through EASYSCP tables before anything else
		exec(DaemonConfig::$cmd->CMD_IPTABLES.' -I INPUT  -j EASYSCP_INPUT  2>> '.self::$logLocation);
		exec(DaemonConfig::$cmd->CMD_IPTABLES.' -I OUTPUT -j EASYSCP_OUTPUT 2>> '.self::$logLocation);

		// Services from matrix basically receiving data
		foreach(self::$SERVICES_IN AS $Service){
			exec(DaemonConfig::$cmd->CMD_IPTABLES.' -I EASYSCP_INPUT  -p tcp --dport "'.$Service.'" 2>> '.self::$logLocation);
			exec(DaemonConfig::$cmd->CMD_IPTABLES.' -I EASYSCP_OUTPUT -p tcp --sport "'.$Service.'" 2>> '.self::$logLocation);
		}

		// Services from matrix basically sending data
		foreach(self::$SERVICES_OUT AS $Service){
			exec(DaemonConfig::$cmd->CMD_IPTABLES.' -I EASYSCP_INPUT  -p tcp --sport "'.$Service.'" 2>> '.self::$logLocation);
			exec(DaemonConfig::$cmd->CMD_IPTABLES.' -I EASYSCP_OUTPUT -p tcp --dport "'.$Service.'" 2>> '.self::$logLocation);
		}

		// Explicit return once done
		exec(DaemonConfig::$cmd->CMD_IPTABLES.' -A EASYSCP_INPUT  -j RETURN');
		exec(DaemonConfig::$cmd->CMD_IPTABLES.' -A EASYSCP_OUTPUT -j RETURN');
	}

	/**
	 * IPT entfernen
	 *
	 * Mit dieser Funktion werden die nötigen IPT chains wieder vom System entfernt
	 *
	 */
	public static function Remove(){
		exec('export COLUMNS=120;'.DaemonConfig::$cmd->CMD_IPTABLES.' -nvxL EASYSCP_INPUT 1>'.DaemonConfig::$cfg->SRV_TRAFF_LOG_DIR.'/easyscp-iptables-input_'.time().'.log');
		// exec(DaemonConfig::$cmd->CMD_IPTABLES.' -Z EASYSCP_INPUT');

		exec('export COLUMNS=120;'.DaemonConfig::$cmd->CMD_IPTABLES.' -nvxL EASYSCP_OUTPUT 1>'.DaemonConfig::$cfg->SRV_TRAFF_LOG_DIR.'/easyscp-iptables-output_'.time().'.log');
		// exec(DaemonConfig::$cmd->CMD_IPTABLES.' -Z EASYSCP_OUTPUT');

		// exec(DaemonConfig::$cmd->CMD_IPTABLES.' -D INPUT  -j EASYSCP_INPUT  2>> '.self::$logLocation);
		// exec(DaemonConfig::$cmd->CMD_IPTABLES.' -D OUTPUT -j EASYSCP_OUTPUT 2>> '.self::$logLocation);

		// exec(DaemonConfig::$cmd->CMD_IPTABLES.' -F EASYSCP_INPUT  2>> '.self::$logLocation);
		// exec(DaemonConfig::$cmd->CMD_IPTABLES.' -F EASYSCP_OUTPUT 2>> '.self::$logLocation);

		// exec(DaemonConfig::$cmd->CMD_IPTABLES.' -X EASYSCP_INPUT  2>> '.self::$logLocation);
		// exec(DaemonConfig::$cmd->CMD_IPTABLES.' -X EASYSCP_OUTPUT 2>> '.self::$logLocation);
	}
}
?>