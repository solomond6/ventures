<?php

define('MEDIA_CREDIT_POSTMETA_KEY', '_media_credit');
define('MEDIA_CREDIT_URL_POSTMETA_KEY', '_media_credit_url');

function get_media_credit($post) {
	return get_post_meta( $post->ID, MEDIA_CREDIT_POSTMETA_KEY, true );
}
function get_media_credit_url($post) {
	return get_post_meta( $post->ID, MEDIA_CREDIT_URL_POSTMETA_KEY, true );
}

function add_media_credit($fields, $post) {
	$fields['media-credit'] = array(
		'label'         => __('Credit:'),
		'input'         => 'html',
		'show_in_edit'  => true,
		'show_in_modal' => true,
		'html'          => "<input id='attachments[$post->ID][media-credit]' class='media-credit-input' size='30' value='".get_media_credit($post)."' name='attachments[$post->ID][media-credit]' />",
	);
	$fields['media-credit-url'] = array(
		'label'         => __('Credit Link:'),
		'input'         => 'html',
		'show_in_edit'  => true,
		'show_in_modal' => true,
		'html'          => "<input id='attachments[$post->ID][media-credit-url]' class='media-credit-input' size='30' value='".get_media_credit_url($post)."' name='attachments[$post->ID][media-credit-url]' />",
	);
	return $fields;
}
add_filter('attachment_fields_to_edit', 'add_media_credit', 10, 2);

function save_media_credit($post, $attachment) {
	$credit = $attachment['media-credit'];
	if (empty($credit)) $credit = '';
	update_post_meta($post['ID'], MEDIA_CREDIT_POSTMETA_KEY, $credit);
	$credit_url = $attachment['media-credit-url'];
	if (empty($credit_url)) $credit_url = '';
	update_post_meta($post['ID'], MEDIA_CREDIT_URL_POSTMETA_KEY, $credit_url);
	return $post;
}
add_filter('attachment_fields_to_save', 'save_media_credit', 10, 2);