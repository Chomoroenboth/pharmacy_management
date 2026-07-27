@extends('layouts.customer-app')

@section('title', 'OTC Medicine Shop')

@section('page-css')
<style>
    .category-tabs { display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap; }
    .category-tab {
        padding: 8px 18px; border-radius: 20px; font-size: 13px;
        border: 1px solid #e1e2e4; color: #3c4a42; text-decoration: none;
        background: #fff;
    }
    .category-tab.active { background: #10b981; color: #fff; border-color: #10b981; }

    .medicine-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
    .medicine-card {
        background: #fff; border-radius: 8px; padding: 20px;
        box-shadow: 0px 1px 3px rgba(0,0,0,0.06);
    }
    .med-category-tag { color: #10b981; font-size: 11px; font-weight: 600; text-transform: uppercase; margin-bottom: 8px; }
    .med-title { font-size: 16px; font-weight: 600; color: #191c1e; margin-bottom: 4px; }
    .med-brand { font-size: 13px; color: #6b7280; margin-bottom: 16px; }
    .med-footer { display: flex; align-items: center; justify-content: space-between; }
    .med-price { font-size: 18px; font-weight: 700; color: #191c1e; }
    .btn-add-cart {
        background: #10b981; color: #fff; border: none;
        padding: 8px 16px; border-radius: 6px; font-size: 13px;
        font-weight: 600; cursor: pointer;
    }
</style>
@endsection

@section('content')

    <div class="page-title">OTC Medicine Shop</div>
    <div class="page-subtitle">Browse our curated selection of over-the-counter medications.</div>

    <div class="category-tabs">
        <a href="{{ route('customer.otc-shop') }}" class="category-tab {{ $activeCategory === 'All Medicines' ? 'active' : '' }}">All Medicines</a>
        @foreach($categories as $category)
            <a href="{{ route('customer.otc-shop', ['category' => $category]) }}" class="category-tab {{ $activeCategory === $category ? 'active' : '' }}">{{ $category }}</a>
        @endforeach
    </div>

    @if($medicines->isEmpty())
        <p style="color:#6b7280;">No medicines found in this category.</p>
    @endif

    <div class="medicine-grid">
        @foreach($medicines as $medicine)
        <div class="medicine-card">
            <div class="med-category-tag">{{ $medicine->category }}</div>
            <div class="med-title">{{ $medicine->medicine_name }}</div>
            <div class="med-brand">{{ $medicine->brand }}</div>
            <div class="med-footer">
                <div class="med-price">${{ number_format($medicine->price, 2) }}</div>
                <form action="{{ route('customer.cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="medicine_id" value="{{ $medicine->medicine_id }}">
                    <button type="submit" class="btn-add-cart">Add to Cart</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

@stop