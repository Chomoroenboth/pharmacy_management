@extends('layouts.auth-app')
@section('title', 'Sign In')
@section('content')
    <div class="auth-logo">PharmaCare MS</div>
    <div class="auth-title">Welcome back</div>
    <div class="auth-subtitle">Sign in to continue</div>
    <form id="customerLoginForm">
        <div class="form-group">
            <label class="form-label">Phone number or customer ID</label>
            <input type="text" name="identifier" id="identifier" class="form-input" placeholder="Enter ID">
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="password-wrapper">
                <input type="password" name="password" id="password" class="form-input" placeholder="Enter password">
                <span class="password-toggle" onclick="togglePassword()">👁</span>
            </div>
        </div>
        <a href="{{ route('customer.forgot-password') }}" class="forgot-link">Forgot password?</a>
        <p id="loginError" style="color:red; display:none;"></p>
        <button type="submit" class="btn-submit">Sign in</button>
    </form>
    <div class="auth-footer">
        New customer? <a href="{{ route('customer.register') }}">Create an account</a>
    </div>
@endsection
@section('page-css')
<script>
    function togglePassword() {
        const input = document.getElementById('password');
        input.type = input.type === 'password' ? 'text' : 'password';
    }
</script>
@vite(['resources/js/app.js'])
<script>
document.getElementById('customerLoginForm').addEventListener('submit', async function (e) {
    e.preventDefault();
    const errorEl = document.getElementById('loginError');
    errorEl.style.display = 'none';

    try {
        const response = await window.axios.post('/api/auth/login', {
            email: document.getElementById('identifier').value, // NOTE: API expects "email" — see caveat below
            password: document.getElementById('password').value,
        });

        window.saveAuth(response.data.token, response.data.user);
        window.location.href = "{{ route('customer.dashboard') }}";
    } catch (err) {
        errorEl.innerText = err.response?.data?.message || 'Login failed. Please try again.';
        errorEl.style.display = 'block';
    }
});
</script>
@endsection
