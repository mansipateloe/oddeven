(function () {
    'use strict';
    var paths = {
        add: 'M12 5v14M5 12h14',
        save: 'M5 3h12l4 4v14H3V3h2Zm2 0v6h10V3M7 21v-8h10v8',
        edit: 'm16 3 5 5-12 12-6 1 1-6L16 3ZM14 5l5 5',
        cancel: 'm6 6 12 12M6 18 18 6',
        back: 'm12 5-7 7 7 7M5 12h15',
        search: 'M21 21l-6-6M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0',
        delete: 'M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7M14 10v7',
        download: 'M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5',
        send: 'm3 3 19 9-19 9 4-9-4-9Zm4 9h15',
        view: 'M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Zm13 0a3 3 0 1 1-6 0 3 3 0 0 1 6 0'
    };
    function actionFor(text) {
        if (/^(cancel|reset|clear|close)\b/i.test(text)) return 'cancel';
        if (/^(back|return)\b/i.test(text)) return 'back';
        if (/^(add|new|create)\b/i.test(text)) return 'add';
        if (/^(save|update)\b/i.test(text)) return 'save';
        if (/^edit\b/i.test(text)) return 'edit';
        if (/^(delete|remove)\b/i.test(text)) return 'delete';
        if (/^(search|filter|apply filter)\b/i.test(text)) return 'search';
        if (/^(download|export)\b/i.test(text)) return 'download';
        if (/^(submit|send|apply leave)\b/i.test(text)) return 'send';
        if (/^(view|details)\b/i.test(text)) return 'view';
        return '';
    }
    function enhance(root) {
        root.querySelectorAll('.input-group-addon').forEach(function (addon) {
            if (addon.textContent.trim() || addon.querySelector('input, button, a') || !addon.querySelector('.fa, .glyphicon')) return;
            // Keep interactive calendar controls and meaningful units visible.
            if (addon.matches('[onclick], [data-toggle]') || addon.querySelector('.fa-calendar, .fa-calendar-o, .fa-rupee, .fa-dollar')) return;
            addon.classList.add('crm-decorative-addon');
        });
        root.querySelectorAll('form.mini-form .form-control').forEach(function (field) {
            if (field.labels && field.labels.length || field.closest('.crm-field, .oecrm-select-search, .select2-search')) return;
            var caption = field.getAttribute('placeholder') || (field.name || '').replace(/\[\]|_id$/g, '').replace(/_/g, ' ');
            if (!caption) return;
            var holder = field.closest('.oecrm-select') || field;
            var wrapper = document.createElement('div');
            wrapper.className = 'crm-field';
            var label = document.createElement('label');
            label.textContent = caption.charAt(0).toUpperCase() + caption.slice(1);
            holder.parentNode.insertBefore(wrapper, holder);
            wrapper.appendChild(label);
            wrapper.appendChild(holder);
        });
        root.querySelectorAll('.btn, .icon-action, td a:has(> .fa), td button:has(> .fa)').forEach(function (button) {
            if (button.closest('.note-editor, .letter-preview, .invoice-print, .salary_report, [data-crm-ui="off"]')) return;
            var text = (button.tagName === 'INPUT' ? button.value : button.textContent).trim();
            var action = actionFor(text);
            if (action) {
                var white = action !== 'cancel' && button.matches('.btn-primary, .btn-success, .btn-danger, .btn-warning, .btn-info');
                var svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="' + (white ? '#ffffff' : '#526174') + '" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="' + paths[action] + '"/></svg>';
                button.classList.add('crm-action');
                button.dataset.crmAction = action;
                button.style.setProperty('--crm-action-icon', 'url("data:image/svg+xml,' + encodeURIComponent(svg) + '")');
            } else if (!text && button.querySelector('.fa, .glyphicon')) {
                button.classList.add('crm-icon-action');
                var icon = button.querySelector('.fa, .glyphicon');
                var label = button.getAttribute('aria-label') || button.title;
                if (!label) {
                    var names = { 'fa-pencil': 'Edit', 'fa-edit': 'Edit', 'fa-trash-o': 'Delete', 'fa-trash': 'Delete', 'fa-eye': 'View', 'fa-download': 'Download', 'fa-times': 'Close', 'fa-plus': 'Add' };
                    Object.keys(names).some(function (name) { if (!icon.classList.contains(name)) return false; label = names[name]; return true; });
                }
                if (label) { button.setAttribute('aria-label', label); button.title = label; }
            }
        });
        root.querySelectorAll('form label').forEach(function (label) {
            if (!label.textContent.trim()) return;
            var group = label.closest('.form-group, .crm-field') || label.parentElement;
            if (label.control && group.contains(label.control)) return;
            var fields = Array.from(group.querySelectorAll('input:not([type="hidden"]):not([type="submit"]):not([type="reset"]), select, textarea')).filter(function (field) {
                return !field.closest('.oecrm-select-search, .select2-search');
            });
            if (fields.length !== 1) return;
            var field = fields[0];
            if (!field.id) {
                do { field.id = 'crm-field-' + (++enhance.fieldId); } while (document.querySelectorAll('[id="' + field.id + '"]').length > 1);
            }
            label.htmlFor = field.id;
        });
        root.querySelectorAll('form').forEach(function (form) {
            var columns = Array.from(form.children).filter(function (child) { return /\bcol-(lg|md|sm)-\d+\b/.test(child.className); });
            // Only compact master forms with direct Bootstrap columns; leave nested editors intact.
            if (columns.length < 2 || columns.length > 6 || !columns.some(function (column) { return column.querySelector('.form-control'); })) return;
            form.classList.add('crm-inline-master-form');
            columns.forEach(function (column) {
                if (column.querySelector('.btn') && !column.querySelector('.form-control')) column.classList.add('crm-action-column');
            });
        });
    }
    enhance.fieldId = 0;
    function syncHeaderTitle() {
        var content = document.querySelector('#page-wrapper, #main-content');
        if (!content) return;
        var candidates = content.querySelectorAll('[data-crm-page-title], h1, h2, h3, h4, .page-header, .panel-heading');
        var hasPageTitle = Array.from(candidates).some(function (heading) {
            if (heading.closest('form, table, .modal, .analytics-card, .topboxes, .employee-summary-card, .employee-section-heading, .continuation-card-head, .note-editor')) return false;
            if (!heading.textContent.trim() || !heading.getClientRects().length) return false;
            var style = window.getComputedStyle(heading);
            return style.display !== 'none' && style.visibility !== 'hidden';
        });
        document.body.classList.toggle('crm-page-has-title', hasPageTitle);
    }
    function start() {
        if (!document.body.matches('.admin-portal, .employee-portal, .lead-portal')) return;
        document.body.classList.add('crm-ui');
        var roots = document.querySelectorAll('#page-wrapper, #main-content, .modal');
        roots.forEach(enhance);
        syncHeaderTitle();
        // Delegated enhancement handles table redraws, new form rows and edit/add toggles.
        var pending = false;
        new MutationObserver(function (mutations) {
            if (pending || !mutations.some(function (mutation) { return mutation.type === 'childList' || mutation.attributeName === 'value'; })) return;
            pending = true;
            window.requestAnimationFrame(function () { roots.forEach(enhance); syncHeaderTitle(); pending = false; });
        }).observe(document.querySelector('#page-wrapper, #main-content') || document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['value'] });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start); else start();
})();
