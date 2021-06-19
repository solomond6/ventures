<ul id="share-links" class="share-buttons-list horizontal-list mobile-bar">
	<?php foreach (get_post_sharing_links() as $link): extract($link); ?>
	<li class="<?php echo $slug; ?>">
		<?php if (!empty($url)): ?>
		<a <?php echo $target_attr; ?> href="<?php echo $url; ?>" title="Share on <?php echo $title; ?>">
			<?php echo file_get_contents(get_template_directory()."/img/social/$slug.svg"); ?>
		</a>
		<?php else: ?>
		<button class="clean-button" title="Share on <?php echo $title; ?>"><?php echo file_get_contents(get_template_directory()."/img/social/$slug.svg"); ?></button>
		<?php endif; ?>
	</li>
	<?php endforeach; ?>
</ul>