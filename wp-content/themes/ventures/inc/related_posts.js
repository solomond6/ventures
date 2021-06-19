(function($) {
	$(document).ready(function() {
		var $button = $('#related_posts_button');
		if ($button.length > 0) {
			var $container = $(document.createElement('div')).insertAfter($button);
		}
		$button.on('click', function(e) {
			e.preventDefault();
			e.stopPropagation();
			$container.html();
			$.ajax({
				url: VENTURES.AJAX_URL,
				method: 'POST',
				data: {
					action: 'related_posts',
					id: $button.prev().val()
				}
			}).done(function(data, textStatus, jqXHR) {
				$container.html(data);
			})
		});
	});
})(jQuery);
