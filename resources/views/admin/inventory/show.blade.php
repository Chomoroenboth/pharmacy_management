@extends('layouts.admin-app')

@section('title', 'Medicine Details')

@section('page-css')
<style>
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-size: 28px; }

    .detail-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 24px; margin-bottom: 24px; }
    .detail-item .label { font-size: 12px; color: #6c7a71; font-weight: 600; letter-spacing: 0.02em; margin-bottom: 6px; }
    .detail-item .value { font-size: 16px; color: #191c1e; }
    .detail-item .value.price { color: #10b981; font-size: 22px; font-weight: 700; }
    .badge-yesno { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 12px; background: #edeef0; color: #3c4a42; }
    .stock-pill { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; }
    .stock-pill .dot { width: 8px; height: 8px; border-radius: 50%; }
    .dot-low { background: #ba1a1a; }
    .dot-ok { background: #10b981; }

    .action-row { display: flex; gap: 12px; margin-top: 8px; padding-top: 20px; border-top: 1px solid #f3f4f6; }
    .btn-outline { background: #fff; border: 1px solid #10b981; border-radius: 6px; padding: 9px 18px; font-size: 13px; font-weight: 600; color: #006c49; cursor: pointer; text-decoration: none; }

    .price-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    .price-table th { background: #f3f4f6; text-align: left; padding: 12px 16px; font-size: 12px; color: #3c4a42; font-weight: 600; }
    .price-table td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
    .diff-up { color: #ba1a1a; background: #ffdad6; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
    .diff-down { color: #10b981; background: #adedd3; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
    .no-history { padding: 24px 16px; color: #6c7a71; font-size: 14px; text-align: center; }

    /* Modals */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: #fff; border-radius: 8px; width: 100%; max-width: 480px; padding: 32px; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h2 { font-size: 20px; }
    .modal-header .med-tag { color: #006c49; font-size: 13px; font-weight: 600; }
    .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; }
    .form-group { margin-bottom: 16px; }
    .form-row { display: flex; gap: 12px; }
    .form-row .form-group { flex: 1; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #3c4a42; margin-bottom: 6px; }
    .form-group input, .form-group select, .form-group textarea {
        width: 100%; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; font-family: inherit;
    }
    .modal-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
    .modal-footer-right { display: flex; gap: 12px; }
    .btn-cancel { background: #fff; border: 1px solid #d7d9dc; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 500; cursor: pointer; }
    .btn-save { background: #10b981; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
    .btn-delete-text { background: none; border: none; color: #ba1a1a; font-size: 14px; font-weight: 600; cursor: pointer; }
    .btn-delete-solid { background: #ba1a1a; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
    .delete-body { font-size: 14px; color: #3c4a42; line-height: 1.5; margin: 12px 0 20px; }
</style>
@endsection

@section('content')

    @if (session('message'))
        <div style="background:#d1fae5; color:#065f46; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
            {{ session('message') }}
        </div>
    @endif

    <div class="page-header">
        <h1>Medicine Details</h1>
    </div>

    <div class="card">
        <div class="detail-grid">
            <div class="detail-item">
                <div class="label">MEDICINE ID</div>
                <div class="value">{{ $medicine->code }}</div>
            </div>
            <div class="detail-item">
                <div class="label">MEDICINE NAME</div>
                <div class="value">{{ $medicine->name }}</div>
            </div>
            <div class="detail-item">
                <div class="label">CATEGORY</div>
                <div class="value">{{ $medicine->category }}</div>
            </div>
            <div class="detail-item">
                <div class="label">BRAND</div>
                <div class="value">{{ $medicine->brand }}</div>
            </div>
            <div class="detail-item">
                <div class="label">CURRENT PRICE</div>
                <div class="value price">${{ number_format($medicine->price, 2) }}</div>
            </div>
            <div class="detail-item">
                <div class="label">REQUIRES PRESCRIPTION</div>
                <div class="badge-yesno">{{ $medicine->requires_prescription ? 'Yes' : 'No' }}</div>
            </div>
            <div class="detail-item">
                <div class="label">STOCK LEVEL</div>
                <div class="stock-pill">
                    <span class="dot {{ $medicine->stock <= 10 ? 'dot-low' : 'dot-ok' }}"></span>
                    {{ $medicine->stock }} Units
                </div>
            </div>
        </div>

        <div class="action-row">
            <a href="#" class="btn-outline" onclick="openModal('editModal', event)">&#9998; Edit Medicine</a>
            <a href="#" class="btn-outline" onclick="openModal('restockModal', event)">&#128230; Update Stock</a>
        </div>
    </div>

    <div class="card">
        <div class="card-title">&#8635; Price Change Log</div>
        @if($priceHistory->count())
        <table class="price-table">
            <thead>
                <tr><th>DATE</th><th>OLD PRICE</th><th>NEW PRICE</th><th>DIFFERENCE</th></tr>
            </thead>
            <tbody>
                @foreach($priceHistory as $p)
                <tr>
                    <td>{{ $p->date }}</td>
                    <td>${{ number_format($p->old_price, 2) }}</td>
                    <td>${{ number_format($p->new_price, 2) }}</td>
                    <td>
                        <span class="{{ $p->diff >= 0 ? 'diff-up' : 'diff-down' }}">
                            {{ $p->diff >= 0 ? '+' : '-' }}${{ number_format(abs($p->diff), 2) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="no-history">No price changes recorded yet.</div>
        @endif

        <div style="margin-top: 16px;">
            <a href="{{ route('admin.inventory.price', $medicine->id) }}" class="btn-outline">&#43; Record Price Change</a>
        </div>
    </div>

    {{-- Edit Medicine modal --}}
    <div class="modal-overlay" id="editModal">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Edit Medicine</h2>
                <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.inventory.update', $medicine->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>Medicine Name</label>
                    <input type="text" name="medicine_name" value="{{ $medicine->name }}">
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" value="{{ $medicine->category }}">
                </div>
                <div class="form-group">
                    <label>Brand</label>
                    <input type="text" name="brand" value="{{ $medicine->brand }}">
                </div>
                <div class="form-group">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="price" value="{{ $medicine->price }}">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-delete-text" onclick="closeModal('editModal'); openModal('deleteModal');">Delete</button>
                    <div class="modal-footer-right">
                        <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
                        <button type="submit" class="btn-save">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Update Stock (Restock) modal --}}
    <div class="modal-overlay" id="restockModal">
        <div class="modal-box">
            <div class="modal-header">
                <div>
                    <h2>Log Stock Transaction</h2>
                    <div class="med-tag">{{ $medicine->name }}</div>
                </div>
                <button class="modal-close" onclick="closeModal('restockModal')">&times;</button>
            </div>
            <form method="POST" action="{{ route('admin.inventory.restock', $medicine->id) }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label>Transaction Type</label>
                        <select name="txn_type">
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="txn_date" value="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" name="quantity" placeholder="e.g. 500">
                    </div>
                    <div class="form-group">
                        <label>Unit Cost ($)</label>
                        <input type="number" step="0.01" name="unit_cost" placeholder="0.00">
                    </div>
                </div>

                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="3" placeholder="Add any relevant details regarding this transaction (optional)..."></textarea>
                </div>

                <div class="modal-footer" style="justify-content: flex-end;">
                    <div class="modal-footer-right">
                        <button type="button" class="btn-cancel" onclick="closeModal('restockModal')">Cancel</button>
                        <button type="submit" class="btn-save">Save Transaction</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete confirmation modal --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box" style="max-width: 420px;">
            <div class="modal-header">
                <h2><span style="color:#ba1a1a;">&#9888;</span> Delete Medicine</h2>
            </div>
            <div class="delete-body">
                Are you sure you want to delete {{ $medicine->name }}? This action cannot be undone and will remove it from inventory, including its price history.
            </div>
            <form method="POST" action="{{ route('admin.inventory.destroy', $medicine->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-footer" style="justify-content: flex-end;">
                    <div class="modal-footer-right">
                        <button type="button" class="btn-cancel" onclick="closeModal('deleteModal')">Cancel</button>
                        <button type="submit" class="btn-delete-solid">Delete Medicine</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, e) { if (e) e.preventDefault(); document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }
    </script>

@stop