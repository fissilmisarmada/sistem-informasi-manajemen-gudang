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
    <body class="{{ request()->routeIs('login', 'password.*') ? 'body--auth' : '' }}">
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
    <div class="site-layout">
        @auth
        <aside class="side-nav" aria-label="Navigasi utama">
            @php $isActiveSide = fn($p) => request()->routeIs(...(array)$p); $roleSide = auth()->user()->role ?? ''; @endphp
            @if($roleSide === 'admin')
                <a href="{{ route('dashboard.admin') }}" class="side-nav__item {{ $isActiveSide('dashboard.admin') ? 'side-nav__item--active' : '' }}" aria-label="Beranda">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z"/></svg><span>Beranda</span>
                </a>
                <a href="{{ route('barang.index') }}" class="side-nav__item {{ $isActiveSide('barang.*') ? 'side-nav__item--active' : '' }}" aria-label="Barang">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05"/><path d="M12 22.08V12"/></svg><span>Barang</span>
                </a>
                <a href="{{ route('pencarian.input') }}" class="side-nav__item side-nav__item--scan {{ $isActiveSide(['pencarian.input','pencarian.input.*']) ? 'side-nav__item--active' : '' }}" aria-label="Scan">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 7V5a2 2 0 012-2h2"/><path d="M17 3h2a2 2 0 012 2v2"/><path d="M21 17v2a2 2 0 01-2 2h-2"/><path d="M7 21H5a2 2 0 01-2-2v-2"/><path d="M7 12h10"/><path d="M7 8h10"/><path d="M7 16h10"/></svg><span>Scan</span>
                </a>
                <a href="{{ route('denah-gudang') }}" class="side-nav__item {{ $isActiveSide(['denah-gudang','denah-area.*','rak.*']) ? 'side-nav__item--active' : '' }}" aria-label="Denah">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg><span>Denah</span>
                </a>
                <a href="{{ route('laporan.index') }}" class="side-nav__item {{ $isActiveSide('laporan.*') ? 'side-nav__item--active' : '' }}" aria-label="Laporan">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V8z"/><path d="M14 2v5h5"/><path d="M10 13H8"/><path d="M16 17H8"/></svg><span>Laporan</span>
                </a>
            @elseif($roleSide === 'staff')
                <a href="{{ route('dashboard.staff') }}" class="side-nav__item {{ $isActiveSide('dashboard.staff') ? 'side-nav__item--active' : '' }}" aria-label="Beranda">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z"/></svg><span>Beranda</span>
                </a>
                <a href="{{ route('barang.index') }}" class="side-nav__item {{ $isActiveSide(['barang.*','kategori.*']) ? 'side-nav__item--active' : '' }}" aria-label="Barang">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05"/><path d="M12 22.08V12"/></svg><span>Barang</span>
                </a>
                <a href="{{ route('pencarian.input') }}" class="side-nav__item side-nav__item--scan {{ $isActiveSide(['pencarian.input','pencarian.input.*','pencarian.index']) ? 'side-nav__item--active' : '' }}" aria-label="Scan">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 7V5a2 2 0 012-2h2"/><path d="M17 3h2a2 2 0 012 2v2"/><path d="M21 17v2a2 2 0 01-2 2h-2"/><path d="M7 21H5a2 2 0 01-2-2v-2"/><path d="M7 12h10"/><path d="M7 8h10"/><path d="M7 16h10"/></svg><span>Scan</span>
                </a>
                <a href="{{ route('denah-gudang') }}" class="side-nav__item {{ $isActiveSide(['denah-gudang','denah-area.*','rak.*']) ? 'side-nav__item--active' : '' }}" aria-label="Denah">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg><span>Denah</span>
                </a>
                <a href="{{ route('stock-opname-barang.index') }}" class="side-nav__item {{ $isActiveSide('stock-opname-barang.*') ? 'side-nav__item--active' : '' }}" aria-label="Opname">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 12h6"/><path d="M14 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V7z"/><path d="M14 2v5h5"/></svg><span>Opname</span>
                </a>
            @else
                <a href="{{ route('dashboard.pimpinan') }}" class="side-nav__item {{ $isActiveSide('dashboard.pimpinan') ? 'side-nav__item--active' : '' }}" aria-label="Beranda">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z"/></svg><span>Beranda</span>
                </a>
                <a href="{{ route('barang.index') }}" class="side-nav__item {{ $isActiveSide('barang.*') ? 'side-nav__item--active' : '' }}" aria-label="Barang">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05"/><path d="M12 22.08V12"/></svg><span>Barang</span>
                </a>
                <a href="{{ route('denah-gudang') }}" class="side-nav__item {{ $isActiveSide(['denah-gudang','denah-area.*','rak.*']) ? 'side-nav__item--active' : '' }}" aria-label="Denah">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg><span>Denah</span>
                </a>
                <a href="{{ route('laporan.index') }}" class="side-nav__item {{ $isActiveSide('laporan.*') ? 'side-nav__item--active' : '' }}" aria-label="Laporan">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V8z"/><path d="M14 2v5h5"/><path d="M10 13H8"/><path d="M16 17H8"/></svg><span>Laporan</span>
                </a>
            @endif
        </aside>
        @endauth
        <main class="site-main">
            @yield('content')
        </main>
    </div>
    @auth
    @if(request()->routeIs('barang.index') && in_array(auth()->user()->role ?? '', ['admin','staff'], true))
        <a href="{{ route('barang.create') }}" class="fab-add" aria-label="Tambah barang">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:24px;height:24px" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        </a>
    @elseif(request()->routeIs('kategori.index') && (auth()->user()->role ?? '') === 'admin')
        <button type="button" class="fab-add" aria-label="Tambah kategori" onclick="document.getElementById('modal-tambah')?.classList.add('active')">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:24px;height:24px" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        </button>
    @endif
    <nav class="bottom-nav" aria-label="Navigasi utama">
        @php $isActive = fn($p) => request()->routeIs(...(array)$p); $role = auth()->user()->role ?? ''; @endphp
        @if($role === 'admin')
            <a href="{{ route('dashboard.admin') }}" class="bottom-nav__item {{ $isActive('dashboard.admin') ? 'bottom-nav__item--active' : '' }}" aria-label="Beranda">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z"/></svg><span>Beranda</span>
            </a>
            <a href="{{ route('barang.index') }}" class="bottom-nav__item {{ $isActive('barang.*') ? 'bottom-nav__item--active' : '' }}" aria-label="Barang">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05"/><path d="M12 22.08V12"/></svg><span>Barang</span>
            </a>
            <a href="{{ route('pencarian.input') }}" class="bottom-nav__item bottom-nav__item--scan {{ $isActive(['pencarian.input','pencarian.input.*']) ? 'bottom-nav__item--active' : '' }}" aria-label="Scan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 7V5a2 2 0 012-2h2"/><path d="M17 3h2a2 2 0 012 2v2"/><path d="M21 17v2a2 2 0 01-2 2h-2"/><path d="M7 21H5a2 2 0 01-2-2v-2"/><path d="M7 12h10"/><path d="M7 8h10"/><path d="M7 16h10"/></svg><span>Scan</span>
            </a>
            <a href="{{ route('denah-gudang') }}" class="bottom-nav__item {{ $isActive(['denah-gudang','denah-area.*','rak.*']) ? 'bottom-nav__item--active' : '' }}" aria-label="Denah">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg><span>Denah</span>
            </a>
            <a href="{{ route('laporan.index') }}" class="bottom-nav__item {{ $isActive('laporan.*') ? 'bottom-nav__item--active' : '' }}" aria-label="Laporan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V8z"/><path d="M14 2v5h5"/><path d="M10 13H8"/><path d="M16 17H8"/></svg><span>Laporan</span>
            </a>
        @elseif($role === 'staff')
            <a href="{{ route('dashboard.staff') }}" class="bottom-nav__item {{ $isActive('dashboard.staff') ? 'bottom-nav__item--active' : '' }}" aria-label="Beranda">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z"/></svg><span>Beranda</span>
            </a>
            <a href="{{ route('barang.index') }}" class="bottom-nav__item {{ $isActive(['barang.*','kategori.*']) ? 'bottom-nav__item--active' : '' }}" aria-label="Barang">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05"/><path d="M12 22.08V12"/></svg><span>Barang</span>
            </a>
            <a href="{{ route('pencarian.input') }}" class="bottom-nav__item bottom-nav__item--scan {{ $isActive(['pencarian.input','pencarian.input.*','pencarian.index']) ? 'bottom-nav__item--active' : '' }}" aria-label="Scan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 7V5a2 2 0 012-2h2"/><path d="M17 3h2a2 2 0 012 2v2"/><path d="M21 17v2a2 2 0 01-2 2h-2"/><path d="M7 21H5a2 2 0 01-2-2v-2"/><path d="M7 12h10"/><path d="M7 8h10"/><path d="M7 16h10"/></svg><span>Scan</span>
            </a>
            <a href="{{ route('denah-gudang') }}" class="bottom-nav__item {{ $isActive(['denah-gudang','denah-area.*','rak.*']) ? 'bottom-nav__item--active' : '' }}" aria-label="Denah">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg><span>Denah</span>
            </a>
            <a href="{{ route('stock-opname-barang.index') }}" class="bottom-nav__item {{ $isActive('stock-opname-barang.*') ? 'bottom-nav__item--active' : '' }}" aria-label="Opname">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M9 12h6"/><path d="M14 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V7z"/><path d="M14 2v5h5"/></svg><span>Opname</span>
            </a>
        @else
            <a href="{{ route('dashboard.pimpinan') }}" class="bottom-nav__item {{ $isActive('dashboard.pimpinan') ? 'bottom-nav__item--active' : '' }}" aria-label="Beranda">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1V9.5z"/></svg><span>Beranda</span>
            </a>
            <a href="{{ route('barang.index') }}" class="bottom-nav__item {{ $isActive('barang.*') ? 'bottom-nav__item--active' : '' }}" aria-label="Barang">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05"/><path d="M12 22.08V12"/></svg><span>Barang</span>
            </a>
            <a href="{{ route('denah-gudang') }}" class="bottom-nav__item {{ $isActive(['denah-gudang','denah-area.*','rak.*']) ? 'bottom-nav__item--active' : '' }}" aria-label="Denah">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M15 3v18M3 9h18M3 15h18"/></svg><span>Denah</span>
            </a>
            <a href="{{ route('laporan.index') }}" class="bottom-nav__item {{ $isActive('laporan.*') ? 'bottom-nav__item--active' : '' }}" aria-label="Laporan">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V8z"/><path d="M14 2v5h5"/><path d="M10 13H8"/><path d="M16 17H8"/></svg><span>Laporan</span>
            </a>
        @endif
    </nav>
    @endauth
</body>
</html>
