<?php 
get_header();
$main_column_size = bootstrapBasicGetMainColumnSizeAchieve();
?> 
<?php get_sidebar('left'); ?>
<header class="article-infograph-story">

	<div class="container">
		<div class="col-md-10 col-md-offset-1">
			<h1 class="page-title">
			<?php
				if (is_category()) :
					single_cat_title();

				elseif (is_tag()) :
					single_tag_title();

				elseif (is_author()) :
					/* Queue the first post, that way we know
					 * what author we're dealing with (if that is the case).
					 */
					the_post();
					printf(__('Author: %s', 'bootstrap-basic'), '<span class="vcard">' . get_the_author() . '</span>');
					/* Since we called the_post() above, we need to
					 * rewind the loop back to the beginning that way
					 * we can run the loop properly, in full.
					 */
					rewind_posts();

				elseif (is_day()) :
					printf(__('Day: %s', 'bootstrap-basic'), '<span>' . get_the_date() . '</span>');

				elseif (is_month()) :
					printf(__('Month: %s', 'bootstrap-basic'), '<span>' . get_the_date('F Y') . '</span>');

				elseif (is_year()) :
					printf(__('Year: %s', 'bootstrap-basic'), '<span>' . get_the_date('Y') . '</span>');

				elseif (is_tax('post_format', 'post-format-aside')) :
					_e('Asides', 'bootstrap-basic');

				elseif (is_tax('post_format', 'post-format-image')) :
					_e('Images', 'bootstrap-basic');

				elseif (is_tax('post_format', 'post-format-video')) :
					_e('Videos', 'bootstrap-basic');

				elseif (is_tax('post_format', 'post-format-quote')) :
					_e('Quotes', 'bootstrap-basic');

				elseif (is_tax('post_format', 'post-format-link')) :
					_e('Links', 'bootstrap-basic');

				else :
					_e($GLOBALS['wpse16243_title'], 'bootstrap-basic');

				endif;
				?> 
			</h1>
		</div>
	</div>
	<?php
	// Show an optional term description.
	$term_description = term_description();
	if (!empty($term_description)) {
		printf('<div class="taxonomy-description">%s</div>', $term_description);
	} //endif;
	?>
</header><!-- .page-header -->

<div class="container">
	<div class="col-md-<?php echo $main_column_size; ?> content-area" id="main-column">
		<main id="main" class="site-main" role="main">
			<?php if (have_posts()) { ?> 
			<?php 
			/* Start the Loop */
			while (have_posts()) {
				the_post();

				/* Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part('content', get_post_format());
			} //endwhile; 
			?> 

			<?php bootstrapBasicPagination(); ?> 

			<?php } else { ?> 

			<?php get_template_part('no-results', 'archive'); ?> 

			<?php } //endif; ?> 
		</main>
	</div>
</div>
<?php get_sidebar('eck');?> 
<?php get_footer(); ?> 