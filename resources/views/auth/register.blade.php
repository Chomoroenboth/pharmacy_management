<!DOCTYPE html>
<html>
<head><title>Register</title></head>
<body>
    <h1>Customer Registration</h1>

    @if($errors->any())
        <p style="color:red">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="/register">
        @csrf
        <input type="text"     name="first_name"             placeholder="First Name" required><br>
        <input type="text"     name="last_name"              placeholder="Last Name"><br>
        <input type="email"    name="email"                  placeholder="Email" required><br>
        <input type="password" name="password"               placeholder="Password" required><br>
        <input type="password" name="password_confirmation"  placeholder="Confirm Password" required><br>
        <input type="text"     name="phone_number"           placeholder="Phone Number"><br>
        <input type="date"     name="date_of_birth"><br>
        <input type="text"     name="address"                placeholder="Address"><br>
        <button type="submit">Register</button>
    </form>

    <a href="/login">Already have an account?</a>
</body>
</html>
