var Scroll = (function() {
	var scrollElement = window,
		fns = [],
		prevScroll = 0;
	var Scroll = {
		set: function(val) {
			Scroll._scroll = {
				prev: prevScroll,
				current: val,
				change: val - prevScroll
			};
			prevScroll = val;
		},
		add: function(fn) {
			fns.push(fn);
		}
	};
	var onScroll = function(e) {
		var scroll = scrollElement === window
			? document.documentElement.scrollTop || document.body.scrollTop || window.scrollY
			: scrollElement.scrollTop || scrollElement.scrollY;
		Scroll.set(Math.max(0, scroll));
		if (Scroll._scroll.change !== 0) {
			for (var i = 0, l = fns.length; i < l; i++) fns[i].call(this, e, Scroll._scroll);
		}
	};
	if ('addEventListener' in scrollElement) scrollElement.addEventListener('scroll', onScroll);
	else if ('attachEvent' in scrollElement) scrollElement.attachEvent('onscroll', onScroll);
	else scrollElement.onscroll = onScroll;
	return Scroll;
})();
