(function () {
    'use strict';
    function initialize() {
        var section = document.getElementById('invoice-tax-section');
        if (!section) return;
        var form = section.closest('form');
        var project = form.querySelector('[name="project_Id"]');
        var mode = document.getElementById('invoice-tax-mode');
        var message = document.getElementById('invoice-tax-message');
        var person = form.querySelector('[name="person_name"]');
        var hasSavedGst = section.dataset.savedGst === '1';
        var eligible = hasSavedGst;
        var lookupError = '';
        var request = null;
        var loadedProject = '';
        var types = ['cgst', 'sgst', 'igst'];
        var round = function (n) { return Math.round((n + Number.EPSILON) * 100) / 100; };
        function calculate() {
            var subtotal = 0, tax = 0;
            form.querySelectorAll('[name="amounts[]"]').forEach(function (input) {
                subtotal += Number(input.value) || 0;
            });
            subtotal = round(subtotal);
            types.forEach(function (type) {
                var active = eligible && (mode.value === 'igst' ? type === 'igst' : mode.value === 'cgst_sgst' && type !== 'igst');
                var input = document.getElementById('invoice-' + type);
                input.disabled = !active;
                input.closest('.invoice-tax-rate').style.display = active ? '' : 'none';
                if (active) tax += round(subtotal * (Number(input.value) || 0) / 100);
            });
            var cgst = document.getElementById('invoice-cgst');
            var sgst = document.getElementById('invoice-sgst');
            sgst.setCustomValidity(eligible && mode.value === 'cgst_sgst' && Number(cgst.value) !== Number(sgst.value) ? 'CGST and SGST rates must match.' : '');
            document.getElementById('invoice-subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('invoice-tax-summary').style.display = eligible && mode.value !== 'none' ? '' : 'none';
            document.getElementById('invoice-tax-total').textContent = round(tax).toFixed(2);
            document.getElementById('invoice-grand-total').textContent = round(subtotal + tax).toFixed(2);
        }
        function resetTaxes() {
            mode.value = 'cgst_sgst';
            types.forEach(function (type) { document.getElementById('invoice-' + type).value = '0'; });
        }
        function setClient(data, preserveTax) {
            if (preserveTax && hasSavedGst && data.is_indian_client !== true) {
                // Show the saved tax instead of silently removing it from an old invoice.
                loadedProject = '';
                lookupError = 'Saved GST is displayed. Check the linked client country in Clients, then reload before saving.';
                message.textContent = lookupError;
                calculate();
                return;
            }
            eligible = data.is_indian_client === true;
            section.style.display = eligible ? '' : 'none';
            mode.disabled = !eligible;
            if (!eligible) resetTaxes();
            message.textContent = eligible ? 'Indian client: select the GST type and enter the applicable rates.' :
                data.client_country ? 'Client country: ' + data.client_country + '. GST fields are not required.' :
                'The linked client has no country. Set the country in Clients to enable GST options.';
            if (person && !preserveTax) person.value = data.customerName || '';
            calculate();
        }
        function loadClient(preserveTax) {
            loadedProject = '';
            lookupError = '';
            if (request) request.abort();
            if (!preserveTax) resetTaxes();
            eligible = preserveTax && hasSavedGst;
            section.style.display = eligible ? '' : 'none';
            mode.disabled = !eligible;
            calculate();
            if (!project.value) {
                message.textContent = 'Select a project to load its client and tax options.';
                return;
            }
            request = new AbortController();
            var selected = project.value;
            message.textContent = 'Loading client details...';
            var body = new URLSearchParams({proj_id: selected});
            var csrf = form.querySelector('[name="csrf_token"]');
            if (csrf) body.set('csrf_token', csrf.value);
            fetch('getPerson.php', {method: 'POST', credentials: 'same-origin', body: body, signal: request.signal})
                .then(function (response) {
                    if (!response.ok) throw new Error('Client lookup failed');
                    return response.json();
                })
                .then(function (response) {
                    if (project.value === selected && response.data) {
                        loadedProject = selected;
                        setClient(response.data, preserveTax);
                    }
                })
                .catch(function (error) {
                    if (error.name === 'AbortError' || project.value !== selected) return;
                    if (!preserveTax) resetTaxes();
                    calculate();
                    lookupError = 'Could not load the linked client. Saved values have been kept. Reload or select the project again to retry.';
                    message.textContent = lookupError;
                });
        }
        form.addEventListener('submit', function (event) {
            if (project.value && loadedProject !== project.value) {
                event.preventDefault();
                message.textContent = lookupError || 'Please wait for client details to load, or select the project again to retry.';
            }
        });
        form.addEventListener('input', calculate);
        form.addEventListener('change', calculate);
        form.addEventListener('reset', function () { setTimeout(function () { loadClient(true); }, 0); });
        project.addEventListener('change', function () { loadClient(false); });
        var items = form.querySelector('#perticulars_div');
        if (items) new MutationObserver(calculate).observe(items, {childList: true, subtree: true});
        loadClient(true);
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize);
    else initialize();
}());