(function () {
    'use strict';

    function ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            callback();
        }
    }

    function currentFile() {
        var file = window.location.pathname.split('/').pop();
        return file || 'dashboard.php';
    }

    function initialiseSidebar() {
        var sidebar = document.querySelector('.sidebar');
        var menu = document.getElementById('side-menu');
        if (!sidebar || !menu) return;

        var current = currentFile().toLowerCase();
        var links = menu.querySelectorAll('a[href]');
        var activeLink = null;
        Array.prototype.forEach.call(links, function (link) {
            var href = (link.getAttribute('href') || '').split('?')[0].toLowerCase();
            if (href && href !== '#' && href === current) activeLink = link;
        });

        var submenus = menu.querySelectorAll('.nav-second-level');
        Array.prototype.forEach.call(submenus, function (submenu) {
            submenu.style.display = 'none';
            submenu.parentElement.classList.remove('open', 'active');
        });

        if (activeLink) {
            activeLink.classList.add('active');
            var parentSubmenu = activeLink.closest('.nav-second-level');
            if (parentSubmenu) {
                parentSubmenu.style.display = 'block';
                parentSubmenu.parentElement.classList.add('open', 'active');
            }
        }

        Array.prototype.forEach.call(menu.querySelectorAll('li > a[href="#"]'), function (trigger) {
            trigger.addEventListener('click', function (event) {
                event.preventDefault();
                var item = trigger.parentElement;
                var submenu = item.querySelector(':scope > .nav-second-level');
                if (!submenu) return;
                var opening = submenu.style.display !== 'block';
                Array.prototype.forEach.call(menu.querySelectorAll(':scope > li > .nav-second-level'), function (other) {
                    if (other === submenu) return;
                    other.style.display = 'none';
                    other.parentElement.classList.remove('open', 'active');
                });
                submenu.style.display = opening ? 'block' : 'none';
                item.classList.toggle('open', opening);
                item.classList.toggle('active', opening);
            });
        });

        sidebar.scrollTop = 0;
        var scrollArea = sidebar.querySelector('.sidebar-nav');
        if (scrollArea) scrollArea.scrollTop = 0;
    }

    function selectedText(select) {
        var selected = Array.prototype.filter.call(select.options, function (option) {
            return option.selected && option.value !== '';
        });
        if (!selected.length) return select.getAttribute('data-placeholder') || (select.options[0] ? select.options[0].text : 'Select');
        if (select.multiple) return selected.length + ' selected';
        return selected[0].text;
    }

    function enhanceSelect(select) {
        if (select.dataset.oecrmNative === '1' || select.dataset.oecrmEnhanced === '1' || select.disabled || select.size > 1 && !select.multiple) return;
        if (select.closest('.dataTables_length')) return;
        select.dataset.oecrmEnhanced = '1';
        select.classList.add('oecrm-native-select');

        var wrapper = document.createElement('div');
        wrapper.className = 'oecrm-select' + (select.multiple ? ' is-multiple' : '');
        select.parentNode.insertBefore(wrapper, select);
        wrapper.appendChild(select);

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'oecrm-select-toggle';
        button.innerHTML = '<span></span><i class="fa fa-chevron-down"></i>';
        wrapper.appendChild(button);

        var panel = document.createElement('div');
        panel.className = 'oecrm-select-panel';
        panel.innerHTML = '<div class="oecrm-select-search"><i class="fa fa-search"></i><input type="search" autocomplete="off" placeholder="Search..."></div><div class="oecrm-select-options"></div>';
        wrapper.appendChild(panel);
        var label = button.querySelector('span');
        var search = panel.querySelector('input');
        var optionsBox = panel.querySelector('.oecrm-select-options');

        function syncLabel() {
            label.textContent = selectedText(select);
            button.classList.toggle('has-value', select.value !== '' || select.multiple && select.selectedOptions.length > 0);
        }

        function render(filter) {
            optionsBox.innerHTML = '';
            var needle = (filter || '').toLowerCase();
            Array.prototype.forEach.call(select.options, function (option) {
                if (option.hidden) return;
                if (option.disabled && option.value === '') return;
                if (needle && option.text.toLowerCase().indexOf(needle) === -1) return;
                var row = document.createElement('button');
                row.type = 'button';
                row.className = 'oecrm-select-option' + (option.selected ? ' selected' : '');
                row.innerHTML = (select.multiple ? '<i class="fa ' + (option.selected ? 'fa-check-square' : 'fa-square-o') + '"></i>' : '') + '<span></span>';
                row.querySelector('span').textContent = option.text;
                row.addEventListener('click', function () {
                    if (select.multiple) {
                        option.selected = !option.selected;
                        render(search.value);
                    } else {
                        select.value = option.value;
                        wrapper.classList.remove('open');
                    }
                    syncLabel();
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                });
                optionsBox.appendChild(row);
            });
            if (!optionsBox.children.length) optionsBox.innerHTML = '<div class="oecrm-select-empty">No matching options</div>';
        }

        button.addEventListener('click', function () {
            document.querySelectorAll('.oecrm-select.open').forEach(function (open) {
                if (open !== wrapper) open.classList.remove('open');
            });
            wrapper.classList.toggle('open');
            if (wrapper.classList.contains('open')) {
                search.value = '';
                render('');
                window.setTimeout(function () { search.focus(); }, 10);
            }
        });
        search.addEventListener('input', function () { render(search.value); });
        select.addEventListener('change', syncLabel);
        new MutationObserver(function () {
            syncLabel();
            if (wrapper.classList.contains('open')) render(search.value);
        }).observe(select, { childList: true, subtree: true, attributes: true });
        syncLabel();
    }

    function initialiseSelects(scope) {
        Array.prototype.forEach.call((scope || document).querySelectorAll('select.form-control, select[data-oecrm-select]'), enhanceSelect);
    }

    function initialiseTables() {
        if (!window.jQuery || !jQuery.fn || !jQuery.fn.DataTable) return;
        jQuery('table.no-datatable').each(function () {
            if (jQuery.fn.DataTable.isDataTable(this)) {
                jQuery(this).DataTable().destroy();
            }
        });
        jQuery('table.table').each(function () {
            var table = jQuery(this);
            if (table.hasClass('no-datatable') || table.closest('.letter-editor').length || jQuery.fn.DataTable.isDataTable(this)) return;
            var rows = table.find('tbody > tr');
            if (!rows.length || rows.length === 1 && rows.first().find('td[colspan]').length) return;
            table.DataTable({
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [],
                autoWidth: false,
                responsive: true,
                language: {
                    search: '',
                    searchPlaceholder: 'Search records...',
                    lengthMenu: 'Show _MENU_'
                }
            });
        });
    }

    ready(function () {
        if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
        window.scrollTo(0, 0);
        initialiseSidebar();
        initialiseSelects(document);
        initialiseTables();
        window.OECRM_UI = { refreshSelects: initialiseSelects, refreshTables: initialiseTables };
    });

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.oecrm-select')) {
            document.querySelectorAll('.oecrm-select.open').forEach(function (select) { select.classList.remove('open'); });
        }
    });
}());
