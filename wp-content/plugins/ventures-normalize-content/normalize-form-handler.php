<?php
set_time_limit(0);
define('NORMALIZE_CONTENT_START_LENGTH', 100);
define('USER_PASSWORDS_FILE', __DIR__.'/users.txt');
define('SECTIONS_SEPARATOR', str_repeat('-', 150));
define('IS_DRY_RUN', isset($_POST['dry-run']));

require_once(dirname(dirname(dirname(__DIR__))) . '/wp-load.php');

$log_handle = fopen(__DIR__.'/migration.log', 'w');
if (!$log_handle) {
	echo 'Could not open log file.';
	exit(1);
}

$updates_handle = fopen(__DIR__.'/post_content_updates.sql', 'w');
if (!$updates_handle) {
	echo 'Could not open post updates file.';
	exit(1);
}

function out($s) {
	global $log_handle;
	fwrite($log_handle, $s."\n");
	if (!IS_DRY_RUN) return; 
	echo "$s<br>";
	ob_flush();
}

if (!isset($_POST['tasks']) || empty($_POST['tasks'])) wp_die(__('No tasks were submitted. Go for a walk if you have nothing to do.'));
if (!current_user_can('edit_posts')) wp_die(__('You do not have sufficient permissions to normalize the content.'));

if (!wp_verify_nonce($_POST['_wpnonce'], 'ventures-content-normalizer')) wp_die(__('Bad request. Nonce verification failed.'));

$tasks = array(
	'Posts' => array('remove_empty_paragraphs','remove_ventures_start','remove_author_start','create_short_urls','opinions_to_ideas'),
	'Users' => array('recover_user_passwords'),
	'Categories' => array('fix_category_hierarchy'),
	'Analytics' => array('convert_popularity_data'),
	'Comments' => array('remove_comments'),
);

if (in_array('create_short_urls', $_POST['tasks']) && !($yourls_options = get_option('yourls_options'))) {
	unset($tasks['Posts'][array_search('create_short_urls', $tasks['Posts'])]);
	out(__('The YOURLS plugin is not setup. Short URLs will not be created.'));
	out(SECTIONS_SEPARATOR);
}

$flat = array_reduce($tasks, function($carry, $item) { return array_merge($carry, $item); }, array());
$submitted = array_filter($_POST['tasks'], function($task) use ($flat) { return in_array($task, $flat); });
$submitted_from_posts = array_intersect($tasks['Posts'], $submitted);

function remove_ventures_start($post) {
	out(SECTIONS_SEPARATOR);
	out(__('Removing "VENTURES AFRICA - ".'));
	$start = substr($post->post_content, 0, NORMALIZE_CONTENT_START_LENGTH);
	out('From: '.strip_tags($start));
	$new = preg_replace('/ventures africa ?(-|—|–|&#8211;)[^\s]*\s/i', '', $start);
	out('To: '.strip_tags($new));
	$new .= substr($post->post_content, NORMALIZE_CONTENT_START_LENGTH);
	$post->post_content = $new;
	return $post;
}

function remove_author_start($post) {
	out(SECTIONS_SEPARATOR);
	out(__('Removing "By {author}".'));
	$start = substr($post->post_content, 0, NORMALIZE_CONTENT_START_LENGTH);
	out('From: '.strip_tags($start));
	$new = preg_replace('/^.*By [^\s]+ [^\s]+\s*/i', '', $start);
	out('To: '.strip_tags($new));
	$new .= substr($post->post_content, NORMALIZE_CONTENT_START_LENGTH);
	$post->post_content = $new;
	return $post;
}

function remove_empty_paragraphs($post) {
	out(SECTIONS_SEPARATOR);
	out(__('Removing empty paragraphs.'));
	$new = preg_replace("/[\n\r]&nbsp;[\n\r]/", '', $post->post_content);
	$post->post_content = $new;
	return $post;
}

function _short_url($args) {
	$build = YOURLSCreator_Helper::run_yourls_api_call('shorturl', $args);
	if (
		(empty($build) && ($err = 'YOURLS Request Error: Empty API response.')) ||
		($build['success'] === FALSE && ($err = sprintf('YOURLS Request Error: [%d] "%s".', $build['errcode'], $build['message']))) ||
		(!isset($build['data']['shorturl']) && ($err = sprintf('YOURLS Request Error: "%s".', $build['message']))) ||
		(empty($build['data']['shorturl']) && ($err = 'YOURLS Response Error: Empty YOURLS URL.'))
	) {
		out(__($err));
		return '';
	}
	return $build['data']['shorturl'];
}

function create_short_urls($post) {
	global $yourls_options;
	out(SECTIONS_SEPARATOR);
	out(__('Generating short URL.'));
	if (IS_DRY_RUN) {
		$shorturl = $yourls_options['url'].'/'.substr(md5(mt_rand()), mt_rand(0, 20), 6);
	}
	else {
		if (!($url = YOURLSCreator_Helper::prepare_api_link($post->ID))) {
			out(__('Error generating the arguments for YOURLS.'));
			return $post;
		}
		$title = $post->post_title;
		if ($shorturl = YOURLSCreator_Helper::get_yourls_meta($post->ID, '_yourls_url')) {
			out(__(sprintf('Post already has a short URL: %s.', $shorturl)));
			return $post;
		}
		if (!($shorturl = _short_url(compact('title','url')))) {
			out(__('Failed to fetch the short URL.'));
			return $post;
		}
		if (!update_post_meta($post->ID, '_yourls_url', $shorturl)) {
			out(__('Failed to save the short URL.'));
			return $post;
		}
		update_post_meta($post->ID, '_yourls_clicks', '0');
	}
	out(__(sprintf('Successfully saved the short URL %s.', $shorturl)));
	return $post;
}

function opinions_to_ideas($post) {
	$opinion_categories = array('opinions','fact-comment','oped');
	$cats = wp_get_object_terms($post->ID, 'category', array('fields' => 'slugs'));
	$op_cats = array_intersect($cats, $opinion_categories);
	if (count($op_cats) > 0) {
		out(SECTIONS_SEPARATOR);
		out(__('Converting opinion post into "Idea".'));
		$post->post_type = 'ventures_ideas';
	}
	return $post;
}

function recover_user_passwords() {
	global $wpdb;
	out(__('Recovering user passwords.'));
	out(SECTIONS_SEPARATOR);
	if (is_file(USER_PASSWORDS_FILE) && is_readable(USER_PASSWORDS_FILE) && ($handle = fopen(USER_PASSWORDS_FILE, 'r'))) {
		$sql = "UPDATE `$wpdb->users` SET `user_pass`=%s WHERE `user_nicename`=%s"; 
		while ($user = fgets($handle)) {
			$parts = explode(':', $user);
			$name = str_replace("\n", '', $parts[1]);
			$pass = $parts[0];
			out(__(sprintf('Setting the password for user "%s" to hash "%s".', $name, $pass)));
			$query = $wpdb->prepare($sql, $pass, $name);
			if (IS_DRY_RUN) out(__(sprintf('Running query "%s".', $query)));
			if (IS_DRY_RUN || $wpdb->query($query)) {
				out(__(sprintf('Password updated successfully for user "%s".', $name)));
			}
			else out(__(sprintf('Password not updated for user "%s".', $name)));
		}
	}
	else out(__('Original user password hashes not found, or could not be read.'));
	out(SECTIONS_SEPARATOR);
}

function fix_category_hierarchy() {
	out(__('Fixing category hierarchies.'));
	$sub_merges = array(array('business','entrepreneurs','investing')); // sub-categories from roots join in single root.
	$merges = array(array('financing','finance-money'),array('travel-tourism','places-to-visit')); // sub-categories merge into one.
	$parent_change = array('lifestyle-travel' => array('sports'), 'leadership' => array('world-affairs','politics'), 'business' => array('ventures-quotes','economic-development'));
	$to_remove = array('opinions','editors-pick','breaking-news','lists','video');
	$renames = array('leadership' => 'policy', 'lifestyle-travel' => 'life', 'financing' => 'finance');

	foreach ($sub_merges as $sub_merge) {
		$dest = get_category_by_slug($sub_merge[0]);
		if (!$dest) {
			out(SECTIONS_SEPARATOR);
			out(__(sprintf('Could not find category "%s".', $sub_merge[0])));
			continue;
		}
		foreach (array_slice($sub_merge, 1) as $src_slug) {
			out(SECTIONS_SEPARATOR);
			$src = get_category_by_slug($src_slug);
			if (!$src) {
				out(__(sprintf('Could not find category "%s".', $src_slug)));
				continue;
			}
			out(__(sprintf('Sending the sub-categories of "%s" into "%s".', $src->name, $dest->name)));
			foreach (get_categories("parent=$src->cat_ID&number=0&hide_empty=0") as $child) {
				if (IS_DRY_RUN || wp_update_term($child->cat_ID, 'category', array('parent' => $dest->cat_ID))) {
					out(__(sprintf('Category "%s" is now under "%s".', $child->name, $dest->name)));
				}
				else out(__(sprintf('Failed to update the parent of the category "%s".', $child->name)));
			}
			if (IS_DRY_RUN || wp_delete_category($src->cat_ID)) {
				out(__(sprintf('Removed now empty category "%s".', $src->name)));
			}
			else out(__(sprintf('Failed to remove the category "%s".', $src->name)));
		}
	}
	foreach ($merges as $merge) {
		$dest = get_category_by_slug($merge[0]);
		if (!$dest) {
			out(SECTIONS_SEPARATOR);
			out(__(sprintf('Could not find category "%s".', $merge[0])));
			continue;
		}
		foreach (array_slice($merge, 1) as $src_slug) {
			out(SECTIONS_SEPARATOR);
			$src = get_category_by_slug($src_slug);
			if (!$src) {
				out(__(sprintf('Could not find category "%s".', $src_slug)));
				continue;
			}
			out(__(sprintf('Marking posts of category "%s" as also belonging to "%s".', $src->name, $dest->name)));
			foreach (get_posts("cat=$src->cat_ID&posts_per_page=-1") as $post) {
				if (IS_DRY_RUN || wp_set_object_terms($post->ID, $dest->cat_ID, 'category', TRUE)) {
					out(__(sprintf('Post "%s" now belongs to category "%s".', $post->post_title, $dest->name)));
				}
				else out(__(sprintf('Failed to add the category "%s" to the post "%s".', $dest->name, $post->post_title)));
			}
			if (IS_DRY_RUN || wp_delete_category($src->cat_ID)) {
				out(__(sprintf('Removed category "%s".', $src->name)));
			}
			else out(__(sprintf('Failed to remove the category "%s".', $src->name)));
		}
	}
	out(SECTIONS_SEPARATOR);
	foreach ($parent_change as $dest_slug => $srcs) {
		$dest = get_category_by_slug($dest_slug);
		if (!$dest) {
			out(__(sprintf('Could not find category "%s".', $dest_slug)));
			continue;
		}
		foreach ($srcs as $src_slug) {
			$src = get_category_by_slug($src_slug);
			if (!$src) {
				out(__(sprintf('Could not find category "%s".', $src_slug)));
				continue;
			}
			if (IS_DRY_RUN || wp_update_term($src->cat_ID, 'category', array('parent' => $dest->cat_ID))) {
				out(__(sprintf('Category "%s" is now under "%s".', $src->name, $dest->name)));
			}
			else out(__(sprintf('Failed to update the parent of the category "%s".', $src->name)));
		}
	}
	out(SECTIONS_SEPARATOR);
	foreach ($to_remove as $slug) {
		$cat = get_category_by_slug($slug);
		if (!$cat) {
			out(__(sprintf('Could not find category "%s".', $slug)));
			continue;
		}
		foreach (get_categories("parent=$cat->cat_ID&number=0&hide_empty=0") as $child) {
			if (IS_DRY_RUN || wp_delete_category($child->cat_ID)) {
				out(__(sprintf('Removed child category "%s".', $child->name)));
			}
			else out(__(sprintf('Failed to remove child category "%s".', $child->name)));
		}
		if (IS_DRY_RUN || wp_delete_category($cat->cat_ID)) {
			out(__(sprintf('Removed category "%s".', $cat->name)));
		}
		else out(__(sprintf('Failed to remove the category "%s".', $cat->name)));
	}
	out(SECTIONS_SEPARATOR);
	foreach ($renames as $from => $to) {
		$src = get_category_by_slug($from);
		if (!$src) {
			out(__(sprintf('Could not find category "%s".', $slug)));
			continue;
		}
		if (IS_DRY_RUN || wp_update_term($src->cat_ID, 'category', array('slug' => $to, 'name' => ucwords($to)))) {
			out(__(sprintf('Renamed category "%s" to "%s".', $from, $to)));
		}
		else out(__(sprintf('Failed to rename the category "%s".', $from)));
	}
	out(SECTIONS_SEPARATOR);
}

function convert_popularity_data() {
	global $wpdb;
	$table = $wpdb->prefix . "popularpostsdata";
	$old_table = $wpdb->prefix . "cn_track_post";
	out(__('Adding the views tracked by Post View Stats to the Wordpress Popular Posts data.'));
	$rows = $wpdb->get_results("SELECT `post_id` AS id
			,count(*) AS views
			,from_unixtime(max(`created_at`)) AS now
		FROM `$old_table` GROUP BY `post_id`",
		ARRAY_A
	);
	if (!$rows || empty($rows)) {
		out(__(sprintf('Could not find Post View Stats in table "%s".', $old_table)));
	}
	else {
		foreach ($rows as $row) {
			extract($row);
			$sql = $wpdb->prepare(
				"INSERT INTO `$table`
				(postid, day, last_viewed, pageviews) VALUES (%d, %s, %s, %d)
				ON DUPLICATE KEY UPDATE pageviews = pageviews + %4\$d, last_viewed = '%3\$s';",
				$id,
				$now,
				$now,
				$views
			);
			if (IS_DRY_RUN || $wpdb->query($sql)) {
				out(__(sprintf('Successfully added %d view(s) for post %d.', $views, $id)));
				if (IS_DRY_RUN || $wpdb->query("DELETE FROM `$old_table` WHERE `post_id`=$id")) {
					out(__(sprintf('Successfully removed the old views for post %d.', $id)));
				}
				else out(__(sprintf('Failed to remove the old views for post %d.', $id)));
			}
			else out(__(sprintf('Failed to add views for post %d.', $id)));
		}
	}
	out(SECTIONS_SEPARATOR);
}

function remove_comments() {
	$comments = get_comments();
	$success = 0;
	out(__(sprintf('Removing %d comments.', count($comments))));
	foreach ($comments as $comment) {
		if (IS_DRY_RUN || wp_delete_comment($comment->comment_ID)) {
			$success++;
		}
		else out(__(sprintf('Failed to delete comment with ID %d from post with ID %d', $comment->comment_ID, $comment->comment_post_ID)));
	}
	if ($success > 0) out(__(sprintf('Successfully removed %d comments.', $success)));
}

function run_posts_tasks($tasks) {
	global $wpdb, $updates_handle;
	$posts = get_posts("posts_per_page=-1&orderby=modified&order=ASC");
	if (IS_DRY_RUN) $posts = array_slice($posts, 0, 10);
	$success = $processed = 0;
	$total = count($posts);
	out(__(sprintf('Updating %d posts.', $total)));
	foreach ($posts as $post) {
		out(SECTIONS_SEPARATOR);
		out(__(sprintf('Updating post %d: "%s".', $post->ID, $post->post_title)));
		foreach ($tasks as $task) $post = call_user_func($task, $post);
		out(SECTIONS_SEPARATOR);
		$processed++;
		$sql = "UPDATE `$wpdb->posts` SET `post_content`=%s, `post_modified`=NOW(), `post_modified_gmt`=NOW() WHERE `ID`=%d"; 
		fwrite($updates_handle, $wpdb->prepare($sql, $post->post_content, $post->ID).";\n");
		out(__('Updates finished for post.'));
		out(__(sprintf('%d posts processed. %d succeded, %d failed. %d remaining.', $processed, $success, $processed - $success, $total - $processed)));
	}
	if ($success > 0) out(__(sprintf('Successfully updated %d posts.', $success)));
	out(SECTIONS_SEPARATOR);
}

if (count($submitted_from_posts)) {
	run_posts_tasks($submitted_from_posts);
}

foreach (array_diff($submitted, $tasks['Posts']) as $task) call_user_func($task);

fclose($log_handle);
fclose($updates_handle);

printf('<br><a href="%s">Back to admin</a>', $_POST['_url']);
