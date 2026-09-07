@extends('layouts.app')

@section('content')
    @php
        $bukuKategori = \App\Models\Kategori::where('kode_kategori', 'BKU')->first();
        $bukuKategoriId = $bukuKategori?->id;

        $totalBuku = \App\Models\Barang::where('kategori_id', $bukuKategoriId)->count();
        $totalBarang = \App\Models\Barang::where('kategori_id', '!=', $bukuKategoriId)->count();
        $totalItem = $totalBuku + $totalBarang;
        $totalRak = \App\Models\Rak::count();
        $totalKategori = \App\Models\Kategori::count();
        $barangMenipis = \App\Models\Barang::whereRaw('stok <= stok_minimum AND stok_minimum > 0')->count();
        $aktivitasTerbaru = \App\Models\MutasiBarang::with(['barang.kategori', 'staff'])->latest('created_at')->take(4)->get();
    @endphp

    <style>
        .leader-dashboard { max-width:1120px; margin:0 auto; padding:10px 0 36px; }
        .leader-heading { display:flex; justify-content:space-between; align-items:flex-end; gap:20px; margin-bottom:22px; }
        .leader-eyebrow { color:#64748b; font-size:12px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; }
        .leader-heading h1 { margin:7px 0 0; color:#0f172a; font-size:32px; }
        .leader-heading p { margin:0; color:#64748b; font-size:12px; }
        .scan-link { display:inline-flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; background:#0f4aa5; color:#fff; font-size:12px; font-weight:800; box-shadow:0 7px 16px rgba(15,74,165,.18); transition:transform .2s ease, box-shadow .2s ease, background .2s ease; }
        .scan-link:hover, .scan-link:focus-visible { background:#0b3c86; box-shadow:0 12px 22px rgba(15,74,165,.25); transform:translateY(-3px); outline:0; }
        .scan-link span { font-size:19px; line-height:1; }
        .overview { display:grid; grid-template-columns:1.4fr 1fr 1fr 1fr; gap:12px; margin-bottom:22px; }
        .overview-item { position:relative; min-height:108px; padding:18px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; transition:transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .overview-item::after { content:'->'; position:absolute; top:17px; right:18px; color:#2563eb; font-size:17px; font-weight:800; opacity:.65; transition:transform .2s ease, opacity .2s ease; }
        .overview-item:hover, .overview-item:focus-visible { border-color:#93c5fd; box-shadow:0 14px 26px rgba(15,74,165,.15); transform:translateY(-5px); outline:0; }
        .overview-item:hover::after, .overview-item:focus-visible::after { opacity:1; transform:translate(4px, -3px); }
        .overview-item.primary { background:linear-gradient(135deg,#1C396A 0%,#1651A4 52%,#357A38 86%,#F7D60A 125%); border-color:#1C396A; color:#fff; }
        .overview-item.danger { background:#991b1b; border-color:#991b1b; color:#fff; }
        .overview-item.primary::after { color:#dbeafe; }
        .overview-item.danger::after { color:#fecaca; }
        .overview-item small { display:block; color:#64748b; font-size:11px; font-weight:700; }
        .overview-item.primary small, .overview-item.danger small { color:#bfdbfe; }
        .overview-item strong { display:block; margin-top:10px; color:#0f172a; font-size:29px; line-height:1; }
        .overview-item.primary strong { color:#fff; }
        .overview-item em { display:block; margin-top:8px; color:#64748b; font-size:11px; font-style:normal; }
        .overview-item.primary em { color:#dbeafe; }
        .leader-grid { display:grid; grid-template-columns:1.2fr .8fr; gap:18px; }
        .leader-panel { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; }
        .panel-head { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:17px 18px; border-bottom:1px solid #eef2f7; }
        .panel-head h2 { margin:0; color:#1e3a6f; font-size:15px; }
        .panel-head a { color:#2563eb; font-size:11px; font-weight:800; }
        .inventory-list { padding:4px 18px 12px; }
        .inventory-row { display:flex; justify-content:space-between; gap:16px; padding:13px 0; border-bottom:1px solid #f1f5f9; color:#64748b; font-size:12px; }
        .inventory-row:last-child { border-bottom:0; }
        .inventory-row strong { color:#1e3a6f; }
        .activity-row { display:flex; align-items:center; gap:11px; padding:13px 18px; border-bottom:1px solid #f1f5f9; }
        .activity-row:last-child { border-bottom:0; }
        .activity-icon { width:31px; height:31px; display:grid; place-items:center; flex:none; border-radius:7px; background:#dcfce7; color:#15803d; font-size:11px; font-weight:900; }
        .activity-copy { min-width:0; flex:1; }
        .activity-copy strong { display:block; overflow:hidden; color:#1e3a6f; font-size:12px; text-overflow:ellipsis; white-space:nowrap; }
        .activity-copy small, .activity-time { color:#64748b; font-size:10px; }
        .activity-time { white-space:nowrap; }
        .empty-state { padding:20px 18px; color:#64748b; font-size:12px; }
        @media (max-width:800px) { .overview { grid-template-columns:repeat(2,1fr); } .leader-grid { grid-template-columns:1fr; } }
        @media (max-width:560px) { .leader-heading { display:block; margin-bottom:18px; } .leader-heading h1 { font-size:27px; } .leader-heading p { margin-top:6px; line-height:1.45; } .scan-link { width:100%; justify-content:center; margin-top:15px; } .overview { gap:8px; } .overview-item { min-height:104px; padding:14px 12px; } .overview-item strong { font-size:24px; } .overview-item em { line-height:1.35; } }
    </style>

    <div class="leader-dashboard">
        <div class="leader-heading">
            <div>
                <div class="leader-eyebrow">Ringkasan pengelolaan</div>
                <h1>Dashboard Pimpinan</h1>
                <p>Pantau kondisi inventaris dan aktivitas gudang secara keseluruhan.</p>
            </div>
            <a class="scan-link" href="{{ route('pencarian.index') }}"><span>🔍</span> Cari Barang</a>
        </div>

        <div class="overview">
            <a class="overview-item primary" href="{{ route('barang.index') }}"><small>Total Item Gudang</small><strong>{{ number_format($totalItem, 0, ',', '.') }}</strong><em>{{ $totalBuku }} buku + {{ $totalBarang }} barang lain</em></a>
            <a class="overview-item" href="{{ route('kategori.index') }}"><small>Kategori</small><strong>{{ number_format($totalKategori, 0, ',', '.') }}</strong><em>Jenis barang di gudang</em></a>
            <a class="overview-item" href="{{ route('denah-gudang') }}"><small>Rak Gudang</small><strong>{{ number_format($totalRak, 0, ',', '.') }}</strong><em>Lokasi penyimpanan</em></a>
            <a class="overview-item {{ $barangMenipis > 0 ? 'danger' : '' }}" href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}"><small>⚠ Stok Menipis</small><strong>{{ number_format($barangMenipis, 0, ',', '.') }}</strong><em>Butuh perhatian</em></a>
        </div>

        <div class="leader-grid">
            <section class="leader-panel">
                <div class="panel-head"><h2>Kondisi Inventaris Gudang</h2><a href="{{ route('laporan.index') }}">Lihat laporan &rsaquo;</a></div>
                <div class="inventory-list">
                    <div class="inventory-row"><span>Total barang</span><strong>{{ number_format($totalItem, 0, ',', '.') }} item</strong></div>
                    <div class="inventory-row"><span>Kategori</span><strong>{{ number_format($totalKategori, 0, ',', '.') }} kategori</strong></div>
                    <div class="inventory-row"><span>Rak gudang</span><strong>{{ number_format($totalRak, 0, ',', '.') }} rak</strong></div>
                    <div class="inventory-row"><span>Stok menipis</span><strong>{{ number_format($barangMenipis, 0, ',', '.') }} item</strong></div>
                </div>
            </section>

            <section class="leader-panel">
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
