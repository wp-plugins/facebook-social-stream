<?php

require_once('FBSS_JS.php');
require_once('FBSS_Logger.php');
require_once('FBSS_Registry.php');
require_once('FBSS_SocialStream.php');


class FBSS_Shortcodes {
	
	private static $logger;
	
	public static function register() {
		self::$logger = new FBSS_Logger(__CLASS__);
		self::$logger->log("Registered shortcode 'fb_social_stream'.", __LINE__);
		
		add_shortcode('fb_social_stream', array( __CLASS__, 'fb_social_stream_sc' ));
	}
	
	public static function fb_social_stream_sc($atts, $content, $name) {
		$limit = FBSS_Registry::get('stream_msg_limit');
		
		$social = new FBSS_SocialStream;
		$stream_data = $social->get($limit);
		
		/* enqueue script only if shortcode included */
		FBSS_JS::enqueue();
		
		return $stream_data;
	}
}
