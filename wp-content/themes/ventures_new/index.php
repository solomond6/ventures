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
			
			<?php if (current($_POSTS['latest'])): $use_thumb = latest_articles_image_indexes(count($_POSTS['latest'])); ?>
			<section id="latest-articles" class="column-left column">
				<h2><?php echo ventures_narrow_teasers_title(); ?></h2>
				<?php if (ventures_is_mobile()): ?>
					<a id="dailybrief-button" class="bottom-fixed-button normal-button" href="/daily-brief">Daily Brief <?php echo file_get_contents(__DIR__.'/img/arrows/triangle.svg'); ?></a>
				<?php endif; ?>
				<ul class="narrow-teaser-list clean-list">
					<?php while (current($_POSTS['latest'])): ?>
					<li class="<?php echo !ENABLE_NARROW_TEASERS_CAROUSEL || key($_POSTS['latest']) < LATEST_ARTICLES_PER_PAGE ? 'visible' : 'hidden'; ?>"><?php ventures_narrow_post_teaser(current($_POSTS['latest']), in_array(key($_POSTS['latest']), $use_thumb)); ?></li>
					<?php next($_POSTS['latest']); endwhile; ?>
				</ul>
			</section><?php endif;

			if (next($_POSTS['top']) !== FALSE):

			ventures_showad_mobile();

			?><section id="top-stories" class="column-right column">
				<h2><?php echo ventures_teaser_title('Top', 'Stories'); ?><?php ventures_top_stories_headline(); ?></h2>
				<ul class="teaser-list clean-list">
					<?php for ($i = 0; $i < 3 && key($_POSTS['top']) + 1 < count($_POSTS['top']); $i++): ?>
					<li><?php ventures_post_teaser(current($_POSTS['top'])); next($_POSTS['top']); ?></li>
					<?php endfor; ?>
					<?php if (key($_POSTS['top']) + 1 < count($_POSTS['top'])): ?>
					<li><?php ventures_inline_teaser_ad(); ?></li>
					<?php while (next($_POSTS['top']) !== FALSE): ?>
					<li><?php ventures_post_teaser(current($_POSTS['top'])); ?></li>
					<?php endwhile; endif; ?>
				</ul>
			</section>
			<?php endif; ?>

			<?php ventures_showad_fullwidth(); ?>

			<?php include(__DIR__.'/inc/category-boxes.php'); ?>

		</main><!-- #main -->
		<?php else: ?>
		<main id="main" class="site-main" role="main">
			<?php ventures_simple_top_banner(""); ?>
			<div id="page-content" class="entry-content">&nbsp;</div>
			<?php include(__DIR__.'/inc/category-boxes.php'); ?>
		</main>
		<?php endif; ?>
	</div><!-- #primary -->

<?php wp_reset_query(); get_footer(); ?>
