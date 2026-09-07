@extends('layouts.app')

@section('content')
<style>
    .report-page { max-width:1120px; margin:0 auto; padding:10px 0 36px; }
    .report-heading { display:flex; align-items:flex-end; justify-content:space-between; gap:18px; margin-bottom:22px; }
    .eyebrow { color:#64748b; font-size:12px; font-weight:800; letter-spacing:1.2px; text-transform:uppercase; }
    h1 { margin:7px 0 0; color:#0f172a; font-size:32px; }
    .heading-copy p { margin:6px 0 0; color:#64748b; font-size:12px; }
    .heading-actions { display:flex; gap:9px; flex-wrap:wrap; justify-content:flex-end; }
    .btn { display:inline-flex; align-items:center; gap:7px; padding:10px 15px; border:0; border-radius:9px; font-size:13px; font-weight:800; text-decoration:none; cursor:pointer; }
    .btn-dark { background:#0f172a; color:#fff; }
    .btn-blue { background:#2563eb; color:#fff; }
    .btn-light { background:#e2e8f0; color:#0f172a; }
    .btn:hover { filter:brightness(.96); }
    .back-button { margin-bottom:18px; }
    .metrics { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:22px; }
    .metric { min-height:112px; padding:18px; border-radius:12px; color:#fff; box-shadow:0 8px 18px rgba(15,23,42,.12); }
    .metric.blue { background:linear-gradient(135deg,#1C396A 0%,#1651A4 52%,#357A38 86%,#F7D60A 125%); }
    .metric.red { background:#991b1b; }
    .metric.green { background:#047857; }
    .metric.amber { background:#b45309; }
    .metric-label { font-size:11px; font-weight:700; opacity:.82; }
    .metric-value { margin-top:12px; font-size:29px; font-weight:900; line-height:1; }
    .metric-note { margin-top:8px; font-size:11px; opacity:.82; }
    .report-grid { display:grid; grid-template-columns:1.35fr .65fr; gap:18px; margin-bottom:22px; }
    .panel { overflow:hidden; background:#fff; border:1px solid #e2e8f0; border-radius:12px; }
    .panel-heading { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:17px 18px; border-bottom:1px solid #eef2f7; }
    .panel-heading h2 { margin:0; color:#1e3a6f; font-size:15px; }
    .panel-heading a { color:#2563eb; font-size:11px; font-weight:800; }
    .activity { display:flex; align-items:center; gap:11px; padding:13px 18px; border-bottom:1px solid #f1f5f9; }
    .activity:last-child { border-bottom:0; }
    .activity-icon { width:31px; height:31px; display:grid; place-items:center; flex:none; border-radius:7px; font-size:16px; font-weight:900; }
    .activity-icon.in { background:#dcfce7; color:#15803d; }
    .activity-icon.out { background:#fee2e2; color:#dc2626; }
    .activity-copy { min-width:0; flex:1; }
    .activity-copy strong { display:block; overflow:hidden; color:#1e3a6f; font-size:12px; text-overflow:ellipsis; white-space:nowrap; }
    .activity-copy small { color:#64748b; font-size:10px; }
    .activity-time { color:#64748b; font-size:10px; white-space:nowrap; }
    .quick-stats { padding:5px 18px 12px; }
    .quick-stat { display:flex; justify-content:space-between; gap:12px; padding:12px 0; border-bottom:1px solid #f1f5f9; color:#64748b; font-size:12px; }
    .quick-stat:last-child { border-bottom:0; }
    .quick-stat strong { color:#1e3a6f; }
    .table-wrap { overflow-x:auto; background:#fff; border:1px solid #e2e8f0; border-radius:12px; }
    table { width:100%; min-width:760px; border-collapse:collapse; }
    th { padding:12px 16px; background:#f8fafc; color:#475569; font-size:11px; text-align:left; }
    td { padding:13px 16px; border-top:1px solid #f1f5f9; color:#334155; font-size:12px; }
    .item-name { color:#2563eb; font-weight:800; }
    .stock-low { color:#dc2626; font-weight:800; }
    .stock-ok { color:#15803d; font-weight:800; }
    .status { display:inline-block; padding:4px 9px; border-radius:999px; font-size:10px; font-weight:800; }
    .status.low { background:#fee2e2; color:#991b1b; }
    .status.ok { background:#dcfce7; color:#166534; }
    .import-panel { display:flex; align-items:end; gap:12px; flex-wrap:wrap; margin-bottom:22px; padding:17px 18px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; }
    .import-panel label { display:block; margin-bottom:7px; color:#334155; font-size:12px; font-weight:800; }
    .import-panel input { max-width:280px; font-size:12px; }
    .hint { color:#64748b; font-size:11px; }
    .alert { margin-bottom:18px; padding:12px 14px; border-radius:9px; font-size:13px; font-weight:700; }
    .alert-success { background:#dcfce7; border:1px solid #86efac; color:#166534; }
    .alert-error { background:#fee2e2; border:1px solid #fca5a5; color:#991b1b; }
    .empty { padding:28px 18px; color:#64748b; font-size:12px; text-align:center; }
    @media (max-width:850px) { .metrics { grid-template-columns:repeat(2,1fr); } .report-grid { grid-template-columns:1fr; } }
    @media (max-width:560px) { .report-heading { display:block; } .heading-actions { justify-content:flex-start; margin-top:15px; } h1 { font-size:27px; } .metrics { gap:8px; } .metric { min-height:104px; padding:14px 12px; } .metric-value { font-size:24px; } }
</style>

<div class="report-page">
    <a class="btn btn-light back-button" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}">&larr; Kembali</a>
    <div class="report-heading">
        <div class="heading-copy">
            <div class="eyebrow">Monitoring inventaris gudang</div>
            <h1>Laporan Gudang</h1>
            <p>Ringkasan stok, penempatan, dan aktivitas operasional gudang.</p>
        </div>
        <div class="heading-actions">
            @if(in_array(auth()->user()->role, ['admin', 'staff'], true))
                <a class="btn btn-dark" href="{{ route('laporan.export') }}">Export CSV</a>
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
            <div class="panel-heading"><h2>Mutasi Barang Terbaru</h2><a href="{{ route('mutasi-barang.index', ['from' => 'laporan']) }}">Lihat semua &rsaquo;</a></div>
            @forelse($mutasiTerbaru as $mutasi)
                <div class="activity"><span class="activity-icon {{ $mutasi->jenis === 'masuk' ? 'in' : 'out' }}">{{ $mutasi->jenis === 'masuk' ? '↑' : '↓' }}</span><span class="activity-copy"><strong>{{ $mutasi->barang?->nama ?? 'Barang dihapus' }}</strong><small>{{ $mutasi->jenis === 'masuk' ? 'Barang masuk' : 'Barang keluar' }} &middot; {{ $mutasi->jumlah }} {{ $mutasi->barang?->satuan ?? '' }} &middot; {{ $mutasi->staff?->name ?? 'Staff' }}</small></span><span class="activity-time">{{ $mutasi->tanggal?->diffForHumans() }}</span></div>
            @empty
                <div class="empty">Belum ada aktivitas mutasi barang.</div>
            @endforelse
        </section>
        <section class="panel">
            <div class="panel-heading"><h2>Ringkasan Gudang</h2><a href="{{ route('barang.stok-menipis', ['from' => 'laporan']) }}">Stok menipis &rsaquo;</a></div>
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
            <div><label for="file">Import data barang CSV</label><input type="file" id="file" name="file" accept=".csv,.txt" required></div>
            <button class="btn btn-blue" type="submit">Import CSV</button>
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
