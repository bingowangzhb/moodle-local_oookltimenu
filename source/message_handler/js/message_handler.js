/*
 * Compatibility shim for legacy message handler path.
 *
 * Some launched pages may reference:
 *   /local/oookltimenu/source/message_handler/js/message_handler.js
 * We intentionally keep this file lightweight to avoid 404 noise.
 */
(function() {
    if (window.localOookLtiMenuMessageHandlerLoaded) {
        return;
    }
    window.localOookLtiMenuMessageHandlerLoaded = true;
})();
