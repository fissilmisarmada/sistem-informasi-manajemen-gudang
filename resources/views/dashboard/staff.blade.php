@extends('layouts.app')
@section('content')
<div class="andon-dashboard">
    <section class="andon-role-panel andon-role-panel--staff">
        <div class="andon-role-panel__copy">
            <h1>Tugas gudang hari ini</h1>
            <p>Fokus operasional: cek stok menipis, lanjutkan mutasi, dan tuntaskan opname tanpa selisih.</p>
        </div>
        <div class="andon-role-panel__actions" aria-label="Aksi staff">
            <a href="{{ route('pencarian.index') }}"><span class="andon-role-action__head"><em>01</em><i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="6"/><path d="m15.5 15.5 4 4"/></svg></i></span><strong>Cari Barang</strong><span>Temukan lokasi sebelum bergerak.</span></a>
            <a href="{{ route('mutasi-barang.create') }}"><span class="andon-role-action__head"><em>02</em><i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8.5 19 12l-7 3.5L5 12 12 8.5Z"/><path d="M5 12v3.5L12 19l7-3.5V12"/><path d="M12 12v7"/></svg></i></span><strong>Input Mutasi</strong><span>Catat masuk atau keluar.</span></a>
            <a href="{{ route('stock-opname-barang.index') }}"><span class="andon-role-action__head"><em>03</em><i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5h6a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/><path d="M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/><path d="m9 13 2 2 4-4"/></svg></i></span><strong>Stock Opname</strong><span>Cocokkan stok fisik.</span></a>
            <a href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}"><span class="andon-role-action__head"><em>04</em><i aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.3 2.2 16a1 1 0 0 0 .9 1.5h16a1 1 0 0 0 .9-1.5L11.7 3.3a1 1 0 0 0-1.7 0Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg></i></span><strong>Stok Menipis</strong><span>Prioritaskan restock.</span></a>
        </div>
    </section>

    <div class="andon-tiles andon-tiles--staff">
        <a class="andon-tile andon-tile--navy" href="{{ route('barang.index') }}">
            <span class="andon-tile__label">TOTAL ITEM</span>
            <span class="andon-tile__value">{{ number_format($totalItem, 0, ',', '.') }}</span>
            <span class="andon-tile__note">{{ $totalBuku }} buku + {{ $totalBarang }} barang lain</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('kategori.index') }}">
            <span class="andon-tile__label">KATEGORI</span>
            <span class="andon-tile__value">{{ number_format($totalKategori, 0, ',', '.') }}</span>
            <span class="andon-tile__note">Tipe barang terdaftar</span>
        </a>
        <a class="andon-tile {{ ($totalStokMenipis ?? 0) > 0 ? 'andon-tile--red' : 'andon-tile--amber' }}" href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}">
            <span class="andon-tile__label">STOK MENIPIS</span>
            <span class="andon-tile__value">{{ number_format($totalStokMenipis ?? 0, 0, ',', '.') }}</span>
            <span class="andon-tile__note">{{ ($totalStokMenipis ?? 0) > 0 ? 'Butuh restock segera' : 'Kondisi stok aman' }}</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('rak.index', ['from' => 'dashboard']) }}">
            <span class="andon-tile__label">RAK GUDANG</span>
            <span class="andon-tile__value">{{ number_format($totalRak, 0, ',', '.') }}</span>
            <span class="andon-tile__note">Lokasi penyimpanan</span>
        </a>
        <a class="andon-tile andon-tile--line" href="{{ route('mutasi-barang.index') }}">
            <span class="andon-tile__label">MUTASI</span>
            <span class="andon-tile__value">{{ number_format($totalMutasi ?? 0, 0, ',', '.') }}</span>
            <span class="andon-tile__note">Masuk & keluar tercatat</span>
        </a>
    </div>

    <div class="andon-section"><h2>STOK MENDEKATI HABIS</h2><a href="{{ route('barang.stok-menipis', ['from' => 'dashboard']) }}">Lihat peringatan ›</a></div>
    <div class="andon-panel">
        @forelse($barangMenipis3 as $brg)
            <a class="andon-row" href="{{ route('barang.show', $brg) }}">
                <span class="andon-row__icon andon-row__icon--warn"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.3 3.3L2.2 16a1 1 0 00.9 1.5h16a1 1 0 00.9-1.5L11.7 3.3a1 1 0 00-1.7 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg></span>
                <span class="andon-row__copy"><strong>{{ $brg->nama }}</strong><small>{{ $brg->kategori->nama }} · Tersisa: <b style="color:var(--andon-red)">{{ $brg->stok }}</b> / Min {{ $brg->stok_minimum }} {{ $brg->satuan }}</small></span>
                <span class="andon-row__action">Restock ›</span>
            </a>
        @empty
            <div class="andon-empty">Semua stok masih di atas minimum.</div>
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
