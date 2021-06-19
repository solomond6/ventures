<?php global $post; ?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post '.(ventures_has_image_credit() ? 'has' : 'no').'-credit'); ?>>
	<div class="entry-content">
		<?php ventures_featured_image_credit(); ?>
		<div class="post-content"><?php the_content(); ?></div>
		<div class="post-asides">
			<aside class="article-info">
				<?php if (ventures_get_author_thumbnail_id(get_the_author_meta('ID'))): ?>
				<div class="author vcard">
					<a class="url fn n" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
						<?php ventures_author_thumbnail(get_the_author_meta('ID')); ?>
					</a>
				</div>
				<?php endif; ?>
				<h3>Author</h3>
				<div class="post-author-name more-info">
					<a class="url fn n" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
						<?php echo ventures_get_user_name(get_the_author_meta('ID')); ?>
					</a>
				</div>
				<h3>Published</h3>
				<div class="post-date more-info"><?php the_date(); ?></div>
			</aside>
			<?php ventures_showad_articleside(); ?>
			<aside class="article-url">
				<h3>Direct Link</h3>
				<p><input class="post-permalink text-select" value="<?php echo get_permalink($post->ID); ?>" readonly></p>
			</aside>
		</div>
	</div><!-- .entry-content -->
	<?php ventures_showad_fullwidth(); ?>
</article><!-- #post-## -->
<div id="infinite-scroll-target" class="clearfix"></div>
