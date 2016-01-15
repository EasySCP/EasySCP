<?php
/**
 * EasySCP a Virtual Hosting Control Panel
 * Copyright (C) 2010-2016 by Easy Server Control Panel - http://www.easyscp.net
 *
 * This work is licensed under the Creative Commons Attribution-NoDerivs 3.0 Unported License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nd/3.0/.
 *
 * @link 		http://www.easyscp.net
 * @author 		EasySCP Team
 */

/**
 * EasySCP Daemon Config Tools functions
 */

class DaemonConfigMail {

	/**
	 * @return mixed
	 */
	public static function SaveMTAConfig(){
		System_Daemon::debug('Starting "DaemonConfigMail::SaveMTAConfig" subprocess.');

		/**
		 * Postfix main.cf
		 */

		// Backup current main.cf if exists
		if (file_exists(DaemonConfig::$cfg->{'POSTFIX_CONF_FILE'})){
			exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'POSTFIX_CONF_FILE'} .' '.DaemonConfig::$cfg->{'CONF_DIR'}.'/postfix/backup/main.cf'.'_'.date("Y_m_d_H_i_s"), $result, $error);
		}

		// Loading the template from /etc/easyscp/postfix/, Building the file

		$tpl_param = array(
			'MTA_HOSTNAME'			=> idn_to_ascii(DaemonConfig::$cfg->{'SERVER_HOSTNAME'}),
			'MTA_LOCAL_DOMAIN'		=> idn_to_ascii(DaemonConfig::$cfg->{'SERVER_HOSTNAME'}).'.local',
			'MTA_VERSION'			=> DaemonConfig::$cfg->{'Version'},
			'MTA_TRANSPORT'			=> DaemonConfig::$cfg->{'MTA_TRANSPORT'},
			'MTA_LOCAL_MAIL_DIR'	=> DaemonConfig::$cfg->{'MTA_LOCAL_MAIL_DIR'},
			'MTA_LOCAL_ALIAS_HASH'	=> DaemonConfig::$cfg->{'MTA_LOCAL_ALIAS_HASH'},
			'MTA_VIRTUAL_MAIL_DIR'	=> DaemonConfig::$cfg->{'MTA_VIRTUAL_MAIL_DIR'},
			'MTA_VIRTUAL_DMN'		=> DaemonConfig::$cfg->{'MTA_VIRTUAL_DMN'},
			'MTA_VIRTUAL_MAILBOX'	=> DaemonConfig::$cfg->{'MTA_VIRTUAL_MAILBOX'},
			'MTA_VIRTUAL_ALIAS'		=> DaemonConfig::$cfg->{'MTA_VIRTUAL_ALIAS'},
			'MTA_MAILBOX_MIN_UID'	=> DaemonConfig::$cfg->{'MTA_MAILBOX_MIN_UID'},
			'MTA_MAILBOX_UID'		=> DaemonConfig::$cfg->{'MTA_MAILBOX_UID'},
			'MTA_MAILBOX_GID'		=> DaemonConfig::$cfg->{'MTA_MAILBOX_GID'},
			'PORT_POSTGREY'			=> DaemonConfig::$cfg->{'PORT_POSTGREY'},
			'MTA_SSL'				=> (DaemonConfig::$cfg->{'MTA_SSL_STATUS'} == '1') ? true : false
		);

		$tpl = DaemonCommon::getTemplate($tpl_param);
		$config = $tpl->fetch('postfix/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'} . '/main.cf');
		$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/postfix/working/main.cf';
		$tpl = NULL;
		unset($tpl);

		// Storing the new file in working directory
		if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
			return 'Error: Failed to write '.$confFile;
		}

		// Installing the new file in production directory
		exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/postfix/working/main.cf '.DaemonConfig::$cfg->{'POSTFIX_CONF_FILE'}, $result, $error);


		/**
		 * Postfix master.cf
		 */

		// Backup current master.cf if exists
		if (file_exists(DaemonConfig::$cfg->{'POSTFIX_MASTER_CONF_FILE'})){
			exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'POSTFIX_MASTER_CONF_FILE'} .' '.DaemonConfig::$cfg->{'CONF_DIR'}.'/postfix/backup/master.cf'.'_'.date("Y_m_d_H_i_s"), $result, $error);
		}

		// Storing the new file in working directory
		exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/postfix/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'} . '/master.cf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/postfix/working/master.cf', $result, $error);
		DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/postfix/working/master.cf', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);

		// Installing the new file in production directory
		exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/postfix/working/master.cf '.DaemonConfig::$cfg->{'POSTFIX_MASTER_CONF_FILE'}, $result, $error);

		// Loading the template from /etc/easyscp/postfix/, Building the file

		$MTA_DB_HOST = idn_to_ascii(DaemonConfig::$cfg->{'DATABASE_HOST'});
		$MTA_DB_USER = 'mail_admin';
		$MTA_DB_PASS = DaemonCommon::generatePassword(9);
		$MTA_CONFIG_FILES = array('domains', 'email2email', 'forwardings', 'mailboxes', 'transports');

		$tpl_param = array(
			'MTA_DB_USER'	=> $MTA_DB_USER,
			'MTA_DB_PASS'	=> $MTA_DB_PASS
		);

		foreach($MTA_CONFIG_FILES AS $FILE){
			$tpl = DaemonCommon::getTemplate($tpl_param);
			$config = $tpl->fetch('postfix/parts/mysql-virtual_'.$FILE.'.cf');
			$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/postfix/working/mysql-virtual_'.$FILE.'.cf';
			$tpl = NULL;
			unset($tpl);

			// Storing the new file in working directory
			if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
				return 'Error: Failed to write '.$confFile;
			}

			// Installing the new file in production directory
			exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/postfix/working/mysql-virtual_'.$FILE.'.cf '.DaemonConfig::$cfg->{'MTA_VIRTUAL_CONF_DIR'}.'/mysql-virtual_'.$FILE.'.cf', $result, $error);
		}

		# Adding the mail_admin user
		$sql_param = array(
			':MTA_DB_HOST'	=> $MTA_DB_HOST,
			':MTA_DB_USER'	=> $MTA_DB_USER,
			':MTA_DB_PASS'	=> $MTA_DB_PASS
		);
		$sql_query = "
			GRANT SELECT, INSERT, UPDATE, DELETE ON mail.* TO :MTA_DB_USER@:MTA_DB_HOST IDENTIFIED BY :MTA_DB_PASS;
			FLUSH PRIVILEGES;
		";

		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();

		/**
		 * Dovecot Konfiguration
		 */

		$configPath = 'dovecot/parts/' . DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'};

		switch(DaemonConfig::$cfg->{'DistName'} . '_' . DaemonConfig::$cfg->{'DistVersion'}){

			default:
				/**
				 * 10-auth.conf
				 */
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/' . $configPath . '/10-auth.conf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/dovecot/working/10-auth.conf', $result, $error);
				DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/10-auth.conf', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
				exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/10-auth.conf '.DaemonConfig::$cfg->{'DOVECOT_CONF_DIR'}.'/conf.d/10-auth.conf', $result, $error);

				/**
				 * 10-logging.conf
				 */
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -f ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/' . $configPath . '/10-logging.conf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/dovecot/working/10-logging.conf', $result, $error);
				DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/10-logging.conf', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
				exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/10-logging.conf '.DaemonConfig::$cfg->{'DOVECOT_CONF_DIR'}.'/conf.d/10-logging.conf', $result, $error);

				/**
				 * 10-mail.conf
				 */
				$tpl_param = array(
					'MTA_VIRTUAL_MAIL_DIR'	=> DaemonConfig::$cfg->{'MTA_VIRTUAL_MAIL_DIR'},
					'MTA_MAILBOX_UID'		=> DaemonConfig::$cfg->{'MTA_MAILBOX_UID'}
				);

				$tpl = DaemonCommon::getTemplate($tpl_param);
				$config = $tpl->fetch($configPath . '/10-mail.conf');
				$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/10-mail.conf';
				$tpl = NULL;
				unset($tpl);

				if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
					return 'Error: Failed to write '.$confFile;
				}

				exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/10-mail.conf '.DaemonConfig::$cfg->{'DOVECOT_CONF_DIR'}.'/conf.d/10-mail.conf', $result, $error);

				/**
				 * 10-master.conf
				 */
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/' . $configPath . '/10-master.conf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/dovecot/working/10-master.conf', $result, $error);
				DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/10-master.conf', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
				exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/10-master.conf '.DaemonConfig::$cfg->{'DOVECOT_CONF_DIR'}.'/conf.d/10-master.conf', $result, $error);

				/**
				 * 10-ssl.conf
				 */
				if(file_exists(DaemonConfig::$cfg->{'CONF_DIR'} . '/' . $configPath . '/10-ssl.conf')){
					exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/' . $configPath . '/10-ssl.conf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/dovecot/working/10-ssl.conf', $result, $error);
					DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/10-ssl.conf', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
					exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/10-ssl.conf '.DaemonConfig::$cfg->{'DOVECOT_CONF_DIR'}.'/conf.d/10-ssl.conf', $result, $error);
				}

				/**
				 * 15-lda.conf
				 */
				$tpl_param = array(
					'DEFAULT_ADMIN_ADDRESS'	=> DaemonConfig::$cfg->{'DEFAULT_ADMIN_ADDRESS'}
				);

				$tpl = DaemonCommon::getTemplate($tpl_param);
				$config = $tpl->fetch($configPath . '/15-lda.conf');
				$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/15-lda.conf';
				$tpl = NULL;
				unset($tpl);

				if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
					return 'Error: Failed to write '.$confFile;
				}

				exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/15-lda.conf '.DaemonConfig::$cfg->{'DOVECOT_CONF_DIR'}.'/conf.d/15-lda.conf', $result, $error);

				/**
				 * 20-lmtp.conf
				 */

				if(file_exists(DaemonConfig::$cfg->{'CONF_DIR'} . '/' . $configPath . '/20-lmtp.conf')){
					exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/' . $configPath . '/20-lmtp.conf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/dovecot/working/20-lmtp.conf', $result, $error);
					DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/20-lmtp.conf', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
					exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/20-lmtp.conf '.DaemonConfig::$cfg->{'DOVECOT_CONF_DIR'}.'/conf.d/20-lmtp.conf', $result, $error);
				}

				/**
				 * 20-managesieve.conf
				 */

				if(file_exists(DaemonConfig::$cfg->{'CONF_DIR'} . '/' . $configPath . '/20-managesieve.conf')){
					exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/' . $configPath . '/20-managesieve.conf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/dovecot/working/20-managesieve.conf', $result, $error);
					DaemonCommon::systemSetFilePermissions(DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/20-managesieve.conf', DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644);
					exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/20-managesieve.conf '.DaemonConfig::$cfg->{'DOVECOT_CONF_DIR'}.'/conf.d/20-managesieve.conf', $result, $error);
				}

				/**
				 * 90-sieve.conf
				 */

				if(file_exists(DaemonConfig::$cfg->{'CONF_DIR'} . '/' . $configPath . '/90-sieve.conf')){

					$tpl_param = array(
						'MTA_VIRTUAL_MAIL_DIR'	=> DaemonConfig::$cfg->{'MTA_VIRTUAL_MAIL_DIR'}
					);

					$tpl = DaemonCommon::getTemplate($tpl_param);
					$config = $tpl->fetch($configPath . '/90-sieve.conf');
					$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/90-sieve.conf';
					$tpl = NULL;
					unset($tpl);

					if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
						return 'Error: Failed to write '.$confFile;
					}

					exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/90-sieve.conf '.DaemonConfig::$cfg->{'DOVECOT_CONF_DIR'}.'/conf.d/90-sieve.conf', $result, $error);

				}

				/**
				 * auth-sql.conf.ext
				 */

				$tpl_param = array(
					'MTA_MAILBOX_UID'		=> DaemonConfig::$cfg->{'MTA_MAILBOX_UID'},
					'MTA_MAILBOX_GID'		=> DaemonConfig::$cfg->{'MTA_MAILBOX_GID'},
					'MTA_VIRTUAL_MAIL_DIR'	=> DaemonConfig::$cfg->{'MTA_VIRTUAL_MAIL_DIR'}
				);

				$tpl = DaemonCommon::getTemplate($tpl_param);
				$config = $tpl->fetch($configPath . '/auth-sql.conf.ext');
				$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/auth-sql.conf.ext';
				$tpl = NULL;
				unset($tpl);

				if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
					return 'Error: Failed to write '.$confFile;
				}

				exec(DaemonConfig::$cmd->{'CMD_CP'}.' -pf '.DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/auth-sql.conf.ext '.DaemonConfig::$cfg->{'DOVECOT_CONF_DIR'}.'/conf.d/auth-sql.conf.ext', $result, $error);

				/**
				 * dovecot-sql.conf.ext
				 */

				$tpl_param = array(
					'MTA_DB_HOST'	=> $MTA_DB_HOST,
					'MTA_DB_USER'	=> $MTA_DB_USER,
					'MTA_DB_PASS'	=> $MTA_DB_PASS
				);

				$tpl = DaemonCommon::getTemplate($tpl_param);
				$config = $tpl->fetch($configPath . '/dovecot-sql.conf.ext');
				$confFile = DaemonConfig::$cfg->{'CONF_DIR'}.'/dovecot/working/dovecot-sql.conf.ext';
				$tpl = NULL;
				unset($tpl);

				// Storing the new file in working directory
				if (!DaemonCommon::systemWriteContentToFile($confFile, $config, DaemonConfig::$cfg->{'ROOT_USER'}, DaemonConfig::$cfg->{'ROOT_GROUP'}, 0644 )){
					return 'Error: Failed to write '.$confFile;
				}

				// Installing the new file in production directory
				exec(DaemonConfig::$cmd->{'CMD_CP'} . ' -pf ' . DaemonConfig::$cfg->{'CONF_DIR'} . '/dovecot/working/dovecot-sql.conf.ext ' . DaemonConfig::$cfg->{'DOVECOT_CONF_DIR'} . '/dovecot-sql.conf.ext', $result, $error);
		}

		unset($configPath);

		// Sonstige Anpassungen

		switch(DaemonConfig::$cfg->{'DistName'}){
			case 'CentOS':
				// Wird auf CentOS benötigt da ansonsten bei unzustellbaren Mails der Fehler
				// "status=bounced (can't create user output file. Command output: procmail: Couldn't create "/var/spool/mail/nobody" )" im log erscheint.
				// "procmail" hat hier wohl nicht die richtigen Rechte.
				if (file_exists('/usr/bin/procmail')){
					exec("chmod g+s /usr/bin/procmail", $result, $error);
				}
				break;
			default:
		}

		System_Daemon::debug('Finished "DaemonConfigMail::SaveMTAConfig" subprocess.');

		return true;
	}
}
?>