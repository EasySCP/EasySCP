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
 * Statische Klasse für Exceptions
 *
 * @category	EasySCP
 * @package		EasySCP_Exception
 * @copyright 	2010-2014 by EasySCP | http://www.easyscp.net
 * @author 		EasySCP Team
 */
class EasySCP_Exception extends Exception {

	/**
	 * EasySCP_TemplateEngine instance
	 *
	 * @var EasySCP_TemplateEngine
	 */
	protected static $EasySCP_TemplateEngine = null;

	/**
	 * Exception instance
	 *
	 * This variable contains the real exception raised.
	 *
	 * @var Exception
	 */
	protected static $exception = null;

	/**
	 * Exception message to be written
	 *
	 * @var string
	 */
	protected static $exceptionMessage = '';

	/**
	 * Legt den EasySCP_Exception_Handler als neuen Handler für Exception fest.
	 */
	static public function setHandler(){
		set_exception_handler(array('EasySCP_Exception', 'exceptionHandler' ));
	}

	/**
	 * Exception Handler
	 *
	 * This is the exception handler provided by this class.
	 * This method act like an exception handler for all uncaught exceptions.
	 *
	 * @param Exception $exception Exception object
	 *
	 * @return void
	 */
	static public function exceptionHandler(Exception $exception) {

		if(error_reporting() == 0 || !ini_get('display_errors')) {
			self::$exception = $exception;
		} else {
			self::$exception = $exception;
		}

		EasySCP_Exception::createMessage();

		die();
	}

	/**
	 * Writes the exception message to the client browser
	 *
	 * @return void
	 */
	protected static function write() {
		if(!is_null(self::$EasySCP_TemplateEngine)) {
			self::$EasySCP_TemplateEngine->display('exception_message.tpl');
		} else {
			echo self::$exceptionMessage;
		}
	}

	/**
	 * Erzeugt die Nachricht für die Ausgabe
	 *
	 * todo Fertig schreiben des Trace Moduls
	 *
	 * @return void
	 */
	protected static function createMessage() {

		// Always write the real exception message if we are the admin
		if(isset($_SESSION) && ((isset($_SESSION['logged_from']) && $_SESSION['logged_from'] == 'admin') || isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin')){

			self::$exceptionMessage = self::$exception->getMessage();

			if(EasyConfig::$cfg->DEBUG == '1'){
				self::$exceptionMessage .= '<br />';
				foreach(array_reverse(self::$exception->getTrace(), true) AS $trace){
					self::$exceptionMessage .= '<br />';
					self::$exceptionMessage .= 'File: ' . $trace['file'] . '<br />';
					self::$exceptionMessage .= 'Line: ' . $trace['line'] . '<br />';
					self::$exceptionMessage .= 'Function: ' . $trace['function'] . '<br />';
					/*
					if (count($trace['args']) != 0){
						var_dump($trace['args']);
					}
					*/
					// $this->_message .= 'Arg: ' . $trace['args']['0'] . '<br />';
				}
			}

		} else {

			// var_dump(self::$exception);
			// var_dump($exceptionHandler->getException());
			// var_dump($exceptionHandler->getException()->getTrace());

			// An exception for production exists ? If it's not case, use the real exception raised
			self::$exceptionMessage = 'An error occured! Please, contact your administrator!';

			if(EasyConfig::$cfg->DEBUG == '1'){
				self::$exceptionMessage = self::$exception->getMessage();
				self::$exceptionMessage .= '<br />';
				foreach(array_reverse(self::$exception->getTrace(), true) AS $trace){
					self::$exceptionMessage .= '<br />';
					self::$exceptionMessage .= 'File: ' . $trace['file'] . '<br />';
					self::$exceptionMessage .= 'Line: ' . $trace['line'] . '<br />';
					self::$exceptionMessage .= 'Function: ' . $trace['function'] . '<br />';
					/*
					if (count($trace['args']) != 0){
						var_dump($trace['args']);
					}
					*/
					// $this->_message .= 'Arg: ' . $trace['args']['0'] . '<br />';
				}
			}
			// $this->_message .= var_dump($exceptionHandler->getException()->getTrace());
		}

		self::prepareTemplate();

		// Finally, we write the output
		self::write();
	}

	/**
	 * Prepares the template
	 *
	 * @return void
	 */
	protected static function prepareTemplate() {

		self::$EasySCP_TemplateEngine = EasySCP_TemplateEngine::getInstance();

		// check if i18n support is available
		if (function_exists('tr')) {
			self::$EasySCP_TemplateEngine->assign(
				array(
					'TR_PAGE_TITLE'		=> tr('EasySCP Error'),
					'THEME_CHARSET'		=> tr('encoding'),
					'MSG_TYPE'			=> 'error',
					'MESSAGE'			=> self::$exceptionMessage
				)
			);
		} else {
			self::$EasySCP_TemplateEngine->assign(
				array(
					'TR_PAGE_TITLE'		=> 'EasySCP Error',
					'THEME_CHARSET'		=> 'UTF-8',
					'MSG_TYPE'			=> 'error',
					'MESSAGE'			=> self::$exceptionMessage
				)
			);
		}
	} // end prepareTemplate()
}
?>