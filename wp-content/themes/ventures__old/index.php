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

			<header class="top-story-banners">
				<?php for ($i = 0; $i < max(1, FEATURED_POST_COUNT); $i++, next($_POSTS['top'])) ventures_top_story_banner(current($_POSTS['top'])); ?>
			</header>

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

			if (current($_POSTS['top']) !== FALSE):

			ventures_showad_mobile();

			?><section id="top-stories" class="column-right column">
				<h2><?php echo ventures_teaser_title('Top', 'Stories'); ?><?php ventures_top_stories_headline(); ?></h2>
				<ul class="teaser-list clean-list">
					<?php $count = count($_POSTS['top']); foreach (array_slice(array_filter($_POSTS['top'], function($post) { return $post->post_type !== 'ventures_feature_ads'; }), 1) as $i => $post): ?>
					<li><?php ventures_post_teaser($post, '(min-width:760px) 728px, 100vw'); ?></li>
					<?php if ($i === 0 && $count > 2) ventures_inline_teaser_ad(); ?>
					<?php endforeach; ?>
				</ul>
			</section>
			<?php else: 
				ventures_showad_mobile();
			?>
			<section id="top-stories" class="column-right column">
				<h2><?php echo ventures_teaser_title('Top', 'Stories'); ?><?php ventures_top_stories_headline(); ?></h2>
				<ul class="teaser-list clean-list">
					<?php $count = count($_POSTS['top']);

					foreach (array_slice(array_filter($_POSTS['top'], function($post) { return $post->post_type !== 'ventures_feature_ads'; }), 0) as $i => $post): ?>
					<li><?php ventures_post_teaser($post, '(min-width:760px) 728px, 100vw'); ?></li>
					<?php if ($i === 0 && $count > 2) ventures_inline_teaser_ad(); ?>
					<?php endforeach; ?>
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
