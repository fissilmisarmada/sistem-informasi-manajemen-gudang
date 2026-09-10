<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="color-scheme" content="light">
    @vite(['resources/css/app.css', 'resources/css/dashboard.css', 'resources/js/app.js'])
    <title>{{ config('app.name', 'Gudang') }} — Manajemen Gudang</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
</head>
<body>
    <header class="site-header">
        <div class="site-header__bar"></div>
        <div class="site-header__inner">
            @auth
                <div class="site-header__profile">
                    <span class="site-header__avatar" aria-hidden="true">{{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                    <div class="site-header__copy">
                        <span class="site-header__name">{{ auth()->user()->name }}</span>
                        <span class="site-header__meta">{{ strtoupper(auth()->user()->role ?? 'USER') }} · SISTEM GUDANG</span>
                    </div>
                </div>
                <div class="site-header__actions">
                    <span class="site-header__shift" aria-hidden="true"><span class="andon-dot"></span> {{ now()->format('d M Y') }} · ONLINE</span>
                    <form method="POST" action="{{ route('logout') }}" class="site-header__logout">
                        @csrf
                        <button type="submit" class="btn btn--ghost">Keluar</button>
                    </form>
                </div>
            @else
                <div class="site-header__brand">GUDANG</div>
                <a class="btn btn--primary" href="{{ route('login') }}">Login</a>
            @endauth
        </div>
    </header>
    <main class="site-main">
        @yield('content')
    </main>
</body>
</html>
