(function () {
    'use strict';
    function clearPreviousFeedback() {
        document.querySelectorAll('[data-master-feedback]').forEach(function (message) { message.remove(); });
    }
    document.addEventListener('input', clearPreviousFeedback);
    document.addEventListener('reset', clearPreviousFeedback);
    document.addEventListener('submit', clearPreviousFeedback);
    window.setTimeout(clearPreviousFeedback, 8000);
}());