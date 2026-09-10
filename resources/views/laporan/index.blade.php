@extends('layouts.app')

@section('content')
<style>
    .report-page { max-width:1120px; margin:0 auto; }
    .report-head { display:flex; align-items:flex-end; justify-content:space-between; gap:18px; margin-bottom:18px; flex-wrap:wrap; }
    .andon-kicker { display:inline-flex; align-items:center; gap:8px; font-size:10px; font-weight:800; letter-spacing:.14em; color:var(--andon-faint); }
    .andon-kicker i { width:18px; height:2px; background:var(--andon-amber); display:inline-block; border-radius:999px; }
    .report-head h1 { margin:8px 0 0; color:var(--andon-ink); font-size:28px; font-weight:800; letter-spacing:-.04em; line-height:1; }
    .report-head p { margin:6px 0 0; color:var(--andon-muted); font-size:12px; line-height:1.5; }
    .alert { padding:12px 14px; border-radius:12px; margin-bottom:16px; font-size:13px; font-weight:600; display:flex; align-items:center; gap:10px; }
    .alert-success { background:#ECFDF5; color:#065F46; border:1px solid #A7F3D0; }
    .alert-error { background:#FEF2F2; color:#991B1B; border:1px solid #FECACA; }
    .metrics { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:18px; }
    .metric { position:relative; overflow:hidden; background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; padding:16px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); }
    .metric::before { content:''; position:absolute; left:0; right:0; top:0; height:3px; background:var(--andon-line-strong); }
    .metric.blue::before { background:var(--andon-navy); }
    .metric.green::before { background:var(--andon-green); }
    .metric.red::before { background:var(--andon-red); }
    .metric.amber::before { background:var(--andon-amber); }
    .metric-label { font-size:10px; font-weight:800; letter-spacing:.10em; text-transform:uppercase; color:var(--andon-faint); }
    .metric-value { margin-top:10px; font-size:28px; font-weight:800; letter-spacing:-.04em; line-height:1; color:var(--andon-ink); font-variant-numeric:tabular-nums; }
    .metric-note { margin-top:6px; font-size:11px; font-weight:600; color:var(--andon-muted); }
    .report-grid { display:grid; grid-template-columns:1.35fr .65fr; gap:16px; margin-bottom:18px; }
    .panel { overflow:hidden; background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); }
    .panel-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:14px 16px; border-bottom:1px solid var(--andon-line); background:#FCFCF9; }
    .panel-heading h2 { margin:0; color:var(--andon-ink); font-size:12px; font-weight:800; letter-spacing:.08em; }
    .panel-heading a { color:var(--andon-navy); font-size:11px; font-weight:800; letter-spacing:.04em; }
    .panel-heading a:hover { text-decoration:underline; }
    .activity { display:flex; align-items:center; gap:11px; padding:12px 16px; border-bottom:1px solid #F1F5F9; }
    .activity:last-child { border-bottom:0; }
    .activity-icon { width:36px; height:36px; display:grid; place-items:center; flex:none; border-radius:10px; font-size:14px; font-weight:900; border:1px solid var(--andon-line); }
    .activity-icon.in { background:#ECFDF5; color:#065F46; border-color:#A7F3D0; }
    .activity-icon.out { background:#FEF2F2; color:#991B1B; border-color:#FECACA; }
    .activity-copy { min-width:0; flex:1; }
    .activity-copy strong { display:block; overflow:hidden; color:var(--andon-ink); font-size:13px; font-weight:700; letter-spacing:-.02em; text-overflow:ellipsis; white-space:nowrap; }
    .activity-copy small { color:var(--andon-muted); font-size:11px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; display:block; margin-top:2px; }
    .activity-time { color:var(--andon-faint); font-size:11px; font-weight:700; white-space:nowrap; }
    .quick-stats { padding:6px 16px 10px; }
    .quick-stat { display:flex; justify-content:space-between; gap:12px; padding:12px 0; border-bottom:1px solid #F1F5F9; color:var(--andon-muted); font-size:12px; }
    .quick-stat:last-child { border-bottom:0; }
    .quick-stat strong { color:var(--andon-ink); font-weight:700; }
    .table-wrap { overflow-x:auto; background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); }
    table { width:100%; min-width:760px; border-collapse:collapse; }
    th { padding:12px 16px; background:#F8FAFC; color:var(--andon-muted); font-size:11px; font-weight:800; letter-spacing:.06em; text-align:left; border-bottom:1px solid var(--andon-line); }
    td { padding:13px 16px; border-top:1px solid #F1F5F9; color:#334155; font-size:12px; }
    tbody tr:hover td { background:#F8FAFC; }
    .item-name { color:var(--andon-navy); font-weight:800; }
    .item-name:hover { text-decoration:underline; }
    .stock-low { color:var(--andon-red); font-weight:800; }
    .stock-ok { color:var(--andon-green); font-weight:800; }
    .status { display:inline-flex; align-items:center; padding:4px 10px; border-radius:999px; font-size:10px; font-weight:800; letter-spacing:.04em; border:1px solid transparent; }
    .status.low { background:#FEF2F2; color:#991B1B; border-color:#FECACA; }
    .status.ok { background:#ECFDF5; color:#065F46; border-color:#A7F3D0; }
    .import-panel { display:flex; align-items:end; gap:12px; flex-wrap:wrap; margin-bottom:18px; padding:14px 16px; background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); }
    .import-panel label { display:block; margin-bottom:6px; color:var(--andon-faint); font-size:10px; font-weight:800; letter-spacing:.10em; }
    .andon-control { padding:12px 14px; border:1px solid #E8EAF0; border-radius:14px; font-size:13px; color:var(--andon-ink); background:#FBFBFD; outline:none; transition:border-color .18s,box-shadow .18s,background .18s; }
    .andon-control:focus { border-color:var(--andon-ink); box-shadow:0 0 0 3px rgba(15,23,42,.06); background:#fff; }
    .import-panel input[type="file"] { max-width:280px; font-size:12px; }
    .hint { color:var(--andon-muted); font-size:11px; font-weight:600; }
    .empty { padding:28px 18px; color:var(--andon-faint); font-size:12px; font-weight:600; text-align:center; }
    @media (max-width:850px) { .metrics { grid-template-columns:repeat(2,1fr); } .report-grid { grid-template-columns:1fr; } }
    @media (max-width:560px) { .report-head { display:block; } .report-head h1 { font-size:24px; } .metrics { gap:10px; } .metric { padding:14px 12px; } .metric-value { font-size:24px; } }
</style>

<div class="report-page">
    <div style="margin-bottom:14px;">
        <a class="btn btn--ghost" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" style="min-height:36px;padding:0 14px;font-size:12px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
            Kembali
        </a>
    </div>
    <div class="report-head">
        <div>
            <span class="andon-kicker"><i></i> MONITORING INVENTARIS GUDANG</span>
            <h1>Laporan Gudang</h1>
            <p>Ringkasan stok, penempatan, dan aktivitas operasional gudang.</p>
        </div>
        <div style="display:flex;gap:9px;flex-wrap:wrap;">
            @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
                <a class="btn btn--primary" href="{{ route('laporan.export') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0l4-4m-4 4l-4-4"/><path stroke-linecap="round" stroke-linejoin="round" d="M3 15v4a1 1 0 001 1h16a1 1 0 001-1v-4"/></svg>
                    Export CSV
                </a>
            @endif
        </div>
    </div>

    @if(session('sukses'))
        <div class="alert alert-success">{{ session('sukses') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="metrics">
        <div class="metric blue"><div class="metric-label">Total Item</div><div class="metric-value">{{ number_format($ringkasan['total_item'], 0, ',', '.') }}</div><div class="metric-note">Barang terdaftar</div></div>
        <div class="metric green"><div class="metric-label">Total Stok</div><div class="metric-value">{{ number_format($ringkasan['total_stok'], 0, ',', '.') }}</div><div class="metric-note">Jumlah seluruh satuan</div></div>
        <div class="metric red"><div class="metric-label">Stok Menipis</div><div class="metric-value">{{ number_format($ringkasan['stok_menipis'], 0, ',', '.') }}</div><div class="metric-note">Perlu perhatian</div></div>
        <div class="metric amber"><div class="metric-label">Ditempatkan</div><div class="metric-value">{{ number_format($ringkasan['ditempatkan'], 0, ',', '.') }}</div><div class="metric-note">Memiliki lokasi rak</div></div>
    </div>

    <div class="report-grid">
        <section class="panel">
            <div class="panel-heading"><h2>MUTASI BARANG TERBARU</h2><a href="{{ route('mutasi-barang.index', ['from' => 'laporan']) }}">Lihat semua ›</a></div>
            @forelse($mutasiTerbaru as $mutasi)
                <div class="activity"><span class="activity-icon {{ $mutasi->jenis === 'masuk' ? 'in' : 'out' }}">{{ $mutasi->jenis === 'masuk' ? '↑' : '↓' }}</span><span class="activity-copy"><strong>{{ $mutasi->barang?->nama ?? 'Barang dihapus' }}</strong><small>{{ $mutasi->jenis === 'masuk' ? 'Barang masuk' : 'Barang keluar' }} · {{ $mutasi->jumlah }} {{ $mutasi->barang?->satuan ?? '' }} · {{ $mutasi->staff?->name ?? 'Staff' }}</small></span><span class="activity-time">{{ $mutasi->tanggal?->diffForHumans() }}</span></div>
            @empty
                <div class="empty">Belum ada aktivitas mutasi barang.</div>
            @endforelse
        </section>
        <section class="panel">
            <div class="panel-heading"><h2>RINGKASAN GUDANG</h2><a href="{{ route('barang.stok-menipis', ['from' => 'laporan']) }}">Stok menipis ›</a></div>
            <div class="quick-stats">
                <div class="quick-stat"><span>Kategori</span><strong>{{ number_format($ringkasan['kategori'], 0, ',', '.') }}</strong></div>
                <div class="quick-stat"><span>Rak</span><strong>{{ number_format($ringkasan['rak'], 0, ',', '.') }}</strong></div>
                <div class="quick-stat"><span>Riwayat mutasi</span><strong>{{ number_format($ringkasan['mutasi'], 0, ',', '.') }}</strong></div>
                <div class="quick-stat"><span>Stock opname</span><strong>{{ number_format($ringkasan['opname'], 0, ',', '.') }}</strong></div>
            </div>
        </section>
    </div>

    @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
        <form class="import-panel" method="POST" action="{{ route('laporan.import') }}" enctype="multipart/form-data">
            @csrf
            <div><label for="file">IMPORT DATA BARANG CSV</label><input type="file" id="file" name="file" accept=".csv,.txt" required class="andon-control"></div>
            <button class="btn btn--amber" type="submit">Import CSV</button>
            <span class="hint">Gunakan format dari file export laporan.</span>
        </form>
    @endif

    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Rak</th><th>Stok</th><th>Minimum</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($barangs as $barang)
                    <tr><td>{{ $barang->kode_barang }}</td><td><a class="item-name" href="{{ route('barang.show', $barang) }}">{{ $barang->nama }}</a></td><td>{{ $barang->kategori?->nama ?? '-' }}</td><td>{{ $barang->rak?->kode_rak ?? 'Belum ditempatkan' }}</td><td class="{{ $barang->isStokMenipis() ? 'stock-low' : 'stock-ok' }}">{{ $barang->stok }} {{ $barang->satuan }}</td><td>{{ $barang->stok_minimum }} {{ $barang->satuan }}</td><td><span class="status {{ $barang->isStokMenipis() ? 'low' : 'ok' }}">{{ $barang->isStokMenipis() ? 'Stok menipis' : 'Aman' }}</span></td></tr>
                @empty
                    <tr><td class="empty" colspan="7">Belum ada data barang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
