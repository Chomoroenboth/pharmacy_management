@extends('layouts.admin-app')

@section('title', 'Prescription Details')

@section('page-css')
<style>
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 28px; }
    .page-header .rx-code { color: #006c49; }

    .info-grid { display: flex; gap: 24px; margin-bottom: 24px; }
    .info-grid .card { flex: 1; margin-bottom: 0; }
    .section-label { font-size: 12px; color: #6c7a71; font-weight: 700; letter-spacing: 0.03em; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #f3f4f6; }
    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 11px; font-weight: 600; color: #3c4a42; margin-bottom: 6px; }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; font-family: inherit;
    }
    .form-row { display: flex; gap: 12px; }
    .form-row .form-group { flex: 1; }
    .locked-field { background: #f3f4f6; display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; color: #3c4a42; }

    .med-table { width: 100%; border-collapse: collapse; }
    .med-table th { background: #f3f4f6; text-align: left; padding: 12px 16px; font-size: 12px; color: #3c4a42; font-weight: 600; }
    .med-table td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
    .med-name { font-weight: 600; }
    .row-actions { display: flex; gap: 12px; }
    .row-actions button { background: none; border: none; cursor: pointer; font-size: 14px; }
    .row-actions .edit-icon { color: #006c49; }
    .row-actions .delete-icon { color: #ba1a1a; }

    .add-med-link { color: #006c49; background: none; border: 1px solid #10b981; border-radius: 6px; padding: 9px 18px; font-size: 13px; font-weight: 600; cursor: pointer; margin-top: 16px; }

    .bottom-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 24px; }
    .btn-delete-outline { background: #fff; border: 1px solid #ba1a1a; color: #ba1a1a; border-radius: 6px; padding: 10px 20px; font-size: 14px; font-weight: 600; cursor: pointer; }
    .bottom-right { display: flex; gap: 12px; }
    .btn-cancel { background: #fff; border: 1px solid #d7d9dc; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; color: #191c1e; }
    .btn-save { background: #10b981; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
    .btn-save:disabled { opacity: 0.6; cursor: default; }

    .banner { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; display: none; }
    .banner.success { background: #d1fae5; color: #065f46; }
    .banner.error { background: #ffdad6; color: #ba1a1a; }

    /* Modals */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: #fff; border-radius: 8px; width: 100%; max-width: 460px; padding: 32px; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h2 { font-size: 20px; }
    .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px; }
    .locked-input { background: #f3f4f6; display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; color: #3c4a42; }
    .delete-body { font-size: 14px; color: #3c4a42; line-height: 1.5; margin: 12px 0 0; }
    .delete-body strong { color: #191c1e; }
    .warn-icon-circle { width: 56px; height: 56px; border-radius: 50%; background: #ffdad6; color: #ba1a1a; font-size: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; }
    .btn-remove-solid { background: #ba1a1a; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }

    .search-wrap { position: relative; }
    .search-results { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #d7d9dc; border-radius: 6px; max-height: 180px; overflow-y: auto; z-index: 10; display: none; }
    .search-results.open { display: block; }
    .search-result-item { padding: 10px 12px; font-size: 14px; cursor: pointer; }
    .search-result-item:hover { background: #f3f4f6; }
    .search-result-empty { padding: 10px 12px; font-size: 13px; color: #6c7a71; }
    .selected-med-pill { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border: 1px solid #10b981; background: #ecfdf5; border-radius: 6px; font-size: 14px; }
    .selected-med-pill button { background: none; border: none; color: #6c7a71; cursor: pointer; font-size: 13px; }
</style>
@endsection

@section('content')

    <div id="pageBanner" class="banner"></div>

    <div class="page-header">
        <h1>Prescription Details - <span class="rx-code" id="rxCode">...</span></h1>
    </div>

    <div id="pageBody" style="display:none;">
        <div class="info-grid">
            <div class="card">
                <div class="section-label">DOCTOR INFORMATION</div>
                {{-- Doctor name/clinic are read-only here: the update endpoint
                     (Api\PrescriptionController@update) only persists status,
                     expiry_date, and notes — editing these fields would silently
                     not save, so they're shown locked instead of as live inputs. --}}
                <div class="form-group">
                    <label>DOCTOR NAME</label>
                    <div class="locked-field"><span id="doctorName">—</span> &#128274;</div>
                </div>
                <div class="form-group">
                    <label>CLINIC</label>
                    <div class="locked-field"><span id="doctorClinic">—</span> &#128274;</div>
                </div>
                <div class="form-group">
                    <label>LICENSE</label>
                    <div class="locked-field"><span id="doctorLicense">—</span> &#128274;</div>
                </div>
            </div>

            <div class="card">
                <div class="section-label">PRESCRIPTION DETAILS</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>STATUS</label>
                        <select id="statusInput">
                            <option value="active">Active</option>
                            <option value="filled">Filled</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>EXPIRY DATE</label>
                        <input type="date" id="expiryInput">
                    </div>
                </div>
                <div class="form-group">
                    <label>NOTES</label>
                    <textarea id="notesInput" rows="3"></textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="section-label">PRESCRIBED MEDICATIONS</div>
            <table class="med-table">
                <thead>
                    <tr><th>MEDICINE</th><th>DOSAGE</th><th>QTY</th><th>INSTRUCTIONS</th><th>ACTION</th></tr>
                </thead>
                <tbody id="medicinesTableBody">
                    <tr><td colspan="5">Loading...</td></tr>
                </tbody>
            </table>
            <button type="button" class="add-med-link" onclick="openModal('addMedicineModal')">+ Add Medicine</button>
        </div>

        <div class="bottom-actions">
            <button type="button" class="btn-delete-outline" onclick="openModal('deleteRxModal')">&#128465; Delete Prescription</button>
            <div class="bottom-right">
                <a href="{{ route('admin.prescriptions') }}" class="btn-cancel">Cancel</a>
                <button type="button" class="btn-save" id="saveChangesBtn">Save Changes</button>
            </div>
        </div>
    </div>

    {{-- Add Medicine modal — real medicine search against /api/inventory/medicines,
         then POST /api/prescriptions/{id}/medicines with the chosen medicine_id. --}}
    <div class="modal-overlay" id="addMedicineModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Add Medicine</h2>
                <button class="modal-close" onclick="closeModal('addMedicineModal')">&times;</button>
            </div>
            <div id="addMedError" style="display:none; color:#ba1a1a; font-size:13px; margin-bottom:12px;"></div>

            <div class="form-group search-wrap" id="addMedSearchGroup">
                <label>MEDICINE NAME</label>
                <input type="text" id="addMedSearchInput" placeholder="Search medicine...">
                <div class="search-results" id="addMedSearchResults"></div>
            </div>
            <div class="form-group" id="addMedSelectedGroup" style="display:none;">
                <label>MEDICINE</label>
                <div class="selected-med-pill">
                    <span id="addMedSelectedName"></span>
                    <button type="button" onclick="clearSelectedMedicine()">Change</button>
                </div>
            </div>

            <div class="form-group">
                <label>DOSAGE</label>
                <input type="text" id="addMedDosage" placeholder="e.g. 500mg">
            </div>
            <div class="form-group">
                <label>QUANTITY</label>
                <input type="number" id="addMedQuantity" placeholder="0" min="1">
            </div>
            <div class="form-group">
                <label>INSTRUCTIONS</label>
                <textarea id="addMedInstructions" rows="2" placeholder="e.g. Take 1 tablet daily"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('addMedicineModal')">Cancel</button>
                <button type="button" class="btn-save" id="submitAddMedicineBtn" disabled>Add Medicine</button>
            </div>
        </div>
    </div>

    {{-- Edit Medicine modal — not yet wired --}}
    <div class="modal-overlay" id="editMedicineModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Edit Medicine</h2>
                <button class="modal-close" onclick="closeModal('editMedicineModal')">&times;</button>
            </div>
            <div class="form-group">
                <label>MEDICINE NAME</label>
                <div class="locked-input"><span id="editMedName"></span> &#128274;</div>
            </div>
            <div class="form-group">
                <label>DOSAGE</label>
                <input type="text" id="editMedDosage">
            </div>
            <div class="form-group">
                <label>QUANTITY</label>
                <input type="number" id="editMedQuantity">
            </div>
            <div class="form-group">
                <label>INSTRUCTIONS</label>
                <textarea id="editMedInstructions" rows="2"></textarea>
            </div>
            <div id="editMedError" style="display:none; color:#ba1a1a; font-size:13px; margin-bottom:12px;"></div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('editMedicineModal')">Cancel</button>
                <button type="button" class="btn-save" id="submitEditMedicineBtn">&#10003; Save Changes</button>
            </div>
        </div>
    </div>

    {{-- Remove Medicine modal — not yet wired --}}
    <div class="modal-overlay" id="removeMedicineModal">
        <div class="modal-box" style="text-align:center;">
            <div class="warn-icon-circle">&#9888;</div>
            <h2>Remove Medicine?</h2>
            <div class="delete-body">
                Are you sure you want to remove <strong id="removeMedName"></strong> from this prescription? This action cannot be undone.
            </div>
            <div id="removeMedError" style="display:none; color:#ba1a1a; font-size:13px; margin: 8px 0 0;"></div>
            <div class="modal-footer" style="justify-content:center;">
                <button type="button" class="btn-cancel" onclick="closeModal('removeMedicineModal')">Cancel</button>
                <button type="button" class="btn-remove-solid" id="submitRemoveMedicineBtn">Remove</button>
            </div>
        </div>
    </div>

    {{-- Delete Prescription modal — already wired to the real API --}}
    <div class="modal-overlay" id="deleteRxModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2><span style="color:#ba1a1a;">&#9888;</span> Delete Prescription</h2>
            </div>
            <div class="delete-body">
                Are you sure you want to delete this prescription? This action cannot be undone and will remove the record from the patient's active treatment history.
            </div>
            <div id="deleteRxError" style="display:none; color:#ba1a1a; font-size:13px; margin-top:8px;"></div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('deleteRxModal')">Cancel</button>
                <button type="button" class="btn-remove-solid" id="confirmDeleteRxBtn">Delete Prescription</button>
            </div>
        </div>
    </div>

    <script>
        const prescriptionId = {{ (int) $prescriptionId }};
        const token = localStorage.getItem('auth_token');

        function openModal(id) { document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }

        function showBanner(message, type) {
            const banner = document.getElementById('pageBanner');
            banner.textContent = message;
            banner.className = 'banner ' + type;
            banner.style.display = 'block';
        }

        function renderMedicines(medicines) {
            const tbody = document.getElementById('medicinesTableBody');
            tbody.innerHTML = '';

            if (!medicines.length) {
                tbody.innerHTML = '<tr><td colspan="5">No medicines on this prescription.</td></tr>';
                return;
            }

            medicines.forEach(med => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="med-name">${med.medicine_name}</td>
                    <td>${med.dosage ?? ''}</td>
                    <td>${med.quantity}</td>
                    <td>${med.instructions ?? ''}</td>
                    <td>
                        <div class="row-actions">
                            <button type="button" class="edit-icon" onclick="openEditMedicine(${med.detail_id}, '${(med.medicine_name || '').replace(/'/g, "\\'")}', '${(med.dosage || '').replace(/'/g, "\\'")}', ${med.quantity}, '${(med.instructions || '').replace(/'/g, "\\'")}')">&#9998;</button>
                            <button type="button" class="delete-icon" onclick="openRemoveMedicine(${med.detail_id}, '${(med.medicine_name || '').replace(/'/g, "\\'")}')">&#128465;</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function loadPrescription() {
            fetch(`/api/prescriptions/${prescriptionId}`, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (!ok || data.status !== 'success') {
                    showBanner(data.message || 'Failed to load prescription.', 'error');
                    return;
                }

                const p = data.data.prescription;
                const medicines = data.data.medicines;

                document.getElementById('rxCode').textContent = p.display_code;
                document.getElementById('doctorName').textContent = p.doctor_name;
                document.getElementById('doctorClinic').textContent = p.doctor_clinic || '—';
                document.getElementById('doctorLicense').textContent = p.doctor_license || '—';
                document.getElementById('statusInput').value = p.status;
                document.getElementById('expiryInput').value = p.expiry_date || '';
                document.getElementById('notesInput').value = p.notes || '';

                renderMedicines(medicines);

                document.getElementById('pageBody').style.display = 'block';
            })
            .catch(() => showBanner('Network error while loading prescription.', 'error'));
        }

        document.getElementById('saveChangesBtn').addEventListener('click', function () {
            const btn = this;
            btn.disabled = true;

            const payload = {
                status: document.getElementById('statusInput').value,
                expiry_date: document.getElementById('expiryInput').value || null,
                notes: document.getElementById('notesInput').value
            };

            fetch(`/api/prescriptions/${prescriptionId}`, {
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.status === 'success') {
                    showBanner('Prescription updated successfully.', 'success');
                } else {
                    showBanner(data.message || 'Failed to update prescription.', 'error');
                }
                btn.disabled = false;
            })
            .catch(() => {
                showBanner('Network error while saving changes.', 'error');
                btn.disabled = false;
            });
        });

        document.getElementById('confirmDeleteRxBtn').addEventListener('click', function () {
            const btn = this;
            const errorBox = document.getElementById('deleteRxError');

            btn.disabled = true;
            errorBox.style.display = 'none';

            fetch(`/api/prescriptions/${prescriptionId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.status === 'success') {
                    window.location.href = "{{ route('admin.prescriptions') }}";
                } else {
                    errorBox.textContent = data.message || 'Failed to delete prescription.';
                    errorBox.style.display = 'block';
                    btn.disabled = false;
                }
            })
            .catch(() => {
                errorBox.textContent = 'Network error — could not delete prescription.';
                errorBox.style.display = 'block';
                btn.disabled = false;
            });
        });

        // ----- Add Medicine: live search against /api/inventory/medicines -----
        // NOTE: the exact JSON field names below (medicine_id, medicine_name) are
        // based on the `medicine` table columns from the schema, since the
        // Api\MedicineController@index response itself wasn't shown to me this
        // session. If the search results don't render correctly, this is the
        // first place to check against the real response shape.
        let addMedSearchTimer = null;
        let addMedSelectedId = null;

        document.getElementById('addMedSearchInput').addEventListener('input', function () {
            const query = this.value.trim();
            clearTimeout(addMedSearchTimer);

            if (!query) {
                document.getElementById('addMedSearchResults').classList.remove('open');
                return;
            }

            addMedSearchTimer = setTimeout(() => {
                fetch(`/api/inventory/medicines?search=${encodeURIComponent(query)}&per_page=8`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const resultsBox = document.getElementById('addMedSearchResults');
                    const items = (data.data || []);

                    if (!items.length) {
                        resultsBox.innerHTML = '<div class="search-result-empty">No medicines found.</div>';
                    } else {
                        resultsBox.innerHTML = items.map(m => `
                            <div class="search-result-item"
                                 onclick="selectMedicine(${m.medicine_id}, '${(m.medicine_name || '').replace(/'/g, "\\'")}')">
                                ${m.medicine_name}
                            </div>
                        `).join('');
                    }
                    resultsBox.classList.add('open');
                })
                .catch(() => {
                    document.getElementById('addMedError').textContent = 'Could not search medicines.';
                    document.getElementById('addMedError').style.display = 'block';
                });
            }, 300);
        });

        function selectMedicine(medicineId, name) {
            addMedSelectedId = medicineId;
            document.getElementById('addMedSelectedName').textContent = name;
            document.getElementById('addMedSearchGroup').style.display = 'none';
            document.getElementById('addMedSelectedGroup').style.display = 'block';
            document.getElementById('addMedSearchResults').classList.remove('open');
            document.getElementById('submitAddMedicineBtn').disabled = false;
        }

        function clearSelectedMedicine() {
            addMedSelectedId = null;
            document.getElementById('addMedSearchInput').value = '';
            document.getElementById('addMedSearchGroup').style.display = 'block';
            document.getElementById('addMedSelectedGroup').style.display = 'none';
            document.getElementById('submitAddMedicineBtn').disabled = true;
        }

        function resetAddMedicineForm() {
            clearSelectedMedicine();
            document.getElementById('addMedDosage').value = '';
            document.getElementById('addMedQuantity').value = '';
            document.getElementById('addMedInstructions').value = '';
            document.getElementById('addMedError').style.display = 'none';
        }

        document.getElementById('submitAddMedicineBtn').addEventListener('click', function () {
            const btn = this;
            const errorBox = document.getElementById('addMedError');
            errorBox.style.display = 'none';

            if (!addMedSelectedId) {
                errorBox.textContent = 'Please select a medicine first.';
                errorBox.style.display = 'block';
                return;
            }

            btn.disabled = true;

            const payload = {
                medicine_id: addMedSelectedId,
                dosage: document.getElementById('addMedDosage').value,
                quantity: parseInt(document.getElementById('addMedQuantity').value, 10),
                instructions: document.getElementById('addMedInstructions').value
            };

            fetch(`/api/prescriptions/${prescriptionId}/medicines`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.status === 'success') {
                    closeModal('addMedicineModal');
                    resetAddMedicineForm();
                    showBanner('Medicine added to prescription.', 'success');
                    loadPrescription();
                } else {
                    errorBox.textContent = data.message || 'Failed to add medicine.';
                    errorBox.style.display = 'block';
                }
                btn.disabled = false;
            })
            .catch(() => {
                errorBox.textContent = 'Network error while adding medicine.';
                errorBox.style.display = 'block';
                btn.disabled = false;
            });
        });

        // ----- Edit Medicine -----
        let currentEditDetailId = null;

        function openEditMedicine(detailId, name, dosage, quantity, instructions) {
            currentEditDetailId = detailId;
            document.getElementById('editMedError').style.display = 'none';
            document.getElementById('editMedName').textContent = name;
            document.getElementById('editMedDosage').value = dosage;
            document.getElementById('editMedQuantity').value = quantity;
            document.getElementById('editMedInstructions').value = instructions;
            openModal('editMedicineModal');
        }

        document.getElementById('submitEditMedicineBtn').addEventListener('click', function () {
            const btn = this;
            const errorBox = document.getElementById('editMedError');
            btn.disabled = true;

            const payload = {
                dosage: document.getElementById('editMedDosage').value,
                quantity: parseInt(document.getElementById('editMedQuantity').value, 10),
                instructions: document.getElementById('editMedInstructions').value
            };

            fetch(`/api/prescriptions/medicines/${currentEditDetailId}`, {
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.status === 'success') {
                    closeModal('editMedicineModal');
                    showBanner('Medicine entry updated.', 'success');
                    loadPrescription();
                } else {
                    errorBox.textContent = data.message || 'Failed to update medicine.';
                    errorBox.style.display = 'block';
                }
                btn.disabled = false;
            })
            .catch(() => {
                errorBox.textContent = 'Network error while updating medicine.';
                errorBox.style.display = 'block';
                btn.disabled = false;
            });
        });

        // ----- Remove Medicine -----
        let currentRemoveDetailId = null;

        function openRemoveMedicine(detailId, name) {
            currentRemoveDetailId = detailId;
            document.getElementById('removeMedError').style.display = 'none';
            document.getElementById('removeMedName').textContent = name;
            openModal('removeMedicineModal');
        }

        document.getElementById('submitRemoveMedicineBtn').addEventListener('click', function () {
            const btn = this;
            const errorBox = document.getElementById('removeMedError');
            btn.disabled = true;

            fetch(`/api/prescriptions/medicines/${currentRemoveDetailId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (ok && data.status === 'success') {
                    closeModal('removeMedicineModal');
                    showBanner('Medicine removed from prescription.', 'success');
                    loadPrescription();
                } else {
                    errorBox.textContent = data.message || 'Failed to remove medicine.';
                    errorBox.style.display = 'block';
                }
                btn.disabled = false;
            })
            .catch(() => {
                errorBox.textContent = 'Network error while removing medicine.';
                errorBox.style.display = 'block';
                btn.disabled = false;
            });
        });

        loadPrescription();
    </script>

@stop
