<?php
$catClass = "";
if(is_single()){
	$catClass = "";
}else{
	$catClass = "cat-post";
}
?>

<div class="col-md-10 col-md-8 col-md-offset-2 <?php echo $catClass; ?>">
	<?php
			echo '<div class="" id="'.$post->post_name .'" name="'.$post->post_name .'">';
			echo '<div id="'.$post->post_name.'" class="catpost">';
				
				$categories = get_the_category();
				if($categories[0]->name == "Infographics"){
					echo '<h2 class="postTitle">' .get_the_title(). '<span style="font-size:0.8em; font-weight:400;"></span></h2>';
				}elseif($categories[0]->name == "Main Story"){
					echo '<h2 class="postTitle" style="font-size:1.4em; font-weight:600;">' .get_the_title(). '<span style="font-size:0.8em; font-weight:400;"></span></h2>';
					echo '<h3> By '. get_the_author_meta('first_name').' '.get_the_author_meta('last_name').'</h3>';
				}else{
					echo '<div class="col-md-5 mheader">';
						$url = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'thumbnail');
						echo '<img src="'. $url .'" alt="'.$post->post_name.'"/>';
						$post_tags = get_the_tags();
	 					
	 					
						if ( $post_tags ) {
							echo '<div class="tags">';
							$numItems = count($post_tags);
							$i = 0;
							foreach($post_tags as $key=>$tag) {
								echo $tag->name. ', '; 
							  	if(++$i === $numItems) {
							    	echo $tag->name. '. ';
							  	}
							}
						    echo '</div>';
						}
					echo '</div>';
					echo '<h2 class="postTitle">' .get_the_title(). '</h2>';
					echo '<h4 class="subtitle">'.get_secondary_title(). '</h4>';
				}
				
				echo '<div class="theConntent">'.the_content(). '</div>';
			echo '</div></div>';
		?>
		
</div>