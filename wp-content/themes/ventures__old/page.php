<?php get_header(); the_post(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php ventures_top_story_banner(null, true); ?>
			<?php get_template_part( 'content', 'page' ); ?>
			<?php if (get_field('append_inquiry_form')): ?>
				<form id="contact-form" action="/contact" method="post">
						<h2>Inquiries</h2>
						<label>
							<span>Name: </span>
							<input name="name" type="text" placeholder="Name">
						</label>
						<label>
							<span>Email: </span>
							<input name="email" type="email" placeholder="Email">
						</label>
						<label>
							<span>Phone: </span>
							<input name="phone" type="tel" placeholder="Phone">
						</label>
						<label>
							<span>City: </span>
							<input name="city" type="text" placeholder="City">
						</label>
						<label class="full-width">
							<span>Message: </span>
							<textarea name="message" placeholder="Message"></textarea>
						</label>
						<input id="contact-submit" type="submit" value="SUBMIT"/>
				</form>
			<?php endif; ?>
			<?php // include(__DIR__.'/inc/category-boxes.php'); ?>
		</main><!-- #main -->
	</div><!-- #primary -->


<?php get_footer(); ?>
