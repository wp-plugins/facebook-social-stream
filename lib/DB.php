<?php 

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once('Logger.php');

class DB {

	private $db_version = '1.0';
	private $db_version_key = 'wp_fb_social_stream_db_version';
	
	private $logger;
	
	private $table_name;
	private $table_charset_collate;
	
	private $wpdb;
	
	
	public function __construct() {
		$this->logger = new Logger(__CLASS__);
		
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->table_name = $wpdb->prefix . 'wp_fb_social_stream';
		$this->table_charset_collate = $wpdb->get_charset_collate();
	}
	
	public function create() {
		$wpdb = $this->wpdb;
		$table_name = $this->table_name;
		$charset_collate = $this->table_charset_collate;
		
		$this->logger->log("Setup database with table '$table_name' ".
				"($charset_collate).", __LINE__);
		
		// check if table already exists
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
			// create it
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				object_id VARCHAR(255) NOT NULL,
				type VARCHAR(255) NOT NULL,
				data text NOT NULL,
				created_time DATETIME,
				updated_time DATETIME,
				timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				CONSTRAINT uc_object UNIQUE (object_id,type)
			) $charset_collate;";
				
			dbDelta($sql);
			add_option($this->db_version_key, $this->db_version);
		} else {
			$this->logger->log("Table '$table_name' already exists. ".
					"Nothing to do.", __LINE__);
		}
	}
	
	public function drop() {
		$wpdb = $this->wpdb;
		$table_name = $this->table_name;
		
		$this->logger->log("Delete table '$table_name'.", __LINE__);
		
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
			$sql = "DROP TABLE $table_name;";
			$wpdb->query($sql);
			delete_option($this->db_version_key);
		} else {
			$this->logger->log("Table '$table_name' does not exists. ".
					"Nothing to do.", __LINE__);
		}
	}
	
	public function truncate() {
		$wpdb = $this->wpdb;
		$table_name = $this->table_name;
		
		$this->logger->log("Truncate table '$table_name'.", __LINE__);
		
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
			$sql = "TRUNCATE TABLE $table_name;";
			$wpdb->query($sql);
		} else {
			$this->create();
		}
	}
	
	public function insert($object_id, $type, $data, $created_time, $updated_time) {
		$this->logger->log("Insert object with id '$object_id' of type ".
				"'$type' into DB.", __LINE__);
		
		$wpdb = $this->wpdb;
		
		$data = array(
				'object_id'		=> $object_id,
				'type'			=> $type,
				'data'			=> $data,
				'created_time'	=> $created_time,
				'updated_time'	=> $updated_time
		);
		
		$valid = array('%s', '%s', '%s', '%s', '%s');
		
		# http://codex.wordpress.org/Class_Reference/wpdb#REPLACE_row
		$wpdb->replace($this->table_name, $data, $valid);
	}
	
	public function get($object_id, $type) {
		$this->logger->log("Get DB data-rows with object_id '$object_id' and ".
				" type '$type'.", __LINE__);
	
		$wpdb = $this->wpdb;
		$table_name = $this->table_name;
	
		$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE object_id='%s' ".
				"AND type='%s'", $object_id, $type);
		$row = $wpdb->get_row($sql, OBJECT);
	
		return $row;
	}
	
	public function getByType($type, $limit=20) {
		$this->logger->log("Get '$limit' DB data-rows from type '$type'.",
				__LINE__);
		
		$wpdb = $this->wpdb;
		$table_name = $this->table_name;
		
		$sql = $wpdb->prepare("SELECT * FROM $table_name WHERE type='%s' ".
				"ORDER BY created_time DESC LIMIT %d", $type, $limit);
		$entries = $wpdb->get_results($sql, OBJECT_K);
		
		return $entries;
	}
}
