<!DOCTYPE html>
<html>
<head><title>Customer Dashboard</title></head>
<body>
    <h1>Welcome, {{ session('first_name') }}</h1>
    <form method="POST" action="/logout">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
