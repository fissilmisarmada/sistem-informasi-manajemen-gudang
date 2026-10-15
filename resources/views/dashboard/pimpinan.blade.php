@extends('layouts.app')
@section('content')
<div class="andon-dashboard">
    <section class="andon-role-panel andon-role-panel--pimpinan">
        <div class="andon-role-panel__copy">
            <h1>Kondisi gudang</h1>
            <p>Ringkasan eksekutif untuk melihat kesehatan stok, pemanfaatan rak, dan aktivitas operasional terbaru.</p>
        </div>
        <div class="andon-role-panel__actions" aria-label="Aksi pimpinan">
            <a href="{{ route('laporan.index') }}"><span class="andon-role-action__head"><em>01</em><i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h5"/></svg></i></span><strong>Laporan</strong><span>Buka ringkasan siap putuskan.</span></a>
            <a href="{{ route('mutasi-barang.index') }}"><span class="andon-role-action__head"><em>02</em><i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 16 3 12l4-4"/><path d="M3 12h10a4 4 0 0 1 4 4v4"/><path d="M17 8 21 12l-4 4"/><path d="M21 12H11a4 4 0 0 0-4 4v4"/></svg></i></span><strong>Mutasi</strong><span>Pantau pergerakan barang.</span></a>
            <a href="{{ route('denah-gudang') }}"><span class="andon-role-action__head"><em>03</em><i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/><circle cx="9" cy="14" r="1.5" fill="currentColor" stroke="none"/></svg></i></span><strong>Denah Gudang</strong><span>Lihat konteks lokasi fisik.</span></a>
            <a href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}"><span class="andon-role-action__head"><em>04</em><i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.3 2.2 16a1 1 0 0 0 .9 1.5h16a1 1 0 0 0 .9-1.5L11.7 3.3a1 1 0 0 0-1.7 0Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg></i></span><strong>Risiko Stok</strong><span>Cek item perlu perhatian.</span></a>
        </div>
    </section>

    <div class="andon-tiles">
        <a class="andon-tile andon-tile--navy" href="{{ route('barang.index') }}">
            <span class="andon-tile__label">TOTAL ITEM</span>
            <span class="andon-tile__value">{{ number_format($totalItem, 0, ',', '.') }}</span>
            <span class="andon-tile__note">{{ $totalBuku }} buku + {{ $totalBarang }} barang lain</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('kategori.index') }}">
            <span class="andon-tile__label">KATEGORI</span>
            <span class="andon-tile__value">{{ number_format($totalKategori, 0, ',', '.') }}</span>
            <span class="andon-tile__note">Jenis barang terdaftar</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('denah-gudang') }}">
            <span class="andon-tile__label">RAK GUDANG</span>
            <span class="andon-tile__value">{{ number_format($totalRak, 0, ',', '.') }}</span>
            <span class="andon-tile__note">Lokasi penyimpanan</span>
        </a>
        <a class="andon-tile {{ $barangMenipis > 0 ? 'andon-tile--red' : 'andon-tile--line' }}" href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}">
            <span class="andon-tile__label">STOK MENIPIS</span>
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
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--saas-indigo)"></i> Rak gudang</span><strong>{{ number_format($totalRak, 0, ',', '.') }} rak</strong></div>
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
