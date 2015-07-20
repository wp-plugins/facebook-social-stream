<?php
/*
Plugin Name: Facebook Social Stream
Plugin URI: http://angileri.de/blog/wordpress-plugin-facebook-social-stream/
Description: Reads facebook page data and provides social stream
Author: Daniele Angileri <daniele@angileri.det>
Author URI: http://angileri.de
Version: 1.3.0
Text Domain: wp-fb-social-stream
License: GPLv2

Copyright (C) 2015  Daniele Angileri <daniele@angileri.det>

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

require_once('lib/FBSS_Admin.php');
require_once('lib/FBSS_CSS.php');
require_once('lib/FBSS_DB.php');
require_once('lib/FBSS_JS.php');
require_once('lib/FBSS_Logger.php');
require_once('lib/FBSS_Registry.php');
require_once('lib/FBSS_Shortcodes.php');
require_once('lib/FBSS_SocialStream.php');
require_once('lib/FBSS_Template.php');


class WP_FB_SocialStream {
	
	private static $plugin_name = 'WP FB Social Stream';
	private static $plugin_version = '1.3.0';
	private static $plugin_version_key = 'wp_fb_social_stream_plugin_version';
	
	private static $logger;
	private static $db;
	private static $registry;
	
	private static $stream_msg_limit;
	
	
	public static function register() {
		$fb_page_name = get_option('wp_fb_social_stream_setting_fb_page_name');
		$fb_access_token = get_option('wp_fb_social_stream_setting_fb_access_token');
		self::$stream_msg_limit = get_option('wp_fb_social_stream_settings_msg_limit', 20);
		
		// init registry with plugin data first
		FBSS_Registry::set('plugin_name', self::$plugin_name);
		FBSS_Registry::set('plugin_base_dir_url', plugin_dir_url(__FILE__));
		FBSS_Registry::set('plugin_base_dir', plugin_dir_path(__FILE__));
		FBSS_Registry::set('fb_page_name', $fb_page_name);
		FBSS_Registry::set('fb_access_token', $fb_access_token);
		FBSS_Registry::set('stream_msg_limit', self::$stream_msg_limit);
		
		self::$logger = new FBSS_Logger(__CLASS__);
		self::$db = new FBSS_DB;
		
		self::$logger->log("Register plugin.", __LINE__);
		
		/* hooks */
		register_activation_hook(__FILE__, array(__CLASS__, 'onActivation'));
		register_deactivation_hook(__FILE__, array(__CLASS__, 'onDeactivation'));
		register_uninstall_hook(__FILE__, array(__CLASS__, 'onUninstall'));
		
		/* translations */
		add_action('plugins_loaded', array(__CLASS__, 'initTextDomain'));
		
		/* init plugin template */
		self::initTemplate();
		
		if( is_admin() ) {
			/* administration submenu */
			$admin = new FBSS_Admin;
			
			/* settings link */
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 
							array(__CLASS__, 'setPluginSettingsLink') );
		}
		
		/* check plugin version */
		self::checkPluginVersion();
		
		/* register shortcodes */
		FBSS_Shortcodes::register();
		
		/* register ajax handler to update social stream */
		add_action('wp_ajax_wp_fb_social_stream_update', 
						array(__CLASS__, 'ajaxUpdateSocialStream'));
		add_action('wp_ajax_nopriv_wp_fb_social_stream_update', 
						array(__CLASS__, 'ajaxUpdateSocialStream'));
		
		/* register javascript libraries */
		FBSS_JS::register();
		
		/* register stylesheets */
		FBSS_CSS::register();
	}
	
	public static function onActivation() {
		self::$logger->log("Plugin activation.", __LINE__);
		self::$db->create();
	}
	
	public static function onDeactivation() {
		self::$logger->log("Plugin deactivation.", __LINE__);
	}
	
	public static function onUninstall() {
		self::$logger->log("Plugin uninstallation.", __LINE__);
		self::$db->drop();
		delete_option(self::$plugin_version_key);
		delete_option('wp_fb_social_stream_setting_fb_page_name');
		delete_option('wp_fb_social_stream_setting_fb_access_token');
		delete_option('wp_fb_social_stream_settings_update_interval');
		delete_option('wp_fb_social_stream_settings_last_data_update');
		delete_option('wp_fb_social_stream_settings_msg_limit');
		
		# delete template-customization data
		$all_options = wp_load_alloptions();
		foreach ($all_options as $key => $val) {
			if (preg_match('/^fbss_tplt_cfg_/', $key)) {
				delete_option($key);
			}
		}
		
		delete_option('fbss_admin_update_social_stream');
	}
	
	public static function setPluginSettingsLink($links) {
		$mylinks = array(
 			'<a href="'. 
			admin_url('options-general.php?page=wp-fb-social-stream-settings'). 
			'">'.__('Settings', 'wp-fb-social-stream').'</a>'
 		);
		
		return array_merge($links, $mylinks);
	}
	
	public static function ajaxUpdateSocialStream() {
		self::$logger->log("ajaxUpdateSocialStream.", __LINE__);
		
		$update_interval = get_option('wp_fb_social_stream_settings_update_interval',
							30);
		
		# check last update and decide whether to run store or not
		$last_update_time = get_option('wp_fb_social_stream_settings_last_data_update');
		if (!$last_update_time) {
			$last_update_time = time();
			add_option('wp_fb_social_stream_settings_last_data_update', 
						$last_update_time);
		}
		
		$diff_time = time() - $last_update_time;
		$diff_time_mins = $diff_time / 60;
		
		if ($diff_time_mins > $update_interval) {
			self::$logger->log("Update interval of '$update_interval' mins ".
					"reached ($diff_time_mins). Updating.", __LINE__);
			
			$social_stream = new FBSS_SocialStream;
			$social_stream->store();
			
			update_option('wp_fb_social_stream_settings_last_data_update', time());
			
			# print updated social stream as ajax return value
			echo $social_stream->get(self::$stream_msg_limit);
		} else {
			self::$logger->log("Update interval of '$update_interval' mins ".
					"not reached yet ($diff_time_mins). Nothing to do.", __LINE__);
			
			echo '';
		}
		
		wp_die(); // as described in codex: this is required to terminate immediately
	}
	
	public static function initTextDomain() {
		load_plugin_textdomain( 'wp-fb-social-stream', false, 
			dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	

	private static function checkPluginVersion() {
		self::$logger->log("Check plugin version.", __LINE__);
		
		$plugin_version_key = self::$plugin_version_key;
		
		$prev_version = get_option($plugin_version_key);
		$cur_version = self::$plugin_version;
		
		if ($prev_version) {
			if ($prev_version == $cur_version) {
				self::$logger->log("Plugin version '$cur_version' is ".
						"up-to-date", __LINE__);
			} else {
				self::$logger->log("Plugin version '$prev_version' is ".
						"outdated.",	__LINE__);
				self::updateTasks($prev_version);
				update_option($plugin_version_key, $cur_version);
			}
		} else {
			add_option($plugin_version_key, $cur_version);
			self::$logger->log("Plugin version '$cur_version' registered.",
					__LINE__);
		}
	}
	
	private static function updateTasks($prev_version) {
		$cur_version = self::$plugin_version;
		
		self::$logger->log("Checking update tasks '$prev_version' -> ".
				"'$cur_version'.", __LINE__);
		
		# implement update tasks here as soon as there exists some 
	}
	
	private static function initTemplate() {
		self::$logger->log("Init template configuration.", __LINE__);
		$template = new FBSS_Template;
	}
}

/* register the plugin */
WP_FB_SocialStream::register();
