@extends('layouts.app')
@section('content')
<div class="andon-dashboard">
    <section class="andon-hero andon-hero--single">
        <a class="andon-scan" href="{{ route('pencarian.input') }}">
            <span class="andon-scan__label">SCAN BARCODE</span>
            <div>
                <h2>Input cepat via pindai</h2>
            </div>
            <span class="andon-scan__cta">Buka scanner ›</span>
        </a>
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
