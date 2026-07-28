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
            color: #191c1e;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            flex-shrink: 0;
            background: #04281b;
            color: #fff;
            display: flex;
            flex-direction: column;
            padding: 24px 0;
        }

        .sidebar-logo {
            padding: 0 24px 24px;
        }

        .sidebar-logo .name {
            color: #10b981;
            font-size: 20px;
            font-weight: 700;
        }

        .sidebar-logo .subtitle {
            color: #9fb0a8;
            font-size: 12px;
            margin-top: 2px;
        }

        .sidebar-nav {
            flex: 1;
            margin-top: 8px;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            color: #cfd8d3;
            text-decoration: none;
            font-size: 14px;
        }

        .sidebar-nav a:hover {
            background: rgba(255,255,255,0.05);
        }

        .sidebar-nav a.active {
            background: #10b981;
            color: #04281b;
            font-weight: 600;
        }

        .sidebar-footer {
            padding: 12px 24px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #cfd8d3;
            text-decoration: none;
            font-size: 14px;
        }

        .main {
            flex: 1;
            padding: 40px;
            min-width: 0;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 2px 8px rgba(0,0,0,0.08);
            padding: 32px;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
        }
    </style>

    @yield('page-css')
</head>

<body>

<div class="layout">

    <div class="sidebar">

        <div class="sidebar-logo">
            <div class="name">PharmaCare MS</div>
            <div class="subtitle">Management System</div>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">&#9635; Dashboard</a>
            <a href="{{ route('admin.customers') }}" class="{{ request()->routeIs('admin.customers*') ? 'active' : '' }}">&#128101; Customers</a>
            <a href="{{ route('admin.prescriptions') }}" class="{{ request()->routeIs('admin.prescriptions*') ? 'active' : '' }}">&#128203; Prescriptions</a>
            <a href="{{ route('admin.inventory') }}" class="{{ request()->routeIs('admin.inventory*') ? 'active' : '' }}">&#128230; Inventory</a>
            <a href="{{ route('admin.sales') }}" class="{{ request()->routeIs('admin.sales*') ? 'active' : '' }}">&#128181; Sales</a>
            <a href="{{ route('admin.payments') }}" class="{{ request()->routeIs('admin.payments*') ? 'active' : '' }}">&#128179; Payments</a>
        </nav>

        <div class="sidebar-footer">
            <a href="#">&#8618; Log Out</a>
        </div>

    </div>

    <div class="main">
        @yield('content')
    </div>

</div>

</body>
</html>