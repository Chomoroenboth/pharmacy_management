@extends('layouts.customer-app')

@section('title', 'Search Results')

@section('page-css')
<style>
    .search-header { margin-bottom: 24px; }
    .back-link { color: #10b981; font-size: 13px; text-decoration: none; display: inline-block; margin-bottom: 12px; }
    .search-results-title { font-size: 20px; font-weight: 600; margin-bottom: 4px; }
    .search-results-count { color: #6b7280; font-size: 14px; }

    .results-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .result-card {
        background: #fff; border-radius: 8px; padding: 18px;
        box-shadow: 0px 1px 3px rgba(0,0,0,0.06);
    }
    .stock-tag {
        display: inline-block; font-size: 11px; font-weight: 600;
        padding: 3px 10px; border-radius: 12px; margin-bottom: 10px;
    }
    .stock-tag.in-stock { background: #d1fae5; color: #047857; }
    .stock-tag.low-stock { background: #fef3c7; color: #b45309; }
    .result-title { font-size: 15px; font-weight: 600; margin-bottom: 2px; }
    .result-brand { font-size: 12px; color: #6b7280; margin-bottom: 16px; }
    .result-footer { display: flex; align-items: center; justify-content: space-between; }
    .result-price { font-size: 16px; font-weight: 700; }
    .btn-add-sm {
        background: #10b981; color: #fff; border: none;
        padding: 6px 14px; border-radius: 6px; font-size: 12px;
        font-weight: 600; cursor: pointer;
    }
</style>
@endsection

@section('content')

    <div class="search-header">
        <a href="{{ route('customer.otc-shop') }}" class="back-link">&larr; Back to Shop</a>
        <div class="search-results-title">Showing results for: "{{ $query }}"</div>
        <div class="search-results-count">{{ $results->count() }} items found in OTC Inventory.</div>
    </div>

    <div class="results-grid">
        @foreach($results as $item)
        <div class="result-card">
            <span class="stock-tag {{ $item->stock_quantity > 20 ? 'in-stock' : 'low-stock' }}">
                {{ $item->stock_quantity > 20 ? 'In Stock' : 'Low Stock' }} ({{ $item->stock_quantity }})
            </span>
            <div class="result-title">{{ $item->medicine_name }}</div>
            <div class="result-brand">{{ $item->brand }}</div>
            <div class="result-footer">
                <div class="result-price">${{ number_format($item->price, 2) }}</div>
                <form action="{{ route('customer.cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="medicine_id" value="{{ $item->medicine_id }}">
                    <button type="submit" class="btn-add-sm">Add</button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

@stop