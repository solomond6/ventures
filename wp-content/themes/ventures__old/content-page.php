<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package ventures
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div id="page-content" class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->
</article><!-- #post-## -->
