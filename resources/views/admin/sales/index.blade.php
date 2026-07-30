@extends('layouts.admin-app')

@section('title', 'Sales Records')

@section('page-css')
<style>
    .page-heading { margin-bottom: 26px; } .page-heading h1 { margin: 0; font-size: 31px; letter-spacing: -.7px; } .page-heading p { margin: 9px 0 0; color: #4d5853; font-size: 16px; }
    .records-card { max-width: 920px; overflow: hidden; background: #fff; border: 1px solid #e0e5e2; border-radius: 10px; box-shadow: 0 2px 8px rgba(20, 48, 36, .05); }
    .filters { display: flex; align-items: end; gap: 16px; padding: 25px 24px; border-bottom: 1px solid #e7ebe9; } .filter-group { display: grid; gap: 8px; } .filter-group label { color: #44534c; font-size: 12px; font-weight: 750; letter-spacing: .5px; text-transform: uppercase; }
    .filter-group input, .filter-group select { min-width: 180px; height: 40px; padding: 0 12px; border: 1px solid #bfcec7; border-radius: 7px; background: #fff; color: #38443e; font: inherit; font-size: 14px; } .filter-group select { min-width: 150px; }
    .filter-button { height: 40px; margin-left: auto; padding: 0 19px; border: 1px solid #aebfb7; background: #fff; color: #007c5a; font-size: 13px; font-weight: 700; cursor: pointer; }
    .records-table { width: 100%; border-collapse: collapse; } .records-table th { padding: 15px 24px; background: #f1f3f4; color: #44534c; font-size: 12px; letter-spacing: .4px; text-align: left; text-transform: uppercase; } .records-table td { padding: 17px 24px; border-bottom: 1px solid #e7ebe9; font-size: 14px; vertical-align: middle; }
    .sale-id { color: #00805e; font-weight: 650; text-decoration: none; } .muted { margin-top: 4px; color: #64716a; font-size: 12px; } .amount { text-align: right; } .status { display: inline-block; padding: 4px 9px; border-radius: 11px; font-size: 12px; } .paid { color: #167658; background: #d9f4e9; } .pending { color: #59625e; background: #ebedef; } .unpaid { color: #b42318; background: #fee4e2; }
    .view-button { display: inline-block; padding: 8px 15px; border: 1px solid #b9c8c1; border-radius: 4px; color: #176a50; font-size: 13px; font-weight: 700; text-decoration: none; } .empty { padding: 35px; color: #6a746f; text-align: center; }
    .table-footer { display: flex; align-items: center; justify-content: space-between; padding: 16px 17px; color: #58655e; font-size: 13px; } .pages { display: flex; gap: 8px; } .page-btn { display: grid; min-width: 32px; height: 32px; place-items: center; border: 1px solid #cbd6d0; border-radius: 4px; background: #fff; color: #58655e; font-size: 13px; cursor: pointer; } .page-btn.active { border-color: #00805e; color: #00805e; font-weight: 700; } .page-btn:disabled { opacity: .4; cursor: not-allowed; }
    @media (max-width: 850px) { .records-card { overflow-x: auto; } .filters { min-width: 720px; } .records-table { min-width: 820px; } } @media (max-width: 560px) { .page-heading h1 { font-size: 27px; } }
</style>
@endsection

@section('content')
<div class="page-heading"><h1>Sales Records</h1><p>Manage and review all sales transactions.</p></div>
<section class="records-card">
    <form class="filters" id="sales-filter-form" onsubmit="return false;">
        <div class="filter-group"><label for="sale-search">Search sale or customer</label><input id="sale-search" name="search" placeholder="Sale ID or customer"></div>
        <div class="filter-group"><label for="sale-status">Payment Status</label>
            <select id="sale-status" name="status">
                <option value="">All Statuses</option>
                <option value="paid">Paid</option>
                <option value="pending">Pending</option>
                <option value="unpaid">Unpaid</option>
            </select>
        </div>
        <button class="filter-button" type="submit" id="filter-btn">Filter Records</button>
    </form>
    <table class="records-table">
        <thead><tr><th>Sale ID</th><th>Customer</th><th>Date</th><th class="amount">Total</th><th>Payment Status</th><th>Actions</th></tr></thead>
        <tbody id="sales-table-body">
            <tr><td class="empty" colspan="6">Loading...</td></tr>
        </tbody>
    </table>
    <div class="table-footer">
        <span id="sales-showing-text"></span>
        <div class="pages" id="sales-pagination"></div>
    </div>
</section>
@stop

@section('page-js')
<script>
(function () {
    const perPage = 5;
    let currentSearch = '';
    let currentStatus = '';
    let searchDebounce;

    function authToken() {
        return localStorage.getItem('auth_token');
    }

    function formatDate(dateStr) {
        const d = new Date(dateStr);
        const dateLabel = d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
        const timeLabel = d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
        return { dateLabel, timeLabel };
    }

    function renderRows(sales) {
        const tbody = document.getElementById('sales-table-body');

        if (!sales.length) {
            tbody.innerHTML = '<tr><td class="empty" colspan="6">No sales records match your filters.</td></tr>';
            return;
        }

        tbody.innerHTML = sales.map(s => {
            const { dateLabel, timeLabel } = formatDate(s.sale_date);
            return `
                <tr>
                    <td><a class="sale-id" href="/admin/sales/${s.sale_id}">#${s.display_id}</a></td>
                    <td><div>${s.customer_name}</div><div class="muted">ID: ${s.customer_display_id}</div></td>
                    <td><div>${dateLabel}</div><div class="muted">${timeLabel}</div></td>
                    <td class="amount">$${parseFloat(s.total_price).toFixed(2)}</td>
                    <td><span class="status ${s.payment_status}">${s.payment_status.charAt(0).toUpperCase() + s.payment_status.slice(1)}</span></td>
                    <td><a class="view-button" href="/admin/sales/${s.sale_id}">View</a></td>
                </tr>
            `;
        }).join('');
    }

    function renderPagination(meta) {
        document.getElementById('sales-showing-text').textContent =
            meta.total === 0
                ? 'Showing 0 of 0 entries'
                : `Showing ${((meta.current_page - 1) * meta.per_page) + 1} to ${Math.min(meta.current_page * meta.per_page, meta.total)} of ${meta.total} entries`;

        const pag = document.getElementById('sales-pagination');
        let html = `<button class="page-btn" ${meta.current_page <= 1 ? 'disabled' : ''} data-page="${meta.current_page - 1}">&lsaquo;</button>`;

        for (let p = 1; p <= meta.last_page; p++) {
            html += `<button class="page-btn ${p === meta.current_page ? 'active' : ''}" data-page="${p}">${p}</button>`;
        }

        html += `<button class="page-btn" ${meta.current_page >= meta.last_page ? 'disabled' : ''} data-page="${meta.current_page + 1}">&rsaquo;</button>`;
        pag.innerHTML = html;

        pag.querySelectorAll('.page-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const page = parseInt(btn.dataset.page, 10);
                if (page >= 1 && page <= meta.last_page) {
                    loadSales(page);
                }
            });
        });
    }

    async function loadSales(page = 1) {
        try {
            const params = new URLSearchParams({ per_page: perPage, page: page });
            if (currentSearch) params.set('search', currentSearch);
            if (currentStatus) params.set('payment_status', currentStatus);

            const res = await fetch(`/api/shop/sales?${params.toString()}`, {
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
            document.getElementById('sales-table-body').innerHTML =
                '<tr><td class="empty" colspan="6">Failed to load sales records.</td></tr>';
            console.error(err);
        }
    }

    document.getElementById('sale-search').addEventListener('input', (e) => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => {
            currentSearch = e.target.value.trim();
            loadSales(1);
        }, 350);
    });

    document.getElementById('sale-status').addEventListener('change', (e) => {
        currentStatus = e.target.value;
        loadSales(1);
    });

    // Filter Records button still works too, e.g. after picking a status without typing
    document.getElementById('filter-btn').addEventListener('click', () => {
        clearTimeout(searchDebounce);
        currentSearch = document.getElementById('sale-search').value.trim();
        currentStatus = document.getElementById('sale-status').value;
        loadSales(1);
    });

    loadSales();
})();
</script>
@stop
