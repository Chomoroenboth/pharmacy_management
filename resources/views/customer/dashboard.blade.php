@extends('layouts.customer-app')
@section('title', 'Customer Dashboard')
@section('page-css')
<style>
    .profile-grid { display: flex; gap: 40px; margin-top: 8px; }
    .profile-field { flex: 1; }
    .field-label { font-size: 12px; color: #3c4a42; margin-bottom: 8px; }
    .field-value { font-size: 16px; color: #191c1e; }
    .quick-actions { display: flex; gap: 16px; margin-top: 8px; }
    .btn-primary {
        background: #10b981; color: #fff; padding: 11px 24px;
        border-radius: 6px; font-size: 14px; font-weight: 500;
        text-decoration: none;
    }
    .btn-outline {
        background: #fff; color: #10b981; border: 1px solid #10b981;
        padding: 10px 24px; border-radius: 6px; font-size: 14px; font-weight: 500;
        text-decoration: none;
    }
    .purchases-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    .purchases-table th {
        background: #f3f4f6; text-align: left; padding: 12px 16px;
        font-size: 12px; color: #3c4a42; font-weight: 500;
    }
    .purchases-table td {
        padding: 16px; font-size: 14px; border-bottom: 1px solid #e1e2e4;
    }
    .badge-paid { background: rgba(16,185,129,0.2); color: #10b981; padding: 4px 8px; border-radius: 20px; font-size: 12px; }
    .badge-unpaid { background: rgba(220,38,38,0.15); color: #dc2626; padding: 4px 8px; border-radius: 20px; font-size: 12px; }
    .view-link { color: #10b981; font-size: 13px; text-decoration: none; }
    .loading-text { color: #6b7280; padding: 16px 0; }
</style>

@vite(['resources/js/app.js'])
<script>
document.addEventListener('DOMContentLoaded', function () {
    loadProfile();
    loadPurchases();
});

async function loadProfile() {
    try {
        const response = await window.axios.get('/api/profile');
        const user = response.data.data;

        document.getElementById('profileName').textContent = `${user.first_name} ${user.last_name ?? ''}`.trim();
        document.getElementById('profilePhone').textContent = user.phone_number ?? '—';
        document.getElementById('profileEmail').textContent = user.email;
    } catch (err) {
        if (err.response?.status === 401) {
            window.location.href = "{{ route('customer.login') }}";
        }
        console.error('Failed to load profile:', err);
    }
}

async function loadPurchases() {
    const statusEl = document.getElementById('purchasesStatus');
    const tbody = document.getElementById('purchasesBody');

    try {
        const response = await window.axios.get('/api/shop/sales', { params: { per_page: 10 } });
        const sales = response.data.data;

        if (!sales.length) {
            statusEl.textContent = 'No purchases yet.';
            return;
        }

        statusEl.style.display = 'none';

        tbody.innerHTML = sales.map(function (sale) {
            const badgeClass = sale.payment_status === 'paid' ? 'badge-paid' : 'badge-unpaid';
            const date = new Date(sale.sale_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
            const detailUrl = "{{ route('customer.purchase.detail', '__ID__') }}".replace('__ID__', sale.sale_id);

            return `
                <tr>
                    <td>${date}</td>
                    <td>${sale.display_id}</td>
                    <td>$${Number(sale.total_price).toFixed(2)}</td>
                    <td><span class="${badgeClass}">${sale.payment_status.charAt(0).toUpperCase() + sale.payment_status.slice(1)}</span></td>
                    <td><a href="${detailUrl}" class="view-link">View</a></td>
                </tr>
            `;
        }).join('');

    } catch (err) {
        if (err.response?.status === 401) {
            window.location.href = "{{ route('customer.login') }}";
            return;
        }
        statusEl.textContent = 'Failed to load purchases.';
        console.error('Failed to load purchases:', err);
    }
}
</script>
@endsection
@section('content')
    <div class="page-title">Customer Dashboard</div>

    <div class="card">
        <div class="card-title">Profile Overview</div>
        <div class="profile-grid">
            <div class="profile-field">
                <div class="field-label">FULL NAME</div>
                <div class="field-value" id="profileName">Loading...</div>
            </div>
            <div class="profile-field">
                <div class="field-label">PHONE</div>
                <div class="field-value" id="profilePhone">Loading...</div>
            </div>
            <div class="profile-field">
                <div class="field-label">EMAIL ADDRESS</div>
                <div class="field-value" id="profileEmail">Loading...</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Quick Actions</div>
        <div class="quick-actions">
            <a href="{{ route('customer.otc-shop') }}" class="btn-primary">Browse OTC Medicines</a>
            <a href="{{ route('customer.prescriptions') }}" class="btn-outline">My Prescriptions</a>
        </div>
    </div>

    <div class="card">
        <div class="card-title">Recent Purchases</div>
        <p id="purchasesStatus" class="loading-text">Loading purchases...</p>
        <table class="purchases-table">
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>SALE ID</th>
                    <th>TOTAL</th>
                    <th>STATUS</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="purchasesBody"></tbody>
        </table>
    </div>
@stop
