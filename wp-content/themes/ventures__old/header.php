<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package ventures
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta name="msvalidate.01" content="44404DF25670AAC12AB9EA13575CFF95" />
<meta name="google-site-verification" content="wsXJO82JlEx4olJndERqX8q3pxzao_HqsS5Q2fvUTAQ" />
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="google-site-verification" content="wsXJO82JlEx4olJndERqX8q3pxzao_HqsS5Q2fvUTAQ" />
	<?php if (strpos($_SERVER['HTTP_HOST'], 'ventures-africa')!==false): ?>
		<meta name="google-site-verification" content="AGnlz750YbO_nyzHvbHSUUwmpjuCAllTQJoMBY3Ngq8" />
	<?php else: ?>
		<meta name="google-site-verification" content="Kk4eReTn4MdLQrW5IVoEnW3ug_kGS8epmE-NEAgN-3g" />
	<?php endif; ?>
<meta property="fb:pages" content="220865698007399" />
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
	<script>var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';</script>
<!-- Start Alexa Certify Javascript -->
<script type="text/javascript">
_atrk_opts = { atrk_acct:"436og1awAe00EL", domain:"venturesafrica.com",dynamic: true};
(function() { var as = document.createElement('script'); as.type = 'text/javascript'; as.async = true; as.src = "https://d31qbv1cthcecs.cloudfront.net/atrk.js"; var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(as, s); })();
</script>
<noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=436og1awAe00EL" style="display:none" height="1" width="1" alt="" /></noscript>
<!-- End Alexa Certify Javascript -->  
</head>
<!-- <script src="//cdn.mobiopush.com/mobiojs/c6dae0fd065e61af54ce33aaa5ee6c85" type="text/javascript" id="_mobio_js"></script> -->
<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
	<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'ventures' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		
		<section id="top-bar" class="uppercase">
			<h1 class="site-title left"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">Ventures</a></h1>
			
			<nav id="site-navigation" class="main-navigation horizontal-menu middle" role="navigation">
				<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
			</nav><!-- #site-navigation -->
			
			<section class="right">
				<div class="search-box">
					<button id="search-form-toggle" class="search-toggle clean-button overlay-button no-text" aria-controls="search-overlay" aria-expanded="false" data-overlay="search">Search</button>
					<div id="search-overlay" class="fixed-overlay">
						<?php get_search_form(); ?>
						<div id="search-results"></div>
					</div>
				</div>
				<nav id="extra-navigation" class="main-navigation burger-menu" role="navigation">
					<button id="main-menu-toggle" class="menu-toggle burger clean-button overlay-button no-text" aria-controls="burger-overlay" aria-expanded="false" data-overlay="burger"><span><?php _e( 'Primary Menu', 'ventures' ); ?></span></button>
					<div id="burger-overlay" class="fixed-overlay">
						<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">Ventures</a></h1>
						<div class="menu-main-menu-container menu-container left">
							<?php wp_nav_menu( array( 'theme_location' => 'primary', 'container' => false ) ); ?>
						</div>
						<div class="menu-secondary-menu-container menu-container right">
							<?php wp_nav_menu( array( 'theme_location' => 'burger', 'container' => 'false' ) ); ?>
							<div id="burger-external-links" class="center">
								<?php include(__DIR__.'/inc/external-links.php'); ?>
							</div>
						</div>
						<div id="burger-mailing-list" class="mailing-list-form-container center"><?php include(__DIR__.'/inc/mailing-list-form.php'); ?></div>
					</div>
				</nav><!-- #site-navigation -->
			</section>
		</section><!-- .site-branding -->

	</header><!-- #masthead -->

	<div id="content" class="site-content">
