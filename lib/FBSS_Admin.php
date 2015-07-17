<?php

require_once('FBSS_Logger.php');
require_once('FBSS_SocialStream.php');


class FBSS_Admin {
	
	private $logger;
	private $options;
	
	public function __construct() {
		$this->logger = new FBSS_Logger(__CLASS__);
		
		add_action('admin_menu', array($this, 'addOptionsPage'));
		add_action('admin_init', array($this, 'initPageSettings'));
	}
	
	public function addOptionsPage() {
		$this->logger->log("Add administration menu.", __LINE__);
		
		$page_title = 'WordPress Facebook Social Stream';
		$menu_title = 'Facebook Social Stream';
		$capability = 'manage_options'; # https://codex.wordpress.org/Roles_and_Capabilities
		$menu_slug = 'wp-fb-social-stream-settings';
		$function = array($this, 'createAdminPage');
		
		$hook = add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
		add_action('load-'.$hook, array($this, 'onSettingsSave'));
	}
	
	public function createAdminPage() {
		$this->logger->log("createAdminPage.", __LINE__);
		
		$this->options = get_option('wp-fb-social-stream-option-name');
		
		echo '
			<div class="wrap">
				<h2>'.__('Facebook Social Stream Settings', 'wp-fb-social-stream') .'</h2>
				<form method="post" action="options.php">
		';
		
		settings_fields('wp-fb-social-stream-settings-group');
		do_settings_sections('wp-fb-social-stream-settings-group');
		
		echo '
					<table class="form-table">
						<tbody>
				        	<tr>
				        		<th scope="row">
				        			<label for="wp_fb_social_stream_setting_fb_page_name">'.__('Facebook Page Name', 'wp-fb-social-stream').'</label>
				        		</th>
				        		<td>
				        			<input type="text" name="wp_fb_social_stream_setting_fb_page_name" value="'.esc_attr( get_option('wp_fb_social_stream_setting_fb_page_name') ).'" class="regular-text" />
				        			<p class="description">'.__('You can idintify the page-name as follows: https://www.facebook.com/{<strong>page-name</strong>}', 'wp-fb-social-stream').'</p>
				        		</td>
				        	</tr>
							<tr>
								<th scope="row">
									<label for="wp_fb_social_stream_setting_fb_access_token">'.__('Facebook Access Token', 'wp-fb-social-stream').'</label>
								</th>
						        <td>
						        	<input type="text" name="wp_fb_social_stream_setting_fb_access_token" value="'.esc_attr( get_option('wp_fb_social_stream_setting_fb_access_token') ).'" class="regular-text" /> ('.__('optional', 'wp-fb-social-stream').')
						        	<p class="description">'.__('Either use your own one with specific rights or leave it empty to use the plugin-default', 'wp-fb-social-stream').'</p>
					        	</td>
							</tr>
		        			<tr>
								<th scope="row">
									<label for="wp_fb_social_stream_settings_update_interval">'.__('Update interval', 'wp-fb-social-stream').'</label>
								</th>
						        <td>
						        	<input type="text" name="wp_fb_social_stream_settings_update_interval" value="'.esc_attr( get_option('wp_fb_social_stream_settings_update_interval', 1800) ).'" class="regular-text" /> ('.__('minutes', 'wp-fb-social-stream').')
						        	<p class="description">'.__('Default value is 30 minutes', 'wp-fb-social-stream').'</p>
					        	</td>
							</tr>
		        			<tr>
								<th scope="row">
									<label for="wp_fb_social_stream_settings_msg_limit">'.__('Max messages', 'wp-fb-social-stream').'</label>
								</th>
						        <td>
						        	<input type="text" name="wp_fb_social_stream_settings_msg_limit" value="'.esc_attr( get_option('wp_fb_social_stream_settings_msg_limit', 20) ).'" class="regular-text" />
				        			<p class="description">'.__('Default value is 20 messages', 'wp-fb-social-stream').'</p>
					        	</td>
							</tr>
						<tbody>
					</table>
		';
		
		submit_button();
		
		echo '
				</form>
			</div>
		';
	}
	
	public function initPageSettings() {
		$this->logger->log("initPageSettings.", __LINE__);
		
		register_setting('wp-fb-social-stream-settings-group', 
							'wp_fb_social_stream_setting_fb_page_name');
		register_setting('wp-fb-social-stream-settings-group', 
							'wp_fb_social_stream_setting_fb_access_token');
		register_setting('wp-fb-social-stream-settings-group', 
							'wp_fb_social_stream_settings_update_interval', 
							array($this, 'intval'));
		register_setting('wp-fb-social-stream-settings-group', 
							'wp_fb_social_stream_settings_msg_limit', 
							array($this, 'intval'));
	}
	
	public function intval($input) {
		$this->logger->log("Validation intval '$input'.", __LINE__);
		return intval($input);
	}
	
	public function onSettingsSave() {
		$this->logger->log("onSettingsSave.", __LINE__);
		
		if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
			$this->logger->log("Saving social stream data.", __LINE__);
			
			$social_stream = new FBSS_SocialStream;
			$social_stream->drop();
			$result = $social_stream->store();
			
			if ($result === true) {
				$this->logger->log("Saved data.", __LINE__);
				
				add_settings_error('wp_fb_social_stream_setting_store', 
					esc_attr('social_stream_updated'), 
					__('Retrieved and updated Facebook data.', 'wp-fb-social-stream'),
					'updated');
			} else {
				$this->logger->log("Save data ERROR!", __LINE__);
				
				add_settings_error('wp_fb_social_stream_setting_store',
					esc_attr('social_stream_updated'),
					__('Could not retrieve Facebook data. Please check your settings.', 'wp-fb-social-stream'), 
					'error');
			}
		}
	}
}
