<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'PharmaCare MS')</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Inter", Helvetica, sans-serif; background: #f9fafb; color: #191c1e; }

        .top-nav {
            display: flex; align-items: center; justify-content: space-between;
            background: #fff; padding: 14px 40px;
            box-shadow: 0px 1px 2px rgba(0,0,0,0.05);
        }
        .logo { color: #006c49; font-size: 24px; font-weight: 600; text-decoration: none; }
        .search-box {
            background: #f3f4f6; border-radius: 8px;
            padding: 11px 20px; width: 500px; border: none;
            color: rgba(60,74,66,0.7); font-size: 14px;
        }
        .nav-right { display: flex; align-items: center; gap: 16px; }
        .nav-link { display: flex; align-items: center; gap: 6px; color: #4a5550; font-size: 14px; font-weight: 600; text-decoration: none; }
        .nav-link.active { color: #008562; }
        .avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: #10b981; color: #00422b;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 12px;
        }
        .user-name { font-size: 14px; }
        .logout-btn {
            background: #fff; border: 1px solid #e1e2e4; border-radius: 4px;
            padding: 8px 16px; font-size: 12px; color: #3c4a42; font-weight: bold;
            cursor: pointer; text-decoration: none;
        }

        .page-container { padding: 40px; max-width: 1200px; margin: 0 auto; }
        .page-title { font-size: 32px; margin-bottom: 8px; }
        .page-subtitle { color: #6b7280; font-size: 14px; margin-bottom: 24px; }

        .card {
            background: #fff; border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.08);
            padding: 32px; margin-bottom: 24px;
        }
        .card-title { font-size: 24px; margin-bottom: 20px; }
        .flash-message {
            background: #d1fae5; color: #047857; padding: 12px 20px;
            border-radius: 6px; margin-bottom: 20px; font-size: 14px; font-weight: 500;
        }
    </style>
    @yield('page-css')
</head>
<body>
    <div class="top-nav">
        <a href="/" class="logo">PharmaCare MS</a>
        <form action="{{ route('customer.search') }}" method="GET" style="flex:1; max-width:500px;">
            <input type="text" name="q" class="search-box" style="width:100%;" placeholder="Search medicines, symptoms, brands...">
        </form>
        <div class="nav-right">
            <a href="{{ route('customer.prescriptions') }}" class="nav-link {{ request()->routeIs('customer.prescriptions*') ? 'active' : '' }}">📋 Prescriptions</a>
            <a href="{{ route('customer.cart') }}" class="nav-link">🛒 Cart</a>
            <div class="avatar">{{ $userInitials ?? 'JD' }}</div>
            <div class="user-name">{{ $userName ?? 'John Doe' }}</div>
            <a href="#" class="logout-btn">Log Out</a>
        </div>
    </div>

    <div class="page-container">
        @if(session('message'))
            <div class="flash-message">{{ session('message') }}</div>
        @endif
        @yield('content')
    </div>
</body>
</html>