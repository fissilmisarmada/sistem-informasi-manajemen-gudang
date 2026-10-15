@extends('layouts.app')
@section('content')
<div class="andon-dashboard">
    <section class="andon-role-panel andon-role-panel--admin">
        <div class="andon-role-panel__copy">
            <h1>Kendali data gudang</h1>
            <p>Prioritas admin: rapikan master data, pantau akses pengguna, dan pastikan setiap barang punya lokasi fisik.</p>
        </div>
        <div class="andon-role-panel__actions" aria-label="Aksi admin">
            <a href="{{ route('barang.index') }}"><span class="andon-role-action__head"><em>01</em><i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8.5 19 12l-7 3.5L5 12 12 8.5Z"/><path d="M5 12v3.5L12 19l7-3.5V12"/><path d="M12 12v7"/></svg></i></span><strong>Kelola Barang</strong><span>Rapikan data barang dan lokasi.</span></a>
            <a href="{{ route('rak.index', ['from' => 'dashboard']) }}"><span class="andon-role-action__head"><em>02</em><i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></i></span><strong>Rak &amp; Denah</strong><span>Susun area penyimpanan.</span></a>
            <a href="{{ route('users') }}"><span class="andon-role-action__head"><em>03</em><i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></i></span><strong>User Gudang</strong><span>Atur akses tim operasional.</span></a>
            <a href="{{ route('kategori.index') }}"><span class="andon-role-action__head"><em>04</em><i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 12 4.99 3.99 13l5.3 5.3a2 2 0 0 0 2.82 0l5.3-5.3a2 2 0 0 0 0-2.82Z"/><path d="M12 17v4"/><path d="M8 14h8"/></svg></i></span><strong>Kategori</strong><span>Jaga klasifikasi tetap rapi.</span></a>
        </div>
    </section>

    <div class="andon-tiles">
        <a class="andon-tile andon-tile--navy" href="{{ route('barang.index') }}">
            <span class="andon-tile__label">TOTAL ITEM</span>
            <span class="andon-tile__value">{{ number_format($totalItem, 0, ',', '.') }}</span>
            <span class="andon-tile__note">{{ $totalBuku }} buku + {{ $totalBarang }} barang</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('kategori.index') }}">
            <span class="andon-tile__label">KATEGORI</span>
            <span class="andon-tile__value">{{ number_format($totalKategori, 0, ',', '.') }}</span>
            <span class="andon-tile__note">Jenis barang terdaftar</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('users') }}">
            <span class="andon-tile__label">PENGGUNA</span>
            <span class="andon-tile__value">{{ number_format($totalUser, 0, ',', '.') }}</span>
            <span class="andon-tile__note">{{ $totalStaff }} staff · {{ $totalPimpinan }} pimpinan</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('rak.index', ['from' => 'dashboard']) }}">
            <span class="andon-tile__label">RAK GUDANG</span>
            <span class="andon-tile__value">{{ number_format($totalRak, 0, ',', '.') }}</span>
            <span class="andon-tile__note">Lokasi penyimpanan</span>
        </a>
    </div>

    <div class="andon-grid">
        <section class="andon-panel">
            <div class="andon-panel__head"><h2>RINGKASAN INVENTARIS</h2></div>
            <div class="andon-data">
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-ink)"></i> Total item gudang</span><strong>{{ number_format($totalItem, 0, ',', '.') }} item</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-green)"></i> Kategori</span><strong>{{ number_format($totalKategori, 0, ',', '.') }} kategori</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-red)"></i> Stok menipis</span><strong style="color: {{ $barangMenipis > 0 ? 'var(--andon-red)' : 'var(--andon-ink)' }}">{{ number_format($barangMenipis, 0, ',', '.') }} item perlu restock</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--saas-indigo)"></i> Pengguna</span><strong>{{ $totalAdmin }} admin · {{ $totalStaff }} staff · {{ $totalPimpinan }} pimpinan</strong></div>
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
