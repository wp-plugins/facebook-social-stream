<?php

require_once('FBSS_ExtensionFactory.php');
require_once('FBSS_ExtensionManager.php');
require_once('FBSS_Logger.php');
require_once('FBSS_SocialStream.php');
require_once('FBSS_Template.php');
require_once('FBSS_TemplateStringUtils.php');
require_once('FBSS_View.php');

class FBSS_Admin {

	private $logger;
	private $options;
	private $menu_slug = 'facebook-social-stream-settings';
	private $template;
	private $template_config;
	private $template_name = 'default'; // TODO set dynamically if more templates available
	private $view;
	private $active_tab;

	public function __construct() {
		$this->logger = new FBSS_Logger(__CLASS__);

		$this->template = new FBSS_Template;
		$this->template_config = $this->template->getConfiguration();

		$this->view = new FBSS_View;
		
		$this->active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'settings';

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
		$function = array($this, 'createAdminPage');

		$menu_icon = '';
		if ( version_compare(get_bloginfo('version'), '3.8', '>') ) {
			$menu_icon = 'dashicons-facebook'; # introduced version 3.8+
			$menu_title = 'Social Stream';
		}
		
		$num_updates = $this->getNumExtensionUpdates();
		if ($num_updates) {
			$menu_title .= $this->view->render('admin/menu-update-plugins-count',
					array('update_count' =>  $num_updates));
		}
		
		$hook = add_menu_page( $page_title, $menu_title, $capability,
				$this->menu_slug, $function, 'dashicons-facebook', '81.1999');
		add_action('load-'.$hook, array($this, 'onSettingsSave'));
	}

	public function createAdminPage() {
		$this->logger->log("createAdminPage.", __LINE__);
		$this->options = get_option('wp-fb-social-stream-option-name');
		
		$view_data = array(
			'active_tab'	=> $this->active_tab,
			'menu_slug'		=> $this->menu_slug,
		);
		
		try {
			if ($this->active_tab == 'settings') {
				$view_data['fb_options'] = $this->view->render('admin/settings-fb-options');
				$view_data['stream_options'] = $this->view->render('admin/settings-stream-options');
			} elseif ($this->active_tab == 'styling') {
				$view_data['styling_options'] = $this->getTemplateStylingOptionsHTML();
			} elseif ($this->active_tab == 'extensions') {
				$view_data['extensions'] = $this->getExtensionsHTML();
			}

			$view_data['stream_info_col'] = $this->getStreamInfoHTML();
		} catch (Exception $e) {
			echo $e->getMessage();
			return;
		}
		
		$this->view->render_e('admin/wrap', $view_data);
	}

	public function initPageSettings() {
		$this->logger->log("initPageSettings.", __LINE__);

		register_setting('wp-fb-social-stream-settings-group',
			'fbss_setting_fb_page_name',
			array($this, 'validateFBPageName'));
		register_setting('wp-fb-social-stream-settings-group',
			'fbss_setting_fb_access_token',
			array($this, 'validateFBAccessToken'));
		register_setting('wp-fb-social-stream-settings-group',
			'fbss_setting_update_interval',
			array($this, 'validateUpdateInterval'));
		register_setting('wp-fb-social-stream-settings-group',
			'fbss_setting_msg_limit',
			array($this, 'validateMessageLimit'));

		// register template config settings
		$template = $this->template;
		$template_config_keys = $template->getDBOptionsConfigurationCSSKeys($with_hidden = false);
		foreach ($template_config_keys as $key) {
			register_setting('wp-fb-social-stream-styling-group', $key);
		}
		
		// register activation settings
		$extension_factory = new FBSS_ExtensionFactory;
		$extensions_i = $extension_factory->getInstalledExtensions();
		
		foreach ($extensions_i as $extension_id => $extension) {
			register_setting('wp-fb-social-stream-extensions-group',
				'fbss_extension_license_key_'.$extension::getId());
		}
	}

	public function validateFBPageName($input) {
		$this->logger->log("Validation FBPageName '$input'.", __LINE__);
		$fb_page_name = $input;

		if (preg_match('/(https?:\/\/)?(www\.)?facebook\.com\/(.*)/i', $fb_page_name, $match)) {
			$fb_page_name = $match[3];
		}

		$stored_page_name = get_option('fbss_setting_fb_page_name');
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

		$stored_token = get_option('fbss_setting_fb_access_token');
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
		$stored_limit = get_option('fbss_setting_msg_limit');
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
				update_option('fbss_setting_last_data_update', time());
				
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
		if ($hook == 'toplevel_page_'.$this->menu_slug) {
			$this->logger->log("addCSSLibraries.", __LINE__);

			$plugin_version = FBSS_Registry::get('plugin_version');
			$plugin_dir_url = FBSS_Registry::get('plugin_base_dir_url');

			wp_enqueue_style('wp-fb-social-stream-colorpicker',
				$plugin_dir_url.'tools/colorpicker/css/colorpicker.css',
				array(), $plugin_version, 'all');

			wp_enqueue_style('wp-fb-social-stream-admin-font-awesome',
				$plugin_dir_url.'templates/default/css/font-awesome-4.3.0/'.
				'css/font-awesome.min.css', array(), $plugin_version, 'all');

			wp_enqueue_style('wp-fb-social-stream-admin',
				$plugin_dir_url.'css/admin.css',
				array(), $plugin_version, 'all');
		}
	}

	public function addJSLibraries($hook) {
		if ($hook == 'toplevel_page_'.$this->menu_slug) {
			$this->logger->log("addJSLibraries hook '$hook'.", __LINE__);

			$plugin_dir_url = FBSS_Registry::get('plugin_base_dir_url');

			wp_enqueue_script('wp-fb-social-stream-colorpicker',
				$plugin_dir_url.'tools/colorpicker/js/colorpicker.js',
				array('jquery'), '', 'all');

			wp_enqueue_script('wp-fb-social-stream-colorpicker-admin',
				$plugin_dir_url.'js/admin/colorpicker-fb-social-stream.js',
				array('wp-fb-social-stream-colorpicker'), '', 'all');

			wp_enqueue_script('wp-fb-social-stream-admin-functions',
				$plugin_dir_url.'js/admin/functions.js',
				array('jquery'), '', 'all');

			wp_localize_script('wp-fb-social-stream-admin-functions',
				'wp_fb_social_stream_js_vars',
				array( 'ajaxurl' => admin_url('admin-ajax.php') ));
		}
	}

	private function getTemplateStylingOptionsHTML() {
		$this->logger->log("getTemplateStylingOptionsHTML.", __LINE__);

		$view_data = array(
			'table_rows'	=> array()
		);

		// CSS settings
		$css_configs = $this->template->getConfigurationCSS();
		
		foreach ($css_configs as $config) {
			$config_index = $config['config']['index'];
			$child_rows = array();

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
				
				array_push($child_rows, array(
					'input_name'	=> $input_name,
					'input_val'		=> get_option($input_name, ''),
					'type'			=> $sub_config['type'],
					'desc'			=> $sub_config['desc'],
					'selector'		=> $sub_config['selector'],
				));
			}
			
			array_push($view_data['table_rows'], array(
				'name'			=> $config['name'],
				'desc'			=> $config['desc'],
				'child_rows'	=> $child_rows
			));
		}
		
		return $this->view->render('admin/settings-styling-options', $view_data);;
	}

	private function getStreamInfoHTML() {
		$this->logger->log("getStreamInfoHTML.", __LINE__);
		
		$timestamp = get_option('fbss_setting_last_data_update');
		$stream_update_timestamp = FBSS_TemplateStringUtils::getLocalTimestamp($timestamp);

		/* translators: date format, see http://php.net/date */
		$stream_update_date_format = __('Y-m-d h:i:s a', 'wp-fb-social-stream');
		$stream_update_date = date($stream_update_date_format, $stream_update_timestamp);

		$view_data = array('stream_update_date' => $stream_update_date);
		$html = $this->view->render('admin/stream-info', $view_data);

		return $html;
	}
	
	private function getExtensionsHTML() {
		$this->logger->log("getExtensionsHTML.", __LINE__);
		
		$view_data = array(
			'extensions_installed' => array(),
			'extensions_available' => array(),
		);
		
		// get installed extensions
		$extension_factory = new FBSS_ExtensionFactory;
		$extensions_i = $extension_factory->getInstalledExtensions();
		
		foreach ($extensions_i as $extension_id => $extension) {
			$license_response = false;
			$valid = false;
			
			$license_key = $extension::getLicenseKey();
			$extension_name = $extension::getName();
			
			if ($license_key) {
				try {
					$validation_res = $extension::isValid();
				} catch (Exception $e) {
					add_settings_error('fbss-validation', 'validation_code', $e->getMessage());
					return;
				}
				
				$license_response = $validation_res['response'];
				
				if ($validation_res['result'] === true) {
					$valid = true;
				} else {
					// try to activate
					try {
						$activation_res = $extension::activate();
					} catch (Exception $e) {
						add_settings_error('fbss-validation', 'validation_code', $e->getMessage());
						return;
					}
					
					$license_response = $activation_res['response'];
					
					if ($activation_res['result'] === true) {
						$valid = true;
						add_settings_error('fbss-validation', 'validation_code',
							__('Extension activated successfully', 'wp-fb-social-stream'),
							'updated');
					} else {
						add_settings_error('fbss-validation', 'validation_code',
							__('Extension activation failed'));
					}
				}
			}
			
			// license can be valid, invalid, expired, disabled
			$ext_id = $extension::getId();
			$view_data['extensions_installed'][$ext_id] =
				array(
					'id'				=> $ext_id,
					'name'				=> $extension_name,
					'desc'				=> $extension::getDescription(),
					'version'			=> $extension::getVersion(),
					'valid'				=> $valid,
					'license_key'		=> $license_key,
					'license_response'	=> $license_response,
				);
		}
		
		// get available extensions
		$extension_manager = new FBSS_ExtensionManager;
		try {
			$extensions_a = $extension_manager->getAvailable();
		} catch (Exception $e) {
			add_settings_error('fbss-validation', 'validation_code', $e->getMessage());
			return;
		}

		foreach ($extensions_a as $extension) {
			$ext_id = $extension['id'];
			
			if ( !isset($view_data['extensions_installed'][$ext_id]) ) {
				$view_data['extensions_available'][$ext_id] =
					array(
						'id'		=> $ext_id,
						'name'		=> $extension['name'],
						'desc'		=> $extension['desc'],
						'img'		=> $extension['img'],
						'version'	=> $extension['version'],
						'download'	=> $extension['download'],
						'activated'	=> false,
					);
			}
		}
		
		return $this->view->render('admin/extensions-store', $view_data);
	}
	
	private function getNumExtensionUpdates() {
		$this->logger->log("getNumExtensionUpdates.", __LINE__);
		
		# TODO really needed?
				
		$num_updates = 0;
		
		return $num_updates;
	}
}
