<?php
if ( !current_user_can('edit_posts') )
	wp_die(__('You do not have sufficient permissions to import content in this site.'));

$title = __('Ventures Content Normalizer');
?>

<div class="wrap">
	<h2><?php echo esc_html( $title ); ?></h2>
	<p><?php _e('Select which tasks to run:'); ?></p>
	<form action="<?php echo plugins_url('normalize-form-handler.php', __FILE__); ?>" method="POST">
		<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce('ventures-content-normalizer'); ?>">
		<input type="hidden" name="_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<fieldset>
			<h3><legend><?php _e('Posts'); ?></legend></h3>
			<label><input type="checkbox" name="tasks[]" value="remove_empty_paragraphs"> Remove empty paragraphs, which ruin the uniform vertical space.</label><br>
			<label><input type="checkbox" name="tasks[]" value="remove_ventures_start"> Remove &ldquo;VENTURES AFRICA - &rdquo; from the beginning of posts.</label><br>
			<label><input type="checkbox" name="tasks[]" value="remove_author_start"> Remove &ldquo;By {Author}&rdquo; from the beginning of posts.</label><br>
			<label><input type="checkbox" name="tasks[]" value="create_short_urls"> Create short URLs.</label><br>
			<label><input type="checkbox" name="tasks[]" value="opinions_to_ideas" disabled> Change the post type of all opinion pieces to &ldquo;Ideas&rdquo;.</label><br>
		</fieldset>
		<fieldset>
			<h3><legend><?php _e('Users'); ?></legend></h3>
			<label><input type="checkbox" name="tasks[]" value="recover_user_passwords" disabled> Set user passwords to the values found in the old site.</label><br>
		</fieldset>
		<fieldset>
			<h3><legend><?php _e('Categories'); ?></legend></h3>
			<label><input type="checkbox" name="tasks[]" value="fix_category_hierarchy"> Consolidate categories into four root categories.</label><br>
		</fieldset>
		<fieldset>
			<h3><legend><?php _e('Analytics'); ?></legend></h3>
			<label><input type="checkbox" name="tasks[]" value="convert_popularity_data" disabled> Update the posts popularity data to the format used by the new plugin.</label><br>
		</fieldset>
		<fieldset>
			<h3><legend><?php _e('Comments'); ?></legend></h3>
			<label><input type="checkbox" name="tasks[]" value="remove_comments" disabled> Remove all comments, which are not used by the new theme.</label><br>
		</fieldset>
		<br><br>
		<br><br>
		<label><input type="checkbox" name="dry-run" value="true"> Dry run.</label>
		<?php submit_button('Begin Normalization'); ?>
	</form>
</div>
