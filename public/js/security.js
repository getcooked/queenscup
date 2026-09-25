(function () {
    'use strict';

    // Rebuild chat formatting using only inert elements and text. Never copy
    // attributes, URLs or event handlers from a reply into the live document.
    function appendChatMarkup(target, value) {
        var parsed = new DOMParser().parseFromString(String(value || ''), 'text/html');
        function copy(source, destination) {
            Array.from(source.childNodes).forEach(function (node) {
                if (node.nodeType === 3) {
                    destination.appendChild(document.createTextNode(node.textContent));
                } else if (node.nodeType === 1) {
                    if (['SCRIPT', 'STYLE', 'IFRAME', 'OBJECT', 'SVG', 'MATH', 'TEMPLATE'].indexOf(node.tagName) !== -1) return;
                    if (['BR', 'B', 'STRONG', 'EM', 'I'].indexOf(node.tagName) !== -1) {
                        var safe = document.createElement(node.tagName.toLowerCase());
                        copy(node, safe);
                        destination.appendChild(safe);
                    } else {
                        copy(node, destination);
                    }
                }
            });
        }
        copy(parsed.body, target);
    }

    window.QueenSecurity = { appendChatMarkup: appendChatMarkup };
}());
