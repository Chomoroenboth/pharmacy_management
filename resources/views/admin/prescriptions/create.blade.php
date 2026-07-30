@extends('layouts.admin-app')

@section('title', 'New Prescription')

@section('page-css')
<style>
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
    .modal-box { background: #fff; border-radius: 8px; width: 100%; max-width: 820px; padding: 32px; max-height: 90vh; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h2 { font-size: 22px; }
    .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; text-decoration: none; color: #191c1e; }

    .form-row { display: flex; gap: 20px; margin-bottom: 18px; }
    .form-group { flex: 1; position: relative; }
    .form-group label { display: block; font-size: 12px; font-weight: 600; color: #3c4a42; letter-spacing: 0.02em; margin-bottom: 6px; }
    .form-group input, .form-group textarea {
        width: 100%; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; font-family: inherit;
    }
    .field-error { color: #ba1a1a; font-size: 12px; margin-top: 4px; }

    .autocomplete-list {
        position: absolute; top: 100%; left: 0; right: 0; background: #fff;
        border: 1px solid #d7d9dc; border-radius: 6px; max-height: 180px; overflow-y: auto;
        z-index: 10; display: none;
    }
    .autocomplete-list.open { display: block; }
    .autocomplete-item { padding: 8px 12px; font-size: 13px; cursor: pointer; }
    .autocomplete-item:hover { background: #f3f4f6; }

    .med-section-header { display: flex; justify-content: space-between; align-items: center; margin: 20px 0 12px; }
    .med-section-header h3 { font-size: 16px; }
    .add-row-link { color: #006c49; background: none; border: none; font-size: 13px; font-weight: 600; cursor: pointer; }

    .med-row { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; position: relative; }
    .med-row .med-name-wrap { flex: 1; position: relative; }
    .med-row input { padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; width: 100%; }
    .med-row input.dosage { flex: 0 0 110px; }
    .med-row input.qty { flex: 0 0 70px; }
    .med-row input.instructions { flex: 1; }
    .remove-row-btn { background: none; border: none; color: #ba1a1a; font-size: 16px; cursor: pointer; padding: 6px; }

    .modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f3f4f6; }
    .btn-cancel { background: #fff; border: 1px solid #d7d9dc; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; color: #191c1e; }
    .btn-save { background: #10b981; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
    .btn-save:disabled { opacity: 0.6; cursor: not-allowed; }
    .form-error-banner { background: #ffdad6; color: #930a0a; padding: 10px 14px; border-radius: 6px; font-size: 13px; margin-bottom: 16px; display: none; }
</style>
@endsection

@section('content')

    <div class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2>New Prescription</h2>
                <a href="{{ route('admin.prescriptions') }}" class="modal-close">&times;</a>
            </div>

            <div id="formErrorBanner" class="form-error-banner"></div>

            <form id="prescriptionForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>CUSTOMER</label>
                        <input type="text" id="customerSearch" placeholder="Search customer name or ID...">
                        <input type="hidden" id="customerId" name="user_id">
                        <div id="customerResults" class="autocomplete-list"></div>
                        <div class="field-error" id="customerError"></div>
                    </div>
                    <div class="form-group">
                        <label>PRESCRIBING DOCTOR</label>
                        <input type="text" id="doctorFullName" placeholder="Dr. First Last">
                        <div class="field-error" id="doctorError"></div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>DOCTOR LICENSE (optional)</label>
                        <input type="text" id="doctorLicense" placeholder="LIC-2023-0456">
                    </div>
                    <div class="form-group">
                        <label>CLINIC (optional)</label>
                        <input type="text" id="doctorClinic" placeholder="St. Jude Medical Center">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>DATE ISSUED</label>
                        <input type="date" id="issueDate">
                        <div class="field-error" id="issueDateError"></div>
                    </div>
                    <div class="form-group">
                        <label>VALID UNTIL</label>
                        <input type="date" id="expiryDate">
                    </div>
                </div>

                <div class="form-group">
                    <label>PRESCRIPTION NOTES</label>
                    <textarea id="notes" rows="2" placeholder="Any additional notes or warnings..."></textarea>
                </div>

                <div class="med-section-header">
                    <h3>Medicines</h3>
                    <button type="button" class="add-row-link" onclick="addMedRow()">+ Add Row</button>
                </div>

                <div id="medRows"></div>
                <div class="field-error" id="medicinesError"></div>

                <div class="modal-footer">
                    <a href="{{ route('admin.prescriptions') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-save" id="saveBtn">Save Prescription</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let medRowCount = 0;

        function makeMedRow() {
            medRowCount++;
            const rowId = medRowCount;
            const row = document.createElement('div');
            row.className = 'med-row';
            row.dataset.rowId = rowId;
            row.innerHTML = `
                <div class="med-name-wrap">
                    <input type="text" class="med-search" placeholder="Search medicine...">
                    <input type="hidden" class="med-id">
                    <div class="autocomplete-list med-results"></div>
                </div>
                <input type="text" class="dosage" placeholder="e.g. 500mg">
                <input type="number" class="qty" placeholder="0" min="1">
                <input type="text" class="instructions" placeholder="e.g. 1x Daily">
                <button type="button" class="remove-row-btn" onclick="removeMedRow(this)">&#128465;</button>
            `;
            return row;
        }

        function addMedRow() {
            const rows = document.getElementById('medRows');
            const row = makeMedRow();
            rows.appendChild(row);
            wireMedicineAutocomplete(row);
        }

        function removeMedRow(btn) {
            const rows = document.getElementById('medRows');
            if (rows.children.length > 1) {
                btn.closest('.med-row').remove();
            }
        }

        function debounce(fn, delay) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => fn(...args), delay);
            };
        }

        // --- Customer autocomplete ---
        const customerSearchEl = document.getElementById('customerSearch');
        const customerIdEl = document.getElementById('customerId');
        const customerResultsEl = document.getElementById('customerResults');

        const searchCustomers = debounce(async (query) => {
            if (!query) { customerResultsEl.classList.remove('open'); return; }
            try {
                const res = await window.axios.get('/api/staff/customers', { params: { search: query } });
                const customers = res.data.data || [];
                customerResultsEl.innerHTML = customers.map(c =>
                    `<div class="autocomplete-item" data-id="${c.user_id}" data-name="${c.first_name} ${c.last_name ?? ''}">
                        ${c.first_name} ${c.last_name ?? ''} — ${c.email}
                    </div>`
                ).join('');
                customerResultsEl.classList.toggle('open', customers.length > 0);
            } catch (err) {
                console.error('Customer search failed', err);
            }
        }, 300);

        customerSearchEl.addEventListener('input', (e) => {
            customerIdEl.value = '';
            searchCustomers(e.target.value);
        });

        customerResultsEl.addEventListener('click', (e) => {
            const item = e.target.closest('.autocomplete-item');
            if (!item) return;
            customerSearchEl.value = item.dataset.name;
            customerIdEl.value = item.dataset.id;
            customerResultsEl.classList.remove('open');
        });

        // --- Medicine autocomplete (per row) ---
        function wireMedicineAutocomplete(row) {
            const searchEl = row.querySelector('.med-search');
            const idEl = row.querySelector('.med-id');
            const resultsEl = row.querySelector('.med-results');

            const searchMedicines = debounce(async (query) => {
                if (!query) { resultsEl.classList.remove('open'); return; }
                try {
                    const res = await window.axios.get('/api/inventory/medicines', { params: { search: query, per_page: 8 } });
                    const meds = res.data.data || [];
                    resultsEl.innerHTML = meds.map(m =>
                        `<div class="autocomplete-item" data-id="${m.medicine_id}" data-name="${m.medicine_name}">
                            ${m.medicine_name} — $${m.price}
                        </div>`
                    ).join('');
                    resultsEl.classList.toggle('open', meds.length > 0);
                } catch (err) {
                    console.error('Medicine search failed', err);
                }
            }, 300);

            searchEl.addEventListener('input', (e) => {
                idEl.value = '';
                searchMedicines(e.target.value);
            });

            resultsEl.addEventListener('click', (e) => {
                const item = e.target.closest('.autocomplete-item');
                if (!item) return;
                searchEl.value = item.dataset.name;
                idEl.value = item.dataset.id;
                resultsEl.classList.remove('open');
            });
        }

        // Start with one empty medicine row
        document.addEventListener('DOMContentLoaded', () => {
            addMedRow();
        });

        // --- Form submit ---
        document.getElementById('prescriptionForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const banner = document.getElementById('formErrorBanner');
            banner.style.display = 'none';
            document.querySelectorAll('.field-error').forEach(el => el.textContent = '');

            const userId = customerIdEl.value;
            const doctorFullName = document.getElementById('doctorFullName').value.trim();
            const issueDate = document.getElementById('issueDate').value;

            let hasError = false;

            if (!userId) {
                document.getElementById('customerError').textContent = 'Please select a customer from the list.';
                hasError = true;
            }
            if (!doctorFullName) {
                document.getElementById('doctorError').textContent = 'Doctor name is required.';
                hasError = true;
            }
            if (!issueDate) {
                document.getElementById('issueDateError').textContent = 'Issue date is required.';
                hasError = true;
            }

            const rows = document.querySelectorAll('#medRows .med-row');
            const medicines = [];
            rows.forEach(row => {
                const medId = row.querySelector('.med-id').value;
                const dosage = row.querySelector('.dosage').value;
                const qty = row.querySelector('.qty').value;
                const instructions = row.querySelector('.instructions').value;
                if (medId && qty) {
                    medicines.push({
                        medicine_id: parseInt(medId, 10),
                        dosage: dosage || null,
                        quantity: parseInt(qty, 10),
                        instructions: instructions || null,
                    });
                }
            });

            if (medicines.length === 0) {
                document.getElementById('medicinesError').textContent = 'Add at least one medicine selected from the search results.';
                hasError = true;
            }

            if (hasError) return;

            // Split "Dr. First Last" into first/last for the API's separate fields
            const nameParts = doctorFullName.replace(/^Dr\.?\s*/i, '').trim().split(' ');
            const doctorFirstName = nameParts[0] || doctorFullName;
            const doctorLastName = nameParts.slice(1).join(' ') || null;

            const payload = {
                user_id: parseInt(userId, 10),
                doctor_first_name: doctorFirstName,
                doctor_last_name: doctorLastName,
                doctor_license: document.getElementById('doctorLicense').value || null,
                doctor_clinic: document.getElementById('doctorClinic').value || null,
                issue_date: issueDate,
                expiry_date: document.getElementById('expiryDate').value || null,
                notes: document.getElementById('notes').value || null,
                medicines: medicines,
            };

            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';

            try {
                await window.axios.post('/api/prescriptions', payload);
                window.location.href = "{{ route('admin.prescriptions') }}";
            } catch (err) {
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save Prescription';

                if (err.response && err.response.status === 422) {
                    const errors = err.response.data.errors || {};
                    banner.textContent = 'Please fix the errors below.';
                    banner.style.display = 'block';
                    console.error('Validation errors:', errors);
                } else if (err.response && err.response.status === 403) {
                    banner.textContent = 'You are not authorized to create prescriptions. Please log in as staff.';
                    banner.style.display = 'block';
                } else {
                    banner.textContent = 'Something went wrong. Please try again.';
                    banner.style.display = 'block';
                    console.error(err);
                }
            }
        });
    </script>

@stop
