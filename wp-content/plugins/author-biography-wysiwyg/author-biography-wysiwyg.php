<?php
/**
 * Plugin Name: Author biography wysiwyg
 * Plugin URI: http://versionindustries.com
 * Description: Turns the author biography text box into a tinymce wysiwyg.
 * Version: 1.0.0
 * Author: Carlos Padilla
 * Author URI: http://github.com/elpadi
 * License: MIT
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('admin_enqueue_scripts', function() {
	if (get_current_screen()->id === 'profile') {
		add_action('admin_footer', function() {
			wp_editor('', 'description');
		});
	}
});

/**
 * Remove the textarea wp_editor creates.
 */
add_action('admin_head-profile.php', function() {
	echo "<style>.wp-editor-wrap { display:none; };</style>";
});
