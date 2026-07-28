<!DOCTYPE html>
<html>
<head><title>Forgot Password</title></head>
<body>
    <h1>Forgot Password</h1>

    @if($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="/forgot-password">
        @csrf
        <input type="email" name="email" placeholder="Enter your email" required><br>
        <button type="submit">Send Reset Link</button>
    </form>

    <a href="/login">Back to login</a>
</body>
</html>
