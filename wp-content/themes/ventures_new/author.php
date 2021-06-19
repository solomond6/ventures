<?php
global $post;
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
$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php ventures_simple_top_banner(ventures_get_user_name($curauth->ID), 'Author'); ?>

			<article id="author-<?php echo $curauth->ID; ?>" class="post author-post">
				<div class="entry-content">
					<div class="post-content">
						<div class="text">
							<?php echo apply_filters('the_content', $curauth->user_description); ?>
						</div>
					</div>
					<div class="post-asides">
						<aside class="article-info">
							<div class="thumbnail-wrap">
								<?php ventures_author_thumbnail($curauth->ID); ?>
								<?php /*
								<h2 class="ctcond uppercase">Contact</h2>
								<a class="user-email" href="mailto:<?php echo $curauth->user_email; ?>"><?php echo $curauth->user_email; ?></a>
								*/ ?>
								<ul class="share-buttons-list horizontal-list">
									<?php if (!empty($curauth->facebook)): ?>
									<li class="facebook"><a target="_blank" href="<?php echo $curauth->facebook; ?>">
										<?php echo file_get_contents(get_template_directory()."/img/social/facebook.svg"); ?>
										<span><?php echo ventures_pretty_url($curauth->facebook); ?></span>
									</a></li>
									<?php endif; ?>
									<?php if (!empty($curauth->twitter)): ?>
									<li class="twitter"><a target="_blank" href="https://twitter.com/<?php echo $curauth->twitter; ?>">
										<?php echo file_get_contents(get_template_directory()."/img/social/twitter.svg"); ?>
										<span><?php echo ventures_pretty_url($curauth->twitter); ?></span>
									</a></li>
									<?php endif; ?>
									<?php if (!empty($curauth->user_url)): ?>
									<li class="linkedin"><a target="_blank" href="<?php echo $curauth->user_url; ?>">
										<?php echo file_get_contents(get_template_directory()."/img/social/linkedin.svg"); ?>
										<span><?php echo ventures_pretty_url($curauth->user_url); ?></span>
									</a></li>
									<?php endif; ?>
								</ul>
							</div>
						</aside>
					</div>
					<?php if (isset($_POSTS['latest']) && !empty($_POSTS['latest'])): ?>
					<section id="top-stories">
						<h3>Latest Articles by <?php echo ventures_get_user_name($curauth->ID); ?></h3>
						<ul class="teaser-list clean-list">
							<?php while (current($_POSTS['latest'])): ?>
							<li><?php ventures_post_teaser(current($_POSTS['latest'])); ?></li>
							<?php next($_POSTS['latest']); endwhile; ?>
						</ul>
					</section>
					<?php else: ?>
						<h3>No articles yet by <?php echo ventures_get_user_name($curauth->ID); ?></h3>
					<?php endif; ?>
				</div>
			</article>


		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
