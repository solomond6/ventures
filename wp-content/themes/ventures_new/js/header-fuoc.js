(function() {
	if (!('getElementsByClassName' in document.body) || !('classList' in document.body)) return;
	var ts = document.getElementsByClassName('top-story'), tsh;
	if (document.body.classList.contains('single') && ts.length > 0 && ts[0].getElementsByClassName('thumb').length > 0) {
		tsh = Math.round(window.innerHeight - document.getElementById('top-bar').clientHeight);
		if (document.body.classList.contains('admin-bar')) tsh -= 32;
	}
	else {
		tsh = Math.min(window.innerHeight * 0.66, window.innerWidth * 0.5);
	}
	for (var i = 0; i < ts.length; i++) {
		ts[i].style.height = tsh + 'px';
	};
	
	if (!Modernizr.objectFit || !('dataset' in document.body) || ts.length === 0) return;
	var pixelDensity = ('devicePixelRatio' in window) ? window.devicePixelRatio : screen.availWidth / document.documentElement.clientWidth;
	for (var i = 0, imgs = ts[0].getElementsByTagName('img'); i < imgs.length; i++) {
		img.classList.toggle('blur', el.offsetWidth * pixelDensity > parseInt(img.dataset.originalWidth, 10));
	}
})();
