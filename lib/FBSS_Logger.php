<?php

require_once('FBSS_Registry.php');


class FBSS_Logger {
	
	private $plugin_name;
	private $class;
	
	public function __construct($class) {
		$this->plugin_name = FBSS_Registry::get('plugin_name');
		$this->class = $class;
	}
	
	public function log($message, $line, $force=0) {
		if (WP_DEBUG === true || $force) {
			$plugin = $this->plugin_name;
			$class = $this->class;
				
			if (is_array($message) || is_object($message)) {
				error_log("'$plugin' ($class) line $line: ".print_r($message, true));
			} else {
				error_log("'$plugin' ($class) line $line: ".$message);
			}
		}
	}
}
