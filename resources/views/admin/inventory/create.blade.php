@extends('layouts.admin-app')

@section('title', 'Add New Medicine')

@section('page-css')
<style>
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; }
    .modal-box { background: #fff; border-radius: 8px; width: 100%; max-width: 480px; padding: 32px; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h2 { font-size: 20px; }
    .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; text-decoration: none; color: #191c1e; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #3c4a42; margin-bottom: 6px; }
    .form-group input, .form-group select {
        width: 100%; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; font-family: inherit;
    }
    .checkbox-row { display: flex; align-items: center; gap: 8px; }
    .checkbox-row input { width: auto; }
    .checkbox-row label { margin-bottom: 0; }
    .price-input-wrap { display: flex; align-items: center; border: 1px solid #d7d9dc; border-radius: 6px; padding: 0 12px; }
    .price-input-wrap span { color: #6c7a71; font-size: 14px; }
    .price-input-wrap input { border: none; padding: 10px 8px; flex: 1; }
    .field-error { color: #ba1a1a; font-size: 12px; margin-top: 4px; }
    .form-error-banner { color: #ba1a1a; font-size: 13px; margin-bottom: 16px; display: none; }
    .form-error-banner.show { display: block; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
    .btn-cancel { background: #fff; border: 1px solid #d7d9dc; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; color: #191c1e; }
    .btn-save { background: #10b981; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
    .btn-save:disabled { opacity: 0.6; cursor: default; }

    .search-wrap { position: relative; }
    .search-results { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid #d7d9dc; border-radius: 6px; max-height: 200px; overflow-y: auto; z-index: 10; display: none; }
    .search-results.open { display: block; }
    .search-result-item { padding: 10px 12px; font-size: 14px; cursor: pointer; border-bottom: 1px solid #f3f4f6; }
    .search-result-item:last-child { border-bottom: none; }
    .search-result-item:hover { background: #f3f4f6; }
    .search-result-item .srm-name { font-weight: 600; }
    .search-result-item .srm-meta { font-size: 12px; color: #6c7a71; margin-top: 2px; }
    .search-result-empty { padding: 10px 12px; font-size: 13px; color: #6c7a71; }
    .existing-note { font-size: 12px; color: #92400e; background: #fef3c7; padding: 6px 10px; border-radius: 6px; margin-top: 6px; display: none; }
    .existing-note.show { display: block; }
</style>
@endsection

@section('content')

    <div class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Add New Medicine</h2>
                <a href="{{ route('admin.inventory') }}" class="modal-close">&times;</a>
            </div>

            <div class="form-error-banner" id="form-error-banner"></div>

            <form id="addMedicineForm">
                <div class="form-group search-wrap">
                    <label>Medicine Name</label>
                    <input type="text" id="f-medicine_name" placeholder="e.g., Amoxicillin 500mg" autocomplete="off">
                    <div class="search-results" id="medNameResults"></div>
                    <div class="field-error" id="err-medicine_name"></div>
                    <div class="existing-note" id="existingNote"></div>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select id="f-category">
                        <option value="">Select category</option>
                        <option>Antibiotics</option>
                        <option>Painkillers</option>
                        <option>Cardiovascular</option>
                        <option>Vitamins</option>
                        <option>Gastrointestinal</option>
                    </select>
                    <div class="field-error" id="err-category"></div>
                </div>

                <div class="form-group">
                    <label>Brand</label>
                    <input type="text" id="f-brand" placeholder="e.g., PharmaCorp">
                    <div class="field-error" id="err-brand"></div>
                </div>

                <div class="form-group">
                    <label>Price ($)</label>
                    <div class="price-input-wrap">
                        <span>$</span>
                        <input type="number" step="0.01" id="f-price" placeholder="0.00">
                    </div>
                    <div class="field-error" id="err-price"></div>
                </div>

                {{-- Added: the API accepts requires_prescription, but the original
                     form had no field for it, so every medicine would silently
                     default to "No". Flagging this addition rather than assuming
                     the omission was intentional. --}}
                <div class="form-group checkbox-row">
                    <input type="checkbox" id="f-requires_prescription">
                    <label for="f-requires_prescription">Requires Prescription</label>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('admin.inventory') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-save" id="submitBtn">Save</button>
                </div>
            </form>
        </div>
    </div>

@stop

@section('page-js')
<script>
(function () {
    function authToken() {
        return localStorage.getItem('auth_token');
    }

    function clearErrors() {
        document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
        const banner = document.getElementById('form-error-banner');
        banner.textContent = '';
        banner.classList.remove('show');
    }

    // --- Live medicine-name search (duplicate check + autofill convenience) ---
    // Reuses the same /api/inventory/medicines?search= endpoint that already
    // powers the working search box on the Inventory list page.
    let medSearchTimer = null;

    const nameInput = document.getElementById('f-medicine_name');
    const resultsBox = document.getElementById('medNameResults');
    const existingNote = document.getElementById('existingNote');

    nameInput.addEventListener('input', function () {
        const query = this.value.trim();
        existingNote.classList.remove('show');
        clearTimeout(medSearchTimer);

        if (!query) {
            resultsBox.classList.remove('open');
            return;
        }

        medSearchTimer = setTimeout(() => {
            fetch(`/api/inventory/medicines?search=${encodeURIComponent(query)}&per_page=6`, {
                headers: {
                    'Authorization': `Bearer ${authToken()}`,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(json => {
                const items = json.data || [];

                if (!items.length) {
                    resultsBox.innerHTML = '<div class="search-result-empty">No matching medicines found.</div>';
                } else {
                    resultsBox.innerHTML = items.map(m => `
                        <div class="search-result-item"
                             onclick='selectExistingMedicine(${JSON.stringify(m)})'>
                            <div class="srm-name">${m.medicine_name}</div>
                            <div class="srm-meta">${m.display_id} • ${m.category ?? 'No category'} • $${parseFloat(m.price).toFixed(2)}</div>
                        </div>
                    `).join('');
                }
                resultsBox.classList.add('open');
            })
            .catch(() => {
                resultsBox.classList.remove('open');
            });
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!resultsBox.contains(e.target) && e.target !== nameInput) {
            resultsBox.classList.remove('open');
        }
    });

    window.selectExistingMedicine = function (m) {
        nameInput.value = m.medicine_name;
        document.getElementById('f-brand').value = m.brand ?? '';
        document.getElementById('f-price').value = m.price;

        const categorySelect = document.getElementById('f-category');
        const hasOption = Array.from(categorySelect.options).some(opt => opt.value === m.category);
        if (m.category && hasOption) {
            categorySelect.value = m.category;
        }

        existingNote.textContent = `Already in inventory as ${m.display_id} — saving will add this as a separate entry, not edit the existing one.`;
        existingNote.classList.add('show');
        resultsBox.classList.remove('open');
    };

    document.getElementById('addMedicineForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        clearErrors();

        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;

        const payload = {
            medicine_name: document.getElementById('f-medicine_name').value,
            category: document.getElementById('f-category').value,
            brand: document.getElementById('f-brand').value,
            price: document.getElementById('f-price').value,
            requires_prescription: document.getElementById('f-requires_prescription').checked,
        };

        try {
            const res = await fetch('/api/inventory/medicines', {
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

            if (res.status === 422 && json.errors) {
                Object.keys(json.errors).forEach(field => {
                    const el = document.getElementById(`err-${field}`);
                    if (el) el.textContent = json.errors[field][0];
                });
                submitBtn.disabled = false;
                return;
            }

            if (!res.ok) {
                const banner = document.getElementById('form-error-banner');
                banner.textContent = json.message || 'Failed to add medicine.';
                banner.classList.add('show');
                submitBtn.disabled = false;
                return;
            }

            window.location.href = "{{ route('admin.inventory') }}";
        } catch (err) {
            const banner = document.getElementById('form-error-banner');
            banner.textContent = 'Something went wrong. Please try again.';
            banner.classList.add('show');
            submitBtn.disabled = false;
            console.error(err);
        }
    });
})();
</script>
@stop
