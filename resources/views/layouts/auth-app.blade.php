<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'PharmaCare MS')</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: "Inter", Helvetica, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            background: #fff;
            border-radius: 12px;
            padding: 40px;
            width: 400px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .auth-logo { color: #006c49; font-size: 24px; font-weight: 700; margin-bottom: 24px; }
        .auth-title { font-size: 24px; font-weight: 600; color: #191c1e; margin-bottom: 4px; }
        .auth-subtitle { font-size: 14px; color: #6b7280; margin-bottom: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-label { font-size: 13px; color: #374151; margin-bottom: 6px; display: block; }
        .form-input {
            width: 100%; padding: 10px 14px;
            border: 1px solid #d1d5db; border-radius: 6px;
            font-size: 14px;
        }
        .form-input:focus { outline: none; border-color: #10b981; }
        .password-wrapper { position: relative; }
        .password-toggle {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            cursor: pointer; color: #9ca3af; font-size: 14px; user-select: none;
        }
        .forgot-link {
            display: block; text-align: right;
            font-size: 13px; color: #10b981; text-decoration: none;
            margin-bottom: 20px;
        }
        .btn-submit {
            width: 100%; padding: 12px;
            background: #10b981; color: #fff;
            border: none; border-radius: 6px;
            font-size: 14px; font-weight: 600; cursor: pointer;
        }
        .btn-submit:hover { background: #0ea371; }
        .auth-footer {
            text-align: center; font-size: 13px; color: #6b7280;
            margin-top: 20px;
        }
        .auth-footer a { color: #10b981; text-decoration: none; font-weight: 500; }
    </style>
    @yield('page-css')
</head>
<body>
    <div class="auth-card">
        @yield('content')
    </div>
</body>
</html>