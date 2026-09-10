@extends('layouts.app')
@section('content')
<div class="andon-dashboard">
    <section class="andon-hero">
        <div class="andon-hero__copy">
            <span class="andon-kicker"><i></i> PUSAT KENDALI SISTEM</span>
            <h1>Dashboard Admin</h1>
            <p>Kelola master data, pengguna, dan aktivitas gudang dari satu panel. Hazard amber menandai yang butuh perhatian.</p>
            <div class="andon-hero__actions">
                <a class="btn btn--amber" href="{{ route('pencarian.input') }}">Scan & Input Barang</a>
                <a class="btn btn--ghost" href="{{ route('users') }}">Kelola Pengguna</a>
            </div>
        </div>
        <a class="andon-scan" href="{{ route('pencarian.input') }}">
            <span class="andon-scan__label">SCAN BARCODE</span>
            <div>
                <h2>Input cepat via pindai</h2>
                <p>Barcode → lookup barang → langsung assign ke rak & denah area.</p>
            </div>
            <span class="andon-scan__cta">Buka scanner ›</span>
        </a>
    </section>

    <div class="andon-tiles">
        <a class="andon-tile andon-tile--navy" href="{{ route('barang.index') }}">
            <span class="andon-tile__label">TOTAL ITEM <span class="andon-tile__arrow">↗</span></span>
            <span class="andon-tile__value">{{ number_format($totalItem, 0, ',', '.') }}</span>
            <span class="andon-tile__note">{{ $totalBuku }} buku + {{ $totalBarang }} barang</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('kategori.index') }}">
            <span class="andon-tile__label">KATEGORI <span class="andon-tile__arrow">↗</span></span>
            <span class="andon-tile__value">{{ number_format($totalKategori, 0, ',', '.') }}</span>
            <span class="andon-tile__note">Jenis barang terdaftar</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('users') }}">
            <span class="andon-tile__label">PENGGUNA <span class="andon-tile__arrow">↗</span></span>
            <span class="andon-tile__value">{{ number_format($totalUser, 0, ',', '.') }}</span>
            <span class="andon-tile__note">{{ $totalStaff }} staff · {{ $totalPimpinan }} pimpinan</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('rak.index', ['from' => 'dashboard']) }}">
            <span class="andon-tile__label">RAK GUDANG <span class="andon-tile__arrow">↗</span></span>
            <span class="andon-tile__value">{{ number_format($totalRak, 0, ',', '.') }}</span>
            <span class="andon-tile__note">Lokasi penyimpanan</span>
        </a>
    </div>

    <div class="andon-section"><h2>MENU CEPAT</h2></div>
    <div class="andon-quick">
        <a class="andon-q" href="{{ route('mutasi-barang.create', ['from' => 'dashboard']) }}"><span class="andon-q__icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 16V8l-4 4 4 4"/><path d="M17 8v8l4-4-4-4"/><path d="M3 12h18"/></svg></span>Mutasi</a>
        <a class="andon-q" href="{{ route('stock-opname-barang.index', ['from' => 'dashboard']) }}"><span class="andon-q__icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12h6"/><path d="M14 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V7z"/><path d="M14 2v5h5"/></svg></span>Opname</a>
        <a class="andon-q" href="{{ route('denah-gudang', ['from' => 'dashboard']) }}"><span class="andon-q__icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 3v18M3 9h18M3 15h18"/></svg></span>Denah Gudang</a>
        <a class="andon-q" href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}"><span class="andon-q__icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.3 3.3L2.2 16a1 1 0 00.9 1.5h16a1 1 0 00.9-1.5L11.7 3.3a1 1 0 00-1.7 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg></span>Stok Menipis</a>
        <a class="andon-q" href="{{ route('laporan.index', ['from' => 'dashboard']) }}"><span class="andon-q__icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H7a2 2 0 00-2 2v16a2 2 0 002 2h10a2 2 0 002-2V8z"/><path d="M14 2v5h5"/><path d="M10 13H8"/><path d="M16 17H8"/></svg></span>Laporan</a>
        <a class="andon-q" href="{{ route('mutasi-barang.index', ['from' => 'dashboard']) }}"><span class="andon-q__icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg></span>Riwayat Mutasi</a>
    </div>

    <div class="andon-grid">
        <section class="andon-panel">
            <div class="andon-panel__head"><h2>RINGKASAN INVENTARIS</h2></div>
            <div class="andon-data">
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-ink)"></i> Total item gudang</span><strong>{{ number_format($totalItem, 0, ',', '.') }} item</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-green)"></i> Kategori</span><strong>{{ number_format($totalKategori, 0, ',', '.') }} kategori</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-red)"></i> Stok menipis</span><strong style="color: {{ $barangMenipis > 0 ? 'var(--andon-red)' : 'var(--andon-ink)' }}">{{ number_format($barangMenipis, 0, ',', '.') }} item perlu restock</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:#8B5CF6"></i> Pengguna</span><strong>{{ $totalAdmin }} admin · {{ $totalStaff }} staff · {{ $totalPimpinan }} pimpinan</strong></div>
            </div>
        </section>
        <section class="andon-panel">
            <div class="andon-panel__head"><h2>MUTASI TERBARU</h2><a href="{{ route('mutasi-barang.index') }}">Semua ›</a></div>
            @forelse($aktivitasTerbaru as $aktivitas)
                <div class="andon-row">
                    <span class="andon-row__icon {{ $aktivitas->jenis === 'masuk' ? 'andon-row__icon--in' : 'andon-row__icon--out' }}">
                        @if($aktivitas->jenis === 'masuk')
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
                        @else
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14"/><path d="M19 12l-7 7-7-7"/></svg>
                        @endif
                    </span>
                    <span class="andon-row__copy"><strong>{{ $aktivitas->barang->nama }}</strong><small>{{ $aktivitas->jenis === 'masuk' ? 'Masuk' : 'Keluar' }} · {{ $aktivitas->staff?->name ?? 'Staff' }}</small></span>
                    <span class="andon-row__meta">{{ \Carbon\Carbon::parse($aktivitas->created_at)->diffForHumans() }}</span>
                </div>
            @empty
                <div class="andon-empty">Belum ada aktivitas mutasi.</div>
            @endforelse
        </section>
    </div>
</div>
@endsection
