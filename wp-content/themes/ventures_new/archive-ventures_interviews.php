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

get_header(); ?>

	<div id="primary" class="content-area">
		<?php if (isset($_POSTS['top']) && !empty($_POSTS['top']) && isset($_POSTS['latest']) && !empty($_POSTS['latest'])): ?>
		<main id="main" class="site-main two-column" role="main">

			<?php ventures_top_story_banner(current($_POSTS['top'])); ?>
			
			<?php if (count($_POSTS['top']) || $_POSTS['latest']->have_posts()): ?>
			<section id="interviews-listing" class="column-row">
				<ul class="horizontal-list teaser-list interview-teasers">
					<?php $i = 0; $cols = 2; while (next($_POSTS['top']) !== FALSE): ?>
					<li class="row-<?php echo floor($i / $cols) + 1; ?> col-<?php echo $i % $cols + 1; ?>"><?php ventures_interview_teaser(current($_POSTS['top'])); ?></li>
					<?php $i++; endwhile; while (current($_POSTS['latest'])): ?>
					<li class="row-<?php echo floor($i / $cols) + 1; ?> col-<?php echo $i % $cols + 1; ?>"><?php ventures_interview_teaser(current($_POSTS['latest'])); ?></li>
					<?php $i++; next($_POSTS['latest']); endwhile; ?>
				</ul>
			</section>
			<?php endif; ?>

			<?php ventures_showad_fullwidth(); ?>

			<?php include(__DIR__.'/inc/category-boxes.php'); ?>

		</main><!-- #main -->
		<?php else: ?>
		<main id="main" class="site-main" role="main">
			<?php ventures_simple_top_banner("Interviews"); ?>
			<div id="page-content" class="entry-content">&nbsp;</div>
			<?php include(__DIR__.'/inc/category-boxes.php'); ?>
		</main>
		<?php endif; ?>
	</div><!-- #primary -->

<?php get_footer(); ?>
