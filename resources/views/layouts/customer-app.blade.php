<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'PharmaCare MS')</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Inter", Helvetica, sans-serif; background: #f3f4f6; color: #191c1e; }

        .top-nav {
            display: flex; align-items: center; justify-content: space-between;
            background: #fff; padding: 16px 40px;
            box-shadow: 0px 1px 2px rgba(0,0,0,0.05);
        }
        .nav-left { display: flex; align-items: center; gap: 12px; }
        .back-arrow { color: #191c1e; text-decoration: none; font-size: 18px; }
        .logo { color: #006c49; font-size: 24px; font-weight: 700; }
        .nav-right { display: flex; align-items: center; gap: 16px; }
        .avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: #10b981; color: #00422b;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 12px;
        }
        .user-name { font-size: 14px; font-weight: 500; }
        .logout-btn {
            background: #fff; border: none;
            font-size: 14px; color: #191c1e; font-weight: 500;
            cursor: pointer;
        }

        .page-container { padding: 40px; max-width: 1216px; margin: 0 auto; }

        .card {
            background: #fff; border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.08);
            padding: 32px; margin-bottom: 24px;
        }
        .card-title { font-size: 18px; font-weight: 600; margin-bottom: 16px; }
    </style>
    @yield('page-css')
</head>
<body>

    <div class="top-nav">
        <div class="nav-left">
            <a href="{{ url()->previous() }}" class="back-arrow">&larr;</a>
            <div class="logo">PharmaCare MS</div>
        </div>
        <div class="nav-right">
            <div class="avatar">{{ $userInitials ?? 'JD' }}</div>
            <div class="user-name">{{ $userName ?? 'John Doe' }}</div>
            <button class="logout-btn">Log Out</button>
        </div>
    </div>

    <div class="page-container">
        @yield('content')
    </div>

</body>
</html>
