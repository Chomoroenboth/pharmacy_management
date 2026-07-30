@extends('layouts.admin-app')

@section('title', 'Payments')

@section('page-css')
<style>
    .page-heading { margin-bottom: 26px; } .page-heading h1 { margin: 0; font-size: 31px; letter-spacing: -.7px; } .page-heading p { margin: 9px 0 0; color: #4d5853; font-size: 16px; }
    .filter-card, .payment-card { width: 100%; background: #fff; border-radius: 9px; box-shadow: 0 2px 9px rgba(20,48,36,.05); box-sizing: border-box; }
    .filter-card { display: flex; align-items: end; gap: 16px; margin-bottom: 22px; padding: 28px 32px; }
    .filter-group { display: grid; gap: 8px; }
    .filter-group label { font-size: 12px; font-weight: 750; letter-spacing: .45px; color: #44534c; }
    .filter-group input, .filter-group select { height: 41px; min-width: 220px; padding: 0 12px; border: 1px solid #c7d2cc; border-radius: 7px; font: inherit; font-size: 14px; }
    .filter-group select { min-width: 160px; }
    .apply { height: 41px; padding: 0 21px; border: 0; border-radius: 6px; background: #10b981; color: #fff; font-weight: 700; cursor: pointer; }
    .payment-card { overflow: hidden; }
    .payment-card h2 { margin: 0; padding: 34px 32px 18px; font-size: 18px; }
    .payment-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    .payment-table th { padding: 15px 24px; background: #f1f3f4; color: #44534c; font-size: 12px; text-align: left; }
    .payment-table td { padding: 18px 24px; border-bottom: 1px solid #e7ebe9; font-size: 14px; }
    .payment-id { color: #38433e; }
    .sale-id { color: #7b8580; }
    .status { display: inline-block; padding: 4px 9px; border-radius: 4px; font-size: 12px; }
    .paid { color: #167658; background: #d9f4e9; }
    .pending { color: #59625e; background: #ebedef; }
    .unpaid { color: #b42318; background: #fee4e2; }
    .footer { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; color: #58655e; font-size: 13px; }
    .pages { display: flex; gap: 8px; }
    .page-btn { display: grid; min-width: 32px; height: 32px; place-items: center; border: 1px solid #cbd6d0; border-radius: 4px; background: #fff; color: #58655e; font-size: 13px; cursor: pointer; }
    .page-btn.active { color: #00805e; border-color: #00805e; font-weight: 700; }
    .page-btn:disabled { opacity: .4; cursor: not-allowed; }
    @media (max-width: 850px) { .filter-card, .payment-card { overflow-x: auto; } .payment-table { min-width: 820px; } }
    @media (max-width: 560px) { .page-heading h1 { font-size: 27px; } }
</style>
@endsection

@section('content')
<div class="page-heading">
    <h1>Payments</h1>
    <p>Manage and track all transaction records.</p>
</div>

<form class="filter-card" id="payment-filter-form" onsubmit="return false;">
    <div class="filter-group">
        <label for="payment-search">Search Customer or ID</label>
        <input id="payment-search" placeholder="e.g. PAY-0012 or John Doe">
    </div>
    <div class="filter-group">
        <label for="payment-status">Status</label>
        <select id="payment-status">
            <option value="">All Statuses</option>
            <option value="paid">Paid</option>
            <option value="pending">Pending</option>
            <option value="unpaid">Unpaid</option>
        </select>
    </div>
    <button class="apply" type="submit" id="apply-btn">Apply</button>
</form>

<section class="payment-card">
    <h2>Transaction Records</h2>
    <table class="payment-table">
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Sale ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody id="payments-table-body">
            <tr><td colspan="7" style="padding:34px;text-align:center;color:#66716b">Loading...</td></tr>
        </tbody>
    </table>
    <div class="footer">
        <span id="payments-showing-text"></span>
        <div class="pages" id="payments-pagination"></div>
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

    function methodLabel(method) {
        const map = { cash: 'Cash', credit_card: 'Credit Card', debit_card: 'Debit Card', transfer: 'Bank Transfer' };
        return map[method] ?? method;
    }

    function renderRows(payments) {
        const tbody = document.getElementById('payments-table-body');

        if (!payments.length) {
            tbody.innerHTML = '<tr><td colspan="7" style="padding:34px;text-align:center;color:#66716b">No payment records match your filters.</td></tr>';
            return;
        }

        tbody.innerHTML = payments.map(p => {
            const d = new Date(p.payment_date);
            const dateLabel = d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
            return `
                <tr>
                    <td class="payment-id">${p.display_id}</td>
                    <td class="sale-id">${p.sale_display_id}</td>
                    <td>${p.customer_name}</td>
                    <td>$${parseFloat(p.total_amount).toFixed(2)}</td>
                    <td>${methodLabel(p.payment_method)}</td>
                    <td><span class="status ${p.status}">${p.status.charAt(0).toUpperCase() + p.status.slice(1)}</span></td>
                    <td>${dateLabel}</td>
                </tr>
            `;
        }).join('');
    }

    function renderPagination(meta) {
        document.getElementById('payments-showing-text').textContent =
            meta.total === 0
                ? 'Showing 0 of 0 entries'
                : `Showing ${((meta.current_page - 1) * meta.per_page) + 1} to ${Math.min(meta.current_page * meta.per_page, meta.total)} of ${meta.total} entries`;

        const pag = document.getElementById('payments-pagination');
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
                    loadPayments(page);
                }
            });
        });
    }

    async function loadPayments(page = 1) {
        try {
            const params = new URLSearchParams({ per_page: perPage, page: page });
            if (currentSearch) params.set('search', currentSearch);
            if (currentStatus) params.set('status', currentStatus);

            const res = await fetch(`/api/shop/payments?${params.toString()}`, {
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
            document.getElementById('payments-table-body').innerHTML =
                '<tr><td colspan="7" style="padding:34px;text-align:center;color:#66716b">Failed to load payments.</td></tr>';
            console.error(err);
        }
    }

    document.getElementById('payment-search').addEventListener('input', (e) => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(() => {
            currentSearch = e.target.value.trim();
            loadPayments(1);
        }, 350);
    });

    document.getElementById('payment-status').addEventListener('change', (e) => {
        currentStatus = e.target.value;
        loadPayments(1);
    });

    document.getElementById('apply-btn').addEventListener('click', () => {
        clearTimeout(searchDebounce);
        currentSearch = document.getElementById('payment-search').value.trim();
        currentStatus = document.getElementById('payment-status').value;
        loadPayments(1);
    });

    loadPayments();
})();
</script>
@stop
