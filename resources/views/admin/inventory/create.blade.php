@extends('layouts.admin-app')

@section('title', 'Add New Medicine')

@section('page-css')
<style>
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; }
    .modal-box { background: #fff; border-radius: 8px; width: 100%; max-width: 480px; padding: 32px; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h2 { font-size: 20px; }
    .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; text-decoration: none; color: #191c1e; }
    .form-group { margin-bottom: 18px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #3c4a42; margin-bottom: 6px; }
    .form-group input, .form-group select {
        width: 100%; padding: 10px 12px; border: 1px solid #d7d9dc; border-radius: 6px; font-size: 14px; font-family: inherit;
    }
    .price-input-wrap { display: flex; align-items: center; border: 1px solid #d7d9dc; border-radius: 6px; padding: 0 12px; }
    .price-input-wrap span { color: #6c7a71; font-size: 14px; }
    .price-input-wrap input { border: none; padding: 10px 8px; flex: 1; }
    .field-error { color: #ba1a1a; font-size: 12px; margin-top: 4px; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
    .btn-cancel { background: #fff; border: 1px solid #d7d9dc; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 500; cursor: pointer; text-decoration: none; color: #191c1e; }
    .btn-save { background: #10b981; border: none; border-radius: 6px; padding: 10px 24px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer; }
</style>
@endsection

@section('content')

    <div class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h2>Add New Medicine</h2>
                <a href="{{ route('admin.inventory') }}" class="modal-close">&times;</a>
            </div>

            <form method="POST" action="{{ route('admin.inventory.store') }}">
                @csrf

                <div class="form-group">
                    <label>Medicine Name</label>
                    <input type="text" name="medicine_name" placeholder="e.g., Amoxicillin 500mg" value="{{ old('medicine_name') }}">
                    @error('medicine_name') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="">Select category</option>
                        <option>Antibiotics</option>
                        <option>Painkillers</option>
                        <option>Cardiovascular</option>
                        <option>Vitamins</option>
                        <option>Gastrointestinal</option>
                    </select>
                    @error('category') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Brand</label>
                    <input type="text" name="brand" placeholder="e.g., PharmaCorp" value="{{ old('brand') }}">
                </div>

                <div class="form-group">
                    <label>Price ($)</label>
                    <div class="price-input-wrap">
                        <span>$</span>
                        <input type="number" step="0.01" name="price" placeholder="0.00" value="{{ old('price') }}">
                    </div>
                    @error('price') <div class="field-error">{{ $message }}</div> @enderror
                </div>

                <div class="modal-footer">
                    <a href="{{ route('admin.inventory') }}" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>

@stop