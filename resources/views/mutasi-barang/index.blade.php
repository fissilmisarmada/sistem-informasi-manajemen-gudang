@extends('layouts.app')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .page-title { font-size:22px; font-weight:800; color:#0f172a; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:10px 18px; border-radius:9px; font-weight:700; font-size:14px; cursor:pointer; border:none; text-decoration:none; }
    .btn-primary { background:#3b82f6; color:#fff; }
    .btn-primary:hover { background:#2563eb; }
    .btn-secondary { background:#e2e8f0; color:#0f172a; }
    .btn-secondary:hover { background:#cbd5e1; }
    .filter-bar { display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px; }
    .filter-bar select { padding:10px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:14px; }
    .card { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.08); padding:24px; margin-bottom:20px; }
    table { width:100%; border-collapse:collapse; }
    th { background:#f1f5f9; padding:12px 14px; text-align:left; font-size:13px; font-weight:700; color:#475569; }
    td { padding:12px 14px; border-bottom:1px solid #f1f5f9; font-size:14px; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#f8fafc; }
    .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:12px; font-weight:700; }
    .badge-green { background:#dcfce7; color:#166534; }
    .badge-red { background:#fee2e2; color:#991b1b; }
    .badge-blue { background:#dbeafe; color:#1d4ed8; }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; padding:12px 16px; border-radius:9px; margin-bottom:18px; font-weight:600; }
    .empty-state { text-align:center; padding:40px; color:#94a3b8; }
</style>

<div class="page-header">
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" class="btn btn-secondary" style="padding:8px 14px; font-size:13px;">← Kembali</a>
        <h1 class="page-title">Mutasi Barang</h1>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
        <a href="{{ route('mutasi-barang.create') }}" class="btn btn-primary">+ Catat Mutasi</a>
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
    <button type="submit" class="btn btn-primary">Filter</button>
    @if(request()->hasAny(['jenis','barang_id']))
        <a href="{{ route('mutasi-barang.index') }}" class="btn btn-secondary">Reset</a>
    @endif
</form>

<div class="card">
    @if($mutasis->isEmpty())
        <div class="empty-state">Belum ada data mutasi.</div>
    @else
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
                    <td>{{ $m->tanggal->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('barang.show', $m->barang) }}" style="color:#3b82f6;font-weight:700;">{{ $m->barang->nama }}</a>
                        <div style="font-size:11px;color:#94a3b8;">{{ $m->barang->kode_barang }}</div>
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
                    <td>{{ $m->keterangan ?? '-' }}</td>
                    <td>{{ $m->staff->name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top:16px;">{{ $mutasis->links() }}</div>
    @endif
</div>
@endsection
