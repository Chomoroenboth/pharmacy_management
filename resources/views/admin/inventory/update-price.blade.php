@extends('layouts.admin-app')

@section('title', 'Update Price')

@section('page-css')
<style>
    .page-header { display: flex; align-items: center; gap: 10px; margin-bottom: 24px; }
    .page-header h1 { font-size: 24px; }

    .info-box { background: #f3f4f6; border-radius: 8px; padding: 16px 20px; display: flex; gap: 40px; margin-bottom: 24px; }
    .info-item .label { font-size: 12px; color: #6c7a71; font-weight: 600; letter-spacing: 0.02em; margin-bottom: 4px; }
    .info-item .value { font-size: 16px; font-weight: 600; }

    .form-row { display: flex; gap: 20px; }
    .form-group { flex: 1; margin-bottom: 20px; }
    .form-group label { display: block; font-size: 12px; font-weight: 600; color: #3c4a42; letter-spacing: 0.02em; margin-bottom: 8px; }
    .price-input-wrap { display: flex; align-items: center; border: 1px solid #d7d9dc; border-radius: 6px; padding: 0 12px; }
    .price-input-wrap.readonly { background: #f3f4f6; }
    .price-input-wrap span { color: #6c7a71; font-size: 14px; }
    .price-input-wrap input { border: none; padding: 11px 8px; flex: 1; font-size: 14px; background: transparent; }
    .field-error { color: #ba1a1a; font-size: 12px; margin-top: 4px; }

    .notice-row { display: flex; align-items: center; justify-content: space-between; margin-top: 8px; padding-top: 20px; border-top: 1px solid #f3f4f6; }
    .notice-text { font-size: 13px; color: #6c7a71; display: flex; align-items: center; gap: 6px; }
    .modal-footer-right { display: flex; gap: 12px; }
    .btn-cancel { background: #fff; border: 1px solid #d7d9dc; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; color: #191c1e; }
    .btn-save { background: #10b981; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
</style>
@endsection

@section('content')

    <div class="page-header">
        <h1>&#127991; Update Price</h1>
    </div>

    <div class="card">
        <div class="info-box">
            <div class="info-item">
                <div class="label">MEDICINE NAME</div>
                <div class="value">{{ $medicine->name }}</div>
            </div>
            <div class="info-item">
                <div class="label">CURRENT STOCK</div>
                <div class="value">{{ $medicine->stock }} units</div>
            </div>
        </div>

        <form id="priceForm">
            <div class="form-row">
                <div class="form-group">
                    <label>CURRENT PRICE</label>
                    <div class="price-input-wrap readonly">
                        <span>$</span>
                        <input type="text" value="{{ number_format($medicine->price, 2) }}" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label>NEW PRICE</label>
                    <div class="price-input-wrap">
                        <span>$</span>
                        <input type="number" step="0.01" name="new_price" id="new_price" placeholder="0.00">
                    </div>
                    <div class="field-error" id="price-error"></div>
                </div>
            </div>

            <div class="notice-row">
                <div class="notice-text">&#8505; This will log the price change and update the medicine record.</div>
                <div class="modal-footer-right">
                    <a href="{{ route('admin.inventory.show', $medicine->id) }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-save" id="priceSaveBtn">Save Price Change</button>
                </div>
            </div>
        </form>
    </div>

@stop

@section('page-js')
<script>
(function () {
    const medicineId = {{ $medicine->id }};

    function authToken() {
        return localStorage.getItem('auth_token');
    }

    document.getElementById('priceForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const errorEl = document.getElementById('price-error');
        errorEl.textContent = '';

        const newPrice = document.getElementById('new_price').value;

        try {
            const res = await fetch(`/api/inventory/medicines/${medicineId}/price`, {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${authToken()}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ new_price: newPrice })
            });

            if (res.status === 401) {
                window.location.href = '/admin/login';
                return;
            }

            const json = await res.json();

            if (!res.ok) {
                const firstError = json.errors ? Object.values(json.errors)[0][0] : (json.message || 'Failed to update price.');
                errorEl.textContent = firstError;
                return;
            }

            window.location.href = "{{ route('admin.inventory.show', $medicine->id) }}";
        } catch (err) {
            errorEl.textContent = 'Something went wrong. Please try again.';
            console.error(err);
        }
    });
})();
</script>
@stop
