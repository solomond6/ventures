<?php
/*
Plugin Name: Ventures Normalize Content
Plugin URI: http://versionindustries.com
Description: Prepare the site content for the new theme.
Author: Version Industries
Author URI: http://versionindustries.com
Version: 0.6.1
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

add_action('admin_menu', function() {
	add_submenu_page('tools.php', 'Normalize Content', 'Normalize Content', 'edit_posts', 'ventures-content-filter', 'ventures_filter_form');
});

function ventures_filter_form() {
	include(__DIR__.'/normalize-form.php');
}

