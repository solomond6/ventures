<?php

function get_selected_featured_post_id($query=NULL) {
	global $wp_the_query;
	if (!$query) $query = $wp_the_query;
	$key = $query->is_home() ? 'home' : (
		$query->is_post_type_archive() ? str_replace('ventures_', '', $query->query_vars['post_type']) : (
			$query->is_category() ? $query->query_vars['category_name'] : ''
		)
	);
	if (empty($key)) return array();
	$val = get_field("featured_$key", FEATURED_SETTINGS_PAGE_ID, false);
	return is_array($val) ? $val : array();
}

function get_current_section_top_stories_ids($query) {
	global $wpdb;
	$top = array();
	$featured = get_selected_featured_post_id($query);
	define('FEATURED_POST_COUNT', count($featured));
	$popular_sql = get_most_popular_sql_from_wp_query($query);
	$popular = empty($popular_sql) ? array() : $wpdb->get_col($popular_sql);
	return get_top_stories_ids($featured, $popular);
}

function get_global_top_stories_ids() {
	global $wpdb;
	$featured = get_field("featured_home", FEATURED_SETTINGS_PAGE_ID, false);
	$popular = $wpdb->get_col(get_most_popular_sql());
	return get_top_stories_ids($featured, $popular);
}

function get_top_stories_ids($featured, $popular) {
	$top = array();
	foreach (array_merge($featured, $popular) as $s_id) {
		$top[$s_id] = intval($s_id);
	}
	// count - first featured - 1 advert
	return array_slice(array_values($top), 0, TOP_STORIES_MAX_COUNT - 2);
}

function get_most_popular_sql($filters=array()) {
	global $wpdb;
	$recent = "SELECT `ID` FROM `$wpdb->posts` ";
	$sql = "
		SELECT posts.ID,
			1 - least(DATEDIFF(NOW(), post_date), 14) / 14 AS age,
			(CASE priority.`meta_value`
				WHEN 'high' THEN 0.5
				WHEN 'low' THEN -0.5
				ELSE 0
			END) AS priority,
			(CASE ISNULL(postid)
				WHEN TRUE THEN 0
				ELSE 0.75 * pageviews / (SELECT MAX(`pageviews`) FROM `wp_popularpostsdata`)
			END) AS views
		FROM `$wpdb->posts` posts
		LEFT JOIN `{$wpdb->prefix}popularpostsdata` ON `postid`=posts.`ID` 
		LEFT JOIN `wp_postmeta` priority ON posts.ID=`post_id` AND `meta_key`='priority'
	";
	$sqls = compact('sql','recent');
	foreach ($filters as $key => $val) $sqls = apply_filters("top_stories_{$key}_filter", $sqls, $val);
	extract($sqls);
	
	if (!isset($filters['post_type'])) $recent .= "WHERE `post_type` IN ('".str_replace(',',"','",VENTURES_ARTICLES_POST_TYPES)."') ";
	$recent .= " AND `post_status`='publish' ORDER BY `post_date` DESC LIMIT 0,100";
	$ids = $wpdb->get_col($recent);
	if (empty($ids)) return '';
	
	$sql .= " WHERE `ID` IN (".implode(',', $ids).") ORDER BY age + views + priority DESC LIMIT 0,".(TOP_STORIES_MAX_COUNT * 3);
	
	return $sql;
}

add_filter('top_stories_author_filter', function($sqls, $author) {
	global $wpdb;
	$sqls['sql'] .= "JOIN `$wpdb->users` ON `post_author`=`$wpdb->users`.`ID` AND `user_login`='$author' ";
	return $sqls;
}, 10, 2);

add_filter('top_stories_term_ids_filter', function($sqls, $term_ids) {
	global $wpdb;
	if (is_array($term_ids)) $term_ids = implode(',', $term_ids);
	$sqls['sql'] .= "JOIN `$wpdb->term_relationships` post_terms ON posts.ID=`object_id` JOIN `$wpdb->term_taxonomy` terms ON terms.term_taxonomy_id=post_terms.term_taxonomy_id AND `term_id` IN (".$term_ids.")";
	return $sqls;
}, 10, 2);

add_filter('top_stories_post_type_filter', function($sqls, $post_type) {
	$sqls['recent'] .= "WHERE `post_type`='{$post_type}' ";
	return $sqls;
}, 10, 2);

function get_most_popular_sql_from_wp_query($query) {
	$filters = array();
	if ($query->is_author()) $filters['author'] = $query->query_vars['author_name'];
	if ($query->is_category() || $query->is_tag()) $filters['term_ids'] = QUERY_TERM_IDS;
	if ($query->is_post_type_archive()) $filters['post_type'] = $query->query_vars['post_type'];
	return get_most_popular_sql($filters);
}

function ventures_topstory_to_js_obj($post) {
	return [
		'url' => get_permalink($post->ID),
		'cat_slug' => $post->cat_slug,
		'cat_name' => $post->cat_name,
		'title' => $post->post_title,
		'img' => ventures_resp_img(0, $post->img_meta_value)
	];
}

function add_top_stories($vars) {
	global $post, $_POSTS;
	if (is_single() && !ventures_is_mobile()) {
		$vars['TOP_STORIES'] = array_map('ventures_topstory_to_js_obj', ventures_get_posts_by_id(array_slice(get_global_top_stories_ids(), 0, 4)));
	}
	return $vars;
}
add_filter( 'ventures_js_vars', 'add_top_stories' );
