// $(".main-menu").hide();


// $(".category-header").scroll(function() {
//     if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
//         $(".main-menu").show();    
//     } else {
//         $(".main-menu").hide();
//     }
// });
$(document).ready(function(){
    $(".main-menu1").hide();
    $('.at-share-btn').hide();
    $('.main-menu3').hide().removeAttr('style');
    $('#sideads1').hide();
    $('#sideads2').hide();
    var $output = $('.category-header');
    $(window).on('scroll', function (){
        var scrollTop     = $(window).scrollTop(),
            elementOffset = $('.container').offset().top,
            distance      = (elementOffset - scrollTop);
            if(distance <= 0){
              $(".main-menu1").show();
              $('#at4-share').show();
              $('.main-menu3').show().removeAttr('style');
              $('#sideads1').show().removeAttr('style');
              $('#sideads2').show().removeAttr('style');
            }else{
              $(".main-menu1").hide();
              $('#at4-share').hide();
              $('.main-menu3').hide().removeAttr('style');
              $('#sideads1').hide();
              $('#sideads2').hide();
            }
            var currentFixedDivPosition = $('div.container').position().top + $('div.container').height() + $(window).scrollTop();
            var temp, whichOne;
            $('div.catpost').each(function (i, s) {
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
            history.pushState(null, $('head > title').html(), window.location.href);
            pageurl = '#/' + $(whichOne).attr('id');
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
              ga('set', 'page', '#/' + $(whichOne).attr('id'));
              ga('send', 'pageview');
            }
            gaTracker('UA-38400527-1');
            ga('send', 'pageview');
    });
    $("#main-menu3").click(function(e) {
        e.preventDefault();
        $(".main-menu").toggleClass("expanded");
        if($(this).find($(".fa")).hasClass('fa-arrow-circle-right')){
            $(this).find($(".fa")).removeClass('fa-arrow-circle-right').addClass('fa-arrow-circle-left');
        }else if($(this).find($(".fa")).hasClass('fa-arrow-circle-left')){                     
            $(this).find($(".fa")).removeClass('fa-arrow-circle-left').addClass('fa-arrow-circle-right');
        }
    });
});