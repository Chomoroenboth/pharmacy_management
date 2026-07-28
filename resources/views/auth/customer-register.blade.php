@extends('layouts.auth-app')

@section('title', 'Create Your Account')

@section('content')

    <div class="auth-logo">PharmaCare MS</div>
    <div class="auth-title">Create Your Account</div>

    @if (session('message'))
        <div class="status-message">{{ session('message') }}</div>
    @endif

    <form method="POST" action="{{ route('customer.register.submit') }}">
        @csrf

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">First Name</label>
                <input type="text" name="first_name" class="form-input" value="{{ old('first_name') }}">
                @error('first_name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Last Name</label>
                <input type="text" name="last_name" class="form-input" value="{{ old('last_name') }}">
                @error('last_name')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone_number" class="form-input" value="{{ old('phone_number') }}">
            @error('phone_number')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-input" value="{{ old('email') }}">
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" class="form-input">
                <span class="password-toggle" onclick="togglePassword()">&#128065;</span>
            </div>
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">Register Account</button>
    </form>

    <div class="auth-footer">
        Already registered? <a href="{{ route('customer.login') }}">Log in</a>
    </div>

@endsection

@section('page-css')
<style>
    .form-row { display: flex; gap: 12px; }
    .form-row .form-group { flex: 1; }
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
