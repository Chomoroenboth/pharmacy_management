<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PharmaCare MS')</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Inter", Helvetica, sans-serif; background: #f3f4f6; color: #191c1e; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; flex-shrink: 0; background: #04281b; color: #fff; display: flex; flex-direction: column; padding: 24px 0; }
        .sidebar-logo { padding: 0 24px 24px; }
        .sidebar-logo .name { color: #10b981; font-size: 20px; font-weight: 700; }
        .sidebar-logo .subtitle { color: #9fb0a8; font-size: 12px; margin-top: 2px; }
        .sidebar-nav { flex: 1; margin-top: 8px; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 12px 24px; color: #cfd8d3; text-decoration: none; font-size: 14px; }
        .sidebar-nav a:hover { background: rgba(255,255,255,0.05); }
        .sidebar-nav a.active { background: #10b981; color: #04281b; font-weight: 600; }
        .sidebar-footer { padding: 12px 24px; border-top: 1px solid rgba(255,255,255,0.1); }
        .sidebar-footer a { display: flex; align-items: center; gap: 10px; color: #cfd8d3; text-decoration: none; font-size: 14px; }
        .main { flex: 1; padding: 40px; min-width: 0; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 32px; margin-bottom: 24px; }
        .card-title { font-size: 18px; font-weight: 600; margin-bottom: 16px; }
        @media (max-width: 720px) { .sidebar { width: 64px; } .sidebar-logo { padding: 0 10px 24px; text-align: center; } .sidebar-logo .name { font-size: 0; } .sidebar-logo .name::after { content: 'P'; font-size: 22px; } .sidebar-logo .subtitle, .sidebar-nav a span, .sidebar-footer a span { display: none; } .sidebar-nav a, .sidebar-footer a { justify-content: center; padding: 14px 8px; } .main { padding: 24px 16px; } }
    </style>
    @yield('page-css')
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="sidebar-logo">
                <div class="name">PharmaCare MS</div>
                <div class="subtitle">Management System</div>
            </div>
            <nav class="sidebar-nav">
                <a href="#"><span>&#9635;</span><span>Dashboard</span></a>
                <a href="#"><span>&#128101;</span><span>Customers</span></a>
                <a href="#"><span>&#128203;</span><span>Prescriptions</span></a>
                <a href="#"><span>&#128230;</span><span>Inventory</span></a>
                <a href="{{ route('admin.sales.index') }}" class="{{ request()->routeIs('admin.sales.*') ? 'active' : '' }}"><span>&#128181;</span><span>Sales</span></a>
                <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}"><span>&#128179;</span><span>Payments</span></a>
            </nav>
            <div class="sidebar-footer"><a href="#"><span>&#8618;</span><span>Log Out</span></a></div>
        </aside>
        <main class="main">@yield('content')</main>
    </div>
</body>
</html>
