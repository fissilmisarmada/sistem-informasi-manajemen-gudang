@extends('layouts.app')

@section('content')
<style>
    .rack-page { max-width:1000px; margin:0 auto; padding:10px 0 36px; }
    .rack-heading { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom:22px; }
    .eyebrow { color:#64748b; font-size:11px; font-weight:800; letter-spacing:1.1px; text-transform:uppercase; }
    h1 { margin:7px 0 0; color:#123b82; font-size:31px; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:10px 15px; border-radius:9px; font-size:13px; font-weight:800; text-decoration:none; }
    .btn-light { background:#e2e8f0; color:#0f172a; }
    .back-button { margin-bottom:18px; }
    .panel { overflow:hidden; background:#fff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 5px 16px rgba(15,23,42,.05); }
    .rack-info { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; padding:18px; margin-bottom:18px; }
    .info-item { padding:13px 15px; background:#f8fafc; border-radius:9px; }
    .info-item span { display:block; color:#64748b; font-size:11px; }
    .info-item strong { display:block; margin-top:6px; color:#1e3a6f; font-size:16px; }
    .panel-heading { padding:17px 18px; border-bottom:1px solid #eef2f7; }
    .panel-heading h2 { margin:0; color:#1e3a6f; font-size:15px; }
    .table-wrap { overflow-x:auto; }
    table { width:100%; min-width:650px; border-collapse:collapse; }
    th { padding:12px 16px; background:#f8fafc; color:#475569; font-size:11px; text-align:left; }
    td { padding:13px 16px; border-top:1px solid #f1f5f9; color:#334155; font-size:12px; }
    .empty { padding:34px 18px; color:#64748b; text-align:center; font-size:12px; }
    .stock { color:#15803d; font-weight:800; }
    @media (max-width:600px) { .rack-heading { display:block; } .rack-heading .btn { margin-top:15px; } .rack-info { grid-template-columns:1fr; } h1 { font-size:27px; } }
</style>

<div class="rack-page">
    <a class="btn btn-light back-button" href="{{ request('from') === 'kelola-rak' ? route('rak.index', request('return_from') ? ['from' => request('return_from')] : []) : route('denah-gudang', ['rak' => $rak->id]) }}">&larr; Kembali</a>
    <div class="rack-heading">
        <div><div class="eyebrow">Lokasi inventaris gudang</div><h1>{{ $rak->kode_rak }}</h1></div>
    </div>

    <div class="panel rack-info">
        <div class="info-item"><span>Nama Lokasi</span><strong>{{ $rak->nama_lokasi }}</strong></div>
        <div class="info-item"><span>Total Jenis Barang</span><strong>{{ $barang->count() }} jenis</strong></div>
        <div class="info-item"><span>Kapasitas Rak</span><strong>{{ $rak->kapasitas ? number_format($rak->kapasitas, 0, ',', '.') . ' unit' : 'Belum ditentukan' }}</strong></div>
    </div>

    <section class="panel">
        <div class="panel-heading"><h2>Daftar Barang di Rak</h2></div>
        @if($barang->isEmpty())
            <div class="empty">Belum ada barang yang ditempatkan di rak ini.</div>
        @else
            <div class="table-wrap"><table><thead><tr><th>Kode Barang</th><th>Nama Barang</th><th>Kategori</th><th>Stok</th></tr></thead><tbody>
                @foreach($barang as $item)
                    <tr><td>{{ $item->kode_barang }}</td><td>{{ $item->nama }}</td><td>{{ $item->kategori?->nama ?? '-' }}</td><td class="stock">{{ $item->stok }} {{ $item->satuan }}</td></tr>
                @endforeach
            </tbody></table></div>
        @endif
    </section>
</div>
@endsection
