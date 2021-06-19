<?php
get_header();
$main_column_size = bootstrapBasicGetMainColumnSize();
?> 
<?php get_sidebar('left'); ?>
<?php
while (have_posts()) {
	the_post();

	$current_id = get_the_ID();
	$category_detail=get_the_category($current_id);
	
?>
<?php if($category_detail[0]->name =="Main Story"){ ?>
	<header class="article-main-story">
		<div class="container">
			<div class="col-md-10 col-md-offset-1">
				<h1 class="page-title">
					<?php echo $category_detail[0]->name; ?>
				</h1>
			</div>
		</div>
	</header>
<?php }elseif($category_detail[0]->name =="Interview"){?>
	<header class="article-interview-story">
		<div class="container">
			<div class="col-md-10 col-md-offset-1">
				<h1 class="page-title">
					<?php echo $category_detail[0]->name; ?>
				</h1>
			</div>
		</div>
	</header>
<?php }elseif($category_detail[0]->name =="Infographics"){?>
	<header class="article-infograph-story">
		<div class="container">
			<div class="col-md-10 col-md-offset-1">
				<h1 class="page-title">
					<?php echo $category_detail[0]->name; ?>
				</h1>
			</div>
		</div>
	</header>
<?php } ?>


<!-- .page-header -->

<div class="container">
	<div class="col-md-<?php echo $main_column_size; ?> content-area" id="main-column">
		<main id="main" class="site-main" role="main">
			<?php 
				

					get_template_part('content', get_post_format());

					echo "\n\n";
					
					bootstrapBasicPagination();

				} //endwhile;
			?> 
		</main>
	</div>
</div>
<?php //get_sidebar('right'); ?> 
<?php get_footer(); ?> 