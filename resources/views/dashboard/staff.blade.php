@extends('layouts.app')
@section('content')
<div class="andon-dashboard">
    <section class="andon-hero">
        <div class="andon-hero__copy">
            <span class="andon-kicker"><i></i> RUANG KERJA OPERASIONAL</span>
            <h1>Dashboard Staff</h1>
            <p>Scan, tempatkan, dan mutasi barang tanpa hunting. Semua aksi penting satu ketuk dari sini — optimal di HP lorong rak.</p>
            <div class="andon-hero__actions">
                <a class="btn btn--amber" href="{{ route('pencarian.input') }}">Scan & Input Barang</a>
                <a class="btn btn--ghost" href="{{ route('denah-gudang') }}">Buka Denah</a>
            </div>
        </div>
        <a class="andon-scan" href="{{ route('pencarian.input') }}">
            <span class="andon-scan__label">SCAN BARCODE</span>
            <div>
                <h2>Pindai untuk mutasi</h2>
                <p>QR / barcode → langsung ke proses mutasi masuk & keluar. Manual tetap bisa sebagai fallback.</p>
            </div>
            <span class="andon-scan__cta">Mulai scan ›</span>
        </a>
    </section>

    <div class="andon-tiles">
        <a class="andon-tile andon-tile--navy" href="{{ route('barang.index') }}">
            <span class="andon-tile__label">TOTAL ITEM <span class="andon-tile__arrow">↗</span></span>
            <span class="andon-tile__value">{{ number_format($totalItem, 0, ',', '.') }}</span>
            <span class="andon-tile__note">{{ $totalBuku }} buku + {{ $totalBarang }} barang lain</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('kategori.index') }}">
            <span class="andon-tile__label">KATEGORI <span class="andon-tile__arrow">↗</span></span>
            <span class="andon-tile__value">{{ number_format($totalKategori, 0, ',', '.') }}</span>
            <span class="andon-tile__note">Tipe barang terdaftar</span>
        </a>
        <a class="andon-tile {{ ($totalStokMenipis ?? 0) > 0 ? 'andon-tile--red' : 'andon-tile--amber' }}" href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}">
            <span class="andon-tile__label">STOK MENIPIS <span class="andon-tile__arrow">↗</span></span>
            <span class="andon-tile__value">{{ number_format($totalStokMenipis ?? 0, 0, ',', '.') }}</span>
            <span class="andon-tile__note">{{ ($totalStokMenipis ?? 0) > 0 ? 'Butuh restock segera' : 'Kondisi stok aman' }}</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('rak.index', ['from' => 'dashboard']) }}">
            <span class="andon-tile__label">RAK GUDANG <span class="andon-tile__arrow">↗</span></span>
            <span class="andon-tile__value">{{ number_format($totalRak, 0, ',', '.') }}</span>
            <span class="andon-tile__note">Lokasi penyimpanan</span>
        </a>
    </div>

    <div class="andon-section"><h2>MENU CEPAT</h2></div>
    <div class="andon-quick andon-quick--staff">
        <a class="andon-q" href="{{ route('pencarian.index', ['from' => 'dashboard']) }}">
            <span class="andon-q__icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg></span>
            Cari Barang
        </a>
        <a class="andon-q" href="{{ route('mutasi-barang.create', ['from' => 'dashboard']) }}">
            <span class="andon-q__icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 16V8l-4 4 4 4"/><path d="M17 8v8l4-4-4-4"/><path d="M3 12h18"/></svg></span>
            Mutasi Masuk/Keluar
        </a>
        <a class="andon-q" href="{{ route('stock-opname-barang.index', ['from' => 'dashboard']) }}">
            <span class="andon-q__icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12h6"/><path d="M9 15h6"/><path d="M14 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V7z"/><path d="M14 2v5h5"/></svg></span>
            Opname Stok
        </a>
        <a class="andon-q" href="{{ route('denah-gudang', ['from' => 'dashboard']) }}">
            <span class="andon-q__icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M3 9h18M3 15h18"/></svg></span>
            Denah Gudang
        </a>
        <a class="andon-q" href="{{ route('laporan.index', ['from' => 'dashboard']) }}">
            <span class="andon-q__icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V8z"/><path d="M14 2v5h5"/><path d="M10 13H8"/><path d="M16 17H8"/><path d="M13 13h3"/></svg></span>
            Cetak Laporan
        </a>
    </div>

    <div class="andon-section"><h2>STOK MENDekati HABIS</h2><a href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}">Lihat peringatan ›</a></div>
    <div class="andon-panel">
        @forelse($barangMenipis3 as $brg)
            <a class="andon-row" href="{{ route('barang.show', $brg) }}">
                <span class="andon-row__icon andon-row__icon--warn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.3 3.3L2.2 16a1 1 0 00.9 1.5h16a1 1 0 00.9-1.5L11.7 3.3a1 1 0 00-1.7 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg></span>
                <span class="andon-row__copy"><strong>{{ $brg->nama }}</strong><small>{{ $brg->kategori->nama }} · Tersisa: <b style="color:var(--andon-red)">{{ $brg->stok }}</b> / Min {{ $brg->stok_minimum }} {{ $brg->satuan }}</small></span>
                <span class="andon-row__action">Restock ›</span>
            </a>
        @empty
            <div class="andon-empty">Aman. Semua stok masih di atas minimum.</div>
        @endforelse
    </div>

    <div class="andon-section"><h2>MUTASI TERBARU</h2><a href="{{ route('mutasi-barang.index', ['from' => 'dashboard']) }}">Riwayat lengkap ›</a></div>
    <div class="andon-panel">
        @forelse($aktivitasTerbaru as $aktivitas)
            <div class="andon-row">
                <span class="andon-row__icon {{ $aktivitas->jenis === 'masuk' ? 'andon-row__icon--in' : 'andon-row__icon--out' }}">
                    @if($aktivitas->jenis === 'masuk')
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
                    @else
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14"/><path d="M19 12l-7 7-7-7"/></svg>
                    @endif
                </span>
                <span class="andon-row__copy"><strong>{{ $aktivitas->barang->nama }}</strong><small><b>{{ $aktivitas->jenis === 'masuk' ? 'Masuk' : 'Keluar' }}</b> · {{ $aktivitas->jumlah }} {{ $aktivitas->barang->satuan }} · {{ $aktivitas->staff?->name ?? 'Staff' }}</small></span>
                <span class="andon-row__meta">{{ \Carbon\Carbon::parse($aktivitas->created_at)->diffForHumans() }}</span>
            </div>
        @empty
            <div class="andon-empty">Belum ada mutasi tercatat.</div>
        @endforelse
    </div>
</div>
@endsection
