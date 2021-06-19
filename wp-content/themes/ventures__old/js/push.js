$(document).ready(function() {
    $(window).on('scroll', function() {
        var scrollTop = $(window).scrollTop(),
            elementOffset = $('div.site-content').offset().top,
            distance = (elementOffset - scrollTop);
        var currentFixedDivPosition = $('div.site-content').position().top + $('div.site-content').height() + $(window).scrollTop();
        var temp, whichOne;
        $('section.top-story').each(function(i, s) {
            var diff = Math.abs($(s).position().top - currentFixedDivPosition);
            if (temp) {
                if (diff < temp) {
                    temp = diff;
                    whichOne = s;
                }
            } else {
                temp = diff;
                whichOne = s;
            }
        });
        //console.log($(whichOne).find('.inner a.gray-arrow').attr('href'));
        history.pushState(null, $(whichOne).find('.inner h1.entry-title').text(), $(whichOne).find('.inner a.gray-arrow').attr('href'));
        pageurl = $(whichOne).find('.inner a.gray-arrow').attr('href');
        if(typeof _gag !== 'undefined') {
          _gaq.push(['_trackPageview', pageurl]);
        }
        function gaTracker(id){
          $.getScript('//www.google-analytics.com/analytics.js'); // jQuery shortcut
          window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
          ga('create', 'UA-38400527-1', 'auto');
          ga('send', 'pageview');
        }
        var script = document.createElement('script');
        script.src = '//www.google-analytics.com/ga.js';
        document.getElementsByTagName('head')[0].appendChild(script);
        function gaTrack(path, title) {
          ga('set', 'page', $(whichOne).find('.inner a.gray-arrow').attr('href'));
          ga('send', 'pageview');
        }
        gaTracker('UA-38400527-1');
        ga('send', 'pageview');
    });
});