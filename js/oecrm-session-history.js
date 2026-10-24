(function () {
    'use strict';

    // Revalidate the session when restoring a protected page from browser history.
    window.addEventListener('pagehide', function () {
        document.documentElement.style.visibility = 'hidden';
    });
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            window.location.reload();
            return;
        }
        document.documentElement.style.visibility = '';
    });
}());
