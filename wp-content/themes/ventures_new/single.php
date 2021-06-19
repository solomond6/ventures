<?php
global $post;
/**
 * The template for displaying all single posts.
 *
 * @package ventures
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main" data-body-class="<?php echo implode(' ', get_body_class()); ?>">

			<?php
				the_post();
				ventures_top_story_banner($post, false, true);
				if (get_post_type()==='ventures_briefs') {
					get_template_part( 'content', 'briefs' );
				}
				else {
					get_template_part( 'content', 'single' );
				}
			?>

			<?php include(__DIR__.'/inc/category-boxes.php'); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php wp_reset_query(); wp_reset_postdata(); get_footer(); ?>
