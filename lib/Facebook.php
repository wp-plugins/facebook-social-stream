<?php 

require_once('Logger.php');
require_once('Registry.php');


class Facebook {
	
	const FB_GRAPH_URI = 'https://graph.facebook.com';
	const FALLBACK_GRAPH_URI = 'http://angileri.de/rest/facebook';
	
	private $access_token;
	private $logger;
	
	
	public function __construct() {
		$this->logger = new Logger(__CLASS__);
		$this->access_token = Registry::get('fb_access_token');
	}
	
	public function getFBPageID ($page_name) {
		$this->logger->log("getFBPageID by page-name '$page_name'.", __LINE__);
		
		if ($this->access_token) {
			$json = @file_get_contents(self::FB_GRAPH_URI.'/'.$page_name.
					'?access_token='.$this->access_token);
		} else {
			$json = @file_get_contents(self::FALLBACK_GRAPH_URI.'/'.$page_name);
		}
		
		if($json === FALSE) {
			$this->logger->log("Facebook-API did not return valid JSON for ".
					"page-name '$page_name': $json", __LINE__, true);
			return false;
		}
		
		$obj = json_decode($json);
		$page_id = $obj->id;
		
		$this->logger->log("page-id: '$page_id'", __LINE__);
		
		return $page_id;
	}
	
	public function getFBPosts ($page_id, $limit=20) {
		$this->logger->log("getFBPosts with page-id '$page_id' limit '$limit'.",
				 __LINE__);
		
		if ($this->access_token) {
			$json = @file_get_contents(self::FB_GRAPH_URI.'/'.$page_id.
					'/posts?fields=message&limit='.$limit.'&access_token='.
					$this->access_token);
		} else {
			$json = @file_get_contents(self::FALLBACK_GRAPH_URI.'/'.$page_id.
					'/posts?fields=message&limit='.$limit);
		}
		
		if($json === FALSE) {
			$this->logger->log("Facebook-API did not return valid JSON for ".
					"page-id '$page_id': $json", __LINE__, true);
			return false;
		}
		
		return $json;
	}
	
	public function getFBMessage ($msg_id) {
		$this->logger->log("getFBMessage with msg-id '$msg_id'.", __LINE__);
		
		if ($this->access_token) {
			$json = @file_get_contents(self::FB_GRAPH_URI.'/'.$msg_id.
					'?access_token='.$this->access_token);
		} else {
			$json = @file_get_contents(self::FALLBACK_GRAPH_URI.'/'.$msg_id);
		}
		
		if($json === FALSE) {
			$this->logger->log("Facebook-API did not return valid JSON for ".
					"msg-id '$msg_id': $json", __LINE__, true);
			return false;
		}
		
		return $json;
	}
	
	public function getFBImage ($img_id) {
		$this->logger->log("getFBImage with img-id '$img_id'.", __LINE__);
		
		if ($this->access_token) {
			$json = @file_get_contents(self::FB_GRAPH_URI.'/'.$img_id.
					'?access_token='.$this->access_token);
		} else {
			$json = @file_get_contents(self::FALLBACK_GRAPH_URI.'/'.$img_id);
		}
		
		if($json === FALSE) {
			$this->logger->log("Facebook-API did not return valid JSON for ".
					"img-id '$img_id': $json", __LINE__, true);
			return false;
		}
		
		return $json;
	}
}
