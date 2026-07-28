@extends('layouts.customer-app')

@section('title', 'Purchase Detail')

@section('page-css')
<style>
    .detail-card { background: #fff; border-radius: 8px; box-shadow: 0px 1px 3px rgba(0,0,0,0.06); padding: 32px; max-width: 700px; margin: 0 auto; }
    .back-link { color: #10b981; font-size: 13px; text-decoration: none; display: inline-block; margin-bottom: 16px; }
    .detail-title { font-size: 20px; font-weight: 600; margin-bottom: 2px; }
    .detail-subtitle { font-size: 13px; color: #6b7280; margin-bottom: 24px; }

    .info-row { display: flex; gap: 40px; padding: 16px 0; border-bottom: 1px solid #f3f4f6; margin-bottom: 20px; }
    .info-field .info-label { font-size: 11px; color: #9ca3af; text-transform: uppercase; margin-bottom: 4px; }
    .info-field .info-value { font-size: 14px; font-weight: 500; }
    .badge-paid { background: #d1fae5; color: #047857; padding: 3px 10px; border-radius: 12px; font-size: 12px; }

    .section-label { font-size: 13px; font-weight: 600; color: #6b7280; margin-bottom: 12px; text-transform: uppercase; }

    .rx-box { background: #f9fafb; border-radius: 6px; padding: 16px; margin-bottom: 24px; }

    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .items-table th { text-align: left; font-size: 12px; color: #9ca3af; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
    .items-table td { padding: 10px 0; font-size: 14px; border-bottom: 1px solid #f3f4f6; }

    .totals { text-align: right; }
    .totals-row { display: flex; justify-content: flex-end; gap: 40px; font-size: 14px; color: #6b7280; margin-bottom: 6px; }
    .totals-final { display: flex; justify-content: flex-end; gap: 40px; font-size: 18px; font-weight: 700; color: #191c1e; margin-top: 10px; }
</style>
@endsection

@section('content')

    <div class="detail-card">
        {{-- TODO: once Customer Management branch is merged, swap this back to route('customer.dashboard') --}}
        {{-- Using otc-shop for now since customer.dashboard doesn't exist on this branch yet --}}
        <a href="{{ route('customer.otc-shop') }}" class="back-link">&larr; Back to Shop</a>
        <div class="detail-title">Purchase #{{ $purchase->display_id }}</div>
        <div class="detail-subtitle">Detailed transaction record</div>

        <div class="info-row">
            <div class="info-field">
                <div class="info-label">Date</div>
                <div class="info-value">{{ $purchase->date }}</div>
            </div>
            <div class="info-field">
                <div class="info-label">Transaction Type</div>
                <div class="info-value">{{ $purchase->type }}</div>
            </div>
            <div class="info-field">
                <div class="info-label">Payment Method</div>
                <div class="info-value">{{ ucwords(str_replace('_', ' ', $purchase->payment_method)) }}</div>
            </div>
            <div class="info-field">
                <div class="info-label">Payment Status</div>
                <div class="info-value"><span class="badge-paid">{{ ucfirst($purchase->status) }}</span></div>
            </div>
        </div>

        @if($purchase->doctor_name)
        <div class="section-label">Prescription Information</div>
        <div class="rx-box">
            <div class="info-field">
                <div class="info-label">Prescribing Doctor</div>
                <div class="info-value">{{ $purchase->doctor_name }}</div>
            </div>
        </div>
        @endif

        <div class="section-label">Items Purchased</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $item)
                <tr>
                    <td>{{ $item->medicine_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->unit_price, 2) }}</td>
                    <td>${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-final">
                <span>Total</span>
                <span>${{ number_format($purchase->total, 2) }}</span>
            </div>
        </div>
    </div>

@stop