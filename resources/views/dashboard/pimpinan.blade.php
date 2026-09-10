@extends('layouts.app')
@section('content')
<div class="andon-dashboard">
    <section class="andon-hero">
        <div class="andon-hero__copy">
            <span class="andon-kicker"><i></i> RINGKASAN PENGELOLAAN</span>
            <h1>Dashboard Pimpinan</h1>
            <p>Pantau kondisi inventaris dan aktivitas gudang. Butuh detail? Masuk ke laporan atau cari barang langsung.</p>
            <div class="andon-hero__actions">
                <a class="btn btn--primary" href="{{ route('laporan.index') }}">Lihat Laporan</a>
                <a class="btn btn--ghost" href="{{ route('pencarian.index') }}">Cari Barang</a>
            </div>
        </div>
        <a class="andon-scan" href="{{ route('pencarian.index') }}" style="background: var(--andon-panel); color: var(--andon-ink); border: 1px solid var(--andon-line);">
            <span class="andon-scan__label" style="color: var(--andon-ink);">AKSES CEPAT</span>
            <div>
                <h2 style="color: var(--andon-ink);">Cari & audit stok</h2>
                <p style="color: var(--andon-muted);">Telusuri barang, cek rak, dan tinjau mutasi terbaru tanpa mengubah data.</p>
            </div>
            <span class="andon-scan__cta" style="color: var(--andon-navy);">Cari barang ›</span>
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
            <span class="andon-tile__note">Jenis barang terdaftar</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('denah-gudang') }}">
            <span class="andon-tile__label">RAK GUDANG <span class="andon-tile__arrow">↗</span></span>
            <span class="andon-tile__value">{{ number_format($totalRak, 0, ',', '.') }}</span>
            <span class="andon-tile__note">Lokasi penyimpanan</span>
        </a>
        <a class="andon-tile {{ $barangMenipis > 0 ? 'andon-tile--red' : 'andon-tile--line' }}" href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}">
            <span class="andon-tile__label">STOK MENIPIS <span class="andon-tile__arrow">↗</span></span>
            <span class="andon-tile__value">{{ number_format($barangMenipis, 0, ',', '.') }}</span>
            <span class="andon-tile__note">{{ $barangMenipis > 0 ? 'Perlu perhatian segera' : 'Stok aman' }}</span>
        </a>
    </div>

    <div class="andon-grid">
        <section class="andon-panel">
            <div class="andon-panel__head"><h2>KONDISI INVENTARIS</h2><a href="{{ route('laporan.index') }}">Laporan ›</a></div>
            <div class="andon-data">
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-ink)"></i> Total item</span><strong>{{ number_format($totalItem, 0, ',', '.') }} item</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-green)"></i> Kategori</span><strong>{{ number_format($totalKategori, 0, ',', '.') }} kategori</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:#8B5CF6"></i> Rak gudang</span><strong>{{ number_format($totalRak, 0, ',', '.') }} rak</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-red)"></i> Stok menipis</span><strong style="color: {{ $barangMenipis > 0 ? 'var(--andon-red)' : 'var(--andon-ink)' }}">{{ number_format($barangMenipis, 0, ',', '.') }} item</strong></div>
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
