@extends('layouts.auth-app')

@section('title', 'Staff Login')

@section('content')

    <div class="auth-logo">PharmaCare MS</div>
    <div class="auth-title">Staff Login</div>

    @if (session('message'))
        <div class="status-message">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Your ID</label>
            <input type="text" name="identifier" class="form-input" placeholder="Enter your ID">
            @error('identifier')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" class="form-input" placeholder="Enter your password">
                <span class="password-toggle" onclick="togglePassword()">&#128065;</span>
            </div>
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="options-row">
            <label class="checkbox-label">
                <input type="checkbox" name="remember" value="1"> Remember me
            </label>
            <a href="{{ route('admin.forgot-password') }}" class="forgot-link">Forgot password?</a>
        </div>
        {{-- Note: "Remember me" has no `remember_token` column on the
             `staff` table yet, so this won't persist beyond the session
             until that column is added. --}}

        <button type="submit" class="btn-submit">Sign In</button>
    </form>

@endsection

@section('page-css')
<style>
    .options-row { display: flex; justify-content: space-between; align-items: center; margin: 16px 0; font-size: 13px; }
    .checkbox-label { display: flex; align-items: center; gap: 6px; }
    .forgot-link { color: #006c49; text-decoration: none; }
    .status-message {
        background: #d1fae5; color: #065f46; font-size: 13px;
        padding: 10px 14px; border-radius: 8px; margin-bottom: 18px;
    }
    .field-error { color: #ba1a1a; font-size: 12px; margin-top: 4px; }
</style>
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
@endsection
