@extends('layouts.app')
@section('content')
<div class="andon-dashboard">
    <section class="andon-hero andon-hero--single">
        <a class="andon-scan andon-scan--light" href="{{ route('laporan.index') }}">
            <span class="andon-scan__label">LAPORAN</span>
            <div>
                <h2>Lihat laporan</h2>
                <p>Ringkasan inventaris & aktivitas — siap untuk keputusan.</p>
            </div>
            @php
                $bl = ['Buku' => $totalBuku ?? 0, 'Barang' => $totalBarang ?? 0, 'Kategori' => $totalKategori ?? 0, 'Rak' => $totalRak ?? 0];
                $mx = max(1, max($bl));
            @endphp
            <div class="andon-bar-chart andon-bar-chart--vertical" aria-label="Ringkasan: Buku, Barang, Kategori, Rak">
                <svg viewBox="0 0 320 96" width="100%" height="96" role="img" aria-hidden="true" style="display:block;" preserveAspectRatio="xMidYMid meet">
                    @foreach($bl as $lbl => $val)
                        @php $i = $loop->index; $x = 14 + $i * 78; $h = $mx > 0 ? max(4, ($val / $mx) * 58) : 4; $y = 72 - $h; @endphp
                        <rect x="{{ $x }}" y="{{ $y }}" width="44" height="{{ $h }}" rx="6" fill="{{ $lbl === 'Buku' ? '#0F172A' : ($lbl === 'Barang' ? '#334155' : ($lbl === 'Kategori' ? '#4F46E5' : '#0D9488')) }}" />
                        <text x="{{ $x + 22 }}" y="{{ $y - 6 }}" text-anchor="middle" font-family="'JetBrains Mono',ui-monospace,monospace" font-size="10" font-weight="600" fill="#0F172A">{{ number_format($val, 0, ',', '.') }}</text>
                        <text x="{{ $x + 22 }}" y="88" text-anchor="middle" font-family="'Instrument Sans',ui-sans-serif,system-ui,sans-serif" font-size="8" font-weight="700" letter-spacing="0.06em" fill="#94A3B8">{{ strtoupper($lbl) }}</text>
                    @endforeach
                </svg>
            </div>
            <span class="andon-scan__cta">Buka laporan ›</span>
        </a>
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
