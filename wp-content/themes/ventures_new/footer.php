<?php
global $_POSTS;
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package ventures
 */
?>
<?php if (defined('SAVEQUERIES') && SAVEQUERIES): ?>
		<div class="queries">
			<?php global $wpdb; ?>
			<p><strong><?php echo count($wpdb->queries); ?> Queries</strong></p>
			<table class="querylog" style="font: normal 11px monospaced; width: 95%; margin: 0 auto; " border="1">
				<?php foreach($wpdb->queries as $q) {
					echo '
					<tr><td style="padding:10px;">'.$q[0].'</td><td style="padding:10px;">'.$q[1].'</td></tr>';
				} ?>
			</table>
		</div>
<?php endif; ?>

	</div><!-- #content -->

	<footer id="colophon" class="site-footer uppercase" role="contentinfo">
		<nav id="footer-nav">
			<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
			<?php wp_nav_menu( array( 'theme_location' => 'footer' ) ); ?>
			<div id="footer-share"><?php include(__DIR__.'/inc/external-links.php'); ?></div>
		</nav>
		<div id="footer-mailing-list" class="mailing-list-form-container"><?php include(__DIR__.'/inc/mailing-list-form.php'); ?></div>
		<p id="footer-copyright">&copy; 2015 Ventures Africa. All rights reserved</p>
	</footer><!-- #colophon -->
</div><!-- #page -->
<?php if (!ventures_is_mobile()): ?>
	<a id="dailybrief-button" class="bottom-fixed-button normal-button" href="/daily-brief">Daily Brief <?php echo file_get_contents(__DIR__.'/img/arrows/triangle.svg'); ?></a>
<?php endif; ?>
<div id="share-button" class="bottom-fixed-button">
	<button class="clean-button uppercase" aria-controls="share-links" aria-expanded="false">Share</button>
	<?php echo file_get_contents(get_template_directory().'/img/arrows/triangle.svg'); ?>
	<?php if (isset($_POSTS['top']) || isset($_POSTS['latest'])): ?>
	<ul id="share-links" class="share-buttons-list horizontal-list">
		<?php foreach (get_post_sharing_links() as $link): extract($link); ?>
		<li class="<?php echo $slug; ?>">
			<?php if (!empty($url)): ?>
			<a target="_blank" href="<?php echo $url; ?>" title="Share on <?php echo $title; ?>">
				<?php echo file_get_contents(get_template_directory()."/img/social/$slug.svg"); ?>
			</a>
			<?php else: ?>
			<button class="clean-button" title="Share on <?php echo $title; ?>"><?php echo file_get_contents(get_template_directory()."/img/social/$slug.svg"); ?></button>
			<?php endif; ?>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>
<div id="overlay-container">
	<div id="overlay-bg"></div>
	<div id="video-overlay" class="fixed-overlay"></div>
</div>
<button id="overlay-close-button" class="clean-button" aria-expanded="false">
	<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 20 20" width="20" height="20" role="img" aria-label="Close Menu">
		<title>Close Menu</title>
		<path d="M 1 1 L 19 19 M 1 19 L 19 1"></path>
	</svg>
</button>
<?php wp_footer(); ?>

</body>
</html>
