<ul class="ventures-external-links horizontal-list">
	<?php foreach(get_ventures_external_links() as $external): extract($external); ?>
	<li class="<?php echo $slug; ?>">
		<a target="_blank" href="<?php echo htmlspecialchars($url); ?>">
			<?php include(dirname(__DIR__)."/img/social/$slug.svg"); ?>
		</a>
	</li>
	<?php endforeach; ?>
</ul>

