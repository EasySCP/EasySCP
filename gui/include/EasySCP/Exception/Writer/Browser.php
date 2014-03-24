<?php
/**
 * ispCP ω (OMEGA) a Virtual Hosting Control System
 *
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
 * The Original Code is "ispCP - ISP Control Panel".
 *
 * The Initial Developer of the Original Code is ispCP Team.
 * Portions created by Initial Developer are Copyright (C) 2006-2011 by
 * isp Control Panel. All Rights Reserved.
 *
 * @category	ispCP
 * @package		EasySCP_Exception
 * @subpackage	Writer
 * @copyright	2006-2011 by ispCP | http://isp-control.net
 * @author		Laurent Declercq <laurent.declercq@ispcp.net>
 * @version		SVN: $Id: Browser.php 3871 2011-06-25 09:27:27Z benedikt $
 * @link		http://isp-control.net ispCP Home Site
 * @license		http://www.mozilla.org/MPL/ MPL 1.1
 */

/**
 * @see EasySCP_Exception_Writer
 */
require_once  INCLUDEPATH . '/EasySCP/Exception/Writer.php';

/**
 * Browser writer class
 *
 * This writer writes an exception messages to the client browser. This writer
 * acts also as a formatter that will use a specific template for the message
 * formatting. If no template path is given, or if the template file is not
 * reachable, a string that represent the message is write to the client
 * browser.
 *
 * The given template should be a template file that can be treated by a
 * template object.
 *
 * <b>Note:</b> Will be improved later.
 *
 * @category	ispCP
 * @package		EasySCP_Exception
 * @subpackage	Writer
 * @author		ispCP Team
 * @since		1.0.7
 * @version		1.0.4
 * @todo		Display more information like trace on debug mode.
 */
class EasySCP_Exception_Writer_Browser extends EasySCP_Exception_Writer {

	/**
	 * EasySCP_TemplateEngine instance
	 *
	 * @var EasySCP_TemplateEngine
	 */
	protected $EasySCP_TemplateEngine = null;

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Writes the exception message to the client browser
	 *
	 * @return void
	 * @todo Add inline template for rescue
	 */
	protected function _write() {
		if(!is_null($this->EasySCP_TemplateEngine)) {
			$this->EasySCP_TemplateEngine->display('exception_message.tpl');
		} else {
			echo $this->_message;
		}
	}

	/**
	 * This methods is called from the subject (i.e. when an event occur)
	 *
	 * @param EasySCP_Exception_Handler $exceptionHandler EasySCP_Exception_Handler
	 * @return void
	 */
	public function update(SplSubject $exceptionHandler) {

		// Always write the real exception message if we are the admin
		if(isset($_SESSION) && ((isset($_SESSION['logged_from']) &&
			$_SESSION['logged_from'] == 'admin') ||
				isset($_SESSION['user_type']) &&
					$_SESSION['user_type'] == 'admin')) {

			$this->_message = $exceptionHandler->getException()->getMessage();

		} else {

			$productionException = $exceptionHandler->getProductionException();

			// An exception for production exists ? If it's not case, use the
			// real exception raised
			$this->_message = ($productionException !== false)
				? $productionException->getMessage()
				: $exceptionHandler->getException()->getMessage();
		}

		$this->_prepareTemplate();

		// Finally, we write the output
		$this->_write();
	}

	/**
	 * Prepares the template
	 *
	 * @return void
	 */
	protected function _prepareTemplate() {

		$this->EasySCP_TemplateEngine = EasySCP_TemplateEngine::getInstance();


		if(EasySCP_Registry::isRegistered('backButtonDestination')) {
			$backButtonDest = EasySCP_Registry::get('backButtonDestination');
		} else {
			$backButtonDest = 'javascript:history.go(-1)';
		}

		$this->EasySCP_TemplateEngine->assign(
			array(
				'BACKBUTTONDESTINATION' => $backButtonDest,
				'MESSAGE' => $this->_message,
				'MSG_TYPE' => 'error'
			)
		);

		// i18n support is available ?
		if (function_exists('tr')) {
			$this->EasySCP_TemplateEngine->assign(
				array(
					'TR_PAGE_TITLE'		=> tr('EasySCP Error'),
					'THEME_CHARSET'		=> tr('encoding'),
					'TR_BACK'			=> tr('Back'),
					'TR_ERROR_MESSAGE'	=> tr('Error Message'),

				)
			);
		} else {
			$this->EasySCP_TemplateEngine->assign(
				array(
					'TR_PAGE_TITLE'		=> 'EasySCP Error',
					'THEME_CHARSET'		=> 'UTF-8',
					'TR_BACK'			=> 'Back',
					'TR_ERROR_MESSAGE'	=> 'Error Message',
				)
			);
		}

	} // end prepareTemplate()
}
?>