(function($) {
	$(document).ready(function() {
		var node;
		var $target = $('#link-options').find('.link-target');
		var $new = $target.clone().insertAfter($target);
		var checkbox = $new.find('input').attr('id', 'link-button-checkbox').on('change', function(e) {
			if (node) node.classList.toggle('ventures-button');
		}).get(0);

		$new.find('label').contents().filter(function(i, el) { return el.nodeType === 3; }).get(0).textContent = ' This link is a button';
	
		tinymce.PluginManager.add('venturesTheme', function(editor, url) {
			editor.on('ExecCommand', function(e) {
				if (e.command === 'WP_Link') {
					var selected = editor.selection.getNode();
					if (selected.nodeName === 'A') {
						node = selected;
						checkbox.checked = node.classList.contains('ventures-button');
					}
					else node = null;
				}
			});
		});
	});
})(jQuery);
