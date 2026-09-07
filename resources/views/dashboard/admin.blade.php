@extends('layouts.app')

@section('content')
    @php
        $bukuKategori = \App\Models\Kategori::where('kode_kategori', 'BKU')->first();
        $bukuKategoriId = $bukuKategori?->id;

        $totalBuku = \App\Models\Barang::where('kategori_id', $bukuKategoriId)->count();
        $totalBarang = \App\Models\Barang::where('kategori_id', '!=', $bukuKategoriId)->count();
        $totalStok = \App\Models\Barang::where('kategori_id', $bukuKategoriId)->sum('stok');
        $bukuDitempatkan = \App\Models\Barang::where('kategori_id', $bukuKategoriId)->whereNotNull('rak_id')->count();
        $totalItem = $totalBuku + $totalBarang;
        $totalRak = \App\Models\Rak::count();
        $totalKategori = \App\Models\Kategori::count();
        $totalUser = \App\Models\User::count();
        $totalAdmin = \App\Models\User::where('role', 'admin')->count();
        $totalStaff = \App\Models\User::where('role', 'staff')->count();
        $totalPimpinan = \App\Models\User::where('role', 'pimpinan')->count();
        $barangMenipis = \App\Models\Barang::whereRaw('stok <= stok_minimum AND stok_minimum > 0')->count();
        $barangMenipis3 = \App\Models\Barang::whereRaw('stok <= stok_minimum AND stok_minimum > 0')->orderBy('nama')->take(3)->get();
        $aktivitasTerbaru = \App\Models\MutasiBarang::with(['barang.kategori', 'staff'])->latest('created_at')->take(4)->get();
    @endphp

    <style>
        .admin-dashboard { max-width:1120px; margin:0 auto; padding:10px 0 36px; }
        .admin-heading { display:flex; justify-content:space-between; align-items:flex-end; gap:20px; margin-bottom:22px; }
        .admin-eyebrow { color:#64748b; font-size:12px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; }
        .admin-heading h1 { margin:7px 0 0; color:#0f172a; font-size:32px; }
        .admin-heading p { margin:0; color:#64748b; font-size:12px; }
        .scan-link { display:inline-flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; background:linear-gradient(115deg,#1C396A 0%,#1651A4 48%,#357A38 78%,#F7D60A 115%); color:#fff; font-size:12px; font-weight:800; box-shadow:0 7px 16px rgba(28,57,106,.18); transition:transform .2s ease, box-shadow .2s ease, background .2s ease; }
        .scan-link:hover, .scan-link:focus-visible { background:linear-gradient(115deg,#1651A4 0%,#357A38 58%,#F7D60A 105%); box-shadow:0 12px 22px rgba(22,81,164,.25); transform:translateY(-3px); outline:0; }
        .scan-link span { font-size:19px; line-height:1; }
        .overview { display:grid; grid-template-columns:1.4fr 1fr 1fr 1fr; gap:12px; margin-bottom:22px; }
        .overview-item { position:relative; min-height:108px; padding:18px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .overview-item::after { content:'->'; position:absolute; top:17px; right:18px; color:#2563eb; font-size:17px; font-weight:800; opacity:.65; transition:transform .2s ease, opacity .2s ease; }
        .overview-item:hover, .overview-item:focus-visible { border-color:#93c5fd; box-shadow:0 14px 26px rgba(15,74,165,.15); transform:translateY(-5px); outline:0; }
        .overview-item:hover::after, .overview-item:focus-visible::after { opacity:1; transform:translate(4px, -3px); }
        .overview-item.primary { background:linear-gradient(135deg,#1C396A 0%,#1651A4 52%,#357A38 86%,#F7D60A 125%); border-color:#1C396A; }
        .overview-item.primary::after { color:#dbeafe; }
        .overview-item small { display:block; color:#64748b; font-size:11px; font-weight:700; }
        .overview-item.primary small { color:#bfdbfe; }
        .overview-item strong { display:block; margin-top:10px; color:#0f172a; font-size:29px; line-height:1; }
        .overview-item.primary strong { color:#fff; }
        .overview-item em { display:block; margin-top:8px; color:#64748b; font-size:11px; font-style:normal; }
        .overview-item.primary em { color:#dbeafe; }
        .admin-section { margin:22px 0 12px; display:flex; justify-content:space-between; align-items:center; }
        .admin-section h2 { margin:0; color:#1e3a6f; font-size:15px; }
        .admin-section a { color:#2563eb; font-size:11px; font-weight:800; }
        .quick-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:9px; }
        .quick-action { min-height:84px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; background:#fff; border:1px solid #e2e8f0; border-radius:11px; color:#1e3a6f; font-size:10px; font-weight:800; text-align:center; box-shadow:0 4px 12px rgba(15,23,42,.04); transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .quick-action:hover, .quick-action:focus-visible { border-color:#93c5fd; box-shadow:0 10px 18px rgba(15,74,165,.13); transform:translateY(-4px); outline:0; }
        .quick-action span { color:#1651A4; font-size:20px; line-height:1; }
        .admin-grid { display:grid; grid-template-columns:1.2fr .8fr; gap:18px; }
        .admin-panel { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; }
        .panel-head { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:17px 18px; border-bottom:1px solid #eef2f7; }
        .panel-head h2 { margin:0; color:#1e3a6f; font-size:15px; }
        .panel-head a { color:#2563eb; font-size:11px; font-weight:800; }
        .data-row { display:flex; justify-content:space-between; gap:16px; padding:13px 18px; border-bottom:1px solid #f1f5f9; color:#64748b; font-size:12px; }
        .data-row:last-child { border-bottom:0; }
        .data-row strong { color:#1e3a6f; }
        .activity-row { display:flex; align-items:center; gap:11px; padding:13px 18px; border-bottom:1px solid #f1f5f9; }
        .activity-row:last-child { border-bottom:0; }
        .activity-icon { width:31px; height:31px; display:grid; place-items:center; flex:none; border-radius:7px; background:#dcfce7; color:#15803d; font-size:11px; font-weight:900; }
        .activity-copy { min-width:0; flex:1; }
        .activity-copy strong { display:block; overflow:hidden; color:#1e3a6f; font-size:12px; text-overflow:ellipsis; white-space:nowrap; }
        .activity-copy small, .activity-time { color:#64748b; font-size:10px; }
        .activity-time { white-space:nowrap; }
        .empty-state { padding:20px 18px; color:#64748b; font-size:12px; }
        @media (max-width:900px) { .quick-grid { grid-template-columns:repeat(3,1fr); } .overview { grid-template-columns:repeat(2,1fr); } .admin-grid { grid-template-columns:1fr; } }
        @media (max-width:560px) { .admin-heading { display:block; margin-bottom:18px; } .admin-heading h1 { font-size:27px; } .admin-heading p { margin-top:6px; line-height:1.45; } .scan-link { width:100%; justify-content:center; margin-top:15px; } .quick-grid { grid-template-columns:repeat(3,1fr); gap:7px; } .overview { gap:8px; } .overview-item { min-height:104px; padding:14px 12px; } .overview-item strong { font-size:24px; } .overview-item em { line-height:1.35; } .admin-section { margin-top:18px; } }
    </style>

    <div class="admin-dashboard">
        <div class="admin-heading">
            <div>
                <div class="admin-eyebrow">Pusat kendali sistem</div>
                <h1>Dashboard Admin</h1>
                <p>Kelola inventaris gudang, pengguna, dan aktivitas dari satu tempat.</p>
            </div>
            <a class="scan-link" href="{{ route('pencarian.input') }}"><span>▣</span> Scan & Input Barang</a>
        </div>

        <div class="overview">
            <a class="overview-item primary" href="{{ route('barang.index') }}"><small>Total Item</small><strong>{{ number_format($totalItem, 0, ',', '.') }}</strong><em>{{ $totalBuku }} buku + {{ $totalBarang }} barang</em></a>
            <a class="overview-item" href="{{ route('kategori.index') }}"><small>Kategori</small><strong>{{ number_format($totalKategori, 0, ',', '.') }}</strong><em>Jenis barang terdaftar</em></a>
            <a class="overview-item" href="{{ route('users') }}"><small>Total Pengguna</small><strong>{{ number_format($totalUser, 0, ',', '.') }}</strong><em>{{ $totalStaff }} staff, {{ $totalPimpinan }} pimpinan</em></a>
            <a class="overview-item" href="{{ route('rak.index', ['from' => 'dashboard']) }}"><small>Kelola Lokasi Rak</small><strong>{{ number_format($totalRak, 0, ',', '.') }}</strong><em>Atur lokasi penyimpanan</em></a>
        </div>

        <div class="admin-section"><h2>Menu Cepat</h2></div>
        <div class="quick-grid">
            <a class="quick-action" href="{{ route('mutasi-barang.create', ['from' => 'dashboard']) }}"><span>↔</span>Mutasi</a>
            <a class="quick-action" href="{{ route('stock-opname-barang.index', ['from' => 'dashboard']) }}"><span>✓</span>Opname</a>
            <a class="quick-action" href="{{ route('denah-gudang', ['from' => 'dashboard']) }}"><span>⌂</span>Denah Gudang</a>
            <a class="quick-action" href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}"><span>⚠</span>Stok Menipis</a>
            <a class="quick-action" href="{{ route('laporan.index', ['from' => 'dashboard']) }}"><span>▥</span>Laporan</a>
            <a class="quick-action" href="{{ route('riwayat.index', ['from' => 'dashboard']) }}"><span>≡</span>Riwayat</a>
        </div>

        <div class="admin-section"><h2>Informasi Sistem</h2></div>
        <div class="admin-grid">
            <section class="admin-panel">
                <div class="panel-head"><h2>Ringkasan Inventaris Gudang</h2></div>
                <div class="data-row"><span>Total item gudang</span><strong>{{ number_format($totalItem, 0, ',', '.') }} item ({{ $totalBuku }} buku + {{ $totalBarang }} barang)</strong></div>
                <div class="data-row"><span>Kategori barang</span><strong>{{ number_format($totalKategori, 0, ',', '.') }} kategori</strong></div>
                <div class="data-row"><span>Stok menipis</span><strong>{{ number_format($barangMenipis, 0, ',', '.') }} item perlu restock</strong></div>
                <div class="data-row"><span>Pengguna sistem</span><strong>{{ $totalAdmin }} admin, {{ $totalStaff }} staff, {{ $totalPimpinan }} pimpinan</strong></div>
            </section>
            <section class="admin-panel">
                <div class="panel-head"><h2>Mutasi Barang Terbaru</h2><a href="{{ route('mutasi-barang.index') }}">Semua &rsaquo;</a></div>
                @forelse($aktivitasTerbaru as $aktivitas)
                    <div class="activity-row"><span class="activity-icon">{{ $aktivitas->jenis === 'masuk' ? '↑' : '↓' }}</span><span class="activity-copy"><strong>{{ $aktivitas->barang->nama }}</strong><small>{{ $aktivitas->jenis === 'masuk' ? 'Masuk' : 'Keluar' }} · Oleh {{ $aktivitas->staff?->name ?? 'Staff' }}</small></span><span class="activity-time">{{ \Carbon\Carbon::parse($aktivitas->created_at)->diffForHumans() }}</span></div>
                @empty
                    <div class="empty-state">Belum ada aktivitas mutasi barang.</div>
                @endforelse
            </section>
        </div>
    </div>
@endsection
