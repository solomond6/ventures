<?php
/*
Plugin Name: APO Africa Importer
Plugin URI: http://www.venturesafrica.com/
Description: Import articles from the feed from APO Africa.
Version: 1.0
Author: Temidayo Uji
Author URI: http://www.venturesafrica.com/
License: GPL
Copyright: Temidayo
*/

define('APO_IMPORT_INTERVAL', 0.5); // hours
define('APO_FEED_ARTICLE_LIMIT', 50);
define('APO_LATEST_FEED_ARTICLE_LIMIT', 20);
//define('APO_DEFAULT_CATEGORY', 17572); // 'News'
define('APO_DEFAULT_CATEGORY', 17925); // 'Apo'
define('APO_FEED_URL', 'https://www.africa-newsroom.com/africarc/rss/Tzo4OiJzdGRDbGFzcyI6Mjp7czo5OiJsYW5ndWFnZXMiO3M6MTk6ImE6MTp7aTowO3M6MjoiZW4iO30iO3M6NDoidHlwZSI7czoxOiJyIjt9');

register_activation_hook(__FILE__, 'apo_activation');
register_deactivation_hook(__FILE__, 'apo_deactivation');
//register_activation_hook(__FILE__, 'apo_check_import_interval');

add_action('apo_import_interval_check', 'apo_check_import_interval');
add_action('admin_init', 'apo_admin_init');
add_action('deleted_post', function($post_id) {
	global $wpdb;
	$wpdb->delete("{$wpdb->prefix}apo_imported", array('post_id' => $post_id));
});

if (!function_exists('tail')) require_once(__DIR__ . '/tail.php');
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
class apo_List_Table extends WP_List_Table {
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

function apo_activation() {
	apo_create_db_table();
	apo_cron_enabled();
	add_option('apo_last_import_timestamp', 0, 'no', 'no');
	add_option('apo_last_imported_id', 0, 'no', 'no');
	//add_option('apo_cron_enabled', 0, 'no', 'no');
}

function apo_deactivation() {
	apo_disable_cron();
	delete_option('apo_last_import_timestamp');
	delete_option('apo_last_imported_id');
	delete_option('apo_cron_enabled');
}

function apo_create_db_table() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'apo_imported';
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

function apo_check_import_interval() {
	$current = time();
	$prev = get_option('apo_last_import_timestamp', 0);
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
	if ($current - $prev > 3600 * APO_IMPORT_INTERVAL) {
		apo_enable_log();
		apo_log("Starting CRON job. ".date('r'));
		//apo_run_import();
		apo_import_latest();
		update_option('apo_last_import_timestamp', time());
		apo_log("Finished CRON job. ".date('r'));
		wp_mail("uji@ventures-africa.com", "APO Africa CRON Job Report.", tail(__DIR__.'/import.log', 65));
	}
}

function apo_enable_log() {
	global $apo_log_handle;
	return $apo_log_handle = @fopen(__DIR__.'/import.log', 'a');
}

function apo_reset_log() {
	global $apo_log_handle;
	$apo_log_handle = fopen(__DIR__.'/import.log', 'w');
}

function apo_log($s) {
	global $apo_log_handle;
	echo $s;
	if (isset($apo_log_handle) && $apo_log_handle) fwrite($apo_log_handle, "$s\n");
	else throw new Exception('log handle not available.');
}

function apo_reset_posts() {
	global $wpdb;
	$posts = get_posts('post_type=ventures_apo&posts_per_page=-1');
	foreach ($posts as $post) {
		apo_log("Deleting post [$post->ID]$post->post_title.");
		wp_delete_post($post->ID, true);
	}
	update_option('apo_last_import_timestamp', 0);
	update_option('apo_last_imported_id', 0);
}

function apo_back_to_index() {
	header("Location: ".admin_url('admin.php?import=aljazeera'));
	exit();
}

function apo_form($method, $title) {
	return sprintf(
		'<form method="POST" action="%s" style="display:inline-block; vertical-align:middle;"><input type="hidden" name="method" value="%s"><input type="submit" name="submit" value="%s" class="button button-primary button-large"></form>', 
		admin_url('admin.php?import=aljazeera'), 
		$method, 
		$title);
}

function apo_importer() {
	if (!empty($_POST)) {
		return apo_form_handler();
	}
	echo '
	<div class="wrap">
		<h2>APO Africa Importer</h2>',
		apo_info(),
	'</div>';
}

function apo_force_reset() {
	apo_reset_log();
	apo_reset_posts();
}

function apo_reset_timestamp() {
	update_option('apo_last_import_timestamp', 0);
}

function apo_prepare_environment() {
	// error_reporting(-1);
	// ini_set('display_errors',false);
	// ini_set('log_errors','1');
	// ini_set('error_log',__DIR__.'/import.log');
}

function apo_force_import_run() {
	apo_prepare_environment();
	apo_run_import();
	update_option('apo_last_import_timestamp', time());
}

function apo_import_single_item() {
	apo_prepare_environment();
	apo_run_import(TRUE);
}

function apo_form_handler() {
	if (isset($_POST['method'])) {
		$fn = "apo_$_POST[method]";
		if (function_exists($fn)) $fn();
	}
	apo_back_to_index();
}

function apo_disable_cron() {
	wp_clear_scheduled_hook('apo_import_interval_check');
	update_option('apo_cron_enabled', 0);
}

function apo_cron_enabled() {
	wp_schedule_event(time(), 'daily', 'apo_import_interval_check');
	update_option('apo_cron_enabled', 1);
}

function apo_article_2_post($obj) {
	$postdata = array(
		'post_content' => $obj->body_text,
		'post_name' => sanitize_title($obj->title),
		'post_title' => $obj->title,
		'post_status' => 'publish',
		'post_type' => 'ventures_apo',
		'post_date' => $obj->date,
		'post_category' => [APO_DEFAULT_CATEGORY],
	);
	return $postdata;
}

function apo_article_2_post_metadata($obj) {
	$metadata = array(
		'item_id' => $obj->id,
		'apo_source_name' => $obj->apo_source_name,
		'apo_url' => $obj->apo_url
	);
	return $metadata;
}

function apo_create_attachment_tmpfile_info($src) {
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

function apo_create_attachment($src, $post_id) {
	$file_array = apo_create_attachment_tmpfile_info($src);
	apo_log("Sideload attachment $src to $file_array[tmp_name].");
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

function apo_save_shortlink($post_id) {
	// get my post URL and title
	$url    = YOURLSCreator_Helper::prepare_api_link( $post_id );
	$title  = get_the_title( $post_id );
	apo_log(sprintf('Creating short URL. Long URL: %s. Title: %s', $url, $title));

	// and optional keyword
	$keywd  = '';

	// set my args for the API call
	$args   = array( 'url' => esc_url( $url ), 'title' => sanitize_text_field( $title ), 'keyword' => $keywd );

	// make the API call
	$build  = YOURLSCreator_Helper::run_yourls_api_call( 'shorturl', $args, FALSE );

	// bail if empty data or error received
	if ( empty( $build ) || false === $build['success'] ) {
		apo_log('Error creating short URL.');
		return;
	}

	// we have done our error checking and we are ready to go
	if( false !== $build['success'] && ! empty( $build['data']['shorturl'] ) ) {
		// get my short URL
		$shorturl   = esc_url( $build['data']['shorturl'] );

		apo_log(sprintf('Short URL created: %s. Saving as post meta...', $shorturl));
		// update the post meta
		add_post_meta( $post_id, '_yourls_url', $shorturl, true );
		add_post_meta( $post_id, '_yourls_clicks', '0', true );
	}
}

function apo_insert_post($obj, $postdata, $metadata) {
	global $wpdb;
	apo_log(sprintf('Importing article [%d]:%s.', $obj->id, $obj->title));
	$post_id = wp_insert_post($postdata, true);
	if (!is_wp_error($post_id)) {
		$wpdb->insert($wpdb->prefix.'apo_imported', array('item_id' => $obj->id, 'post_id' => $post_id, 'title'=> $obj->title));
		foreach ($metadata as $key => $val){
			wp_set_post_terms($post_id, array(APO_DEFAULT_CATEGORY), 'category', true );
			add_post_meta($post_id, $key, $val, true);
		}
		if (!empty($obj->featured_image)) {
			apo_log('Article has image. Importing...');
			$att_id = apo_create_attachment($obj->featured_image, $post_id);
			if (!is_wp_error($att_id)) {
				set_post_thumbnail($post_id, $att_id);
			}
			else apo_log(sprintf('Failed to import attachment %s for [%d]:%s. Error: %s.', $obj->featured_image, $obj->id, $obj->title, $att_id->get_error_message()));
		}
		if (defined('DOING_CRON') && DOING_CRON) apo_save_shortlink($post_id);
	}
	return $post_id;
}

function apo_import_article($obj, $existing_ids) {
	if (empty($obj->id)) {
		throw new Exception('Bad entry. Skip.');
	}
	
	if (in_array($obj->title, $existing_ids)) {
		throw new Exception(sprintf('Article [%d]:%s was already imported. Skip.', $obj->id, $obj->title));
	}
	$postdata = apo_article_2_post($obj);
	$metadata = apo_article_2_post_metadata($obj);
	$post_id = apo_insert_post($obj, $postdata, $metadata);
	if (is_wp_error($post_id)) {
		throw new Exception(sprintf('Article import failed for [%d]:%s. Error: %s.', $obj->id, $obj->title, $post_id->get_error_message()));
	}
}

function apo_xml_to_obj($xmlstring) {
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
    	$apo_source_name = 'Al Jazeera';
    	$apo_url = $id;
    	preg_match("/<p[^>]*>[^<]*<span[^>]*>By\s+<a class=\"colorbox\" href=\"([^\"']+)\">([^<]*)<\/a><\/span>/i", $body, $author);
    	if (!empty($author[1])) {
    		$apo_source_name = $author[2];
    		$apo_url = $author[1];
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
            'apo_source_name' => $apo_source_name,
            'apo_url' => $apo_url,
        ];

        array_push($feed, (object)$item);
    }
    return $feed;
}

function apo_info() {
	global $wpdb;
	$imported = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}apo_imported ORDER BY `item_id` ASC");
	$cron_enabled = get_option('apo_cron_enabled', 0);
	if (strpos($wpdb->last_error, 'apo_imported') !== FALSE) {
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
			date('c', get_option('apo_last_import_timestamp', 0)), 
			$cron_enabled 
				? apo_form('disable_cron', 'Disable') 
				: apo_form('enable_cron', 'Enable'), 
			apo_form('reset_timestamp', 'Reset Schedule')
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
								$table = new apo_List_Table(array_slice($imported, -20));
								$table->prepare_items();
								$table->display();
							}
							echo '
							</div><br>
							<div class="rss-widget">',
								apo_form('force_import_run', 'Run full import'), ' ',
								apo_form('import_single_item', 'Import next item'),
							'</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>';

	$existing_ids = array_map(function($row) { return $row->item_id; }, $imported);
	
	$articles = apo_xml_to_obj(file_get_contents(APO_FEED_URL));

	foreach ($articles as $obj) {
		if ($obj && $obj->id && !in_array($obj->id, $existing_ids)) {
			$postdata = apo_article_2_post($obj);
			$metadata = apo_article_2_post_metadata($obj);
			print '<h2>Next Article In Feed</h2>';
			apo_display_dump($obj);
			print '<h2>Post to Insert</h2>';
			apo_display_dump($postdata);
			print '<h2>Post Metadata</h2>';
			apo_display_dump($metadata);
			print '<h2>Attachment</h2>';
			$tmpfile_info = apo_create_attachment_tmpfile_info($obj->featured_image);
			apo_display_dump($tmpfile_info);
			break;
		}
	}
	print '<h2>Import Log</h2>';
	$last_log_lines = nl2br(tail(__DIR__.'/import.log', 120), false);
	apo_display_dump($last_log_lines);
}

function apo_display_dump(&$obj) {
	echo '<textarea readonly style="width:100%;max-width:700px;height:200px;resizable:vertical;">';
	if (is_scalar($obj)) echo $obj;
	else print_r($obj);
	echo '</textarea>';
}

function apo_run_import($single_item=FALSE) {
	global $wpdb;
	
	$imported = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}apo_imported ORDER BY `item_id` ASC");
	
	apo_enable_log();
	apo_log(sprintf('Importing feed URL: %s.', APO_FEED_URL));
	
	$existing_ids = array_map(function($row) { return $row->title; }, $imported);
	$articles = apo_xml_to_obj(file_get_contents(APO_FEED_URL));
	
	foreach ($articles as $obj) {
		try {
			apo_import_article($obj, $existing_ids);
			$existing_ids[] = $obj->id;
			if ($single_item) break;
		}
		catch (Exception $e) {
			apo_log($e->getMessage());
			continue;
		}
	}
	
	update_option('apo_last_imported_id', $obj->id);
	apo_log(sprintf('Finished importing feed URL: %s.', APO_FEED_URL));
}

function apo_feed_url($limit=10, $last_id=0) {
	$base = "https://www.africa-newsroom.com/africarc/rss/Tzo4OiJzdGRDbGFzcyI6Mjp7czo5OiJsYW5ndWFnZXMiO3M6MTk6ImE6MTp7aTowO3M6MjoiZW4iO30iO3M6NDoidHlwZSI7czoxOiJyIjt9";
	$base .= '?'.($last_id > 0 ? "last_id=$last_id" : "order=DESC");
	return $base . "&limit=$limit";
}

function apo_import_latest() {
	global $wpdb;
	
	$feed_url = apo_feed_url(APO_LATEST_FEED_ARTICLE_LIMIT);
	$existing_ids = array_map(function($n) { return $n; }, $wpdb->get_col("SELECT `title` FROM {$wpdb->prefix}apo_imported ORDER BY `item_id` DESC"));
	
	apo_enable_log();
	apo_log(sprintf('Importing feed URL: %s.', $feed_url));
	
	$articles = apo_xml_to_obj(file_get_contents($feed_url));
	
	foreach ($articles as $obj) {
		try {
			apo_import_article($obj, $existing_ids);
			$existing_ids[] = $obj->id;
		}
		catch (Exception $e) {
			apo_log($e->getMessage());
			continue;
		}
	}
	
	apo_log(sprintf('Finished importing feed URL: %s.', $feed_url));
}

function apo_admin_init() {
	register_importer('apo', 'Ventures: APO Africa', __('Test the APo Africa Importer cron job. ARTICLES ARE NOT CREATED HERE.', 'apo-importer'), 'apo_importer');
}
