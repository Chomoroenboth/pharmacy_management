@extends('layouts.admin-app')

@section('title', 'Admin Dashboard')

@section('page-css')
<style>
    body { background: #f8f9fa; }

    .header { margin-bottom: 32px; }
    .header .title { color: #191c1e; font-size: 28px; font-weight: 600; letter-spacing: -0.5px; }
    .header .subtitle { color: #6b7280; font-size: 15px; margin-top: 6px; }

    .stat-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 32px; }
    .stat-card {
        background: #fff; border-radius: 12px; padding: 24px;
        border: 1px solid #eef0f2;
        transition: box-shadow 0.15s ease;
    }
    .stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .stat-card.alert { border-color: #f3d4d4; background: #fef8f8; }
    .stat-label {
        font-size: 12px; font-weight: 600; letter-spacing: 0.5px;
        color: #6b7280; text-transform: uppercase; margin-bottom: 12px;
    }
    .stat-label.danger { color: #b91c1c; }
    .stat-value { font-size: 30px; font-weight: 700; color: #111827; line-height: 1.2; }
    .stat-trend { font-size: 13px; color: #10b981; margin-top: 8px; font-weight: 500; }
    .stat-trend.danger { color: #b91c1c; }

    .tables-section { display: grid; grid-template-columns: 1.4fr 1fr; gap: 20px; }
    .table-card {
        background: #fff; border-radius: 12px;
        border: 1px solid #eef0f2; overflow: hidden;
    }
    .table-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #eef0f2;
    }
    .table-card-header h3 { color: #111827; font-size: 16px; font-weight: 600; margin: 0; }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead tr { background: #fafbfc; }
    .data-table th {
        text-align: left; padding: 12px 24px;
        font-size: 11px; font-weight: 600; letter-spacing: 0.5px;
        color: #6b7280; text-transform: uppercase;
    }
    .data-table td {
        padding: 14px 24px; font-size: 14px; color: #374151;
        border-top: 1px solid #f3f4f6;
    }
    .data-table tbody tr:hover { background: #fafbfc; }
    .med-name { color: #111827; font-size: 14px; font-weight: 500; }
    .med-category { color: #9ca3af; font-size: 12px; margin-top: 2px; }

    .badge {
        display: inline-flex; align-items: center;
        padding: 4px 10px; border-radius: 20px;
        font-size: 12px; font-weight: 500;
    }
    .badge-completed { background: #d1fae5; color: #047857; }
    .badge-pending { background: #fee2e2; color: #b91c1c; }

    .stock-low { color: #b91c1c; font-weight: 600; font-size: 14px; }
    .stock-medium { color: #b45309; font-weight: 600; font-size: 14px; }

    .loading-row td, .empty-row td { text-align: center; padding: 32px; color: #6c7a71; }
</style>
@endsection

@section('content')

    <div class="header">
        <div>
            <div class="title">Dashboard Overview</div>
            <div class="subtitle">Real-time pharmacy metrics and alerts.</div>
        </div>
    </div>

    <div class="stat-cards">
        <div class="stat-card">
            <div class="stat-label">Total Customers</div>
            <div class="stat-value" id="stat-total-customers">—</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Sales Today</div>
            <div class="stat-value" id="stat-total-sales">—</div>
        </div>
        <div class="stat-card alert">
            <div class="stat-label danger">Low Stock Alerts</div>
            <div class="stat-value" id="stat-low-stock-count">—</div>
            <div class="stat-trend danger">Requires immediate review</div>
        </div>
    </div>

    <div class="tables-section">

        <div class="table-card recent-sales">
            <div class="table-card-header">
                <h3>Recent Sales</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="recent-sales-body">
                    <tr class="loading-row"><td colspan="4">Loading...</td></tr>
                </tbody>
            </table>
        </div>

        <div class="table-card low-stock">
            <div class="table-card-header">
                <h3>Low Stock Alerts</h3>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Medication</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody id="low-stock-body">
                    <tr class="loading-row"><td colspan="2">Loading...</td></tr>
                </tbody>
            </table>
        </div>

    </div>

@stop

@section('page-js')
<script>
(function () {
    function authToken() {
        return localStorage.getItem('auth_token');
    }

    async function loadDashboard() {
        try {
            const res = await fetch('/api/dashboard', {
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
            const d = json.data;

            document.getElementById('stat-total-customers').textContent = d.total_customers.toLocaleString();
            document.getElementById('stat-total-sales').textContent = '$' + Number(d.total_sales_today).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('stat-low-stock-count').textContent = d.low_stock_count;

            renderRecentSales(d.recent_sales);
            renderLowStock(d.low_stock_alerts);
        } catch (err) {
            console.error(err);
        }
    }

    function renderRecentSales(sales) {
        const tbody = document.getElementById('recent-sales-body');

        if (!sales.length) {
            tbody.innerHTML = '<tr class="empty-row"><td colspan="4">No recent sales.</td></tr>';
            return;
        }

        tbody.innerHTML = sales.map(sale => {
            const badgeClass = sale.status === 'Completed' ? 'badge-completed' : 'badge-pending';
            return `
                <tr>
                    <td>#${String(sale.sale_id).padStart(4, '0')}</td>
                    <td>${sale.customer_name}</td>
                    <td>$${Number(sale.total_price).toFixed(2)}</td>
                    <td><span class="badge ${badgeClass}">${sale.status}</span></td>
                </tr>
            `;
        }).join('');
    }

    function renderLowStock(items) {
        const tbody = document.getElementById('low-stock-body');

        if (!items.length) {
            tbody.innerHTML = '<tr class="empty-row"><td colspan="2">No low stock items.</td></tr>';
            return;
        }

        tbody.innerHTML = items.map(item => {
            const stockClass = item.current_stock < 10 ? 'stock-low' : 'stock-medium';
            return `
                <tr>
                    <td>
                        <div class="med-name">${item.medicine_name}</div>
                        <div class="med-category">${item.category ?? ''}</div>
                    </td>
                    <td class="${stockClass}">${item.current_stock} units</td>
                </tr>
            `;
        }).join('');
    }

    loadDashboard();
})();
</script>
@endsection
