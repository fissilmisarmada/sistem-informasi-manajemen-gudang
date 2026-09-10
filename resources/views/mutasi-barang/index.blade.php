@extends('layouts.app')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
    .filter-bar { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:16px; background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; padding:14px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); }
    .filter-bar select { padding:12px 14px; border:1px solid #E8EAF0; border-radius:14px; font-size:14px; background:#FBFBFD; color:var(--andon-ink); transition:border-color .18s ease,box-shadow .18s ease,background .18s ease; }
    .filter-bar select:focus { outline:none; border-color:var(--andon-ink); box-shadow:0 0 0 3px rgba(15,23,42,.06); background:#fff; }
    .card { background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); padding:20px; margin-bottom:20px; }
    .table-responsive { width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch; }
    table { width:100%; min-width:920px; border-collapse:collapse; }
    th { background:#F8FAFC; padding:11px 14px; text-align:left; font-size:12px; font-weight:800; color:var(--andon-muted); letter-spacing:.04em; text-transform:uppercase; border-bottom:1px solid var(--andon-line); }
    td { padding:12px 14px; border-bottom:1px solid var(--andon-line); font-size:13px; color:var(--andon-ink); }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#F8FAFC; }
    .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700; border:1px solid transparent; }
    .badge-green { background:var(--andon-green); color:#fff; }
    .badge-red { background:var(--andon-red); color:#fff; }
    .badge-blue { background:var(--andon-navy); color:#fff; }
    .alert-success { background:#F0FDF4; color:#14532D; border:1px solid #BBF7D0; padding:12px 16px; border-radius:12px; margin-bottom:16px; font-weight:600; font-size:13px; }
    .empty-state { text-align:center; padding:40px; color:var(--andon-faint); font-weight:600; }
    @media (max-width:600px) {
        .page-header > div { width:100%; }
        .page-header > div:first-child { align-items:flex-start !important; gap:10px !important; }
        .page-header > .btn { width:100%; justify-content:center; }
        .filter-bar > * { flex:1 1 100%; width:100%; }
        .filter-bar .btn { justify-content:center; }
    }
</style>

<div class="page-header">
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="{{ request('from') === 'laporan' ? route('laporan.index') : (auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan'))) }}" class="btn btn--ghost" style="padding:8px 14px; font-size:13px;">← Kembali</a>
        <h1 class="andon-page-title" style="margin:0">Mutasi Barang</h1>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <a href="{{ route('mutasi-barang.create') }}" class="btn btn--primary">+ Catat Mutasi</a>
    @endif
</div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<form method="GET" action="{{ route('mutasi-barang.index') }}" class="filter-bar">
    <select name="jenis">
        <option value="">Semua Jenis</option>
        <option value="masuk" {{ request('jenis') === 'masuk' ? 'selected' : '' }}>Masuk</option>
        <option value="keluar" {{ request('jenis') === 'keluar' ? 'selected' : '' }}>Keluar</option>
    </select>
    <select name="barang_id">
        <option value="">Semua Barang</option>
        @foreach($barangs as $b)
            <option value="{{ $b->id }}" {{ request('barang_id') == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn--primary">Filter</button>
    @if(request()->hasAny(['jenis','barang_id']))
        <a href="{{ route('mutasi-barang.index') }}" class="btn btn--ghost">Reset</a>
    @endif
</form>

<div class="card">
    @if($mutasis->isEmpty())
        <div class="empty-state">Belum ada data mutasi.</div>
    @else
        <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Barang</th>
                    <th>Kategori</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Keterangan</th>
                    <th>Staff</th>
                </tr>
            </thead>
            <tbody>
                @foreach($mutasis as $m)
                <tr>
                    <td style="color:var(--andon-muted); font-weight:600;">{{ $m->tanggal->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('barang.show', $m->barang) }}" style="color:var(--andon-navy);font-weight:800;">{{ $m->barang->nama }}</a>
                        <div style="font-size:11px;color:var(--andon-faint); font-weight:600;">{{ $m->barang->kode_barang }}</div>
                    </td>
                    <td><span class="badge badge-blue">{{ $m->barang->kategori->nama }}</span></td>
                    <td>
                        @if($m->jenis === 'masuk')
                            <span class="badge badge-green">↑ Masuk</span>
                        @else
                            <span class="badge badge-red">↓ Keluar</span>
                        @endif
                    </td>
                    <td><strong>{{ $m->jumlah }}</strong> {{ $m->barang->satuan }}</td>
                    <td style="color:var(--andon-muted);">{{ $m->keterangan ?? '-' }}</td>
                    <td style="font-weight:600;">{{ $m->staff->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </div>
        </table>
        <div style="margin-top:16px;">{{ $mutasis->links() }}</div>
    @endif
</div>
@endsection
