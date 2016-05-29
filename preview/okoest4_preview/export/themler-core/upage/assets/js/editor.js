/*global UPageUtility, DataUploader, upageSettings */

window.upageEditor = {};

(function() {
    'use strict';

    function addEvent(elem, name, handler){
        if (elem.addEventListener){
            elem.addEventListener(name, handler, false);
        } else {
            elem.attachEvent(name, handler);
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        addEvent(document.body, 'upageClose', window.upageEditor.save);
    });
})();

window.upageEditor.save = function save() {
    'use strict';

    var win = window.parent || window;

    win.postMessage(JSON.stringify({
        message: 'savingStart'
    }), window.location.origin);

    var sections = new UpageSectionsCollection();

    $('body').children(':not(style):not(.editor-container)').each(function(i, el) {
        var sectionElement = $(el);

        if (sectionElement.length === 0) {
            return;
        }

        var sectionStyle = sectionElement.prev();
        sections.push(new UpageSection(sectionStyle,  sectionElement));
    });

    sections.synchronize(function(error) {
        UPageUtility.showError(error);

        win.postMessage(JSON.stringify({
            message: 'savingEnd',
            hasChanges: sections.getChanged().length > 0
        }), window.location.origin);
    });
};


function UpageSectionsCollection() {
    'use strict';

    var all = [];
    var changed = [];

    this.push = function(section) {
        all.push(section);
        var thumbnail = section.dom.attr('data-thumbnail');
        if (!section.mediaId || UPageUtility.isBase64String(thumbnail)) {
            changed.push(section);
        }
    };

    this.getChanged = function() {
        return changed;
    };

    this.synchronize = function(callback) {

        if (changed.length === 0) {
            // there is no changes
            callback();
            return;
        }

        uploadImages(function() {

            var updateData = [];

            changed.forEach(function(section) {
                var html = section.html
                    .replace(/data-thumbnail="[^"]*"/g, '')
                    .replace(/data-media-id="[^"]*"/g, '');

                updateData.push({
                    name: section.name,
                    html: html,
                    mediaId: section.mediaId || 0,
                    thumbnail: section.thumbnail
                });
            });

            DataUploader.uploadSections(updateData, function (error, responce) {
                if (error) {
                    callback(error);
                    return;
                }

                var i;
                for (i = 0; i < changed.length; i++) {
                    var section = changed[i],
                        changes = responce.data[i];

                    section.updateMediaId(changes.section_id);
                }

                var shortcode = '';
                for (i = 0; i < all.length; i++) {
                    shortcode += '[upage_section id=' + all[i].mediaId + ']\n';
                }

                DataUploader.updatePost({
                    id: upageSettings.postId,
                    content: shortcode
                }, callback);
            });
        });
    };

    function uploadImages(callback) {
        var uploadRequired = {};

        changed.forEach(function(section) {
            var html = section.html;
            section.uploads = [];

            html.replace(/"(data:[^"]*?;base64,[^"]*)"/g, function(match, dataUrl) {
                uploadRequired[dataUrl] = null;
                section.uploads.push(dataUrl);
                return '';
            }).replace(/(https?:)?\/\/(\S+?)\.(png|jpg|gif|svg|ico|jpeg|bmp)/gi, function(url) {
                if (url.indexOf(location.protocol + '//' + location.host) !== 0) {
                    uploadRequired[url] = null;
                    section.uploads.push(url);
                }
                return '';
            });
        });

        var urls = Object.keys(uploadRequired);

        function upload(idx) {
            if (idx === urls.length) {

                changed.forEach(function(section) {
                    section.uploads.forEach(function(url) {
                        if (uploadRequired[url]) {
                            var newUrl = uploadRequired[url].url;
                            section.html = section.html.split(url).join(newUrl);
                        } else {
                            console.warn('Can\'t replace url ' + url);
                        }
                    });
                    var thumbnail = uploadRequired[section.dom.attr('data-thumbnail')];
                    section.updateDom();
                    section.thumbnail = thumbnail;
                });

                callback();
                return;
            }

            DataUploader.uploadImage({
                fileName: 'image.png',
                data: urls[idx]
            }, '0', function(error, data) {
                if (error) {
                    // show error and continue uploading
                    UPageUtility.showError(error);
                } else {
                    uploadRequired[urls[idx]] = {
                        url: data.image_url,
                        mediaId: data.upload_id
                    };
                }
                upload(idx + 1);
            });
        }
        upload(0);
    }
}

function UpageSection(style, dom) {
    'use strict';

    var _style, _dom;

    Object.defineProperty(this, 'style', {
        enumerable: true,
        configurable: true,
        get: function() {
            return _style;
        },
        set: function(value) {
            _style = value && value.is('style') ? value : $('<style></style>');
        }
    });

    Object.defineProperty(this, 'dom', {
        enumerable: true,
        configurable: true,
        get: function() {
            return _dom;
        },
        set: function(value) {
            _dom = value;
        }
    });

    this.style = style;
    this.dom = dom;

    this.html = style[0].outerHTML + '\n' + dom[0].outerHTML;
    this.name = dom.attr('data-section-title');
    this.mediaId = dom.attr('data-media-id');


    this.updateMediaId = function(id) {
        this.mediaId = id;
        this.dom.attr('data-media-id', id);
    };

    this.updateDom = function() {
        var newElements = $($.parseHTML(this.html));

        var oldStyle = this.style,
            oldDom = this.dom;
        this.style = newElements.filter('style');
        this.dom = newElements.filter(':not(style)');
        oldStyle.replaceWith(this.style);
        oldDom.replaceWith(this.dom);
    };
}