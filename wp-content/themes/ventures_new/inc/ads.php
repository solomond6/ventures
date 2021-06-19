<?php

/*

This file defines the ad units for the site and implements them.

*/

add_action('admin_init', 'ventures_register_and_build_ad_fields');
function ventures_register_and_build_ad_fields() { 
	add_settings_section(
		'ventures_advertising',
		'Advertising',
		function( $arg ) {
			echo 'Settings related to your AdSense and DoubleClick for Publishers
			accounts for advertising on the site.';
		},
		'general'
	);

	register_setting(
		'general', 
		'ventures_dfp_networkid', 
		function($i) { return filter_var($i, FILTER_SANITIZE_NUMBER_INT); }
	);

	add_settings_field(
		'ventures_dfp-networkid',
		'DFP Network ID',
		function() {
			$value = get_option('ventures_dfp_networkid', '');
        	echo '<input type="text" id="ventures_dfp_networkid" placeholder="e.g. 12345678" name="ventures_dfp_networkid" value="'.htmlspecialchars($value).'" />';
		},
		'general', 
		'ventures_advertising', 
		array('label_for' => 'ventures_dfp_networkid')
	);
}

add_shortcode('banner-ad', function() {
	return '<section class="adunit-wrap mid-article-ad" data-unit=""></section>';
});

add_action('wp_footer', function() {
	$dfp_id = get_option('ventures_dfp_networkid', '');
	if (empty($dfp_id)) return;
	echo '<script>VENTURES.DFP_NETWORK_ID = '.$dfp_id.';</script>';
}, 20);

function ventures_showad_fullwidth() {
	echo '<section class="adunit-wrap" data-unit="Ventures_FullWidth"></section>';
}

function ventures_showad_listbanner() {
	if (ventures_is_mobile()) {
		return ventures_showad_mobile();
	}
	echo '<section class="adunit-wrap" data-unit="Ventures_ListBanner"></section>';
}

function ventures_showad_articleside() {
	echo '<aside class="adunit-wrap" data-unit="Ventures_ArticleSide"></aside>';
}

function ventures_showad_mobile() {
	if (!ventures_is_mobile()) return;
	echo '<aside class="adunit-wrap" data-unit="Ventures_MobileBanner"></aside>';
}
