(function () {
    'use strict';

    var confirmModal;
    var confirmMessage;
    var alertModal;
    var alertMessage;
    var pendingLink = '';
    var pendingForm = null;

    function show(element) {
        if (element) element.style.display = 'flex';
    }

    function hide(element) {
        if (element) element.style.display = 'none';
    }

    function appendCsrf(url) {
        var token = window.OECRM_CSRF_TOKEN || '';
        if (!url || !token || url.indexOf('csrf_token=') !== -1) return url;
        return url + (url.indexOf('?') === -1 ? '?' : '&') + 'csrf_token=' + encodeURIComponent(token);
    }

    function extractMessage(source) {
        var match = String(source || '').match(/confirm\s*\(\s*(['"])(.*?)\1\s*\)/i);
        return match ? match[2] : 'Are you sure you want to continue?';
    }

    function preparePage() {
        var token = window.OECRM_CSRF_TOKEN || '';
        document.querySelectorAll('form').forEach(function (form) {
            if ((form.method || '').toLowerCase() === 'post' && token && !form.querySelector('input[name="csrf_token"]')) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'csrf_token';
                input.value = token;
                form.appendChild(input);
            }
            var handler = form.getAttribute('onsubmit');
            if (handler && /^\s*return\s+confirm\s*\(/i.test(handler)) {
                form.dataset.confirm = extractMessage(handler);
                form.removeAttribute('onsubmit');
            }
        });

        document.querySelectorAll('a[href]').forEach(function (link) {
            var href = link.getAttribute('href') || '';
            if (/delete|_delete|delete[A-Za-z]+\.php|process\/projectexpense\.php/i.test(href)) {
                link.setAttribute('href', appendCsrf(href));
                if (!link.dataset.confirm) link.dataset.confirm = 'Are you sure you want to continue?';
            }
            var handler = link.getAttribute('onclick');
            if (handler && /^\s*return\s+confirm\s*\(/i.test(handler)) {
                link.dataset.confirm = extractMessage(handler);
                link.removeAttribute('onclick');
            }
        });
    }

    function openConfirmation(message, link, form) {
        pendingLink = link || '';
        pendingForm = form || null;
        confirmMessage.textContent = message || 'Are you sure you want to continue?';
        show(confirmModal);
    }

    function resetConfirmation() {
        pendingLink = '';
        pendingForm = null;
        hide(confirmModal);
    }

    document.addEventListener('DOMContentLoaded', function () {
        confirmModal = document.getElementById('oecrmConfirmModal');
        confirmMessage = document.getElementById('oecrmConfirmMessage');
        alertModal = document.getElementById('oecrmAlertModal');
        alertMessage = document.getElementById('oecrmAlertMessage');
        preparePage();

        document.addEventListener('click', function (event) {
            var trigger = event.target.closest ? event.target.closest('a[data-confirm]') : null;
            if (!trigger) return;
            event.preventDefault();
            openConfirmation(trigger.dataset.confirm, trigger.getAttribute('href'), null);
        });

        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!form.dataset.confirm || form.dataset.confirmBypass === '1') return;
            event.preventDefault();
            openConfirmation(form.dataset.confirm, '', form);
        });

        document.getElementById('oecrmConfirmOk').addEventListener('click', function () {
            var link = pendingLink;
            var form = pendingForm;
            resetConfirmation();
            if (form) {
                form.dataset.confirmBypass = '1';
                form.submit();
            } else if (link) {
                window.location.href = link;
            }
        });

        document.querySelectorAll('[data-dialog-close]').forEach(function (button) {
            button.addEventListener('click', function () {
                hide(document.getElementById(button.dataset.dialogClose));
                pendingLink = '';
                pendingForm = null;
            });
        });

        [confirmModal, alertModal].forEach(function (modal) {
            if (!modal) return;
            modal.addEventListener('click', function (event) {
                if (event.target === modal) hide(modal);
            });
        });
    });

    window.alert = function (message) {
        if (!alertModal || !alertMessage) return;
        alertMessage.textContent = String(message || '');
        show(alertModal);
    };
}());
