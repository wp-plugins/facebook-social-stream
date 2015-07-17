<?php

require_once('Registry.php');


class JS {
	
	private static $logger;
	private static $plugin_dir_url;
	private static $version = '1.0.0';
	
	public static function register() {
		self::$logger = new Logger(__CLASS__);
		self::$plugin_dir_url = Registry::get('plugin_base_dir_url');
		
		self::$logger->log("Registered action for JS.", __LINE__);
		
		add_action('wp_enqueue_scripts', array(__CLASS__, 'register_action'));
	}
	
	public static function enqueue() {
		self::$logger->log("Enqueued JS.", __LINE__);
		
		wp_enqueue_script( 'wp-fb-social-stream-js' );
	}
	
	public static function register_action() {
		wp_register_script('wp-fb-social-stream-js', self::$plugin_dir_url.
			'js/wp-fb-social-stream.js', array('jquery'), self::$version, true);
	}
}
