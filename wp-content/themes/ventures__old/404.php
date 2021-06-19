<?php
/**
 * The template for displaying all single posts.
 *
 * @package ventures
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main" data-body-class="<?php echo implode(' ', get_body_class()); ?>">

			<?php ventures_simple_top_banner("Page Not Found"); ?>

			<article id="post" <?php post_class('post no-credit'); ?>>
				<div class="entry-content">
					<div class="post-content">
						This page could not be found on the site.
					</div>
				</div>
			</article>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php wp_reset_query(); wp_reset_postdata(); get_footer(); ?>
