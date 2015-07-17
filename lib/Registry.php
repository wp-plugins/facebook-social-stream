<?php
class Registry {
	
	protected static $instance = null;
	protected $values = array();
	
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new Registry;
		}
		return self::$instance;
	}
	
	protected function __construct() {}
	
	private function __clone() {}	// disable cloning
	
	
	public static function set($key, $value) {
		$instance = self::getInstance();
		$instance->values[$key] = $value;
	}
	
	public static function get($key) {
		$instance = self::getInstance();
	
		if (!isset($instance->values[$key])) {
			throw new Exception("Entry with key '$index' does not exist!");
		}
	
		return $instance->values[$key];
	}
}
