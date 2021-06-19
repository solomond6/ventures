<?php
/**
 * The theme header
 * 
 * @package bootstrap-basic
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]>  <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>     <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>     <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?php wp_title('|', true, 'right'); ?></title>
		<meta name="viewport" content="width=device-width">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
		<link rel="apple-touch-icon" sizes="57x57" href="<?php bloginfo('template_url'); ?>/img/favicon/apple-touch-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="<?php bloginfo('template_url'); ?>/img/favicon/apple-touch-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="<?php bloginfo('template_url'); ?>/img/favicon/apple-touch-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="<?php bloginfo('template_url'); ?>/img/favicon/apple-touch-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="<?php bloginfo('template_url'); ?>/img/favicon/apple-touch-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="<?php bloginfo('template_url'); ?>/img/favicon/apple-touch-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="<?php bloginfo('template_url'); ?>/img/favicon/apple-touch-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="<?php bloginfo('template_url'); ?>/img/favicon/apple-touch-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="<?php bloginfo('template_url'); ?>/img/favicon/apple-touch-icon-180x180.png">
		<link rel="icon" type="image/png" href="<?php bloginfo('template_url'); ?>/img/favicon/favicon-32x32.png" sizes="32x32">
		<link rel="icon" type="image/png" href="<?php bloginfo('template_url'); ?>/img/favicon/favicon-194x194.png" sizes="194x194">
		<link rel="icon" type="image/png" href="<?php bloginfo('template_url'); ?>/img/favicon/android-chrome-192x192.png" sizes="192x192">
		<link rel="icon" type="image/png" href="<?php bloginfo('template_url'); ?>/img/favicon/favicon-96x96.png" sizes="96x96">
		<link rel="icon" type="image/png" href="<?php bloginfo('template_url'); ?>/img/favicon/favicon-16x16.png" sizes="16x16">
		<link rel="shortcut icon" href="<?php bloginfo('template_url'); ?>/img/favicon/favicon.ico">
		
		<!--wordpress head-->
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-38400527-1', 'auto');
		  ga('send', 'pageview');
		</script>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-KSMGRC" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KSMGRC');</script>
		<!--[if lt IE 8]>
			<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
		<![endif]-->
		<!--.navbar-collapse-->
<div class="main-menu1">
	<div class="main-menu3">
		<a href="#" id="main-menu3">
			<i class="fa fa-arrow-circle-right"></i>
		</a>
	</div>

    <?php 
    	//$cats=  get_categories();
    	//var_dump($cats);exit;
        $the_query = new WP_Query("cat=2,3,4,8");
        if($the_query->have_posts()){
            echo '<ol type="i" id="menu-top-menu" class="main-menu">';
            // echo '<br/>';
            echo '<li><a href="'.get_home_url().'">Home</a></li>';
            while($the_query->have_posts()){
                $the_query->the_post();
                $current_id = get_the_ID();
                $category_detail=get_the_category($current_id);
				$term_id = $category_detail[0]->term_id;
                //echo '<li><a href="'.get_category_link(3).'#'.$post->post_name.'">' .get_the_title(). '</a></li>';
                //$url = the_permalink();
                if($term_id == 8){
                	echo "<li><a href='".get_category_link(10)."#".$post->post_name."'><h5><small>".get_secondary_title().":</small> ".get_the_title(). "</h5></a></li>";
                }else{
                	echo "<li><a href='".esc_url(get_permalink(get_page_by_title(get_the_title())))."'><h5><small>".get_secondary_title().":</small> ".get_the_title(). "</h5></a></li>";
                }
            }
            //echo '<li><a href="'.get_home_url().'#interview">Interview</a></li>';
            echo '<li><a href="'.get_home_url().'#videos">Videos</a></li>';
            echo '<li><a href="'.get_home_url().'#more-stories">More stories</a></li>';
            //echo '<li><a href="'.get_home_url().'#infographics">Infographics</a></li>';
            echo '</ol>';
        }else{
            // no posts found
        }
    ?> 
	<?php //dynamic_sidebar('navbar-right'); ?> 
</div>
</nav>
		<div class="page-container .no-padding">
			<?php do_action('before'); ?> 
			<header role="banner" class="banner">
				<div class="container-fluid  row site-branding">
					<div class="col-md-12 site-title" style="text-align:center;">
						<a href="<?php echo get_home_url(); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><img src="<?php bloginfo('template_url'); ?>/img/bvlogo2.png" alt="Ventures Africa" width="250"></a>
					</div>
					<div class="col-md-3 page-header-top-right">
						<div class="sr-only">
							<a href="#content" title="<?php esc_attr_e('Skip to content', 'bootstrap-basic'); ?>"><?php _e('Skip to content', 'bootstrap-basic'); ?></a>
						</div>
					</div>
				</div><!--.site-branding-->
			</header>
        </div>
		<div id="content" class="row site-content">