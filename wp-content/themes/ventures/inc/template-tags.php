<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package ventures
 */

add_filter('wp_head', function() {
	$favdir = get_stylesheet_directory_uri() . '/img/favicon/';
	foreach([57,60,72,76,114,120,144,152,180] as $size) {
		$sizex = $size.'x'.$size;
		echo '<link rel="apple-touch-icon" sizes="'.$sizex.'" href="'.$favdir.'apple-touch-icon-'.$sizex.'.png">';
	}
	echo '
	<link rel="icon" type="image/png" href="'.$favdir.'favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="'.$favdir.'favicon-194x194.png" sizes="194x194">
	<link rel="icon" type="image/png" href="'.$favdir.'android-chrome-192x192.png" sizes="192x192">
	<link rel="icon" type="image/png" href="'.$favdir.'favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="'.$favdir.'favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="'.$favdir.'manifest.json">
	<link rel="shortcut icon" href="'.$favdir.'favicon.ico">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-TileImage" content="'.$favdir.'mstile-144x144.png">
	<meta name="msapplication-config" content="'.$favdir.'browserconfig.xml">
	<meta name="theme-color" content="#ffffff">';
});

if ( ! function_exists( 'ventures_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 */
function ventures_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'ventures' ); ?></h1>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'ventures' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'ventures' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'ventures_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function ventures_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'ventures' ); ?></h1>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', _x( '<span class="meta-nav">&larr;</span>&nbsp;%title', 'Previous post link', 'ventures' ) );
				next_post_link(     '<div class="nav-next">%link</div>',     _x( '%title&nbsp;<span class="meta-nav">&rarr;</span>', 'Next post link',     'ventures' ) );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'ventures_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function ventures_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		_x( 'Posted on %s', 'post date', 'ventures' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);

	$byline = sprintf(
		_x( 'by %s', 'post author', 'ventures' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>';

}
endif;

if ( ! function_exists( 'ventures_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function ventures_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' == get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( __( ', ', 'ventures' ) );
		if ( $categories_list && ventures_categorized_blog() ) {
			printf( '<span class="cat-links">' . __( 'Posted in %1$s', 'ventures' ) . '</span>', $categories_list );
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', __( ', ', 'ventures' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . __( 'Tagged %1$s', 'ventures' ) . '</span>', $tags_list );
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( __( 'Leave a comment', 'ventures' ), __( '1 Comment', 'ventures' ), __( '% Comments', 'ventures' ) );
		echo '</span>';
	}

	edit_post_link( __( 'Edit', 'ventures' ), '<span class="edit-link">', '</span>' );
}
endif;

if ( ! function_exists( 'the_archive_title' ) ) :
/**
 * Shim for `the_archive_title()`.
 *
 * Display the archive title based on the queried object.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 *
 * @param string $before Optional. Content to prepend to the title. Default empty.
 * @param string $after  Optional. Content to append to the title. Default empty.
 */
function the_archive_title( $before = '', $after = '' ) {
	if ( is_category() ) {
		$title = sprintf( __( 'Category: %s', 'ventures' ), single_cat_title( '', false ) );
	} elseif ( is_tag() ) {
		$title = sprintf( __( 'Tag: %s', 'ventures' ), single_tag_title( '', false ) );
	} elseif ( is_author() ) {
		$title = sprintf( __( 'Author: %s', 'ventures' ), '<span class="vcard">' . get_the_author() . '</span>' );
	} elseif ( is_year() ) {
		$title = sprintf( __( 'Year: %s', 'ventures' ), get_the_date( _x( 'Y', 'yearly archives date format', 'ventures' ) ) );
	} elseif ( is_month() ) {
		$title = sprintf( __( 'Month: %s', 'ventures' ), get_the_date( _x( 'F Y', 'monthly archives date format', 'ventures' ) ) );
	} elseif ( is_day() ) {
		$title = sprintf( __( 'Day: %s', 'ventures' ), get_the_date( _x( 'F j, Y', 'daily archives date format', 'ventures' ) ) );
	} elseif ( is_tax( 'post_format' ) ) {
		if ( is_tax( 'post_format', 'post-format-aside' ) ) {
			$title = _x( 'Asides', 'post format archive title', 'ventures' );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			$title = _x( 'Galleries', 'post format archive title', 'ventures' );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			$title = _x( 'Images', 'post format archive title', 'ventures' );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			$title = _x( 'Videos', 'post format archive title', 'ventures' );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			$title = _x( 'Quotes', 'post format archive title', 'ventures' );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			$title = _x( 'Links', 'post format archive title', 'ventures' );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			$title = _x( 'Statuses', 'post format archive title', 'ventures' );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			$title = _x( 'Audio', 'post format archive title', 'ventures' );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			$title = _x( 'Chats', 'post format archive title', 'ventures' );
		}
	} elseif ( is_post_type_archive() ) {
		$title = sprintf( __( 'Archives: %s', 'ventures' ), post_type_archive_title( '', false ) );
	} elseif ( is_tax() ) {
		$tax = get_taxonomy( get_queried_object()->taxonomy );
		/* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
		$title = sprintf( __( '%1$s: %2$s', 'ventures' ), $tax->labels->singular_name, single_term_title( '', false ) );
	} else {
		$title = __( 'Archives', 'ventures' );
	}

	/**
	 * Filter the archive title.
	 *
	 * @param string $title Archive title to be displayed.
	 */
	$title = apply_filters( 'get_the_archive_title', $title );

	if ( ! empty( $title ) ) {
		echo $before . $title . $after;
	}
}
endif;

if ( ! function_exists( 'the_archive_description' ) ) :
/**
 * Shim for `the_archive_description()`.
 *
 * Display category, tag, or term description.
 *
 * @todo Remove this function when WordPress 4.3 is released.
 *
 * @param string $before Optional. Content to prepend to the description. Default empty.
 * @param string $after  Optional. Content to append to the description. Default empty.
 */
function the_archive_description( $before = '', $after = '' ) {
	$description = apply_filters( 'get_the_archive_description', term_description() );

	if ( ! empty( $description ) ) {
		/**
		 * Filter the archive description.
		 *
		 * @see term_description()
		 *
		 * @param string $description Archive description to be displayed.
		 */
		echo $before . $description . $after;
	}
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function ventures_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'ventures_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'ventures_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so ventures_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so ventures_categorized_blog should return false.
		return false;
	}
}

/** 
 * Takes a thumbnail ID, and returns the default "src" and the responsive 
 * "srcset" image sizes. 
 */
function ventures_resp_img($post_thumbnail_id, $thumb_data=null) {
	global $ventures_img_responsive_sizes, 
	       $ventures_img_default_size, 
	       $ventures_img_size_prefix;
	$return = [
		'src' => get_template_directory_uri().'/img/blank.gif', 
		'srcset' => ''
	];
	if (!$post_thumbnail_id && !$thumb_data) return $return;
	if ($thumb_data && isset($thumb_data['sizes']) && is_array($thumb_data['sizes'])) {
		$uploads = wp_upload_dir();
		$path = substr($thumb_data['file'], 0, strrpos($thumb_data['file'], '/'));
		// ensure that the $thumb_data['sizes'] array only contains numerical sizes
		$sizes = [];
		foreach($thumb_data['sizes'] as $k => $v) {
			if (strpos($k, 'w-')===0) $sizes[] = $v;
		}
		$widths = $ventures_img_responsive_sizes;
		$srcs = array();
		for ($i = 0, $n = 0; $i < count($sizes) && $n < count($widths); $i++) {
			if ($sizes[$i]['width'] >= $widths[$n]) {
				$srcs[] = "$uploads[baseurl]/$path/{$sizes[$i]['file']} {$widths[$n]}w";
				$n++;
			}
		}
		$return['src'] = count($srcs) ? substr($srcs[0], 0, strpos($srcs[0], ' ')) : "$uploads[baseurl]/$thumb_data[file]";
		$return['srcset'] = implode(', ', array_filter($srcs));
	}
	else {
		$src = wp_get_attachment_image_src(
			$post_thumbnail_id, 
			$ventures_img_size_prefix.$ventures_img_default_size
		);
		if (!empty($src[0])) $return['src'] = $src[0];
		
		foreach($ventures_img_responsive_sizes as $index => $s) {
			$src = wp_get_attachment_image_src(
				$post_thumbnail_id, 
				$ventures_img_size_prefix.$s
			);
			if (empty($src[0]) || ($index > 0 && $src[0]===$return['src'])) continue;
			$return['srcset'] .= $src[0] .' '.$s.'w, ';
		}
	}
	$return['srcset'] = trim($return['srcset'], ' ,');
	
	return $return;
}

// takes e.g. https://www.facebook.com/username/ ---> facebook.com/username
function ventures_pretty_url($url) {
	return trim(preg_replace("/https?:\/\//i", '', $url), '/');
}

function ventures_play_icon($icon='play-circle') {
	return sprintf('<img class="play-icon %s" src="%s" alt="play">', $icon, get_template_directory_uri()."/img/$icon.svg"); 
}

function ventures_get_author_thumbnail_id($user_id) {
	global $wpdb;
	return ($thumb_id = get_the_author_meta($wpdb->base_prefix.'user_avatar', $user_id)) && wp_attachment_is_image($thumb_id) ? $thumb_id : 0;
}

function ventures_author_thumbnail($user_id, $echo=true, $user_data=null, $use_article_count=false, $size='medium') {
	global $wpdb;
	if ($user_data) {
		$user_name = ventures_get_user_name(0, $user_data);
		$thumb_id = $user_data->thumb_id;
		if ($user_data->thumb_data) $thumb_data = unserialize($user_data->thumb_data);
	}
	else {
		$user_name = ventures_get_user_name($user_id);
		$thumb_id = ventures_get_author_thumbnail_id($user_id);
	}
	if ($user_data && $use_article_count) {
		$p = ($user_data->article_count==1) ? '' : 's';
		$caption = $user_name.($use_article_count ? '<span class="article-count">'.$user_data->article_count.' article'.$p.'</span>' : '');
	}
	else $caption = $user_name;
	if ($thumb_id) {
		$image = wp_get_attachment_image($thumb_id, $size, false, array('alt' => "Picture of $user_name"));
		$src = preg_replace('/.*src="(.*?)".*/', '$1', $image);
		$s = sprintf('<figure class="thumbnail"><div class="round" style="background-image:url(%s);">%s</div><figcaption>%s</figcaption></figure>', $src, $image, $caption);
	}
	elseif (($src = preg_replace('/.*src="(.*?)".*/', '$1', get_avatar($user_id, 100))) && strpos($src, 'gravatar.com') !== FALSE) {
		$s = sprintf('<figure class="thumbnail"><div class="round" style="background-image:url(%s);"><img src="%s" alt="%s"></div><figcaption>%s</figcaption></figure>', $src, $src, 'Picture of '.$user_name, $caption);
	}
	else {
		$s = '<span class="figcaption">'.$caption.'</span>';
	}
	if ($echo) echo $s; else return $s;
}

function ventures_simple_top_banner($title, $top_left_label=null, $title_label=null) {
	print_top_story_banner(array(
		'type' => 'error',
		'bg_color' => '',
		'bg_pos' => 'center center',
		'share_links' => '',
		'image' => '',
		'top_left_label' => $top_left_label ? $top_left_label : get_top_story_top_left_label(),
		'title_label' => $title_label ? sprintf('<span class="category-label">%s</span>', $title_label) : get_formatted_top_story_title_label(),
		'content' => '<h1 class="entry-title small-bottom-bar">'.$title.'</h1>',
	));
}

function ventures_top_story_banner($post, $mute_category=false, $add_fuoc_script=true) {
	$bg_color = ($color = get_field('background_color', $post->ID)) ? "background-color:$color;" : '';
	$bg_pos = ($pos = get_field('background_position', $post->ID)) ? $pos : 'center center';
	$link_atts = array();
	$label = !QUERY_IS_SINGLE_RESULT ? '<span class="bullet-button normal-button inverted-colors">Just in</span>' : '';
	$image_link = '';
	
	if (isset($post->img_meta) && $post->img_meta) {
		$thumb_data = unserialize($post->img_meta);
		$img_srcs = ventures_resp_img(0, $thumb_data);
	}
	elseif ($thumb_id = get_post_thumbnail_id($post->ID)) {
		$img_srcs = ventures_resp_img($thumb_id);
		$thumb_data = wp_get_attachment_metadata($thumb_id);
	}
	
	//$hasVideo = get_post_meta($post->ID, '_fvp_video', true);
	// var_dump(has_post_video( $post->ID ));
	// var_dump(get_the_post_video( $post->ID, $size ));

	// exit;
	// if(has_post_video( $post->ID )){
	// 	$image = get_the_post_video( $post->ID, $size );
	// 	//var_dump($image);exit;
	// }else{
		$image = isset($img_srcs) ? sprintf('<img class="thumb fitted" style="object-position:%s;" src="%s" srcset="%s" sizes="100vw" alt="" data-original-width="%d">',
			$bg_pos,
			$img_srcs['src'],
			$img_srcs['srcset'],
			$thumb_data['width']
		) : '';
	//}
	
	if (QUERY_IS_SINGLE_RESULT && $post->post_type === 'ventures_interviews') {
		$content = wp_oembed_get(ventures_fetch_interview_video_url($post));
		if (strpos($content, 'soundcloud') === FALSE) $image = '';
	}
	elseif ($post->post_type === 'ventures_feature_ads' && is_acf_checkbox_checked('hide_text', $post->ID)) {
		$content = '';
		$image_link = sprintf('<a href="%s" target="_blank" class="imgwrap">&nbsp;</a>', esc_url(get_permalink($post)));
	}
	else {
		$content = sprintf('<h1 class="entry-title small-bottom-bar">%s</h1>', get_the_title($post)).ventures_play_icon();
		if (!QUERY_IS_SINGLE_RESULT) {
			if ($post->post_type === 'ventures_interviews') {
				$link_atts['data-video-html'] = wp_oembed_get(ventures_fetch_interview_video_url($post));
				$link_atts['data-description'] = sprintf('<h1>%s</h1><p>%s</p>', get_the_title($post), ventures_interview_excerpt($post));
			}
			if ($post->post_type === 'ventures_feature_ads') {
				$link_atts['target'] = '_blank';
			}
			else {
				$link_atts['rel'] = 'bookmark';
			}
			foreach ($link_atts as $key => $val) $atts[] = "$key=".'"'.esc_attr($val).'"';
			$content = sprintf('<a href="%s" class="gray-arrow" %s>%s</a>', esc_url(get_permalink($post)), empty($link_atts) ? '' : implode(' ', $atts), $content);
		}
		if (WP_DEBUG && !(is_single() || is_page())) {
			$content .= ventures_post_debug_info($post);
			if ($featured = get_selected_featured_post_id()) {
				if (defined('FEATURED_POST_COUNT') && FEATURED_POST_COUNT) {
					$content .= " (Featured post)";
				}
			}
		}
	}

	if ($post->post_type === 'ventures_feature_ads') {
		$image = str_replace('class="', 'class="never-blur ', $image);
	}else if ($add_fuoc_script) {
		$image = str_replace('class="', 'class="blur ', $image);
	}
	// else if(has_post_video( $post->ID )){
	// 	$image = str_replace('class="', 'class="never-blur ', $image);
	// }
	// else if ($add_fuoc_script) {
	// 	$image = str_replace('class="', 'class="blur ', $image);
	// }

	
	print_top_story_banner(array(
		'type' => $post->post_type,
		'bg_color' => $bg_color,
		'bg_pos' => $bg_pos,
		'share_links' => htmlspecialchars(json_encode(get_post_sharing_links($post))),
		'image' => $image,
		'top_left_label' => get_top_story_top_left_label(),
		'title_label' => get_formatted_top_story_title_label($post),
		'content' => $content,
		'image_link' => $image_link,
	));
	if ($add_fuoc_script) {
		echo '
		<script id="fuoc">
			var __fuoc_is_mobile = '.(ventures_is_mobile() ? 'true' : 'false').';
			var __fuoc_is_soundcloud = '.($post->post_type === 'ventures_interviews' && strpos($content, 'soundcloud') !== FALSE ? 'true' : 'false').';
		';
		include get_template_directory().'/js/header-fuoc.js';
		echo '</script>';
	}
}

function print_top_story_banner($data) {
	extract($data);
	printf(
		'<section class="top-story banner type--%s" style="%s" data-share_links="%s">
			<div class="imgwrap fitted-container cover" style="background-position:%s;">%s</div>
			%s
			<div class="inner">
				%s
				%s
			</div>
		</section>',
		$type,
		$bg_color,
		$share_links,
		$bg_pos,
		$image,
		empty($content) && !empty($image_link) ? $image_link : '',
		$title_label,
		$content
	);
}

function ventures_post_teaser($post, $sizes="100vw") {
	switch ($post->post_type) {
	case 'ventures_interviews':
		return ventures_interview_teaser($post);
	default:
		if (is_home() && !ventures_is_mobile()) {
			ob_start();
			ventures_top_story_banner($post, false, false);
			include(dirname(__DIR__).'/content-single.php');
			$full = ob_get_clean();
		}
		
		if (isset($post->img_meta) || isset($post->thumb_data)) {
			$thumb_data = ventures_post_image_data($post);
			$img_srcs = ventures_resp_img(0, $thumb_data);
			$image = array(
				'src' => $img_srcs['src'],
				'srcset' => $img_srcs['srcset'],
				'sizes' => $sizes,
				'width' => $thumb_data['width'],
			);
		}elseif(get_the_post_thumbnail_url($post->ID, 'medium') != ""){
			$image = array(
				'src' => get_the_post_thumbnail_url($post->ID, 'medium'),
				'srcset' => get_the_post_thumbnail_url($post->ID),
				'sizes' => '(min-width:760px) 728px, 100vw',
				//'width' => $thumb_data['width'],
			);
		}
		else {
			$image = NULL;
		}
		
		$label = get_top_story_title_label($post);
		$title = trim(get_the_title($post));
		$stopchars = ['.', '?', '!'];
		if (!in_array(strrev($title)[0], $stopchars)) $title = $title.'.';

		if (WP_DEBUG) {
			$title .= ventures_post_debug_info($post);
		}

		ventures_full_teaser(array(
			'classes' => array('post', "type-$post->post_type", $post->img_meta ? 'has-image' : 'no-image'),
			'related_posts' => isset($full) ? htmlspecialchars(json_encode(array_map('related_post', get_related_posts($post->ID)))) : '',
			'full_content' => isset($full) ? htmlspecialchars($full) : '',
			'url' => esc_url(get_the_permalink($post)),
			'title' => $title,
			'id' => $post->ID,
			'image' => $image,
			'label' => $label['title'],
			'teaser_text' => ventures_excerpt($post),
		));
	}
}


function ventures_post_more_teaser($post, $sizes="100vw") {
	switch ($post->post_type) {
	case 'ventures_interviews':
		return ventures_interview_teaser($post);
	default:
		if (is_home() && !ventures_is_mobile()) {
			ob_start();
			ventures_top_story_banner($post, false, false);
			include(dirname(__DIR__).'/content-single.php');
			$full = ob_get_clean();
		}
		
		if (isset($post->img_meta) || isset($post->thumb_data)) {
			$thumb_data = ventures_post_image_data($post);
			$img_srcs = ventures_resp_img(0, $thumb_data);
			$image = array(
				'src' => $img_srcs['src'],
				'srcset' => $img_srcs['srcset'],
				'sizes' => $sizes,
				'width' => $thumb_data['width'],
			);
		}elseif(get_the_post_thumbnail_url($post->ID, 'medium') != ""){
			$image = array(
				'src' => get_the_post_thumbnail_url($post->ID, 'medium'),
				'srcset' => get_the_post_thumbnail_url($post->ID),
				'sizes' => '(min-width:760px) 728px, 100vw',
				//'width' => $thumb_data['width'],
			);
		}
		else {
			$image = NULL;
		}
		
		$label = get_top_story_title_label($post);
		$title = trim(get_the_title($post));
		$stopchars = ['.', '?', '!'];
		if (!in_array(strrev($title)[0], $stopchars)) $title = $title.'.';

		if (WP_DEBUG) {
			$title .= ventures_post_debug_info($post);
		}

		ventures_full_teaser(array(
			'classes' => array('post', "type-$post->post_type", get_the_post_thumbnail_url($post->ID, 'medium') ? 'has-image' : 'no-image'),
			'related_posts' => isset($full) ? htmlspecialchars(json_encode(array_map('related_post', get_related_posts($post->ID)))) : '',
			'full_content' => isset($full) ? htmlspecialchars($full) : '',
			'url' => esc_url(get_the_permalink($post)),
			'title' => $title,
			'id' => $post->ID,
			'image' => $image,
			'label' => $label['title'],
			'teaser_text' => ventures_excerpt($post),
		));
	}
}

function ventures_interview_excerpt($post) {
	$description = ventures_fetch_interview_description($post);
	$excerpt = ventures_excerpt($post, 35, $description);
	if ($excerpt !== $description) $excerpt .= sprintf('... <a class="orange uppercase ventures-button no-overlay" href="%s">%s</a>', get_the_permalink($post), 'Read more');
	return $excerpt;
}

function ventures_excerpt($post, $word_count=35, $text='') {
	if (empty($text)) $text = !empty($post->post_excerpt) ? $post->post_excerpt : $post->post_content;
	$text = apply_filters( 'the_content', $text );
	$text = str_replace(']]>', ']]&gt;', $text);
	$text = wp_trim_words($text, $word_count, '');
	return apply_filters('get_the_excerpt', $text);
}

function ventures_full_teaser($data, $new_window=false) {
	extract($data);
	$sizes = empty($image['sizes']) ? '100vw' : $image['sizes'];
	$img = $image ? sprintf('<img class="thumb fitted cover" src="%s" srcset="%s" sizes="%s" alt="" data-original-width="%d">',
		$image['src'],
		$image['srcset'],
		$sizes,
		$image['width']
	) : '';
	if (ventures_is_mobile()) {
		$related_posts = '';
		$full_content = '';
		if (empty($show_on_mobile)) {
			$img = '';
		}
	}
	printf(
		'<article class="%s">
			<a class="teaser-content gray-arrow" href="%s" title="%s" data-post-id="%d" target="%s">
				<div class="imgwrap fitted-container cover">%s</div>
				<div class="background">&nbsp;</div>
				<span class="text"><span class="in">
					<div class="category category-label">%s</div>
					<strong>%s</strong>
					<span class="excerpt">%s</span>
				</span></span>
			</a>
		</article>',
		implode(' ', $classes),
		$url,
		$title,
		$id,
		isset($link_target) ? $link_target : '_self',
		$img,
		$label,
		$title,
		$teaser_text
	);
}

function ventures_post_search_teaser() {
	global $post;
	$category = get_top_story_title_label();
	$cat = (empty($category['title'])) ? '' : sprintf('<strong>%s</strong>', $category['title']);
	printf(
		'<li class="%s">
			<a href="%s">
				%s<span>%s</span>
			</a>
		</li>',	
		implode(' ', get_post_class('post')),
		esc_url(get_the_permalink()),
		$cat,
		get_the_title()
	);
}
add_filter('excerpt_length', function($l) { return 35; }, 999);
add_filter('excerpt_more', function() { return ''; });

add_filter('get_the_excerpt', 'end_with_sentence');
function end_with_sentence($excerpt) {
  $allowed_end = array('.', '!', '?', '...');
  $exc = explode(' ', $excerpt);
  $found = false;
  $last = '';
  while (!$found && !empty($exc)) { 
    $last = array_pop($exc);
    $end = strrev($last);
    $found = isset($end{0}) && in_array($end{0}, $allowed_end);
  }
  return (empty($exc)) 
  	? $excerpt.'...'
  	: rtrim(implode(' ', $exc) . ' ' .$last);
}

function ventures_featured_image_credit($post_id=0, $credit_text='', $credit_url='') {
	global $post;
	$text = '';
	if (empty($credit_text)) {
		if (empty($post_id)) $post_id = $post->ID;
		$attpost = get_post(get_post_thumbnail_id($post_id));
		$credit_text = get_media_credit($attpost);
		$credit_url = get_media_credit_url($attpost);
	}
	if (!empty($credit_text)) {
		$text = sprintf('<span>Photograph — %s</span>', $credit_text);
		if (!empty($credit_url)) $text = sprintf('<a class="credit-link" href="%s" target="_blank">%s</a>', $credit_url, $text);
	}
	return sprintf('<header class="credit">%s</header>', $text);
}

function ventures_narrow_post_teaser($post, $use_thumb=false) {
	$info = ventures_post_info($post, '(min-width:760px) 220px, 100vw');
	$label = get_top_story_title_label($post);
	extract($info);
	if ($is_idea) {
		printf(
			'<a href="%s">
				<div class="text">
					%s
				</div>
			</a>',
			esc_url($author['url']),
			$author['img']
		);
	}
	else {
		if (ventures_is_mobile()) {
			$image = '';
		}
		else {
			$image = $use_thumb ? ($is_idea ? $author['img'] : (strpos($img, 'blank.gif') ? '' : $img)) : '';
		}
		printf(
			'<article class="%s">
				<a class="teaser-content small-bottom-bar" href="%s" data-video-html="%s">
					<div class="text">
						<div class="category-button category-label">%s</div>
						<p><strong>%s</strong></p>
					</div>
					<div class="image fitted-container cover">%s</div>
				</a>
			</article>',
			"type-$type",
			esc_url($is_idea ? $author['url'] : $url),
			$video_url,
			$label['title'],
			$is_idea ? '' : trim($title, '.'),
			$image
		);
	}
}

function ventures_interview_teaser($post=null) {
	$info = ventures_post_info($post);
	extract($info);
	if (WP_DEBUG) {
		$title .= ventures_post_debug_info($post);
	}
	$video = wp_oembed_get(ventures_fetch_interview_video_url($post));
	if (strpos($video, 'soundcloud') !== FALSE) {
		$video = '<div class="overlay-player"><div class="imgwrap fitted-container cover blur-container">'.$img.'</div>'.$video.'</div>';
	}
	printf(
		'<article class="%s">
			<a class="teaser-content" title="%s" data-post-id="%d" href="%s" data-video-html="%s" data-description="%s" data-share_links="%s">
				<div class="imgwrap fitted-container cover">%s</div>
				<div class="background">&nbsp;</div>
				<span class="text">
					<span class="in">
						<div class="category category-label">%s</div>
						<strong>%s</strong> <span class="excerpt">%s</span>
					</span>
				</span>
				%s
			</a>
		</article>',
		implode(' ', get_post_class('post has-image teaser-article')),
		$title,
		$id,
		esc_url($url),
		esc_attr($video),
		htmlspecialchars("<h1>$title</h1><p>".ventures_interview_excerpt($post)."</p>"),
		htmlspecialchars(json_encode(get_post_sharing_links($post))),
		$img,
		ventures_play_icon('label-play-icon').$category['title'],
		$title,
		strip_tags(ventures_excerpt($post)),
		ventures_play_icon()
	);
}

/**
 * Flush out the transients used in ventures_categorized_blog.
 */
function ventures_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'ventures_categories' );
}
add_action( 'edit_category', 'ventures_category_transient_flusher' );
add_action( 'save_post',     'ventures_category_transient_flusher' );
