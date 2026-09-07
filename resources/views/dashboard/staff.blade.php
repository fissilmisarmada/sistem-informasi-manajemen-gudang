@extends('layouts.app')

@section('content')
    @php
        $bukuKategori = \App\Models\Kategori::where('kode_kategori', 'BKU')->first();
        $bukuKategoriId = $bukuKategori?->id;

        $totalBuku = \App\Models\Barang::where('kategori_id', $bukuKategoriId)->count();
        $totalBarang = \App\Models\Barang::where('kategori_id', '!=', $bukuKategoriId)->count();
        $totalItem = $totalBuku + $totalBarang;
        $barangMenipis = \App\Models\Barang::whereRaw('stok <= stok_minimum AND stok_minimum > 0')
            ->where('kategori_id', '!=', $bukuKategoriId)->count();
        $bukuMenipis = \App\Models\Barang::whereRaw('stok <= stok_minimum AND stok_minimum > 0')
            ->where('kategori_id', $bukuKategoriId)->count();
        $totalRak = \App\Models\Rak::count();
        $totalKategori = \App\Models\Kategori::count();
        $barangMenipis3 = \App\Models\Barang::whereRaw('stok <= stok_minimum AND stok_minimum > 0')->orderBy('nama')->take(3)->get();
        $aktivitasTerbaru = \App\Models\MutasiBarang::with(['barang.kategori', 'staff'])->latest('created_at')->take(4)->get();
    @endphp

    <style>
        .staff-dashboard { max-width:1120px; margin:0 auto; padding:10px 0 36px; }
        .staff-topbar { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px; }
        .staff-brand { color:#164194; font-weight:900; letter-spacing:.3px; font-size:17px; }
        .staff-profile { display:flex; align-items:center; gap:9px; color:#334155; font-size:13px; font-weight:700; }
        .staff-avatar { width:34px; height:34px; border-radius:50%; display:grid; place-items:center; background:#dbeafe; color:#164194; font-weight:900; }
        .staff-welcome { margin-bottom:22px; }
        .staff-welcome small { display:block; color:#64748b; font-size:12px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; }
        .staff-welcome h1 { margin:7px 0 0; color:#0f172a; font-size:32px; }
        .staff-welcome p { margin:6px 0 0; color:#64748b; font-size:12px; }
        .barcode-action { display:flex; align-items:center; justify-content:space-between; gap:18px; padding:18px 20px; margin-bottom:22px; border-radius:12px; background:linear-gradient(115deg,#1C396A 0%,#1651A4 48%,#357A38 78%,#F7D60A 115%); color:#fff; box-shadow:0 7px 16px rgba(28,57,106,.18); transition:transform .2s ease, box-shadow .2s ease, background .2s ease; }
        .barcode-action:hover, .barcode-action:focus-visible { background:linear-gradient(115deg,#1651A4 0%,#357A38 58%,#F7D60A 105%); box-shadow:0 12px 22px rgba(22,81,164,.25); transform:translateY(-3px); outline:0; }
        .barcode-icon { width:52px; height:52px; display:grid; place-items:center; background:#fff; color:#1651A4; border-radius:12px; font-size:25px; font-weight:900; }
        .barcode-action h2 { margin:0 0 4px; font-size:16px; }
        .barcode-action p { margin:0; color:#dbeafe; font-size:12px; }
        .barcode-arrow { font-size:28px; color:#bfdbfe; }
        .section-label { display:flex; align-items:center; justify-content:space-between; margin:24px 0 12px; }
        .section-label h2 { margin:0; font-size:15px; color:#1e3a6f; }
        .section-label a { color:#2563eb; font-size:12px; font-weight:800; }
        .overview { display:grid; grid-template-columns:1.4fr 1fr 1fr 1fr; gap:12px; margin-bottom:22px; }
        .overview-item { position:relative; min-height:108px; padding:18px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .overview-item::after { content:'->'; position:absolute; top:17px; right:18px; color:#2563eb; font-size:17px; font-weight:800; opacity:.65; transition:transform .2s ease, opacity .2s ease; }
        .overview-item:hover, .overview-item:focus-visible { border-color:#93c5fd; box-shadow:0 14px 26px rgba(15,74,165,.15); transform:translateY(-5px); outline:0; }
        .overview-item:hover::after, .overview-item:focus-visible::after { opacity:1; transform:translate(4px, -3px); }
        .overview-item.primary { background:linear-gradient(135deg,#1C396A 0%,#1651A4 52%,#357A38 86%,#F7D60A 125%); border-color:#1C396A; }
        .overview-item.danger { background:#991b1b; border-color:#991b1b; }
        .overview-item small { display:block; color:#64748b; font-size:11px; font-weight:700; }
        .overview-item.primary small, .overview-item.danger small { color:#bfdbfe; }
        .overview-item strong { display:block; margin-top:10px; color:#0f172a; font-size:29px; line-height:1; }
        .overview-item.primary strong, .overview-item.danger strong { color:#fff; }
        .overview-item em { display:block; margin-top:8px; color:#64748b; font-size:11px; font-style:normal; }
        .overview-item.primary em, .overview-item.danger em { color:#dbeafe; }
        .quick-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:9px; }
        .quick-action { min-height:84px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:8px; background:#fff; border:1px solid #e2e8f0; border-radius:11px; color:#1e3a6f; font-size:10px; font-weight:800; text-align:center; box-shadow:0 4px 12px rgba(15,23,42,.04); transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .quick-action:hover, .quick-action:focus-visible { border-color:#93c5fd; box-shadow:0 10px 18px rgba(15,74,165,.13); transform:translateY(-4px); outline:0; }
        .quick-action span { color:#2563eb; font-size:20px; line-height:1; }
        .dashboard-panel { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; }
        .dashboard-row { display:flex; align-items:center; gap:12px; padding:13px 16px; border-bottom:1px solid #f1f5f9; }
        .dashboard-row:last-child { border-bottom:0; }
        .row-icon { width:34px; height:40px; display:grid; place-items:center; border-radius:7px; background:#fee2e2; color:#991b1b; font-weight:900; font-size:12px; }
        .row-copy { flex:1; min-width:0; }
        .row-copy strong { display:block; color:#1e3a6f; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .row-copy small { color:#64748b; font-size:11px; }
        .row-meta { color:#dc2626; font-weight:800; font-size:11px; white-space:nowrap; }
        .row-icon.mutation-in { background:#dcfce7; color:#16a34a; }
        .row-icon.mutation-out { background:#fee2e2; color:#dc2626; }
        .empty-state { padding:20px; color:#64748b; font-size:13px; }
        @media (max-width:900px) { .overview { grid-template-columns:repeat(2,1fr); } }
        @media (max-width:700px) { .staff-dashboard { padding-top:0; } .quick-grid { grid-template-columns:repeat(3,1fr); } .barcode-action { padding:16px; } .staff-welcome h1 { font-size:26px; } }
        @media (max-width:560px) { .staff-welcome h1 { font-size:27px; } .staff-welcome p { line-height:1.45; } .barcode-action { gap:12px; } .barcode-icon { width:44px; height:44px; font-size:21px; } .barcode-action p { line-height:1.4; } .overview { gap:8px; } .overview-item { min-height:104px; padding:14px 12px; } .overview-item strong { font-size:24px; } .overview-item em { line-height:1.35; } }
    </style>

    <div class="staff-dashboard">
        <section class="staff-welcome">
            <small>Ruang kerja operasional</small>
            <h1>Dashboard Staff</h1>
            <p>Manajemen Gudang & Stok Universitas Terbuka</p>
        </section>

        <a href="{{ route('pencarian.input') }}" class="barcode-action">
            <span class="barcode-icon">🔍</span>
            <span style="flex:1;"><h2>Scan & Input Barang</h2><p>Scan barcode untuk menambah stok atau masukkan barang baru ke gudang</p></span>
            <span class="barcode-arrow">&rsaquo;</span>
        </a>

        <div class="section-label"><h2>Ringkasan Inventaris Gudang</h2><a href="{{ route('laporan.index') }}">Lihat semua</a></div>
        <div class="overview">
            <a class="overview-item primary" href="{{ route('barang.index') }}"><small>Total Item</small><strong>{{ number_format($totalItem, 0, ',', '.') }}</strong><em>{{ $totalBuku }} buku + {{ $totalBarang }} barang</em></a>
            <a class="overview-item" href="{{ route('kategori.index') }}"><small>Kategori</small><strong>{{ number_format($totalKategori, 0, ',', '.') }}</strong><em>Tipe barang terdaftar</em></a>
            <a class="overview-item {{ $barangMenipis > 0 || $bukuMenipis > 0 ? 'danger' : '' }}" href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}"><small>⚠ Stok Menipis</small><strong>{{ $barangMenipis + $bukuMenipis }}</strong><em>Perlu restock segera</em></a>
            <a class="overview-item" href="{{ route('rak.index', ['from' => 'dashboard']) }}"><small>Kelola Lokasi Rak</small><strong>{{ number_format($totalRak, 0, ',', '.') }}</strong><em>Atur lokasi penyimpanan</em></a>
        </div>

        <div class="section-label"><h2>Menu Cepat</h2></div>
        <div class="quick-grid">
            <a class="quick-action" href="{{ route('pencarian.index', ['from' => 'dashboard']) }}"><span>🔍</span>Cari Barang</a>
            <a class="quick-action" href="{{ route('mutasi-barang.create', ['from' => 'dashboard']) }}"><span>↔</span>Mutasi</a>
            <a class="quick-action" href="{{ route('stock-opname-barang.index', ['from' => 'dashboard']) }}"><span>✓</span>Opname Barang</a>
            <a class="quick-action" href="{{ route('denah-gudang', ['from' => 'dashboard']) }}"><span>⌂</span>Denah Gudang</a>
            <a class="quick-action" href="{{ route('laporan.index', ['from' => 'dashboard']) }}"><span>▥</span>Laporan</a>
        </div>

        <div class="section-label"><h2>⚠ Barang dengan Stok Menipis</h2><a href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}">Lihat semua &rsaquo;</a></div>
        <div class="dashboard-panel">
            @forelse($barangMenipis3 as $brg)
                <a class="dashboard-row" href="{{ route('barang.show', $brg) }}"><span class="row-icon">!</span><span class="row-copy"><strong>{{ $brg->nama }}</strong><small>{{ $brg->kategori->nama }} · Stok: {{ $brg->stok }} / min {{ $brg->stok_minimum }} {{ $brg->satuan }}</small></span><span class="row-meta">Restock &rsaquo;</span></a>
            @empty
                <div class="empty-state">✓ Semua barang memiliki stok yang mencukupi.</div>
            @endforelse
        </div>

        <div class="section-label"><h2>Mutasi Barang Terbaru</h2><a href="{{ route('mutasi-barang.index', ['from' => 'dashboard']) }}">Lihat semua &rsaquo;</a></div>
        <div class="dashboard-panel">
            @forelse($aktivitasTerbaru as $aktivitas)
                <div class="dashboard-row"><span class="row-icon {{ $aktivitas->jenis === 'masuk' ? 'mutation-in' : 'mutation-out' }}">{{ $aktivitas->jenis === 'masuk' ? '↑' : '↓' }}</span><span class="row-copy"><strong>{{ $aktivitas->barang->nama }}</strong><small>{{ $aktivitas->jenis === 'masuk' ? 'Barang masuk' : 'Barang keluar' }} · {{ $aktivitas->jumlah }} {{ $aktivitas->barang->satuan }} · Oleh {{ $aktivitas->staff?->name ?? 'Staff' }}</small></span><span style="color:#64748b;font-size:11px;">{{ \Carbon\Carbon::parse($aktivitas->created_at)->diffForHumans() }}</span></div>
            @empty
                <div class="empty-state">Belum ada aktivitas mutasi barang.</div>
            @endforelse
        </div>
    </div>
@endsection
