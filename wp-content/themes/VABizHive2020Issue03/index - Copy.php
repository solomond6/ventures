<?php
get_header();
$main_column_size = bootstrapBasicGetMainColumnSize();
?>
<?php get_sidebar('left'); ?> 
	<div class="col-md-<?php //echo $main_column_size; ?> content-area" id="main-column" style="overflow:hidden;">
		<main id="main" class="site-main" role="main">
			<div class="category-header">
				<div class="container">
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class="col-md-7">
								<h2 class="headline">10</h2>
								<h3 class="headline">WOMEN</h3>
								<h3 class="headline2">DRIVING TECH</h3>
								<h3 class="headline3">IN NIGERIA</h3>
							</div>
							<div class="col-md-5">
								<?php
									$the_query = new WP_Query("cat=4");
				                    if($the_query->have_posts()){
				                        while($the_query->have_posts()){
				                            $the_query->the_post();
				                            $content = get_the_content();
											echo substr($content, 0, 430);
				                           	//echo the_content();
				                        }
				                    }else{
				                    }
				                ?>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<?php
								$the_query = new WP_Query("cat=3&posts_per_page=10");
			                    if($the_query->have_posts()){
			                    	$postCount = 1;
			                        while($the_query->have_posts()){
			                            $the_query->the_post();
										$url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'thumbnail' );
										if($postCount == 1){
											echo '<div class="col-xs-3 col-md-2 col-md-offset-1"><a href="'.get_permalink().'#'.$post->post_name.'" class="thumbnail"><img src="'. $url .'" /></a></div>';
										}else{
											echo '<div class="col-xs-3 col-md-2"><a href="'.get_permalink().'#'.$post->post_name.'" class="thumbnail"><img src="'. $url .'" /></a></div>';
										}
			                        }
			                    }else{
			                    }
			                ?>
						</div>
					</div>
				</div>
			</div>
				
				<!-- <div class="container"><div class="col-md-12"><div class="contentFooter">Illustrations: <a href="http://www.instagram.com/knuckleheroes" target="_blank">Dimeji Ezekiel</a>, Staff Writer: Cynthia Okoroafor, Digital Design: Temidayo Uji</div></div></div> -->
			<!-- </div> -->
		</main>
	</div>
	<div class="container footer-top">
		<div class="col-md-12"><h4>More on Ventures Africa</h4></div>
			<div class="col-md-4 no-padding-left">
				<div class="col-md-12"><a href="http://venturesafrica.com/features/kenyas-new-favourite-customer-iran/"><img src="<?php bloginfo('template_url'); ?>/img/handshake.jpg" alt="" width="100%"></a></div>
				<div class="col-md-12">
					<div class="footer-content"><a href="http://venturesafrica.com/features/kenyas-new-favourite-customer-iran/">Kenya's New Favourite Customer: IRAN</a></div>
				</div>
			</div>
			<div class="col-md-4 no-padding-left">
				<div class="col-md-12"><a href="http://venturesafrica.com/features/36-year-old-woman-nigeria-is-changing-the-face-of-cycling/"><img src="<?php bloginfo('template_url'); ?>/img/cycling.jpg" alt="" width="100%"></a></div>
				<div class="col-md-12">
					<div class="footer-content"><a href="http://venturesafrica.com/features/36-year-old-woman-nigeria-is-changing-the-face-of-cycling/">Glory Road: How One 36-Year-Old Woman In Nigeria Is Changing The of Cycling</a></div>
				</div>
			</div>
			<div class="col-md-4 no-padding-left">
				<div class="col-md-12"><a href="http://venturesafrica.com/features/paint-the-city-oshodi-how-an-ngo-is-bringing-life-and-hope-to-a-community-in-lagos-through-art-and-colour/"><img src="<?php bloginfo('template_url'); ?>/img/market.jpg" alt="" width="100%"></a></div>
				<div class="col-md-12">
					<div class="footer-content"><a href="http://venturesafrica.com/features/paint-the-city-oshodi-how-an-ngo-is-bringing-life-and-hope-to-a-community-in-lagos-through-art-and-colour/">Paint The City – OSHODI: How An NGO Is Bringing Life And Hope To A Lagos Community Through Art</a></div>
				</div>
			</div>			
	</div>
<?php get_sidebar('right'); ?> 
<?php get_footer(); ?>