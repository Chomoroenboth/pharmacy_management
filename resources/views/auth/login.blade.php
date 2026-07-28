<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <h1>Customer Login</h1>

    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="/login">
        @csrf
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>

    <a href="/register">Create account</a> |
    <a href="/forgot-password">Forgot password?</a>
</body>
</html>
