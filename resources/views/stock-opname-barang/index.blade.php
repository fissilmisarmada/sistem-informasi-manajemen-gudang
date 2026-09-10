@extends('layouts.app')

@section('content')
<style>
    .card { background:var(--andon-panel); border:1px solid #EDEEF2; border-radius:20px; box-shadow:0 6px 24px rgba(15,23,42,.06), 0 1px 2px rgba(15,23,42,.04); padding:24px; margin-bottom:20px; }
    table { width:100%; border-collapse:collapse; }
    th { background:#f1f5f9; padding:12px 14px; text-align:left; font-size:13px; font-weight:700; color:#475569; }
    td { padding:12px 14px; border-bottom:1px solid #f1f5f9; font-size:14px; }
    tr:last-child td { border-bottom:none; }
    tr:hover td { background:#f8fafc; }
    .badge { display:inline-block; padding:3px 10px; border-radius:999px; font-size:12px; font-weight:700; }
    .badge-green { background:#dcfce7; color:#166534; }
    .badge-red { background:#fee2e2; color:#991b1b; }
    .badge-blue { background:#dbeafe; color:#1d4ed8; }
    .badge-yellow { background:#fef9c3; color:#854d0e; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; font-weight:700; font-size:13px; cursor:pointer; border:none; text-decoration:none; }
    .btn-primary { background:#3b82f6; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#0f172a; }
    .action-group { display:flex; flex-direction:column; align-items:stretch; gap:7px; min-width:92px; }
    .action-group .btn { justify-content:center; width:100%; }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; padding:12px 16px; border-radius:9px; margin-bottom:18px; font-weight:600; }
    .empty-state { text-align:center; padding:40px; color:#94a3b8; }
</style>

<div class="page-header">
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" class="btn btn-secondary" style="padding:8px 14px; font-size:13px;">← Kembali</a>
        <h1 class="andon-page-title" style="margin:0">Stock Opname Barang</h1>
    </div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    @if($barangs->isEmpty())
        <div class="empty-state">Belum ada barang terdaftar.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Stok Sistem</th>
                    <th>Opname Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barangs as $barang)
                <tr>
                    <td><span class="badge badge-blue">{{ $barang->kode_barang }}</span></td>
                    <td>
                        <a href="{{ route('barang.show', $barang) }}" style="color:#3b82f6;font-weight:700;">{{ $barang->nama }}</a>
                    </td>
                    <td>{{ $barang->kategori->nama }}</td>
                    <td>
                        <strong>{{ $barang->stok }}</strong> {{ $barang->satuan }}
                        @if($barang->isStokMenipis())
                            <span class="badge badge-red" style="margin-left:4px;">Menipis</span>
                        @endif
                    </td>
                    <td>
                        @if($barang->stockOpname->isNotEmpty())
                            @php $last = $barang->stockOpname->first(); @endphp
                            {{ $last->tanggal->format('d/m/Y') }}
                            @if($last->selisih != 0)
                                <span class="badge badge-red">Selisih {{ $last->selisih }}</span>
                            @else
                                <span class="badge badge-green">OK</span>
                            @endif
                        @else
                            <span style="color:#94a3b8;">Belum pernah</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('stock-opname-barang.create', ['barang' => $barang, 'from' => 'stock-opname-barang']) }}" class="btn btn-primary">Opname</a>
                            <a href="{{ route('stock-opname-barang.riwayat', ['barang' => $barang, 'from' => 'stock-opname-barang']) }}" class="btn btn-secondary">Riwayat</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
