<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name', 'SEEBOOK') }}</title>
    <style>
        :root {
            --ut-navy: #1C396A;
            --ut-blue: #1651A4;
            --ut-yellow: #F7D60A;
            --ut-light-blue: #0096FF;
            --ut-green: #357A38;
            --ut-red: #D32F2F;
            --ut-bg: #F8F9FA;
            --ut-text: #212529;
            --ut-border: #e1e6ed;
        }
        * { box-sizing: border-box; }
        body { font-family: Segoe UI, Arial, sans-serif; margin: 0; background: var(--ut-bg); color: var(--ut-text); }
        a { text-decoration: none; }
        .navbar {
            background: var(--ut-yellow);
            color: var(--ut-navy);
            padding: 14px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(28,57,106,.14);
            box-shadow: 0 6px 18px rgba(28,57,106,.12);
            position: relative;
        }
        .navbar::before { content:''; position:absolute; inset:0; background-image:url('/images/batikplb.png'); background-position:0 0; background-size:120px 120px; background-repeat:repeat; opacity:.18; mix-blend-mode:multiply; pointer-events:none; }
        .navbar > * { position:relative; z-index:1; }
        .nav-right { display: flex; align-items: center; gap: 10px; }
        .nav-right a {
            color: var(--ut-navy); padding: 8px 12px; border-radius: 8px; font-size: 14px; font-weight: 700;
            transition: background 0.2s ease;
        }
        .nav-right a:hover { background: rgba(28,57,106,.1); }
        .user-pill {
            display: inline-flex; align-items: center; gap: 8px; background: rgba(28,57,106,.1); padding: 8px 12px; border-radius: 999px; color: var(--ut-navy); font-size: 14px; font-weight: 700;
        }
        .logout-btn {
            background: var(--ut-navy); border: 1px solid var(--ut-navy); color: #fff; padding: 8px 12px; border-radius: 8px; cursor: pointer; font-weight: 700;
            transition: background 0.2s ease;
        }
        .logout-btn:hover { background: var(--ut-blue); border-color: var(--ut-blue); }
        .container { padding: 24px; max-width: 1400px; margin: 0 auto; }
        .stat-card { position: relative; transition: transform .2s ease, box-shadow .2s ease, filter .2s ease; cursor: pointer; }
        .stat-card::after { content: '->'; position: absolute; top: 18px; right: 20px; font-size: 18px; font-weight: 800; opacity: .7; transition: transform .2s ease; }
        .stat-card:hover { transform: translateY(-6px); box-shadow: 0 18px 34px rgba(15,23,42,.22) !important; filter: saturate(1.12); }
        .stat-card:hover::after { transform: translate(4px, -4px); }
        .back-dashboard { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 18px; padding: 10px 14px; background: #e8eef7; color: var(--ut-navy); border-radius: 9px; font-weight: 700; }
        .back-dashboard:hover { background: #d8e4f4; }

        .btn-primary, .btn-blue, .btn-dark { background: linear-gradient(135deg, var(--ut-navy), var(--ut-blue)) !important; color: #fff !important; }
        .btn-primary:hover, .btn-blue:hover, .btn-dark:hover { background: linear-gradient(135deg, var(--ut-blue), var(--ut-navy)) !important; }
        .btn-danger, .btn-red { background: var(--ut-red) !important; color: #fff !important; }
        .btn-warning { background: var(--ut-yellow) !important; color: var(--ut-text) !important; }
        .btn-success { background: var(--ut-green) !important; color: #fff !important; }
        .btn-secondary, .btn-light { background: #e8eef7 !important; color: var(--ut-navy) !important; }
        .btn-secondary:hover, .btn-light:hover { background: #d8e4f4 !important; }
        a { color: var(--ut-blue); }
        input:focus, select:focus, textarea:focus { border-color: var(--ut-light-blue) !important; box-shadow: 0 0 0 3px rgba(0,150,255,.15); }
        th { background: #eef3f9 !important; color: var(--ut-navy) !important; }
        .badge-green, .status.ok { background: #e2f0e3 !important; color: var(--ut-green) !important; }
        .badge-red, .status.low { background: #fde4e4 !important; color: var(--ut-red) !important; }

        @media (max-width:600px) {
            .navbar { padding: 10px 12px; }
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

    <main class="container">
        @yield('content')
    </main>
</body>
</html>
