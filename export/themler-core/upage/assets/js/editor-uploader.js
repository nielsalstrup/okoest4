/*exported DataUploader */
/*global UPageUtility, upageSettings */

var DataUploader = (function() {
    'use strict';

    function updatePost(postData, callback) {
        doRequest(upageSettings.actions.updatePost, postData, callback);
    }

    function uploadImage(imageData, postId, callback) {

        var mimeType = imageData.mimeType;
        var fileName = imageData.fileName;
        var uInt8Array;

        if (imageData.data instanceof Uint8Array) {
            uInt8Array = imageData.data;
            upload();
            return;

        }

        if (UPageUtility.isBase64String(imageData.data)) {
            var parts = imageData.data.split(';base64,');
            mimeType = parts[0].split(':')[1];
            var raw = window.atob(parts[1]);
            var rawLength = raw.length;

            uInt8Array = new Uint8Array(rawLength);

            for (var i = 0; i < rawLength; i++) {
                uInt8Array[i] = raw.charCodeAt(i);
            }
            upload();
            return;
        }

        UPageUtility.downloadByteArrayInline(imageData.data, false, 1, function(error, array) {
            if (error) {
                callback(error);
                return;
            }
            uInt8Array = array;
            upload();
        });

        function upload() {
            var uploader = new ImageUploader(uInt8Array, $.extend(true, {
                url: upageSettings.actions.uploadImage,
                type: mimeType,
                fileName: fileName
            }, upageSettings.uploadImageOptions), callback);
            uploader.upload();
        }
    }

    function uploadSections(sections, callback) {
        doRequest(upageSettings.actions.uploadSections, {
            sections: sections
        }, callback);
    }

    function doRequest(url, data, onError, onSuccess) {
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: $.extend(true, upageSettings.ajaxData, data)
        }).done(function requestSuccess(response, status, xhr) {
            if (response.result === 'done') {
                if (typeof onSuccess === 'undefined') {
                    onError(null, response);
                } else {
                    onSuccess(response);
                }
                return;
            }
            onError(UPageUtility.createError(xhr));
        }).fail(function requestFail(xhr) {
            onError(UPageUtility.createError(xhr));
        });
    }

    return {
        updatePost: updatePost,
        uploadImage: uploadImage,
        uploadSections: uploadSections
    };
})();

function ImageUploader(byteArray, options, callback) {
    'use strict';

    var type = options.type || '';
    var file = new Blob([byteArray], { type: type });

    this.upload = function upload() {

        setTimeout(function () {
            var formData = new FormData();
            formData.append(options.formFileName, file, options.fileName);

            var params = options.params;
            if (typeof params === 'object') {
                for (var i in params) {
                    if (params.hasOwnProperty(i)) {
                        formData.append(i, params[i]);
                    }
                }
            }

            return $.ajax({
                url: options.url,
                data: formData,
                type: 'POST',
                mimeType: 'application/octet-stream',
                processData: false,
                contentType: false,
                headers: {},
                success: function(responce, status, xhr) {
                    var result;
                    try {
                        result = JSON.parse(responce);
                    } catch(e) {
                        callback(UPageUtility.createError(xhr));
                        return;
                    }
                    callback(null, result);
                }
            });

        }, 0);
    };
}