<?php
/*
Plugin Name: Al-Jazeera Importer
Plugin URI: http://www.versionindustries.com/
Description: Import articles from the Al-Jazeera feed from African Media Agency LLC.
Version: 1.0
Author: version (v)
Author URI: http://www.versionindustries.com/
License: GPL
Copyright: version (v)
*/

define('AJ_IMPORT_INTERVAL', 0.5); // hours
define('AJ_FEED_ARTICLE_LIMIT', 50);
define('AJ_LATEST_FEED_ARTICLE_LIMIT', 20);
define('AJ_DEFAULT_CATEGORY', 17572); // 'News'
define('AJ_FEED_URL', 'http://amediaagency.com/category/news-releases/feed/');

register_activation_hook(__FILE__, 'aj_activation');
register_deactivation_hook(__FILE__, 'aj_deactivation');
add_action('aj_import_interval_check', 'aj_check_import_interval');
add_action('admin_init', 'aj_admin_init');
add_action('deleted_post', function($post_id) {
	global $wpdb;
	$wpdb->delete("{$wpdb->prefix}aj_imported", array('post_id' => $post_id));
});

if (!function_exists('tail')) require_once(__DIR__ . '/tail.php');
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
class AJ_List_Table extends WP_List_Table {
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

function aj_activation() {
	aj_create_db_table();
	add_option('aj_last_import_timestamp', 0, 'no', 'no');
	add_option('aj_last_imported_id', 0, 'no', 'no');
	add_option('aj_cron_enabled', 0, 'no', 'no');
}

function aj_deactivation() {
	aj_disable_cron();
	delete_option('aj_last_import_timestamp');
	delete_option('aj_last_imported_id');
	delete_option('aj_cron_enabled');
}

function aj_create_db_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . "aj_imported";
	$charset_collate = $wpdb->get_charset_collate();
	$sql = "CREATE TABLE $table_name (
	  item_id varchar(255) NOT NULL,
	  post_id int(11) NOT NULL,
	  PRIMARY KEY  (item_id),
	  UNIQUE KEY post_id (post_id)
	) $charset_collate;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}

function aj_check_import_interval() {
	$current = time();
	$prev = get_option('aj_last_import_timestamp', 0);
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
	if ($current - $prev > 3600 * AJ_IMPORT_INTERVAL) {
		aj_enable_log();
		aj_log("Starting CRON job. ".date('r'));
		aj_run_import();
		aj_import_latest();
		update_option('aj_last_import_timestamp', time());
		aj_log("Finished CRON job. ".date('r'));
		wp_mail("giles@versionindustries.com", "Al-Jazeera CRON Job Report.", tail(__DIR__.'/import.log', 65));
	}
}

function aj_enable_log() {
	global $aj_log_handle;
	return $aj_log_handle = @fopen(__DIR__.'/import.log', 'a');
}

function aj_reset_log() {
	global $aj_log_handle;
	$aj_log_handle = fopen(__DIR__.'/import.log', 'w');
}

function aj_log($s) {
	global $aj_log_handle;
	echo $s;
	if (isset($aj_log_handle) && $aj_log_handle) fwrite($aj_log_handle, "$s\n");
	else throw new Exception('log handle not available.');
}

function aj_reset_posts() {
	global $wpdb;
	$posts = get_posts('post_type=ventures_aj&posts_per_page=-1');
	foreach ($posts as $post) {
		aj_log("Deleting post [$post->ID]$post->post_title.");
		wp_delete_post($post->ID, true);
	}
	update_option('aj_last_import_timestamp', 0);
	update_option('aj_last_imported_id', 0);
}

function aj_back_to_index() {
	header("Location: ".admin_url('admin.php?import=aljazeera'));
	exit();
}

function aj_form($method, $title) {
	return sprintf(
		'<form method="POST" action="%s" style="display:inline-block; vertical-align:middle;"><input type="hidden" name="method" value="%s"><input type="submit" name="submit" value="%s" class="button button-primary button-large"></form>', 
		admin_url('admin.php?import=aljazeera'), 
		$method, 
		$title);
}

function aj_importer() {
	if (!empty($_POST)) {
		return aj_form_handler();
	}
	echo '
	<div class="wrap">
		<h2>Al-Jazeera Importer</h2>',
		aj_info(),
	'</div>';
}

function aj_force_reset() {
	aj_reset_log();
	aj_reset_posts();
}

function aj_reset_timestamp() {
	update_option('aj_last_import_timestamp', 0);
}

function aj_prepare_environment() {
	// error_reporting(-1);
	// ini_set('display_errors',false);
	// ini_set('log_errors','1');
	// ini_set('error_log',__DIR__.'/import.log');
}

function aj_force_import_run() {
	aj_prepare_environment();
	aj_run_import();
	update_option('aj_last_import_timestamp', time());
}

function aj_import_single_item() {
	aj_prepare_environment();
	aj_run_import(TRUE);
}

function aj_form_handler() {
	if (isset($_POST['method'])) {
		$fn = "aj_$_POST[method]";
		if (function_exists($fn)) $fn();
	}
	aj_back_to_index();
}

function aj_disable_cron() {
	wp_clear_scheduled_hook('aj_import_interval_check');
	update_option('aj_cron_enabled', 0);
}

function aj_enable_cron() {
	wp_schedule_event(time(), 'hourly', 'aj_import_interval_check');
	update_option('aj_cron_enabled', 1);
}

function aj_article_2_post($obj) {
	$postdata = array(
		'post_content' => $obj->body_text,
		'post_name' => sanitize_title($obj->title),
		'post_title' => $obj->title,
		'post_status' => 'pending',
		'post_type' => 'ventures_aj',
		'post_date' => $obj->date,
		'post_category' => [AJ_DEFAULT_CATEGORY],
	);
	return $postdata;
}

function aj_article_2_post_metadata($obj) {
	$metadata = array(
		'item_id' => $obj->id,
		'tn_source_name' => $obj->tn_source_name,
		'tn_url' => $obj->tn_url
	);
	return $metadata;
}

function aj_create_attachment_tmpfile_info($src) {
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

function aj_create_attachment($src, $post_id) {
	$file_array = aj_create_attachment_tmpfile_info($src);
	aj_log("Sideload attachment $src to $file_array[tmp_name].");
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

function aj_save_shortlink($post_id) {
	// get my post URL and title
	$url    = YOURLSCreator_Helper::prepare_api_link( $post_id );
	$title  = get_the_title( $post_id );
	aj_log(sprintf('Creating short URL. Long URL: %s. Title: %s', $url, $title));

	// and optional keyword
	$keywd  = '';

	// set my args for the API call
	$args   = array( 'url' => esc_url( $url ), 'title' => sanitize_text_field( $title ), 'keyword' => $keywd );

	// make the API call
	$build  = YOURLSCreator_Helper::run_yourls_api_call( 'shorturl', $args, FALSE );

	// bail if empty data or error received
	if ( empty( $build ) || false === $build['success'] ) {
		aj_log('Error creating short URL.');
		return;
	}

	// we have done our error checking and we are ready to go
	if( false !== $build['success'] && ! empty( $build['data']['shorturl'] ) ) {
		// get my short URL
		$shorturl   = esc_url( $build['data']['shorturl'] );

		aj_log(sprintf('Short URL created: %s. Saving as post meta...', $shorturl));
		// update the post meta
		add_post_meta( $post_id, '_yourls_url', $shorturl, true );
		add_post_meta( $post_id, '_yourls_clicks', '0', true );
	}
}

function aj_insert_post($obj, $postdata, $metadata) {
	global $wpdb;
	aj_log(sprintf('Importing article [%d]:%s.', $obj->id, $obj->title));
	$post_id = wp_insert_post($postdata, true);
	if (!is_wp_error($post_id)) {
		$wpdb->insert($wpdb->prefix.'aj_imported', array('item_id' => $obj->id, 'post_id' => $post_id));
		foreach ($metadata as $key => $val) add_post_meta($post_id, $key, $val, true);
		if (!empty($obj->featured_image)) {
			aj_log('Article has image. Importing...');
			$att_id = aj_create_attachment($obj->featured_image, $post_id);
			if (!is_wp_error($att_id)) {
				set_post_thumbnail($post_id, $att_id);
			}
			else aj_log(sprintf('Failed to import attachment %s for [%d]:%s. Error: %s.', $obj->featured_image, $obj->id, $obj->title, $att_id->get_error_message()));
		}
		if (defined('DOING_CRON') && DOING_CRON) aj_save_shortlink($post_id);
	}
	return $post_id;
}

function aj_import_article($obj, $existing_ids) {
	if (empty($obj->id)) {
		throw new Exception('Bad entry. Skip.');
	}
	if (in_array($obj->id, $existing_ids)) {
		throw new Exception(sprintf('Article [%d]:%s was already imported. Skip.', $obj->id, $obj->title));
	}
	$postdata = aj_article_2_post($obj);
	$metadata = aj_article_2_post_metadata($obj);
	$post_id = aj_insert_post($obj, $postdata, $metadata);
	if (is_wp_error($post_id)) {
		throw new Exception(sprintf('Article import failed for [%d]:%s. Error: %s.', $obj->id, $obj->title, $post_id->get_error_message()));
	}
}

function aj_xml_to_obj($xmlstring) {
	$rss = new DOMDocument();
    $rss->loadXML($xmlstring);
    $feed = array();
    foreach ($rss->getElementsByTagName('item') as $node) {
    	$id = $node->getElementsByTagName('link')->item(0)->nodeValue;
    	$featured_image = '';
    	$date = $node->getElementsByTagName('pubDate')->item(0)->nodeValue;

    	// try to pull image out of the body
    	$body = $node->getElementsByTagName('encoded')->item(0)->nodeValue;
    	preg_match("/<p[^>]*>[^<]*<img.+src=[\"']([^\"']+)[\"'][^>]*>[^<]*<\/p>/i", $body, $matches);
    	if (!empty($matches[1])) {
    		$featured_image = $matches[1];
    		$body = str_replace($matches[0], '', $body);
    	}

    	// try to pull author out of the body
    	$tn_source_name = 'Al Jazeera';
    	$tn_url = $id;
    	preg_match("/<p[^>]*>[^<]*<span[^>]*>By\s+<a class=\"colorbox\" href=\"([^\"']+)\">([^<]*)<\/a><\/span>/i", $body, $author);
    	if (!empty($author[1])) {
    		$tn_source_name = $author[2];
    		$tn_url = $author[1];
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
            'tn_source_name' => $tn_source_name,
            'tn_url' => $tn_url,
        ];

        array_push($feed, (object)$item);
    }
    return $feed;
}

function aj_info() {
	global $wpdb;
	$imported = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aj_imported ORDER BY `item_id` ASC");
	$cron_enabled = get_option('aj_cron_enabled', 0);
	if (strpos($wpdb->last_error, 'aj_imported') !== FALSE) {
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
			date('c', get_option('aj_last_import_timestamp', 0)), 
			$cron_enabled 
				? aj_form('disable_cron', 'Disable') 
				: aj_form('enable_cron', 'Enable'), 
			aj_form('reset_timestamp', 'Reset Schedule')
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
								$table = new AJ_List_Table(array_slice($imported, -20));
								$table->prepare_items();
								$table->display();
							}
							echo '
							</div><br>
							<div class="rss-widget">',
								aj_form('force_import_run', 'Run full import'), ' ',
								aj_form('import_single_item', 'Import next item'),
							'</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>';

	$existing_ids = array_map(function($row) { return $row->item_id; }, $imported);
	
	$articles = aj_xml_to_obj(file_get_contents(AJ_FEED_URL));

	foreach ($articles as $obj) {
		if ($obj && $obj->id && !in_array($obj->id, $existing_ids)) {
			$postdata = aj_article_2_post($obj);
			$metadata = aj_article_2_post_metadata($obj);
			print '<h2>Next Article In Feed</h2>';
			aj_display_dump($obj);
			print '<h2>Post to Insert</h2>';
			aj_display_dump($postdata);
			print '<h2>Post Metadata</h2>';
			aj_display_dump($metadata);
			print '<h2>Attachment</h2>';
			$tmpfile_info = aj_create_attachment_tmpfile_info($obj->featured_image);
			aj_display_dump($tmpfile_info);
			break;
		}
	}
	print '<h2>Import Log</h2>';
	$last_log_lines = nl2br(tail(__DIR__.'/import.log', 120), false);
	aj_display_dump($last_log_lines);
}

function aj_display_dump(&$obj) {
	echo '<textarea readonly style="width:100%;max-width:700px;height:200px;resizable:vertical;">';
	if (is_scalar($obj)) echo $obj;
	else print_r($obj);
	echo '</textarea>';
}

function aj_run_import($single_item=FALSE) {
	global $wpdb;
	
	$imported = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aj_imported ORDER BY `item_id` ASC");
	
	aj_enable_log();
	aj_log(sprintf('Importing feed URL: %s.', AJ_FEED_URL));
	
	$existing_ids = array_map(function($row) { return $row->item_id; }, $imported);
	$articles = aj_xml_to_obj(file_get_contents(AJ_FEED_URL));
	
	foreach ($articles as $obj) {
		try {
			aj_import_article($obj, $existing_ids);
			$existing_ids[] = $obj->id;
			if ($single_item) break;
		}
		catch (Exception $e) {
			aj_log($e->getMessage());
			continue;
		}
	}
	
	update_option('aj_last_imported_id', $obj->id);
	aj_log(sprintf('Finished importing feed URL: %s.', AJ_FEED_URL));
}

function aj_import_latest() {
	global $wpdb;
	
	$feed_url = aj_feed_url(AJ_LATEST_FEED_ARTICLE_LIMIT);
	$existing_ids = array_map(function($n) { return $n; }, $wpdb->get_col("SELECT `item_id` FROM {$wpdb->prefix}aj_imported ORDER BY `item_id` DESC"));
	
	aj_enable_log();
	aj_log(sprintf('Importing feed URL: %s.', $feed_url));
	
	$articles = aj_xml_to_obj(file_get_contents($feed_url));
	
	foreach ($articles as $obj) {
		try {
			aj_import_article($obj, $existing_ids);
			$existing_ids[] = $obj->id;
		}
		catch (Exception $e) {
			aj_log($e->getMessage());
			continue;
		}
	}
	
	aj_log(sprintf('Finished importing feed URL: %s.', $feed_url));
}

function aj_admin_init() {
	register_importer('aljazeera', 'Ventures: Al-Jazeera', __('Test the Al-Jazeera Importer cron job. ARTICLES ARE NOT CREATED HERE.', 'aj-importer'), 'aj_importer');
	$handle = aj_enable_log();
	if (!$handle) {
		echo '
		<div class="error notice">
			<p>Al-Jazeera: import log file was not writable.</p>
		</div>';
	}
}
