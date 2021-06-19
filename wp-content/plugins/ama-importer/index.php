<?php
/*
Plugin Name: African Media Agency Importer
Plugin URI: http://www.venturesafrica.com/
Description: Import articles from the feed from African Media Agency.
Version: 1.0
Author: Temidayo Uji
Author URI: http://www.venturesafrica.com/
License: GPL
Copyright: Temidayo
*/

define('AMA_IMPORT_INTERVAL', 0.5); // hours
define('AMA_FEED_ARTICLE_LIMIT', 50);
define('AMA_LATEST_FEED_ARTICLE_LIMIT', 20);
//define('AMA_DEFAULT_CATEGORY', 17572); // 'News'
define('AMA_DEFAULT_CATEGORY', 17927); // 'Apo'
define('AMA_FEED_URL', 'https://www.africanmediaagency.com/category/english-news-releases/feed');

register_activation_hook(__FILE__, 'ama_activation');
register_deactivation_hook(__FILE__, 'ama_deactivation');
//register_activation_hook(__FILE__, 'ama_check_import_interval');

add_action('ama_import_interval_check', 'ama_check_import_interval');
add_action('admin_init', 'ama_admin_init');
add_action('deleted_post', function($post_id) {
	global $wpdb;
	$wpdb->delete("{$wpdb->prefix}ama_imported", array('post_id' => $post_id));
});

if (!function_exists('tail')) require_once(__DIR__ . '/tail.php');
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
class ama_List_Table extends WP_List_Table {
	protected $rows;
	public function __construct($rows = array()) {
		parent::__construct();
		$this->rows = $rows;
		$this->_column_headers = array(
			array('post_id' => 'WP ID', 'post_title' => 'Title', 'item_id' => 'RSS URL'),
			array(),
			array(),
			'item_id',
		);
	}
	public function display_tablenav($which) { }
	public function get_table_classes() {
		echo 'wp-list-table widefat striped admin';
	}
	public function column_default($item, $column_name) {
		print $item[$column_name];
	}
	public function get_columns() {
		return $this->_column_headers;
	}
	public function prepare_items() {
		foreach ($this->rows as $row) {
			$item['item_id'] = sprintf('<a target="_blank" href="%s">%s</a>', get_post_meta($row->post_id, 'item_id', true), $row->item_id);
			$item['post_id'] = sprintf('<a target="_blank" href="%s">%s</a>', admin_url("post.php?post=$row->post_id&action=edit"), $row->post_id);
			$item['post_title'] = sprintf('<a target="_blank" href="%s">%s</a>', get_permalink($row->post_id), get_the_title($row->post_id));
			$this->items[] = $item;
		}
	}
}

function ama_activation() {
	ama_create_db_table();
	ama_cron_enabled();
	add_option('ama_last_import_timestamp', 0, 'no', 'no');
	add_option('ama_last_imported_id', 0, 'no', 'no');
	//add_option('ama_cron_enabled', 0, 'no', 'no');
}

function ama_deactivation() {
	ama_disable_cron();
	delete_option('ama_last_import_timestamp');
	delete_option('ama_last_imported_id');
	delete_option('ama_cron_enabled');
}

function ama_create_db_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'ama_imported';
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE $table_name (
	  item_id int(11) NOT NULL AUTO_INCREMENT,
	  post_id int(11) NOT NULL,
	  title text NOT NULL,
	  PRIMARY KEY  (item_id),
	  UNIQUE KEY post_id (post_id)
	) $charset_collate;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

function ama_check_import_interval() {
	$current = time();
	$prev = get_option('ama_last_import_timestamp', 0);
	if (!defined('YOURLS_BASE')) {
		$yourls_path = dirname(plugin_dir_path(__FILE__)).'/yourls-link-creator';
		include($yourls_path.'/yourls-link-creator.php');
		require_once($yourls_path.'lib/admin.php');
		require_once($yourls_path.'lib/settings.php');
		require_once($yourls_path.'lib/ajax.php');
		require_once($yourls_path.'lib/global.php');
		require_once($yourls_path.'lib/helper.php');
		require_once($yourls_path.'lib/display.php');
	}
	if ($current - $prev > 3600 * AMA_IMPORT_INTERVAL) {
		ama_enable_log();
		ama_log("Starting CRON job. ".date('r'));
		//ama_run_import();
		ama_import_latest();
		update_option('ama_last_import_timestamp', time());
		ama_log("Finished CRON job. ".date('r'));
		wp_mail("uji@ventures-africa.com", "African Media Agency CRON Job Report.", tail(__DIR__.'/import.log', 65));
	}
}

function ama_enable_log() {
	global $ama_log_handle;
	return $ama_log_handle = @fopen(__DIR__.'/import.log', 'a');
}

function ama_reset_log() {
	global $ama_log_handle;
	$ama_log_handle = fopen(__DIR__.'/import.log', 'w');
}

function ama_log($s) {
	global $ama_log_handle;
	echo $s;
	if (isset($ama_log_handle) && $ama_log_handle) fwrite($ama_log_handle, "$s\n");
	else throw new Exception('log handle not available.');
}

function ama_reset_posts() {
	global $wpdb;
	$posts = get_posts('post_type=ventures_ama&posts_per_page=-1');
	foreach ($posts as $post) {
		ama_log("Deleting post [$post->ID]$post->post_title.");
		wp_delete_post($post->ID, true);
	}
	update_option('ama_last_import_timestamp', 0);
	update_option('ama_last_imported_id', 0);
}

function ama_back_to_index() {
	header("Location: ".admin_url('admin.php?import=africanmediaagency'));
	exit();
}

function ama_form($method, $title) {
	return sprintf(
		'<form method="POST" action="%s" style="display:inline-block; vertical-align:middle;"><input type="hidden" name="method" value="%s"><input type="submit" name="submit" value="%s" class="button button-primary button-large"></form>', 
		admin_url('admin.php?import=africanmediaagency'), 
		$method, 
		$title);
}

function ama_importer() {
	if (!empty($_POST)) {
		return ama_form_handler();
	}
	echo '
	<div class="wrap">
		<h2>AMA Africa Importer</h2>',
		ama_info(),
	'</div>';
}

function ama_force_reset() {
	ama_reset_log();
	ama_reset_posts();
}

function ama_reset_timestamp() {
	update_option('ama_last_import_timestamp', 0);
}

function ama_prepare_environment() {
	// error_reporting(-1);
	// ini_set('display_errors',false);
	// ini_set('log_errors','1');
	// ini_set('error_log',__DIR__.'/import.log');
}

function ama_force_import_run() {
	ama_prepare_environment();
	ama_run_import();
	update_option('ama_last_import_timestamp', time());
}

function ama_import_single_item() {
	ama_prepare_environment();
	ama_run_import(TRUE);
}

function ama_form_handler() {
	if (isset($_POST['method'])) {
		$fn = "ama_$_POST[method]";
		if (function_exists($fn)) $fn();
	}
	ama_back_to_index();
}

function ama_disable_cron() {
	wp_clear_scheduled_hook('ama_import_interval_check');
	update_option('ama_cron_enabled', 0);
}

function ama_cron_enabled() {
	wp_schedule_event(time(), 'twicedaily', 'ama_import_interval_check');
	update_option('ama_cron_enabled', 1);
}

function ama_article_2_post($obj) {
	$postdata = array(
		'post_content' => $obj->body_text,
		'post_name' => sanitize_title($obj->title),
		'post_title' => $obj->title,
		'post_status' => 'publish',
		'post_type' => 'ventures_ama',
		'post_author' => 313,
		'post_date' => $obj->date,
		'post_category' => [AMA_DEFAULT_CATEGORY],
	);
	return $postdata;
}

function ama_article_2_post_metadata($obj) {
	$metadata = array(
		'item_id' => $obj->id,
		'ama_source_name' => $obj->ama_source_name,
		'ama_url' => $obj->ama_url
	);
	return $metadata;
}

function ama_create_attachment_tmpfile_info($src) {
	if ($qsi = strpos($src, '?')) $src = substr($src, 0, $qsi);
	$name = basename($src);
	if (strpos($name, '.') === FALSE) {
		$tmp_name = tempnam(sys_get_temp_dir(), 'Image');
		$name = basename($tmp_name);
	}
	else $tmp_name = sys_get_temp_dir()."/$name";
	// in case tmp dir is set with a trailing slash.
	$tmp_name = str_replace('//', '/', $tmp_name);
	return compact('name','tmp_name');
}

function ama_create_attachment($src, $post_id) {
	$file_array = ama_create_attachment_tmpfile_info($src);
	ama_log("Sideload attachment $src to $file_array[tmp_name].");
	if (!copy($src, $file_array['tmp_name'])) return new WP_Error(1, sprintf('Error downloading attachment file %s.', $src));
	list($width, $height, $type, $attr) = getimagesize($file_array['tmp_name']);
	$mime = image_type_to_mime_type($type);
	if (strpos($mime, 'image') !== 0 || !preg_match('/(jpeg|gif|png)/', $mime)) return new WP_Error(1, sprintf('Attachment %s is not a valid image.', $src));
	$id = media_handle_sideload($file_array, $post_id);
	if (is_wp_error($id)) {
		@unlink($file_array['tmp_name']);
	}
	return $id;
}

function ama_save_shortlink($post_id) {
	// get my post URL and title
	$url    = YOURLSCreator_Helper::prepare_api_link( $post_id );
	$title  = get_the_title( $post_id );
	ama_log(sprintf('Creating short URL. Long URL: %s. Title: %s', $url, $title));

	// and optional keyword
	$keywd  = '';

	// set my args for the API call
	$args   = array( 'url' => esc_url( $url ), 'title' => sanitize_text_field( $title ), 'keyword' => $keywd );

	// make the API call
	$build  = YOURLSCreator_Helper::run_yourls_api_call( 'shorturl', $args, FALSE );

	// bail if empty data or error received
	if ( empty( $build ) || false === $build['success'] ) {
		ama_log('Error creating short URL.');
		return;
	}

	// we have done our error checking and we are ready to go
	if( false !== $build['success'] && ! empty( $build['data']['shorturl'] ) ) {
		// get my short URL
		$shorturl   = esc_url( $build['data']['shorturl'] );

		ama_log(sprintf('Short URL created: %s. Saving as post meta...', $shorturl));
		// update the post meta
		add_post_meta( $post_id, '_yourls_url', $shorturl, true );
		add_post_meta( $post_id, '_yourls_clicks', '0', true );
	}
}

function ama_insert_post($obj, $postdata, $metadata) {
	global $wpdb;
	ama_log(sprintf('Importing article [%d]:%s.', $obj->id, $obj->title));
	$post_id = wp_insert_post($postdata, true);
	if (!is_wp_error($post_id)) {
		$wpdb->insert($wpdb->prefix.'ama_imported', array('item_id' => $obj->id, 'post_id' => $post_id, 'title'=> $obj->title));
		foreach ($metadata as $key => $val){
			wp_set_post_terms($post_id, array(AMA_DEFAULT_CATEGORY), 'category', true );
			add_post_meta($post_id, $key, $val, true);
		}
		if (!empty($obj->featured_image)) {
			ama_log('Article has image. Importing...');
			$att_id = ama_create_attachment($obj->featured_image, $post_id);
			if (!is_wp_error($att_id)) {
				set_post_thumbnail($post_id, $att_id);
			}
			else ama_log(sprintf('Failed to import attachment %s for [%d]:%s. Error: %s.', $obj->featured_image, $obj->id, $obj->title, $att_id->get_error_message()));
		}
		if (defined('DOING_CRON') && DOING_CRON) ama_save_shortlink($post_id);
	}
	return $post_id;
}

function ama_import_article($obj, $existing_ids) {
	if (empty($obj->id)) {
		throw new Exception('Bad entry. Skip.');
	}
	
	if (in_array($obj->title, $existing_ids)) {
		throw new Exception(sprintf('Article [%d]:%s was already imported. Skip.', $obj->id, $obj->title));
	}
	$postdata = ama_article_2_post($obj);
	$metadata = ama_article_2_post_metadata($obj);
	$post_id = ama_insert_post($obj, $postdata, $metadata);
	if (is_wp_error($post_id)) {
		throw new Exception(sprintf('Article import failed for [%d]:%s. Error: %s.', $obj->id, $obj->title, $post_id->get_error_message()));
	}
}

function ama_xml_to_obj($xmlstring) {
	$rss = new DOMDocument();
    $rss->loadXML($xmlstring);
    $feed = array();
    foreach ($rss->getElementsByTagName('item') as $node) {
    	$id = $node->getElementsByTagName('link')->item(0)->nodeValue;
    	$featured_image = '';
    	$date = $node->getElementsByTagName('pubDate')->item(0)->nodeValue;

    	// try to pull image out of the body
    	$body = $node->getElementsByTagName('description')->item(0)->nodeValue;
    	preg_match("/<p[^>]*>[^<]*<img.+src=[\"']([^\"']+)[\"'][^>]*>[^<]*<\/p>/i", $body, $matches);
    	if (!empty($matches[1])) {
    		$featured_image = $matches[1];
    		$body = str_replace($matches[0], '', $body);
    	}

    	// try to pull author out of the body
    	$ama_source_name = 'African Media Agency';
    	$ama_url = $id;
    	preg_match("/<p[^>]*>[^<]*<span[^>]*>By\s+<a class=\"colorbox\" href=\"([^\"']+)\">([^<]*)<\/a><\/span>/i", $body, $author);
    	if (!empty($author[1])) {
    		$ama_source_name = $author[2];
    		$ama_url = $author[1];
    		$body = str_replace($author[0], '', $body);
    	}

    	$body = str_replace(
    		['<p>Source:', '<p>The post <a'], 
    		['<hr><p class="source-note">Source:', '<p class="source-note">The post <a'], 
    		$body
    	);

        $item = [
            'id' => $id,
            'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
            'date' => date('Y:m:d H:i:s', strtotime($date)),
            'featured_image' => $featured_image,
            'body_text' => $body,
            'ama_source_name' => $ama_source_name,
            'ama_url' => $ama_url,
        ];

        array_push($feed, (object)$item);
    }
    return $feed;
}

function ama_info() {
	global $wpdb;
	$imported = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ama_imported ORDER BY `item_id` ASC");
	$cron_enabled = get_option('ama_cron_enabled', 0);
	if (strpos($wpdb->last_error, 'ama_imported') !== FALSE) {
		echo '
		<div class="error notice">
			<p>'.$wpdb->last_error.'</p>
		</div>';
	}
	print '
	<div id="wpbody-content">
		<div id="dashboard-widgets" class="metabox-holder">
			<div class="postbox-containerX">
				<div class="meta-box-sortables">
					<div class="postbox">
						<h3 class="hndle">Cron Scheduling</h3>
						<div class="inside">
							<div class="rss-widget">';
		printf('
								<p>Currently enabled: <strong>%s</strong></p>
								<p>Last updated: <strong>%s</strong></p>
							</div>
							<div class="rss-widget">
								%s %s
							</div>', 
			$cron_enabled ? 'yes' : 'no', 
			date('c', get_option('ama_last_import_timestamp', 0)), 
			$cron_enabled 
				? ama_form('disable_cron', 'Disable') 
				: ama_form('enable_cron', 'Enable'), 
			ama_form('reset_timestamp', 'Reset Schedule')
		);
		print '
						</div>
					</div>
				</div>
			</div>
			<div class="postbox-containerX">
				<div class="meta-box-sortables">
					<div class="postbox">
						<h3 class="hndle">Recently Imported Articles</h3>
						<div class="inside">
							<div class="rss-widget">';
							if (empty($imported)) {
								print '<p>Nothing imported yet.</p>';
							}
							else {
								$table = new ama_List_Table(array_slice($imported, -20));
								$table->prepare_items();
								$table->display();
							}
							echo '
							</div><br>
							<div class="rss-widget">',
								ama_form('force_import_run', 'Run full import'), ' ',
								ama_form('import_single_item', 'Import next item'),
							'</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>';

	$existing_ids = array_map(function($row) { return $row->item_id; }, $imported);
	
	$articles = ama_xml_to_obj(file_get_contents(AMA_FEED_URL));

	foreach ($articles as $obj) {
		if ($obj && $obj->id && !in_array($obj->id, $existing_ids)) {
			$postdata = ama_article_2_post($obj);
			$metadata = ama_article_2_post_metadata($obj);
			print '<h2>Next Article In Feed</h2>';
			ama_display_dump($obj);
			print '<h2>Post to Insert</h2>';
			ama_display_dump($postdata);
			print '<h2>Post Metadata</h2>';
			ama_display_dump($metadata);
			print '<h2>Attachment</h2>';
			$tmpfile_info = ama_create_attachment_tmpfile_info($obj->featured_image);
			ama_display_dump($tmpfile_info);
			break;
		}
	}
	print '<h2>Import Log</h2>';
	$last_log_lines = nl2br(tail(__DIR__.'/import.log', 120), false);
	ama_display_dump($last_log_lines);
}

function ama_display_dump(&$obj) {
	echo '<textarea readonly style="width:100%;max-width:700px;height:200px;resizable:vertical;">';
	if (is_scalar($obj)) echo $obj;
	else print_r($obj);
	echo '</textarea>';
}

function ama_run_import($single_item=FALSE) {
	global $wpdb;
	
	$imported = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ama_imported ORDER BY `item_id` ASC");
	
	ama_enable_log();
	ama_log(sprintf('Importing feed URL: %s.', AMA_FEED_URL));
	
	$existing_ids = array_map(function($row) { return $row->title; }, $imported);
	$articles = ama_xml_to_obj(file_get_contents(AMA_FEED_URL));
	
	foreach ($articles as $obj) {
		try {
			ama_import_article($obj, $existing_ids);
			$existing_ids[] = $obj->id;
			if ($single_item) break;
		}
		catch (Exception $e) {
			ama_log($e->getMessage());
			continue;
		}
	}
	
	update_option('ama_last_imported_id', $obj->id);
	ama_log(sprintf('Finished importing feed URL: %s.', AMA_FEED_URL));
}

function ama_feed_url($limit=10, $last_id=0) {
	$base = "https://www.africanmediaagency.com/category/english-news-releases/feed";
	$base .= '?'.($last_id > 0 ? "last_id=$last_id" : "order=DESC");
	return $base . "&limit=$limit";
}

function ama_import_latest() {
	global $wpdb;
	
	$feed_url = ama_feed_url(AMA_LATEST_FEED_ARTICLE_LIMIT);
	$existing_ids = array_map(function($n) { return $n; }, $wpdb->get_col("SELECT `title` FROM {$wpdb->prefix}ama_imported ORDER BY `item_id` DESC"));
	
	ama_enable_log();
	ama_log(sprintf('Importing feed URL: %s.', $feed_url));
	
	$articles = ama_xml_to_obj(file_get_contents($feed_url));
	
	foreach ($articles as $obj) {
		try {
			ama_import_article($obj, $existing_ids);
			$existing_ids[] = $obj->id;
		}
		catch (Exception $e) {
			ama_log($e->getMessage());
			continue;
		}
	}
	
	ama_log(sprintf('Finished importing feed URL: %s.', $feed_url));
}

function ama_admin_init() {
	register_importer('ama', 'Ventures: African Media Agency', __('Test the African Media Agency Importer cron job. ARTICLES ARE NOT CREATED HERE.', 'ama-importer'), 'ama_importer');
}
