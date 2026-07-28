@extends('layouts.auth-app')

@section('title', 'Reset Your Password')

@section('content')

    <div class="auth-logo">PharmaCare MS</div>
    <div class="auth-title">Reset Your Password</div>
    <div class="auth-subtitle">Enter your email we'll send you a reset link.</div>

    @if (session('message'))
        <div class="status-message">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('customer.forgot-password.submit') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" placeholder="Enter email" value="{{ old('email') }}">
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">Send Reset Link</button>
    </form>

    <div class="auth-footer">
        Remember your password? <a href="{{ route('customer.login') }}">Sign In</a>
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
