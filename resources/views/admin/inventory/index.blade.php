@extends('layouts.admin-app')

@section('title', 'Inventory')

@section('page-css')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
    .page-header h1 { font-size: 28px; }
    .page-header p { font-size: 14px; color: #6c7a71; margin-top: 4px; }
    .header-controls { display: flex; gap: 12px; align-items: center; }
    .search-input {
        padding: 10px 16px; border: 1px solid #d7d9dc; border-radius: 6px;
        font-size: 14px; width: 240px;
    }
    .category-select {
        padding: 10px 16px; border: 1px solid #d7d9dc; border-radius: 6px;
        font-size: 14px; background: #fff;
    }
    .btn-primary {
        background: #10b981; border: none; border-radius: 6px;
        padding: 10px 20px; font-size: 14px; font-weight: 600; color: #fff;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
    }

    .inv-table { width: 100%; border-collapse: collapse; }
    .inv-table th {
        background: #f3f4f6; text-align: left; padding: 14px 20px;
        font-size: 12px; color: #3c4a42; font-weight: 600;
    }
    .inv-table td { padding: 16px 20px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
    .med-name { font-weight: 600; }
    .stock-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
    .stock-high { background: #adedd3; color: #306d58; }
    .stock-medium { background: #fde68a; color: #92400e; }
    .stock-low { background: #ffdad6; color: #930a0a; }
    .view-link { color: #3c4a42; font-size: 15px; text-decoration: none; }

    .table-footer { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; font-size: 13px; color: #6c7a71; }
    .pagination { display: flex; gap: 6px; }
    .page-btn { min-width: 32px; height: 32px; border: 1px solid #e1e2e4; border-radius: 6px; background: #fff; color: #3c4a42; font-size: 13px; cursor: pointer; }
    .page-btn.active { background: #10b981; border-color: #10b981; color: #fff; font-weight: 600; }
</style>
@endsection

@section('content')

    @if (session('message'))
        <div style="background:#d1fae5; color:#065f46; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
            {{ session('message') }}
        </div>
    @endif

    <div class="page-header">
        <div>
            <h1>Inventory</h1>
            <p>Manage medicine stock and categories</p>
        </div>
        <div class="header-controls">
            <input type="text" class="search-input" placeholder="Search medicine...">
            <select class="category-select"><option>All Categories</option></select>
            <a href="{{ route('admin.inventory.create') }}" class="btn-primary">+ Add Medicine</a>
        </div>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="inv-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock Level</th>
                    <th>View Medicine Details</th>
                </tr>
            </thead>
            <tbody>
                @foreach($medicines as $m)
                <tr>
                    <td>{{ $m->code }}</td>
                    <td class="med-name">{{ $m->name }}</td>
                    <td>{{ $m->category }}</td>
                    <td>{{ $m->brand }}</td>
                    <td>${{ number_format($m->price, 2) }}</td>
                    <td>
                        <span class="stock-badge {{ $m->stock > 20 ? 'stock-high' : ($m->stock > 10 ? 'stock-medium' : 'stock-low') }}">
                            {{ $m->stock }} Units
                        </span>
                    </td>
                    <td><a href="{{ route('admin.inventory.show', $m->id) }}" class="view-link">&#128065;</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="table-footer">
            <div>Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of {{ $pagination['total'] }} entries</div>
            <div class="pagination">
                <button class="page-btn">Previous</button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn">Next</button>
            </div>
        </div>
    </div>

@stop