<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'TextPort Admin' }}</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; color: #111827; margin: 0; }
        .top { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; background: #111827; color: #fff; }
        .shell { display: grid; grid-template-columns: 240px 1fr; min-height: calc(100vh - 58px); }
        .sidebar { background: #1f2937; color: #e5e7eb; padding: 16px 12px; }
        .brand { font-size: 18px; font-weight: 700; margin-bottom: 14px; padding: 0 8px; }
        .nav a { display: block; color: #d1d5db; text-decoration: none; padding: 10px 12px; border-radius: 8px; margin-bottom: 6px; }
        .nav a.active, .nav a:hover { background: #374151; color: #fff; }
        .content { padding: 20px; overflow: auto; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 20px; overflow: auto; }
        .logout { background: #ef4444; color: #fff; border: 0; border-radius: 8px; padding: 8px 12px; cursor: pointer; }
        .muted { color: #6b7280; font-size: 13px; }
    </style>
</head>
<body>
<div class="top">
    <div>
        <strong>TextPort Admin Panel</strong>
        <div class="muted">Manage devices, SMS synchronization, and system logs</div>
    </div>
    <form method="post" action="{{ route('admin.logout') }}">
        @csrf
        <button class="logout" type="submit">Logout</button>
    </form>
</div>

<div class="shell">
    <aside class="sidebar">
        <div class="brand">Navigation</div>
        <nav class="nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Connected Devices</a>
            <a href="{{ route('admin.history') }}" class="{{ request()->routeIs('admin.history') ? 'active' : '' }}">Sync History</a>
            <a href="{{ route('admin.logs') }}" class="{{ request()->routeIs('admin.logs') ? 'active' : '' }}">System Logs</a>
        </nav>
    </aside>
    <main class="content">
        @yield('content')
    </main>
</div>
</body>
</html>
