<?php get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php if ($_POSTS['top']->have_posts()): $_POSTS['top']->the_post(); ?>
			<section class="top-story banner bg-replace"
				data-bg-src="<?php echo reset(wp_get_attachment_image_src(get_post_thumbnail_id(), 'top-story')); ?>"
				data-bg-src2x="<?php echo reset(wp_get_attachment_image_src(get_post_thumbnail_id(), 'top-story-2x')); ?>">
				<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
			</section>
			<?php wp_reset_postdata(); endif; ?>
			
			<section id="latest-articles">
				<?php while ($_POSTS['latest']->have_posts()) : $_POSTS['latest']->the_post(); ?>
				<article <?php post_class(); ?>>
					<p><?php the_author(); ?></p>
					<?php the_excerpt(); ?>
					<p><a href="<?php the_permalink(); ?>">See more</a></p>
				</article>
				<?php endwhile; wp_reset_postdata(); ?>
			</section>

			<?php ventures_showad_fullwidth(); ?>

			<section id="top-stories">
				<?php while ($_POSTS['top']->have_posts()) : $_POSTS['top']->the_post(); ?>
				<article <?php post_class(); ?>>
					<p><?php the_author(); ?></p>
					<?php the_excerpt(); ?>
					<p><a href="<?php the_permalink(); ?>">See more</a></p>
				</article>
				<?php endwhile; wp_reset_postdata(); ?>
				<button id="more-top-stories-button">More news</button>
			</section>

			<?php ventures_showad_fullwidth(); ?>

			<?php include(__DIR__.'/inc/category-boxes.php'); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
