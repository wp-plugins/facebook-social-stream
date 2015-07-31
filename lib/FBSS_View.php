<?php

require_once('FBSS_Logger.php');
require_once('FBSS_Registry.php');


class FBSS_View {

	private $logger;
	private $view_dir;

	public function __construct() {
		$this->logger = new FBSS_Logger(__CLASS__);
		$this->view_dir = FBSS_Registry::get('plugin_base_dir').'/view';
	}
	
	public function setViewDir($view_dir) {
		$this->view_dir = $view_dir;
	}

	public function render($template, $view_data = array()) {
		$this->logger->log("Render template '$template'.", __LINE__);

		if (preg_match('/\.\.\//', $template)) {
			throw new Exception(
					sprintf(__("Path restriction error with template '%s'!",
							'wp-fb-social-stream'), $template) );
		}
		if(!is_array($view_data)) {
			# type hinting only available since PHP 5 :( so using is_array()
			# http://php.net/manual/de/language.oop5.typehinting.php
			throw new Exception('view_data-param has to be an array!');
		}

		ob_start();
		include($this->view_dir.'/'.$template.'.tpl.php');
		$tpl_data = ob_get_clean();

		return $tpl_data;
	}

	public function render_e($template, $view_data = array()) {
		echo $this->render($template, $view_data);
	}
}
