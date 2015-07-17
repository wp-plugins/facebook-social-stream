<?php

require_once('Registry.php');


class CSS {
	
	private static $logger;
	private static $plugin_dir_url;
	private static $version = '1.0.0';
	
	public static function register() {
		self::$logger = new Logger(__CLASS__);
		self::$plugin_dir_url = Registry::get('plugin_base_dir_url');
		
		self::$logger->log("Registered action 'add_css_library'.", __LINE__);
		
		add_action('wp_enqueue_scripts', array(__CLASS__, 'add_css_library'));
	}
	
	public static function add_css_library() {
		# TODO identify custom styles here once templating is possible
		
		wp_enqueue_style('font-awesome', self::$plugin_dir_url.
			'templates/default/css/font-awesome-4.3.0/css/font-awesome.min.css', 
			array(), self::$version, 'all');
		
		wp_enqueue_style('wp-fb-social-stream', self::$plugin_dir_url.
			'templates/default/css/style.css', array('font-awesome'), 
			self::$version, 'all');
	}
}