@extends('layouts.admin-app')

@section('title', 'Sale Details')

@section('page-css')
<style>
    .sale-details-card { max-width: 900px; margin: 24px auto; padding: 32px; background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0, 0, 0, .08); }
    .sale-header { padding-bottom: 24px; border-bottom: 1px solid #e5e9e7; }
    .sale-header h1 { margin: 0; color: #191c1e; font-size: 25px; }
    .sale-header p { margin: 9px 0 0; color: #56625c; font-size: 14px; }
    .section-title { margin: 30px 0 18px; color: #191c1e; font-size: 18px; }
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table th { padding: 13px 16px; background: #f1f3f4; color: #46524c; font-size: 12px; text-align: left; text-transform: uppercase; }
    .items-table th:not(:first-child), .items-table td:not(:first-child) { text-align: right; }
    .items-table td { padding: 16px; border-bottom: 1px solid #e5e9e7; color: #29312d; font-size: 14px; }
    .payment-summary { margin-top: 30px; padding-top: 4px; border-top: 1px solid #e5e9e7; }
    .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; padding: 18px 0 0; }
    .summary-item { display: grid; gap: 7px; }
    .summary-label { color: #58655e; font-size: 12px; font-weight: 700; text-transform: uppercase; }
    .summary-value { color: #191c1e; font-size: 17px; font-weight: 650; }
    .status { display: inline-block; width: fit-content; padding: 4px 9px; border-radius: 4px; color: #167658; background: #d9f4e9; font-size: 12px; font-weight: 600; }
    .detail-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px; padding-top: 22px; border-top: 1px solid #e5e9e7; }
    .action-button { padding: 10px 22px; border: 1px solid #b7c5bf; border-radius: 5px; background: #fff; color: #33423b; font-size: 14px; font-weight: 650; text-decoration: none; cursor: pointer; }
    .action-button.print { border-color: #10b981; color: #00805e; }
    @media (max-width: 620px) { .sale-details-card { margin: 0; padding: 22px 16px; } .items-table { min-width: 600px; } .items-wrap { overflow-x: auto; } .summary-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<article class="sale-details-card">
    <header class="sale-header">
        <h1>Sale Details — #{{ $sale->sale_id }}</h1>
        <p>{{ \Carbon\Carbon::parse($sale->date)->format('M d, Y') }} at {{ $sale->time }}</p>
        <p>Customer: {{ $sale->customer_name }}</p>
    </header>

    <section>
        <h2 class="section-title">Items Purchased</h2>
        <div class="items-wrap">
            <table class="items-table">
                <thead><tr><th>Medicine</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead>
                <tbody>
                    @foreach($items as $item)
                        <tr><td>{{ $item->medicine_name }}</td><td>{{ $item->quantity }}</td><td>${{ number_format($item->unit_price, 2) }}</td><td>${{ number_format($item->total, 2) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section class="payment-summary">
        <h2 class="section-title">Payment Summary</h2>
        <div class="summary-grid">
            <div class="summary-item"><span class="summary-label">Total</span><span class="summary-value">${{ number_format($sale->total, 2) }}</span></div>
            <div class="summary-item"><span class="summary-label">Method</span><span class="summary-value">{{ $sale->payment_method }}</span></div>
            <div class="summary-item"><span class="summary-label">Status</span><span class="status">{{ ucfirst($sale->payment_status) }}</span></div>
        </div>
    </section>

    <footer class="detail-actions">
        <button class="action-button print" type="button">Print Receipt</button>
        <a class="action-button" href="{{ route('admin.sales.index') }}">Cancel</a>
    </footer>
</article>
@endsection
