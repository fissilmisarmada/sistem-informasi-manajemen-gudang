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
    .btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:8px 14px; border-radius:10px; font-weight:700; font-size:13px; cursor:pointer; border:1px solid transparent; text-decoration:none; min-height:32px; }
    .btn-primary { background:var(--andon-ink); color:#fff; border-color:var(--andon-ink); }
    .btn-primary:hover{ background:var(--andon-navy-2); }
    .btn-secondary { background:#fff; color:var(--andon-ink); border-color:var(--andon-line-strong); }
    .action-group { display:flex; flex-direction:column; align-items:stretch; gap:7px; min-width:92px; }
    .action-group .btn { justify-content:center; width:100%; min-height:36px; }
    @media(max-width:640px){ .action-group .btn{ min-height:44px; font-size:14px; } }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; padding:12px 16px; border-radius:9px; margin-bottom:18px; font-weight:600; }
    .empty-state { text-align:center; padding:40px; color:#94a3b8; }
</style>

<div class="page-header">
    <div style="display:flex; align-items:center; gap:16px;">
        <a href="{{ auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan')) }}" class="back-link" aria-label="Kembali"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg></a>
        <h1 class="andon-page-title" style="margin:0">Stock Opname Barang</h1>
    </div>

@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    @if($barangs->isEmpty())
        <div class="empty-state">Belum ada barang terdaftar.</div>
    @else
        <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Stok Sistem</th>
                    <th>Opname Terakhir</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($barangs as $barang)
                @php
                    $gudang = $barang->denahArea->nama ?? $barang->rak->nama_lokasi ?? null;
                    $rakLabel = $barang->rak ? $barang->rak->kode_rak . ' · ' . $barang->rak->nama_lokasi : null;
                @endphp
                <tr>
                    <td data-label="Kode"><span class="badge badge-blue">{{ $barang->kode_barang }}</span></td>
                    <td data-label="Nama Barang">
                        <a href="{{ route('barang.show', $barang) }}" style="color:#3b82f6;font-weight:700;">{{ $barang->nama }}</a>
                    </td>
                    <td data-label="Kategori">{{ $barang->kategori->nama }}</td>
                    <td data-label="Lokasi">
                        @if($gudang || $rakLabel)
                            <div style="display:flex;flex-direction:column;gap:4px;align-items:flex-start;text-align:left">
                                @if($gudang)
                                    <span class="badge badge-gray" style="max-width:100%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $gudang }}</span>
                                @endif
                                @if($rakLabel)
                                    <span style="font-size:11px;font-weight:700;color:var(--andon-faint);line-height:1.2">{{ $rakLabel }}</span>
                                @else
                                    <span style="font-size:11px;color:var(--andon-faint)">Tanpa rak</span>
                                @endif
                            </div>
                        @else
                            <span class="badge badge-yellow">Belum ditempatkan</span>
                        @endif
                    </td>
                    <td data-label="Stok Sistem">
                        <strong style="font-weight:800">{{ $barang->stok }}</strong> {{ $barang->satuan }}
                        @if($barang->isStokMenipis())
                            <span class="badge badge-red" style="margin-left:4px;">Menipis</span>
                        @endif
                    </td>
                    <td data-label="Opname Terakhir">
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
                    <td data-label="Aksi">
                        <div class="action-group">
                            <a href="{{ route('stock-opname-barang.create', ['barang' => $barang, 'from' => 'stock-opname-barang']) }}" class="btn btn-primary">Opname</a>
                            <a href="{{ route('stock-opname-barang.riwayat', ['barang' => $barang, 'from' => 'stock-opname-barang']) }}" class="btn btn-secondary">Riwayat</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif
</div>
@endsection
