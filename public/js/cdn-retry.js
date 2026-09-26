(function () {
    'use strict';

    /*
     * Hostinger's CDN sometimes answers with its own static 403/404 page
     * instead of passing the request on to Laravel. Laravel never answers a
     * request that asked for JSON with an HTML page, so that combination means
     * the request never reached the app and is safe to send again. Without
     * this, a sign-in or an order fails with "could not reach the server"
     * roughly one time in three while the CDN is misbehaving.
     */
    if (!window.fetch || window.fetch.qcRetry) return;

    var nativeFetch = window.fetch.bind(window);
    var DELAYS = [300, 900];

    function acceptHeader(input, init) {
        var headers = (init && init.headers) || (typeof Request !== 'undefined' && input instanceof Request ? input.headers : null);
        if (!headers) return '';
        if (typeof Headers !== 'undefined' && headers instanceof Headers) return headers.get('Accept') || '';
        return headers.Accept || headers.accept || '';
    }

    function isSameOrigin(input) {
        var url = typeof input === 'string' ? input : (input && input.url) || '';
        try {
            return new URL(url, window.location.href).origin === window.location.origin;
        } catch (error) {
            return false;
        }
    }

    function isCdnErrorPage(response) {
        return (response.status === 403 || response.status === 404)
            && /text\/html/i.test(response.headers.get('Content-Type') || '');
    }

    function retryingFetch(input, init) {
        if (!isSameOrigin(input) || !/application\/json/i.test(acceptHeader(input, init))) {
            return nativeFetch(input, init);
        }

        var attempt = 0;

        function send() {
            return nativeFetch(input, init).then(function (response) {
                if (attempt >= DELAYS.length || !isCdnErrorPage(response)) return response;

                var wait = DELAYS[attempt++];
                return new Promise(function (resolve) { setTimeout(resolve, wait); }).then(send);
            });
        }

        return send();
    }

    retryingFetch.qcRetry = true;
    window.fetch = retryingFetch;
}());
