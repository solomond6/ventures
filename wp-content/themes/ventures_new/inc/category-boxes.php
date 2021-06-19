<section id="category-boxes">
	<h2 class="phone-only">Elsewhere on Ventures</h2>
	<?php $category_posts = ventures_category_box_posts($_POSTS['excludes']); foreach ($category_posts as $cat_posts): extract($cat_posts); ?>
	<article class="fitted-container cover">
		<img class="fitted" src="<?php echo $cat->img['src']; ?>" srcset="<?php echo $cat->img['srcset']; ?>" sizes="(max-width: 759px) 100vw, 50vw" alt="" data-original-width="<?php echo $cat->img_meta_value['width']; ?>">
		<?php ventures_resp_img(0, $cat->img_meta_value); ?>
		<h2><a class="normal-button inverted-colors" href="<?php echo $cat->url; ?>"><?php echo $cat->name; ?></a></h2>
		<ul>
			<?php foreach ($posts as $p): ?>
			<li><a href="<?php echo get_permalink($p); ?>"><?php echo get_the_title($p); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</article>
	<?php endforeach; ?>
</section>
