@extends('layouts.admin-app')

@section('title', 'Customer Profile')

@section('page-css')
<style>
    .page-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
    .back-btn {
        width: 36px; height: 36px; border-radius: 50%; background: #adedd3;
        display: flex; align-items: center; justify-content: center;
        text-decoration: none; color: #04281b; font-size: 16px;
    }
    .page-header h1 { font-size: 24px; flex: 1; }
    .header-actions { display: flex; gap: 10px; }
    .btn-outline {
        background: #fff; border: 1px solid #10b981; border-radius: 6px;
        padding: 9px 18px; font-size: 13px; font-weight: 600; color: #006c49;
        cursor: pointer; text-decoration: none;
    }

    .detail-layout { display: flex; gap: 24px; align-items: flex-start; }
    .detail-layout .card { margin-bottom: 24px; }
    .side-card { width: 280px; flex-shrink: 0; }
    .main-col { flex: 1; min-width: 0; }

    .cust-name-lg { font-size: 20px; font-weight: 700; }
    .cust-id-sm { font-size: 13px; color: #6c7a71; margin-bottom: 16px; }
    .side-divider { border-top: 1px solid #f3f4f6; margin: 16px 0; }
    .side-label { font-size: 12px; color: #6c7a71; font-weight: 600; letter-spacing: 0.02em; margin-bottom: 8px; }
    .side-row { font-size: 14px; margin-bottom: 6px; display: flex; align-items: center; gap: 8px; }

    .rx-table, .hist-table { width: 100%; border-collapse: collapse; }
    .rx-table th, .hist-table th {
        background: #f3f4f6; text-align: left; padding: 12px 16px;
        font-size: 12px; color: #3c4a42; font-weight: 600;
    }
    .rx-table td, .hist-table td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
    .status-pill { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; }
    .status-valid, .status-paid { background: #adedd3; color: #306d58; }
    .status-expired { background: #ffdad6; color: #930a0a; }

    /* Shared modal styles (same pattern as customer edit modal) */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: flex-start; justify-content: center; padding: 60px 20px; overflow-y: auto; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: #fff; border-radius: 8px; width: 100%; max-width: 600px; padding: 32px; }
    .modal-box.narrow { max-width: 460px; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h2 { font-size: 20px; }
    .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; }
    .modal-section-title { font-size: 15px; font-weight: 600; margin-bottom: 16px; }
    .form-row { display: flex; gap: 16px; margin-bottom: 16px; }
    .form-field { flex: 1; }
    .form-field label { display: block; font-size: 11px; font-weight: 600; color: #3c4a42; margin-bottom: 6px; }
    .form-field input { width: 100%; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; }
    .allergy-edit-row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
    .badge-removable { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; padding: 6px 10px; border-radius: 4px; font-weight: 500; }
    .badge-warn { background: #ffdad6; color: #930a0a; }
    .badge-info { background: #e7e8ea; color: #3c4a42; }
    .badge-removable button { background: none; border: none; cursor: pointer; color: inherit; }
    .add-allergy-row { display: flex; gap: 8px; margin-bottom: 8px; }
    .add-allergy-row input { flex: 1; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; }
    .btn-add-allergy { background: #edeef0; border: none; border-radius: 6px; padding: 10px 20px; font-size: 14px; font-weight: 500; cursor: pointer; }
    .modal-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
    .modal-footer-right { display: flex; gap: 12px; }
    .btn-cancel { background: #fff; border: 1px solid #d7d9dc; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 500; cursor: pointer; }
    .btn-save { background: #10b981; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
    .btn-delete-text { background: none; border: none; color: #ba1a1a; font-size: 14px; font-weight: 600; cursor: pointer; }

    .delete-warning-icon { color: #ba1a1a; font-size: 18px; }
    .delete-body { font-size: 14px; color: #3c4a42; line-height: 1.5; margin: 16px 0 24px; }
    .btn-delete-solid { background: #ba1a1a; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
</style>
@endsection

@section('content')

    <div class="page-header">
        <a href="{{ route('admin.customers') }}" class="back-btn">&larr;</a>
        <h1>Customer Profile</h1>
        <div class="header-actions">
            <a href="#" class="btn-outline" onclick="openEditModal(event)">Edit Details</a>
        </div>
    </div>

    <div class="detail-layout">
        <div class="card side-card">
            <div class="cust-name-lg" id="cust-name">Loading...</div>
            <div class="cust-id-sm" id="cust-id-sm">ID: —</div>

            <div class="side-divider"></div>
            <div class="side-label">CONTACT INFORMATION</div>
            <div class="side-row">&#128222; <span id="cust-phone">—</span></div>
            <div class="side-row">&#9993; <span id="cust-email">—</span></div>
        </div>

        <div class="main-col">
            <div class="card" style="padding:0; overflow:hidden;">
                <div style="padding: 20px 24px 12px;">
                    <span class="card-title" style="margin-bottom:0;">Active Prescriptions</span>
                </div>
                <table class="rx-table">
                    <thead>
                        <tr><th>MEDICATION</th><th>DOSAGE</th><th>STATUS</th></tr>
                    </thead>
                    <tbody id="rx-body">
                        <tr class="loading-row"><td colspan="3">Loading...</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="card" style="padding:0; overflow:hidden;">
                <div style="padding: 20px 24px 12px;">
                    <span class="card-title" style="margin-bottom:0;">Purchase History</span>
                </div>
                <table class="hist-table">
                    <thead>
                        <tr><th>DATE</th><th>ITEMS</th><th>TOTAL</th><th>STATUS</th></tr>
                    </thead>
                    <tbody id="hist-body">
                        <tr class="loading-row"><td colspan="4">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Edit Customer modal --}}
    <div class="modal-overlay" id="editModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Edit Customer Details</h2>
                <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form onsubmit="return saveEdit(event)">
                <div class="modal-section-title">Personal Information</div>
                <div class="form-row">
                    <div class="form-field"><label>FIRST NAME</label><input type="text" id="edit_first_name"></div>
                    <div class="form-field"><label>LAST NAME</label><input type="text" id="edit_last_name"></div>
                </div>
                <div class="form-row">
                    <div class="form-field"><label>PHONE NUMBER</label><input type="text" id="edit_phone"></div>
                    <div class="form-field"><label>EMAIL ADDRESS</label><input type="email" id="edit_email"></div>
                </div>
                <div class="form-row">
                    <div class="form-field"><label>DATE OF BIRTH</label><input type="text" id="edit_dob"></div>
                </div>

                <div class="modal-section-title">Allergies &amp; Alerts</div>
                <div class="allergy-edit-row" id="editAllergyList"></div>
                <div class="add-allergy-row">
                    <input type="text" id="newAllergyInput" placeholder="Add new allergy...">
                    <button type="button" class="btn-add-allergy" onclick="addAllergyEdit()">Add</button>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-delete-text" onclick="closeModal('editModal'); openModal('deleteModal');">Delete</button>
                    <div class="modal-footer-right">
                        <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box narrow">
            <div class="modal-header">
                <h2><span class="delete-warning-icon">&#9888;</span> Delete Customer</h2>
            </div>
            <div class="delete-body">
                Are you sure you want to delete this customer? This action cannot be undone and will permanently remove their profile, prescriptions, and purchase history from the system.
            </div>
            <div class="modal-footer" style="justify-content: flex-end;">
                <div class="modal-footer-right">
                    <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                    <button type="button" class="btn-delete-solid" onclick="confirmDelete()">Delete Customer</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('page-js')
<script>
(function () {
    const customerId = {{ $customerId }};

    function authToken() {
        return localStorage.getItem('auth_token');
    }

    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
    }

    async function loadCustomer() {
        try {
            const res = await fetch(`/api/staff/customers/${customerId}`, {
                headers: {
                    'Authorization': `Bearer ${authToken()}`,
                    'Accept': 'application/json'
                }
            });

            if (res.status === 401) {
                window.location.href = '/admin/login';
                return;
            }

            const json = await res.json();
            const { customer, allergies, prescriptions, purchases } = json.data;

            document.getElementById('cust-name').textContent = customer.full_name;
            document.getElementById('cust-id-sm').textContent = `ID: #${customer.display_id}`;
            document.getElementById('cust-phone').textContent = customer.phone_number ?? '—';
            document.getElementById('cust-email').textContent = customer.email;

            document.getElementById('edit_first_name').value = customer.first_name ?? '';
            document.getElementById('edit_last_name').value = customer.last_name ?? '';
            document.getElementById('edit_phone').value = customer.phone_number ?? '';
            document.getElementById('edit_email').value = customer.email ?? '';
            document.getElementById('edit_dob').value = customer.date_of_birth ?? '';

            renderAllergies(allergies);
            renderPrescriptions(prescriptions);
            renderPurchases(purchases);
        } catch (err) {
            console.error(err);
        }
    }

    function renderAllergies(allergies) {
        const list = document.getElementById('editAllergyList');
        list.innerHTML = allergies.map(a => `
            <span class="badge-removable badge-info">
                ${a.allergy_name}
                <button type="button" onclick="this.parentElement.remove()">&times;</button>
            </span>
        `).join('');
    }

    function renderPrescriptions(prescriptions) {
        const tbody = document.getElementById('rx-body');
        if (!prescriptions.length) {
            tbody.innerHTML = '<tr class="empty-row"><td colspan="3">No prescriptions.</td></tr>';
            return;
        }
        tbody.innerHTML = prescriptions.map(rx => `
            <tr>
                <td>${rx.medicine_name}</td>
                <td>${rx.dosage ?? '—'}</td>
                <td><span class="status-pill status-${rx.status}">${rx.status.charAt(0).toUpperCase() + rx.status.slice(1)}</span></td>
            </tr>
        `).join('');
    }

    function renderPurchases(purchases) {
        const tbody = document.getElementById('hist-body');
        if (!purchases.length) {
            tbody.innerHTML = '<tr class="empty-row"><td colspan="4">No purchase history.</td></tr>';
            return;
        }
        tbody.innerHTML = purchases.map(p => `
            <tr>
                <td>${formatDate(p.sale_date)}</td>
                <td>${p.items || '—'}</td>
                <td>$${Number(p.total_price).toFixed(2)}</td>
                <td><span class="status-pill status-${p.payment_status === 'paid' ? 'paid' : 'expired'}">${p.payment_status.charAt(0).toUpperCase() + p.payment_status.slice(1)}</span></td>
            </tr>
        `).join('');
    }

    window.openModal = function (id) { document.getElementById(id).classList.add('open'); };
    window.closeModal = function (id) { document.getElementById(id).classList.remove('open'); };
    window.openEditModal = function (e) { e.preventDefault(); openModal('editModal'); };

    window.addAllergyEdit = function () {
        const input = document.getElementById('newAllergyInput');
        const name = input.value.trim();
        if (!name) return;
        const badge = document.createElement('span');
        badge.className = 'badge-removable badge-info';
        badge.innerHTML = name + ' <button type="button" onclick="this.parentElement.remove()">&times;</button>';
        document.getElementById('editAllergyList').appendChild(badge);
        input.value = '';
    };

    window.saveEdit = async function (e) {
        e.preventDefault();
        try {
            const res = await fetch(`/api/staff/customers/${customerId}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${authToken()}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    first_name: document.getElementById('edit_first_name').value,
                    last_name: document.getElementById('edit_last_name').value,
                    email: document.getElementById('edit_email').value,
                    phone_number: document.getElementById('edit_phone').value,
                    date_of_birth: document.getElementById('edit_dob').value
                })
            });

            if (!res.ok) {
                const err = await res.json();
                alert('Failed to save: ' + (err.message || 'Unknown error'));
                return false;
            }

            closeModal('editModal');
            loadCustomer();
        } catch (err) {
            alert('Failed to save changes.');
            console.error(err);
        }
        return false;
    };

    window.confirmDelete = async function () {
        try {
            const res = await fetch(`/api/staff/customers/${customerId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${authToken()}`,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                alert('Failed to delete customer.');
                return;
            }

            window.location.href = "{{ route('admin.customers') }}";
        } catch (err) {
            alert('Failed to delete customer.');
            console.error(err);
        }
    };

    loadCustomer();
})();
</script>
@stop
