var UPageUtility = {};

(function() {
    'use strict';

    UPageUtility.downloadByteArrayInline = function downloadByteArrayInline(url, withCredentials, numberOfTries, callback) {

        var xhr = new window.XMLHttpRequest();

        function onError(e) {
            if (numberOfTries >= 2) {
                callback(UPageUtility.createError({
                    url: url,
                    withCredentials: withCredentials,
                    numberOfTries: numberOfTries,
                    event: e
                }));
            } else {
                setTimeout(function () {
                    downloadByteArrayInline(url, withCredentials, numberOfTries + 1, callback);
                }, 50);
            }
        }

        xhr.withCredentials = !!withCredentials;
        xhr.open("GET", url, true);
        xhr.responseType = "arraybuffer";
        xhr.onload = function () {
            if (xhr.readyState !== 4 || xhr.status !== 200) {
                onError(xhr);
            } else {
                var array = xhr.response ? new window.Uint8Array(xhr.response) : new window.Uint8Array("");
                callback(null, array);
            }
        };
        xhr.onerror = onError;
        xhr.send();
    };


    UPageUtility.createError = function createError(obj) {
        return new Error(JSON.stringify(obj, null, 4));
    };


    UPageUtility.showError = function showError(error) {
        if (!error) {
            return;
        }
        console.error(error);
    };


    UPageUtility.isBase64String = function isBase64String(str) {
        return str.indexOf(';base64,') !== -1;
    };
})();