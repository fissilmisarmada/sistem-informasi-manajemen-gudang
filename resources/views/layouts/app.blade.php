<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name', 'SEEBOOK') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Segoe UI, Arial, sans-serif; margin: 0; background: #f8fafc; color: #0f172a; }
        a { text-decoration: none; }
        .navbar {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #fff;
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
        }
        .nav-left, .nav-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .brand {
            font-weight: 800; font-size: 18px; letter-spacing: 0.5px; color: #fff; padding: 8px 12px; border-radius: 8px;
        }
        .nav-left a, .nav-right a {
            color: #e2e8f0; padding: 8px 12px; border-radius: 8px; font-size: 14px; font-weight: 600;
        }
        .nav-left a:hover, .nav-right a:hover { background: rgba(255,255,255,0.08); }
        .user-pill {
            display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.08); padding: 8px 12px; border-radius: 999px; color: #fff; font-size: 14px;
        }
        .logout-btn {
            background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-weight: 600;
        }
        .container { padding: 24px; max-width: 1400px; margin: 0 auto; }
        .stat-card { position: relative; transition: transform .2s ease, box-shadow .2s ease, filter .2s ease; cursor: pointer; }
        .stat-card::after { content: '->'; position: absolute; top: 18px; right: 20px; font-size: 18px; font-weight: 800; opacity: .7; transition: transform .2s ease; }
        .stat-card:hover { transform: translateY(-6px); box-shadow: 0 18px 34px rgba(15,23,42,.22) !important; filter: saturate(1.12); }
        .stat-card:hover::after { transform: translate(4px, -4px); }
        .back-dashboard { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 18px; padding: 10px 14px; background: #e2e8f0; color: #0f172a; border-radius: 9px; font-weight: 700; }
        .back-dashboard:hover { background: #cbd5e1; }
        @media (max-width:600px) {
            .navbar { padding:10px 12px; gap:8px; }
            .nav-left, .nav-right { width:100%; gap:3px; }
            .nav-left { justify-content:flex-start; }
            .nav-right { justify-content:flex-end; }
            .nav-left a, .nav-right a { padding:7px 8px; font-size:11px; }
            .user-pill { padding:7px 10px; font-size:11px; }
            .logout-btn { padding:7px 10px; font-size:11px; }
            .container { padding:16px 12px; }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <div class="nav-left">
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('dashboard.admin') }}">Dashboard</a>
                    <a href="{{ route('denah-gudang') }}">Denah Gudang</a>
                    <a href="{{ route('laporan.index') }}">Laporan</a>
                    <a href="{{ route('riwayat.index') }}">Riwayat</a>
                @elseif(auth()->user()->role === 'staff')
                    <a href="{{ route('dashboard.staff') }}">Dashboard</a>
                    <a href="{{ route('denah-gudang') }}">Denah Gudang</a>
                    <a href="{{ route('laporan.index') }}">Laporan</a>
                    <a href="{{ route('riwayat.index') }}">Riwayat</a>
                @elseif(auth()->user()->role === 'pimpinan')
                    <a href="{{ route('dashboard.pimpinan') }}">Dashboard</a>
                    <a href="{{ route('denah-gudang') }}">Denah Gudang</a>
                @endif
            @endauth
        </div>
        <div class="nav-right">
            @auth
                <span class="user-pill">{{ auth()->user()->name }}</span>
                <form style="display:inline" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
            @endauth
        </div>
    </header>

    <main class="container">
        @yield('content')
    </main>
</body>
</html>
