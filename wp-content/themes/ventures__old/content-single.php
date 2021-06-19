<?php 

$truncate = false;
if ($post->post_type === 'ventures_tn' || $post->post_type === 'ventures_aj'|| $post->post_type === 'ventures_apo') { 
	$url = get_post_meta($post->ID, 'tn_url', true);
	$host = str_replace('www.', '', parse_url($url, PHP_URL_HOST));
	$name = get_post_meta($post->ID, 'tn_source_name', true); 
	if ($post->post_type === 'ventures_tn') {
		$truncate = get_field('truncate_disabled');
		$truncate = ($truncate==='disabled') ? false : true;
	}
}

?>
<article id="post-<?php echo $post->ID; ?>" <?php if (is_single()) post_class('post '.(ventures_has_image_credit() ? 'has' : 'no').'-credit'); else printf('class="post type-%s %s-credit"', $post->post_type, isset($post->img_meta_value) && !empty($post->img_meta_value['image_meta']['credit']) ? 'has' : 'no'); ?>>
	<div class="entry-content">
		<?php echo ventures_featured_image_credit(); ?>
		<?php if (ventures_is_mobile()) include(__DIR__.'/inc/mobile_share_links.php'); ?>
		<div class="post-content">
			<?php echo apply_filters('the_content', (empty($truncate) ? $post->post_content : tn_truncate_content($post->post_content))); ?>
			<?php if ($post->post_type === 'ventures_tn'): ?>
			<p>Read more at <a target="_blank" href="<?php echo $url; ?>"><?php echo empty($name) ? $host : $name; ?></a></p>
			<?php endif; ?>
		</div>
		<div class="post-asides">
			<aside class="article-info">
				<?php if ($post->post_type === 'ventures_tn' || $post->post_type === 'ventures_aj'|| $post->post_type === 'ventures_apo'): ?>
				<h3>Author</h3>
				<div class="post-author-name more-info">
				<a class="url fn n" target="_blank" href="<?php echo $url; ?>"><?php echo empty($name) ? $host : $name; ?></a>
				</div>
				<?php else: ?>
				<?php if ($author_thumb_id = ventures_get_author_thumbnail_id($post->post_author)): ?>
				<div class="author vcard">
					<a class="url fn n" href="<?php echo esc_url(get_author_posts_url($post->post_author)); ?>">
						<?php ventures_author_thumbnail($post->post_author); ?>
					</a>
				</div>
				<?php endif; ?>
				<h3>Author</h3>
				<div class="post-author-name more-info">
					<a class="url fn n" href="<?php echo esc_url(get_author_posts_url($post->post_author)); ?>">
						<?php echo ventures_get_user_name($post->post_author); ?>
					</a>
				</div>
				<?php endif; ?>
				<h3>Published</h3>
				<div class="post-date more-info"><?php echo get_the_date('', $post); ?></div>
			</aside>
			<?php ventures_showad_articleside(); ?>
			<aside class="article-url">
				<h3>Direct Link</h3>
				<p><input class="post-permalink text-select" value="<?php echo wp_get_shortlink($post->ID, 'post'); ?>" readonly></p>
			</aside>
		</div>
		<?php if (ventures_is_mobile()) include(__DIR__.'/inc/mobile_share_links.php'); ?>
	</div><!-- .entry-content -->
	<?php ventures_showad_fullwidth(); ?>
</article><!-- #post-## -->
<div id="infinite-scroll-target" class="clearfix"></div>
