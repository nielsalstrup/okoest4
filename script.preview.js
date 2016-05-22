function getUrlParameter(name, url) {
    'use strict';

    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(url);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function addUrlParameter(url, key, value) {
    'use strict';

    return url + (url.indexOf('?') === -1 ? '?' : '&') + key + '=' + value;
}

jQuery(function ($) {
    'use strict';

    $(".carousel .left-button, .carousel .right-button").filter('a').attr("href", "#");

    $("a[href*=\'#038;\']").each(function(){
        var href = $(this).attr("href");
        href = href.replace("#038;","&");
        $(this).attr("href", href);
    });

    $('form').each(function() {
        var form = $(this);
        var action = form.attr('action');
        if (!action) {
            return;
        }
        var anchor = '',
            anchorPosition = action.indexOf('#');

        if (anchorPosition !== -1) {
            anchor = action.substring(anchorPosition);
            action = action.substring(0, anchorPosition);
        }
        ['preview', 'template', 'stylesheet', 'preview_iframe', 'theme', 'nonce', 'original', 'wp_customize'].forEach(function(attr) {
            var value = getUrlParameter(attr, location.href);
            if (value !== '' && getUrlParameter(attr, action) === '') {
                action = addUrlParameter(action, attr, value);
            }
        });
        form.attr('action', action + anchor);
    });
});