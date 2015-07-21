<?php 

require_once('FBSS_Logger.php');

class FBSS_Update {

	private $logger;
	
	public function __construct() {
		$this->logger = new FBSS_Logger(__CLASS__);
	}
	
	public function update($prev_version, $cur_version) {
		$this->logger->log("Update plugin from '$prev_version' -> ".
				"'$cur_version'.", __LINE__);
		
		/* option changes in release 1.3.3 */
		$options = array(
			'wp_fb_social_stream_plugin_version' 			=> 'fbss_plugin_version',
			'wp_fb_social_stream_db_version'				=> 'fbss_db_version',
			'wp_fb_social_stream_setting_fb_page_name'		=> 'fbss_setting_fb_page_name',
			'wp_fb_social_stream_setting_fb_access_token'	=> 'fbss_setting_fb_access_token',
			'wp_fb_social_stream_settings_update_interval'	=> 'fbss_setting_update_interval',
			'wp_fb_social_stream_settings_last_data_update'	=> 'fbss_setting_last_data_update',
			'wp_fb_social_stream_settings_msg_limit'		=> 'fbss_setting_msg_limit'
		);
		
		foreach ($options as $old_key => $new_key) {
			$this->changeOptionKey($old_key, $new_key);
		}
	}
	
	
	private function changeOptionKey($old_key, $new_key) {
		if ($val = get_option($old_key)) {
			$this->logger->log("Found value '$val' for old option '$old_key'.", 
					__LINE__);
			if (get_option($new_key)) {
				$this->logger->log("New option '$new_key' alread exists, ".
						"deleting old one.", __LINE__);
				delete_option($old_key);
			} else {
				$this->logger->log("New option '$new_key' does not exist, ".
						"Creating it with value '$val'.", __LINE__);
				add_option($new_key, $val);
				delete_option($old_key);
			}
		}
	}
}
