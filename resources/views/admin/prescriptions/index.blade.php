@extends('layouts.admin-app')

@section('title', 'Prescriptions')

@section('page-css')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-header h1 { font-size: 28px; }
    .btn-primary { background: #10b981; border: none; border-radius: 6px; padding: 10px 20px; font-size: 14px; font-weight: 600; color: #fff; text-decoration: none; }

    .rx-table { width: 100%; border-collapse: collapse; }
    .rx-table th { background: #f3f4f6; text-align: left; padding: 14px 20px; font-size: 12px; color: #3c4a42; font-weight: 600; }
    .rx-table td { padding: 16px 20px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
    .rx-code { color: #006c49; font-weight: 600; }
    .status-pill { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 500; }
    .status-active { background: #adedd3; color: #306d58; }
    .status-expired { background: #ffdad6; color: #930a0a; }
    .status-filled { background: #e7e8ea; color: #3c4a42; }
    .view-link { color: #3c4a42; font-size: 16px; text-decoration: none; }

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
        <h1>Prescriptions</h1>
        <a href="{{ route('admin.prescriptions.create') }}" class="btn-primary">+ Add Prescription</a>
    </div>

    <div class="card" style="padding: 0; overflow: hidden;">
        <table class="rx-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Doctor</th>
                    <th>Date Issued</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($prescriptions as $rx)
                <tr>
                    <td class="rx-code">{{ $rx->code }}</td>
                    <td>{{ $rx->customer }}</td>
                    <td>{{ $rx->doctor }}</td>
                    <td>{{ \Carbon\Carbon::parse($rx->issue_date)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($rx->expiry_date)->format('M d, Y') }}</td>
                    <td><span class="status-pill status-{{ $rx->status }}">{{ ucfirst($rx->status) }}</span></td>
                    <td><a href="{{ route('admin.prescriptions.show', $rx->id) }}" class="view-link">&#x1F441;</a></td>
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