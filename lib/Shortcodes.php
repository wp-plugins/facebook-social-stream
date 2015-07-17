<?php

require_once('JS.php');
require_once('Registry.php');
require_once('SocialStream.php');


class Shortcodes {
	
	private static $logger;
	
	public static function register() {
		self::$logger = new Logger(__CLASS__);
		self::$logger->log("Registered shortcode 'fb_social_stream'.", __LINE__);
		
		add_shortcode('fb_social_stream', array( __CLASS__, 'fb_social_stream_sc' ));
	}
	
	public static function fb_social_stream_sc($atts, $content, $name) {
		$limit = Registry::get('stream_msg_limit');
		
		$social = new SocialStream;
		$stream_data = $social->get($limit);
		
		/* enqueue script only if shortcode included */
		JS::enqueue();
		
		return $stream_data;
	}
}
