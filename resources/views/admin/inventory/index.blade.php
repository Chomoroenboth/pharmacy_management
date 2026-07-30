@extends('layouts.admin-app')

@section('title', 'Inventory')

@section('page-css')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
    .page-header h1 { font-size: 28px; }
    .page-header p { font-size: 14px; color: #6c7a71; margin-top: 4px; }
    .header-controls { display: flex; gap: 12px; align-items: center; }
    .search-input {
        padding: 10px 16px; border: 1px solid #d7d9dc; border-radius: 6px;
        font-size: 14px; width: 240px;
    }
    .category-select {
        padding: 10px 16px; border: 1px solid #d7d9dc; border-radius: 6px;
        font-size: 14px; background: #fff;
    }
    .btn-primary {
        background: #10b981; border: none; border-radius: 6px;
        padding: 10px 20px; font-size: 14px; font-weight: 600; color: #fff;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }

    .inv-table { width: 100%; border-collapse: collapse; }
    .inv-table th {
        background: #f3f4f6; text-align: left; padding: 14px 20px;
        font-size: 12px; color: #3c4a42; font-weight: 600;
    }
    .inv-table td { padding: 16px 20px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
    .med-name { font-weight: 600; }
    .stock-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
    .stock-high { background: #adedd3; color: #306d58; }
    .stock-medium { background: #fde68a; color: #92400e; }
    .stock-low { background: #ffdad6; color: #930a0a; }
    .view-link { color: #3c4a42; font-size: 15px; text-decoration: none; }

    .table-footer { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; font-size: 13px; color: #6c7a71; }
    .pagination { display: flex; gap: 6px; }
    .page-btn { min-width: 32px; height: 32px; border: 1px solid #e1e2e4; border-radius: 6px; background: #fff; color: #3c4a42; font-size: 13px; cursor: pointer; }
    .page-btn.active { background: #10b981; border-color: #10b981; color: #fff; font-weight: 600; }
    .page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
</style>
@endsection

@section('content')

    @if (session('message'))
        <div style="background:#d1fae5; color:#065f46; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
            {{ session('message') }}
        </div>
    @endif

    <div class="page-header">
        <div>
            <h1>Inventory</h1>
            <p>Manage medicine stock and categories</p>
        </div>
        <div class="header-controls">
            <input type="text" id="searchInput" class="search-input" placeholder="Search medicine...">
            <select id="categorySelect" class="category-select">
                <option value="">All Categories</option>
            </select>
            <a href="{{ route('admin.inventory.create') }}" class="btn-primary">+ Add Medicine</a>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock Level</th>
                    <th>View Medicine Details</th>
                </tr>
            </thead>
            <tbody id="inv-table-body">
                <tr><td colspan="7" style="text-align:center; padding:24px;">Loading...</td></tr>
            </tbody>
        </table>

        <div class="table-footer">
            <div id="inv-showing-text"></div>
            <div class="pagination" id="inv-pagination"></div>
        </div>
    </div>

@stop

@section('page-js')
<script>
(function () {
    const perPage = 5;
    let currentSearch = '';
    let currentCategory = '';
    let searchDebounce;
    const seenCategories = new Set();

    function authToken() {
        return localStorage.getItem('auth_token');
    }

    function stockClass(stock) {
        if (stock > 20) return 'stock-high';
        if (stock > 10) return 'stock-medium';
        return 'stock-low';
    }

    function renderRows(medicines) {
        const tbody = document.getElementById('inv-table-body');

        if (!medicines.length) {
            tbody.innerHTML = '<tr class="empty-row"><td colspan="7" style="text-align:center; padding:24px;">No medicines found.</td></tr>';
            return;
        }

        tbody.innerHTML = medicines.map(m => `
            <tr>
                <td>${m.display_id}</td>
                <td class="med-name">${m.medicine_name}</td>
                <td>${m.category ?? '-'}</td>
                <td>${m.brand ?? '-'}</td>
                <td>$${parseFloat(m.price).toFixed(2)}</td>
                <td><span class="stock-badge ${stockClass(m.current_stock)}">${m.current_stock} Units</span></td>
                <td><a href="/admin/inventory/${m.medicine_id}" class="view-link">&#128065;</a></td>
            </tr>
        `).join('');

        medicines.forEach(m => { if (m.category) seenCategories.add(m.category); });
        refreshCategoryOptions();
    }

    function refreshCategoryOptions() {
        const select = document.getElementById('categorySelect');
        const current = select.value;
        select.innerHTML = '<option value="">All Categories</option>' +
            [...seenCategories].sort().map(c => `<option value="${c}">${c}</option>`).join('');
        select.value = current;
    }

    function renderPagination(meta) {
        document.getElementById('inv-showing-text').textContent =
            meta.total === 0
                ? 'Showing 0 of 0 entries'
                : `Showing ${((meta.current_page - 1) * meta.per_page) + 1} to ${Math.min(meta.current_page * meta.per_page, meta.total)} of ${meta.total} entries`;

        const pag = document.getElementById('inv-pagination');
        let html = `<button class="page-btn" ${meta.current_page <= 1 ? 'disabled' : ''} data-page="${meta.current_page - 1}">Previous</button>`;

        for (let p = 1; p <= meta.last_page; p++) {
            html += `<button class="page-btn ${p === meta.current_page ? 'active' : ''}" data-page="${p}">${p}</button>`;
        }

        html += `<button class="page-btn" ${meta.current_page >= meta.last_page ? 'disabled' : ''} data-page="${meta.current_page + 1}">Next</button>`;
        pag.innerHTML = html;

        pag.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const page = parseInt(btn.dataset.page, 10);
                if (page >= 1 && page <= meta.last_page) {
                    loadInventory(page);
                }
            });
        });
    }

    async function loadInventory(page = 1) {
        try {
            const params = new URLSearchParams({
                per_page: perPage,
                page: page,
            });
            if (currentSearch) params.set('search', currentSearch);
            if (currentCategory) params.set('category', currentCategory);

            const res = await fetch(`/api/inventory/medicines?${params.toString()}`, {
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
            renderRows(json.data);
            renderPagination(json.meta);
        } catch (err) {
            document.getElementById('inv-table-body').innerHTML =
                '<tr class="empty-row"><td colspan="7" style="text-align:center; padding:24px; color:#930a0a;">Failed to load inventory.</td></tr>';
            console.error(err);
        }
    }

    document.getElementById('searchInput').addEventListener('input', (e) => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => {
            currentSearch = e.target.value;
            loadInventory(1);
        }, 350);
    });

    document.getElementById('categorySelect').addEventListener('change', (e) => {
        currentCategory = e.target.value;
        loadInventory(1);
    });

    loadInventory();
})();
</script>
@stop
