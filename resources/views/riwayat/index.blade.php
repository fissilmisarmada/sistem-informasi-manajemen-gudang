@extends('layouts.app')

@section('content')
<style>
    .history-page { max-width:1120px; margin:0 auto; padding:10px 0 36px; }
    .history-heading { margin-bottom:22px; }
    .eyebrow { color:#64748b; font-size:11px; font-weight:800; letter-spacing:1.1px; text-transform:uppercase; }
    h1 { margin:7px 0 0; color:#123b82; font-size:31px; }
    .subtitle { margin:6px 0 0; color:#64748b; font-size:12px; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:10px 15px; border-radius:9px; background:#e2e8f0; color:#0f172a; font-size:13px; font-weight:800; text-decoration:none; }
    .btn:hover { background:#cbd5e1; }
    .history-grid { display:grid; grid-template-columns:1.25fr .75fr; gap:18px; }
    .panel { overflow:hidden; background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 5px 16px rgba(15,23,42,.04); }
    .panel-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:17px 18px; border-bottom:1px solid #eef2f7; }
    .panel-heading h2 { margin:0; color:#1e3a6f; font-size:15px; }
    .panel-heading span { color:#64748b; font-size:11px; }
    .activity { display:flex; align-items:center; gap:11px; padding:13px 18px; border-bottom:1px solid #f1f5f9; }
    .activity:last-child { border-bottom:0; }
    .activity-icon { width:32px; height:32px; display:grid; place-items:center; flex:none; border-radius:8px; font-size:16px; font-weight:900; }
    .activity-icon.in { background:#dcfce7; color:#15803d; }
    .activity-icon.out { background:#fee2e2; color:#dc2626; }
    .activity-icon.check { background:#dbeafe; color:#1d4ed8; }
    .activity-copy { min-width:0; flex:1; }
    .activity-copy strong { display:block; overflow:hidden; color:#1e3a6f; font-size:12px; text-overflow:ellipsis; white-space:nowrap; }
    .activity-copy small { color:#64748b; font-size:10px; }
    .activity-time { color:#64748b; font-size:10px; white-space:nowrap; }
    .empty { padding:28px 18px; color:#64748b; font-size:12px; text-align:center; }
    @media (max-width:800px) { .history-grid { grid-template-columns:1fr; } }
    @media (max-width:560px) { h1 { font-size:27px; } .activity-time { display:none; } }
</style>

<div class="history-page">
    <a class="btn" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}">&larr; Kembali</a>
    <div class="history-heading">
        <div class="eyebrow">Audit operasional gudang</div>
        <h1>Riwayat Aktivitas Gudang</h1>
        <p class="subtitle">Catatan pergerakan stok dan pemeriksaan fisik barang.</p>
    </div>

    <div class="history-grid">
        <section class="panel">
            <div class="panel-heading"><h2>Riwayat Mutasi Barang</h2><span>{{ $mutasi->count() }} aktivitas</span></div>
            @forelse($mutasi as $item)
                <div class="activity"><span class="activity-icon {{ $item->jenis === 'masuk' ? 'in' : 'out' }}">{{ $item->jenis === 'masuk' ? '↑' : '↓' }}</span><span class="activity-copy"><strong>{{ $item->barang?->nama ?? 'Barang dihapus' }}</strong><small>{{ $item->jenis === 'masuk' ? 'Barang masuk' : 'Barang keluar' }} · {{ $item->jumlah }} {{ $item->barang?->satuan ?? '' }} · {{ $item->staff?->name ?? 'Staff' }}</small></span><span class="activity-time">{{ $item->tanggal?->format('d/m/Y') }}</span></div>
            @empty
                <div class="empty">Belum ada riwayat mutasi barang.</div>
            @endforelse
        </section>
        <section class="panel">
            <div class="panel-heading"><h2>Riwayat Stock Opname</h2><span>{{ $opname->count() }} pemeriksaan</span></div>
            @forelse($opname as $item)
                <div class="activity"><span class="activity-icon check">✓</span><span class="activity-copy"><strong>{{ $item->barang?->nama ?? 'Barang dihapus' }}</strong><small>Tercatat {{ $item->jumlah_tercatat }}, fisik {{ $item->jumlah_fisik }} · {{ $item->staff?->name ?? 'Staff' }}</small></span><span class="activity-time">{{ $item->tanggal?->format('d/m/Y') }}</span></div>
            @empty
                <div class="empty">Belum ada riwayat stock opname.</div>
            @endforelse
        </section>
    </div>
</div>
@endsection
