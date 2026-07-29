@extends('layouts.customer-app')

@section('title', 'OTC Medicine Shop')

@section('page-css')
<style>
    .category-tabs { display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap; }
    .category-tab {
        padding: 8px 18px; border-radius: 20px; font-size: 13px;
        border: 1px solid #e1e2e4; color: #3c4a42; text-decoration: none;
        background: #fff; cursor: pointer;
    }
    .category-tab.active { background: #10b981; color: #fff; border-color: #10b981; }

    .medicine-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .medicine-card {
        background: #fff; border-radius: 8px; padding: 20px;
        box-shadow: 0px 1px 3px rgba(0,0,0,0.06);
    }
    .med-category-tag { color: #10b981; font-size: 11px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px; }
    .med-title { font-size: 16px; font-weight: 600; color: #191c1e; margin-bottom: 4px; }
    .med-brand { font-size: 13px; color: #6b7280; margin-bottom: 16px; }
    .med-footer { display: flex; align-items: center; justify-content: space-between; }
    .med-price { font-size: 18px; font-weight: 700; color: #191c1e; }
    .btn-add-cart {
        background: #10b981; color: #fff; border: none;
        padding: 8px 16px; border-radius: 6px; font-size: 13px;
        font-weight: 600; cursor: pointer;
    }
    .btn-add-cart:disabled { background: #9ca3af; cursor: not-allowed; }
    .loading-text, .empty-text { color: #6b7280; padding: 20px 0; }
    .out-of-stock { color: #dc2626; font-size: 12px; font-weight: 600; margin-top: 4px; }
</style>

@vite(['resources/js/app.js'])
<script>
document.addEventListener('DOMContentLoaded', function () {
    const grid = document.getElementById('medicineGrid');
    const statusEl = document.getElementById('shopStatus');
    const tabs = document.querySelectorAll('.category-tab');

    async function loadMedicines(category) {
        statusEl.textContent = 'Loading medicines...';
        statusEl.style.display = 'block';
        grid.innerHTML = '';

        try {
            const params = {};
            if (category && category !== 'All Medicines') {
                params.category = category;
            }
            params.per_page = 50;

            const response = await window.axios.get('/api/inventory/medicines', { params });
            const medicines = response.data.data;

            if (!medicines.length) {
                statusEl.textContent = 'No medicines found in this category.';
                return;
            }

            statusEl.style.display = 'none';

            grid.innerHTML = medicines.map(function (med) {
                const outOfStock = med.current_stock <= 0;
                return `
                    <div class="medicine-card">
                        <div class="med-category-tag">${med.category ?? ''}</div>
                        <div class="med-title">${med.medicine_name}</div>
                        <div class="med-brand">${med.brand ?? ''}</div>
                        ${outOfStock ? '<div class="out-of-stock">Out of stock</div>' : ''}
                        <div class="med-footer">
                            <div class="med-price">$${Number(med.price).toFixed(2)}</div>
                            <button
                                type="button"
                                class="btn-add-cart"
                                data-medicine-id="${med.medicine_id}"
                                ${outOfStock ? 'disabled' : ''}
                            >Add to Cart</button>
                        </div>
                    </div>
                `;
            }).join('');

        } catch (err) {
            if (err.response?.status === 401) {
                window.location.href = "{{ route('customer.login') }}";
                return;
            }
            statusEl.textContent = 'Failed to load medicines. Please try again.';
            console.error('Failed to load medicines:', err);
        }
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            loadMedicines(tab.dataset.category);
        });
    });

   grid.addEventListener('click', async function (e) {
        if (!e.target.matches('.btn-add-cart')) return;

        const btn = e.target;
        const medicineId = btn.dataset.medicineId;
        const originalText = btn.textContent;

        btn.disabled = true;
        btn.textContent = 'Adding...';

        try {
            await window.axios.post('/api/cart', {
                medicine_id: medicineId,
                quantity: 1,
            });

            btn.textContent = 'Added ✓';
            setTimeout(function () {
                btn.textContent = originalText;
                btn.disabled = false;
            }, 1200);

        } catch (err) {
            if (err.response?.status === 401) {
                window.location.href = "{{ route('customer.login') }}";
                return;
            }
            btn.textContent = 'Failed - retry';
            btn.disabled = false;
            console.error('Add to cart failed:', err);
        }
    });

    loadMedicines('All Medicines');
});
</script>
@endsection

@section('content')

    <div class="page-title">OTC Medicine Shop</div>
    <div class="page-subtitle">Browse our curated selection of over-the-counter medications.</div>

    <div class="category-tabs" id="categoryTabs">
        <button class="category-tab active" data-category="All Medicines">All Medicines</button>
        <button class="category-tab" data-category="Pain Relief">Pain Relief</button>
        <button class="category-tab" data-category="Vitamins &amp; Supplements">Vitamins &amp; Supplements</button>
        <button class="category-tab" data-category="Cold &amp; Flu">Cold &amp; Flu</button>
        <button class="category-tab" data-category="Digestive Health">Digestive Health</button>
        <button class="category-tab" data-category="First Aid">First Aid</button>
        <button class="category-tab" data-category="Allergy">Allergy</button>
    </div>

    <p id="shopStatus" class="loading-text">Loading medicines...</p>

    <div class="medicine-grid" id="medicineGrid"></div>

@stop
