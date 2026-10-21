(function () {
    'use strict';
    function init() {
        var form = document.getElementById('collection-form');
        if (!form) return;
        var project = document.getElementById('collection-project');
        var invoice = document.getElementById('collection-invoice');
        var account = document.getElementById('collection-account');
        var amount = document.getElementById('collection-amount');
        var balance = document.getElementById('collection-bank-balance');
        var client = document.getElementById('collection-client');
        var date = document.getElementById('collection-invoice-date');
        var hint = document.getElementById('collection-bank-hint');
        var message = document.getElementById('collection-message');
        var save = document.getElementById('collection-save');
        var invoices = [], request = null, ready = false;
        var initial = JSON.parse(document.getElementById('collection-initial').textContent);
        function changed(select) { select.dispatchEvent(new Event('change', {bubbles: true})); }
        function showBalance() {
            if (!balance) return;
            var option = account.selectedOptions[0];
            if (!account.value || !option || option.dataset.balance === undefined) {
                balance.textContent = '';
                return;
            }
            // Work in cents and reverse the old deposit when editing the same bank.
            var current = Math.round(Number(option.dataset.balance) * 100);
            var credit = Math.round((Number(amount.value) || 0) * 100);
            var original = Number(initial.original_account_id) > 0 && Number(option.dataset.accountId) === Number(initial.original_account_id)
                ? Math.round((Number(initial.original_amount) || 0) * 100) : 0;
            function money(cents) { return (cents / 100).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }
            balance.textContent = 'Current Balance: ' + money(current);
            if (amount.value) balance.textContent += ' | Balance After ' + (Number(form.querySelector('[name="id"]').value) > 0 ? 'Update' : 'Deposit') + ': ' + money(current - original + credit);
        }
        account.addEventListener('change', showBalance);
        function details(keepAccount) {
            var selected = invoices.find(function (row) { return String(row.invoice_id) === invoice.value; });
            amount.value = selected ? selected.total_amount : '';
            client.value = selected ? selected.person_name : '';
            date.value = selected ? selected.invoice_date : '';
            hint.textContent = selected && selected.banks.length ? 'Invoice bank account(s): ' + selected.banks.map(function (bank) { return bank.account_name; }).join(', ') : 'Select the bank account that receives this payment.';
            if (!keepAccount) {
                account.value = selected && selected.suggested_account_id ? String(selected.suggested_account_id) : '';
                changed(account);
            }
            showBalance();
            save.disabled = !ready || !selected;
        }
        function options(rows, selectedId, keepAccount) {
            invoices = rows;
            invoice.replaceChildren(new Option(rows.length ? 'Select Invoice' : project.value ? 'No unpaid invoices available' : 'Select Project First', ''));
            rows.forEach(function (row) {
                var label = row.invoice_no + ' | ' + row.invoice_date + ' | ' + row.total_amount;
                invoice.add(new Option(label, String(row.invoice_id)));
            });
            invoice.value = selectedId ? String(selectedId) : '';
            changed(invoice);
            details(keepAccount);
        }
        invoice.addEventListener('change', function () { details(false); });
        project.addEventListener('change', function () {
            if (request) request.abort();
            ready = false;
            options([], 0, false);
            if (!project.value) { message.textContent = ''; return; }
            var selected = project.value;
            request = new AbortController();
            message.textContent = 'Loading invoices...';
            var body = new URLSearchParams({proj_id: selected, deposit_id: form.querySelector('[name="id"]').value});
            fetch('getInvoices.php', {method: 'POST', credentials: 'same-origin', body: body, signal: request.signal})
                .then(function (response) { if (!response.ok) throw new Error('Lookup failed'); return response.json(); })
                .then(function (response) {
                    if (project.value !== selected) return;
                    ready = true;
                    options(response.data || [], 0, false);
                    message.textContent = invoices.length ? 'Select the invoice being collected.' : 'No unpaid invoices are available for this project.';
                })
                .catch(function (error) {
                    if (error.name === 'AbortError') return;
                    message.textContent = 'Could not load invoices. Select the project again to retry.';
                });
        });
        form.addEventListener('submit', function (event) {
            if (!ready || !amount.value || !invoice.value || !account.value) {
                event.preventDefault();
                message.textContent = 'Select a loaded invoice and receiving bank account before saving.';
            }
        });
        ready = true;
        // Keep the bank already credited when opening an existing deposit.
        var existingAccount = account.value;
        options(initial.invoices || [], initial.invoice_id, true);
        if (Number(form.querySelector('[name="id"]').value) > 0) {
            account.value = existingAccount;
            changed(account);
        }
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
}());