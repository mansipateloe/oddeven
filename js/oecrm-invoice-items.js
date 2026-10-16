(function () {
    'use strict';
    document.addEventListener('DOMContentLoaded', function () {
        var items = document.getElementById('perticulars_div');
        var template = document.getElementById('invoice-item-template');
        var add = document.getElementById('add_more');
        if (!items || !template || !add) return;
        var refreshing = false;
        var refresh = document.getElementById('refresh-invoice-banks');
        var feedback = document.getElementById('invoice-bank-feedback');
        function refreshBanks(showResult) {
            if (refreshing) return;
            refreshing = true;
            if (refresh) refresh.disabled = true;
            fetch('getInvoiceBanks.php', {headers: {'X-Requested-With': 'XMLHttpRequest'}})
                .then(function (response) { if (!response.ok) throw new Error('Bank refresh failed'); return response.json(); })
                .then(function (data) {
                    if (typeof data.options_html !== 'string') throw new Error('Invalid bank list');
                    var selects = Array.from(items.querySelectorAll('select[name="bank_id[]"]'));
                    selects.push(template.content.querySelector('select[name="bank_id[]"]'));
                    selects.forEach(function (select) {
                        var selected = select.value;
                        select.innerHTML = data.options_html;
                        select.value = selected;
                        if (select.selectedIndex < 0) select.value = '';
                        select.dispatchEvent(new Event('change', {bubbles: true}));
                    });
                    if (showResult && feedback) feedback.textContent = 'Bank accounts refreshed.';
                }).catch(function () {
                    if (showResult && feedback) feedback.textContent = 'Could not refresh banks. Please reload and try again.';
                }).finally(function () { refreshing = false; if (refresh) refresh.disabled = false; });
        }
        if (refresh) refresh.addEventListener('click', function () { refreshBanks(true); });
        window.addEventListener('focus', function () { refreshBanks(false); });
        add.addEventListener('click', function () {
            var fragment = template.content.cloneNode(true);
            var row = fragment.querySelector('.invoice-item-row');
            items.appendChild(fragment);
            if (window.OECRM_UI) window.OECRM_UI.refreshSelects(row);
            row.querySelector('input[name="perticulars[]"]').focus();
        });
        items.addEventListener('click', function (event) {
            var remove = event.target.closest('.remove');
            if (remove) remove.closest('.invoice-item-row').remove();
        });
    });
}());