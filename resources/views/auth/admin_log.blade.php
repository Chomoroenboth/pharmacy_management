<!DOCTYPE html>
<html>
<head><title>Admin Login</title></head>
<body>
    <h1>Staff Login</h1>

    @if($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="/admin/login">
        @csrf
        <input type="email"    name="email"    placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
