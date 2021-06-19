<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
	<label>
		<span class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ) ?></span>
		<input type="search" class="search-field" autocomplete="off" placeholder="Enter query" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" />
	</label>
	<div class="icons">
		<input type="submit" class="search-submit" value="<?php echo esc_attr_x( 'Search', 'submit button' ) ?>">
		<div class="search-loading"></div>
	</div>
</form>
