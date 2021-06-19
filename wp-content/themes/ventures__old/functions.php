<?php
/**
 * ventures functions and definitions
 *
 * @package ventures
 */
if (!defined('FEATURED_SETTINGS_PAGE_ID')) define('FEATURED_SETTINGS_PAGE_ID', 63291);
if (!defined('INLINE_ADS_SETTINGS_PAGE_ID')) define('INLINE_ADS_SETTINGS_PAGE_ID', 71419);
if (!defined('MAINTENANCE_MODE_ENABLED')) define('MAINTENANCE_MODE_ENABLED', FALSE);
define('UNCATEGORIZED_TERM_ID', 1);
define('LATEST_ARTICLES_PER_PAGE', 6);
define('TOP_STORIES_MAX_COUNT', 8);
define('LATEST_ARTICLES_PAGE_COUNT', (ventures_is_mobile() ? 1 : 3));
define('INTERVIEW_URL_REGEX', '/http(?:s)?:\/\/(vimeo|soundcloud)\.com\/(.+)/');
define('VENTURES_ARTICLES_POST_TYPES','post,ventures_interviews,ventures_ideas,ventures_features,ventures_tn,ventures_apo,ventures_ama');
define('VENTURES_LATEST_ARTICLES_POST_TYPES','post,ventures_tn,ventures_apo,ventures_ama');
define('TN_AUTO_TRUNCATE_PARAGRAPH_COUNT', 3);
define('V_ASSETS_VERSION', '4.3.5');

require(__DIR__.'/inc/related_posts.php');
require(__DIR__.'/inc/top_stories.php');

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if (strpos($_SERVER['REQUEST_URI'], 'wp-content/uploads') !== FALSE) {
	// this is a 404.
	status_header( 404 );
	nocache_headers();
	header('HTTP/1.0 404 Not Found');
	exit();
}

add_action('wp_ajax_nopriv_ventures_ajax_search', 'ventures_ajax_search');
add_action('wp_ajax_ventures_ajax_search', 'ventures_ajax_search');
function ventures_ajax_search()
{
	$args = array(
		's' => stripslashes($_POST['search']),
		'post_status' => 'publish',
		'posts_per_page' => 5
	);
	$the_query = new WP_Query( $args );
	if ($the_query->have_posts()) {
		echo '<ul>';
		while ($the_query->have_posts()) {
			$the_query->the_post();
			ventures_post_search_teaser();
		}
		echo '</ul>';
	}
	else {
		echo '<p class="search-no-results">No results were found.</p>';
	}
	wp_reset_postdata();
	exit;
}

function register_ventures_post_type($slug, $singular='', $plural='', $rewrite = null, $supports=array(), $settings=array()) {
	if (empty($singular)) $singular = ucwords(substr($slug, 0, strlen($slug) - 1));
	if (empty($plural)) $plural = $singular.'s';
	$rewrite_slug = ($rewrite) ? $rewrite : $slug;
	register_post_type("ventures_$slug", array_merge(array(
		'public' => true,
		'label' => $plural,
		'labels' => array(
			'singular_name' => $singular,
			'add_new_item' => "Add New $singular",
		),
		'supports' => empty($supports) ? array('title','editor','author','thumbnail') : $supports,
		'taxonomies' => array('category','post_tag'),
		'menu_position' => 5,
		'has_archive' => true,
		'rewrite' => array(
			'slug' => $rewrite_slug,
		),
	), $settings));
}

function ventures_init() {
	if (MAINTENANCE_MODE_ENABLED && !is_user_logged_in()) {
		wp_redirect(site_url('maintenance.html'), 302);
		exit();
	}
	register_nav_menu('burger', __('Burger Menu'));
	register_nav_menu('footer', __('Footer Menu'));
	$news_type = get_post_type_object('post');
	$news_type->label = 'News Posts';
	$news_type->labels->name = 'News Posts';
	$news_type->labels->menu_name = 'News Posts';
	register_post_type('post', $news_type);
	// side menu label is hardcoded, but uses __.
	if (is_admin()) {
		add_filter('gettext', function($s) { return $s === 'Posts' ? 'News Posts' : $s; });
	}
	register_ventures_post_type('features', 'Feature Piece');
	register_ventures_post_type('feature_ads', 'Feature Ad', null, 'advertisement', array('title','thumbnail'), array(
		'menu_icon' => 'dashicons-archive',
		'taxonomies' => array(),
	));
	register_ventures_post_type('ideas');
	register_ventures_post_type('interviews');
	register_ventures_post_type('briefs');
	register_ventures_post_type('apo', 'APO Africa Article', null, 'apostories', array('title','editor','custom-fields'));
	register_ventures_post_type('tn', 'Affiliate Article', null, 'story', array('title','editor','custom-fields'));
	register_ventures_post_type('aj', 'Al-Jazeera Article', null, 'stories', array('title','editor','custom-fields'));
	register_ventures_post_type('ama', 'African Media Agency Article', null, 'amastories', array('title','editor','custom-fields'));
}
add_action('init', 'ventures_init');

function smartwp_remove_wp_block_library_css(){
 wp_dequeue_style( 'wp-block-library' );
 wp_dequeue_style( 'wp-block-library-theme' );
}
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css' );

function ventures_editor_settings() {
	$remove_from_first = array('strikethrough','wp_more','wpUserAvatar','wp_adv');
	$remove_from_second = array('alignjustify');
	add_editor_style();
	add_filter('mce_buttons', function($buttons) use ($remove_from_first) {
		return array_diff($buttons, $remove_from_first);
	});
	add_filter('mce_buttons_2', function($buttons) use ($remove_from_second) {
		return array_diff($buttons, $remove_from_second);
	});
	add_filter('mce_external_plugins', function($plugins) {
		$plugins['venturesTheme'] = get_stylesheet_directory_uri().'/js/tinymce-plugin.js';
		return $plugins;
	});
	add_filter('tiny_mce_before_init', function($args) {
		$allowed_format_tags = array('p','h3');
		$formats = isset($args['block_formats']) ? array_map(function($format) { return explode('=', $format); }, explode(';', $args['block_formats'])) : array();
		$allowed = array_filter($formats, function($format) use ($allowed_format_tags) {
			return in_array($format[1], $allowed_format_tags);
		});
		$args['block_formats'] = implode(';', array_map(function($format) { return $format[0].'='.$format[1]; }, $allowed));
		return $args;
	});
}
function ventures_admin_init() {
	ventures_related_post_settings();
	ventures_editor_settings();
}
add_action('admin_init', 'ventures_admin_init');

add_action('ventures_404', function() {
	global $wp_the_query;
	$wp_the_query->set_404();
	status_header( 404 );
	nocache_headers();
	include( get_query_template( '404' ) );
	die();
});

if ( ! function_exists( 'ventures_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
$ventures_img_responsive_sizes = [320, 640, 768, 1536, 3072];
$ventures_img_default_size     = 320;
$ventures_img_size_prefix      = 'w-';
function ventures_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on ventures, use a find and replace
	 * to change 'ventures' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'ventures', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );

	global $ventures_img_responsive_sizes,
	       $ventures_img_size_prefix;
	foreach($ventures_img_responsive_sizes as $width) {
		add_image_size($ventures_img_size_prefix.$width, $width);
	}

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	add_filter('wp_title', function($s) {
		if (is_archive()) {
			$s = str_replace(array('Archives ','Archive '), '', $s);
		}
		if (is_author()) {
			list($username, $rest) = explode(',', $s);
			$s = ventures_get_user_name(get_the_author_meta('ID')) . ',' . $rest;
		}
		return $s;
	}, 20);

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'ventures' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'ventures_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // ventures_setup
add_action( 'after_setup_theme', 'ventures_setup' );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function ventures_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'ventures' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'ventures_widgets_init' );

function add_async($tag, $handle) {
	if (strpos($handle, '-async')!==false) {
		$tag = str_replace(' src', ' async src', $tag);
	}
	if (strpos($handle, '-defer')!==false) {
		$tag = str_replace(' src', ' defer src', $tag);
	}
	return $tag;
}
add_filter('script_loader_tag', 'add_async', 10, 2 );

/**
 * Enqueue scripts and styles.
 */
function ventures_scripts() {
	$js_dir = get_template_directory_uri() . '/js';
	$version = V_ASSETS_VERSION;
	
	wp_deregister_script('underscore');
	wp_register_script('underscore', $js_dir.'/vendor/underscore.js', array(), $version, true );
	wp_register_script('slick', $js_dir.'/vendor/slick.js', array(), $version, true );
	wp_register_script('animation', $js_dir.'/animation.js', array(), $version, true );
	wp_register_script('touch-events', $js_dir.'/touch-events.js', array('underscore'), $version, true );
	wp_register_script('waypoints', $js_dir.'/vendor/jquery.waypoints.js', array('jquery'), $version, true );
	wp_register_script('outsideevents', $js_dir.'/vendor/jquery.outsideevents.js', array('jquery'), $version, true );
	// wp_register_script('css-js', $js_dir.'/css.js', array(), $version, true );
	// wp_register_script('smooth-scroll', $js_dir.'/vendor/jquery.smooth-scroll.js', array('jquery'), $version, true );
	wp_register_script('scroll', $js_dir.'/Scroll.js', array(), $version, true );
	wp_register_script('picturefill-async', $js_dir.'/vendor/picturefill.js', array(), $version, true );
	wp_register_script('centered-popup', $js_dir.'/centered-popup.js', array(), $version, true );
	wp_register_script('jquery-dfp', $js_dir.'/vendor/jquery.dfp.js', array('jquery'), $version, true);
	wp_register_script('zepto-dfp', $js_dir.'/vendor/jquery.dfp.js', array('zepto'), $version, true);
	wp_register_script('blur-canvas', $js_dir.'/blur-canvas.js', array(), $version, true);
	wp_register_script('placeholder', $js_dir.'/vendor/jquery.html5-placeholder-shim.js', array('jquery'), $version, true);
wp_enqueue_script('ventures-jq', $js_dir.'/jquery-1.12.4.min.js');
	wp_enqueue_script('ventures-push', $js_dir.'/push.js');

	$js_localize_data = apply_filters('ventures_js_vars', array(
		'THEME_URL' => get_template_directory_uri(),
		'AJAX_URL' => admin_url('admin-ajax.php'),
		'WPP_NONCE' => wp_create_nonce('wpp-token'),
		'LATEST_ARTICLES_PER_PAGE' => LATEST_ARTICLES_PER_PAGE,
		'ENABLE_NARROW_TEASERS_CAROUSEL' => defined('ENABLE_NARROW_TEASERS_CAROUSEL') && ENABLE_NARROW_TEASERS_CAROUSEL,
	));
	wp_localize_script( "jquery", 'VENTURES', $js_localize_data);

	$use_uncompressed_js = defined('VENTURES_UNCOMPRESSED_JS') && VENTURES_UNCOMPRESSED_JS===true;

	if (ventures_is_mobile()) {
		if ($use_uncompressed_js) {
			wp_enqueue_script('ventures-mobile', $js_dir.'/main.mobile.js', array('jquery', 'underscore','touch-events','animation','scroll','picturefill-async','jquery-dfp','slick'), $version, true );
		}
		else {
			wp_enqueue_script('ventures-mobile', $js_dir.'/main.mobile.min.js', array('jquery'), $version, true );
		}
	}
	else {
		if ($use_uncompressed_js) {
			wp_enqueue_script('ventures-desktop', $js_dir.'/main.desktop.js', array('jquery','picturefill-async','scroll','waypoints','outsideevents','centered-popup','jquery-dfp','blur-canvas','placeholder','slick'), $version, true);
		}
		else {
			wp_enqueue_script('ventures-desktop', $js_dir.'/main.desktop.min.js', array('jquery'), $version, true);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'ventures_scripts' );
function ventures_styles() {
	$css_dir = get_template_directory_uri() . '/css';
	$version = V_ASSETS_VERSION;

	wp_enqueue_style('ventures-style', get_stylesheet_uri(), array(), $version);
	wp_enqueue_style('ventures-tablets', $css_dir.'/tablets.css', array(), $version, 'only screen and (min-width: 760px)' );
	if (!ventures_is_mobile()) {
		wp_enqueue_style('font-hftitling', 'https://cloud.typography.com/6065954/693746/css/fonts.css');
		wp_enqueue_style('ventures-desktop', $css_dir.'/desktop.css', array(), $version, 'only screen and (min-width: 1200px)' );
	}
}
add_action( 'wp_enqueue_scripts', 'ventures_styles' );

// inline modernizr for speed
add_action('wp_head', function() {
	echo '<script id="modernizr">';
	include get_template_directory().'/js/vendor/modernizr.js';
	echo '</script>';
});

// remove WPP styles - pointless on front-end
add_action('wp_enqueue_scripts', function() { wp_deregister_style('wordpress-popular-posts'); }, 100);

// move all scripts to the footer
remove_action('wp_head', 'wp_print_scripts');
remove_action('wp_head', 'wp_print_head_scripts', 9);
add_action('wp_footer', 'wp_print_scripts', 5);
add_action('wp_footer', 'wp_print_head_scripts', 5);

/**
 * ventures_is_mobile
 *
 * Wrapper for wp_is_mobile, making tablets NOT mobile.
 */
function ventures_is_mobile() {
	static $ventures_is_mobile;

	if ( isset($ventures_is_mobile) )
		return $ventures_is_mobile;

	$ventures_is_mobile = wp_is_mobile();

	if (!empty($_SERVER['HTTP_USER_AGENT'])) {
		$ua = $_SERVER['HTTP_USER_AGENT'];

		// iPads are not mobile
		if (strpos($ua, 'iPad') !== false) {
			$ventures_is_mobile = false;
		}

		// Tablet Android is not mobile
		if (strpos($ua, 'Mobile') === false && strpos($ua, 'Android') !== false) {
			$ventures_is_mobile = false;
		}
	}

	return $ventures_is_mobile;
}

/**
 * Get posts objects, including thumb data and category in one single query.
 *
 * Avoids GROUP BY and ORDER BY clauses which can make the query much slower.
 */
function ventures_get_posts_by_id($ids, $extended_data=false, $where='', $limit='') {
	global $wpdb;
	$posts = array();
	$sql = "
		SELECT
			posts.*,
			att_meta.`meta_value` AS img_meta,
			term.`slug` AS cat_slug,
			term.`name` AS cat_name
	";
	if ($extended_data) {
		$sql .= ",
			credit.meta_value AS credit,
			credit_url.meta_value AS credit_url,
			CONCAT(fname.meta_value,' ',lname.meta_value) AS author_name,
			users.display_name AS author_dname,
			users.user_nicename AS author_slug,
			truncate_disabled.meta_value AS truncate_disabled_serialized,
			source_name.meta_value AS source_name,
			source_url.meta_value AS source_url
		";
	}
	if (WP_DEBUG) {
		$sql .= ",
			pageviews,
			priority.meta_value AS priority
		";
	}
	$sql .= "
		FROM $wpdb->posts posts
			LEFT JOIN $wpdb->postmeta att_id ON att_id.`post_id`=posts.`ID` AND att_id.`meta_key`='_thumbnail_id'
			LEFT JOIN $wpdb->postmeta att_meta ON att_meta.`post_id`=att_id.`meta_value` AND att_meta.`meta_key`='_wp_attachment_metadata'
			LEFT JOIN $wpdb->term_relationships post_terms ON posts.`ID`=`object_id` AND post_terms.`term_taxonomy_id`!=1
			LEFT JOIN $wpdb->term_taxonomy cat ON cat.`taxonomy`='category' AND cat.`term_taxonomy_id`=post_terms.`term_taxonomy_id`
			LEFT JOIN $wpdb->terms term ON cat.`term_id`=term.`term_id`
	";
	if ($extended_data) {
		$sql .= "
			JOIN $wpdb->users users ON users.ID = posts.post_author
			LEFT JOIN $wpdb->postmeta AS credit ON att_meta.post_id = credit.post_id AND credit.meta_key='_media_credit'
			LEFT JOIN $wpdb->postmeta AS credit_url ON att_meta.post_id = credit_url.post_id AND credit_url.meta_key='_media_credit_url'
			LEFT JOIN $wpdb->postmeta AS truncate_disabled ON att_meta.post_id = truncate_disabled.post_id AND truncate_disabled.meta_key='truncate_disabled'
			LEFT JOIN $wpdb->postmeta AS source_name ON att_meta.post_id = source_name.post_id AND source_name.meta_key='tn_source_name'
			LEFT JOIN $wpdb->postmeta AS source_url ON att_meta.post_id = source_url.post_id AND source_url.meta_key='tn_url'
			LEFT JOIN $wpdb->usermeta AS fname ON post_author = fname.user_id AND fname.meta_key='first_name'
			LEFT JOIN $wpdb->usermeta AS lname ON post_author = lname.user_id AND lname.meta_key='last_name'
		";
	}
	if (WP_DEBUG) {
		$sql .= "
		LEFT JOIN `{$wpdb->prefix}popularpostsdata` pageviews ON `postid`=posts.`ID` 
		LEFT JOIN `wp_postmeta` priority ON posts.ID=priority.`post_id` AND priority.`meta_key`='priority'
		";
	}
	$sql .= "
		WHERE
			posts.`ID` IN (".implode(',', $ids).")
			{$where}
		{$limit}
	";
	if (WP_DEBUG) error_log($sql);
	$rows = $wpdb->get_results($sql);
	$root_cat_sql = "
		SELECT `object_id`, `parent`, term.`slug` AS root_slug, term.`name` AS root_name
		FROM $wpdb->term_relationships post_terms
			JOIN $wpdb->term_taxonomy USING (`term_taxonomy_id`)
			JOIN $wpdb->terms term USING (`term_id`)
		WHERE `object_id` IN (".implode(',', $ids).")
			AND `term_taxonomy_id`!=1
			AND (parent=0 OR `term_id` IN (`parent`))
		GROUP BY `object_id`
	";
	$root_cats = $wpdb->get_results($root_cat_sql);
	foreach ($ids as $id) {
		foreach ($rows as &$row) if ($row->ID == $id) {
			$row->img_meta_value = $row->img_meta ? unserialize($row->img_meta) : NULL;
			foreach ($root_cats as $cat) if ($cat->object_id == $id) {
				$row->root_slug = $cat->root_slug;
				$row->root_name = $cat->root_name;
				break;
			}
			$row->truncate_disabled = $extended_data && $row->truncate_disabled_serialized ? unserialize($row->truncate_disabled_serialized) : NULL;
			$posts[] = $row;
			break;
		}
	}
	return $posts;
}

function ventures_define_query_info($query) {
	global $wpdb;
	if ($query->is_main_query() && !$query->is_404()) {
		define('QUERY_IS_SINGLE_RESULT', !($query->is_home() || $query->is_archive() || $query->is_search() || $query->is_author()));
		define('QUERY_IS_IDEAS', $query->is_post_type_archive() && $query->query_vars['post_type'] === 'ventures_ideas');
		define('QUERY_IS_INTERVIEWS', $query->is_post_type_archive() && $query->query_vars['post_type'] === 'ventures_interviews');
		define('ENABLE_NARROW_TEASERS_CAROUSEL', !QUERY_IS_SINGLE_RESULT && !QUERY_IS_IDEAS);
		if ($query->is_category() || $query->is_tag()) {
			$taxonomy = $query->is_category() ? 'category' : 'tag';
			$slug = $query->query_vars[$query->is_category() ? 'category_name' : 'tag'];
			$base_query = "SELECT `term_id` FROM `$wpdb->terms` JOIN `$wpdb->term_taxonomy` USING (`term_id`) WHERE `slug`='$slug' AND `taxonomy`='$taxonomy'";
			$sql = "$base_query
				UNION
					SELECT `parent` FROM `wp_term_taxonomy`
					WHERE `term_id`=($base_query)
				UNION
					SELECT `term_id` FROM `wp_term_taxonomy`
					WHERE `parent`=($base_query)
			";
			define('QUERY_TERM_IDS', implode(',', $wpdb->get_col($sql)));
			if (QUERY_TERM_IDS === '') do_action('ventures_404');
		}
	}
}
if (!is_admin()) add_action( 'parse_query', 'ventures_define_query_info' );

if (is_admin()) add_filter('acf/fields/relationship/query', function($options, $field, $the_post) {
	if (strpos($field['name'], 'featured_') === 0 && strpos($options['s'], ':') !== FALSE) {
		$parts = explode(':', $options['s']);
		if ($parts[0] === 'ads') {
			$options['post_type'] = ['ventures_feature_ads'];
			$options['s'] = substr($options['s'], 4);
		}
	}
	$options['post_status'] = 'publish';
	return $options;
}, 10, 3);

function ventures_post_queries($query) {
	global $wpdb, $_POSTS;
	$_POSTS['excludes'] = array();
	if ($query->is_main_query() && !$query->is_404()) {
		remove_action( 'pre_get_posts', 'ventures_post_queries' );
		if (!QUERY_IS_SINGLE_RESULT) {
			if (!$query->is_post_type_archive()) {
				$query->set('post_type', explode(',', VENTURES_LATEST_ARTICLES_POST_TYPES));
			}
			if ($query->is_author()) {
				$username = $query->query_vars['author_name'];
				$sql = "
					SELECT posts.ID FROM $wpdb->posts posts
						JOIN $wpdb->users users ON users.ID = posts.post_author
					WHERE users.user_nicename='$username' AND posts.post_status='publish'
					ORDER BY posts.post_date DESC
					LIMIT 0,".(TOP_STORIES_MAX_COUNT - 1);
				$_POSTS['latest'] = ventures_get_posts_by_id($wpdb->get_col($sql), TRUE);
				$_POSTS['top'] = array();
			}
			else {
				$top = get_current_section_top_stories_ids($query);
				if (QUERY_IS_IDEAS) {
					$sql = "
						SELECT
							posts.*,
							CONCAT(fname.meta_value,' ',lname.meta_value) AS author_name,
							users.display_name AS author_dname,
							users.user_nicename AS author_slug,
							avatar_id.meta_value AS thumb_id,
							avatar.meta_value AS thumb_data,
							COUNT(*) AS article_count
						FROM $wpdb->posts posts
							JOIN $wpdb->users users ON users.ID = posts.post_author
							JOIN $wpdb->usermeta AS fname ON users.ID=fname.user_id AND fname.meta_key='first_name'
							JOIN $wpdb->usermeta AS lname ON users.ID=lname.user_id AND lname.meta_key='last_name'
							LEFT JOIN $wpdb->usermeta AS avatar_id ON users.ID=avatar_id.user_id AND avatar_id.meta_key='{$wpdb->base_prefix}user_avatar'
							LEFT JOIN $wpdb->postmeta AS avatar ON avatar_id.meta_value = avatar.post_id AND avatar.meta_key='_wp_attachment_metadata'
						WHERE posts.post_type='ventures_ideas' AND posts.post_status='publish'
						GROUP BY users.ID
						ORDER BY article_count DESC
						LIMIT 0,12
					";
					$_POSTS['latest'] = $wpdb->get_results($sql);
				}
				else {
					$query->set('posts_per_page', LATEST_ARTICLES_PER_PAGE * LATEST_ARTICLES_PAGE_COUNT);
					add_filter('found_posts', function($count, &$query) use ($top) {
						global $wpdb, $_POSTS;
						if ($query->is_main_query()) {
							remove_all_filters('found_posts');
							$ids = array_diff(array_map(function($s_id) { return intval($s_id); }, $query->posts), $top);
							$_POSTS['latest'] = ventures_get_posts_by_id($ids);
							$_POSTS['excludes'] = array_merge($_POSTS['excludes'], array_slice($ids, 0, LATEST_ARTICLES_PER_PAGE));
						}
						return $count;
					}, 10, 2);
				}
				if (!QUERY_IS_INTERVIEWS && !count($top)) {
					$_POSTS['top'] = get_posts(array('post_type' => explode(',', VENTURES_ARTICLES_POST_TYPES), 'post__in' => array_slice(array_flip($order), -6), 'orderby' => 'post__in'));
				}
				elseif (count($top)) {
					$_POSTS['top'] = ventures_get_posts_by_id($top);
					$_POSTS['excludes'] = array_merge($_POSTS['excludes'], $top);
				}
				else {
					$_POSTS['top'] = array();
				}
				if (count($top)) $query->set('post__not_in', $top);
			}
		}
		else {
			add_filter('found_posts', function($count, &$query) {
				global $wpdb, $_POSTS;
				if ($query->is_main_query()) {
					remove_all_filters('found_posts');
					$_POSTS['top'] = array();
					$_POSTS['latest'] = $query->posts;
				}
				return $count;
			}, 10, 2);
		}
		if ($query->is_author()) {
			$query->set('posts_per_page', 5);
			$query->set('post_type', explode(',', VENTURES_ARTICLES_POST_TYPES));
		}
	}
}
if (!is_admin()) add_action( 'pre_get_posts', 'ventures_post_queries' );

function get_posts_ids_from_query($wp_query) {
	if (!$wp_query || !($wp_query instanceof WP_Query) || empty($wp_query->posts)) return array();
	return array_map(function($p) { return $p->ID; }, $wp_query->posts);
}

function ventures_category_box_posts($excludes=array()) {
	global $wpdb;
	$excludes_where = empty($excludes) ? '' : 'AND object_id NOT IN ('.implode(',', $excludes).')';
	$category_posts = array_map(function($cat) use ($excludes_where) {
		global $wpdb;
		$rows = $wpdb->get_results("
			SELECT posts.*
			FROM $wpdb->term_relationships JOIN $wpdb->posts posts ON `object_id`=posts.`ID`
			WHERE `term_taxonomy_id`=$cat->term_taxonomy_id AND `post_status`='publish' $excludes_where
			ORDER BY `post_date` DESC LIMIT 0,12
		");
		shuffle($rows);
		$posts = array_slice($rows, 0, 4);
		$cat->url = ventures_category_url($cat->slug);
		$cat->img_meta_value = unserialize($cat->img_meta);
		$cat->img = ventures_resp_img(0, $cat->img_meta_value);
		return compact('cat','posts');
	}, $wpdb->get_results("
		SELECT `term_id`,`term_taxonomy_id`,`name`,`slug`, att_meta.meta_value AS img_meta
		FROM $wpdb->term_taxonomy JOIN $wpdb->terms USING (`term_id`)
			JOIN $wpdb->options z_tax ON z_tax.`option_name`=concat('z_taxonomy_image',`term_id`)
			JOIN $wpdb->posts atts ON SUBSTRING_INDEX(SUBSTRING_INDEX(z_tax.`option_value`, '/', -1), '.', 1)=atts.`post_name`
			JOIN $wpdb->postmeta att_meta ON att_meta.`post_id`=atts.`ID` AND att_meta.`meta_key`='_wp_attachment_metadata'
		WHERE `parent`=0 AND `name`!='Uncategorized' AND taxonomy='category'
	"));
	return $category_posts;
}

function ventures_post_info($_post=null, $sizes = '100vw') {
	global $post;
	if (!$_post) $_post = $post;
	$title = trim(get_the_title($_post));
	if (!in_array(strrev($title)[0], ['.', '?', '!'])) $title = $title.'.';
	if (!empty($_post->img_meta)) {
		$bg_pos = ($pos = get_field('background_position', $_post->ID)) ? $pos : 'center center';
		$thumb_data = ventures_post_image_data($_post);
		$img_srcs = ventures_resp_img(0, $thumb_data);
		$image = sprintf('<img class="thumb fitted" style="object-position:%s;" src="%s" srcset="%s" sizes="%s" alt="" data-original-width="%d">',
			$bg_pos,
			$img_srcs['src'],
			$img_srcs['srcset'],
			$sizes,
			$thumb_data['width']
		);
	}
	else {
		$image = get_the_post_thumbnail($_post->ID, 'medium', array('class' => 'thumb fitted', 'title' => ''));
	}
	if (QUERY_IS_IDEAS) {
		$author = array(
			'name' => trim(empty($_post->author_name) ? $_post->author_dname : $_post->author_name),
			'url' => ventures_author_url($_post->author_slug),
			'img' => ventures_author_thumbnail($_post->post_author, false, $_post, true, 'small'),
		);
	}
	else {
		$author = null;
	}
	$url = get_the_permalink($_post);
	return array(
		'id' => $_post->ID,
		'type' => $_post->post_type,
		'title' => $title,
		'img' => $image,
		'category' => get_root_category($_post),
		'url' => $url,
		'video_url' => $_post->post_type === 'ventures_interviews' ? ventures_fetch_interview_video_url($_post) : '',
		'author' => $author,
		'is_idea' => $_post->post_type === 'ventures_ideas',
		'is_tn' => $_post->post_type === 'ventures_tn',
		'is_apo' => $_post->post_type === 'ventures_apo',
		'is_ama' => $_post->post_type === 'ventures_ama',
	);
}

function ventures_resp_category_img($term_id) {
	$taxonomy_image_url = get_option('z_taxonomy_image'.$term_id);
    if(!empty($taxonomy_image_url)) {
	    $att_id = z_get_attachment_id_by_url($taxonomy_image_url);
	    if ($att_id) {
	    	$img_srcs = ventures_resp_img($att_id);
				$width = ($img_info = wp_get_attachment_metadata($att_id)) ? $img_info['width'] : 0;
	    	echo sprintf('<img class="fitted" src="%s" srcset="%s" sizes="(max-width: 759px) 100vw, 50vw" alt="%s" data-original-width="%d">', $img_srcs['src'], $img_srcs['srcset'], get_post_meta($att_id, '_wp_attachment_image_alt', true), $width);
	    }
	}
}

function ventures_has_image_credit() {
	if (has_post_thumbnail() && ($attpost = get_post(get_post_thumbnail_id()))) {
		return (bool)get_media_credit($attpost);
	}
	return false;
}

function ventures_body_class( $classes ) {
	global $wp_the_query;
	if ($wp_the_query->is_home || $wp_the_query->is_archive || $wp_the_query->is_search) $classes[] = 'home-template';
	if ($wp_the_query->is_author) $classes[] = 'single';
	if (get_field("desktop_layout_width")==='narrow') $classes[] = 'layout-narrow';
	return $classes;
}
add_filter( 'body_class', 'ventures_body_class' );

function featured_ad_url_rewrite($permalink, $post) {
	if ($post->post_type === 'ventures_feature_ads' && ($add_url = get_field('ad_url', $post->ID))) {
		return $add_url;
	}
	return $permalink;
}
add_filter('post_type_link', 'featured_ad_url_rewrite', 10, 2);

function ventures_category_url($slug) {
	global $wp_rewrite;
	return home_url(user_trailingslashit(str_replace('%category%', $slug, $wp_rewrite->get_extra_permastruct('category'))));
}

function ventures_author_url($slug) {
	global $wp_rewrite;
	return home_url(user_trailingslashit(str_replace('%author%', $slug, $wp_rewrite->get_author_permastruct())));
}

function ventures_post_category($post) {
	$prefix = (is_category() || !isset($post->root_name)) ? 'cat' : 'root';
	return array(
		'title' => $post->{$prefix.'_name'},
		'url' => ventures_category_url($post->{$prefix.'_slug'}),
	);
}

function ventures_post_image_data($post) {
	$serialized_keys = array('img_meta_value');
	$unserialized_keys = array('thumb_data','img_meta');
	foreach ($serialized_keys as $key) if (isset($post->$key)) return $post->$key;
	foreach ($unserialized_keys as $key) if (isset($post->$key) && $post->$key) return unserialize($post->$key);
	return NULL;
}

function ventures_post_debug_info($post) {
	return sprintf('(%s, %d views, priority: %s)', substr($post->post_date, 0, 10), $post->pageviews, $post->priority ? $post->priority : 'none');
}

function get_root_category($_post=null) {
	global $post;
	if (!$_post) $_post = $post;
	else {
		if (isset($_post->cat_name) && $_post->cat_name) return ventures_post_category($_post);
	}
	$parent = is_category() ? get_query_var('cat') : 0;
	foreach (array_reverse(get_the_category()) as $cat) {
		if ($cat->parent === $parent || $cat->term_id === $parent) return array(
			'title' => $cat->cat_name === 'Uncategorized' ? '' : $cat->cat_name,
			'url' => esc_url(get_category_link($cat->term_id)),
		);
	}
	return array(
		'title' => '',
		'url' => '',
	);
}

function get_post_child_category($parent) {
	$cats = array_filter(get_the_category(), function($cat) use ($parent) {
		return $cat->cat_name !== 'Uncategorized' && $cat->term_id !== $parent;
	});
	if (empty($cats)) return array(
		'title' => '',
		'url' => '',
	);
	// if not looking for root, then we want to get a sub-category.
	usort($cats, function($a, $b) { return $a->parent === 0 ? -1 : ($b->parent === 0 ? 1 : 0); });
	foreach ($cats as $cat) if ($cat->parent === $parent) break;
	// if looking for root cat, go up the chain.
	if ($cat->parent !== $parent && $parent === 0) while ($cat->parent !== 0) $cat = get_category($cat->parent);
	// if no child cat and no root, return sibling.
	return array(
		'title' => $cat->cat_name,
		'url' => esc_url(get_category_link($cat->term_id)),
	);
}

function get_top_story_top_left_label() {
	if (is_home()) return 'Featured';
	if (is_category()) return get_category(get_query_var('cat'))->name;
	if (is_post_type_archive()) return get_post_type_object(get_query_var('post_type'))->label;
	return '';
}

function get_formatted_top_story_title_label($_post=null) {
	extract(get_top_story_title_label($_post));
	$tag = empty($url) ? 'span' : 'a';
	return sprintf('<%s class="category-label" href="%s">%s</%s>', $tag, $url, $title, $tag); 
}

function get_top_story_title_label($_post=null) {
	global $post;
	if (!$_post) $_post = $post;
	if (is_404()) return array('title' => 'Error', 'url' => '');
	switch ($_post->post_type) {
	case 'ventures_interviews':
		if (is_post_type_archive() && get_query_var('post_type') === 'ventures_interviews') return get_root_category($_post);
		$type = get_post_type_object($_post->post_type);
		return array(
			'title' => $type->labels->singular_name,
			'url' => esc_url(get_post_type_archive_link($_post->post_type)),
		);
	case 'ventures_ideas':
		if (is_author()) {
			$type = get_post_type_object('ventures_ideas');
			return array(
				'title' => $type->label,
				'url' => esc_url(get_post_type_archive_link($_post->post_type)),
			);
		}
		return array(
			'title' => ventures_get_user_name($_post->post_author),
			'url' => get_author_posts_url($_post->post_author),
		);
	case 'ventures_feature_ads':
		return array(
			'title' => is_acf_checkbox_checked('hide_text', $_post->ID) ? '' : get_field('ad_headline', $_post->ID),
			'url' => '',
		);
	default:
		if (is_author()) {
			return array(
				'title' => 'Contributor',
				'url' => false
			);
		}
		if (!isset($_post->cat_name) || !$_post->cat_name) {
			return get_post_child_category(is_category() ? get_query_var('cat') : 0);
		}
		else {
			return ventures_post_category($_post);
		}
	}
}

function get_post_sharing_links($_post=null) {
	global $post, $_POSTS;
	if (!$_post) {
		$_post = (is_single() || is_page()) ? $post : (count($_POSTS['top']) ? $_POSTS['top'][0] : $_POSTS['latest'][0]);
	}
	list($title, $url) = [html_entity_decode(get_the_title($_post)), wp_get_shortlink($_post->ID, 'post')];
	$links = array(
		array(
			'title' => 'Twitter',
			'slug' => 'twitter',
			'target_attr' => 'target="_blank"',
			'url' => 'https://twitter.com/intent/tweet?text='.rawurlencode($title.' - '.$url),
		),array(
			'title' => 'Email',
			'slug' => 'email',
			'target_attr' => '',
			'url' => 'mailto:?subject='.rawurlencode($title).'&body='.rawurlencode("VENTURES AFRICA: $title \n\nRead more: $url"),
		),array(
			'title' => 'Google+',
			'slug' => 'googleplus',
			'target_attr' => 'target="_blank"',
			'url' => 'https://plus.google.com/share?url='.rawurlencode($url),
		),array(
			'title' => 'LinkedIn',
			'slug' => 'linkedin',
			'target_attr' => 'target="_blank"',
			'url' => 'https://www.linkedin.com/cws/share?url='.rawurlencode($url),
		),array(
			'title' => 'Facebook',
			'slug' => 'facebook',
			'target_attr' => 'target="_blank"',
			'url' => 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($url),
		),
	);
	if (ventures_is_mobile()) {
		$links[] = [
			'title' => 'WhatsApp',
			'slug' => 'whatsapp',
			'target_attr' => 'data-text="'.htmlspecialchars($title).'" data-href="'.htmlspecialchars($url).'" class="wa_btn wa_btn_s" style="display:none"',
			'url' => 'whatsapp://send',
		];
	}
	return $links;
}

function get_ventures_external_links() {
	return array(
		array(
			'title' => 'Twitter',
			'slug' => 'twitter',
			'url' => 'https://twitter.com/VenturesAfrica',
		),array(
			'title' => 'Google+',
			'slug' => 'googleplus',
			'url' => 'https://plus.google.com/+Ventures-africa',
		),array(
			'title' => 'LinkedIn',
			'slug' => 'linkedin',
			'url' => 'http://www.linkedin.com/company/2552019',
		),array(
			'title' => 'Facebook',
			'slug' => 'facebook',
			'url' => 'https://www.facebook.com/VenturesAfrica',
		),array(
			'title' => 'Download Ventures Magazine on the App Store',
			'slug' => 'apple',
			'url' => 'https://itunes.apple.com/us/app/ventures-africa-magazine/id696538208?ls=1&mt=8',
		),array(
			'title' => 'Download Ventures Magazine on Google Play',
			'slug' => 'android',
			'url' => 'https://play.google.com/store/apps/details?id=za.co.snapplify.venturesafrica',
		),
	);
}

function wpp_nonce_refresh_response() {
	echo wp_create_nonce('wpp-token');
	exit();
}
add_action( 'wp_ajax_wpp_nonce_refresh', 'wpp_nonce_refresh_response' );
add_action( 'wp_ajax_nopriv_wpp_nonce_refresh', 'wpp_nonce_refresh_response' );

if (defined('HIDE_ADMIN_BAR') && HIDE_ADMIN_BAR) {
	add_filter('show_admin_bar', '__return_false');
}

function ventures_break_interview_content($_post=null) {
	global $post;
	if (!$_post) $_post = $post;
	$content = str_replace('[embed]', '', str_replace('[/embed]', '', $_post->post_content));
	$parts = array(
		'description' => $content,
		'video' => '',
	);
	preg_match(INTERVIEW_URL_REGEX, $content, $matches, PREG_OFFSET_CAPTURE);
	if ($matches && count($matches)) {
		$parts['description'] = substr($content, 0, $matches[0][1]).substr($content, $matches[0][1] + strlen($matches[0][0]));
		$parts['video'] = $matches[0][0];
	}
	return $parts;
}
function ventures_fetch_interview_video_url($post=null) {
	$parts = ventures_break_interview_content($post);
	return $parts['video'];
}
function ventures_fetch_interview_description($post=null) {
	$parts = ventures_break_interview_content($post);
	return $parts['description'];
}

function ventures_narrow_teasers_title() {
	global $wp_the_query;
	$ct = single_cat_title('', false);
	return (is_post_type_archive() && $wp_the_query->query_vars['post_type'] === 'ventures_ideas')
		? 'Our Contributors' 
		: file_get_contents(get_template_directory().'/img/arrows/down.svg').($ct 
			? '<span class="cat"><em>'.$ct.' </em><span>What\'s New</span></span>' 
			: 'What\'s New'
		  );
}

function ventures_top_stories_headline() {
	global $wp_the_query;
	if (is_category()) {
		$cat = get_category_by_slug($wp_the_query->query_vars['category_name']);
		if (!empty($cat->description)) $headline = $cat->description;
	}
	if (is_home()) {
		$headline = 'Critical News in Africa';
	}
	if (isset($headline)) printf('<span class="title-headline">%s</span>', $headline);
}

function ventures_teaser_title($list_title, $items_type) {
	$suffix = is_home() ? $items_type : (is_category() || is_tag() ? 'In '.single_cat_title('', false) : (
		is_post_type_archive() ? post_type_archive_title('', false) : $items_type));
	return "$list_title $suffix";
}

function ventures_inline_teaser_ad() {
	global $wp_the_query;
	$key = 'default';
	if (is_home() && get_field("home_title", INLINE_ADS_SETTINGS_PAGE_ID, false)) $key = 'home';
	if (is_category() && get_field("{$wp_the_query->query_vars['category_name']}_title", INLINE_ADS_SETTINGS_PAGE_ID, false)) $key = $wp_the_query->query_vars['category_name'];
	if (is_post_type_archive() && ($type = str_replace('ventures_', '', $wp_the_query->query_vars['post_type'])) && get_field("{$type}_title", INLINE_ADS_SETTINGS_PAGE_ID, false)) $key = $type;
	$thumb_id = get_field("{$key}_image", INLINE_ADS_SETTINGS_PAGE_ID, false);
	$img_srcs = ventures_resp_img($thumb_id);
	$img_info = wp_get_attachment_metadata($thumb_id);
	$ad_url = get_field("{$key}_url", INLINE_ADS_SETTINGS_PAGE_ID, false);
	if (empty($ad_url)) {
		return;
	}
	ventures_full_teaser(array(
		'show_on_mobile' => true,
		'classes' => array('post','has-image','advert'),
		'related_posts' => '',
		'full_content' => '',
		'url' => $ad_url,
		'link_target' => '_blank',
		'title' => get_field("{$key}_title", INLINE_ADS_SETTINGS_PAGE_ID, false),
		'id' => 0,
		'image' => array(
			'src' => $img_srcs['src'],
			'srcset' => $img_srcs['srcset'],
			'alt' => '',
			'width' => $img_info ? $img_info['width'] : 0,
		),
		'label' => 'Advertisement',
		'teaser_text' => get_field("{$key}_summary", INLINE_ADS_SETTINGS_PAGE_ID, false),
	), true);
}

function latest_articles_image_indexes($count) {
	$use_thumb = array_rand(range(0, LATEST_ARTICLES_PER_PAGE - 1), 3);
	if (!in_array(0, $use_thumb)) {
		array_pop($use_thumb); 
		$use_thumb[] = 0;
	}
	$second = array_map(function($n) { return 2 * LATEST_ARTICLES_PER_PAGE - $n - 1; }, $use_thumb);
	$third = array_map(function($n) { return 3 * LATEST_ARTICLES_PER_PAGE - $n - 1; }, $use_thumb);
	return array_merge($use_thumb, $second, $third);
}

function ventures_get_user_name($user_id, $user_data=null) {
	if ($user_data) {
		return trim($user_data->author_name ? $user_data->author_name : $user_data->author_dname);
	}
	$info = get_userdata($user_id);
	$name = trim("$info->first_name $info->last_name");
	return empty($name) ? $info->display_name : $name;
}

function tn_truncate_content($content) {
	$paras = preg_split("/[\r\n]+/", $content);
	$paras = array_slice($paras, 0, TN_AUTO_TRUNCATE_PARAGRAPH_COUNT);
	return implode("\n", $paras);
}

function is_acf_checkbox_checked($field_name, $post_id=0, $value_string='') {
	$value = get_field($field_name, $post_id);
	if (is_bool($value)) return $value;
	if (is_array($value) && count($value) && (empty($value_string) || $value[0] === $value_string)) return TRUE;
	return FALSE;
}

add_filter('the_content', function($content) {
	$content = str_replace('<p>VENTURES AFRICA – ', '<p>', $content);
	$content = str_replace('<p>VENTURES AFRICA — ', '<p>', $content);
	$content = str_replace('<p>VENTURES AFRICA &#8211; ', '<p>', $content);
	$content = str_replace('<p>&nbsp;</p>', '', $content);
	if (strpos($content, 'iframe')!==false) {
		$content = preg_replace('/(src=".+vimeo.com\/[^"]+)"/i', '$1?autoplay=1&badge=0&byline=0&color=ea6c11&portrait=0"', $content);
	}
	$content = str_replace('https:', 'http:', $content);
	return $content;
});

add_shortcode('latest-daily-brief', function() {
	$q = new WP_Query([
		'numberposts' => 1,
		'post_type' => 'ventures_briefs'
	]);
	if ($q->have_posts()) {
		$q->the_post();
		get_template_part('content', 'briefs');
	}
	wp_reset_postdata();
});

add_shortcode('author_contact_widgets', function($atts, $content) {
	return '<div class="author-contact-widget clear">'.do_shortcode(strip_tags($content)).'</div>';
});

add_shortcode('author_contact', function($atts) {
	if (isset($atts['user']) && ($user = get_user_by('login', $atts['user']))) {
		$thumb = ventures_author_thumbnail($user->ID, false);
		if (isset($atts['position'])) $thumb .= "<p>$atts[position]</p>";
		if (isset($atts['phone'])) $thumb .= "<p>$atts[phone]</p>";
		if (isset($atts['phone_2'])) $thumb .= "<p>$atts[phone_2]</p>";
		return sprintf(
			'<article><a href="%s">%s</a><a href="mailto:%s">%s</a></article>',
			esc_url(get_author_posts_url($user->ID)),
			$thumb,
			$user->user_email,
			$user->user_email
		);
	}
	return '';
});

add_filter('oembed_result', function($result, $url, $args) {
	if (strpos($url, 'vimeo') !== FALSE) {
		$result = preg_replace('/src="(.*?)"/', 'src="\1?byline=0&badge=0&portrait=0&title=0&autoplay=1"', $result);
	}
	if (strpos($url, 'soundcloud') !== FALSE) {
		$result = str_replace('<iframe ', '<iframe class="soundcloud" ', $result);
		$result = str_replace('show_artwork=true', 'show_artwork=false&buying=false&sharing=false&download=false&show_playcount=false&show_comments=false&default_height=160&auto_play=true&color=000000', $result);
		$result = str_replace('visual=true', 'visual=false', $result);
	}
	$result = str_replace('http:', 'https:', $result);
	return $result;
}, 10, 3);

function ventures_posted_on() {
}

function ventures_entry_footer() {
}

// Widget for mailing lists.
add_action('widgets_init', function() {
	register_sidebar([
		'name'          => 'Mailing List Widget Slot',
		'id'            => 'mlist_slot',
		'before_widget' => '<div class="mailing-list-form-container">',
		'after_widget'  => '</div>',
		'before_title'  => '',
		'after_title'   => '',
	]);
});

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

require get_template_directory() . '/inc/admin.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Media Credits on images
 */
require get_template_directory() . '/inc/media-credit.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

require get_template_directory() . '/inc/ads.php';
