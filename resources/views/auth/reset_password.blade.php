<!DOCTYPE html>
<html>
<head><title>Reset Password</title></head>
<body>
    <h1>Reset Password</h1>

    @if(session('success'))
        <p style="color:green">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="/reset-password">
        @csrf
        <input type="password" name="password"              placeholder="New Password" required><br>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required><br>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
