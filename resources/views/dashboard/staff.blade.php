@extends('layouts.app')
@section('content')
<div class="andon-dashboard">
    <section class="andon-hero andon-hero--single">
        <a class="andon-scan" href="{{ route('pencarian.input') }}">
            <span class="andon-scan__label">SCAN BARCODE</span>
            <div>
                <h2>Pindai untuk mutasi</h2>
            </div>
            <span class="andon-scan__cta">Mulai scan ›</span>
        </a>
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
