;function openCenteredPopup(url, title, w, h) {
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;

    width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2.5) - (h / 2.5)) + dualScreenTop;
    var opts = 'scrollbars=yes, resize=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left;

    // ie8 can't really handle the window title arg
    if (document.documentElement.className.indexOf('ie8')>=0) {
        title = '';
    }

    var newWindow = (window.open(url, title, opts));

    if (!newWindow) return false;

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.document.title = title;
        newWindow.focus();
    }
    return true;
};
