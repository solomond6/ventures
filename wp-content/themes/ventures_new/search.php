<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package ventures
 */

global $wp_query;
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php ventures_simple_top_banner("$wp_query->post_count Results For &ldquo;{$wp_query->query['s']}&rdquo;", 'Search Results', 'Search Results'); ?>
			
			<div id="page-content" class="entry-content">
				<?php if (have_posts()): ?>
				<section id="top-stories">
					<ul class="teaser-list clean-list">
						<?php while (have_posts()): the_post(); ?>
						<li><?php ventures_post_teaser(); ?></li>
						<?php endwhile; ?>
					</ul>
				</section>
				<?php else: ?>
				<p id="search-empty" class="message">Sorry, no results were found for <strong><?php echo $wp_query->query['s']; ?></strong>.</p>
				<?php endif; ?>
			</div>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
