<?php

$items = get_field('briefing_items');

?><article id="post-<?php the_ID(); ?>" <?php post_class('post '.(ventures_has_image_credit() ? 'has' : 'no').'-credit'); ?>>
	<div class="entry-content">
		<?php echo ventures_featured_image_credit(); ?>
		<div class="post-content">
		<?php if (empty($items)): ?>
			<p>This briefing is empty.</p>
		<?php else: ?>
			<section id="brief-stories" class="column">
				<h2><?php echo ventures_teaser_title('News In ', 'Brief'); ?><span class="title-headline"><?php the_date(); ?></span></h2>
				<ul class="teaser-list clean-list">
				<?php foreach($items as $item): if (empty($item['content'])) continue; ?>
					<li>
						<?php echo $item['content']; ?>
					</li>
				<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>
		</div>
	</div><!-- .entry-content -->
	<?php ventures_showad_listbanner(); ?>
</article><!-- #post-## -->
<div id="infinite-scroll-target" class="clearfix"></div>
