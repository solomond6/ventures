;
window.animate = function(easeFn, initialValue, valueChange, duration, onTick, onEnd) {
	var initialTime = Date.now(),
		stop = false;
	var stopFn = function() { stop = true; };
	var tick = function() {
		var elapsed = Date.now() - initialTime;
		if (!stop && elapsed < duration) {
			onTick(easeFn(elapsed, initialValue, valueChange, duration));
			requestAnimationFrame(tick);
		}
		else {
			if (!stop) {
				onTick(initialValue + valueChange);
				onEnd && onEnd();
			}
		}
	};
	requestAnimationFrame(tick);
	return stopFn;
};

/**
* Easing functions.
*
* @param t Time elapsed since the animation began.
* @param b Original value.
* @param c Value change.
* @param d Duration.
*/
window.Easing = {
	linear: function(t, b, c, d) {
		return b + (t / d) * c;
	},
	quadInOut: function(t, b, c, d) {
		return t / d < 0.5 ? c / 2 * Math.pow(t / (d / 2), 2) + b : -c / 2 * (((t / (d / 2)) - 1) * (((t / (d / 2)) - 1) - 2) - 1) + b; // easeInOut
	},
	quartOut: function (t, b, c, d) {
		return -c * ((t=t/d-1)*t*t*t - 1) + b;
	},
	quadOut: function (t, b, c, d) {
		return -c *(t/=d)*(t-2) + b;
	},
	additiveBounce: function(fn, t, b, c, d, splitIn) {
		if (splitIn === undefined) {
			splitIn = 0.5;
		}
		var splitOut = 1 - splitIn;
		return (t < d * splitIn) ? fn(t, b, c * splitOut, d * splitIn) : fn(t - (d * splitIn), b, c * splitIn * -1, d * splitOut);
	},
	createBounce: function(fn, splitIn) {
		if (splitIn === undefined) {
			splitIn = 0.5;
		}
		var splitOut = 1 - splitIn;
		return function(t, b, c, d) {
			return (t < d * splitIn) ? fn(t, b, c, d * splitIn) : fn(t - (d * splitIn), b + c, c * -1, d * splitOut);
		};
	},
	bounce: function(fn, t, b, c, d, splitIn) {
		if (splitIn === undefined) {
			splitIn = 0.5;
		}
		var splitOut = 1 - splitIn;
		return (t < d * splitIn) ? fn(t, b, c, d * splitIn) : fn(t - (d * splitIn), b + c, c * -1, d * splitOut);
	}
};

