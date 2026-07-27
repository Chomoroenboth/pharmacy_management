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
    .sale-row.is-filtered-out { display: none; } .client-no-results { display: none; } .client-no-results.is-visible { display: table-row; } .client-no-results td { padding: 35px; color: #6a746f; text-align: center; }
    .table-footer { display: flex; align-items: center; justify-content: space-between; padding: 16px 17px; color: #58655e; font-size: 13px; } .pages { display: flex; gap: 8px; } .pages span { display: grid; width: 32px; height: 32px; place-items: center; border: 1px solid #cbd6d0; border-radius: 4px; } .pages .selected { border-color: #00805e; color: #00805e; }
    @media (max-width: 850px) { .records-card { overflow-x: auto; } .filters { min-width: 720px; } .records-table { min-width: 820px; } } @media (max-width: 560px) { .page-heading h1 { font-size: 27px; } }
</style>
@endsection

@section('content')
<div class="page-heading"><h1>Sales Records</h1><p>Manage and review all sales transactions.</p></div>
<section class="records-card">
    <form class="filters" id="sales-filter-form" method="GET" action="{{ route('admin.sales.index') }}">
        <div class="filter-group"><label for="sale-search">Search sale or customer</label><input id="sale-search" name="search" value="{{ $filters['search'] }}" placeholder="Sale ID or customer"></div>
        <div class="filter-group"><label for="sale-status">Payment Status</label><select id="sale-status" name="status"><option value="">All Statuses</option><option value="paid" @selected($filters['status'] === 'paid')>Paid</option><option value="pending" @selected($filters['status'] === 'pending')>Pending</option><option value="unpaid" @selected($filters['status'] === 'unpaid')>Unpaid</option></select></div>
        <button class="filter-button" type="submit">Filter Records</button>
    </form>
    <table class="records-table"><thead><tr><th>Sale ID</th><th>Customer</th><th>Date</th><th class="amount">Total</th><th>Payment Status</th><th>Actions</th></tr></thead><tbody>
    @forelse($sales as $sale)<tr class="sale-row" data-sale-id="{{ $sale->sale_id }}" data-customer="{{ strtolower($sale->customer_name) }}" data-status="{{ $sale->payment_status }}"><td><a class="sale-id" href="{{ route('admin.sales.show', $sale->sale_id) }}">#{{ $sale->sale_id }}</a></td><td><div>{{ $sale->customer_name }}</div><div class="muted">{{ $sale->customer_id ? 'ID: ' . $sale->customer_id : 'N/A' }}</div></td><td><div>{{ \Carbon\Carbon::parse($sale->date)->format('M d, Y') }}</div><div class="muted">{{ $sale->time }}</div></td><td class="amount">${{ number_format($sale->total, 2) }}</td><td><span class="status {{ $sale->payment_status }}">{{ ucfirst($sale->payment_status) }}</span></td><td><a class="view-button" href="{{ route('admin.sales.show', $sale->sale_id) }}">View</a></td></tr>@empty<tr><td class="empty" colspan="6">No sales records match your filters.</td></tr>@endforelse
    <tr class="client-no-results" id="sales-no-results"><td colspan="6">No sales records match your filters.</td></tr>
    </tbody></table>
    <div class="table-footer"><span>Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of {{ $pagination['total'] }} entries</span><div class="pages"><span>‹</span><span class="selected">1</span><span>2</span><span>3</span><span>›</span></div></div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('sales-filter-form');
    const searchInput = document.getElementById('sale-search');
    const statusSelect = document.getElementById('sale-status');
    const rows = document.querySelectorAll('.sale-row');
    const noResults = document.getElementById('sales-no-results');
    const normalize = (value) => value.toLowerCase().replace(/[^a-z0-9]/g, '');
    const filterRows = () => {
        const term = searchInput.value.trim().toLowerCase();
        const saleTerm = normalize(term);
        let visible = 0;
        rows.forEach(function (row) {
            const matchesSearch = normalize(row.dataset.saleId).includes(saleTerm) || row.dataset.customer.includes(term);
            const matchesStatus = statusSelect.value === '' || row.dataset.status === statusSelect.value;
            row.classList.toggle('is-filtered-out', !(matchesSearch && matchesStatus));
            if (matchesSearch && matchesStatus) visible++;
        });
        noResults.classList.toggle('is-visible', rows.length > 0 && visible === 0);
    };
    searchInput.addEventListener('input', filterRows);
    statusSelect.addEventListener('change', filterRows);
    form.addEventListener('submit', function (event) { event.preventDefault(); filterRows(); });
});
</script>
@endsection
