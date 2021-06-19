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

$posts = array_merge($_POSTS['top'], isset($_POSTS['latest']) ? $_POSTS['latest'] : array());
get_header(); ?>

	<div id="primary" class="content-area">
		<?php if (count($posts)): ?>
		<main id="main" class="site-main two-column" role="main">

			<?php ventures_top_story_banner($posts[0]); ?>
			
			<?php if (count($posts) > 1): ?>
			<section id="interviews-listing" class="column-row">
				<ul class="horizontal-list teaser-list interview-teasers">
					<?php for ($i = 0, $cols = 2, $l = count($posts) - 1; $i < $l; $i++): ?>
					<li class="row-<?php echo floor($i / $cols) + 1; ?> col-<?php echo $i % $cols + 1; ?>"><?php ventures_interview_teaser($posts[$i + 1]); ?></li>
					<?php endfor; ?>
				</ul>
			</section>
			<?php endif; ?>

			<?php ventures_showad_fullwidth(); ?>

			<?php include(__DIR__.'/inc/category-boxes.php'); ?>

		</main><!-- #main -->
		<?php else: ?>
		<main id="main" class="site-main" role="main">
			<?php ventures_simple_top_banner("Interviews", null, "No interviews found"); ?>
			<div id="page-content" class="entry-content">&nbsp;</div>
			<?php include(__DIR__.'/inc/category-boxes.php'); ?>
		</main>
		<?php endif; ?>
	</div><!-- #primary -->

<?php get_footer(); ?>
