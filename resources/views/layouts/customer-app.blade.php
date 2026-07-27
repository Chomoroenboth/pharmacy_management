<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PharmaCare MS')</title>
    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body { margin: 0; min-width: 320px; background: #f6f8fa; color: #202124; font-family: Inter, Arial, Helvetica, sans-serif; }
        .customer-header { min-height: 64px; display: flex; align-items: center; gap: 28px; padding: 0 42px; background: #fff; border-bottom: 1px solid #e7ebea; box-shadow: 0 1px 4px rgba(21, 46, 35, .06); }
        .brand { color: #007c5a; font-size: 24px; font-weight: 750; letter-spacing: -.55px; text-decoration: none; white-space: nowrap; }
        .global-search { display: flex; align-items: center; gap: 10px; width: min(608px, 50vw); padding: 10px 13px; border: 1px solid #e0e4e4; border-radius: 8px; background: #f4f5f6; color: #69736f; font-size: 14px; }
        .search-icon { width: 16px; height: 16px; border: 2px solid currentColor; border-radius: 50%; position: relative; flex: 0 0 auto; }
        .search-icon::after { content: ''; width: 7px; height: 2px; position: absolute; right: -5px; bottom: -3px; background: currentColor; border-radius: 2px; transform: rotate(45deg); }
        .account { display: flex; align-items: center; gap: 12px; margin-left: auto; font-size: 14px; color: #26332e; white-space: nowrap; }
        .avatar { display: grid; width: 33px; height: 33px; place-items: center; border-radius: 50%; background: #10b981; color: #063c2d; font-size: 12px; font-weight: 700; }
        .logout { color: #26332e; font-weight: 600; text-decoration: none; }
        .customer-nav { display: flex; gap: 32px; min-height: 65px; padding: 0 42px; background: #fff; border-bottom: 1px solid #e7ebea; }
        .customer-nav a { display: flex; align-items: center; padding: 0 16px; color: #4a5550; border-bottom: 3px solid transparent; font-size: 14px; font-weight: 600; text-decoration: none; }
        .customer-nav a.active { color: #008562; border-bottom-color: #13a97b; }
        .customer-main { padding: 24px 42px 56px; }
        @media (max-width: 700px) { .customer-header { gap: 16px; padding: 0 20px; } .brand { font-size: 20px; } .global-search, .account > span, .logout { display: none; } .customer-nav { gap: 0; padding: 0 12px; justify-content: space-around; } .customer-nav a { padding: 0 10px; } .customer-main { padding: 18px 16px 40px; } }
    </style>
    @yield('page-css')
</head>
<body>
    <header class="customer-header">
        <a class="brand" href="{{ route('customer.prescriptions') }}">PharmaCare MS</a>
        <div class="global-search" aria-label="Search"><span class="search-icon" aria-hidden="true"></span>Search medicines, symptoms, brands...</div>
        <div class="account"><span class="avatar">JD</span><span>John Doe</span><a class="logout" href="#">Log Out</a></div>
    </header>
    <nav class="customer-nav" aria-label="Customer navigation">
        <a href="#">Dashboard</a>
        <a href="{{ route('customer.prescriptions') }}" class="{{ request()->routeIs('customer.prescriptions*') ? 'active' : '' }}">Prescriptions</a>
        <a href="#">Profile</a>
    </nav>
    <main class="customer-main">@yield('content')</main>
</body>
</html>
