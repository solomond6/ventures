<?php
/**
 * The template for displaying all single posts.
 *
 * @package ventures
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php global $_POSTS; $_POSTS['latest']->rewind_posts(); the_post(); ventures_top_story_banner(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class((ventures_has_image_credit() ? 'has' : 'no').'-credit'); ?>>
				<div class="entry-content">
					<?php echo apply_filters('the_content', ventures_fetch_interview_description()); ?>
				</div><!-- .entry-content -->
			</article><!-- #post-## -->

			<?php include(__DIR__.'/inc/category-boxes.php'); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
