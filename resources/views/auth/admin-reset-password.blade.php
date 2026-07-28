@extends('layouts.auth-app')

@section('title', 'Reset Your Password')

@section('content')

    <div class="auth-title">Reset Your Password</div>
    <div class="auth-subtitle">Enter your email we'll send you a reset link.</div>

    @if (session('message'))
        <div class="status-message">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.reset-password.submit') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">New Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" class="form-input">
                <span class="password-toggle" onclick="togglePassword('password')">&#128065;</span>
            </div>
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <div class="password-wrapper">
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-input">
                <span class="password-toggle" onclick="togglePassword('password_confirmation')">&#128065;</span>
            </div>
        </div>

        <button type="submit" class="btn-submit">Save New Password</button>
    </form>

    <div class="auth-divider"></div>

    <div class="auth-footer">
        Remember your password? <a href="{{ route('admin.login') }}">Sign In</a>
    </div>

@endsection

@section('page-css')
<style>
    .auth-divider { border-top: 1px solid #e1e2e4; margin: 24px 0; }
    .status-message {
        background: #d1fae5; color: #065f46; font-size: 13px;
        padding: 10px 14px; border-radius: 8px; margin-bottom: 18px;
    }
    .field-error { color: #ba1a1a; font-size: 12px; margin-top: 4px; }
</style>
<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
@endsection
