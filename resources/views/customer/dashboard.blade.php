@extends('layouts.customer-app')

@section('title', 'Customer Dashboard')

@section('page-css')
<style>
    .profile-grid { display: flex; gap: 40px; margin-top: 8px; }
    .profile-field { flex: 1; }
    .field-label { font-size: 12px; color: #3c4a42; margin-bottom: 8px; }
    .field-value { font-size: 16px; color: #191c1e; }

    .quick-actions { display: flex; gap: 16px; margin-top: 8px; }
    .btn-primary {
        background: #10b981; color: #fff; padding: 11px 24px;
        border-radius: 6px; font-size: 14px; font-weight: 500;
        text-decoration: none;
    }
    .btn-outline {
        background: #fff; color: #10b981; border: 1px solid #10b981;
        padding: 10px 24px; border-radius: 6px; font-size: 14px; font-weight: 500;
        text-decoration: none;
    }

    .purchases-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    .purchases-table th {
        background: #f3f4f6; text-align: left; padding: 12px 16px;
        font-size: 12px; color: #3c4a42; font-weight: 500;
    }
    .purchases-table td {
        padding: 16px; font-size: 14px; border-bottom: 1px solid #e1e2e4;
    }
    .badge-paid {
        background: rgba(16,185,129,0.2); color: #10b981;
        padding: 4px 8px; border-radius: 20px; font-size: 12px;
    }
</style>
@endsection

@section('content')

    <div class="page-title">Customer Dashboard</div>

    {{-- Profile Overview --}}
    <div class="card">
        <div class="card-title">Profile Overview</div>
        <div class="profile-grid">
            <div class="profile-field">
                <div class="field-label">FULL NAME</div>
                <div class="field-value">{{ $user->first_name }} {{ $user->last_name }}</div>
            </div>
            <div class="profile-field">
                <div class="field-label">PHONE</div>
                <div class="field-value">{{ $user->phone_number }}</div>
            </div>
            <div class="profile-field">
                <div class="field-label">EMAIL ADDRESS</div>
                <div class="field-value">{{ $user->email }}</div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="card">
        <div class="card-title">Quick Actions</div>
        <div class="quick-actions">
            <a href="#" class="btn-primary">Browse OTC Medicines</a>
            <a href="#" class="btn-outline">My Prescriptions</a>
        </div>
    </div>

    {{-- Recent Purchases --}}
    <div class="card">
        <div class="card-title">Recent Purchases</div>
        <table class="purchases-table">
            <thead>
                <tr>
                    <th>DATE</th>
                    <th>ITEMS</th>
                    <th>TOTAL</th>
                    <th>TYPE</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->date }}</td>
                    <td>{{ $purchase->items }}</td>
                    <td>${{ number_format($purchase->total, 2) }}</td>
                    <td>{{ $purchase->type }}</td>
                    <td><span class="badge-{{ strtolower($purchase->status) }}">{{ $purchase->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@stop