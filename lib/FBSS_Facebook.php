<?php 

require_once('FBSS_Logger.php');
require_once('FBSS_Registry.php');


class FBSS_Facebook {
	
	const FB_GRAPH_VERSION = 'v2.4';
	const FB_GRAPH_URI = 'https://graph.facebook.com';
	const FALLBACK_GRAPH_URI = 'http://angileri.de/rest/facebook';
	
	private $access_token;
	private $logger;
	
	
	public function __construct() {
		$this->logger = new FBSS_Logger(__CLASS__);
		$this->access_token = FBSS_Registry::get('fb_access_token');
	}
	
	public function getFBPageID ($page_name) {
		$this->logger->log("getFBPageID by page-name '$page_name'.", __LINE__);
		
		if ($this->access_token) {
			$json = @file_get_contents(self::FB_GRAPH_URI.'/'.
					self::FB_GRAPH_VERSION.'/'.$page_name.
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
	
	public function getLikeCount ($msg_id) {
		$this->logger->log("getLikeCount with msg-id '$msg_id'.", __LINE__);
		
		if ($this->access_token) {
			$json = @file_get_contents(self::FB_GRAPH_URI.'/'.$msg_id.
					'?access_token='.$this->access_token.
					'&fields=likes.summary(true)');
		} else {
			$json = @file_get_contents(self::FALLBACK_GRAPH_URI.'/'.$msg_id.
					'?fields=likes.summary(true)');
		}
		
		if($json === FALSE) {
			$this->logger->log("Facebook-API did not return valid JSON for ".
					"msg-id '$msg_id': $json", __LINE__, true);
			return false;
		}
		
		return $json;
	}
	
	public function getCommentCount ($msg_id) {
		$this->logger->log("getCommentCount with msg-id '$msg_id'.", __LINE__);
		
		if ($this->access_token) {
			$json = @file_get_contents(self::FB_GRAPH_URI.'/'.$msg_id.
					'?access_token='.$this->access_token.
					'&fields=comments.summary(true)');
		} else {
			$json = @file_get_contents(self::FALLBACK_GRAPH_URI.'/'.$msg_id.
					'?fields=comments.summary(true)');
		}
		
		if($json === FALSE) {
			$this->logger->log("Facebook-API did not return valid JSON for ".
					"msg-id '$msg_id': $json", __LINE__, true);
			return false;
		}
		
		return $json;
	}
}
