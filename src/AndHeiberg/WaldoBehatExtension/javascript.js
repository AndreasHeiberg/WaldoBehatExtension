// disable CSS animations as they will cause false positives for visual regression
(function() {
    var disableAnimation = '-webkit-transition: none !important;' +
        '-moz-transition: none !important;' +
        '-ms-transition: none !important;' +
        '-o-transition: none !important;' +
        'transition: none !important;';

    var styles = document.createElement('style');
    styles.type = 'text/css';
    styles.innerHTML = '*, *:before, *:after { '+disableAnimation+' } .sf-toolbar { display: none !important; }';
    document.head.appendChild(styles);
})();

// keep track of running AJAX requests
(function(xhr) {
    xhr.active = 0;
    var pt = xhr.prototype;
    var _send = pt.send;
    pt.send = function() {
        xhr.active++;
        this.addEventListener('readystatechange', function(e) {
            if ( this.readyState == 4 ) {
                setTimeout(function() {
                    xhraactive--;
                }, 1);
            }
        });
        _send.apply(this, arguments);
    }
})(XMLHttpRequest);

// only take screenshot when all images are loaded
(function() {
    var script = document.createElement('script');
    script.setAttribute('src', 'https://unpkg.com/imagesloaded@4.1/imagesloaded.pkgd.min.js');
    script.onload = function() {
        imagesLoaded('body', function() {
            window._behat_images_loaded = true;
        });
    };
    document.body.appendChild(script);
})();
