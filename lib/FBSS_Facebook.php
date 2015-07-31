<?php

require_once('FBSS_Logger.php');
require_once('FBSS_Registry.php');


class FBSS_Facebook {
	
	const FB_GRAPH_VERSION = 'v2.4';
	const FB_GRAPH_URI = 'https://graph.facebook.com';
	const FALLBACK_GRAPH_URI = 'http://angileri.de/rest/facebook';
	
	private $access_token;
	private $plugin_version;
	private $logger;
	
	
	public function __construct() {
		$this->logger = new FBSS_Logger(__CLASS__);
		$this->access_token = FBSS_Registry::get('fb_access_token');
		$this->plugin_version = FBSS_Registry::get('plugin_version');
	}
	
	public function getFBPageID ($page_name) {
		$this->logger->log("getFBPageID by page-name '$page_name'.", __LINE__);
		
		if ($this->access_token) {
			$json = @file_get_contents(self::FB_GRAPH_URI.'/'.
						self::FB_GRAPH_VERSION.'/'.$page_name.
						'?access_token='.$this->access_token);
		} else {
			$json = @file_get_contents(self::FALLBACK_GRAPH_URI.'/'.$page_name.
						'?fbss_v='.$this->plugin_version);
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
		
		$fields = array('id');
		
		if ($this->access_token) {
			$json = @file_get_contents(self::FB_GRAPH_URI.'/'.
						self::FB_GRAPH_VERSION.'/'.$page_id.
						'/posts?fields='.implode(',',$fields).'&limit='.$limit.
						'&access_token='.$this->access_token);
		} else {
			$json = @file_get_contents(self::FALLBACK_GRAPH_URI.'/'.$page_id.
						'/posts?fields='.implode(',',$fields).'&limit='.$limit.
						'&fbss_v='.$this->plugin_version);
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
		
		$fields = array('id', 'message', 'type', 'status_type', 'created_time',
				'updated_time', 'object_id', 'actions', 'link', 'picture,name',
				'caption', 'description', 'source', 'shares', 'is_hidden',
				'is_expired', 'likes.summary(true)', 'comments.summary(true)');
		
		if ($this->access_token) {
			$json = @file_get_contents(self::FB_GRAPH_URI.'/'.
						self::FB_GRAPH_VERSION.'/'.$msg_id.
						'?fields='.implode(',',$fields).'&access_token='.
						$this->access_token);
		} else {
			$json = @file_get_contents(self::FALLBACK_GRAPH_URI.'/'.$msg_id.
						'?fields='.implode(',',$fields).'&fbss_v='.
						$this->plugin_version);
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
		
		$fields = array('id', 'source', 'images', 'picture', 'width', 'height',
				'name', 'created_time', 'updated_time');
		
		if ($this->access_token) {
			$json = @file_get_contents(self::FB_GRAPH_URI.'/'.
						self::FB_GRAPH_VERSION.'/'.$img_id.
						'?fields='.implode(',',$fields).'&access_token='.
						$this->access_token);
		} else {
			$json = @file_get_contents(self::FALLBACK_GRAPH_URI.'/'.$img_id.
						'?fields='.implode(',',$fields).'&fbss_v='.
						$this->plugin_version);
		}
		
		if($json === FALSE) {
			$this->logger->log("Facebook-API did not return valid JSON for ".
					"img-id '$img_id': $json", __LINE__, true);
			return false;
		}
		
		return $json;
	}
	
	public function getFBComments ($msg_id, $paging_type='', $cursor='') {
		$this->logger->log("getFBComments with msg-obj-id '$msg_id'.", __LINE__);
		
		$fields = array('id', 'message', 'created_time', 'from', 'message_tags',
			'user_likes'
		);

		$paging_options = '';
		if ($paging_type == 'next') {
			$paging_options = '&limit=25&after='.$cursor.'&order=chronological';
		} elseif ($paging_type == 'previous') {
			$paging_options = '&limit=25&before='.$cursor.'&order=chronological';
		}
		
		if ($this->access_token) {
			$json = @file_get_contents(self::FB_GRAPH_URI.'/'.
					self::FB_GRAPH_VERSION.'/'.$msg_id.'/comments'.
					'?fields='.implode(',',$fields).'&access_token='.
					$this->access_token.$paging_options);
		} else {
			$json = @file_get_contents(self::FALLBACK_GRAPH_URI.'/'.$msg_id.
					'/comments?fields='.implode(',',$fields).'&fbss_v='.
					$this->plugin_version.$paging_options);
		}
		
		if($json === FALSE) {
			$this->logger->log("Facebook-API did not return valid JSON for ".
					"img-id '$img_id': $json", __LINE__, true);
			return false;
		}
		
		return $json;
	}
}
