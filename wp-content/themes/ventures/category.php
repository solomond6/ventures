<?php get_header(); ?>
<?php 
	$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1; 
	$current_page = get_queried_object();
	$category     = $current_page->cat_name;
	//var_dump($category);exit;
?>
<?php //var_dump($paged);exit; ?>
	<?php if($paged == 1){ ?>
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
					</section>
				<?php endif;

				if (current($_POSTS['top']) !== FALSE):
					ventures_showad_mobile();
				?>
				<section id="top-stories" class="column-right column">
					<h2><?php echo ventures_teaser_title('Top', 'Stories'); ?><?php ventures_top_stories_headline(); ?></h2>
					<ul class="teaser-list clean-list">

						<?php $count = count($_POSTS['top']);

						foreach (array_slice(array_filter($_POSTS['top'], function($post) { return $post->post_type !== 'ventures_feature_ads'; }), 1) as $i => $post): ?>
						<li><?php ventures_post_teaser($post, '(min-width:760px) 728px, 100vw'); ?></li>
						<?php if ($i === 0 && $count > 2) ventures_inline_teaser_ad(); ?>
						<?php endforeach; ?>
					</ul>
					<br/>
					<?php 
						the_posts_pagination(array(
							    'mid_size'  => 6,
							    'prev_text' => __('Prev Posts'),
							    'next_text' => __('Next Posts'),
							)); 
					?>
				</section>
				<?php else: 
					ventures_showad_mobile();
				?>
				<section id="top-stories" class="column-right column">
					<h2><?php echo ventures_teaser_title('Top', 'Stories'); ?><?php ventures_top_stories_headline(); ?></h2>
					<ul class="teaser-list clean-list">
						<?php $count = count($_POSTS['top']);
						echo('<pre>');
					print_r($_POSTS['top']);
					echo('</pre>');
					exit;
							foreach (array_slice(array_filter($_POSTS['top'], function($post) { return $post->post_type !== 'ventures_feature_ads'; }), 0) as $i => $post): ?>
						<li><?php ventures_post_teaser($post, '(min-width:760px) 728px, 100vw'); ?></li>
						<?php if ($i === 0 && $count > 2) ventures_inline_teaser_ad(); ?>
						<?php endforeach; ?>
					</ul>
					<br/>
					<?php 
						the_posts_pagination(array(
							    'mid_size'  => 10,
							    'prev_text' => __('Prev Posts'),
							    'next_text' => __('Next Posts'),
							)); 
					?>
				</section>
				<?php endif; ?>
				<section id="top-stories" class="column-right column">
					<?php 
						$current_page = get_queried_object();
					    $category = $current_page->post_name;
				        $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
				        $query = new WP_Query( 
				            array(
				                'paged'         => $paged, 
				                'category_name' => $category,
				                'order'         => 'asc',
				                'post_type'     => 'post',
				                'post_status'   => 'publish',
				            )
				        );
				        // if ($query->have_posts()) {
				        //     next_posts_link( 'Older Entries', $query->max_num_pages);
				        //     previous_posts_link( 'Newer Entries' );
				        //     wp_reset_postdata();
				        // }
					?>
					
				</section>
				
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
	<?php }else{ ?>
	<div id="primary" class="content-area">

		<main id="main" class="site-main post-asides" role="main">
			<section class="top-story banner bg-replace"
				data-bg-src="<?php echo reset(wp_get_attachment_image_src(get_post_thumbnail_id(), 'top-story')); ?>"
				data-bg-src2x="<?php echo reset(wp_get_attachment_image_src(get_post_thumbnail_id(), 'top-story-2x')); ?>">
				<div class="inner">
					<a href="#"><h1 class="entry-title small-bottom-bar"><?php echo $category; ?></h1></a>
				</div>
			</section>
			<article class="post no-credit single2">
				<div class="entry-content" style="margin-top:0em; margin-bottom: 0em;">
					<?php
						$query = new WP_Query( 
				            array(
				                'paged' => $paged, 
				                'category_name' => $category,
				                'order' => 'asc',
				                'post_type' => 'post',
				                'post_status' => 'publish',
				            )
				        );

				        // if ($query->have_posts()) {
				        //     next_posts_link( 'Older Entries', $query->max_num_pages);
				        //     previous_posts_link( 'Newer Entries' );
				        //     wp_reset_postdata();
				        // }
					?>
					<div class="post-content">
						<h2><?php echo ventures_teaser_title('Archive', 'Stories'); ?><?php ventures_top_stories_headline(); ?></h2>
						<ul class="teaser-list clean-list">
							<?php $count = count($query->posts);
								foreach (array_slice(array_filter($query->posts, function($post) { return $post->post_type !== 'ventures_feature_ads'; }), 0) as $i => $post): ?>
							<li><?php ventures_post_more_teaser($post, '(min-width:760px) 728px, 100vw'); ?></li>
							<?php if ($i === 0 && $count > 2) ventures_inline_teaser_ad(); ?>
							<?php endforeach; ?>
						</ul>
						<br/>
						<div class="prev-button">
							<?php previous_posts_link('Prev Posts', $query->max_num_pages ); ?>
						</div>
						<div class="next-button">
				        	<?php next_posts_link('Next Posts'); ?>
				        </div>
						<?php wp_reset_postdata(); ?>
					</div>
					<div class="post-asides">
						<?php ventures_showad_articleside(); ?>
						<br/>
						<?php ventures_showad_articleside(); ?>
						<br/>
						<?php ventures_showad_articleside(); ?>
					</div>
				</div>
				<?php ventures_showad_fullwidth(); ?>
			</article>
			
			<?php include(__DIR__.'/inc/category-boxes.php'); ?>
		</main>
	</div>
	<?php } ?>
<?php wp_reset_query(); get_footer(); ?>
