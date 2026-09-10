@extends('layouts.app')

@section('content')
<style>
    .stok-page{max-width:1120px;margin:0 auto}
    .page-head{display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:16px}
    .page-head h1{margin:6px 0 0;font-size:22px;font-weight:800;letter-spacing:-.03em;color:var(--andon-ink);line-height:1}
    .page-head p{margin:6px 0 0;font-size:12px;color:var(--andon-muted)}
    .summary{margin-bottom:16px;padding:16px 18px;background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);position:relative;overflow:hidden;display:flex;align-items:center;gap:14px;transition:box-shadow .22s cubic-bezier(.16,1,.3,1)}
    .summary:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
    .summary::before{content:'';position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--andon-red)}
    .summary__icon{width:40px;height:40px;border-radius:10px;display:grid;place-items:center;background:#FEF2F2;border:1px solid #FECACA;color:var(--andon-red);flex:0 0 auto;font-size:18px}
    .summary strong{font-size:14px;font-weight:800;color:var(--andon-ink);letter-spacing:-.02em}
    .summary span{font-size:12px;color:var(--andon-muted);font-weight:600}
    .card{overflow:hidden;background:var(--andon-panel);border:1px solid #EDEEF2;border-radius:20px;box-shadow:0 6px 24px rgba(15,23,42,.06),0 1px 2px rgba(15,23,42,.04);position:relative;transition:box-shadow .22s cubic-bezier(.16,1,.3,1),border-color .22s ease}
    .card:hover{box-shadow:0 10px 28px rgba(15,23,42,.08),0 2px 6px rgba(15,23,42,.05)}
    .card::before{content:'';position:absolute;left:0;right:0;top:0;height:3px;background:var(--andon-red)}
    .card__scroll{overflow-x:auto}
    table{width:100%;min-width:680px;border-collapse:collapse}
    th{padding:12px 14px;background:#FCFCF9;color:var(--andon-muted);font-size:11px;font-weight:800;letter-spacing:.08em;text-align:left;border-bottom:1px solid var(--andon-line);white-space:nowrap}
    td{padding:12px 14px;border-top:1px solid #F1F5F9;color:var(--andon-ink);font-size:13px}
    .item-name{color:var(--andon-navy);font-weight:800;text-decoration:none}
    .item-name:hover{text-decoration:underline}
    .stock-low{color:var(--andon-red);font-weight:800}
    .stock-minimum{color:var(--andon-faint);font-weight:600;font-size:12px}
    .badge{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;background:#FEF2F2;color:#991B1B;border:1px solid #FECACA;font-size:11px;font-weight:800}
    .empty-state{padding:48px 20px;color:var(--andon-faint);text-align:center;font-weight:600;font-size:13px}
    .pagination-wrap{margin-top:16px;display:flex;justify-content:center}
</style>

<div class="stok-page">
<div class="page-head">
    <div>
        <a href="{{ request('from') === 'laporan' ? route('laporan.index') : (auth()->user()->isAdmin() ? route('dashboard.admin') : (auth()->user()->isStaff() ? route('dashboard.staff') : route('dashboard.pimpinan'))) }}" class="btn btn--ghost" style="min-height:36px;padding:0 14px;font-size:12px">&larr; Kembali</a>
        <h1>Stok Menipis</h1>
        <p>Barang pada atau di bawah batas minimum — butuh restock.</p>
    </div>
</div>

<div class="summary">
    <span class="summary__icon">⚠</span>
    <div>
        <strong>Perlu perhatian — {{ $barangs->total() }} item</strong><br>
        <span>Berada pada atau di bawah batas stok minimum.</span>
    </div>
</div>

<div class="card">
    @if($barangs->isEmpty())
        <div class="empty-state">Tidak ada barang dengan stok menipis.</div>
    @else
        <div class="card__scroll">
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
                        <td style="font-weight:700;color:var(--andon-muted);font-size:12px">{{ $barang->kode_barang }}</td>
                        <td><a class="item-name" href="{{ route('barang.show', ['barang' => $barang, 'from' => request('from')]) }}">{{ $barang->nama }}</a></td>
                        <td>{{ $barang->kategori->nama }}</td>
                        <td>{{ $barang->rak?->kode_rak ?? 'Belum ditempatkan' }}</td>
                        <td><span class="stock-low">{{ $barang->stok }} {{ $barang->satuan }}</span> <span class="stock-minimum">/ min. {{ $barang->stok_minimum }}</span></td>
                        <td><span class="badge">Stok menipis</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @endif
</div>

<div class="pagination-wrap">{{ $barangs->withQueryString()->links() }}</div>
</div>
@endsection
