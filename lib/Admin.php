<?php

require_once('Logger.php');
require_once('Registry.php');
require_once('SocialStream.php');


class Admin {
	
	private $logger;
	private $options;
	
	public function __construct() {
		$this->logger = new Logger(__CLASS__);
		
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
		
		echo <<<HTML
			<div class="wrap">
				<h2>Facebook Social Stream Settings</h2>
				<form method="post" action="options.php">
HTML;
		
		settings_fields('wp-fb-social-stream-settings-group');
		do_settings_sections('wp-fb-social-stream-settings-group');
		
		$val_fb_page_name = esc_attr( get_option('wp_fb_social_stream_setting_fb_page_name') );
		$val_fb_access_token = esc_attr( get_option('wp_fb_social_stream_setting_fb_access_token') );
		$val_stream_update_interval = esc_attr( get_option('wp_fb_social_stream_settings_update_interval', 1800) );
		$val_stream_message_limit = esc_attr( get_option('wp_fb_social_stream_settings_msg_limit', 20) );
		
		echo <<<HTML
					<table class="form-table">
						<tbody>
				        	<tr>
				        		<th scope="row">
				        			<label for="wp_fb_social_stream_setting_fb_page_name">Facebook Page Name</label>
				        		</th>
				        		<td>
				        			<input type="text" name="wp_fb_social_stream_setting_fb_page_name" value="$val_fb_page_name" class="regular-text" />
				        			<p class="description">You can idintify the page-name as follows: https://www.facebook.com/{<strong>page-name</strong>}</p>
				        		</td>
				        	</tr>
							<tr>
								<th scope="row">
									<label for="wp_fb_social_stream_setting_fb_access_token">Facebook Access Token</label>
								</th>
						        <td>
						        	<input type="text" name="wp_fb_social_stream_setting_fb_access_token" value="$val_fb_access_token" class="regular-text" /> (optional)
						        	<p class="description">Either use your own one with specific rights or leave it empty to use the plugin-default</p>
					        	</td>
							</tr>
		        			<tr>
								<th scope="row">
									<label for="wp_fb_social_stream_settings_update_interval">Update interval</label>
								</th>
						        <td>
						        	<input type="text" name="wp_fb_social_stream_settings_update_interval" value="$val_stream_update_interval" class="regular-text" /> (minutes)
						        	<p class="description">Default value is 30 minutes</p>
					        	</td>
							</tr>
		        			<tr>
								<th scope="row">
									<label for="wp_fb_social_stream_settings_msg_limit">Max messages</label>
								</th>
						        <td>
						        	<input type="text" name="wp_fb_social_stream_settings_msg_limit" value="$val_stream_message_limit" class="regular-text" />
				        			<p class="description">Default value is 20 messages</p>
					        	</td>
							</tr>
						<tbody>
					</table>
HTML;
		submit_button();
		
		echo <<<HTML
				</form>
			</div>
HTML;
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
			
			$social_stream = new SocialStream;
			$result = $social_stream->store();
			
			if ($result === true) {
				$this->logger->log("Saved data.", __LINE__);
				
				add_settings_error('wp_fb_social_stream_setting_store', 
					esc_attr('social_stream_updated'), 
					__('Retrieved and updated Facebook data.'), 'updated');
			} else {
				$this->logger->log("Save data ERROR!", __LINE__);
				
				add_settings_error('wp_fb_social_stream_setting_store',
				esc_attr('social_stream_updated'),
				__('Could not retrieve Facebook data. Please check your settings.'), 
				'error');
			}
		}
	}
}
