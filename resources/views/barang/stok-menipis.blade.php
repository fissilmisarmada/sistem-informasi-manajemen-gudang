@extends('layouts.app')

@section('content')
<style>
    .page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
    .page-title { font-size:22px; font-weight:800; color:#0f172a; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:10px 18px; border-radius:9px; font-weight:700; font-size:14px; text-decoration:none; }
    .btn-secondary { background:#e2e8f0; color:#0f172a; }
    .btn-secondary:hover { background:#cbd5e1; }
    .summary { margin-bottom:20px; padding:18px 20px; background:#fff7ed; border:1px solid #fed7aa; border-radius:12px; color:#9a3412; }
    .summary strong { display:block; margin-bottom:5px; font-size:18px; }
    .summary span { font-size:13px; }
    .card { overflow-x:auto; background:#fff; border:1px solid #e2e8f0; border-radius:14px; box-shadow:0 2px 12px rgba(15,23,42,.06); }
    table { width:100%; min-width:680px; border-collapse:collapse; }
    th { padding:12px 16px; background:#f8fafc; color:#475569; font-size:12px; text-align:left; }
    td { padding:14px 16px; border-top:1px solid #f1f5f9; color:#334155; font-size:13px; }
    .item-name { color:#1d4ed8; font-weight:800; }
    .stock-low { color:#dc2626; font-weight:800; }
    .stock-minimum { color:#64748b; }
    .badge { display:inline-block; padding:4px 10px; border-radius:999px; background:#fee2e2; color:#991b1b; font-size:11px; font-weight:800; }
    .empty-state { padding:60px 20px; color:#64748b; text-align:center; }
    .pagination-wrap { margin-top:20px; }
</style>

<div class="page-header">
    <div>
        <a href="{{ request('from') === 'laporan' ? route('laporan.index') : (auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan'))) }}" class="btn btn-secondary">&larr; Kembali</a>
        <h1 class="page-title" style="margin:18px 0 0;">Stok Menipis</h1>
    </div>
</div>

<div class="summary">
    <strong>Perlu perhatian</strong>
    <span>{{ $barangs->total() }} barang berada pada atau di bawah batas stok minimum.</span>
</div>

<div class="card">
    @if($barangs->isEmpty())
        <div class="empty-state">Tidak ada barang dengan stok menipis.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Lokasi Rak</th>
                    <th>Stok</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barangs as $barang)
                    <tr>
                        <td>{{ $barang->kode_barang }}</td>
                        <td><a class="item-name" href="{{ route('barang.show', ['barang' => $barang, 'from' => request('from')]) }}">{{ $barang->nama }}</a></td>
                        <td>{{ $barang->kategori->nama }}</td>
                        <td>{{ $barang->rak?->kode_rak ?? 'Belum ditempatkan' }}</td>
                        <td><span class="stock-low">{{ $barang->stok }} {{ $barang->satuan }}</span> <span class="stock-minimum">/ min. {{ $barang->stok_minimum }}</span></td>
                        <td><span class="badge">Stok menipis</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="pagination-wrap">{{ $barangs->withQueryString()->links() }}</div>
@endsection
