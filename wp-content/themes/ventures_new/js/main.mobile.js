VENTURES.isHighDensity = (('devicePixelRatio' in window) && window.devicePixelRatio > 1.5);

// generic handler for all clicks
document.addEventListener('click', function(e) {
	var t = e.target;
	if (t.parentNode.id==='main-menu-toggle') { // this is to shim clicks on inner elements.
		t = t.parentNode;
	}
	if (t.id === 'main-menu-toggle') {
		document.body.classList.toggle('menu-visible');
		return false;
	}
	if (t.id !== 'main-menu-toggle' && !document.getElementById('extra-navigation').contains(t)) {
		document.body.classList.remove('menu-visible');
	}
	if (t.classList.contains('post-content-more')) {
		onPostContentMoreClicked(t);
		return false;
	}
});

function postAjax(url, data, successCallback, errorCallback){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function(){
        if (xmlhttp.readyState == 4) {
            if (xmlhttp.status >= 200 && xmlhttp.status < 300){
                successCallback(xmlhttp.responseText);
            }
            else {
                errorCallback(xmlhttp);
            }
        }
    }
    xmlhttp.open('POST', url, true);
    xmlhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(data);
}

function checkValidity(form) {
    var inputs = form.querySelectorAll("input");
    for(var k = 0; k < inputs.length; k++) {
        inputs[k].classList.remove('invalid');
    }
    var invalidElts = getInvalidElements(form);
    if (invalidElts.length) {
        for(var j = 0; j < invalidElts.length; j++) {
            invalidElts[j].classList.add('invalid');
        }
        return false;
    }
    return true;
}

function getInvalidElements(form) {
    var elts = [];
    var reqs = form.querySelectorAll("[required]");
    for(var i = 0; i < reqs.length; i++) {
        if (reqs[i].value==='') elts.push(reqs[i]);
    }
    return elts;
}

function serializeForm(form) {
	var params = '';
    for (var i = 0; i < form.elements.length; i++) {
        var elt = form.elements[i];
        var value = (elt.tagName == "SELECT")
            ? elt.options[elt.selectedIndex].value
            : elt.value;                
        params += elt.name + "=" + encodeURIComponent(value) + "&";
    }
    return params;
}

function serialize(obj) {
    var str = [];
    for(var p in obj) {
        if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
    }
    return str.join("&");
}

function initAjaxSearch() {
	var search_overlay = document.getElementById("search-overlay");
	var search_form    = search_overlay.querySelector("form");
	var search_input   = search_overlay.querySelector("input[name=s]");
	var search_submit  = search_overlay.querySelector("input[type=submit]");
	var search_loading = search_overlay.querySelector(".search-loading");
	var search_results = document.getElementById("search-results");
	function disableSubmit(form) {
		search_overlay.classList.add("loading");
		search_submit.setAttribute("disabled", "disabled");
		search_submit.style.display = 'none';
		search_loading.style.display = 'block';
		search_results.innerHTML = '';
	}
	function enableSubmit(form) {
		search_overlay.classList.remove("loading");
		search_submit.removeAttribute("disabled");
		search_submit.style.display = 'block';
		search_loading.style.display = 'none';
	}
	function successCallback(responseText) {
		enableSubmit(search_form);
		search_overlay.classList.add("results");
		search_results.innerHTML = responseText;
	}
	function errorCallback(xmlhttp) {
		window.location.href = search_form.getAttribute('action')+'?'+serializeForm(search_form);
	}
    search_form.addEventListener("submit", function(event) {
        if (!checkValidity(search_form)) return false;

        disableSubmit(search_form);

        var data = {
        	action: 'ventures_ajax_search',
        	search: search_input.value
		};

        postAjax(
        	'/wp/wp-admin/admin-ajax.php', 
        	serialize(data), 
        	successCallback, 
        	errorCallback
        );

    	if (event.preventDefault) event.preventDefault();
    	event.returnValue = false;
    });
}

var initOverlays = function() {
	var container = document.getElementById('overlay-container');
	var fixedOverlays = document.querySelectorAll('.fixed-overlay');
	[].forEach.call(fixedOverlays, function(elt) {
		container.appendChild(elt);
	});
	var openButtons = document.querySelectorAll('.overlay-button');
	var closeButton = document.getElementById("overlay-close-button");
	[].forEach.call(openButtons, function(elt) {
		elt.addEventListener('click', function(e) {
			[].forEach.call(fixedOverlays, function(elt) {
				elt.classList.remove('visible');
			});
			var overlay = document.getElementById(this.getAttribute('data-overlay')+'-overlay');
			overlay.classList.add('visible');
			container.classList.add('visible');
			closeButton.classList.add('visible');
			[].forEach.call(openButtons, function(elt) {
				elt.classList.add('hidden');
			});
		}.bind(elt));
	});
	closeButton.addEventListener('click', function(e) {
		container.classList.remove('visible');
		closeButton.classList.remove('visible');
		[].forEach.call(openButtons, function(elt) {
			elt.classList.remove('hidden');
		});
	});
};

if (typeof($)==='undefined') {
	var $ = jQuery || Zepto;
}

var initAds = function() {
	if ('DFP_NETWORK_ID' in VENTURES) {
		$('body').transformAdUnits();
		$.dfp(VENTURES.DFP_NETWORK_ID);
	}
}

$.fn.transformAdUnits = function() {
	this.find('.adunit-wrap').each(function() {
		var unit_name = $(this).attr('data-unit');
		if (unit_name!=='Ventures_FullWidth' && unit_name!=='Ventures_MobileBanner' && unit_name!=='') {
			$(this).remove();
			return;
		}
		unit_name = 'Ventures_MobileBanner';
		$(this).removeClass('adunit-wrap').addClass('adunit '+unit_name).data('adunit', unit_name);
	});
	return this;
}

initOverlays();
initAjaxSearch();
$(initAds);
$(function() {
	$(".post-type-archive-ventures_ideas #latest-articles, .post-type-archive-ventures_ideas #latest-articles + aside").insertBefore("#category-boxes");
	$(".post-type-archive-ventures_ideas #dailybrief-button").insertAfter("#top-stories > h2");
	$("body").addClass("ideas-page-adjusted");
})
