<?php
get_header();
$main_column_size = bootstrapBasicGetMainColumnSize();
?>
<?php get_sidebar('left'); ?> 
	<div class="col-md-<?php //echo $main_column_size; ?> content-area" id="main-column" style="overflow:hidden;">
		<main id="main" class="site-main" role="main">
			<div class="category-header">
				<div class="container">
					<div class="">
						<div class="col-md-offset-1">
							<div class="col-md-7">
								<h2 class="headline">10</h2>
								<h3 class="headline">Women Driving Tech In Nigeria</h3>
							</div>
							<div class="col-md-4 theConntent">
								<?php
									$the_query = new WP_Query("cat=4");
				                    if($the_query->have_posts()){
				                        while($the_query->have_posts()){
				                            $the_query->the_post();
				                            $content = get_the_content();
											echo substr($content, 0, 400);
											echo '&nbsp;&nbsp;';
											echo '<a href="'.get_category_link(3).'" class="btn btn-danger">Read More...</a>';
				                        }
				                    }else{
				                    }
				                ?>
							</div>
						</div>
					</div>
				</div>
				<div class="container">
					<div class="">
						<?php
							$the_query = new WP_Query("cat=3&posts_per_page=10");
		                    if($the_query->have_posts()){
		                    	$postCount = 1;
		                        while($the_query->have_posts()){
		                            $the_query->the_post();
									$url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'thumbnail');
									$pad = "";
									if($postCount == 1){
										$pad = "col-md-offset-1";
									}elseif($postCount == 6){
										$pad = "col-md-offset-1";
									}
									echo '<div class="col-xs-12 col-xs-6 col-xs-4 col-md-2 '.$pad.' containerImg"><a href="'.get_category_link(3).'#'.$post->post_name.'" class="thumbnail top-thumbnail"><img src="'. $url .'" class="image img-responsive center-block"/><div class="middle"><div class="text">'.get_the_title().'</div></div></a></div>';

		                        $postCount++;
		                        }

		                    }else{
		                    }
		                ?>
					</div>
				</div>
			</div>
			<section class="inteviews">
				<div class="container">
					<div class="col-md-10 col-md-offset-1">
						<?php
							$the_query = new WP_Query("cat=5&posts_per_page=1");
							echo '<h2 class="interviews-header">'.get_cat_name(5).'</h2>';
		                    if($the_query->have_posts()){
		                        while($the_query->have_posts()){
		                            $the_query->the_post();
		                            $url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'thumbnail');
		                            echo '<div class="col-xs-12 col-md-6"><a href="'.get_permalink().'#'.$post->post_name.'" class="thumbnail"><img src="'. $url .'" /></a></div>';
		                            echo '<div class="col-md-6">';
			                            echo '<h2 class="interviews-title">'.get_the_title().'</h2>';
			                            echo '<h4 class="subtitle">'.get_secondary_title(). '</h4>';
			                            $content = get_the_content();
			                            echo '<div class="theConntent">'.substr($content, 0, 1000). '</div>';
			                            echo '<span class="readmore btn btn-danger"><a href="'.get_permalink().'">Readmore</a></span>';
		                            echo '</div>';
		                        }
		                    }else{
		                    }
		                ?>
		            </div>
		        </div>
			</section>
			
			<section class="videos">
				<div class="container">
					<div class="col-md-10 col-md-offset-1">
						<?php
							$the_query = new WP_Query("cat=7&posts_per_page=1");
							echo '<h2 class="videos-header">'.get_cat_name(7).'</h2>';
		                    if($the_query->have_posts()){
		                        while($the_query->have_posts()){
		                            $the_query->the_post();
		                            $url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'thumbnail');
		                            echo '<div class="col-md-6">';
			                            echo '<h2 class="videos-title">'.get_the_title().'</h2>';
			                            $content = get_the_content();
			                            echo '<div class="theConntent">'.substr($content, 0, 800). '</div>';
			                            echo '<span class="readmore btn btn-danger"><a href="'.get_permalink().'">Readmore</a></span>';
		                            echo '</div>';
		                            echo '<div class="col-xs-12 col-md-6"><a href="'.get_permalink().'#'.$post->post_name.'" class="thumbnail"><img src="'. $url .'" /></a></div>';
		                        }
		                    }else{
		                    }
		                ?>
		            </div>
		        </div>
			</section>
			

			<section class="other-stories">
				<div class="container">
					<div class="col-md-10 col-md-offset-1">
						<?php
							$the_query = new WP_Query("cat=7&posts_per_page=3");
							echo '<h2 class="other-stories-header">Other Stories</h2>';
		                    if($the_query->have_posts()){
		                        while($the_query->have_posts()){
		                            $the_query->the_post();
		                            $url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'thumbnail');
		                            echo '<div class="col-md-4">';
		                            	echo '<a href="'.get_permalink().'#'.$post->post_name.'" class="thumbnail"><img src="'. $url .'" /></a>';
			                            echo '<h2 class="other-stories-title">'.get_the_title().'</h2>';
			                            $content = get_the_content();
			                            echo '<div class="theConntent">'.substr($content, 0, 300). '</div>';
			                            echo '<span class="readmore btn btn-danger"><a href="'.get_permalink().'">Readmore</a></span>';
		                            echo '</div>';
		                            
		                        }
		                    }else{
		                    }
		                ?>
		            </div>
		        </div>
			</section>

			<section class="infographics">
				<div class="container">
					<div class="col-md-10 col-md-offset-1">
						<?php
							$the_query = new WP_Query("cat=10&posts_per_page=1");
							echo '<h2 class="infographics-header">'.get_cat_name(10).'</h2>';
		                    if($the_query->have_posts()){
		                        while($the_query->have_posts()){
		                            $the_query->the_post();
		                            $url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'large');
		                            echo '<h2 class="infographics-title">'.get_the_title().'</h2>';
		                            echo '<a href="'.get_permalink().'#'.$post->post_name.'" class="thumbnail"><img src="'. $url .'" /></a>';
		                        }
		                    }else{
		                    }
		                ?>
		            </div>
		        </div>
			</section>

			<section class="videos">
				<div class="container">
					<div class="col-md-10 col-md-offset-1">
						<?php
							$the_query = new WP_Query("cat=10&posts_per_page=1");
							echo '<h2 class="videos-header">'.get_cat_name(4).'</h2>';
		                    if($the_query->have_posts()){
		                        while($the_query->have_posts()){
		                            $the_query->the_post();
		                            $url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'thumbnail');
		                            echo '<div class="col-md-6">';
			                            echo '<h2 class="videos-title">'.get_the_title().'</h2>';
			                            $content = get_the_content();
			                            echo '<div class="theConntent">'.substr($content, 0, 800). '</div>';
			                            echo '<span class="readmore btn btn-danger"><a href="'.get_permalink().'">Readmore</a></span>';
		                            echo '</div>';
		                            echo '<div class="col-xs-12 col-md-6"><a href="'.get_permalink().'#'.$post->post_name.'" class="thumbnail"><img src="'. $url .'" /></a></div>';
		                        }
		                    }else{
		                    }
		                ?>
		            </div>
		        </div>
			</section>
				<!-- <div class="container"><div class="col-md-12"><div class="contentFooter">Illustrations: <a href="http://www.instagram.com/knuckleheroes" target="_blank">Dimeji Ezekiel</a>, Staff Writer: Cynthia Okoroafor, Digital Design: Temidayo Uji</div></div></div> -->
			<!-- </div> -->
		</main>
	</div>
	<!-- <div class="container footer-top">
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
	</div> -->
<?php get_sidebar('right'); ?> 
<?php get_footer(); ?>