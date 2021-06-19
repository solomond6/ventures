<?php

add_filter('manage_posts_columns', function($cols) {
	$cols['new_post_thumb'] = __('Featured');
	return $cols;
}, 5);

add_action('manage_posts_custom_column', function($col, $id) {
	if ($col==='new_post_thumb') {
		if (function_exists('the_post_thumbnail')) {
			the_post_thumbnail('w-320', ['style' => 'width:100%;height:auto;']);
		}
	}
}, 5, 2);

add_action('admin_print_scripts', function() {
	echo '<script>window.addEventListener("load", function() { jQuery(".user-url-wrap label").text("LinkedIn"); });</script>';
});
