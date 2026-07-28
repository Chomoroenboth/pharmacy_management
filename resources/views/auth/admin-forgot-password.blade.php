@extends('layouts.auth-app')

@section('title', 'Reset Staff Password')

@section('content')

    <div class="auth-logo">PharmaCare MS</div>
    <div class="auth-title">Reset Staff password</div>
    <div class="auth-subtitle">Enter your ID and we'll send a reset link to your registered email.</div>

    @if (session('message'))
        <div class="status-message">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.forgot-password.submit') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">ID or Email</label>
            <input type="text" name="email" class="form-input" placeholder="Enter your ID or Email">
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">Send Reset Link</button>
    </form>

    <div class="auth-footer">
        <a href="{{ route('admin.login') }}">&larr; Back to Sign In</a>
    </div>

@endsection

@section('page-css')
<style>
    .status-message {
        background: #d1fae5; color: #065f46; font-size: 13px;
        padding: 10px 14px; border-radius: 8px; margin-bottom: 18px;
    }
    .field-error { color: #ba1a1a; font-size: 12px; margin-top: 4px; }
</style>
@endsection
