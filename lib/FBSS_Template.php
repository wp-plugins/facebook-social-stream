<?php

require_once('FBSS_Logger.php');
require_once('FBSS_Registry.php');


class FBSS_Template {
	
	private $logger;
	
	private $template_name = 'default'; // TODO set dynamically if more templates available
	private $template_config;
	
	
	public function __construct() {
		$this->logger = new FBSS_Logger(__CLASS__);
		
		$template_name = $this->template_name;
		
		$this->logger->log("Init configuration for template '$template_name'.",
				__LINE__);
		
		// register configuration in registry
		require_once(plugin_dir_path(__FILE__).'../templates/default/config.php');
		
		$this->template_config = FBSS_Registry::get('template_config');
	}
	
	public function getName() {
		return $this->template_name;
	}
	
	public function getConfiguration() {
		return $this->template_config;
	}
	
	public function getConfigurationAPIVersion() {
		$template_config = $this->template_config;
		$api_version = $template_config['api_version'];
		
		$this->logger->log("Configuration API version is '$api_version'.",
				__LINE__);
		
		return $api_version;
	}
	
	public function getConfigurationCSS() {
		return $this->template_config['css'];
	}
	
	public function getConfigurationCSSKeydata($with_hidden = true) {
		$this->logger->log("getConfigurationCSSKeydata.", __LINE__);
		
		$template_config = $this->template_config;
		$css_configs = $template_config['css'];
		$config_keys = array();
		
		foreach ($css_configs as $config) {
			$config_index = $config['config']['index'];
				
			foreach ($config['config']['configs'] as $sub_config) {
				if (!$with_hidden) {
					if (isset($sub_config['actions'])) {
						if (isset($sub_config['actions']['hide'])) {
							if ($sub_config['actions']['hide']) {
								continue;
							}
						}
					}
				}
				
				$template_config_key = 'fbss_tplt_cfg_'.$this->template_name.'_'.
					$config_index.'_'.$sub_config['config_id'];
		
				$this->logger->log("Generated css config key ".
						"'$template_config_key.", __LINE__);
		
				array_push($config_keys, array(
					'template_config_key' 	=> $template_config_key,
					'config_index'			=> $config_index,
					'config_id'				=> $sub_config['config_id'],
					'type'					=> $sub_config['type'],
				));
			}
		}
		
		return $config_keys;
	}
	
	public function getDBOptionsConfigurationCSSKeys($with_hidden = true) {
		$this->logger->log("getDBOptionsConfigurationCSSKeys.", __LINE__);
		
		$option_keys = array();
		$config_keydata = $this->getConfigurationCSSKeydata($with_hidden);
		
		foreach ($config_keydata as $key_data) {
			
			array_push($option_keys, $key_data['template_config_key']);
			
			# register unit configuration key for size-type configs
			if ($key_data['type'] == 'size') {
				array_push($option_keys, $key_data['template_config_key'].'_u');
			}
		}
		
		return $option_keys;
	}
	
	public function getDBOptionsKey($config_index, $config_id) {
		return 'fbss_tplt_cfg_'.$this->template_name.'_'.$config_index.'_'.$config_id;
	}
	
	public function getDBOptionsValue($db_options_key) {
		return get_option($db_options_key);
	}
}
