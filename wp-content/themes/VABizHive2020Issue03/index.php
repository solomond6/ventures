<?php
get_header();
$main_column_size = bootstrapBasicGetMainColumnSize();
?>
<?php get_sidebar('left'); ?> 
	<div class="col-md-<?php //echo $main_column_size; ?> content-area" id="main-column" style="overflow:hidden;">
		<main id="main" class="site-main" role="main">
			<div class="category-header">
				<img src="<?php bloginfo('template_url'); ?>/img/TheVACover.jpg" alt="Ventures Africa" width="100%">
				<!-- <div class="container mobileContainer">
					<div class="">
						<div class="col-md-12"> -->
							<!-- <div class="col-md-4 text-center">
								<h3 class="headline">The future Of</h3>
								<h2 class="headlineTwo">Nigeria's Tech Landscape</h2>
								<h3 class="headline">Through </h3>
								<h3 class="headline">The Eyes Of </h3>
								<h2 class="headlineTwo">10 Leading Women</h3>
							</div> -->
							<!-- <div class="col-md-8">
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
											echo '<div class="col-xs-10 '.$pad.' containerImg"><a href="#'.$post->post_name.'" class="thumbnail top-thumbnail"><img src="'. $url .'" class="image img-responsive center-block"/><div class="middle"><div class="text">'.get_the_title().'</div></div></a></div>';

				                        $postCount++;
				                        }

				                    }else{
				                    }
				                ?>
							</div> -->
							<!-- <div class="col-md-4 theConntent">
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
							</div> -->
						<!-- </div>
					</div>
				</div> -->
				<!-- <div class="container">
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
									echo '<div class="col-xs-12 '.$pad.' containerImg"><a href="'.get_category_link(3).'#'.$post->post_name.'" class="thumbnail top-thumbnail"><img src="'. $url .'" class="image img-responsive center-block"/><div class="middle"><div class="text">'.get_the_title().'</div></div></a></div>';

		                        $postCount++;
		                        }

		                    }else{
		                    }
		                ?>
					</div>
				</div> -->
			</div>

			<div class="container mobileContainer">
				
				<div class="col-md-10 col-md-8 col-md-offset-2 introduction">
					<div class="catpost">
						<?php
							$the_query = new WP_Query("cat=4");
			                if($the_query->have_posts()){
			                    while($the_query->have_posts()){
			                        $the_query->the_post();
			                        $content = get_the_content();
			                        echo '<h2 class="postTitleIntro">' .get_the_title(). '</h2>';
			                        echo '<h4> By '. get_the_author_meta('first_name').' '.get_the_author_meta('last_name').'</h4>';
									echo '<div class="theConntent">'.the_content(). '</div>';
			                    }
			                }else{
			                }
			            ?>
		            </div>

	            </div>
	            <div class="introductionImg">
	            	<?php
						$the_query = new WP_Query("cat=5&posts_per_page=1");
	                    if($the_query->have_posts()){
	                    	$postCount = 1;
	                        while($the_query->have_posts()){
	                            $the_query->the_post();
								$url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'thumbnail');
								// kdmfi_the_featured_image( 'featured-image-2', 'full' );
								$url_animated = kdmfi_get_featured_image_src( 'featured-image-2', 'full' );
								$pad = "";
								if($postCount == 1){
									$pad = "col-md-offset-1";
								}elseif($postCount == 6){
									$pad = "col-md-offset-1";
								}
								echo '<div class="col-xs-10 '.$pad.' containerImg"><a href="'.get_permalink().'" class="thumbnail top-thumbnail"><img src="'. $url_animated .'" alt="'.$post->post_name.'" class="image img-responsive center-block"/><div class="middle"><div class="text">'.get_the_title().'</div></div></a></div>';

	                        $postCount++;
	                        }

	                    }else{
	                    }
	                ?>
	            </div>
	            <div class="clearfix"></div>
				</div>
			</div>
			<section class="inteviews" id="interview" name="interview">
				<div class="container mobileContainer">
					<div class="col-md-10 col-md-offset-1">

						<?php
							$the_query = new WP_Query("cat=3&posts_per_page=2");
							echo '<h2 class="interviews-header">'.get_cat_name(5).'</h2>';
		                    if($the_query->have_posts()){
		                        while($the_query->have_posts()){
		                            $the_query->the_post();
		                            $url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'thumbnail');
		                            echo '<div class="col-xs-12 col-md-6"><a href="'.get_permalink().'#'.$post->post_name.'" class="thumbnail"><img class="interview-img" src="'. $url .'" alt="'.$post->post_name.'"/></a>';
		                            echo '<a href="'.get_permalink().'#'.$post->post_name.'"><h2 class="interviews-title" style="font-size:1.2em; font-weight:600;">'.get_the_title().'</h2></a></div>';
		                            // echo '<div class="col-md-6">';
			                           //  echo '<h2 class="interviews-title" style="font-size:1.4em; font-weight:600;">'.get_the_title().'<span style="font-size:0.8em; font-weight:600;">'.get_secondary_title().'</span></h2>';
			                           //  //echo '<h4 class="subtitle">'.get_secondary_title(). '</h4>';
			                           //  $content = get_the_content();
			                           //  echo '<div class="theConntent">'.substr($content, 0, 468). '</div>';
			                           //  echo '<span class="readmore btn btn-danger"><a href="'.get_permalink().'">Readmore</a></span>';
		                            // echo '</div>';
		                        }
		                    }else{
		                    }
		                ?>
		            </div>
		        </div>
			</section>
			
			<section class="videos" id="videos" name="videos">
				<div class="container mobileContainer">
					<div class="col-md-10 col-md-offset-1">
					<h2 class="videos-header">Videos</h2>
						<h3>Food Safety Issues</h3>
						<iframe width="100%" height="420" src="https://www.youtube.com/embed/52wnxbaxlYI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
						<h3>Food Security</h3>
						<iframe width="100%" height="420" src="https://www.youtube.com/embed/UpYSVp6OUaE" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
		            </div>
		        </div>
			</section>
			
			<section class="infographics" id="infographics" name="infographics">
				<div class="container mobileContainer">
					<div class="col-md-10 col-md-offset-1">
						<?php 
							echo '<h2 class="interviews-header">'.get_cat_name(8).'</h2>';
						    echo do_shortcode("[metaslider id=24]"); 
						?>
		            </div>
		        </div>
			</section>

			<section class="other-stories" id="more-stories" name="more-stories">
				<div class="container mobileContainer">
					<div class="col-md-10 col-md-offset-1">
					<h2 class="other-stories-header">More in BizHive</h2>
						
						<div class="col-md-3 media block-update-card">
							<div class="col-md-12 no-padding">
								<a  href="http://venturesafrica.com/this-nigerian-school-is-building-an-intersection-between-education-and-sustainability-by-accepting-plastic-bottles-as-school-fees/" target="_blank">
							    <img src="<?php bloginfo('template_url'); ?>/img/school-recycles-plastic-bottles-in-Nigeria-1536x863.jpg" alt="..." style="width: 100%;height:150px;">
								</a>
							</div>
							<div class="col-md-12">
							    <h4 class="media-heading"><a href="http://venturesafrica.com/this-nigerian-school-is-building-an-intersection-between-education-and-sustainability-by-accepting-plastic-bottles-as-school-fees/" target="_blank">This Nigerian School Is Building An Intersection Between Education And Sustainability By Accepting Plastic Bottles As School Fees</a></h4>
							    <a href="http://venturesafrica.com/this-nigerian-school-is-building-an-intersection-between-education-and-sustainability-by-accepting-plastic-bottles-as-school-fees/" target="_blank" class="btn btn-default">Read More</a>
							</div>
						</div>

						<div class="col-md-3 media block-update-card">
							<div class="col-md-12 no-padding">
								<a  href="http://venturesafrica.com/this-initiative-in-senegal-empowers-women-and-promotes-sustainability-by-teaching-them-to-knit-with-plastic-bags/" target="_blank">
							    <img src="<?php bloginfo('template_url'); ?>/img/plastic-waste-bags-senegal-nigeria-recycle-1536x1124.jpg" alt="..." style="width: 100%;height:150px;">
								</a>
							</div>
							<div class="col-md-12">
							    <h4 class="media-heading"><a href="http://venturesafrica.com/this-initiative-in-senegal-empowers-women-and-promotes-sustainability-by-teaching-them-to-knit-with-plastic-bags/" target="_blank">This Initiative In Senegal Empowers Women And Promotes Sustainability By Teaching Them To Knit With Plastic Bags</a></h4>
							    <a href="http://venturesafrica.com/this-initiative-in-senegal-empowers-women-and-promotes-sustainability-by-teaching-them-to-knit-with-plastic-bags/" class="btn btn-default" target="_blank">Read More</a>
							</div>
						</div>

						<div class="col-md-3 media block-update-card">
							<div class="col-md-12 no-padding">
								<a  href="http://venturesafrica.com/redisa-gets-traction-with-tyre-recycling/" target="_blank">
							    <img src="<?php bloginfo('template_url'); ?>/img/312_1002_reclaimed_again.jpg" alt="..." style="width: 100%;height:150px;">
								</a>
							</div>
							<div class="col-md-12">
							    <h4 class="media-heading"><a href="http://venturesafrica.com/redisa-gets-traction-with-tyre-recycling/" target="_blank">Redisa Gets Traction With Tyre Recycling</a></h4>
							    <a href="http://venturesafrica.com/redisa-gets-traction-with-tyre-recycling/" class="btn btn-default" target="_blank">Read More</a>
							</div>
						</div>

						<div class="col-md-3 media block-update-card">
							<div class="col-md-12 no-padding">
								<a  href="http://venturesafrica.com/coca-cola-unilever-nestle-and-diageo-launch-africa-plastics-recycling-alliance/" target="_blank">
							    <img src="<?php bloginfo('template_url'); ?>/img/APRA-1.0-1-1536x878.jpg" alt="..." style="width: 100%;height:150px;">
								</a>
							</div>
							<div class="col-md-12">
							    <h4 class="media-heading"><a href="http://venturesafrica.com/coca-cola-unilever-nestle-and-diageo-launch-africa-plastics-recycling-alliance/" target="_blank">Coca-cola, Unilever, Nestle And Diageo Launch Africa Plastics Recycling Alliance</a></h4>
							    <a  href="http://venturesafrica.com/coca-cola-unilever-nestle-and-diageo-launch-africa-plastics-recycling-alliance/" class="btn btn-default" target="_blank">Read More</a>
							</div>
						</div>


		            </div>
		        </div>
			</section>

			

			<!-- <section class="videos">
				<div class="container mobileContainer">
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
			</section> -->
				
			<!-- </div> -->
		</main>
	</div>
	<!-- <div class="container mobileContainer"><div class="col-md-10 col-md-offset-1 text-center"><div class="contentFooter"><strong>Illustrations:</strong> Osoneye Adewale, <strong>Content Developers:</strong> Hadassah Egbedi, <strong>Editor:</strong> Felicia .O. Ochelle<br/><strong>Digital Design:</strong> Temidayo Uji, <strong>Business Unit:</strong> Kaaranja Daniel (+234 909 328 4213)</div></div></div>
 -->
	<img src="<?php bloginfo('template_url'); ?>/img/VABizHiveFooter.jpg" alt="Ventures Africa" width="100%" height="100%">
	<div class="container-fluid text-center"><div class="col-md-12"><div class="contentFooter"> <strong>Content Developer:</strong> Hadassah Egbedi <strong>Editor:</strong> Felicia .O. Ochelle <strong>Business Unit:</strong> Kaaranja Daniel (+234 909 328 4213)<br/><strong>Digital Design:</strong> Temidayo Uji <strong>Illustrations:</strong> Osoneye Adewale <strong>Team Lead:</strong> Edore Nakpodia</div></div></div>
<?php get_sidebar('right'); ?> 
<?php get_footer(); ?>