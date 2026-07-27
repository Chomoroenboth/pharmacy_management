<!DOCTYPE html>
<html>
<head><title>Admin Dashboard</title></head>
<body>
    <h1>Welcome, {{ session('full_name') }}</h1>
    <form method="POST" action="/admin/logout">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
    
