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

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .staff-dashboard {
            max-width: 1120px;
            margin: 0 auto;
            padding: 24px 16px 48px;
            font-family: 'Inter', sans-serif;
        }

        /* Typography & Header */
        .staff-welcome { margin-bottom: 28px; }
        .staff-welcome small { display: inline-block; padding: 4px 12px; background: #e0e7ff; color: #4338ca; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 12px; }
        .staff-welcome h1 { margin: 0; color: #0f172a; font-size: 30px; font-weight: 800; letter-spacing: -0.5px; }
        .staff-welcome p { margin: 6px 0 0; color: #64748b; font-size: 14px; }

        .section-label { display: flex; align-items: center; justify-content: space-between; margin: 32px 0 16px; }
        .section-label h2 { margin: 0; font-size: 17px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px; }
        .section-label a { color: #3b82f6; font-size: 13px; font-weight: 600; text-decoration: none; transition: color 0.2s; }
        .section-label a:hover { color: #2563eb; text-decoration: underline; }

        /* Modern Gradient Banner (Mempertahankan warna asli) */
        .barcode-action {
            display: flex; align-items: center; justify-content: space-between; gap: 20px;
            padding: 24px 28px; margin-bottom: 28px; border-radius: 20px; text-decoration: none;
            background: linear-gradient(115deg, #1C396A 0%, #1651A4 48%, #357A38 78%, #F7D60A 115%);
            color: #fff; box-shadow: 0 10px 25px rgba(28, 57, 106, 0.25);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .barcode-action:hover {
            transform: translateY(-4px) scale(1.01);
            box-shadow: 0 20px 35px rgba(22, 81, 164, 0.3);
        }
        .barcode-icon {
            width: 56px; height: 56px; display: grid; place-items: center;
            background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(8px);
            color: #fff; border-radius: 16px;
        }
        .barcode-text { flex: 1; }
        .barcode-text h2 { margin: 0 0 6px; font-size: 20px; font-weight: 700; letter-spacing: -0.3px; }
        .barcode-text p { margin: 0; color: rgba(255, 255, 255, 0.9); font-size: 13px; font-weight: 400; line-height: 1.5; }
        .barcode-arrow { background: rgba(255, 255, 255, 0.15); border-radius: 50%; padding: 8px; transition: transform 0.2s; }
        .barcode-action:hover .barcode-arrow { transform: translateX(6px); background: rgba(255, 255, 255, 0.25); }

        /* Overview Cards - Soft UI */
        .overview { display: grid; grid-template-columns: 1.4fr 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px; }
        .overview-item {
            position: relative; padding: 22px; background: #fff; text-decoration: none;
            border-radius: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease; border: 1px solid rgba(226, 232, 240, 0.6); overflow: hidden;
        }
        .overview-item:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08); border-color: #cbd5e1; }

        .overview-item small { display: block; color: #64748b; font-size: 13px; font-weight: 600; }
        .overview-item strong { display: block; margin-top: 12px; color: #0f172a; font-size: 32px; font-weight: 800; line-height: 1; letter-spacing: -0.5px; }
        .overview-item em { display: block; margin-top: 10px; color: #94a3b8; font-size: 12px; font-style: normal; font-weight: 500; }

        /* Card Gradient */
        .overview-item.primary {
            background: linear-gradient(135deg, #1C396A 0%, #1651A4 52%, #357A38 86%, #F7D60A 125%);
            border: none; color: #fff; box-shadow: 0 10px 20px rgba(28, 57, 106, 0.2);
        }
        .overview-item.primary small, .overview-item.primary em { color: rgba(255, 255, 255, 0.85); }
        .overview-item.primary strong { color: #fff; }

        /* Card Danger */
        .overview-item.danger { background: #fef2f2; border: 1px solid #fecaca; }
        .overview-item.danger small { color: #dc2626; font-weight: 700; }
        .overview-item.danger strong { color: #991b1b; }
        .overview-item.danger em { color: #ef4444; }

        .item-arrow { position: absolute; top: 22px; right: 22px; color: #cbd5e1; transition: all 0.2s; }
        .overview-item:hover .item-arrow { transform: translateX(4px); color: #3b82f6; }
        .overview-item.primary .item-arrow { color: rgba(255,255,255,0.5); }
        .overview-item.primary:hover .item-arrow { color: #fff; }
        .overview-item.danger .item-arrow { color: #fca5a5; }

        /* Quick Menu Grid */
        .quick-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; }
        .quick-action {
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 10px;
            padding: 20px 10px; background: #fff; border-radius: 16px; color: #475569; font-size: 12px; font-weight: 600;
            text-align: center; text-decoration: none; border: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02); transition: all 0.2s ease;
        }
        .quick-action:hover { background: #f8fafc; border-color: #bae6fd; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(14, 165, 233, 0.1); color: #0f172a; }
        .quick-icon { width: 28px; height: 28px; color: #3b82f6; background: #eff6ff; padding: 10px; border-radius: 12px; transition: transform 0.2s; }
        .quick-action:hover .quick-icon { transform: scale(1.1); background: #dbeafe; color: #2563eb; }

        /* Dashboard Panels */
        .dashboard-panel { background: #fff; border-radius: 20px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03); border: 1px solid rgba(226, 232, 240, 0.6); overflow: hidden; }
        .dashboard-row { display: flex; align-items: center; gap: 16px; padding: 16px 20px; text-decoration: none; border-bottom: 1px solid #f1f5f9; transition: background 0.2s; }
        .dashboard-row:last-child { border-bottom: 0; }
        .dashboard-row:hover { background: #f8fafc; }

        .row-icon { width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
        .row-icon.warning { background: #fef2f2; color: #ef4444; }
        .row-icon.mutation-in { background: #ecfdf5; color: #10b981; }
        .row-icon.mutation-out { background: #fff1f2; color: #f43f5e; }

        .row-copy { flex: 1; min-width: 0; }
        .row-copy strong { display: block; color: #0f172a; font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-bottom: 2px; }
        .row-copy small { display: block; color: #64748b; font-size: 12px; }

        .row-meta { font-size: 12px; font-weight: 600; color: #64748b; display: flex; align-items: center; gap: 4px; }
        .meta-action { color: #3b82f6; transition: color 0.2s; }
        .dashboard-row:hover .meta-action { color: #2563eb; }

        .empty-state { padding: 32px 20px; text-align: center; color: #64748b; font-size: 14px; font-weight: 500; }
        .empty-icon { width: 48px; height: 48px; margin: 0 auto 12px; color: #cbd5e1; }

        @media (max-width: 900px) { .overview { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 700px) {
            .quick-grid { grid-template-columns: repeat(3, 1fr); }
            .barcode-action { flex-direction: column; text-align: center; padding: 24px; gap: 16px; }
            .barcode-arrow { display: none; }
        }
        @media (max-width: 560px) {
            .staff-welcome h1 { font-size: 24px; }
            .overview { gap: 12px; }
            .overview-item { padding: 16px; }
            .overview-item strong { font-size: 26px; }
            .quick-grid { gap: 8px; }
            .quick-action { padding: 16px 8px; }
        }
    </style>

    <div class="staff-dashboard">
        <!-- Welcome Section -->
        <section class="staff-welcome">
            <small>Ruang Kerja Operasional</small>
            <h1>Dashboard Staff</h1>
            <p>Manajemen Gudang & Stok Universitas Terbuka</p>
        </section>

        <!-- Main Banner -->
        <a href="{{ route('pencarian.input') }}" class="barcode-action">
            <div class="barcode-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:32px; height:32px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                </svg>
            </div>
            <div class="barcode-text">
                <h2>Scan & Input Barang</h2>
                <p>Scan barcode menggunakan scanner untuk menambah stok atau mendata barang baru ke dalam gudang.</p>
            </div>
            <div class="barcode-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:24px; height:24px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </div>
        </a>

        <!-- Ringkasan Gudang -->
        <div class="section-label">
            <h2>Ringkasan Inventaris Gudang</h2>
            <a href="{{ route('laporan.index') }}">Lihat Semua Laporan</a>
        </div>
        <div class="overview">
            <a class="overview-item primary" href="{{ route('barang.index') }}">
                <small>Total Item di Gudang</small>
                <strong>{{ number_format($totalItem, 0, ',', '.') }}</strong>
                <em>{{ $totalBuku }} buku + {{ $totalBarang }} barang</em>
                <svg class="item-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:20px; height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
            </a>
            <a class="overview-item" href="{{ route('kategori.index') }}">
                <small>Total Kategori</small>
                <strong>{{ number_format($totalKategori, 0, ',', '.') }}</strong>
                <em>Tipe barang terdaftar</em>
                <svg class="item-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:20px; height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
            </a>
            <a class="overview-item {{ $barangMenipis > 0 || $bukuMenipis > 0 ? 'danger' : '' }}" href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}">
                <small>Peringatan Stok Menipis</small>
                <strong>{{ $barangMenipis + $bukuMenipis }}</strong>
                <em>Item perlu restock segera</em>
                <svg class="item-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:20px; height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
            </a>
            <a class="overview-item" href="{{ route('rak.index', ['from' => 'dashboard']) }}">
                <small>Kelola Lokasi Rak</small>
                <strong>{{ number_format($totalRak, 0, ',', '.') }}</strong>
                <em>Peta penyimpanan gudang</em>
                <svg class="item-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:20px; height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
            </a>
        </div>

        <!-- Menu Cepat -->
        <div class="section-label"><h2>Menu Cepat</h2></div>
        <div class="quick-grid">
            <a class="quick-action" href="{{ route('pencarian.index', ['from' => 'dashboard']) }}">
                <svg class="quick-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                Cari Barang
            </a>
            <a class="quick-action" href="{{ route('mutasi-barang.create', ['from' => 'dashboard']) }}">
                <svg class="quick-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" /></svg>
                Mutasi Keluar/Masuk
            </a>
            <a class="quick-action" href="{{ route('stock-opname-barang.index', ['from' => 'dashboard']) }}">
                <svg class="quick-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
                Opname Stok
            </a>
            <a class="quick-action" href="{{ route('denah-gudang', ['from' => 'dashboard']) }}">
                <svg class="quick-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
                Denah Gudang
            </a>
            <a class="quick-action" href="{{ route('laporan.index', ['from' => 'dashboard']) }}">
                <svg class="quick-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                Cetak Laporan
            </a>
        </div>

        <!-- Peringatan Barang Menipis -->
        <div class="section-label">
            <h2>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px; height:20px; color:#ef4444;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                Stok Mendekati Habis
            </h2>
            <a href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}">Lihat detail peringatan</a>
        </div>
        <div class="dashboard-panel">
            @forelse($barangMenipis3 as $brg)
                <a class="dashboard-row" href="{{ route('barang.show', $brg) }}">
                    <div class="row-icon warning">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:24px; height:24px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <div class="row-copy">
                        <strong>{{ $brg->nama }}</strong>
                        <small>{{ $brg->kategori->nama }} · Tersisa: {{ $brg->stok }} / Minimum: {{ $brg->stok_minimum }} {{ $brg->satuan }}</small>
                    </div>
                    <div class="row-meta meta-action">
                        Proses Restock &rsaquo;
                    </div>
                </a>
            @empty
                <div class="empty-state">
                    <svg class="empty-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Kondisi aman. Semua barang di gudang memiliki stok yang mencukupi.
                </div>
            @endforelse
        </div>

        <!-- Riwayat Mutasi -->
        <div class="section-label">
            <h2>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px; height:20px; color:#3b82f6;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                Mutasi Barang Terbaru
            </h2>
            <a href="{{ route('mutasi-barang.index', ['from' => 'dashboard']) }}">Riwayat lengkap mutasi</a>
        </div>
        <div class="dashboard-panel">
            @forelse($aktivitasTerbaru as $aktivitas)
                <div class="dashboard-row">
                    <div class="row-icon {{ $aktivitas->jenis === 'masuk' ? 'mutation-in' : 'mutation-out' }}">
                        @if($aktivitas->jenis === 'masuk')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:20px; height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3" /></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:20px; height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" /></svg>
                        @endif
                    </div>
                    <div class="row-copy">
                        <strong>{{ $aktivitas->barang->nama }}</strong>
                        <small>
                            {{ $aktivitas->jenis === 'masuk' ? 'Barang Masuk' : 'Barang Keluar' }}
                            &bull; {{ $aktivitas->jumlah }} {{ $aktivitas->barang->satuan }}
                            &bull; Oleh {{ $aktivitas->staff?->name ?? 'Staff' }}
                        </small>
                    </div>
                    <div class="row-meta">
                        {{ \Carbon\Carbon::parse($aktivitas->created_at)->diffForHumans() }}
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <svg class="empty-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Belum ada aktivitas mutasi barang yang tercatat.
                </div>
            @endforelse
        </div>
    </div>
@endsection
