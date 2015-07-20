<?php 

require_once('FBSS_DB.php');
require_once('FBSS_Facebook.php');
require_once('FBSS_Logger.php');
require_once('FBSS_Registry.php');
require_once('FBSS_Template.php');


class FBSS_SocialStream {
	
	private $db;
	private $facebook;
	private $logger;
	private $template;
	private $template_name;
	
	private $page_id;
	private $page_name;
	private $access_token;
	
	
	public function __construct() {
		$this->page_name = FBSS_Registry::get('fb_page_name');
		$this->access_token = FBSS_Registry::get('fb_access_token');
		
		$this->db = new FBSS_DB;
		$this->facebook = new FBSS_Facebook;
		$this->logger = new FBSS_Logger(__CLASS__);
		$this->template = new FBSS_Template;
		$this->template_name = $this->template->getName();
	}
	
	public function get($limit=20) {
		$page_name = $this->page_name;
		
		$this->logger->log("Get '$limit' social stream posts from page ".
				"'$page_name'.", __LINE__);
		
		$messages = $this->db->getByType('message', $limit);
		
		$social_stream_html = '';
		$i = 1;
		
		foreach ($messages as $msg) {
			$msg_id = $msg->id;
			$msg_obj_id = $msg->object_id;
			$msg_data_json = $msg->data;
			$msg_data_obj = json_decode($msg_data_json);
			
			$this->logger->log("Processing message '$i' with id '$msg_id' and ".
					"object_id '$msg_obj_id'.", __LINE__);
			
			#$this->logger->log("Message: ".print_r($messages, true), __LINE__);
			
			$msg_type = $msg_data_obj->type;
			
			$msg_share_link = $this->getShareLinkFromJSON($msg_obj_id, $msg_data_obj, $msg_type);
			
			if (property_exists($msg_data_obj, 'message')) {
				$msg_text = $msg_data_obj->message;
			} else {
				$msg_text = '';
			}
			
			$msg_date_iso_8601 = date('Y-m-d', strtotime($msg_data_obj->created_time));
			$msg_date_month = date('F', strtotime($msg_data_obj->created_time));
			$msg_date_month_translated = __($msg_date_month);
			
			/* translators: date format, see http://php.net/date */
			$date_format = __('jS \of %\s Y h:i A', 'wp-fb-social-stream');
			$msg_date_string = sprintf( 
					date($date_format, strtotime($msg_data_obj->created_time)), 
					$msg_date_month_translated );
			
			
			# read likes and comments from DB
			$msg_likes = 0;
			$msg_comments = 0;
			
			$likes_summary = $this->db->get($msg_obj_id, 'likes_summary');
			if ($likes_summary) {
				$likes_summary_json = $likes_summary->data;
				$likes_summary_data_obj = json_decode($likes_summary_json);
					
				$msg_likes = $likes_summary_data_obj->likes->summary->total_count;				
			}
			
			$comments_summary = $this->db->get($msg_obj_id, 'comments_summary');
			if ($comments_summary) {
				$comments_summary_json = $comments_summary->data;
				$comments_summary_data_obj = json_decode($comments_summary_json);
				
				$msg_comments = $comments_summary_data_obj->comments->summary->total_count;				
			}
			
			# handle message types
			$img_id = '';
			$img_src = '';
			$img_widht = '';
			$img_height = '';
			
			$link_src = '';
			$link_img = '';
			$link_name = '';
			$link_caption = '';
			$link_description = '';
			
			if ($msg_type == 'photo') {
				if (property_exists($msg_data_obj, 'object_id')) {
					$img_id = $msg_data_obj->object_id;
					$image = $this->db->get($img_id, 'image');
					
					$this->logger->log("Processing image with id '$img_id'.",
						__LINE__);
					#$this->logger->log("Image: ".print_r($image, true), __LINE__);
					
					if (property_exists($image, 'data')) {
						$img_data_obj = json_decode($image->data);
						
						$img_src = htmlspecialchars($img_data_obj->source);
						$img_src_thumb = htmlspecialchars($img_data_obj->picture);
						$img_widht = $img_data_obj->width;
						$img_height = $img_data_obj->height;
					}
				}
			} else if ($msg_type == 'link') {
				if (property_exists($msg_data_obj, 'link')) {
					$link_src = $msg_data_obj->link;
				}
				if (property_exists($msg_data_obj, 'picture')) {
					$link_img = $msg_data_obj->picture;
				}
				if (property_exists($msg_data_obj, 'name')) {
					$link_name = $msg_data_obj->name;
				}
				if (property_exists($msg_data_obj, 'caption')) {
					$link_caption = $msg_data_obj->caption;
				}
				if (property_exists($msg_data_obj, 'description')) {
					$link_description = $msg_data_obj->description;
					if (strlen($link_description) > 120) { 
						$link_description = substr($link_description,0, 120) . "..."; 
					}
				}
			} else if ($msg_type == 'video') {
				# TODO add video player
				continue;
			}
			
			ob_start();
			include(plugin_dir_path( __FILE__ ).'../templates/'.
					$this->template_name.'/message.php');
			$social_stream_html .= ob_get_clean();
			
			$i++;
		}
		
		ob_start();
		include(plugin_dir_path( __FILE__ ).'../templates/'.
				$this->template_name.'/wrapper.php');
		$html = ob_get_clean();
		
		return $html;
	}
	
	public function store() {
		$page_name = $this->page_name;
		
		$this->logger->log("Store social stream for page '$page_name' in DB.",
				 __LINE__);
		
		$page_id = $this->getPageID();
		if ($page_id === false) {
			return false;
		}
		
		$posts_json = $this->getPosts();
		if ($posts_json === false) {
			return false;
		}
		
		$objPosts = json_decode($posts_json);
		
		foreach ($objPosts->data as $objPost) {
			$message_id = $objPost->id;
		
			$message_json = $this->getMessage($message_id);
			if ($message_json === false) {
				continue;
			}

			$objMessage = json_decode($message_json);
			
			$message_type = $objMessage->type;

			$this->logger->log("Retrieved message with id '$message_id' of ".
					"type '$message_type'.", __LINE__);
			
			if (property_exists($objMessage, 'message')) {
				$message_text = $objMessage->message;
				$this->logger->log("Message text '".
						substr ( $message_text , 0 , 100 )."'.", __LINE__);
			}
		
			// save message to MySQL DB
			$this->db->insert($message_id, 'message', $message_json,
				$objMessage->created_time, $objMessage->updated_time);

			// save like count to MySQL DB
			$like_count_json = $this->getLikeCount($message_id);
			if ($like_count_json === false) {
				continue;
			}
			$this->db->insert($message_id, 'likes_summary', $like_count_json,
					$objMessage->created_time, $objMessage->updated_time);
			
			// save comment count to MySQL DB
			$comment_count_json = $this->getCommentCount($message_id);
			if ($comment_count_json === false) {
				continue;
			}
			$this->db->insert($message_id, 'comments_summary', $comment_count_json,
					$objMessage->created_time, $objMessage->updated_time);
			
			if ($message_type == 'photo') {
				$object_id = $objMessage->object_id;
				
				$image_json = $this->getImage($object_id);
				if ($image_json === FALSE) {
					# facebook bug?
					continue;
				}
				
				$this->logger->log("Retrieved image with id '$object_id'.", 
						__LINE__);

				$objImage = json_decode($image_json);
				
				if (property_exists($objImage, 'images')) {
					$images = $objImage->images;
		
					// save image to MySQL DB
					$this->db->insert($object_id, 'image', $image_json,
						$objImage->created_time, $objImage->updated_time);
		
					foreach ($images as $image) {
						$img_height = $image->height;
						$img_width = $image->width;
						$img_src = $image->source;
		
						$this->logger->log("Image width '$img_width', height ".
								"'$img_height' and src '$img_src'.", __LINE__);
					}
				} else {
					print "\t no images found for message '$message_id' with object_id '$object_id'.\n";
					$this->logger->log("No images found for message ".
							"'$message_id' with object_id '$object_id'.",
							__LINE__);
				}
			}
		}
		
		return true;
	}
	
	public function drop() {
		$this->logger->log("Drop social stream data.",
				__LINE__);
		
		$this->db->truncate();
		
		return true;
	}
	
	
	private function getPageID() {
		$page_name = $this->page_name;

		$this->logger->log("Get page-id by page-name '$page_name'.", __LINE__);
		
		$page_id = $this->facebook->getFBPageID($page_name);
		if ($page_id === false) {
			$this->logger->log("Could not retrieve page_id for page ".
					"'$page_name'!", __LINE__, true);
			return false;
		}
		
		$this->page_id = $page_id;
		
		return $page_id;
	}
	
	private function getPosts($limit=20) {
		$page_name = $this->page_name;
		$page_id = $this->page_id;
		
		$this->logger->log("Get posts for page '$page_name' with id '$page_id'.",
				 __LINE__);
		
		if (!$page_id) {
			$this->logger->log("Could not get posts without page_id for page ".
					"'$page_name'!", __LINE__, true);
			return false;
		}
		
		$posts_json = $this->facebook->getFBPosts($page_id, $limit);
		if ($posts_json === false) {
			$this->logger->log("Could not retrieve posts for page ".
					"'$page_name' with id '$page_id'!", __LINE__, true);
			return false;
		}
		
		return $posts_json;
	}
	
	private function getMessage($message_id) {
		$this->logger->log("Get message with id '$message_id'.", __LINE__);
		$message_json = $this->facebook->getFBMessage($message_id);
		if ($message_json === false) {
			$this->logger->log("Could not retrieve message with id ".
					"'$message_id'!", __LINE__, true);
			return false;
		}
		
		return $message_json;
	}
	
	private function getImage($image_id) {
		$this->logger->log("Get image with id '$image_id'.", __LINE__);
		$image_json = $this->facebook->getFBImage($image_id);
		if ($image_json === false) {
			$this->logger->log("Could not retrieve image with id ".
					"'$image_id'!", __LINE__, true);
			return false;
		}
		
		return $image_json;
	}
	
	private function getLikeCount($message_id) {
		$this->logger->log("Get like count with id '$message_id'.", __LINE__);
		$like_count_json = $this->facebook->getLikeCount($message_id);
		if ($like_count_json === false) {
			$this->logger->log("Could not retrieve like count with id ".
					"'$message_id'!", __LINE__, true);
			return false;
		}
		
		return $like_count_json;
	}
	
	private function getCommentCount($message_id) {
		$this->logger->log("Get comment count with id '$message_id'.", __LINE__);
		$comment_count_json = $this->facebook->getCommentCount($message_id);
		if ($comment_count_json === false) {
			$this->logger->log("Could not retrieve like count with id ".
					"'$message_id'!", __LINE__, true);
			return false;
		}
	
		return $comment_count_json;
	}
	
	private function getShareLinkFromJSON($msg_obj_id, $objMessage, $msg_type) {
		if (property_exists($objMessage, 'actions')) {
			$actions = $objMessage->actions;
			if (is_array($actions)) {
				foreach ($actions as $action) {
					if ($action->name == 'Share') {
						return $action->link;
					}
				}
			}
		} else {
			// fallback
			
			if ($msg_type == 'photo') {
				// if there is no action, then link to gallery itself
				if (preg_match('/(.+)_(.+)/i', $msg_obj_id, $match)) {
					return 'https://www.facebook.com/'.$this->page_name.'/posts/'.
							$match[2];
				}
			}
			
			if (property_exists($objMessage, 'link')) {
				return $objMessage->link;
			}
		}
		
		return '';
	}
}
