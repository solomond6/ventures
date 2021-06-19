<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php //if(is_home()){}else{?><h1 class="entry-title"><?php //the_title(); ?></h1><?php //} ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?> 
		<div class="clearfix"></div>
		<?php
		wp_link_pages(array(
			'before' => '<div class="page-links">' . __('Pages:', 'bootstrap-basic') . ' <ul class="pagination">',
			'after'  => '</ul></div>',
			'separator' => ''
		));
		?>
	</div><!-- .entry-content -->
</article><!-- #post-## -->