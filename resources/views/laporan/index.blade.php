@extends('layouts.app')

@section('content')
<style>
    .report-page { max-width:1120px; margin:0 auto; }
    .report-head { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:18px; flex-wrap:wrap; }
    .report-head__left { display:flex; align-items:center; gap:16px; min-width:0; }
    .report-head .back-link { margin-bottom:0; }
    .report-head .andon-page-title { margin:0; }
    .report-actions { display:flex; gap:8px; flex-wrap:wrap; }
    .report-tiles { margin-bottom:18px; }
    .report-tiles .andon-tile { cursor:default; }
    .report-tiles .andon-tile:hover { background:transparent; }
    .report-tiles .andon-tile--navy:hover { background:linear-gradient(135deg, var(--jet-black) 0%, var(--blue-slate) 100%); }
    .report-grid { margin-bottom:18px; }
    .table-responsive { width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table { width:100%; min-width:760px; border-collapse:collapse; }
    th { padding:12px 16px; background:#F8FAFC; color:var(--andon-muted); font-size:11px; font-weight:800; letter-spacing:.06em; text-align:left; text-transform:uppercase; border-bottom:1px solid var(--andon-line); white-space:nowrap; }
    td { padding:12px 16px; border-bottom:1px solid #F1F5F9; color:#334155; font-size:13px; }
    tbody tr:last-child td { border-bottom:0; }
    tbody tr:hover td { background:#F8FAFC; }
    .item-name { color:var(--andon-navy); font-weight:800; }
    .item-name:hover { text-decoration:underline; }
    .stock-low { color:var(--andon-red); font-weight:800; }
    .stock-ok { color:var(--andon-green); font-weight:800; }
    .import-panel { display:flex; align-items:end; gap:12px; flex-wrap:wrap; margin-bottom:18px; padding:14px 16px; background:var(--andon-panel); border:1px solid var(--andon-line); border-radius:var(--andon-radius); box-shadow:var(--andon-shadow); }
    .import-panel__field { flex:1 1 280px; max-width:360px; }
    .import-panel input[type="file"] { max-width:100%; font-size:12px; }
    .hint { color:var(--andon-muted); font-size:11px; font-weight:600; }
    @media (max-width:640px) { .report-head .andon-page-title { font-size:20px; } }
    @media (max-width:560px) {
        .report-head { align-items:flex-start; }
        .report-head__left { gap:12px; }
        .import-panel { align-items:stretch; }
        .import-panel__field { flex-basis:100%; max-width:none; }
        .import-panel .btn { width:100%; min-height:44px; }
        .import-panel .hint { flex-basis:100%; }
    }
</style>

<div class="report-page">
    <div class="report-head">
        <div class="report-head__left">
            <a class="back-link" href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
            <h1 class="andon-page-title">Laporan Gudang</h1>
        </div>
        <div class="report-actions">
            <a class="btn btn--primary" href="{{ route('laporan.export') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M7 3h7l4 4v14H7z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v4h4M9 12h6M9 16h6"/></svg>
                Export Excel
            </a>
            <a class="btn btn--ghost" href="{{ route('laporan.export.csv') }}" title="Format CSV untuk impor">CSV</a>
        </div>
    </div>

    @if(session('sukses'))
        <div class="alert alert-success" role="status">{{ session('sukses') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" role="alert">{{ $errors->first() }}</div>
    @endif

    <div class="andon-tiles report-tiles">
        <div class="andon-tile andon-tile--navy">
            <span class="andon-tile__label">TOTAL ITEM</span>
            <span class="andon-tile__value">{{ number_format($ringkasan['total_item'], 0, ',', '.') }}</span>
            <span class="andon-tile__note">Barang terdaftar</span>
        </div>
        <div class="andon-tile andon-tile--line">
            <span class="andon-tile__label">TOTAL STOK</span>
            <span class="andon-tile__value">{{ number_format($ringkasan['total_stok'], 0, ',', '.') }}</span>
            <span class="andon-tile__note">Jumlah seluruh satuan</span>
        </div>
        <div class="andon-tile andon-tile--red">
            <span class="andon-tile__label">STOK MENIPIS</span>
            <span class="andon-tile__value" @if((int) $ringkasan['stok_menipis'] === 0) style="color:var(--andon-ink)" @endif>{{ number_format($ringkasan['stok_menipis'], 0, ',', '.') }}</span>
            <span class="andon-tile__note">Perlu perhatian</span>
        </div>
        <div class="andon-tile andon-tile--line">
            <span class="andon-tile__label">DITEMPATKAN</span>
            <span class="andon-tile__value">{{ number_format($ringkasan['ditempatkan'], 0, ',', '.') }}</span>
            <span class="andon-tile__note">Memiliki lokasi rak</span>
        </div>
    </div>

    <div class="andon-grid report-grid">
        <section class="andon-panel">
            <div class="andon-panel__head"><h2>MUTASI BARANG TERBARU</h2><a href="{{ route('mutasi-barang.index', ['from' => 'laporan']) }}">Lihat semua ›</a></div>
            @forelse($mutasiTerbaru->take(5) as $mutasi)
                <div class="andon-row">
                    <span class="andon-row__icon {{ $mutasi->jenis === 'masuk' ? 'andon-row__icon--in' : 'andon-row__icon--out' }}">
                        @if($mutasi->jenis === 'masuk')
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 19V5"/><path d="M5 12l7-7 7 7"/></svg>
                        @else
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14"/><path d="M19 12l-7 7-7-7"/></svg>
                        @endif
                    </span>
                    <span class="andon-row__copy"><strong>{{ $mutasi->barang?->nama ?? 'Barang dihapus' }}</strong><small>{{ $mutasi->jenis === 'masuk' ? 'Barang masuk' : 'Barang keluar' }} · {{ $mutasi->jumlah }} {{ $mutasi->barang?->satuan ?? '' }} · {{ $mutasi->staff?->name ?? 'Staff' }}</small></span>
                    <span class="andon-row__meta">{{ $mutasi->tanggal?->diffForHumans() }}</span>
                </div>
            @empty
                <div class="andon-empty">Belum ada aktivitas mutasi barang.</div>
            @endforelse
        </section>
        <section class="andon-panel">
            <div class="andon-panel__head"><h2>RINGKASAN GUDANG</h2><a href="{{ route('barang.stok-menipis', ['from' => 'laporan']) }}">Stok menipis ›</a></div>
            <div class="andon-data">
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-ink)"></i> Kategori</span><strong>{{ number_format($ringkasan['kategori'], 0, ',', '.') }} kategori</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-green)"></i> Rak</span><strong>{{ number_format($ringkasan['rak'], 0, ',', '.') }} rak</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--saas-indigo)"></i> Riwayat mutasi</span><strong>{{ number_format($ringkasan['mutasi'], 0, ',', '.') }} transaksi</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-amber)"></i> Stock opname</span><strong>{{ number_format($ringkasan['opname'], 0, ',', '.') }} sesi</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-green)"></i> Kepatuhan lokasi</span><strong>{{ number_format($ringkasan['ditempatkan'], 0, ',', '.') }}/{{ number_format($ringkasan['total_item'], 0, ',', '.') }}</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-red)"></i> Barang tanpa rak</span><strong>{{ number_format($ringkasan['total_item'] - $ringkasan['ditempatkan'], 0, ',', '.') }}</strong></div>
                <div class="andon-data__row"><span class="andon-dotline"><i style="background:var(--andon-green)"></i> Stok aman</span><strong>{{ number_format($ringkasan['total_item'] - $ringkasan['stok_menipis'], 0, ',', '.') }}</strong></div>
            </div>
        </section>
    </div>

    @if(auth()->user()->isAdmin())
        <form class="import-panel" method="POST" action="{{ route('laporan.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="import-panel__field">
                <label class="andon-label" for="file">IMPORT DATA BARANG (EXCEL / CSV)</label>
                <input type="file" id="file" name="file" accept=".xlsx,.csv,.txt" required class="andon-control">
            </div>
            <button class="btn btn--amber" type="submit">Import Data</button>
            <span class="hint">Gunakan file hasil export Excel atau CSV. Maks. 5 MB.</span>
        </form>
    @endif

    <div class="andon-panel report-table">
        <div class="table-responsive">
            <table>
                <thead><tr><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Rak</th><th>Stok</th><th>Minimum</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($barangs as $barang)
                        <tr>
                            <td data-label="Kode">{{ $barang->kode_barang }}</td>
                            <td data-label="Nama Barang"><a class="item-name" href="{{ route('barang.show', $barang) }}">{{ $barang->nama }}</a></td>
                            <td data-label="Kategori">{{ $barang->kategori?->nama ?? '-' }}</td>
                            <td data-label="Rak">{{ $barang->rak?->kode_rak ?? 'Belum ditempatkan' }}</td>
                            <td data-label="Stok" class="{{ $barang->isStokMenipis() ? 'stock-low' : 'stock-ok' }}">{{ $barang->stok }} {{ $barang->satuan }}</td>
                            <td data-label="Minimum">{{ $barang->stok_minimum }} {{ $barang->satuan }}</td>
                            <td data-label="Status"><span class="badge {{ $barang->isStokMenipis() ? 'badge-red' : 'badge-green' }}">{{ $barang->isStokMenipis() ? 'Stok Menipis' : 'Aman' }}</span></td>
                        </tr>
                    @empty
                        <tr><td class="andon-empty" colspan="7">Belum ada data barang.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
