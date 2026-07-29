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
    .badge-unpaid { background: #fee2e2; color: #b91c1c; padding: 3px 10px; border-radius: 12px; font-size: 12px; }

    .section-label { font-size: 13px; font-weight: 600; color: #6b7280; margin-bottom: 12px; text-transform: uppercase; }

    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .items-table th { text-align: left; font-size: 12px; color: #9ca3af; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
    .items-table td { padding: 10px 0; font-size: 14px; border-bottom: 1px solid #f3f4f6; }

    .totals { text-align: right; }
    .totals-final { display: flex; justify-content: flex-end; gap: 40px; font-size: 18px; font-weight: 700; color: #191c1e; margin-top: 10px; }
    .loading-text { color: #6b7280; text-align: center; padding: 40px; }
</style>

@vite(['resources/js/app.js'])
<script>
document.addEventListener('DOMContentLoaded', function () {
    const saleId = {{ (int) $saleId }};
    const container = document.getElementById('detailContent');

    async function loadReceipt() {
        try {
            const response = await window.axios.get('/api/shop/sales/' + saleId);
            const sale = response.data.data.sale;
            const items = response.data.data.items;

            const statusBadgeClass = sale.payment_status === 'paid' ? 'badge-paid' : 'badge-unpaid';

            const itemRows = items.map(function (item) {
                return `
                    <tr>
                        <td>${item.medicine_name}</td>
                        <td>${item.quantity}</td>
                        <td>$${Number(item.unit_price).toFixed(2)}</td>
                        <td>$${Number(item.subtotal).toFixed(2)}</td>
                    </tr>
                `;
            }).join('');

            container.innerHTML = `
                <a href="{{ route('customer.dashboard') }}" class="back-link">&larr; Back to Dashboard</a>
                <div class="detail-title">Purchase #${sale.display_id}</div>
                <div class="detail-subtitle">Detailed transaction record</div>

                <div class="info-row">
                    <div class="info-field">
                        <div class="info-label">Date</div>
                        <div class="info-value">${new Date(sale.sale_date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</div>
                    </div>
                    <div class="info-field">
                        <div class="info-label">Payment Method</div>
                        <div class="info-value">${sale.payment_method ? sale.payment_method.replace('_', ' ').replace(/\\b\\w/g, c => c.toUpperCase()) : '—'}</div>
                    </div>
                    <div class="info-field">
                        <div class="info-label">Payment Status</div>
                        <div class="info-value"><span class="${statusBadgeClass}">${sale.payment_status.charAt(0).toUpperCase() + sale.payment_status.slice(1)}</span></div>
                    </div>
                </div>

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
                    <tbody>${itemRows}</tbody>
                </table>

                <div class="totals">
                    <div class="totals-final">
                        <span>Total</span>
                        <span>$${Number(sale.total_price).toFixed(2)}</span>
                    </div>
                </div>
            `;

        } catch (err) {
            if (err.response?.status === 401) {
                window.location.href = "{{ route('customer.login') }}";
                return;
            }
            container.innerHTML = '<p class="loading-text">Could not load this purchase. It may not exist or you may not have access to it.</p>';
            console.error('Failed to load purchase:', err);
        }
    }

    loadReceipt();
});
</script>
@endsection

@section('content')
    <div class="detail-card" id="detailContent">
        <p class="loading-text">Loading purchase details...</p>
    </div>
@stop
