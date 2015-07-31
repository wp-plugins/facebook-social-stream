<?php

require_once('EDD/FBSS_EDD_SL_Plugin_Updater.php');


class FBSS_ExtensionBase {
	
	const LICENSE_SERVICE = 'http://angileri.de/blog/';
	
	public static function getVersion() {
		return static::$version;
	}
	
	public static function getId() {
		return static::$id;
	}
	
	public static function getName() {
		return static::$name;
	}
	
	public static function getDescription() {
		return static::$desc;
	}
	
	public static function getChecksum() {
		return hash('crc32b', static::$id);
	}
	
	public static function activate() {
		$logger = static::$logger;
		
		try {
			$obj = self::runLicenseServiceCall('activate_license');
		} catch (Exception $e) {
			throw $e;
		}
		
		$logger->log("Check activation.", __LINE__);
		
		if ( property_exists($obj, 'license') ) {
			$res = ($obj->license == 'valid') ? true : false;
			$logger->log("Activation result '$res'.", __LINE__);
		} else {
			throw new Exception("Service did not return valid JSON!");
		}
		
		return array('result' => $res, 'response' => $obj);
	}
	
	public static function isValid($expired_is_valid = true) {
		$logger = static::$logger;
		$set_cache = false;
		
		if ($json = get_transient('fbss_extension_valid_'.static::getChecksum())) {
			$obj = json_decode($json);
		} else {
			try {
				$obj = self::runLicenseServiceCall('check_license');
				$set_cache = true;
			} catch (Exception $e) {
				throw $e;
			}
		}
		
		$logger->log("Check isValid.", __LINE__);
		$res = false;

		if (property_exists($obj, 'license') ) {
			if ($obj->license == 'valid') {
				$res = true;
			} else {
				if ($expired_is_valid && ($obj->license == 'expired')) {
					$logger->log("Extension is expired, handle as valid.", __LINE__);
					$res = true;
				}
			}
			
			$logger->log("Validation result '$res'.", __LINE__);
		} else {
			throw new Exception("Service did not return valid JSON!");
		}
		
		if ($res === true && $set_cache) {
			set_transient('fbss_extension_valid_'.static::getChecksum(),
				json_encode($obj), 86400);
		}
		
		return array('result' => $res, 'response' => $obj);
	}
	
	public static function hasLicenseKey() {
		return get_option('fbss_extension_license_key_'.static::$id) ? true : false;
	}
	
	public static function getLicenseKey() {
		return get_option('fbss_extension_license_key_'.static::$id);
	}
	
	public static function setLicenseKey($license_key) {
		update_option('fbss_extension_license_key_'.static::$id, $license_key);
	}
	
	public static function hasUpdates() {
		$logger = static::$logger;
		$logger->log("Check if plugin has updates.", __LINE__);
		
		$license_key = self::getLicenseKey();
		if ($license_key) {
			$edd_updater = new FBSS_EDD_SL_Plugin_Updater(
					self::LICENSE_SERVICE, static::$plugin_uri, array(
						'version' 	=> static::$version,
						'license' 	=> $license_key,
						'item_name' => static::$name,
						'author' 	=> static::$author,
				)
			);
		} else {
			$logger->log("No license key found for plugin '".static::$name."' ".
					"Plugin update check not possible.", __LINE__);
		}
	}
	
	
	private static function runLicenseServiceCall($action) {
		$logger = static::$logger;
		
		$logger->log("runLicenseServiceCall '$action'.", __LINE__);
		
		$item_name = self::getName();
		if (!$item_name) {
			throw new Exception("No item name defined!");
		}
		
		$license_key = self::getLicenseKey();
		if (!$license_key) {
			throw new Exception("No license key defined!");
		}
		
		$json = @file_get_contents(self::LICENSE_SERVICE.'?'.
					'edd_action='.$action.'&item_name='.
					urlencode( $item_name ).'&license='.
					$license_key.'&url='.get_bloginfo('wpurl'));
		
		if (!$json) {
			throw new Exception("License service did not respond with valid ".
					"HTTP response! Please try again later.");
		}
		
		$obj = json_decode($json);
		if (!$obj) {
			throw new Exception("License service did not respond with valid ".
					"JSON! Please try again later.");
		}
		
		return $obj;
	}
	
}