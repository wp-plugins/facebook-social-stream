<?php

require_once('FBSS_Logger.php');
require_once('FBSS_SocialStream.php');
require_once('FBSS_Template.php');


class FBSS_Admin {
	
	private $logger;
	private $options;
	private $menu_slug = 'wp-fb-social-stream-settings';
	private $template;
	private $template_config;
	private $template_name = 'default'; // TODO set dynamically if more templates available
	
	public function __construct() {
		$this->logger = new FBSS_Logger(__CLASS__);
		
		$this->template = new FBSS_Template;
		$this->template_config = $this->template->getConfiguration();
		
		add_action('admin_menu', array($this, 'addOptionsPage'));
		add_action('admin_init', array($this, 'initPageSettings'));
		
		// load external sources
		add_action('admin_enqueue_scripts', array($this, 'addCSSLibraries'));
		add_action('admin_enqueue_scripts', array($this, 'addJSLibraries'));
	}
	
	public function addOptionsPage() {
		$this->logger->log("Add administration menu.", __LINE__);
		
		$page_title = 'WordPress Facebook Social Stream';
		$menu_title = 'Facebook Social Stream';
		$capability = 'manage_options'; # https://codex.wordpress.org/Roles_and_Capabilities
		$menu_slug = 'wp-fb-social-stream-settings';
		$function = array($this, 'createAdminPage');
		
		$hook = add_options_page( $page_title, $menu_title, $capability, 
					$this->menu_slug, $function);
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
					<h3>'.__('Facebook options', 'wp-fb-social-stream') .'</h3>
					<table class="form-table wp-fb-social-stream-customization-fb-options">
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
						<tbody>
					</table>
		';
		
		submit_button();
		
		echo '
					<hr />

					<h3>'.__('Stream options', 'wp-fb-social-stream') .'</h3>
					<table class="form-table wp-fb-social-stream-options">
						<tbody>	
		        			<tr>
								<th scope="row">
									<label for="wp_fb_social_stream_settings_update_interval">'.__('Update interval', 'wp-fb-social-stream').'</label>
								</th>
						        <td>
						        	<input type="text" name="wp_fb_social_stream_settings_update_interval" value="'.esc_attr( get_option('wp_fb_social_stream_settings_update_interval', 30) ).'" class="regular-text" /> ('.__('minutes', 'wp-fb-social-stream').')
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
		
		echo $this->getTemplateOptionsHTML();
		
		submit_button();
		
		echo '
				</form>
			</div>
		';
	}
	
	public function initPageSettings() {
		$this->logger->log("initPageSettings.", __LINE__);
		
		register_setting('wp-fb-social-stream-settings-group', 
							'wp_fb_social_stream_setting_fb_page_name',
							array($this, 'validateFBPageName'));
		register_setting('wp-fb-social-stream-settings-group', 
							'wp_fb_social_stream_setting_fb_access_token',
							array($this, 'validateFBAccessToken'));
		register_setting('wp-fb-social-stream-settings-group', 
							'wp_fb_social_stream_settings_update_interval', 
							array($this, 'validateUpdateInterval'));
		register_setting('wp-fb-social-stream-settings-group', 
							'wp_fb_social_stream_settings_msg_limit', 
							array($this, 'validateMessageLimit'));
		
		// register template config settings
		$template = $this->template;
		$template_config_keys = $template->getDBOptionsConfigurationCSSKeys($with_hidden = false);
		foreach ($template_config_keys as $key) {
			register_setting('wp-fb-social-stream-settings-group', $key);
		}
	}
	
	public function validateFBPageName($input) {
		$this->logger->log("Validation FBPageName '$input'.", __LINE__);
		$fb_page_name = $input;
		
		if (preg_match('/(https?:\/\/)?(www\.)?facebook\.com\/(.*)/i', $fb_page_name, $match)) {
			$fb_page_name = $match[3];
		}
		
		$stored_page_name = get_option('wp_fb_social_stream_setting_fb_page_name');
		if ($fb_page_name != $stored_page_name) {
			$this->logger->log("FBPageName changed from '$stored_page_name' to ".
					"'$fb_page_name'. Set update social stream flag.",
					__LINE__);
			update_option('fbss_admin_update_social_stream', 1);
		}
		
		return $fb_page_name;
	}
	
	public function validateFBAccessToken($input) {
		$this->logger->log("Validation FBAccessToken '$input'.", __LINE__);
	
		$stored_token = get_option('wp_fb_social_stream_setting_fb_access_token');
		if ($input != $stored_token) {
			$this->logger->log("FBAccessToken changed from '$stored_token' to ".
					"'$input'. Set update social stream flag.",
					__LINE__);
			update_option('fbss_admin_update_social_stream', 1);
		}
	
		return $input;
	}
	
	public function validateUpdateInterval($input) {
		$this->logger->log("Validation update interval '$input'.", __LINE__);
		return intval($input);
	}
	
	public function validateMessageLimit($input) {
		$this->logger->log("Validation message limit '$input'.", __LINE__);
		
		$limit = intval($input);
		$stored_limit = get_option('wp_fb_social_stream_settings_msg_limit');
		if ($limit > $stored_limit) {
			$this->logger->log("New message limit '$limit' is bigger than ".
					"stored '$stored_limit'. Set update social stream flag.",
					__LINE__);
			update_option('fbss_admin_update_social_stream', 1);
		}
		
		return $limit;
	}
	
	public function onSettingsSave() {
		$this->logger->log("onSettingsSave.", __LINE__);
		
		if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
			$this->logger->log("Saving social stream data.", __LINE__);
			
			// store stream only if page-name or access-token changed or max-messages increased
			if (get_option('fbss_admin_update_social_stream')) {
				update_option('fbss_admin_update_social_stream', 0);
				
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
	
	public function addCSSLibraries($hook) {
		if ($hook == 'settings_page_'.$this->menu_slug) {
			$this->logger->log("addCSSLibraries.", __LINE__);
			
			$plugin_dir_url = FBSS_Registry::get('plugin_base_dir_url');
			
			wp_enqueue_style('wp-fb-social-stream-colorpicker',
				$plugin_dir_url.'tools/colorpicker/css/colorpicker.css',
				array(), '', 'all');
			
			wp_enqueue_style('wp-fb-social-stream-admin',
				$plugin_dir_url.'css/admin.css',
				array(), '', 'all');
		}
	}
	
	public function addJSLibraries($hook) {
		if ($hook == 'settings_page_'.$this->menu_slug) {
			$this->logger->log("addJSLibraries hook '$hook'.", __LINE__);
			
			$plugin_dir_url = FBSS_Registry::get('plugin_base_dir_url');
			
			wp_enqueue_script('wp-fb-social-stream-colorpicker',
				$plugin_dir_url.'tools/colorpicker/js/colorpicker.js',
				array('jquery'), '', 'all');
			
			wp_enqueue_script('wp-fb-social-stream-colorpicker-admin',
				$plugin_dir_url.'js/admin/colorpicker-fb-social-stream.js',
				array('wp-fb-social-stream-colorpicker'), '', 'all');
		}
	}
	
	private function getTemplateOptionsHTML() {
		$this->logger->log("getTemplateOptionsHTML.", __LINE__);
		
		$html = '
				<hr />
				
				<h3>'.__('Stream customization', 'wp-fb-social-stream') .'</h3>
				<p>'.__('Optional style configuration of your social stream', 'wp-fb-social-stream') .'</p>
				<table class="form-table wp-fb-social-stream-customization">
					<tbody>
		';
		
		// CSS settings
		$css_configs = $this->template->getConfigurationCSS();
		foreach ($css_configs as $config) {
			$html .= '
	        			<tr>
							<th scope="row" colspan="2">
								'.esc_html( __($config['name'], 'wp-fb-social-stream') ).'
								<p class="description">'.esc_html( __($config['desc'], 'wp-fb-social-stream') ).'</p>
							</th>
						</tr>
			';
			
			$config_index = $config['config']['index'];
			
			foreach ($config['config']['configs'] as $sub_config) {
				// check config-actions
				if (isset($sub_config['actions'])) {
					if (isset($sub_config['actions']['hide'])) {
						if ($sub_config['actions']['hide']) {
							// hidden configuration, nothing to do here
							continue;
						}
					}
				}
				
				// create configuration input-field
				$input_name = $this->template->getDBOptionsKey($config_index, 
								$sub_config['config_id']);
				
				$html .= '
        			<tr>
						<td>
							'.esc_html( __($sub_config['desc'], 'wp-fb-social-stream') ).'
							<p class="description">'.esc_html( $sub_config['selector'] ).'</p>
						</td>
						<td>
				';
				
				if ($sub_config['type'] == 'hexcode') {
					$css_val = get_option($input_name);
					$input_style = '';
					if ($css_val) {
						$input_style = 'border: 2px solid #'.$css_val.';';
					}
					$html .= '
				        	#<input type="text" name="'.$input_name.'" value="'.esc_attr( $css_val ).'" class="hexcode" size="6" maxlength="6" style="'.$input_style.'" />
					';
				} else {
					$html .= '
							<input type="text" name="'.$input_name.'" value="'.esc_attr( get_option($input_name) ).'" class="regular-text" />
					';
				}

				$html .= '
						</td>
					</tr>
				';					
			}
		}
		
		$html .= '
					<tbody>
				</table>
		';
		
		return $html;
	}
}
