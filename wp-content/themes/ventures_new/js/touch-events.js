function TouchEvents(element, preventDefault) {
	this.element = element;
	this.preventDefault = preventDefault;
	this.element.addEventListener('touchstart', this.ontouchstart.bind(this));
	this.element.addEventListener('touchmove', this.ontouchmove.bind(this));
	this.element.addEventListener('touchend', this.ontouchend.bind(this));
	this.handlers = { firstmove:[] };
	_.each(_.flatten(_.map(this.directions.x.concat(this.directions.y), function(dir) {
		return _.map(this.eventNames, function(s) { return s + dir; });
	}, this)), function(s) { this.handlers[s] = []; }, this);
	this.reset();
}

TouchEvents.prototype = {
	intervalId: 0,
	eventNames: ['singlefinger','swipe'],
	directions: { x:['left','right'], y:['up','down'] },
	before: function(e) {
		if (this.preventDefault === true) {
			e.preventDefault();
		}
	},
	reset: function() {
		this.firstTouch = this.lastTouch = null;
		this.touches = [];
		this.fingerCount = 0;
		clearInterval(this.intervalId);
	},
	trigger: function(name) {
		_.invoke(this.handlers[name], 'apply', this, Array.prototype.slice.call(arguments, 1));
	},
	onSingleFingerDrag: function() {
		var last = this.touches[this.touches.length - 1];
		var beforeLast = this.touches[this.touches.length - 2];
		_.each(this.directions, function(directions, key) {
			var change = last[key] - beforeLast[key];
			if (Math.abs(change) > 10) {
				this.trigger('singlefinger' + directions[change < 0 ? 0 : 1], change);
			}
		}, this);
	},
	checkSwipe: function() {
		var last = this.touches[this.touches.length - 1];
		var change = {
			x: last.x - this.touches[0].x,
			y: last.y - this.touches[0].y
		};
		var changeRatio = Math.abs(change.x / change.y);
		if (changeRatio > 2 && Math.abs(change.x) > 25) {
			this.trigger('swipe' + this.directions.x[change.x < 0 ? 0 : 1], change.x);
		}
		if (changeRatio < 0.5 && Math.abs(change.y) > 25) {
			this.trigger('swipe' + this.directions.y[change.y < 0 ? 0 : 1], change.y);
		}
	},
	ontouchstart: function(e) {
		this.before(e);
		this.reset();
		this.fingerCount = e.touches.length;
	},
	ontouchmove: function(e) {
		this.before(e);
		if (e.touches.length === 1) {
			touch = e.touches[0] || e.changedTouches[0];
			this.touches.push({ x:touch.screenX, y:touch.screenY });
			if (this.touches.length > 1) {
				this.onSingleFingerDrag();
			}
			else {
				this.trigger('firstmove');
			}
		}
	},
	ontouchend: function(e) {
		this.before(e);
		if (this.fingerCount && this.touches.length > 1) {
			this.checkSwipe();
		}
	}
};

TouchEvents.swipe = function(element, leftFn, rightFn, progressFn, firstFn) {
	var te = new TouchEvents(element);
	te.handlers.swipeleft.push(leftFn);
	te.handlers.swiperight.push(rightFn);
	te.handlers.singlefingerleft.push(progressFn);
	te.handlers.singlefingerright.push(progressFn);
	te.handlers.firstmove.push(firstFn);
};
