@extends('layouts.admin-app')

@section('title', 'Medicine Details')

@section('page-css')
<style>
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 28px; }

    .detail-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 24px; }
    .detail-item .label { font-size: 12px; color: #6c7a71; font-weight: 600; letter-spacing: 0.02em; margin-bottom: 6px; }
    .detail-item .value { font-size: 16px; color: #191c1e; }
    .detail-item .value.price { color: #10b981; font-size: 22px; font-weight: 700; }
    .badge-yesno { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 12px; background: #edeef0; color: #3c4a42; }
    .stock-pill { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; }
    .stock-pill .dot { width: 8px; height: 8px; border-radius: 50%; }
    .dot-low { background: #ba1a1a; }
    .dot-ok { background: #10b981; }

    .action-row { display: flex; gap: 12px; margin-top: 8px; padding-top: 20px; border-top: 1px solid #f3f4f6; }
    .btn-outline { background: #fff; border: 1px solid #10b981; border-radius: 6px; padding: 9px 18px; font-size: 13px; font-weight: 600; color: #006c49; cursor: pointer; text-decoration: none; }

    .price-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    .price-table th { background: #f3f4f6; text-align: left; padding: 12px 16px; font-size: 12px; color: #3c4a42; font-weight: 600; }
    .price-table td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
    .diff-up { color: #ba1a1a; background: #ffdad6; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
    .diff-down { color: #10b981; background: #adedd3; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
    .no-history { padding: 24px 16px; color: #6c7a71; font-size: 14px; text-align: center; }

    /* Modals */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: #fff; border-radius: 8px; width: 100%; max-width: 480px; padding: 32px; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h2 { font-size: 20px; }
    .modal-header .med-tag { color: #006c49; font-size: 13px; font-weight: 600; }
    .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; }
    .form-group { margin-bottom: 16px; }
    .form-row { display: flex; gap: 12px; }
    .form-row .form-group { flex: 1; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #3c4a42; margin-bottom: 6px; }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; font-family: inherit;
    }
    .modal-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
    .modal-footer-right { display: flex; gap: 12px; }
    .btn-cancel { background: #fff; border: 1px solid #d7d9dc; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 500; cursor: pointer; }
    .btn-save { background: #10b981; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
    .btn-delete-text { background: none; border: none; color: #ba1a1a; font-size: 14px; font-weight: 600; cursor: pointer; }
    .btn-delete-solid { background: #ba1a1a; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
    .btn-delete-solid:disabled { opacity: 0.6; cursor: default; }
    .delete-body { font-size: 14px; color: #3c4a42; line-height: 1.5; margin: 12px 0 20px; }
    .form-error { color: #ba1a1a; font-size: 13px; margin-bottom: 12px; display: none; }
    .form-error.show { display: block; }
</style>
@endsection

@section('content')

    <div id="page-message" style="background:#d1fae5; color:#065f46; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px; display:none;"></div>

    <div class="page-header">
        <h1>Medicine Details</h1>
    </div>

    <div class="card">
        <div class="detail-grid" id="detail-grid">
            <div class="detail-item">
                <div class="label">MEDICINE ID</div>
                <div class="value" id="d-code">Loading...</div>
            </div>
            <div class="detail-item">
                <div class="label">MEDICINE NAME</div>
                <div class="value" id="d-name"></div>
            </div>
            <div class="detail-item">
                <div class="label">CATEGORY</div>
                <div class="value" id="d-category"></div>
            </div>
            <div class="detail-item">
                <div class="label">BRAND</div>
                <div class="value" id="d-brand"></div>
            </div>
            <div class="detail-item">
                <div class="label">CURRENT PRICE</div>
                <div class="value price" id="d-price"></div>
            </div>
            <div class="detail-item">
                <div class="label">REQUIRES PRESCRIPTION</div>
                <div class="badge-yesno" id="d-rx"></div>
            </div>
            <div class="detail-item">
                <div class="label">STOCK LEVEL</div>
                <div class="stock-pill" id="d-stock"></div>
            </div>
        </div>

        <div class="action-row">
            <a href="#" class="btn-outline" onclick="openModal('editModal', event)">&#9998; Edit Medicine</a>
            <a href="#" class="btn-outline" onclick="openModal('restockModal', event)">&#128230; Update Stock</a>
        </div>
    </div>

    <div class="card">
        <div class="card-title">&#8635; Price Change Log</div>
        <div id="price-history-container">
            <div class="no-history">Loading...</div>
        </div>

        <div style="margin-top: 16px;">
            <a href="/admin/inventory/{{ $medicineId }}/price" class="btn-outline">&#43; Record Price Change</a>
        </div>
    </div>

    {{-- Edit Medicine modal --}}
    <div class="modal-overlay" id="editModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Edit Medicine</h2>
                <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
            </div>
            <div class="form-error" id="edit-error"></div>
            <form id="editMedicineForm">
                <div class="form-group">
                    <label>Medicine Name</label>
                    <input type="text" name="medicine_name" id="edit-name">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" id="edit-category">
                </div>
                <div class="form-group">
                    <label>Brand</label>
                    <input type="text" name="brand" id="edit-brand">
                </div>
                <div class="form-group">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="price" id="edit-price">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-delete-text" onclick="closeModal('editModal'); openModal('deleteModal');">Delete</button>
                    <div class="modal-footer-right">
                        <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
                        <button type="submit" class="btn-save" id="editSaveBtn">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Update Stock (Restock) modal --}}
    <div class="modal-overlay" id="restockModal">
        <div class="modal-box">
            <div class="modal-header">
                <div>
                    <h2>Log Stock Transaction</h2>
                    <div class="med-tag" id="restock-med-tag"></div>
                </div>
                <button class="modal-close" onclick="closeModal('restockModal')">&times;</button>
            </div>
            <div class="form-error" id="restock-error"></div>
            <form id="restockForm">
                <div class="form-row">
                    <div class="form-group">
                        <label>Transaction Type</label>
                        <select name="txn_type">
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" placeholder="e.g. 500">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Unit Cost ($)</label>
                        <input type="number" step="0.01" name="unit_cost" placeholder="0.00">
                    </div>
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3" placeholder="Add any relevant details regarding this transaction (optional)..."></textarea>
                </div>

                <div class="modal-footer" style="justify-content: flex-end;">
                    <div class="modal-footer-right">
                        <button type="button" class="btn-cancel" onclick="closeModal('restockModal')">Cancel</button>
                        <button type="submit" class="btn-save" id="restockSaveBtn">Save Transaction</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete confirmation modal — now wired to the real API --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box" style="max-width: 420px;">
            <div class="modal-header">
                <h2><span style="color:#ba1a1a;">&#9888;</span> Delete Medicine</h2>
            </div>
            <div class="delete-body" id="delete-body-text">
                Are you sure you want to delete this medicine? This action cannot be undone and will remove it from inventory, including its price history.
            </div>
            <div class="form-error" id="delete-error"></div>
            <div class="modal-footer" style="justify-content: flex-end;">
                <div class="modal-footer-right">
                    <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                    <button type="button" class="btn-delete-solid" id="confirmDeleteBtn">Delete Medicine</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(id, e) { if (e) e.preventDefault(); document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    </script>

@stop

@section('page-js')
<script>
(function () {
    const medicineId = {{ $medicineId }};
    let currentMedicine = null;

    function authToken() {
        return localStorage.getItem('auth_token');
    }

    function showMessage(text) {
        const el = document.getElementById('page-message');
        el.textContent = text;
        el.style.display = 'block';
    }

    function showFormError(id, text) {
        const el = document.getElementById(id);
        el.textContent = text;
        el.classList.add('show');
    }

    function clearFormError(id) {
        const el = document.getElementById(id);
        el.textContent = '';
        el.classList.remove('show');
    }

    function renderMedicine(m) {
        currentMedicine = m;
        document.getElementById('d-code').textContent = m.display_id;
        document.getElementById('d-name').textContent = m.medicine_name;
        document.getElementById('d-category').textContent = m.category ?? '-';
        document.getElementById('d-brand').textContent = m.brand ?? '-';
        document.getElementById('d-price').textContent = `$${parseFloat(m.price).toFixed(2)}`;
        document.getElementById('d-rx').textContent = m.requires_prescription ? 'Yes' : 'No';
        document.getElementById('d-stock').innerHTML =
            `<span class="dot ${m.current_stock <= 10 ? 'dot-low' : 'dot-ok'}"></span> ${m.current_stock} Units`;

        // Pre-fill edit form
        document.getElementById('edit-name').value = m.medicine_name;
        document.getElementById('edit-category').value = m.category ?? '';
        document.getElementById('edit-brand').value = m.brand ?? '';
        document.getElementById('edit-price').value = m.price;

        document.getElementById('restock-med-tag').textContent = m.medicine_name;
        document.getElementById('delete-body-text').textContent =
            `Are you sure you want to delete ${m.medicine_name}? This action cannot be undone and will remove it from inventory, including its price history.`;

        document.title = `${m.medicine_name} - Medicine Details`;
    }

    function renderPriceHistory(rows) {
        const container = document.getElementById('price-history-container');

        if (!rows.length) {
            container.innerHTML = '<div class="no-history">No price changes recorded yet.</div>';
            return;
        }

        const rowsHtml = rows.map(p => {
            const oldPrice = parseFloat(p.old_price);
            const newPrice = parseFloat(p.new_price);
            const diff = newPrice - oldPrice;
            const dateLabel = new Date(p.effective_date).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
            return `
                <tr>
                    <td>${dateLabel}</td>
                    <td>$${oldPrice.toFixed(2)}</td>
                    <td>$${newPrice.toFixed(2)}</td>
                    <td><span class="${diff >= 0 ? 'diff-up' : 'diff-down'}">${diff >= 0 ? '+' : '-'}$${Math.abs(diff).toFixed(2)}</span></td>
                </tr>
            `;
        }).join('');

        container.innerHTML = `
            <table class="price-table">
                <thead><tr><th>DATE</th><th>OLD PRICE</th><th>NEW PRICE</th><th>DIFFERENCE</th></tr></thead>
                <tbody>${rowsHtml}</tbody>
            </table>
        `;
    }

    async function loadMedicine() {
        try {
            const res = await fetch(`/api/inventory/medicines/${medicineId}`, {
                headers: { 'Authorization': `Bearer ${authToken()}`, 'Accept': 'application/json' }
            });

            if (res.status === 401) {
                window.location.href = '/admin/login';
                return;
            }

            if (res.status === 404) {
                document.getElementById('detail-grid').innerHTML = '<div class="no-history">Medicine not found.</div>';
                return;
            }

            const json = await res.json();
            renderMedicine(json.data);
        } catch (err) {
            console.error('Failed to load medicine', err);
        }
    }

    async function loadPriceHistory() {
        try {
            const res = await fetch(`/api/inventory/medicines/${medicineId}/price-history`, {
                headers: { 'Authorization': `Bearer ${authToken()}`, 'Accept': 'application/json' }
            });

            if (res.status === 401) return;

            const json = await res.json();
            const rows = Array.isArray(json.data) ? json.data : (json.data ?? []);
            renderPriceHistory(rows);
        } catch (err) {
            document.getElementById('price-history-container').innerHTML =
                '<div class="no-history">Failed to load price history.</div>';
            console.error(err);
        }
    }

    // --- Edit Medicine ---
    document.getElementById('editMedicineForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        clearFormError('edit-error');

        const form = e.target;
        const payload = {
            medicine_name: form.medicine_name.value,
            category: form.category.value,
            brand: form.brand.value,
            price: form.price.value,
        };

        try {
            const res = await fetch(`/api/inventory/medicines/${medicineId}`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${authToken()}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (res.status === 401) {
                window.location.href = '/admin/login';
                return;
            }

            const json = await res.json();

            if (!res.ok) {
                showFormError('edit-error', json.message || 'Failed to update medicine.');
                return;
            }

            closeModal('editModal');
            showMessage('Medicine updated.');
            loadMedicine();
        } catch (err) {
            showFormError('edit-error', 'Something went wrong. Please try again.');
            console.error(err);
        }
    });

    // --- Update Stock ---
    document.getElementById('restockForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        clearFormError('restock-error');

        const form = e.target;
        const payload = {
            medicine_id: medicineId,
            txn_type: form.txn_type.value,
            quantity: form.quantity.value,
            unit_cost: form.unit_cost.value || null,
            notes: form.notes.value || null,
        };

        try {
            const res = await fetch('/api/inventory/stock', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${authToken()}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (res.status === 401) {
                window.location.href = '/admin/login';
                return;
            }

            const json = await res.json();

            if (!res.ok) {
                const firstError = json.errors ? Object.values(json.errors)[0][0] : (json.message || 'Failed to log stock transaction.');
                showFormError('restock-error', firstError);
                return;
            }

            closeModal('restockModal');
            showMessage('Stock transaction logged.');
            loadMedicine();
        } catch (err) {
            showFormError('restock-error', 'Something went wrong. Please try again.');
            console.error(err);
        }
    });

    // --- Delete Medicine ---
    document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
        clearFormError('delete-error');
        const btn = document.getElementById('confirmDeleteBtn');
        btn.disabled = true;

        try {
            const res = await fetch(`/api/inventory/medicines/${medicineId}`, {
                method: 'DELETE',
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

            if (!res.ok) {
                // If this medicine has stock or price-history rows, a foreign-key
                // constraint on the DB side could surface here as a 500 — the API's
                // destroy() doesn't clean those up first. Surfacing the raw message
                // rather than guessing at a friendlier one, since the real cause
                // hasn't been confirmed.
                showFormError('delete-error', json.message || 'Failed to delete medicine.');
                btn.disabled = false;
                return;
            }

            window.location.href = "{{ route('admin.inventory') }}";
        } catch (err) {
            showFormError('delete-error', 'Something went wrong. Please try again.');
            btn.disabled = false;
            console.error(err);
        }
    });

    loadMedicine();
    loadPriceHistory();
})();
</script>
@stop
