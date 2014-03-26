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

class DaemonMail {
	/**
	 * @param $Input
	 * @return bool
	 */
	public static function Start($Input) {
		System_Daemon::debug('Starting "DaemonMail::Start" subprocess.');

		$sql_param = array(
			':domain_id' => $Input
		);
		$sql_query = "
			SELECT
				d.domain_name,
				m.*
			FROM
				domain d,
				mail_users m
			WHERE
				m.domain_id = d.domain_id
			AND
				d.domain_id = :domain_id
			AND
				m.status <> 'ok';
		";

		// Einzelne Schreibweise
		DB::prepare($sql_query);
		foreach (DB::execute($sql_param) as $row) {
			switch($row['status']){
				case 'change':
					self::DaemonMailChange($row);
					break;
				case 'delete':
					self::DaemonMailDelete($row);
					break;
				case 'ok':
					System_Daemon::info('Nichts zu tun');
					break;
				case 'restore':
					break;
				case 'add':
					self::DaemonMailAdd($row);
					break;
				case 'disable':
					self::DaemonMailDisable($row);
					break;
				case 'enable':
					self::DaemonMailEnable($row);
					break;
				default:
					System_Daemon::info('Unbekannter Mail Status');
			}
		}

		System_Daemon::debug('Finished "DaemonMail::Start" subprocess.');

		return true;
	}

	private static function DaemonMailAdd($row) {
		$mail_ok = true;
		self::DaemonMailAddDomain(substr(strstr($row['mail_addr'], '@'), 1));
		if ( $row['mail_type'] == 'normal_mail' || $row['mail_type'] == 'alias_mail' || $row['mail_type'] == 'subdom_mail'){
			self::DaemonMailAddNormalMail($row);
		}

		if ( $row['mail_type'] == 'normal_forward' || $row['mail_type'] == 'alias_forward' || $row['mail_type'] == 'subdom_forward'){
			self::DaemonMailAddNormalForward($row);
		}

		if ( $mail_ok ){
			$sql_param = array(
				':mail_id'=> $row['mail_id']
			);
			$sql_query = "
				UPDATE
					mail_users
				SET
					status = 'ok'
				WHERE
					mail_id = :mail_id
			";
			DB::prepare($sql_query);
			DB::execute($sql_param)->closeCursor();

			exec(DaemonConfig::$cmd->SRV_MTA . ' reload');

			//mail($row['mail_addr'], 'Welcome to EasySCP!', "\nA new EasySCP Mail account has been created for you.\n\nBest wishes with EasySCP!\nThe EasySCP Team.");
		}
	}

	public static function DaemonMailAddDomain($domain_name) {
		System_Daemon::debug('Starting "DaemonMail::DaemonMailAddDomain = '.$domain_name.'" subprocess.');

		$mail_ok = true;
		if ( $mail_ok ){
			$sql_param = array(
				':domain' => $domain_name
			);
			$sql_query = "
				INSERT INTO
					mail.domains (domain)
				VALUES
					(:domain)
				ON DUPLICATE KEY UPDATE
					domain = :domain
			";
			DB::prepare($sql_query);
			DB::execute($sql_param)->closeCursor();
		}

		System_Daemon::debug('Finished "DaemonMail::DaemonMailAddDomain" subprocess.');

	}

	private static function DaemonMailAddNormalMail($row) {
		$sql_param = array(
				':email'=> $row['mail_addr'],
				':pass' => DB::decrypt_data($row['mail_pass'])
		);
		$sql_query = "
			INSERT INTO
				mail.users
					(email, password)
			VALUES
				(:email, ENCRYPT(:pass))
			ON DUPLICATE KEY UPDATE
				email = :email, password = ENCRYPT(:pass);

		";
		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();
	}

	private static function DaemonMailAddNormalForward($row) {
		if(strpos($row['mail_forward'], ",") !== false){
			$row['mail_forward'] = str_replace(",", " ", $row['mail_forward']);
		}
		$sql_param = array(
			// ':source'		=> $row['mail_acc'] . '@' . $row['domain_name'],
				':source'		=> $row['mail_addr'],
				':destination'	=> $row['mail_forward']
		);
		$sql_query = "
			INSERT INTO
				mail.forwardings
					(source, destination)
			VALUES
				(:source, :destination)
			ON DUPLICATE KEY UPDATE
				source = :source, destination = :destination;
		";
		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();
	}

	private static function DaemonMailChange($row) {
		$mail_ok = true;
		if ( $row['mail_type'] == 'normal_mail' || $row['mail_type'] == 'alias_mail' || $row['mail_type'] == 'subdom_mail'){
			self::DaemonMailAddNormalMail($row);
		}

		if ( $row['mail_type'] == 'normal_forward' || $row['mail_type'] == 'alias_forward' || $row['mail_type'] == 'subdom_forward'){
			self::DaemonMailAddNormalForward($row);
		}

		if ( $mail_ok ){
			$sql_param = array(
				':mail_id'=> $row['mail_id']
			);
			$sql_query = "
				UPDATE
					mail_users
				SET
					status = 'ok'
				WHERE
					mail_id = :mail_id
			";
			DB::prepare($sql_query);
			DB::execute($sql_param)->closeCursor();
		}
	}

	private static function DaemonMailDelete($row) {
		System_Daemon::debug('Starting "DaemonMailDelete" subprocess.');

		$mail_ok = true;
		if ( $row['mail_type'] == 'normal_mail' || $row['mail_type'] == 'alias_mail' || $row['mail_type'] == 'subdom_mail'){
			self::DaemonMailDelete_normal_mail($row);
		}

		if ( $row['mail_type'] == 'normal_forward' || $row['mail_type'] == 'alias_forward' || $row['mail_type'] == 'subdom_forward'){
			self::DaemonMailDelete_normal_forward($row);
		}

		if ( $mail_ok ){
			$mail_dir = substr($row['mail_addr'], strpos($row['mail_addr'], '@') + 1);
			System_Daemon::info('Delete Mail User Directory ' . DaemonConfig::$cfg->MTA_VIRTUAL_MAIL_DIR . '/' . $mail_dir . '/' . $row['mail_acc']);
			if (file_exists(DaemonConfig::$cfg->MTA_VIRTUAL_MAIL_DIR . '/' . $mail_dir . '/' . $row['mail_acc'])){
				exec('rm -R ' . DaemonConfig::$cfg->MTA_VIRTUAL_MAIL_DIR . '/' . $mail_dir . '/' . $row['mail_acc']);
			}


			$sql_param = array(
				':mail_id'=> $row['mail_id']
			);

			$sql_query = "
				DELETE FROM
					mail_users
				WHERE
					mail_id = :mail_id
			";
			DB::prepare($sql_query);
			DB::execute($sql_param)->closeCursor();
		}

		System_Daemon::debug('Finished "DaemonMailDelete" subprocess.');
	}

	public static function DaemonMailDeleteDomain($domain_name) {
		System_Daemon::debug('Starting "DaemonMail::DaemonMailDeleteDomain = '.$domain_name.'" subprocess.');

		$mail_ok = true;
		if ( $mail_ok ){
			$sql_param = array(
				':domain' => $domain_name
			);
			$sql_query = "
				DELETE FROM
					mail.domains
				WHERE
					domain = :domain
			";
			DB::prepare($sql_query);
			DB::execute($sql_param)->closeCursor();
		}

		System_Daemon::debug('Finished "DaemonMail::DaemonMailDeleteDomain" subprocess.');
	}

	private static function DaemonMailDisable($row) {
		System_Daemon::debug('Starting "DaemonMailDisable" subprocess.');

		$mail_ok = true;

		if ( $mail_ok ){
			$sql_param = array(
					':mail_id'=> $row['mail_id']
			);

			$sql_query = "
				UPDATE
					mail_users
				SET
					status = 'ok'
				WHERE
					mail_id = :mail_id
			";
			DB::prepare($sql_query);
			DB::execute($sql_param)->closeCursor();
		}

		System_Daemon::debug('Finished "DaemonMailDisable" subprocess.');
	}

	private static function DaemonMailDelete_normal_mail($row) {
		$sql_param = array(
			':email'=> $row['mail_addr']
		);
		$sql_query = "
			DELETE FROM
				mail.users
			WHERE
				email = :email
		";
		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();
	}

	private static function DaemonMailDelete_normal_forward($row) {
		if(strpos($row['mail_forward'], ",") !== false){
			$row['mail_forward'] = str_replace(",", " ", $row['mail_forward']);
		}
		$sql_param = array(
			// ':source'		=> $row['mail_acc'] . '@' . $row['domain_name'],
			':source'		=> $row['mail_addr'],
			':destination'	=> $row['mail_forward']
		);
		$sql_query = "
			DELETE FROM
				mail.forwardings
			WHERE
				source = :source
			AND
				destination = :destination
		";
		DB::prepare($sql_query);
		DB::execute($sql_param)->closeCursor();
	}

	private static function DaemonMailEnable($row) {
		System_Daemon::debug('Starting "DaemonMailDisable" subprocess.');

		$mail_ok = true;

		if ( $mail_ok ){
			$sql_param = array(
					':mail_id'=> $row['mail_id']
			);

			$sql_query = "
				UPDATE
					mail_users
				SET
					status = 'ok'
				WHERE
					mail_id = :mail_id
			";
			DB::prepare($sql_query);
			DB::execute($sql_param)->closeCursor();
		}

		System_Daemon::debug('Finished "DaemonMailDisable" subprocess.');
	}
}
?>