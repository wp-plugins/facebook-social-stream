<?php

require_once('FBSS_Logger.php');


class FBSS_ExtensionFactory {
	
	private $logger;
	private $plugin_base_dir;
	
	public function __construct() {
		$this->logger = new FBSS_Logger(__CLASS__);
		$this->extensions_base_dir = ABSPATH . 'wp-content/plugins';
	}
	
	public function getInstalledExtensions($valid_check=false) {
		$this->logger->log("getInstalledExtensions.", __LINE__);
		
		$extensions = array();
		
		foreach (get_plugins() as $key => $val) {
			if (preg_match('/^(.+?)\/(.+?)$/', $key, $results)) {
				$plugin_folder = $results[1];
				$plugin_file = $results[2];
				
				if (preg_match('/^fbss-extension-/', $plugin_folder)) {
					
					$this->logger->log("Found extension '$key'.", __LINE__);
					
					$extension = $this->getExtension($plugin_folder,
							$plugin_file, $valid_check);
					if ($extension) {
						$extensions[$plugin_folder] = $extension;
					}
				}
			}
		}
		
		return $extensions;
	}
	
	public function getExtension($dir_name, $plugin_name, $valid_check=false) {
		$this->logger->log("getExtension '$plugin_name'.", __LINE__);
		
		if ( preg_match('/\.\.\//', $dir_name) || preg_match('/\.\.\//', $plugin_name) ) {
			throw new Exception(
					sprintf(__("Name restriction error with extension '%s'!",
							'wp-fb-social-stream'), $dir_name) );
		}
		
		$extension_uri = $dir_name.'/'.$plugin_name;
		$extension_base_uri = $this->extensions_base_dir.'/'.$dir_name.'/'.$plugin_name;
		
		if ( file_exists($extension_base_uri) ) {
			$this->logger->log("Extension '$plugin_name' is installed.", __LINE__);

			if ( is_plugin_active($extension_uri) ) {
				require_once($extension_base_uri);
					
				$class_name = $this->getClassNameFromExtensionURI($extension_base_uri);
				$extension = new $class_name;
				
				if ($valid_check) {
					try {
						$validation_res = $extension::isValid();
					} catch (Exception $e) {
						$this->logger->log("Could not check if plugin ".
								"'$plugin_name' has valid license!", __LINE__);
						return false;
					}
					
					if ($validation_res['result'] === true) {
						$this->logger->log("Extension '$plugin_name' has valid ".
								"license.", __LINE__);
						return $extension;
					}
					
					$this->logger->log("Extension '$plugin_name' has NO valid ".
							"license!", __LINE__);
				} else {
					return $extension;
				}
			}
		}
		
		$this->logger->log("Extension '$plugin_name' not found.", __LINE__);
		
		return false;
	}
	
	private function getClassNameFromExtensionURI($extension_uri) {
		$fp = fopen($extension_uri, 'r');
		$class = $buffer = '';
		$i = 0;
		while (!$class) {
			if (feof($fp)) break;
		
			$buffer .= fread($fp, 512);
			if (preg_match('/\s*class\s*(.+?)\s.*?{/', $buffer, $matches)) {
				return $matches[1];
			}
		}
	}
}