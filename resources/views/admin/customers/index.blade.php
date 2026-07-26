@extends('layouts.admin-app')

@section('title', 'Customers')

@section('page-css')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-header h1 { font-size: 28px; }
    .btn-primary {
        background: #10b981; border: none; border-radius: 6px;
        padding: 10px 20px; font-size: 14px; font-weight: 600; color: #fff;
        text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
        cursor: pointer;
    }

    .cust-table { width: 100%; border-collapse: collapse; }
    .cust-table th {
        background: #f3f4f6; text-align: left; padding: 14px 20px;
        font-size: 12px; color: #3c4a42; font-weight: 600; letter-spacing: 0.02em;
    }
    .cust-table td { padding: 18px 20px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
    .cust-table tr:last-child td { border-bottom: none; }
    .cust-name { font-weight: 600; }
    .actions-cell { display: flex; gap: 14px; align-items: center; }
    .icon-btn { background: none; border: none; cursor: pointer; font-size: 15px; color: #3c4a42; text-decoration: none; }

    .table-footer {
        display: flex; justify-content: space-between; align-items: center;
        padding: 16px 20px; font-size: 13px; color: #6c7a71;
    }
    .pagination { display: flex; gap: 6px; }
    .page-btn {
        min-width: 32px; height: 32px; border: 1px solid #e1e2e4; border-radius: 6px;
        background: #fff; color: #3c4a42; font-size: 13px; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
    }
    .page-btn.active { background: #10b981; border-color: #10b981; color: #fff; font-weight: 600; }
</style>
@endsection

@section('content')

    <div class="page-header">
        <h1>Customers</h1>
        <a href="{{ route('admin.customers.create') }}" class="btn-primary">+ Add Customer</a>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="cust-table">
            <thead>
                <tr>
                    <th>CUSTOMER ID</th>
                    <th>FULL NAME</th>
                    <th>PHONE</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $c)
                <tr>
                    <td>#{{ $c->display_id }}</td>
                    <td class="cust-name">{{ $c->full_name }}</td>
                    <td>{{ $c->phone }}</td>
                    <td>
                        <div class="actions-cell">
                            <a href="{{ route('admin.customers.show', $c->id) }}" class="icon-btn" title="View">&#128065;</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="table-footer">
            <div>Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of {{ $pagination['total'] }} entries</div>
            <div class="pagination">
                <button class="page-btn">&lsaquo;</button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn">&rsaquo;</button>
            </div>
        </div>
    </div>

@stop
