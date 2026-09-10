@extends('layouts.app')

@section('content')
<style>
    .rack-page{max-width:1120px;margin:0 auto}
    .page-head{display:flex;align-items:flex-end;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:18px}
    .page-head h1{margin:0;font-size:22px;font-weight:800;letter-spacing:-.03em;color:var(--andon-ink);line-height:1}
    .eyebrow{color:var(--andon-faint);font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase}
    .panel{overflow:hidden;background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);position:relative;transition:box-shadow .22s cubic-bezier(.16,1,.3,1),border-color .22s ease}
    .panel:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
    .panel::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-navy)}
    .rack-info{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;padding:16px}
    .info-item{padding:13px 15px;background:#F8FAFC;border:1px solid var(--andon-line);border-radius:12px}
    .info-item span{display:block;color:var(--andon-muted);font-size:11px;font-weight:700}
    .info-item strong{display:block;margin-top:6px;color:var(--andon-ink);font-size:15px;letter-spacing:-.02em}
    .panel-heading{padding:16px 16px 14px;border-bottom:1px solid var(--andon-line)}
    .panel-heading h2{margin:0;color:var(--andon-ink);font-size:13px;font-weight:800;letter-spacing:-.02em}
    .table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}
    table{width:100%;min-width:650px;border-collapse:collapse}
    th{padding:11px 14px;background:#F8FAFC;color:var(--andon-muted);font-size:11px;font-weight:800;letter-spacing:.06em;text-align:left;border-bottom:1px solid var(--andon-line)}
    td{padding:11px 14px;border-top:1px solid #F1F5F9;color:var(--andon-ink);font-size:12px}
    tr:last-child td{border-bottom:none}
    .empty{padding:40px 18px;color:var(--andon-muted);text-align:center;font-size:13px;font-weight:600}
    .stock{color:var(--andon-green);font-weight:800}
    @media(max-width:600px){.page-head{display:block} .rack-info{grid-template-columns:1fr} .page-head h1{font-size:20px}}
</style>

<div class="rack-page">
    <a class="btn btn--ghost" href="{{ request('from') === 'kelola-rak' ? route('rak.index', request('return_from') ? ['from' => request('return_from')] : []) : route('denah-gudang', ['rak' => $rak->id]) }}" style="margin-bottom:14px;min-height:36px;padding:0 14px;font-size:12px">&larr; Kembali</a>
    <div class="page-head">
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
