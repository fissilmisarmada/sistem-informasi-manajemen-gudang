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
            position: relative;
        }
        .menu-toggle { display: none; background: transparent; border: none; color: #fff; font-size: 24px; cursor: pointer; padding: 8px 12px; align-items: center; justify-content: center; }
        .menu-toggle:active { opacity: 0.8; }
        .nav-menu { display: flex; align-items: center; gap: 10px; flex: 1; }
        .nav-menu a {
            color: #e2e8f0; padding: 8px 12px; border-radius: 8px; font-size: 14px; font-weight: 600;
            transition: background 0.2s ease;
        }
        .nav-menu a:hover { background: rgba(255,255,255,0.08); }
        .nav-right { display: flex; align-items: center; gap: 10px; }
        .nav-right a {
            color: #e2e8f0; padding: 8px 12px; border-radius: 8px; font-size: 14px; font-weight: 600;
            transition: background 0.2s ease;
        }
        .nav-right a:hover { background: rgba(255,255,255,0.08); }
        .user-pill {
            display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.08); padding: 8px 12px; border-radius: 999px; color: #fff; font-size: 14px; font-weight: 600;
        }
        .logout-btn {
            background: transparent; border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-weight: 600;
            transition: background 0.2s ease;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.08); }
        .container { padding: 24px; max-width: 1400px; margin: 0 auto; }
        .stat-card { position: relative; transition: transform .2s ease, box-shadow .2s ease, filter .2s ease; cursor: pointer; }
        .stat-card::after { content: '->'; position: absolute; top: 18px; right: 20px; font-size: 18px; font-weight: 800; opacity: .7; transition: transform .2s ease; }
        .stat-card:hover { transform: translateY(-6px); box-shadow: 0 18px 34px rgba(15,23,42,.22) !important; filter: saturate(1.12); }
        .stat-card:hover::after { transform: translate(4px, -4px); }
        .back-dashboard { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 18px; padding: 10px 14px; background: #e2e8f0; color: #0f172a; border-radius: 9px; font-weight: 700; }
        .back-dashboard:hover { background: #cbd5e1; }

        @media (max-width:768px) {
            .navbar { flex-wrap: wrap; }
            .menu-toggle { display: flex; order: 1; }
            .nav-menu {
                display: none;
                order: 3;
                width: 100%;
                flex-direction: column;
                gap: 0;
                margin-top: 8px;
            }
            .nav-menu.active { display: flex; }
            .nav-menu a {
                width: 100%;
                padding: 12px 16px;
                border-radius: 0;
                border-bottom: 1px solid rgba(255,255,255,0.08);
                color: #e2e8f0;
                font-size: 14px;
                font-weight: 600;
            }
            .nav-right {
                order: 4;
                width: 100%;
                gap: 8px;
                padding-top: 12px;
                border-top: 1px solid rgba(255,255,255,0.08);
                justify-content: flex-end;
            }
        }

        @media (max-width:600px) {
            .navbar { padding: 10px 12px; }
            .nav-menu a { padding: 10px 14px; font-size: 13px; }
            .nav-right {
                gap: 4px;
                flex-wrap: wrap;
            }
            .user-pill { font-size: 12px; padding: 6px 10px; }
            .logout-btn { padding: 6px 10px; font-size: 12px; }
            .container { padding: 16px 12px; }
        }
    </style>
</head>
<body>
    <header class="navbar">
        <button class="menu-toggle" id="menuToggle" aria-label="Toggle Menu">☰</button>
        <div class="nav-menu" id="navMenu">
            @auth
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('dashboard.admin') }}">Dashboard</a>
                    <a href="{{ route('pencarian.index') }}">Cari Barang</a>
                    <a href="{{ route('barang.index') }}">Barang</a>
                    <a href="{{ route('kategori.index') }}">Kategori</a>
                    <a href="{{ route('mutasi-barang.index') }}">Mutasi</a>
                    <a href="{{ route('denah-gudang') }}">Denah Gudang</a>
                    <a href="{{ route('laporan.index') }}">Laporan</a>
                    <a href="{{ route('riwayat.index') }}">Riwayat</a>
                @elseif(auth()->user()->role === 'staff')
                    <a href="{{ route('dashboard.staff') }}">Dashboard</a>
                    <a href="{{ route('pencarian.index') }}">Cari Barang</a>
                    <a href="{{ route('barang.index') }}">Barang</a>
                    <a href="{{ route('mutasi-barang.index') }}">Mutasi</a>
                    <a href="{{ route('denah-gudang') }}">Denah Gudang</a>
                    <a href="{{ route('laporan.index') }}">Laporan</a>
                    <a href="{{ route('riwayat.index') }}">Riwayat</a>
                @elseif(auth()->user()->role === 'pimpinan')
                    <a href="{{ route('dashboard.pimpinan') }}">Dashboard</a>
                    <a href="{{ route('pencarian.index') }}">Cari Barang</a>
                    <a href="{{ route('denah-gudang') }}">Denah Gudang</a>
                    <a href="{{ route('laporan.index') }}">Laporan</a>
                @endif
            @endauth
        </div>
        <div class="nav-right">
            @auth
                <span class="user-pill">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}">Login</a>
            @endauth
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const navMenu = document.getElementById('navMenu');

            if (menuToggle && navMenu) {
                menuToggle.addEventListener('click', function() {
                    navMenu.classList.toggle('active');
                });

                // Close menu when a link is clicked
                navMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function() {
                        navMenu.classList.remove('active');
                    });
                });
            }
        });
    </script>

    <main class="container">
        @yield('content')
    </main>
</body>
</html>
