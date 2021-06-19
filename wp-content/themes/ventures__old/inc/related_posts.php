<?php
function ventures_related_post_settings() {
	add_settings_field('related_posts_limit', 'Post Count Limit', function() {
		echo '<input name="related_posts_limit" id="related_posts_limit" type="number" value="'.get_option('related_posts_limit', 4).'">';
	}, 'reading', 'related_posts');
	add_settings_field('get_related_posts', 'Test Related Posts', function() {
		echo '<input name="related_posts_id" id="related_posts_id" type="number" value="" placeholder="Post ID"><button id="related_posts_button">Show Related Posts</button>';
	}, 'reading', 'related_posts');
	add_settings_section('related_posts', 'Related Posts', function() {
	}, 'reading');
	register_setting('reading', 'related_posts_limit');

	add_action('admin_enqueue_scripts', function() {
		wp_enqueue_script('related_posts', get_template_directory_uri().'/inc/related_posts.js' );
		wp_localize_script("jquery", 'VENTURES', array('AJAX_URL' => admin_url('admin-ajax.php')));
	});
}

add_action('wp_ajax_related_posts', function() {
	global $wpdb;
	$post_id = intval($_POST['id']);
	$filters = get_related_posts_filters();
	$sql = get_related_posts_sql($post_id, $filters['where'], $filters['limit']);
	$ids = $wpdb->get_col($sql);
	echo "<p>SQL Query: $sql</p>";
	echo '<p>Result: '.implode(',', $ids).'</p>';
	
	printf('<p>Where: %s, Limit: %s</p>', $filters['where'], $filters['limit']);
	$related_posts = get_related_posts($post_id);
	printf('<p>Related Posts Count: %d</p>', count($related_posts));
	array_walk($related_posts, function($p) {
		printf('<p>[%d]: %s</p>', $p->ID, $p->post_title);
	});
	exit;
});

function related_post($local_post) {
	global $post;
	$global_post = $post;
	$post = $local_post;

	$thumb_data = ventures_post_image_data($post);
	$image = ventures_resp_img(isset($post->thumb_id) ? $post->thumb_id : 0, $thumb_data);
	$image['data-original-width'] = $thumb_data ? $thumb_data['width'] : 0;
	$image['sizes'] = '100vw';
	$image['class'] = 'thumb';
	if ($post->post_type === 'ventures_tn' && (empty($post->truncate_disabled) || $post->truncate_disabled[0] !== 'disabled')) {
		$post->post_content = tn_truncate_content($post->post_content);
	}
	$author = $post->post_type === 'ventures_tn' ? array(
		'name' => empty($post->source_name) ? str_replace('www.', '', parse_url($post->source_url, PHP_URL_HOST)) : $post->source_url,
		'url' => $post->source_url,
	) : array(
		'name' => trim(empty($post->author_name) ? $post->author_dname : $post->author_name),
		'url' => ventures_author_url($post->author_slug),
	);
	$arr = array(
		'id' => $post->ID,
		'title' => $post->post_title,
		'image' => strpos($image['src'], 'blank.gif') ? NULL : $image,
		'image_alt' => '',
		'credit_html' => ventures_featured_image_credit($post->ID, $post->credit, $post->credit_url),
		'class' => array("type-$post->post_type", ($post->credit ? 'has' : 'no').'-credit'),
		'author' => $author,
		'category_label' => get_top_story_title_label($post),
		'date' => date('F j, Y', strtotime($post->post_date)),
		'content' => apply_filters('the_content', $post->post_content),
		'permalink' => get_the_permalink($post),
		'short_url' => wp_get_shortlink($post->ID, 'post'),
		'share_links' => get_post_sharing_links($post),
	);

	$post = $global_post;

	return $arr;
}

function get_related_posts_sql($post_id, $where, $limit) {
	global $wpdb, $post;
	if (!$post_id && $post) {
		$post_id = $post->ID;
	}
	if (!$post_id) return array();
	$sql = "
		SELECT `object_id`
		FROM `wp_term_relationships` post_terms
			JOIN `wp_term_taxonomy` USING (`term_taxonomy_id`) 
			JOIN `wp_posts` ON post_terms.`object_id`=`wp_posts`.`ID`
		WHERE post_terms.`term_taxonomy_id` IN (
				SELECT `term_taxonomy_id`
				FROM `wp_term_relationships`
				WHERE `object_id`=$post_id
			)
			$where
			AND `wp_posts`.`post_status`='publish'
			AND post_terms.`object_id`!=$post_id
		GROUP BY `object_id`
		ORDER BY `object_id` DESC
		$limit
	";
	return $sql;
}

function get_related_posts_filters() {
	global $post;
	if (is_single()) {
		$post_type = $post->post_type;
	}
	elseif (is_post_type_archive()) {
		$post_type = get_query_var('post_type');
	}
	else {
		$post_type = 'post';
	}
	$where = "AND `post_type`='$post_type'";
	$limit = 'LIMIT 0,'.get_option('related_posts_limit', 4);
	return compact('where','limit');
}

function get_related_posts($post_id=0) {
	global $wpdb, $post;
	$filters = get_related_posts_filters();
	$ids = $wpdb->get_col(get_related_posts_sql($post_id, $filters['where'], $filters['limit']));
	return empty($ids) ? array() : ventures_get_posts_by_id($ids, true);
}

function add_related_posts( $vars ) {
	global $post, $_POSTS;
	if (is_single() && !ventures_is_mobile()) {
		$vars['RELATED_POSTS'] = array_map('related_post', get_related_posts($post->ID));
	}
	return $vars;
}
add_filter( 'ventures_js_vars', 'add_related_posts' );


