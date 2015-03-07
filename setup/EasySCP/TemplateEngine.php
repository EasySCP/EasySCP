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

/**
 * Class TemplateEngine is the new EasySCP template engine.
 *
 * @category	EasySCP
 * @package		EasySCP_TemplateEngine
 * @copyright 	2010-2012 by EasySCP | http://www.easyscp.net
 * @author 		EasySCP Team
 */
class EasySCP_TemplateEngine {

	protected static $_instance = null;
	private $template_engine;

	/**
	 * Constructor
	 */
	protected function __construct() {
		require(INCLUDEPATH . '/../easyscp/gui/include/Smarty/Smarty.class.php');
		$this->template_engine = new Smarty();
		$this->template_engine->caching = false;
		$this->template_engine->force_compile = true;

		$this->set_globals();
	}

	/**
	 * Get an EasySCP_TemplateEngine instance
	 *
	 * Returns an {@link EasySCP_TemplateEngine} instance, only creating it if it
	 * doesn't already exist.
	 *
	 * @return EasySCP_TemplateEngine An EasySCP_TemplateEngine instance
	 */
	public static function getInstance() {

		if(is_null(self::$_instance)) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	/**
	 * Append data to the template for loop parsing
	 *
	 * @param String $nsp_name
	 * @param String $nsp_data
	 */

	public function append($nsp_name, $nsp_data = '') {
		if (gettype($nsp_name) == "array") {
			$this->template_engine->append($nsp_name);
		} else {
			$this->template_engine->append($nsp_name, $nsp_data);
		}
	}

		/**
	 * Assign data to the template for parsing
	 *
	 * @param String $nsp_name
	 * @param String $nsp_data
	 */

	public function assign($nsp_name, $nsp_data = '') {
		if (gettype($nsp_name) == "array") {
			$this->template_engine->assign($nsp_name);
		} else {
			$this->template_engine->assign($nsp_name, $nsp_data);
		}
	}

	/**
	 * Parse data and displays the template $template
	 *
	 * @param String $template
	 */

	public function display($template) {
		// un-comment the following line to show the debug console
		// $this->template_engine->debugging = true;
		$this->template_engine->display($template);
	}

	/**
	 * Returns the EasySCP_TemplateEngine template dir
	 *
	 * @return String template_dir the current EasySCP_TemplateEngine template dir
	 */
	public function get_template_dir() {
		return $this->template_engine->getTemplateDir('EasySCP');
	}

	/**
	 * Sets the EasySCP_TemplateEngine template dir
	 *
	 * @param String $dir The new EasySCP_TemplateEngine template dir
	 */
	public function set_template_dir($dir) {
		$this->template_engine->setTemplateDir($dir);
	}

	/**
	 * Sets global variables for using in all templates
	 */
	private function set_globals() {
		$this->template_engine->setTemplateDir(array('EasySCP' => INCLUDEPATH . '/theme'));
		$this->template_engine->setCompileDir(INCLUDEPATH . '/theme/templates_c');
		$this->assign(
			array(
				'THEME_CHARSET'		=> 'UTF-8',
				'THEME_COLOR_PATH'	=> './theme',
				'THEME_SCRIPT_PATH'	=> './theme/scripts'
			)
		);
	}
}
?>