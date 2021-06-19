Modernizr.objectFit = 'objectFit' in document.documentElement.style;
Modernizr.canvas = 'getContext' in document.createElement('canvas');
Modernizr.textSelection = ('getSelection' in window);
document.documentElement.className += ' ' + (Modernizr.objectFit ? '' : 'no-') + 'objectfit';
document.documentElement.className += ' ' + (Modernizr.canvas ? '' : 'no-') + 'canvas';

if (VENTURES.isIE = /(msie|trident)/i.test(navigator.userAgent)) document.documentElement.className += ' msie';
if (VENTURES.isHighPerformance = !/(android|blackberry|iphone|ipad|ipod|iemobile)/i.test(navigator.userAgent)) document.documentElement.className += ' high-performance';
VENTURES.pixelDensity = ('devicePixelRatio' in window) ? window.devicePixelRatio : screen.availWidth / document.documentElement.clientWidth;

yepnope({
	test : !('matchMedia' in window) && !('ontouchstart' in document),
	yep  : [VENTURES.THEME_URL + '/css/tablets.css', VENTURES.THEME_URL + '/css/desktop.css'],
	nope : null
});

var isDesktopSafari = /^((?!chrome|mobile|android).)*safari/i.test(navigator.userAgent);
VENTURES.teaserCssTransforms = Modernizr.csstransforms;
document.documentElement.className += ' ' + (VENTURES.teaserCssTransforms ? '' : 'no-') + 'teasertransforms';

VENTURES.applyBlurs = Modernizr.canvas || Modernizr.objectFit;
VENTURES.filterBlurs = Modernizr.objectFit && !isDesktopSafari;
document.documentElement.className += ' ' + (VENTURES.filterBlurs ? '' : 'no-') + 'filterblurs';

Function.prototype.curry = function() {
	if (arguments.length === 0) return this;
	var fn = this, args = Array.prototype.slice.call(arguments, 0);
	return function() {
		return fn.apply(this, args.concat(Array.prototype.slice.call(arguments, 0)));
	};
};

Function.prototype.throttle = function(wait, options) {
	var func = this;
	var context, args, result;
	var timeout = null;
	var previous = 0;
	options || (options = {});
	var later = function() {
		previous = new Date;
		timeout = null;
		result = func.apply(context, args);
	};
	return function() {
		var now = new Date;
		if (!previous && options.leading === false) previous = now;
		var remaining = wait - (now - previous);
		context = this;
		args = arguments;
		if (remaining <= 0) {
			clearTimeout(timeout);
			timeout = null;
			previous = now;
			result = func.apply(context, args);
		} else if (!timeout && options.trailing !== false) {
			timeout = setTimeout(later, remaining);
		}
		return result;
	};
};

Function.prototype.debounce = function(wait, immediate) {
	var func = this;
	var result;
	var timeout = null;
	return function() {
		var context = this, args = arguments;
		var later = function() {
			timeout = null;
			if (!immediate) result = func.apply(context, args);
		};
		var callNow = immediate && !timeout;
		clearTimeout(timeout);
		timeout = setTimeout(later, wait);
		if (callNow) result = func.apply(context, args);
		return result;
	};
};

function indexOf(arr, item) {
	if ('indexOf' in []) return [].indexOf.call(arr, item);
	for (var i = 0; i < arr.length; i++) if (arr[i] === item) return i;
	return -1;
}

function hasClass(element, className) {
	return 'classList' in element ? element.classList.contains(className) : indexOf(element.className.split(' '), className) !== -1;
}

function addClass(element, className) {
	return 'classList' in element ? element.classList.add(className) : (hasClass(element, className) ? null : element.className += ' ' + className);
}

function array_filter(obj, iterator, context) {
	var results = [];
	if (obj == null) return results;
	if (('filter' in obj) && obj.filter === Array.prototype.filter) return obj.filter(iterator, context);
	each(obj, function(value, index, list) {
		if (iterator.call(context, value, index, list)) results.push(value);
	});
	return results;
}

function updateTopStoryBanner(containerNode) {
	if (!('getElementsByClassName' in document.body)) return;
	var ts = (containerNode === undefined ? document : containerNode).getElementsByClassName('top-story');
	if (hasClass(document.body, 'single') && ts.length > 0 && (!hasClass(ts[0], 'type--ventures_interviews') && ts[0].getElementsByClassName('thumb').length > 0)) {
		VENTURES.TOP_BANNER_HEIGHT = Math.round(window.innerHeight - document.getElementById('top-bar').clientHeight);
		if (hasClass(document.body, 'admin-bar')) VENTURES.TOP_BANNER_HEIGHT -= 32;
	}
	else {
		VENTURES.TOP_BANNER_HEIGHT = Math.min(window.innerHeight * 0.66, window.innerWidth * 0.5);
	}
	if (!VENTURES.isHighPerformance) VENTURES.TOP_BANNER_HEIGHT -= document.getElementById('top-bar').clientHeight;
	for (var i = 0; i < ts.length; i++) {
		ts[i].style.height = VENTURES.TOP_BANNER_HEIGHT + 'px';
	};
}

jQuery.fn.toggleBlur = function() {
	if (!VENTURES.applyBlurs) return this;
	return this.each(function(i, el) {
		var $img = jQuery(el);
		var blur = el.offsetWidth > $img.data('original-width');
		if (VENTURES.filterBlurs) {
			$img.toggleClass('blur', blur).parent().toggleClass('blur-container', blur);
		}
		else {
			var $container = $img.closest('.fitted-container').addClass('blur').toggleClass('blur-needed', blur);
			if (blur && $img.siblings('canvas').length === 0) jQuery(createBlurCanvas(el, $container.get(0))).addClass('blur-canvas').insertAfter(el);
			if (!blur) $container.removeClass('blur-added blur-error').find('.blur-canvas').remove();
		}
	});
};

var SHARE_POPUP_DIMENSIONS = { WIDTH: 600, HEIGHT: 257 },
	SELECTION_SHARE_MAX_LENGHT = 50,
	SELECTION_SHARE_BUTTON_OFFSET = 10,
	FULL_WIDTH_MAX_SPACING = 30,
	FULL_WIDTH_MAX_EXTRA_WIDTH = 276,
	ASIDE_ADVERT_HEIGHT = 620;

var TRANSITION_END_EVENT_NAME = (function() {
	var i,
	undefined,
	el = document.createElement('div'),
	transitions = {
		'transition':'transitionend',
		'OTransition':'otransitionend',  // oTransitionEnd in very old Opera
		'MozTransition':'transitionend',
		'WebkitTransition':'webkitTransitionEnd'
	};
	for (i in transitions) {
		if (transitions.hasOwnProperty(i) && el.style[i] !== undefined) {
			return transitions[i];
		}
	}
	return '';
})();

VENTURES.LATEST_ARTICLES_PER_PAGE = parseInt(VENTURES.LATEST_ARTICLES_PER_PAGE, 10);

(function($) {
	var $body = $(document.body),
			$win = $(window),
			$html = $(document.documentElement),
			vpw = window.innerWidth, vph = window.innerHeight,
			$nav = $('#site-navigation'),
			$topBar = $('#top-bar'),
			$categoryBoxes = $('#category-boxes'),
			$main = $('#main'), $new,
			$fullWidthText = $('.post-content blockquote'),
			$fixedButtons = $('.bottom-fixed-button'),
			$videos = $('.post-content').find('iframe'),
			isHighDensity = VENTURES.pixelDensity > 1.5;

	var $resp_imgs = $('.top-story:not(.type--ventures_feature_ads) img')
		.filter(function() {
			var w;
			return this.getAttribute('srcset') !== null && (w = this.getAttribute('data-original-width')) && parseInt(w, 10) > 0;
		});
	console.log('Big responsive images:', $resp_imgs);

	var topStoryBannerSlideshow = function() {
		var $banners = $('.top-story-banners');
		if ($banners.children().length > 1) {
			$banners.slick({
				slide: '.top-story',
				speed: 1000,
				autoplay: true,
				autoplaySpeed: 10000,
				dots: true,
				draggable: false,
				arrows: false
			});
		}
	};

	var Overlays = (function() {
		var $container = $('#overlay-container');
		var $openButton = $('.overlay-button');
		var $closeButton = $("#overlay-close-button");
		var funcs = {
			init: function() {
				$container.append($('.fixed-overlay'));
				$openButton.click(function(e) { funcs.open(this.getAttribute('data-overlay')); });
				$closeButton.click(funcs.close);
			},
			open: function(name) {
				$container.addClass('visible');
				$closeButton.addClass('visible');
				$openButton.addClass('hidden');
				var $targetContent = $('#' + name + '-overlay');//.show();
				var $otherContent = $targetContent.siblings('.fixed-overlay');
				$otherContent.removeClass('visible');
				$targetContent.addClass('visible');
				if (Modernizr.csstransitions) {
					$container.one('transitionend', funcs.afterOpen);
				}
				else {
					$container.animate({ opacity:1 }, 300, 'linear', funcs.afterOpen);
				}
			},
			afterOpen: function() {
				if (!$.placeholder.browser_supported() || !VENTURES.isIE) $(".fixed-overlay.visible input[type=search]").focus(); // placeholder is hidden on focus in IE
			},
			afterClosing: function($overlay) {
				$closeButton.removeClass('visible');
				$openButton.removeClass('hidden');
				if ($overlay.attr('id') === 'video-overlay') $overlay.empty();
				resetSharingLinks();
			},
			close: function() {
				var $overlay = $container.find('.fixed-overlay.visible');
				$overlay.removeClass('visible');
				$container.removeClass('visible');
				if (Modernizr.csstransitions) {
					$container.one('transitionend', function() { funcs.afterClosing($overlay); });
				}
				else {
					funcs.afterClosing($overlay);
					$container.css('opacity','');
				}
			}
		};
		return funcs;
	})();

	$.fn.objectFillPolyfill = function() {
		this.each(function() {
			var css = Modernizr.canvas ? { backgroundImage:'url(' + this.src + ')' }
				: { filter: 'progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=' + this.src + ') progid:DXImageTransform.Microsoft.Blur(pixelRadius=10)' };
			$(this).closest('.fitted-container').css(css);
		});
	};

	$.fn.transformAdUnits = function() {
		var isTablet = ('matchMedia' in window && window.matchMedia('(max-width: 969px)').matches);
		this.find('.adunit-wrap:not(.mid-article-ad)').each(function() {
			var $unit = $(this);
			var unit_name = $unit.attr('data-unit');
			if (unit_name==='Ventures_MobileBanner') {
				return $unit.remove();
			}
			if (isTablet) {
				if (unit_name==='Ventures_ListBanner') {
					return $unit.remove();
				}
				if (unit_name==='Ventures_FullWidth') {
					unit_name = 'Ventures_ListBanner';
				}
			}
			if (unit_name==='Ventures_ArticleSide') {
				var asideHeight = $unit.parent().outerHeight(), contentHeight = $unit.closest('.entry-content').find('.post-content').outerHeight();
				if (asideHeight > 0 && contentHeight < asideHeight + ASIDE_ADVERT_HEIGHT) {
					return $unit.remove();
				}
			}
			$(this).removeClass('adunit-wrap').addClass('adunit '+unit_name).data('adunit', unit_name);
		});
		// mid-article ads require checking to ensure that once the other 
		// ads are instantiated, they will not overlap with the sidebar.
		this.find('.adunit-wrap.mid-article-ad').each(function() {
			var $unit = $(this);
			var $container = $unit.closest(".entry-content");
			var $asides = $container.find(".post-asides");
			var max_width = $container.width();
			var ad_unit_top = $unit.offset().top;
			var asides_btm = $asides.offset().top + $asides.height() + 30;
			if (ad_unit_top <= asides_btm) {
				max_width = $container.width() - $asides.width();
			}
			if (max_width >= 970) unit_name = 'Ventures_FullWidth';
			else if (max_width >= 728) unit_name = 'Ventures_ListBanner';
			else return $unit.remove();
			$(this).removeClass('adunit-wrap').addClass('adunit '+unit_name).data('adunit', unit_name);
		});
		return this;
	};

	function addWatchedElements($header, $body) {
		$resp_imgs = $resp_imgs.add($header.find('.thumb'));
		$fullWidthText = $fullWidthText.add($body.find('blockquote'));
	}

	function updateTopBarVisibility() {
		var scroll = document.documentElement.scrollTop || document.body.scrollTop || window.scrollY;
		$topBar.toggleClass('opaque', scroll > VENTURES.TOP_BANNER_HEIGHT);
	}

	function initTopBarTransitions() {
		var react = true,
			lastScrollY = 0,
			invisible = false,
			scrollChangeThreshold = 20,
			threshold = VENTURES.TOP_BANNER_HEIGHT * 0.7;
		var disable = function() {
			react = false;
			invisible = false;
			$body.removeClass('top-invisible');
			$topBar.removeClass('use-transitions');
		};
		var enable = function() {
			react = true;
			$topBar.addClass('use-transitions').css('top', '');
		};
		var action = function(scroll) {
			if (react) {
				if ((invisible && scroll.change < -scrollChangeThreshold) ||
					(!invisible && scroll.change > scrollChangeThreshold)) {
					invisible = !invisible;
				}
				$body.toggleClass('top-invisible', invisible);
			}
			else $topBar.css('top', -scroll.current + 'px');
		};
		Scroll.add(function(e, scroll) {
			if (react && scroll.current === 0) {
				disable();
			}
			if (!react && scroll.current > threshold) {
				enable();
			}
			action(scroll);
		});
		$win.load(function() {
			disable();
			invisible = window.scrollY > threshold;
			$body.toggleClass('top-invisible', invisible);
		});
		$(document).on('scroll', function() {
			updateTopBarVisibility();
		}.throttle(150));
	}

	function initInterviewsOverlay() {
		var url_regexp = /interviews\/?$/,
				$overlay = $('#video-overlay');
		var addcontent = function($link) {
			$overlay.append($link.attr('data-video-html'))
				.append('<div class="caption small-bottom-bar">'+$link.attr('data-description')+'</div>');
			updateSharingLinks($link.data('share_links'));
		};
		$(document).on('click', 'a[href*="/interviews/"]', function(e) {
			var $link = $(this);
			if (!url_regexp.test(this.href) && !$link.hasClass('no-overlay')) {
				var addVideo = addcontent.curry($link);
				e.preventDefault();
				e.stopImmediatePropagation();
				if (Modernizr.csstransitions) {
					$('#overlay-container').one('transitionend', addVideo);
				}
				else {
					addVideo();
				}
				Overlays.open('video');
				if (this.hasAttribute('data-post-id')) {
					trackPageView(this.title, this.href, this.getAttribute('data-post-id'));
				}
			}
		});
	}

	function centerTopMenu() {
		var extraRight = 18, // space to the left of the search icon.
				$left = $topBar.children('.left');
				$right = $topBar.children('.right');
		var left = parseInt($left.css('margin-left'), 10) + $left.outerWidth(),
				right = $right.outerWidth();
		$topBar.children('.middle').css('margin-left', (extraRight + left - right) + 'px');
	}

	var debouncedResize = function() {
		vpw = $win.width(); vph = $win.height();
		var $articles = $categoryBoxes.children("article");
		$articles.matchHeight();
		if ($articles.length % 2) {
			$articles.last().removeAttr("style").addClass("last");
		}
		$resp_imgs.toggleBlur();
		if ($body.hasClass('single')) {
			var $content = $('.post-content');
			if ($content.length > 0) {
				var contentLeft = $content.offset().left, contentWidth = $content.width(), centerWidth = $content.parent().width();
				var fullWidth = Math.min(centerWidth - 182, contentWidth + Math.min(Math.max(0, contentLeft - FULL_WIDTH_MAX_SPACING), FULL_WIDTH_MAX_EXTRA_WIDTH / 2) * 2);
				$fullWidthText.css({ width: fullWidth + 'px', marginLeft: -((fullWidth - contentWidth) / 2) + 'px' });
			}
		}
		$categoryBoxes.betterWaypoint('refresh');
		centerTopMenu();
		$videos.each(function(i, el) {
			var ratio = parseInt(el.getAttribute('height'), 10) / parseInt(el.getAttribute('width'), 10);
			if (!isNaN(ratio)) {
				el.style.height = (el.offsetWidth * ratio) + 'px';
			}
		});
	}.debounce(250);

	function trackPageView(title, url, postID) {
		if ('__gaTracker' in window) {
			window.__gaTracker('set', { page: url, title: title });
			window.__gaTracker('send', 'pageview');
		}
		if (postID) {
			$.ajax({
				url: VENTURES.AJAX_URL,
				method: 'POST',
				data: {
					action: 'update_views_ajax',
					id: postID,
					token: VENTURES.WPP_NONCE
				}
			}).done(function(view_data, textStatus, jqXHR) {
				// the wpp ajax function kills the script, so a separate request to update the nonce.
				$.ajax({
					url: VENTURES.AJAX_URL,
					method: 'POST',
					dataType: 'text',
					data: {
						action: 'wpp_nonce_refresh'
					}
				}).done(function(nonce, textStatus, jqXHR) {
					VENTURES.WPP_NONCE = nonce;
				});
			});
		}
	}

	function updateHistoryState(data, title, url, isNew) {
		if (title === undefined) title = ('title' in data) ? data.title : document.title;
		if (url === undefined) url = ('url' in data) ? data.url : window.location.href;
		document.title = title;
		window.history[isNew ? 'pushState' : 'replaceState'](data, title, url);
	}
	function replaceHistoryState(data, title, url) {
		updateHistoryState(data, title, url, false);
	}
	function pushHistoryState(data, title, url) {
		updateHistoryState(data, title, url, true);
		trackPageView(data.title, data.url, data.id);
	}

	function updateLayoutOnLoad(e) {
		// let the blur code run first, to avoid showing the unblurred image if it needs blurring.
		if (!VENTURES.filterBlurs && !Modernizr.objectFit) setTimeout(function() { $('.fitted').objectFillPolyfill(); }, 2000);
		$videos.each(function(i, el) {
			var ratio = parseInt(el.getAttribute('height'), 10) / parseInt(el.getAttribute('width'), 10);
			if (!isNaN(ratio)) {
				el.style.height = (el.offsetWidth * ratio) + 'px';
			}
		});
	}
	function updateLayoutOnResize(e) {
		updateTopBarVisibility();
		debouncedResize();
	}
	$(window).load(updateLayoutOnLoad);
	$(window).resize(updateLayoutOnResize);

	function initAjaxSearch() {
		$("form.search-form").submit(function(e) {
			$("#search-overlay").addClass("loading");
			$("#search-overlay input[type=submit]").attr("disabled", "disabled").stop().animate(
				{ opacity: 0 }, 200, 'linear', function() {
				$("#search-overlay .search-loading").stop().fadeIn(200);
			});
			$("#search-overlay").addClass("searching");
			var $form   = $(this);
			var s       = $form.find("input[name=s]").val();
			var htmlUrl = $form.attr("action")+"?"+$form.serialize();
			$.ajax(
				ajaxurl, 
				{
					method: 'POST',
					data: {
						action: 'ventures_ajax_search',
						search: s
					}
				}
			)
			.done(function(data, textStatus, jqXHR) {
				$("#search-overlay").addClass("results").removeClass('searching');
				$("#search-results").html(data).one("transitionend", function() {
					$("#search-results").attr("style", "-webkit-transition-delay: 0;transition-delay: 0;");
				})
			})
			.fail(function(jqXHR, textStatus, errorThrown) {
				window.location.href = htmlUrl;
			})
			.always(function() {
				$("#search-overlay").removeClass("loading");
				$("#search-overlay .search-loading").stop().fadeOut(200, function() {
					$("#search-overlay input[type=submit]").removeAttr("disabled").stop().animate(
					{ opacity: 1 }, 200, 'linear');
				});
			});
			e.preventDefault();
		});
	}
			
	$.fn.betterWaypoint = function(method_or_options) {
		var $elements = this;
		var methods = {
			enable: function() {
				$elements.first().data('waypoints').forEach(function(w) { w.enable(); });
			},
			create: function(options) {
				var ws = $elements.waypoint($.extend(options, $elements.data('waypoint_options')));
				$elements.first().data('waypoints', ws);
			},
			destroy: function() {
				$elements.first().data('waypoints').forEach(function(w) { w.disable(); w.destroy(); });
				$elements.first().removeData('waypoints');
			},
			refresh: function() {
				methods.destroy();
				methods.create({ enabled: false });
			}
		};
		if (typeof(method_or_options) === 'string') {
			if ((method_or_options in methods) && this.first().data('waypoints')) {
				methods[method_or_options]();
			}
		}
		else {
			this.first().data('waypoint_options', method_or_options);
			methods.create({});
		}
		return this;
	};

	$.fn.initInfiniteScroll = function() {
		if (this.length === 0) return this;
		var $post_body = $main.find('.post').first(),
			$post_headers = function() { return $main.find('.top-story') },
			$target = this,
			scrollIndex = 0, wasScrollFromState = false, queue = [], item;
		var $post_header = $post_headers().first(),
			$titlelink = $(document.createElement('a')).attr('rel', 'bookmark').addClass('gray-arrow'),
			$catLabel = null;
		for (var i = 0; i < VENTURES.RELATED_POSTS.length; i++) {
			item = VENTURES.RELATED_POSTS[i];
			queue.push({ header: $post_header.clone(), body: $post_body.clone() });
			queue[i].header
				.data('postID', item.id)
				.data('share_links', item.share_links)
				.attr('class', $post_header.attr("class")+' related')
				.attr('style', 'height: '+$post_header.height()/2+'px')
				.find('.imgwrap').empty().end()
				.find('.entry-title').text(item.title).wrap($titlelink.clone().attr('href', item.permalink)).end();
			if (item.image) $(new Image()).attr(item.image).attr('alt', item.image_alt).addClass('fitted').appendTo(queue[i].header.find('.imgwrap'));
			queue[i].body
				.attr('id', 'post-' + item.id)
				.attr('class', item['class'].join(' ')+' related')
				.find('.credit').replaceWith(item.credit_html).end()
				.find('.post-author-name a').text(item.author.name).attr('href', item.author.url).end()
				.find('.post-date').text(item.date).end()
				.find('.post-content').html(item.content).end()
				.find('.post-permalink').val(item.short_url).end();
			if (item.author.thumb) queue[i].body.find('.author a').attr('href', item.author.url).empty().html(item.author.thumb);
			else queue[i].body.find('.author').remove();
				
			queue[i].header.find('.category-label').remove();
			$catLabel = $(document.createElement(item.category_label.url === '' ? 'span' : 'a')).addClass('category-label')
				.attr('href', item.category_label.url).text(item.category_label.title).prependTo(queue[i].header.find('.inner'));
			queue[i].page_view = [item.title, item.permalink, item.id];
		}
		$post_headers().betterWaypoint({
			handler: function(direction) {
				var nextIndex = scrollIndex + (direction === 'down' ? 1 : -1),
					$headers = $post_headers();
				if (nextIndex < 0 || nextIndex >= $headers.length) return;
				else scrollIndex = nextIndex;
				updateSharingLinks($headers.eq(scrollIndex).data('share_links'));
			},
			enabled: false,
			offset: '50%'
		});
		$target.betterWaypoint({
			handler: function(direction) {
				var post;
				if (direction === 'down' && (post = queue.shift())) {
					$('<hr class="related">').add(post.header).add(post.body).insertBefore($target);
					addWatchedElements(post.header, post.body);
					$win.trigger('resize');
					trackPageView.apply(null, post.page_view);
					post.body.transformAdUnits();
					var adunits = post.body.find(".adunit");
					setTimeout(function() {
						$.dfp({
							selector: adunits,
							refreshExisting: false
						}); 
					}, 10);
					try { FB.XFBML.parse(post.body[0]); } catch(e) {}
					$target.betterWaypoint('refresh').parent().trigger('contentupdated');
					$post_headers().betterWaypoint('refresh').betterWaypoint('enable');
					$target.betterWaypoint('enable');
				}
			},
			offset: '125%'
		});
		return this;
	};

	$.fn.categoryBoxOuterHeight = function() {
		return [].slice.call(this.children().map(function() {
			return $(this).css('position') === 'absolute' ? 0 : $(this).outerHeight();
		}), 0).reduce(function(total, h) {
			return total + h;
		}, 24);
	};

	$.fn.matchHeight = function() {
		var $boxes = this, index = this.data('max-height-index');
		setTimeout(function() {
			var heights = $boxes.map(function() { return $(this).categoryBoxOuterHeight(); });
			var ordered = [].slice.call(heights, 0).sort(function(a, b) { return b - a; });
			$boxes.css('height', ordered[0] + 'px');
		}, 1);
		return this;
	};

	$.fn.teaserRollover = function() {
		var margin = 10, oH = 200;
		var $elts = this;
		var compute = function(e) {
			$elts.each(function(i, el) {
				var $el = $(el);
				var $in = $el.find(".in");
				var $stronger = $el.find("strong").clone().addClass("block").appendTo($in);
				setTimeout(function() {
					$el.data('strongH', $stronger.outerHeight());
					$stronger.remove();
				}, 16); // allow time for the browser to figure out the layout.
				$el.data('catH', $el.find(".category").outerHeight());
				$el.data('init', true);
			});
		}
		$(window).resize(compute).resize();
		$(window).load(compute);
		return $elts.each(function() {
			var timeoutId = 0;
			$(this)
				.data('init', false)
				.on('mouseenter', function() {
					var $el = $(this);
					if (!$el.data('init')) return;
					$el.find(".excerpt, .category").css("opacity", 0);
					timeoutId = setTimeout(function() {
						$el.find(".in").addClass("transitions");
						var h = $el.data('strongH'), catH = $el.data('catH');
						var newTop = oH - (h + margin + catH);
						if (VENTURES.teaserCssTransforms) {
							$el.find(".in").css({ transform: "translate(-50%, " + (newTop - oH / 2) + 'px)' });
							$el.find(".play-circle").css({
								transform: "translate(4px, "+(oH / 2 - 21)+"px) scale(0.65)"
							});
						}
						else {
							$el.find(".in").css("top", newTop + 'px');
							$el.find(".play-circle").css({
								top: (oH - 42)+"px",
								left: "6px",
								width: "26px"
							});
						}
						$el.find(".background").css({ transform: "scaleY(" + ((h + 2 * margin) / oH) + ")" });
					}, 20);
				})
				.on('mouseleave', function() {
					var $el = $(this);
					if (!$el.data('init')) return;
					clearTimeout(timeoutId);
					$el.find('.play-circle').removeAttr("style");
					if (VENTURES.teaserCssTransforms) {
						$el.find(".in").css({transform: ""});
					}
					else {
						$el.find(".in").css('top', '');
					}
					$el.find(".excerpt, .category").css("opacity", 1);
					$el.find(".background").css({ transform: "" });
				});
		});
	};

	var pageSwitch = (function() {
		var preserveBodyClasses = ['logged-in', 'admin-bar'],
			prevScroll = 0;
		var enableNew = function(state, isNew) {
			$html.addClass('end-page-animation');
			$html.addClass('page-animation-fade');
			if (isNew) {
				pushHistoryState({
					title: state.title,
					url: state.href
				});
			}
			$main = $new;
		};
		var revertToOld = function(title) {
			document.title = title;
			document.documentElement.scrollTop = document.body.scrollTop = window.scrollY = prevScroll;
			$html.removeClass('page-animation-fade');
		};
		return {
			forward: function(state, onTransitionEnd, isNew) {
				prevScroll = document.documentElement.scrollTop || document.body.scrollTop || window.scrollY;
				$new.on(TRANSITION_END_EVENT_NAME, function(e) {
					if (e.target.className.indexOf('site-main') !== -1
						&& e.originalEvent.propertyName.toLowerCase().indexOf('transform') !== -1
					) {
						// safari bubbles transition events, so we can't use $.fn.one
						$new.off(TRANSITION_END_EVENT_NAME);
						setTimeout(function() {
							document.documentElement.scrollTop = document.body.scrollTop = window.scrollY = 0;
							enableNew(state, isNew);
							if (onTransitionEnd) onTransitionEnd();
						}, 32);
					}
				});
				$html.addClass('page-animation');
				$main.data('body-class', document.body.className);
				document.body.className = ['single', 'single-post'].concat(array_filter(preserveBodyClasses, function(s) {
					return $body.hasClass(s);
				})).join(' ');
				updateTopStoryBanner($new[0]);
			},
			back: function(title) {
				if (!$html.hasClass('end-page-animation')) return false;
				$main = $('#main');
				$new.on(TRANSITION_END_EVENT_NAME, function(e) {
					if (e.target.className.indexOf('site-main') !== -1
						&& e.originalEvent.propertyName.toLowerCase().indexOf('transform') !== -1
					) {
						$new.off(TRANSITION_END_EVENT_NAME);
						revertToOld(title);
					}
				});
				$html.removeClass('end-page-animation page-animation');
				$html.removeClass('');
				document.body.className = $main.data('body-class');
				updateTopStoryBanner($main[0]);
			}
		};
	})();

	function single_post_init() {
		if (Modernizr.textSelection) {
			selectionShareInit();
		}
		$('#infinite-scroll-target').initInfiniteScroll();
	}

	function resetSharingLinks() {
		$('#share-links').find('a').each(function() { $(this).attr($(this).data('o_attributes')); });
	}

	function updateSharingLinks(links) {
		var $a;
		for (var $list = $('#share-links').children(), i = 0; i < links.length; i++) {
			$a = $list.filter('.' + links[i].slug).find('a');
			$a.data('o_attributes', { href: $a.attr('href'), title: $a.attr('title') }).attr({ href: links[i].url, title: links[i].title });
			if (links[i].slug === 'twitter') {
				$('#selection-share').find('a').each(function() { $(this).data('original-href', links[i].url); });
			}
		}
	}

	function initTeasersCarousel() {
		var $container = $('#latest-articles');
		var $lis = $container.find('li'),
				$ul = $container.find('ul'),
			current = VENTURES.LATEST_ARTICLES_PER_PAGE - 1,
			changeDurations = [5, 5];
		if ($lis.length > VENTURES.LATEST_ARTICLES_PER_PAGE) {
			$lis.addClass('animated').find('img').closest('li').addClass('has-thumb');
			var height = $container.height();
			var minHeight = $('#top-stories').height();
			$container.css('height', Math.max(minHeight, height) + 'px');
			var advance = function() {
				setTimeout(change, (changeDurations.length > 1 ? changeDurations.shift() : changeDurations[0]) * 1000);
			};
			var change = function() {
				var $prev = $container.find('li.visible').last().toggleClass('visible hiding'),
						$first = $container.find('li:first-child');
				current = (current + 1) % $lis.length;
				var $next = $lis.eq(current);
				var top = $next.outerHeight() + parseInt($prev.css('margin-top'), 10);
				height += ($next.height() - $prev.height());
				$next.css('margin-top', -top + 'px').prependTo($ul.addClass('animating').toggleClass('long-animation', $next.hasClass('has-image')));
				$container.css('height', Math.max(minHeight, height) + 'px');
				setTimeout(function() {
					$ul.css('transform', 'translateY(' + top + 'px)');
					$next.toggleClass('hidden showing');
				}, 1);
				setTimeout(function() {
					$ul.removeClass('animating');
					setTimeout(function() {
						$ul.css('transform', '');
						$next.css('margin-top', '');
						$prev.toggleClass('hiding hidden');
						$next.toggleClass('showing visible');
					}, 1);
				}, 1400 + ($next.hasClass('has-image') ? 400 : 0));
				advance();
			};
			advance();
		}
	}

	function selectionShareInit() {
		var lastSelection = '';
		Selection.prototype.isNormalSelection = function() {
			var isRange = !('type' in this) || this.type === 'Range';
			return this.rangeCount === 1 && this.anchorNode.nodeType === 3 && isRange && this.toString() !== '';
		};
		var $links = $('#share-links').clone().attr('id', '').on('click', 'a', function(e) {
			e.preventDefault();
			openCenteredPopup(this.href, this.title, SHARE_POPUP_DIMENSIONS.WIDTH, SHARE_POPUP_DIMENSIONS.HEIGHT);
		}), mousePos = {};
		$links.find('li').not('.twitter').remove();
		$links.find('a').each(function() { $(this).data('original-href', this.href); });
		var $button = $(document.createElement('div')).attr('id', 'selection-share').addClass('bottom-fixed-button').append($links).appendTo($('#colophon'));
		var onSelectionChange = function(sel) {
			var text = sel.toString(), rect = sel.getRangeAt(0).getBoundingClientRect(), quote = '', $content, pos;
			if (text.length > 0 && ($content = $(sel.anchorNode).closest('.post-content')).length > 0 && (
				sel.anchorNode === sel.focusNode
				|| $content.is($(sel.focusNode).closest('.post-content'))
			)) {
				var lineHeight = parseInt($content.css('line-height'), 10) + 5; // add extra for space between lines.
				if (text.length <= SELECTION_SHARE_MAX_LENGHT) {
					quote = text;
				}
				else {
					var words = text.split(/\s/), count = 0, i = -1, j = -1;
					while (count < SELECTION_SHARE_MAX_LENGHT / 2) count += words[++i].length + 1;
					count = 0;
					while (count < SELECTION_SHARE_MAX_LENGHT / 2) count += words[words.length - 1 - (++j)].length + 1;
					quote += words.slice(0, i).join(' ') + ' ... ' + words.slice(-j).join(' ');
				}
				quote = '\u201C' + quote.replace('"', "'").trim() + '\u201D';
				$links.find('a').each(function() {
					var parts = decodeURIComponent($(this).data('original-href')).split('=');
					this.href = parts[0] + '=' + encodeURIComponent(quote + ' - ' + parts[1]);
				});
				if (rect.height > lineHeight) { // multi-line
					var x =  mousePos[mousePos.diff[1] > 0 ? 'start' : 'end'][0];
					var width = rect.right - x;
					if (width > 100 && x > rect.left && x < rect.right) { // normal selection
						pos = { x: x + width / 2, y: rect.top };
					}
					else { // outside selection
						pos = { x: rect.left + rect.width / 2, y: rect.top + (x > rect.left ? lineHeight : 0) };
					}
				}
				else {
					pos = { x: rect.left + rect.width / 2, y: rect.top };
				}
				$button.css({ left: Math.round(pos.x - $button.outerWidth() / 2) + 'px', top: Math.round(pos.y - $button.outerHeight() - SELECTION_SHARE_BUTTON_OFFSET) + 'px' }).addClass('visible');
			}
			else {
				$button.removeClass('visible');
			}
		};
		$(document).on('mousedown', function(e) {
			mousePos.start = [e.clientX, e.clientY];
		}).on('mouseup', function(e) {
			var sel = window.getSelection();
			var text = sel.toString();
			if (sel.isNormalSelection()) {
				if (text !== lastSelection && $(e.target).parents('.entry-content').length > 0) {
					mousePos.end = [e.clientX, e.clientY];
					mousePos.diff = [mousePos.end[0] - mousePos.start[0], mousePos.end[1] - mousePos.start[1]];
					onSelectionChange(sel);
				}
			}
			else {
				$button.removeClass('visible');
			}
			lastSelection = text;
		});
	}

	function keepFixedButtonsAboveFooter() {
		var height = 29, margin = 11;
		$categoryBoxes.betterWaypoint({
			handler: function(direction) {
				var fixed = direction === 'up';
				$body.toggleClass('no-fixed-buttons', !fixed);
				$fixedButtons.css('top', fixed ? '' : ($categoryBoxes.offset().top - height - margin) + 'px');
			},
			enabled: true,
			offset: '100%'
		}).betterWaypoint('enable');
	}

	function buildTopStoriesPanel($btn) {
		var btn = $btn[0];
		btn.hasBuiltPanel = true;
		btn.defaultWidth = $btn.outerWidth();
		btn.defaultHeight = $btn.outerHeight();
		var $list = $('<ul class="top-stories-list">');
		while(VENTURES.TOP_STORIES.length) {
			var s = VENTURES.TOP_STORIES.shift();
			var $li = '<li><a href="'+s.url+'">';
			if (s.img) $li += '<img src="'+s.img.src+'"';
			$li += '><span><em class="category-label">'+s.cat_name+'</em><strong>'+s.title+'</strong></span></a></li>';
			$list.append($li);
		}
		var $wrapper = $('<div class="wrap"></div>');

		// measure height by appending it to an invisible absolute body-div
		$div = $('<div style="visibility:hidden;position:absolute;top:0;left:-100%"></div>');
		$("body").append($div);
		$div.append($list);
		btn.panelWidth = $list.outerWidth() + 1;
		btn.panelHeight = $list.outerHeight() + 5;
		var dims = {
			width: btn.panelWidth,
			height: btn.panelHeight
		}
		$list.css(dims);

		$wrapper.append($list);
		$(btn.parentNode).append($wrapper);
		$div.remove();
	}

	function updateTopStoriesPanel() {
		var $btn = $('#top-stories-button button');
		if (!$btn.length) return;
		var btn = $btn[0];

		var $list = $('ul.top-stories-list');
		var $wrapper = $list.parent();
		$list.remove();
		$list.removeAttr("style");
		$div = $('<div style="visibility:hidden;position:absolute;top:0;left:-100%"></div>');
		$("body").append($div);
		$div.append($list);
		btn.panelWidth = $list.outerWidth() + 1;
		btn.panelHeight = $list.outerHeight() + 5;
		var dims = {
			width: btn.panelWidth,
			height: btn.panelHeight
		}
		$list.css(dims);
		$wrapper.append($list);
	}
	window.updateTopStoriesPanel = updateTopStoriesPanel;

	function initTopStoriesBtn() {
		var $btn = $('#top-stories-button button');
		if (!$btn.length) return;
		var btn = $btn[0];
		buildTopStoriesPanel($btn);
		$btn.click(onTopStoriesClick);
		$("#top-stories-button").bind("clickoutside", closeTopStoriesPanel);
	}

	function closeTopStoriesPanel() {
		var container = $("#top-stories-button");
		var btn = container.find("button")[0];
		var panel = $(btn.parentNode);
		var list = panel.find("ul");
		var wrap = panel.find(".wrap");
		if (wrap.outerHeight() <= 10) return;
		wrap.animate(
			{ 
				width: btn.defaultWidth,
				height: 0
			},
			250, 
			'linear',
			function() {
				panel.css("z-index", "90");
			}
		);
	}

	function onTopStoriesClick(e) {
		var btn = this;
		var panel = $(btn.parentNode);
		var list = panel.find("ul");
		var wrap = panel.find(".wrap");
		if (wrap.outerHeight() > 10) {
			closeTopStoriesPanel();
		}
		else {
			panel.css("z-index", "110");
			wrap.animate(
				{ width: (btn.panelWidth) }, 
				500, 
				'swing', 
				function() {
					wrap.animate(
						{ height: btn.panelHeight },
						200,
						'swing'
					);
				}
			);
		}
	}

	$(document).ready(function() {
		updateTopStoryBanner();
		topStoryBannerSlideshow();
		$(window).resize();
		$resp_imgs.toggleBlur();
		$('#main-menu-toggle').on('click', function(e) {
			$body.toggleClass('menu-visible');
		});
		$(document).on('click', function(e) {
			if (e.target.id !== 'main-menu-toggle' && !$.contains($nav[0], e.target)) {
				$body.removeClass('menu-visible');
			}
		});

		if (VENTURES.isHighPerformance) {
			initInterviewsOverlay();
			initTopBarTransitions();
			$(".has-image .teaser-content").teaserRollover();
		}

		if ($body.hasClass('single')) {
			single_post_init();
		}

		$('#share-links').on('click', 'a', function(e) {
			e.preventDefault();
			openCenteredPopup(this.href, this.title, SHARE_POPUP_DIMENSIONS.WIDTH, SHARE_POPUP_DIMENSIONS.HEIGHT);
		});

		if ('DFP_NETWORK_ID' in VENTURES) {
			$('body').addClass('dfp').transformAdUnits();
			$.dfp(VENTURES.DFP_NETWORK_ID);
		}

		setTimeout(initTopStoriesBtn, 50);
		$(window).load(updateTopStoriesPanel);

		Overlays.init();

		initAjaxSearch();

		if (VENTURES.filterBlurs && !Modernizr.objectFit) $('.fitted').objectFillPolyfill();

		if (VENTURES.ENABLE_NARROW_TEASERS_CAROUSEL) initTeasersCarousel();

		if (!('ontouchstart' in document)) keepFixedButtonsAboveFooter();

		$(window).resize().load(function() {
			setTimeout(function() {
				// can't do it right away, because safari could enter into a reload loop.
				window.onpopstate = function(e) {
					window.location.replace(window.location.href);
				};
			}, 5000);
		});

		$("body").addClass("ideas-page-adjusted");
	});

})(jQuery);
